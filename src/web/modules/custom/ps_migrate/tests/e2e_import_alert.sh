#!/usr/bin/env bash
# E2E — CRM import pipeline failure email alert via Mailpit.
set -euo pipefail

export PS_E2E_COUNTRY="${PS_E2E_COUNTRY:-${COUNTRY:-fr}}"

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

MAILPIT_API="${MAILPIT_API:-http://localhost:8025/api/v1}"
MAILPIT="${MAILPIT_URL:-http://localhost:8025}"
TEST_EMAIL="import-alert-e2e-$(date +%s)@example.com"
SYNC_FILE="e2e_import_alert_sync.xml"
QUEUE_FILE="e2e_import_alert_queue.xml"
DISABLED_FILE="e2e_import_alert_disabled.xml"
NORECIP_FILE="e2e_import_alert_norecip.xml"

PASS=0
FAIL=0
MAILPIT_OK=0
ORIG_ENABLED=""
ORIG_RECIPIENTS=""

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

ps_e2e_clear_incoming_xml() {
  ps_e2e_drush php:eval "$(cat <<'PHP'
$resolver = \Drupal::service('ps_migrate.import_pipeline_path_resolver');
foreach ($resolver->listIncomingXmlFiles() as $path) {
  @unlink($path);
}
print 'cleared_incoming';
PHP
)" >/dev/null 2>&1 || true
}

ps_e2e_reset_import_queue() {
  ps_e2e_drush php:eval "$(cat <<'PHP'
$queue = \Drupal::queue('ps_migrate.import_file');
if (method_exists($queue, 'deleteQueue')) {
  $queue->deleteQueue();
}
\Drupal::state()->delete('ps_migrate.import_pipeline.enqueued_checksums');
print 'cleared_queue';
PHP
)" >/dev/null 2>&1 || true
}

ps_e2e_seed_invalid_xml() {
  local filename="$1"
  ps_e2e_drush php:eval "$(cat <<PHP
\$resolver = \\Drupal::service('ps_migrate.import_pipeline_path_resolver');
\$fs = \\Drupal::service('file_system');
\$uri = \$resolver->buildUri('incoming', '${filename}');
\$resolver->prepareDirectory(\$resolver->getPath('incoming'));
\$real = \$fs->realpath(\$uri);
if (\$real === FALSE) {
  throw new \\RuntimeException('Could not resolve incoming URI: ' . \$uri);
}
\$content = '<?xml version="1.0"?><broken-e2e';
file_put_contents(\$real, \$content);
print 'seeded:' . \$real;
PHP
)" 2>/dev/null | tail -1
}

mailpit_clear() {
  curl -sS -X DELETE "${MAILPIT_API}/messages" >/dev/null 2>&1 || true
}

mailpit_count() {
  curl -sS "${MAILPIT_API}/messages" 2>/dev/null | python3 -c "import json,sys; print(json.load(sys.stdin).get('total', 0))" 2>/dev/null || echo "0"
}

mailpit_has_recipient() {
  local email="$1"
  local subject_contains="${2:-}"
  MAILPIT_API="${MAILPIT_API}" EMAIL="${email}" SUBJECT="${subject_contains}" python3 - <<'PY'
import json, os, sys, urllib.request

api = os.environ["MAILPIT_API"]
email = os.environ["EMAIL"].lower()
subject_needle = os.environ.get("SUBJECT", "")

try:
    data = json.load(urllib.request.urlopen(f"{api}/messages?limit=20"))
except Exception:
    print("no")
    sys.exit(0)

for msg in data.get("messages") or []:
    to_ok = any(email in (entry.get("Address") or "").lower() for entry in (msg.get("To") or []))
    subj = msg.get("Subject") or ""
    subj_ok = (subject_needle == "") or (subject_needle in subj)
    if to_ok and subj_ok:
        print("yes")
        sys.exit(0)

print("no")
PY
}

restore_pipeline_alert_config() {
  if [[ -n "${ORIG_ENABLED}" && -n "${ORIG_RECIPIENTS}" ]]; then
    ps_e2e_drush cset ps_migrate.import_pipeline_settings alert_email_enabled "${ORIG_ENABLED}" -y >/dev/null 2>&1 || true
    ps_e2e_drush cset ps_migrate.import_pipeline_settings alert_email_recipients "${ORIG_RECIPIENTS}" -y >/dev/null 2>&1 || true
  fi
}

cleanup() {
  restore_pipeline_alert_config
}
trap cleanup EXIT

echo "== CRM import failure alert E2E (@ps.${PS_E2E_COUNTRY}) =="
echo "Recipient: ${TEST_EMAIL}"

if curl -sS -o /dev/null -w '%{http_code}' "${MAILPIT_API}/messages" | grep -qE '^200$'; then
  MAILPIT_OK=1
  mailpit_clear
  pass "Mailpit reachable at ${MAILPIT}"
else
  fail "Mailpit unreachable at ${MAILPIT_API} — email assertions will be skipped"
fi

ORIG_ENABLED="$(ps_e2e_drush cget ps_migrate.import_pipeline_settings alert_email_enabled --format=string 2>/dev/null || echo 1)"
ORIG_RECIPIENTS="$(ps_e2e_drush cget ps_migrate.import_pipeline_settings alert_email_recipients --format=string 2>/dev/null || echo '')"

ps_e2e_drush cset ps_migrate.import_pipeline_settings alert_email_enabled 1 -y >/dev/null
ps_e2e_drush cset ps_migrate.import_pipeline_settings alert_email_recipients "${TEST_EMAIL}" -y >/dev/null
pass "Pipeline alert config set for test recipient"

