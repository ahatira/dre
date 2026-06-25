#!/usr/bin/env bash
# Drush wrapper — host only (WSL dev and production servers).

ps_drush() {
  ps_resolve_runtime
  local cmd=()
  if [[ -n "${PS_DRUSH_ALIAS:-}" ]]; then
    cmd=("${PS_DRUSH_ALIAS}")
  fi
  (cd "${PS_SRC_DIR}" && vendor/bin/drush "${cmd[@]}" "$@")
}

ps_memcache_php_extension_available() {
  php -m 2>/dev/null | grep -qE '^(memcache|memcached)$'
}

ps_php_container_drush_available() {
  ps_load_config
  [[ "${PS_APP_ENV}" == "dev" && -d "${PS_REPO_ROOT}/docker" ]] || return 1
  local php_container="${PS_PHP_CONTAINER:-ps_php}"
  command -v docker >/dev/null 2>&1 || return 1
  docker ps --format '{{.Names}}' 2>/dev/null | grep -qx "${php_container}"
}

ps_drush_in_php_container() {
  local php_container="${PS_PHP_CONTAINER:-ps_php}"
  local alias="${PS_DRUSH_ALIAS:-@ps.com}"
  ps_php_container_drush_available \
    || ps_die "PHP container ${php_container} unavailable for Drush"
  docker exec "${php_container}" sh -c \
    "cd /var/www/html && vendor/bin/drush ${alias} $(printf '%q ' "$@")"
}

ps_drush_cr() {
  ps_drush cache:rebuild "$@" || ps_warn "Cache rebuild failed — continuing (check Memcache connectivity)"

  ps_load_config
  # WSL host Drush and ps_php FPM can diverge on the compiled DI container in dev.
  if [[ "${PS_APP_ENV}" == "dev" && -d "${PS_REPO_ROOT}/docker" ]]; then
    local php_container="${PS_PHP_CONTAINER:-ps_php}"
    local alias="${PS_DRUSH_ALIAS:-@ps.com}"
    if command -v docker >/dev/null 2>&1 && docker ps --format '{{.Names}}' 2>/dev/null | grep -qx "${php_container}"; then
      docker exec "${php_container}" sh -c "cd /var/www/html && vendor/bin/drush ${alias} cache:rebuild" >/dev/null \
        || ps_warn "Container cache rebuild failed (${php_container}) — run: docker exec ${php_container} vendor/bin/drush ${alias} cr"
    fi
  fi
}

ps_drush_rebuild_permissions() {
  ps_drush php:eval 'node_access_rebuild(); echo "node_access_rebuild OK\n";' \
    || ps_die "Content permissions rebuild failed"
}

ps_drush_for_country() {
  local country="$1"
  PS_COUNTRY_CODE="${country}"
  export PS_COUNTRY_CODE
  PS_DRUSH_ALIAS="@ps.${country}"
  export PS_DRUSH_ALIAS
}

ps_drush_bootstrapped() {
  ps_drush status --field=bootstrap 2>/dev/null | grep -qi successful
}

ps_require_drush_psql() {
  command -v psql >/dev/null 2>&1 \
    || ps_warn "psql not found on host — drush sql:create may fail for PostgreSQL"
}

ps_drush_database_exists() {
  local result
  result="$(ps_drush ev '
    $options = \Drush\Drush::config()->get("runtime.options");
    $sql = \Drush\Sql\SqlBase::create($options);
    $spec = $sql->getDbSpec();
    $name = $spec["database"] ?? "";
    if ($name === "") { echo "no"; return; }
    $pdo = new \PDO(
      sprintf("pgsql:host=%s;port=%d;dbname=postgres", $spec["host"] ?? "127.0.0.1", (int) ($spec["port"] ?? 5432)),
      $spec["username"] ?? "drupal",
      $spec["password"] ?? "drupal"
    );
    $stmt = $pdo->prepare("SELECT 1 FROM pg_database WHERE datname = :name");
    $stmt->execute(["name" => $name]);
    echo $stmt->fetchColumn() ? "yes" : "no";
  ' 2>/dev/null | tr -d '[:space:]')"
  [[ "${result}" == "yes" ]]
}

