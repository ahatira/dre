#!/bin/bash
# Script de correction automatique - Palette to Semantic Tokens (P2)
# Basé sur docs/DOCS_CONFORMITY_AUDIT.md
# Usage: ./scripts/docs-audit-fix-palette.sh

set -e

echo "🎨 Migration tokens palette → sémantiques dans docs/02-composants/"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Backup directory
BACKUP_DIR="backup/docs-audit-palette-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"

echo "📦 Backup créé: $BACKUP_DIR"
cp -r docs/02-composants "$BACKUP_DIR/"

# Counter
TOTAL_REPLACEMENTS=0

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔧 Phase 1: avatar.md (14+ tokens palette) - CRITIQUE"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

FILE="docs/02-composants/02-molecules/avatar.md"
if [ -f "$FILE" ]; then
  echo "Migration des tokens palette vers sémantiques..."
  
  # --ps-color-neutral-0 → --white
  COUNT=$(grep -c "ps-color-neutral-0" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/--ps-color-neutral-0/--white/g' "$FILE"
    echo -e "${GREEN}✓${NC} --ps-color-neutral-0 → --white ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # --ps-color-neutral-100 → --gray-100
  COUNT=$(grep -c "ps-color-neutral-100" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/--ps-color-neutral-100/--gray-100/g' "$FILE"
    echo -e "${GREEN}✓${NC} --ps-color-neutral-100 → --gray-100 ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # --ps-color-neutral-400 → --gray-400
  COUNT=$(grep -c "ps-color-neutral-400" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/--ps-color-neutral-400/--gray-400/g' "$FILE"
    echo -e "${GREEN}✓${NC} --ps-color-neutral-400 → --gray-400 ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # --ps-color-neutral-600 → --gray-600
  COUNT=$(grep -c "ps-color-neutral-600" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/--ps-color-neutral-600/--gray-600/g' "$FILE"
    echo -e "${GREEN}✓${NC} --ps-color-neutral-600 → --gray-600 ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # --ps-color-neutral-900 → --gray-900
  COUNT=$(grep -c "ps-color-neutral-900" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/--ps-color-neutral-900/--gray-900/g' "$FILE"
    echo -e "${GREEN}✓${NC} --ps-color-neutral-900 → --gray-900 ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # --ps-color-primary-600 → --primary
  COUNT=$(grep -c "ps-color-primary-600" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/--ps-color-primary-600/--primary/g' "$FILE"
    echo -e "${GREEN}✓${NC} --ps-color-primary-600 → --primary ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # --ps-color-success-600 → --success
  COUNT=$(grep -c "ps-color-success-600" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/--ps-color-success-600/--success/g' "$FILE"
    echo -e "${GREEN}✓${NC} --ps-color-success-600 → --success ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # --ps-color-error-600 → --danger
  COUNT=$(grep -c "ps-color-error-600" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/--ps-color-error-600/--danger/g' "$FILE"
    echo -e "${GREEN}✓${NC} --ps-color-error-600 → --danger ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # --ps-border-width-default → --border-size-2
  COUNT=$(grep -c "ps-border-width-default" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/--ps-border-width-default/--border-size-2/g' "$FILE"
    echo -e "${GREEN}✓${NC} --ps-border-width-default → --border-size-2 ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # --ps-border-width-focus → --border-size-2
  COUNT=$(grep -c "ps-border-width-focus" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/--ps-border-width-focus/--border-size-2/g' "$FILE"
    echo -e "${GREEN}✓${NC} --ps-border-width-focus → --border-size-2 ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # --ps-color-interactive-focus-outline → --primary (for focus rings)
  COUNT=$(grep -c "ps-color-interactive-focus-outline" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/--ps-color-interactive-focus-outline/--primary/g' "$FILE"
    echo -e "${GREEN}✓${NC} --ps-color-interactive-focus-outline → --primary ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
else
  echo -e "${RED}✗${NC} Fichier non trouvé: $FILE"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔧 Phase 2: card.md (1 token custom)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

FILE="docs/02-composants/02-molecules/card.md"
if [ -f "$FILE" ]; then
  # --ps-color-border-card → --border-default
  COUNT=$(grep -c "ps-color-border-card" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/--ps-color-border-card/--border-default/g' "$FILE"
    echo -e "${GREEN}✓${NC} --ps-color-border-card → --border-default ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
else
  echo -e "${RED}✗${NC} Fichier non trouvé: $FILE"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔧 Phase 3: Scan global autres tokens palette"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Search for remaining palette tokens across all files
PALETTE_PATTERNS=(
  "--ps-color-neutral-[0-9]+"
  "--ps-color-primary-[0-9]+"
  "--ps-color-secondary-[0-9]+"
  "--ps-color-success-[0-9]+"
  "--ps-color-error-[0-9]+"
  "--ps-color-warning-[0-9]+"
  "--ps-color-info-[0-9]+"
  "--ps-border-width-"
)

echo "Recherche de tokens palette résiduels..."
for pattern in "${PALETTE_PATTERNS[@]}"; do
  FILES_WITH_PATTERN=$(grep -rl "$pattern" docs/02-composants --include="*.md" || true)
  
  if [ -n "$FILES_WITH_PATTERN" ]; then
    echo -e "${YELLOW}⚠${NC}  Pattern détecté: $pattern"
    echo "$FILES_WITH_PATTERN" | while read -r file; do
      COUNT=$(grep -c "$pattern" "$file" || true)
      echo "   → $file ($COUNT occurrences)"
      echo "   → Vérification manuelle requise (contexte spécifique)"
    done
  fi
done

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Résumé"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo -e "${GREEN}Corrections appliquées:${NC} $TOTAL_REPLACEMENTS remplacements"
echo -e "${YELLOW}Backup disponible:${NC} $BACKUP_DIR"
echo ""
echo "📋 Table de conversion (référence):"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Palette (old)              → Semantic (new)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "--ps-color-neutral-0       → --white"
echo "--ps-color-neutral-100     → --gray-100"
echo "--ps-color-neutral-200     → --gray-200"
echo "--ps-color-neutral-400     → --gray-400"
echo "--ps-color-neutral-600     → --gray-600"
echo "--ps-color-neutral-900     → --gray-900"
echo "--ps-color-primary-600     → --primary"
echo "--ps-color-secondary-600   → --secondary"
echo "--ps-color-success-600     → --success"
echo "--ps-color-error-600       → --danger"
echo "--ps-color-warning-600     → --warning"
echo "--ps-color-info-600        → --info"
echo "--ps-border-width-default  → --border-size-2"
echo "--ps-border-width-focus    → --border-size-2"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📋 Prochaines étapes:"
echo "  1. Vérifier les changements: git diff docs/02-composants/"
echo "  2. Valider avatar.md (14+ remplacements)"
echo "  3. Vérifier tokens résiduels (warnings ci-dessus)"
echo "  4. Tester Storybook: npm run watch"
echo "  5. Commiter: git add docs/ && git commit -m 'docs: Migrate palette to semantic tokens (P2)'"
echo ""
