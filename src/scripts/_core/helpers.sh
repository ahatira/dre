#!/usr/bin/env bash
# Helpers - Utility functions (retry, validation, etc.)

ps_retry() {
  local attempts="$1"
  local delay="$2"
  shift 2

  local n=1
  until "$@"; do
    if [[ ${n} -ge ${attempts} ]]; then
      ps_error "Command failed after ${attempts} attempts"
      return 1
    fi
    ps_warn "Attempt ${n}/${attempts} failed, retrying in ${delay}s..."
    sleep "${delay}"
    n=$((n + 1))
  done
}

ps_require_cmd() {
  local cmd="$1"
  command -v "${cmd}" >/dev/null 2>&1 || ps_die "Required command not found: ${cmd}"
}

ps_require_file() {
  local file="$1"
  [[ -f "${file}" ]] || ps_die "Required file not found: ${file}"
}

ps_drush_bootstrapped() {
  ps_drush status --field=bootstrap 2>/dev/null | grep -qi successful
}

# Enable a module; on failure uninstall a partial install before retrying.
ps_enable_module_robust() {
  local module="$1"
  local attempts="${2:-2}"
  local delay="${3:-2}"
  local n=1

  while [[ ${n} -le ${attempts} ]]; do
    if ps_drush en -y "${module}"; then
      return 0
    fi

    ps_warn "Enable ${module} failed (attempt ${n}/${attempts})"
    if ps_drush pm:list --status=enabled --filter="${module}" --format=list 2>/dev/null | grep -q "^${module}$"; then
      ps_warn "${module} is marked enabled but install may be incomplete; uninstalling..."
      ps_drush pm:uninstall "${module}" -y 2>/dev/null || true
    fi

    if [[ ${n} -ge ${attempts} ]]; then
      ps_error "Command failed after ${attempts} attempts"
      return 1
    fi

    sleep "${delay}"
    n=$((n + 1))
  done

  return 1
}

ps_verify_ps_offer_install() {
  ps_drush ev '
    if (!\Drupal::moduleHandler()->moduleExists("ps_offer")) {
      throw new \RuntimeException("ps_offer is not enabled");
    }
    $fields = \Drupal::service("entity_field.manager")->getFieldDefinitions("node", "offer");
    if (!isset($fields["field_surfaces"])) {
      throw new \RuntimeException("field_surfaces missing on offer bundle");
    }
    if (\Drupal::service("plugin.manager.field.formatter")->getDefinition("ps_surface_contextual", FALSE) === NULL) {
      throw new \RuntimeException("ps_surface_contextual formatter plugin not found");
    }
    $display = \Drupal::entityTypeManager()->getStorage("entity_view_display")->load("node.offer.favorite_card");
    if ($display === NULL) {
      throw new \RuntimeException("node.offer.favorite_card display missing");
    }
    echo "ps_offer OK\n";
  ' || return 1
}
