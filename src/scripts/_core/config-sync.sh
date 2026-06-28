#!/usr/bin/env bash
# Config sync helpers — per-country CMI (config/sites/{code}) for install-from-conf.

ps_config_sites_root() {
  printf '%s' "${PS_SRC_DIR}/config/sites"
}

ps_config_sync_dir() {
  local country="${PS_COUNTRY_CODE:-com}"
  printf '%s/%s' "$(ps_config_sites_root)" "${country}"
}

ps_require_config_sync() {
  local country="${PS_COUNTRY_CODE:-com}"
  ps_require_file "$(ps_config_sync_dir)/core.extension.yml" \
    "Missing config/sites/${country} (run: make seed-site-configs or make export-all-configs ${country})"
}

ps_cim_staging_dir() {
  printf '%s' "${PS_SRC_DIR}/tmp/cim-staging"
}

ps_build_cim_staging_dir() {
  local staging
  staging="$(ps_cim_staging_dir)"
  rm -rf "${staging}"
  mkdir -p "${staging}"
  cp -a "$(ps_config_sync_dir)/." "${staging}/"

  if [[ "${PS_SKIP_DEMO_MODULE:-1}" == "1" ]]; then
    ps_info "Staging CMI without ps_demo (install-from-conf excludes demo)..." >&2
    export PS_VENDOR_AUTOLOAD="${PS_SRC_DIR}/vendor/autoload.php"
    export PS_STAGING="${staging}"
    php -r '
      require getenv("PS_VENDOR_AUTOLOAD");
      $path = getenv("PS_STAGING") . "/core.extension.yml";
      $data = \Symfony\Component\Yaml\Yaml::parseFile($path);
      unset($data["module"]["ps_demo"]);
      file_put_contents($path, \Symfony\Component\Yaml\Yaml::dump($data, 4, 2));
    '
    rm -f "${staging}"/ps_demo.*.yml
  fi

  # simple_sitemap hooks fail during config import when webform bundles reconcile.
  ps_info "Staging without simple_sitemap/ps_seo (enabled post-import)..." >&2
  export PS_VENDOR_AUTOLOAD="${PS_SRC_DIR}/vendor/autoload.php"
  export PS_STAGING="${staging}"
  php -r '
    require getenv("PS_VENDOR_AUTOLOAD");
    $path = getenv("PS_STAGING") . "/core.extension.yml";
    $data = \Symfony\Component\Yaml\Yaml::parseFile($path);
    unset($data["module"]["simple_sitemap"], $data["module"]["ps_seo"]);
    file_put_contents($path, \Symfony\Component\Yaml\Yaml::dump($data, 4, 2));
  '
  find "${staging}" -name 'simple_sitemap*.yml' -delete

  php -r '
    require getenv("PS_VENDOR_AUTOLOAD");
    $staging = getenv("PS_STAGING");
    foreach (["user.role.seo_admin.yml", "user.role.site_admin.yml"] as $file) {
      $path = $staging . "/" . $file;
      if (!is_file($path)) { continue; }
      $data = \Symfony\Component\Yaml\Yaml::parseFile($path);
      if (!empty($data["dependencies"]["module"])) {
        $data["dependencies"]["module"] = array_values(array_filter(
          $data["dependencies"]["module"],
          static fn (string $m): bool => $m !== "ps_seo"
        ));
      }
      if (!empty($data["permissions"])) {
        $data["permissions"] = array_values(array_filter(
          $data["permissions"],
          static fn (string $p): bool => !str_contains($p, "ps_seo")
        ));
      }
      file_put_contents($path, \Symfony\Component\Yaml\Yaml::dump($data, 4, 2));
    }
  '

  echo "${staging}"
}

ps_site_install_from_config() {
  local site_name="$1"
  local default_lang="$2"
  local staging

  ps_build_cim_staging_dir >/dev/null
  staging="$(ps_cim_staging_dir)"

  ps_info "Drupal site:install minimal (locale=${default_lang})..."
  ps_retry 2 3 ps_drush site:install minimal \
    --site-name="${site_name}" \
    --account-name="${ADMIN_USER}" \
    --account-pass="${ADMIN_PASS}" \
    --account-mail="${ADMIN_MAIL}" \
    --locale="${default_lang}" \
    --yes

  ps_drush config:get system.site uuid --format=string 2>/dev/null | tr -d '[:space:]' \
    > "${staging}/.install-site-uuid"

  # Fresh install UUID must match staged system.site or config:import is rejected.
  export PS_VENDOR_AUTOLOAD="${PS_SRC_DIR}/vendor/autoload.php"
  export PS_STAGING="${staging}"
  php -r '
    require getenv("PS_VENDOR_AUTOLOAD");
    $staging = getenv("PS_STAGING");
    $uuidFile = $staging . "/.install-site-uuid";
    if (!is_file($uuidFile)) {
      fwrite(STDERR, "Missing install site UUID file\n");
      exit(1);
    }
    $uuid = trim((string) file_get_contents($uuidFile));
    $path = $staging . "/system.site.yml";
    if (!is_file($path)) {
      exit(0);
    }
    $data = \Symfony\Component\Yaml\Yaml::parseFile($path);
    $data["uuid"] = $uuid;
    file_put_contents($path, \Symfony\Component\Yaml\Yaml::dump($data, 4, 2));
  ' || ps_die "Could not align system.site UUID for import"

  ps_info "Importing CMI from config/sites/${PS_COUNTRY_CODE} (staged)..."
  export PS_SKIP_CONFIG_IGNORE_ON_INSTALL=1
  ps_retry 2 3 ps_drush config:import -y --source="${staging}" \
    || ps_die "Config import failed for ${PS_COUNTRY_CODE}"
  unset PS_SKIP_CONFIG_IGNORE_ON_INSTALL
}

