#!/usr/bin/env bash
# E2E — RBAC SEC-* and CTX-ADM-* (QA cahier §8.1, §9).
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PASS=0
FAIL=0
SKIP=0
TMP="${TMPDIR:-/tmp}/ps-rbac-e2e-$$"
mkdir -p "$TMP"

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }
skip() { echo "  SKIP: $1"; SKIP=$((SKIP + 1)); }

cleanup() { rm -rf "$TMP"; }
trap cleanup EXIT

drush() {
  ps_e2e_drush "$@"
}

login_user() {
  local username="$1"
  local jar="$TMP/cookies-${username}.txt"
  rm -f "$jar"
  touch "$jar"
  local uli
  uli=$(drush uli --name="${username}" --uri="${BASE}" 2>/dev/null | tail -1 || true)
  if [[ -z "$uli" ]]; then
    echo ""
    return 1
  fi
  curl -sL -m 120 -b "$jar" -c "$jar" -o /dev/null "$uli" 2>/dev/null || true
  echo "$jar"
}

assert_http_as_user() {
  local username="$1"
  local expected="$2"
  local url="$3"
  local label="$4"
  local jar
  jar=$(login_user "$username") || {
    fail "${label} — could not login ${username}"
    return
  }
  local code
  code=$(curl -s -o /dev/null -w '%{http_code}' -m 60 -b "$jar" -c "$jar" "$url" 2>/dev/null || echo "000")
  if [[ "$code" == "$expected" ]]; then
    pass "${label} (HTTP ${code})"
  else
    fail "${label} — expected HTTP ${expected}, got ${code} (${url})"
  fi
}

load_drush_results() {
  local line key value
  while IFS= read -r line; do
    [[ -z "$line" || "$line" != *"="* ]] && continue
    key="${line%%=*}"
    value="${line#*=}"
    printf -v "DRUSH_${key}" '%s' "$value"
  done < <(drush php:script "web/modules/custom/bnp_admin/tests/e2e_rbac_sec_ctx.evaluate.php" 2>/dev/null)
}

echo "=== RBAC E2E — SEC & CTX-ADM (${BASE}) ==="

echo "--- Prerequisites (Drush) ---"
load_drush_results

if [[ "${DRUSH_roles_ok:-}" == "yes" ]]; then
  pass "BNP roles present, legacy PS roles absent"
else
  fail "Role check failed: ${DRUSH_roles_detail:-unknown} (run: make rbac-sync)"
fi

if [[ "${DRUSH_users_ok:-}" == "yes" ]]; then
  pass "Test users exist"
else
  fail "Missing test users: ${DRUSH_users_detail:-unknown} (run: make create-test-users)"
fi

echo "--- SEC (Drush access) ---"
[[ "${DRUSH_sec01:-}" == "allowed" ]] && pass "SEC-01 Drush: content.editor can add offer" || fail "SEC-01 Drush: expected allowed, got ${DRUSH_sec01:-}"
[[ "${DRUSH_sec02:-}" == "denied" ]] && pass "SEC-02 Drush: content.editor denied matrix" || fail "SEC-02 Drush: expected denied, got ${DRUSH_sec02:-}"
[[ "${DRUSH_sec03:-}" == "allowed" ]] && pass "SEC-03 Drush: site.admin allowed matrix" || fail "SEC-03 Drush: expected allowed, got ${DRUSH_sec03:-}"
[[ "${DRUSH_sec04:-}" == "denied" ]] && pass "SEC-04 Drush: user without offer permission denied" || fail "SEC-04 Drush: expected denied, got ${DRUSH_sec04:-}"
[[ "${DRUSH_sec05_add:-}" == "allowed" ]] && pass "SEC-05 Drush: content.admin can add offer" || fail "SEC-05 Drush add: expected allowed, got ${DRUSH_sec05_add:-}"
[[ "${DRUSH_sec05_edit:-}" == "allowed" ]] && pass "SEC-05 Drush: content.admin can edit foreign offer" || fail "SEC-05 Drush edit: expected allowed, got ${DRUSH_sec05_edit:-}"
[[ "${DRUSH_sec06:-}" == "denied" ]] && pass "SEC-06 Drush: content.editor cannot edit foreign offer" || fail "SEC-06 Drush: expected denied, got ${DRUSH_sec06:-}"

echo "--- SEC (HTTP) ---"
assert_http_as_user "content.editor" "200" "${BASE}/node/add/offer" "SEC-01 HTTP: offer form for content.editor"
assert_http_as_user "content.editor" "403" "${BASE}/admin/ps/config/context" "SEC-02 HTTP: context denied for content.editor"
assert_http_as_user "site.admin" "200" "${BASE}/admin/ps/config/context" "SEC-03 HTTP: context OK for site.admin"

echo "--- CTX-ADM ---"
if [[ "${DRUSH_ctx_adm01_rules:-0}" -ge 15 ]]; then
  pass "CTX-ADM-01: matrix has ${DRUSH_ctx_adm01_rules} rules (>= 15)"
else
  fail "CTX-ADM-01: expected >= 15 rules, got ${DRUSH_ctx_adm01_rules:-0}"
fi

assert_http_as_user "site.admin" "200" "${BASE}/admin/ps/config/context/overview" "CTX-ADM-01 HTTP: context overview for site.admin"
assert_http_as_user "site.admin" "200" "${BASE}/admin/ps/config/context/rules" "CTX-ADM-01 HTTP: context rules for site.admin"
assert_http_as_user "content.editor" "403" "${BASE}/admin/ps/config/context/rules" "CTX-ADM-02 HTTP: context denied for content.editor"

MATRIX_JAR=$(login_user "site.admin")
if [[ -n "$MATRIX_JAR" ]]; then
  html=$(curl -sL -m 60 -b "$MATRIX_JAR" -c "$MATRIX_JAR" "${BASE}/admin/ps/config/context/rules" 2>/dev/null || true)
  if [[ "$html" == *"asset_type_cow"* || "$html" == *"Coworking"* || "$html" == *"COW"* ]]; then
    pass "CTX-ADM-03 hint: COW rule visible on matrix page"
  else
    skip "CTX-ADM-03: COW rule label not detected in HTML (manual check)"
  fi
else
  skip "CTX-ADM-03: could not load matrix HTML"
fi

echo ""
echo "=== Results: ${PASS} passed, ${FAIL} failed, ${SKIP} skipped ==="
if [[ "$FAIL" -gt 0 ]]; then
  exit 1
fi
