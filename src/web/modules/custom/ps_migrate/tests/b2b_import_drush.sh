#!/usr/bin/env bash
# B2B smoke tests — CRM import Drush commands and unified source plugin.
set -euo pipefail

export PS_E2E_COUNTRY="${PS_E2E_COUNTRY:-${COUNTRY:-fr}}"

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

PASS=0
FAIL=0

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "=== PS Migrate B2B — Drush pipeline (@ps.${PS_E2E_COUNTRY}) ==="

QUEUE_STATUS="$(ps_e2e_drush ps:import:queue-status 2>&1 || true)"
if echo "$QUEUE_STATUS" | grep -qiE 'pending|processing|queue|0'; then
  pass "ps:import:queue-status responds"
else
  fail "ps:import:queue-status unexpected output"
  echo "$QUEUE_STATUS" | tail -5
fi

SOURCE_CHECK="$(ps_e2e_drush php:eval "
\$expected = [
  'ps_offer_from_xml' => 'offer',
  'ps_agent_from_xml' => 'agent',
  'ps_feature_groups_from_xml' => 'feature_groups',
  'ps_offer_translations_from_xml' => 'offer_translations',
];
\$missing = [];
foreach (\$expected as \$id => \$mode) {
  \$source = \\Drupal::config('migrate_plus.migration.' . \$id)->get('source');
  if (!is_array(\$source)) {
    \$missing[] = \$id . ':no-source';
    continue;
  }
  if ((\$source['plugin'] ?? '') !== 'ps_crm_offer_xml') {
    \$missing[] = \$id . ':plugin=' . (\$source['plugin'] ?? '?');
    continue;
  }
  if ((\$source['mode'] ?? '') !== \$mode) {
    \$missing[] = \$id . ':mode=' . (\$source['mode'] ?? '?');
  }
}
print \$missing === [] ? 'ok' : implode(',', \$missing);
" 2>/dev/null | tail -1)"

if [[ "$SOURCE_CHECK" == "ok" ]]; then
  pass "CRM migrations use ps_crm_offer_xml with expected modes"
else
  fail "Unified source plugin mismatch: ${SOURCE_CHECK}"
fi

RECOVER="$(ps_e2e_drush ps:import:recover-stale 2>&1 || true)"
if echo "$RECOVER" | grep -qiE 'recovered|stale|processing|0|none'; then
  pass "ps:import:recover-stale responds"
else
  fail "ps:import:recover-stale unexpected output"
  echo "$RECOVER" | tail -5
fi

ROLLBACK_RUN="$(ps_e2e_drush php:eval "
\$storage = \\Drupal::entityTypeManager()->getStorage('import_run');
\$ids = \$storage->getQuery()
  ->accessCheck(FALSE)
  ->condition('pipeline_status', 'success')
  ->sort('id', 'DESC')
  ->range(0, 1)
  ->execute();
print \$ids ? (string) reset(\$ids) : '';
" 2>/dev/null | tail -1)"

if [[ -n "$ROLLBACK_RUN" ]]; then
  ROLLBACK_HELP="$(ps_e2e_drush ps:import:rollback --help 2>&1 || true)"
  if echo "$ROLLBACK_HELP" | grep -q 'run-id'; then
    pass "ps:import:rollback command available (run ${ROLLBACK_RUN})"
  else
    fail "ps:import:rollback help missing run-id option"
  fi
else
  fail "No successful import run found for rollback command check"
fi

echo ""
echo "=== Results: $PASS passed, $FAIL failed ==="
if [[ "$FAIL" -gt 0 ]]; then
  exit 1
fi

echo "CRM import Drush B2B passed."
