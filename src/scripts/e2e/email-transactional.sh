#!/usr/bin/env bash
# Transactional email E2E suite (Phase 6) — Mailpit API assertions across PS modules.
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SRC_DIR="$(cd "${SCRIPT_DIR}/../.." && pwd)"

export PS_E2E_COUNTRY="${PS_E2E_COUNTRY:-${COUNTRY:-com}}"

# shellcheck source=/dev/null
source "${SCRIPT_DIR}/common.sh"

SCRIPTS=(
  "web/modules/custom/ps_compare/tests/b2b_compare_email.sh"
  "web/modules/custom/ps_search/tests/e2e_search_alert_digest.sh"
  "web/modules/custom/ps_migrate/tests/e2e_import_alert.sh"
  "web/modules/custom/ps_form/tests/b2b_contact_email_handlers.sh"
)

PASS=0
FAIL=0
CONTACT_SHELL_SNAPSHOT_FILE=""

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

restore_contact_email_config() {
  if [[ -n "${CONTACT_SHELL_SNAPSHOT_FILE}" && -f "${CONTACT_SHELL_SNAPSHOT_FILE}" ]]; then
    ps_e2e_drush php:eval "
\$json = file_get_contents('${CONTACT_SHELL_SNAPSHOT_FILE}');
\$snapshot = json_decode(\$json, TRUE, 512, JSON_THROW_ON_ERROR);
\\Drupal::configFactory()->getEditable('ps_email.shell')->setData(\$snapshot)->save(TRUE);
print 'restored';
" >/dev/null 2>&1 || true
    rm -f "${CONTACT_SHELL_SNAPSHOT_FILE}"
  fi
}

bootstrap_contact_email_config() {
  CONTACT_SHELL_SNAPSHOT_FILE="$(mktemp)"
  ps_e2e_drush cget ps_email.shell --format=json > "${CONTACT_SHELL_SNAPSHOT_FILE}" 2>/dev/null || echo '{}' > "${CONTACT_SHELL_SNAPSHOT_FILE}"

  ps_e2e_drush cset ps_email.shell legal_markup '<p>BNP Paribas Real Estate processes your personal data to handle your request. For more information on how we process your data and your rights, please consult our <a href="https://data-privacy.realestate.bnpparibas/en">Data Protection Notice</a>.</p>' -y >/dev/null
}

trap restore_contact_email_config EXIT

echo "== PS transactional email E2E suite (@ps.${PS_E2E_COUNTRY}) =="
echo "Scripts: ${#SCRIPTS[@]}"
echo ""

for relative in "${SCRIPTS[@]}"; do
  script="${SRC_DIR}/${relative}"
  name="$(basename "${relative}")"
  echo "---------- ${name} ----------"
  if [[ ! -f "${script}" ]]; then
    fail "Missing script ${relative}"
    continue
  fi
  if [[ "${name}" == "b2b_contact_email_handlers.sh" ]]; then
    bootstrap_contact_email_config
  fi
  if bash "${script}"; then
    pass "${name} completed"
  else
    fail "${name} failed"
  fi
  echo ""
done

echo "== Summary: ${PASS} passed, ${FAIL} failed =="
if [[ "${FAIL}" -gt 0 ]]; then
  exit 1
fi