echo "--- Sync import failure should send alert ---"
ps_e2e_clear_incoming_xml
SEED_RESULT="$(ps_e2e_seed_invalid_xml "${SYNC_FILE}")"
echo "${SEED_RESULT}" | grep -q '^seeded:' || { fail "Could not seed ${SYNC_FILE} (${SEED_RESULT})"; echo "=== Results: ${PASS} passed, ${FAIL} failed ==="; exit 1; }
pass "Seeded invalid XML (${SYNC_FILE})"

if [[ "${MAILPIT_OK}" -eq 1 ]]; then
  mailpit_clear
fi

RUN_RESULT="$(ps_e2e_drush ps:import:run --sync=1 --limit=1 2>&1 || true)"
echo "${RUN_RESULT}" | tail -3
echo "${RUN_RESULT}" | grep -q "failed=1" && pass "Sync import failed as expected" || fail "Sync import did not report failure"

if [[ "${MAILPIT_OK}" -eq 1 ]]; then
  sleep 1
  if [[ "$(mailpit_has_recipient "${TEST_EMAIL}" "CRM import failed: ${SYNC_FILE}")" == "yes" ]]; then
    pass "Mailpit received sync failure alert"
  else
    fail "Mailpit missing sync failure alert for ${TEST_EMAIL}"
  fi
fi

echo "--- Queue import failure should send alert ---"
if [[ "${MAILPIT_OK}" -eq 1 ]]; then
  mailpit_clear
fi

ps_e2e_clear_incoming_xml
ps_e2e_reset_import_queue
SEED_RESULT="$(ps_e2e_seed_invalid_xml "${QUEUE_FILE}")"
echo "${SEED_RESULT}" | grep -q '^seeded:' || { fail "Could not seed ${QUEUE_FILE}"; echo "=== Results: ${PASS} passed, ${FAIL} failed ==="; exit 1; }
pass "Seeded invalid XML (${QUEUE_FILE})"

ps_e2e_drush ps:import:enqueue >/dev/null
QUEUE_RESULT="$(ps_e2e_drush ps:import:queue-process --count=1 2>&1 || true)"
echo "${QUEUE_RESULT}" | tail -3
echo "${QUEUE_RESULT}" | grep -q "failed=1" && pass "Queue import failed as expected" || fail "Queue import did not report failure"

if [[ "${MAILPIT_OK}" -eq 1 ]]; then
  sleep 1
  if [[ "$(mailpit_has_recipient "${TEST_EMAIL}" "CRM import failed: ${QUEUE_FILE}")" == "yes" ]]; then
    pass "Mailpit received queue failure alert"
  else
    fail "Mailpit missing queue failure alert for ${TEST_EMAIL}"
  fi
fi

echo "--- Disabled alerts should not send email ---"
if [[ "${MAILPIT_OK}" -eq 1 ]]; then
  mailpit_clear
fi

ps_e2e_clear_incoming_xml
ps_e2e_drush cset ps_migrate.import_pipeline_settings alert_email_enabled 0 -y >/dev/null
SEED_RESULT="$(ps_e2e_seed_invalid_xml "${DISABLED_FILE}")"
echo "${SEED_RESULT}" | grep -q '^seeded:' || fail "Could not seed ${DISABLED_FILE}"
(ps_e2e_drush ps:import:run --sync=1 --limit=1 >/dev/null 2>&1 || true)

if [[ "${MAILPIT_OK}" -eq 1 ]]; then
  COUNT="$(mailpit_count)"
  if [[ "${COUNT}" == "0" ]]; then
    pass "No email sent when alert_email_enabled=0"
  else
    fail "Unexpected ${COUNT} email(s) when alerts disabled"
  fi
fi

echo "--- Empty recipients should not send email ---"
if [[ "${MAILPIT_OK}" -eq 1 ]]; then
  mailpit_clear
fi

ps_e2e_clear_incoming_xml
ps_e2e_drush cset ps_migrate.import_pipeline_settings alert_email_enabled 1 -y >/dev/null
ps_e2e_drush cset ps_migrate.import_pipeline_settings alert_email_recipients '' -y >/dev/null
SEED_RESULT="$(ps_e2e_seed_invalid_xml "${NORECIP_FILE}")"
echo "${SEED_RESULT}" | grep -q '^seeded:' || fail "Could not seed ${NORECIP_FILE}"
(ps_e2e_drush ps:import:run --sync=1 --limit=1 >/dev/null 2>&1 || true)

if [[ "${MAILPIT_OK}" -eq 1 ]]; then
  COUNT="$(mailpit_count)"
  if [[ "${COUNT}" == "0" ]]; then
    pass "No email sent when alert_email_recipients is empty"
  else
    fail "Unexpected ${COUNT} email(s) with empty recipients"
  fi
fi

WATCHDOG_CHECK="$(ps_e2e_drush php:eval "
\$messages = \\Drupal::database()->select('watchdog', 'w')
  ->fields('w', ['message'])
  ->condition('type', 'ps_migrate')
  ->orderBy('wid', 'DESC')
  ->range(0, 30)
  ->execute()
  ->fetchCol();
foreach (\$messages as \$message) {
  if (stripos((string) \$message, 'alert skipped') !== FALSE) {
    print 'found';
    return;
  }
}
print 'missing';
" 2>/dev/null | tail -1)"
if [[ "${WATCHDOG_CHECK}" == "found" ]]; then
  pass "Watchdog logged alert skipped without recipients"
else
  fail "Watchdog missing alert skipped warning"
fi

echo ""
echo "=== Results: ${PASS} passed, ${FAIL} failed ==="
if [[ "${FAIL}" -gt 0 ]]; then
  exit 1
fi

echo "CRM import failure alert E2E passed."