ps_drush_sql_recreate_pdo() {
  ps_drush ev '
    $options = \Drush\Drush::config()->get("runtime.options");
    $sql = \Drush\Sql\SqlBase::create($options);
    $spec = $sql->getDbSpec();
    $name = $spec["database"] ?? "";
    if ($name === "") {
      throw new \RuntimeException("Missing database name in Drush db spec.");
    }
    $pdo = new \PDO(
      sprintf("pgsql:host=%s;port=%d;dbname=postgres", $spec["host"] ?? "127.0.0.1", (int) ($spec["port"] ?? 5432)),
      $spec["username"] ?? "drupal",
      $spec["password"] ?? "drupal"
    );
    $quoted = "\"" . str_replace("\"", "\"\"", $name) . "\"";
    $pdo->exec("SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = " . $pdo->quote($name) . " AND pid <> pg_backend_pid()");
    $pdo->exec("DROP DATABASE IF EXISTS {$quoted}");
    $pdo->exec("CREATE DATABASE {$quoted}");
    echo "recreated={$name}\n";
  '
  # Drop stale Drush bootstrap after terminating DB connections.
  sleep 1
}

ps_drush_sql_create() {
  if command -v psql >/dev/null 2>&1; then
    ps_drush sql:create -y "$@"
    return $?
  fi
  ps_info "psql not on host — recreating database via PDO (dev/prod PHP)"
  ps_drush_sql_recreate_pdo
}

ps_ensure_country_database() {
  local country="$1"
  ps_drush_for_country "${country}"
  local upper var db_name
  upper="$(ps_country_upper "${country}")"
  var="DB_NAME_${upper}"
  db_name="$(ps_env_get "${var}")"
  [[ -n "${db_name}" ]] || ps_die "Missing ${var} in configuration"

  if ps_drush_database_exists; then
    ps_info "Database exists: ${db_name} (${country})"
    return 0
  fi
  ps_info "Creating database: ${db_name} (${country})"
  ps_retry 2 2 ps_drush_sql_create
}

ps_drush_published_offer_count() {
  ps_drush ev 'echo (int) \Drupal::entityTypeManager()->getStorage("node")->getQuery()->accessCheck(FALSE)->condition("type","offer")->condition("status",1)->count()->execute();' 2>/dev/null | tr -d '[:space:]'
}

ps_drush_import_language_config_overrides() {
  local langcode="$1"
  ps_drush ev '
    use Drupal\Component\Serialization\Yaml;
    $langcode = "'"${langcode}"'";
    if (\Drupal::languageManager()->getLanguage($langcode) === NULL) {
      throw new \RuntimeException("Language not enabled: {$langcode}");
    }
    $roots = [DRUPAL_ROOT . "/modules/custom", DRUPAL_ROOT . "/themes/custom"];
    $imported = 0;
    foreach ($roots as $root) {
      if (!is_dir($root)) { continue; }
      foreach (glob($root . "/*", GLOB_ONLYDIR) ?: [] as $extensionPath) {
        foreach (["install", "optional"] as $type) {
          $languageDir = $extensionPath . "/config/{$type}/language/{$langcode}";
          if (!is_dir($languageDir)) { continue; }
          foreach (glob($languageDir . "/*.yml") ?: [] as $file) {
            $name = basename($file, ".yml");
            $data = Yaml::decode((string) file_get_contents($file));
            if (!is_array($data) || $data === []) { continue; }
            $override = \Drupal::languageManager()->getLanguageConfigOverride($langcode, $name);
            foreach ($data as $key => $value) {
              if ($value === NULL || in_array($key, ["handlers", "variants"], TRUE)) { continue; }
              $override->set($key, $value);
            }
            $override->save();
            $imported++;
          }
        }
      }
    }
    echo "imported={$imported} lang={$langcode}\n";
  ' || ps_warn "Language config override import failed for ${langcode}"
}
