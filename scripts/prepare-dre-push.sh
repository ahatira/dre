#!/usr/bin/env bash
# Audit and prepare ps_project_wsl for initial push to https://github.com/ahatira/dre.git
# First-install target: no hook_update_N, module + library versions 1.0.0.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

issues=0

section() {
  echo ""
  echo "${CYAN}=== $1 ===${NC}"
}

warn() {
  echo "${YELLOW}WARN${NC} $1"
  issues=$((issues + 1))
}

ok() {
  echo "${GREEN}OK${NC} $1"
}

fail() {
  echo "${RED}FAIL${NC} $1"
  issues=$((issues + 1))
}

section "Git remotes"
if git remote get-url dre >/dev/null 2>&1; then
  ok "Remote 'dre' → $(git remote get-url dre)"
else
  warn "Remote 'dre' not configured. Add with: git remote add dre https://github.com/ahatira/dre.git"
fi

section "Tracked dev / workspace paths (should be untracked)"
for path in .cursor .claude archived-scripts backup src/backup; do
  count=$(git ls-files "$path" 2>/dev/null | wc -l)
  if [[ "$count" -gt 0 ]]; then
    fail "$path — $count tracked files (run: git rm -r --cached $path)"
  else
    ok "$path — not tracked"
  fi
done

section "Config sync (optional — skipped for dre initial release)"
sync_count=$(find src/config/sync -name '*.yml' 2>/dev/null | wc -l)
tracked_sync=$(git ls-files 'src/config/sync/*.yml' 2>/dev/null | wc -l)
echo "  Local YAML files: $sync_count"
echo "  Tracked in git:   $tracked_sync"
if [[ "$tracked_sync" -gt 0 ]]; then
  ok "config/sync tracked"
elif [[ "$sync_count" -gt 0 ]]; then
  echo "  (intentionally untracked for this release)"
else
  echo "  No local sync directory"
fi

section "Secrets / sensitive files"
sensitive=$(git ls-files | grep -iE '(^|/)\.env($|\.)|credentials\.|/settings\.php$|/settings\.local\.php$' \
  | grep -v default.settings.php | grep -v example.settings.php || true)
if [[ -n "$sensitive" ]]; then
  fail "Sensitive paths tracked: $sensitive"
else
  ok "No settings.php / .env in tracked files"
fi

section "hook_update_N / post_update (must be 0 for first install)"
update_hooks=$(grep -R --include='*.install' -E 'function [a-z0-9_]+_update_[0-9]+' src/web/modules/custom src/web/themes/custom 2>/dev/null | wc -l)
post_updates=$(grep -R --include='*.post_update.php' -E 'function [a-z0-9_]+_post_update_' src/web/modules/custom 2>/dev/null | wc -l)
echo "  hook_update_N in *.install: $update_hooks"
echo "  post_update hooks:          $post_updates"
if [[ "$update_hooks" -gt 0 ]] || [[ "$post_updates" -gt 0 ]]; then
  warn "Update hooks remain — merge logic into hook_install() before release"
  grep -R --include='*.install' -E 'function [a-z0-9_]+_update_[0-9]+' src/web/modules/custom 2>/dev/null \
    | sed 's/^/    /' | head -20
  if [[ "$update_hooks" -gt 20 ]]; then
    echo "    … ($update_hooks total, see full list in DRE_RELEASE.md workflow)"
  fi
else
  ok "No update hooks"
fi

section "Module versions (info.yml — target version: 1.0.0)"
missing_version=0
wrong_version=0
while IFS= read -r info; do
  mod=$(basename "$(dirname "$info")")
  if ! grep -q '^version:' "$info"; then
    echo "  MISSING version: $mod ($info)"
    missing_version=$((missing_version + 1))
  elif ! grep -q '^version: 1\.0\.0' "$info"; then
    ver=$(grep '^version:' "$info")
    echo "  NOT 1.0.0: $mod — $ver"
    wrong_version=$((wrong_version + 1))
  fi
done < <(find src/web/modules/custom -name '*.info.yml' ! -path '*/tests/*')

if [[ "$missing_version" -eq 0 ]] && [[ "$wrong_version" -eq 0 ]]; then
  ok "All custom modules have version: 1.0.0"
else
  warn "$missing_version missing, $wrong_version not 1.0.0"
fi

section "Theme versions"
for theme_info in src/web/themes/custom/ps_theme/ps_theme.info.yml src/web/themes/custom/ui_suite_bnp/ui_suite_bnp.info.yml; do
  if [[ -f "$theme_info" ]]; then
    if grep -q '^version: 1\.0\.0' "$theme_info"; then
      ok "$(basename "$(dirname "$theme_info")") version 1.0.0"
    else
      warn "$theme_info — set version: 1.0.0"
    fi
  fi
done

section "Library versions (custom — target 1.0.0, not 1.x / VERSION)"
lib_issues=$(grep -R --include='*.libraries.yml' -E '^\s+version:' src/web/modules/custom src/web/themes/custom \
  | grep -v 'version: 1\.0\.0' | grep -v 'version: VERSION' || true)
lib_count=$(echo "$lib_issues" | grep -c 'version:' 2>/dev/null || echo 0)
echo "  Non-1.0.0 library entries: $lib_count"
if [[ "$lib_count" -gt 0 ]]; then
  warn "Normalize library versions to 1.0.0 for release (cache busting on future bumps)"
  echo "$lib_issues" | head -15 | sed 's/^/    /'
  if [[ "$lib_count" -gt 15 ]]; then
    echo "    … ($lib_count total)"
  fi
else
  ok "All custom library versions are 1.0.0"
fi

section "Large tracked assets (>500 KB)"
large=$(git ls-files -z | xargs -0 du -b 2>/dev/null | awk '$1>500000 {printf "%s %s KB\n", $2, $1/1024}' | sort -k2 -rn | head -10)
if [[ -n "$large" ]]; then
  echo "$large" | sed 's/^/  /'
  warn "Review large assets — keep only required project assets"
else
  ok "No large tracked files"
fi

section "Untracked files (sample)"
git status --short | head -20
extra=$(git status --short | wc -l)
if [[ "$extra" -gt 20 ]]; then
  echo "  … and $((extra - 20)) more lines"
fi

section "Structure note (dre.git legacy layout)"
echo "  Current repo:  composer root = src/, docroot = src/web/, docker/ at root"
echo "  Legacy dre:    composer root = ./, docroot = web/, no docker/"
echo "  Decide before push: replace dre layout entirely or migrate paths."

section "Summary"
if [[ "$issues" -eq 0 ]]; then
  echo "${GREEN}Ready for release prep — no blocking audit issues.${NC}"
  exit 0
else
  echo "${YELLOW}$issues audit issue(s) — see above. Fix before push.${NC}"
  echo "Full checklist: DRE_RELEASE.md"
  exit 1
fi
