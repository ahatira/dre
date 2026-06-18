#!/usr/bin/env bash
# B2B smoke tests — Undo removal via toggle API (remove then restore).
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

PASS=0
FAIL=0

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "=== PS Compare B2B — Undo toggle flow ==="

UNDO_RESULT=$(ps_e2e_drush php:eval "
\$account = \\Drupal\\user\\Entity\\User::load(1);
\\Drupal::service(\"account_switcher\")->switchTo(\$account);
\$storage = \\Drupal::entityTypeManager()->getStorage(\"node\");
\$ids = array_values(\$storage->getQuery()->accessCheck(TRUE)->condition(\"type\", \"offer\")->range(0, 2)->execute());
if (count(\$ids) < 2) { print \"FAIL:not_enough_offers\"; \\Drupal::service(\"account_switcher\")->switchBack(); return; }
\$manager = \\Drupal::service(\"ps_compare.manager\");
foreach (\$ids as \$id) { \$manager->removeCompare(\$storage->load(\$id)); }
foreach (\$ids as \$id) { \$manager->addCompare(\$storage->load(\$id)); }
\$controller = \\Drupal::service(\"Drupal\\\\ps_compare\\\\Controller\\\\CompareToggleController\");
\$csrf = \\Drupal::service(\"csrf_token\")->get(\"ps_compare.toggle\");
\$targetId = (int) \$ids[0];
\$request = \\Symfony\\Component\\HttpFoundation\\Request::create(\"\", \"POST\", [], [], [], [\"HTTP_X_CSRF_TOKEN\" => \$csrf]);
\$remove = \$controller->toggle(\$request, \"node\", \$targetId);
\$removeData = json_decode(\$remove->getContent(), true);
if ((\$removeData[\"isCompared\"] ?? TRUE) !== FALSE) { print \"FAIL:remove\"; \\Drupal::service(\"account_switcher\")->switchBack(); return; }
if ((int) (\$removeData[\"count\"] ?? 0) !== 1) { print \"FAIL:count_after_remove\"; \\Drupal::service(\"account_switcher\")->switchBack(); return; }
\$restore = \$controller->toggle(\$request, \"node\", \$targetId);
\$restoreData = json_decode(\$restore->getContent(), true);
\\Drupal::service(\"account_switcher\")->switchBack();
if ((\$restoreData[\"isCompared\"] ?? FALSE) !== TRUE) { print \"FAIL:restore\"; return; }
if ((int) (\$restoreData[\"count\"] ?? 0) !== 2) { print \"FAIL:count_after_restore\"; return; }
\$undoSetting = \\Drupal::config(\"ps_compare.settings\")->get(\"display_undo_removal\");
if (\$undoSetting === NULL) { print \"FAIL:undo_config_missing\"; return; }
print \"PASS:undo_toggle\";
" 2>/dev/null | tail -1)

if [[ "$UNDO_RESULT" == "PASS:undo_toggle" ]]; then
  pass "Toggle remove + restore restores compare count"
else
  fail "Undo toggle API ($UNDO_RESULT)"
fi

LIB_RESULT=$(ps_e2e_drush php:eval "
\$paths = array_map(static fn (array \$item): string => (string) (\$item[\"data\"] ?? \"\"), \\Drupal::service(\"library.discovery\")->getLibraryByName(\"ps_compare\", \"compare-toggle\")[\"js\"] ?? []);
print in_array(\"modules/custom/ps_compare/js/ps-compare-undo.js\", \$paths, true) ? \"PASS:undo_js\" : \"FAIL:undo_js\";
" 2>/dev/null | tail -1)
if [[ "$LIB_RESULT" == "PASS:undo_js" ]]; then
  pass "Undo JS in compare-toggle library"
else
  fail "Undo JS library ($LIB_RESULT)"
fi

LEGACY_JS=$([[ -f "${PS_E2E_SRC_DIR}/web/modules/custom/ps_compare/js/ps-compare-share.js" ]] && echo present || echo absent)
if [[ "$LEGACY_JS" == "absent" ]]; then
  pass "Legacy ps-compare-share.js removed"
else
  fail "Legacy ps-compare-share.js still present"
fi

echo ""
echo "=== Results: $PASS passed, $FAIL failed ==="
if [[ "$FAIL" -gt 0 ]]; then
  exit 1
fi