ps_apply_config_ignore_settings_from_env() {
  ps_info "Applying config_ignore.settings (post-CIM)..."
  ps_drush ev '
    require_once DRUPAL_ROOT . "/modules/custom/ps_core/ps_core.install";
    ps_core_apply_config_ignore_settings();
    echo "config_ignore OK\n";
  ' || ps_warn "config_ignore apply failed"
}

ps_enable_seo_modules_post_import() {
  local sync_dir
  sync_dir="$(ps_config_sync_dir)"

  ps_info "Enabling simple_sitemap + ps_seo (post-CIM)..."
  ps_drush pm:enable -y simple_sitemap ps_seo \
    || ps_die "Could not enable SEO modules post-import"

  local partial_dir="${PS_SRC_DIR}/tmp/cim-deferred-sitemap"
  rm -rf "${partial_dir}"
  mkdir -p "${partial_dir}"
  while IFS= read -r file; do
    [[ -z "${file}" ]] && continue
    cp "${file}" "${partial_dir}/"
  done < <(find "${sync_dir}" -name 'simple_sitemap*.yml' -type f 2>/dev/null)

  if [[ "$(find "${partial_dir}" -name '*.yml' | wc -l)" -gt 0 ]]; then
    ps_drush config:import -y --partial --source="${partial_dir}" \
      || ps_warn "simple_sitemap partial import had warnings"
  fi
  rm -rf "${partial_dir}"

  partial_dir="${PS_SRC_DIR}/tmp/cim-deferred-seo-roles"
  rm -rf "${partial_dir}"
  mkdir -p "${partial_dir}"
  cp "${sync_dir}/user.role.seo_admin.yml" "${partial_dir}/" 2>/dev/null || true
  cp "${sync_dir}/user.role.site_admin.yml" "${partial_dir}/" 2>/dev/null || true
  if [[ -f "${partial_dir}/user.role.seo_admin.yml" ]]; then
    ps_drush config:import -y --partial --source="${partial_dir}" \
      || ps_warn "SEO role partial import had warnings"
  fi
  rm -rf "${partial_dir}"
  ps_drush_cr
}

ps_uninstall_update_module_if_present() {
  if ps_drush pm:list --status=enabled --filter=update --format=list 2>/dev/null | grep -q '^update$'; then
    ps_drush pm:uninstall update -y 2>/dev/null || true
    ps_drush_cr
  fi
}

# Imports PS Form webforms + spam settings from config/sites/{country}/ (Phase 1 CMI).
ps_import_form_cmi_from_site_config() {
  local sync_dir
  sync_dir="$(ps_config_sync_dir)"
  local partial_dir="${PS_SRC_DIR}/tmp/cim-form-webforms"
  rm -rf "${partial_dir}"
  mkdir -p "${partial_dir}"

  local copied=0
  for pattern in \
    webform.webform.*.yml \
    ps_form.settings.yml \
    altcha.settings.yml \
    antibot.settings.yml \
    honeypot.settings.yml \
    captcha.settings.yml \
    captcha.captcha_point.*.yml \
    webform.settings.yml; do
    for file in "${sync_dir}"/${pattern}; do
      [[ -f "${file}" ]] || continue
      cp "${file}" "${partial_dir}/"
      copied=$((copied + 1))
    done
  done

  if [[ -d "${sync_dir}/language" ]]; then
    mkdir -p "${partial_dir}/language"
    cp -a "${sync_dir}/language/." "${partial_dir}/language/"
    copied=$((copied + $(find "${partial_dir}/language" -name 'webform.webform.*.yml' 2>/dev/null | wc -l)))
  fi

  if [[ ! -f "${partial_dir}/webform.webform.contact.yml" ]]; then
    ps_warn "No webform CMI in ${sync_dir} — skipping form import"
    rm -rf "${partial_dir}"
    return 0
  fi

  ps_info "Importing PS Form CMI (${copied} files) from config/sites/${PS_COUNTRY_CODE}..."
  ps_drush config:import --partial -y --source="${partial_dir}" \
    || ps_die "PS Form CMI import failed for ${PS_COUNTRY_CODE}"
  rm -rf "${partial_dir}"
}

ps_install_homepage_from_configuration() {
  ps_info "Creating homepage node from configuration (post-CIM)..."
  ps_drush ev '
    \Drupal::service("ps_homepage.shell_installer")->installFromConfiguration();
    echo "ps_homepage post-config OK\n";
  ' || ps_die "Homepage post-config install failed"
}
