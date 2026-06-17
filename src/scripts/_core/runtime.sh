#!/usr/bin/env bash
# Runtime detection — host-first Drush, Docker optional.

ps_docker_available() {
  command -v docker >/dev/null 2>&1
}

ps_docker_container_running() {
  local container="$1"
  ps_docker_available \
    && docker ps --filter "name=${container}" --filter "status=running" --format '{{.Names}}' 2>/dev/null \
    | grep -qx "${container}"
}

ps_in_docker() {
  ps_docker_container_running "${PS_PHP_CONTAINER}"
}

ps_docker_exec_php() {
  docker exec -u www-data -i "${PS_PHP_CONTAINER}" bash -c "cd ${PS_DRUPAL_ROOT} && $*"
}

PS_NPM_CONTAINER="${PS_NPM_CONTAINER:-ps_npm}"
PS_NODE_IMAGE="${PS_NODE_IMAGE:-node:20-alpine}"

# CI/Jenkins: prefer pinned Node/npm in Docker over agent toolchain (e.g. npm 10.7.0).
ps_npm_use_docker() {
  [[ "${PS_NPM_DOCKER:-}" == "1" ]] && return 0
  [[ "${PS_NPM_DOCKER:-}" == "0" ]] && return 1
  [[ -n "${CI:-}" || -n "${JENKINS_URL:-}" || -n "${JENKINS_HOME:-}" ]] && return 0
  return 1
}

# True when Linux node/npm are available (not Windows interop via /mnt/c).
ps_npm_usable_on_host() {
  if ps_npm_use_docker; then
    return 1
  fi
  local npm_path node_path
  npm_path="$(command -v npm 2>/dev/null || true)"
  node_path="$(command -v node 2>/dev/null || true)"
  [[ -n "${npm_path}" && -n "${node_path}" ]] || return 1
  [[ "${npm_path}" != /mnt/c/* && "${node_path}" != /mnt/c/* ]] || return 1
  node --version >/dev/null 2>&1 && npm --version >/dev/null 2>&1
}

# Reproducible install when package-lock.json is committed (CI/Jenkins).
ps_npm_install_cmd() {
  local dir="$1"
  if [[ -f "${dir}/package-lock.json" ]]; then
    printf '%s' 'npm ci --no-audit --no-fund'
  else
    printf '%s' 'npm install --no-save --no-audit --no-fund'
  fi
}

# Run npm/node in cwd. Uses host when usable, else Node Docker image with PS_SRC_DIR bind mount.
ps_npm_exec() {
  local cwd="$1"
  shift

  if ps_npm_usable_on_host; then
    ( cd "${cwd}" && "$@" )
    return
  fi

  ps_require_cmd docker

  local workspace="/workspace"
  if [[ "${cwd}" != "${PS_SRC_DIR}" ]]; then
    workspace="/workspace/${cwd#"${PS_SRC_DIR}/"}"
  fi

  ps_info "Using Docker ${PS_NODE_IMAGE} for npm ($( \
    if ps_npm_use_docker; then echo 'CI/Jenkins or PS_NPM_DOCKER=1'; else echo 'host npm unavailable'; fi \
  ))"
  docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "${PS_SRC_DIR}:/workspace" \
    -w "${workspace}" \
    "${PS_NODE_IMAGE}" \
    "$@"
}

# Fix root-owned paths after prior Docker npm runs (WSL bind mounts).
ps_npm_fix_ownership_if_needed() {
  local target="$1"
  [[ -e "${target}" ]] || return 0
  if [[ -w "${target}" ]] \
    && ! find "${target}" -not -user "$(id -u)" -print -quit 2>/dev/null | grep -q .; then
    return 0
  fi
  ps_require_cmd docker
  local rel="${target#${PS_SRC_DIR}/}"
  ps_warn "Fixing ownership on ${rel}"
  docker run --rm \
    -u 0:0 \
    -v "${PS_SRC_DIR}:/workspace" \
    "${PS_NODE_IMAGE}" \
    sh -lc "chown -R $(id -u):$(id -g) /workspace/${rel}"
}

ps_npm_fix_libraries_permissions() {
  ps_npm_fix_ownership_if_needed "${PS_WEB_DIR}/libraries"
}

# Fix non-executable npm bin stubs after Windows/WSL cross-install on bind mounts.
ps_npm_prepare() {
  local cwd="$1"
  ps_npm_exec "${cwd}" sh -lc 'find node_modules/.bin -type f -exec chmod +x {} + 2>/dev/null || true'
}

# ps_theme SCSS imports web/libraries/bootstrap (see ps_theme/README.md).
ps_link_theme_bootstrap_library() {
  local bootstrap_src="${PS_WEB_DIR}/themes/custom/ui_suite_bnp/node_modules/bootstrap"
  local lib_link="${PS_WEB_DIR}/libraries/bootstrap"

  ps_require_file "${bootstrap_src}/scss/_functions.scss" \
    "Bootstrap not found in ui_suite_bnp (build ui_suite_bnp theme first)"

  mkdir -p "${PS_WEB_DIR}/libraries"
  rm -rf "${lib_link}"
  if ln -sfn "../themes/custom/ui_suite_bnp/node_modules/bootstrap" "${lib_link}" 2>/dev/null; then
    ps_success "Bootstrap linked at web/libraries/bootstrap"
  else
    ps_warn "Symlink failed — copying bootstrap to web/libraries/bootstrap"
    cp -a "${bootstrap_src}" "${lib_link}"
    ps_success "Bootstrap copied to web/libraries/bootstrap"
  fi
}

ps_resolve_runtime() {
  if [[ -n "${PS_RUNTIME:-}" ]]; then
    return 0
  fi

  ps_load_config
  local db_host
  db_host="$(ps_env_get DB_HOST postgres)"

  # Docker Compose dev: DB_HOST=postgres resolves inside ps_php only.
  if ps_in_docker && [[ "${db_host}" == "postgres" || "${db_host}" == "db" ]]; then
    PS_RUNTIME="docker"
  elif [[ -x "${PS_SRC_DIR}/vendor/bin/drush" ]]; then
    PS_RUNTIME="host"
  elif ps_in_docker; then
    PS_RUNTIME="docker"
  else
    ps_die "Drush not found. Run: cd src && composer install — or start Docker (make up)"
  fi
  export PS_RUNTIME
}

ps_solr_init_cores() {
  local init_script="${PS_PROJECT_ROOT}/docker/solr/init-cores.sh"
  local solr_container="${SOLR_CONTAINER:-ps_solr}"

  if [[ ! -f "${init_script}" ]]; then
    ps_warn "Solr init script not found: ${init_script}"
    return 1
  fi
  if ! ps_docker_container_running "${solr_container}"; then
    ps_warn "Solr container not running (${solr_container})"
    return 1
  fi
  chmod +x "${init_script}"
  SOLR_CONTAINER="${solr_container}" bash "${init_script}"
}
