#!/bin/bash
# Script de correction automatique - Hardcoded Colors (P1)
# Basé sur docs/DOCS_CONFORMITY_AUDIT.md
# Usage: ./scripts/docs-audit-fix-colors.sh

set -e

echo "🎨 Correction des couleurs hardcodées dans docs/02-composants/"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Backup directory
BACKUP_DIR="backup/docs-audit-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"

echo "📦 Backup créé: $BACKUP_DIR"
cp -r docs/02-composants "$BACKUP_DIR/"

# Counter
TOTAL_REPLACEMENTS=0

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔧 Phase 1: button.md (2 hex codes)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

FILE="docs/02-composants/01-atomes/button.md"
if [ -f "$FILE" ]; then
  # Remove hex codes from variant color descriptions (NOT visual specs)
  # Keep: "Aperçu visuel" sections, "UI spec" sections
  # Remove: In CSS code blocks and variant description lines
  
  # Primary color
  COUNT=$(grep -c "#00915A" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/\(Couleurs.*--primary.*\) (vert brand #00915A)/\1/g' "$FILE"
    sed -i 's/`--primary` (#00915A)/`--primary`/g' "$FILE"
    echo -e "${GREEN}✓${NC} Retiré #00915A ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # Secondary color
  COUNT=$(grep -c "#A12B66" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/\(Couleurs.*--secondary.*\) (violet brand #A12B66)/\1/g' "$FILE"
    sed -i 's/`--secondary` (#A12B66)/`--secondary`/g' "$FILE"
    echo -e "${GREEN}✓${NC} Retiré #A12B66 ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
else
  echo -e "${RED}✗${NC} Fichier non trouvé: $FILE"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔧 Phase 2: icon.md (4 hex codes)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

FILE="docs/02-composants/01-atomes/icon.md"
if [ -f "$FILE" ]; then
  # Keep hex codes in "UI spec" visual descriptions
  # Remove from CSS code blocks ONLY
  
  # Note: icon.md uses hex codes in visual spec section - these are OK
  # Only remove if found in CSS code blocks (between ```css and ```)
  
  # We'll use perl for multi-line regex (detect CSS blocks)
  # For now, skip if codes are in visual descriptions
  
  echo -e "${YELLOW}⚠${NC}  icon.md: Codes hex dans section UI spec (OK, visuel)"
  echo "   → Vérification manuelle requise pour séparer code/visuel"
else
  echo -e "${RED}✗${NC} Fichier non trouvé: $FILE"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔧 Phase 3: link.md (1 hex code)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

FILE="docs/02-composants/01-atomes/link.md"
if [ -f "$FILE" ]; then
  COUNT=$(grep -c "#8E2A68" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/#8E2A68/var(--secondary-active)/g' "$FILE"
    echo -e "${GREEN}✓${NC} Remplacé #8E2A68 → var(--secondary-active) ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
else
  echo -e "${RED}✗${NC} Fichier non trouvé: $FILE"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔧 Phase 4: avatar.md (10+ hex codes) - CRITIQUE"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

FILE="docs/02-composants/02-molecules/avatar.md"
if [ -f "$FILE" ]; then
  # Multiple replacements for CSS fallback values
  
  # White
  COUNT=$(grep -c ", #FFF)" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/var(--ps-color-neutral-0, #FFF)/var(--white)/g' "$FILE"
    echo -e "${GREEN}✓${NC} Remplacé #FFF → var(--white) ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # Gray-600
  COUNT=$(grep -c "#54636F" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/var(--ps-color-neutral-600, #54636F)/var(--gray-600)/g' "$FILE"
    echo -e "${GREEN}✓${NC} Remplacé #54636F → var(--gray-600) ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # Success (teal)
  COUNT=$(grep -c "#0DB089" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/var(--ps-color-success-600, #0DB089)/var(--success)/g' "$FILE"
    echo -e "${GREEN}✓${NC} Remplacé #0DB089 → var(--success) ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # Gray-400
  COUNT=$(grep -c "#9AA6B2" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/var(--ps-color-neutral-400, #9AA6B2)/var(--gray-400)/g' "$FILE"
    echo -e "${GREEN}✓${NC} Remplacé #9AA6B2 → var(--gray-400) ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # Danger (red)
  COUNT=$(grep -c "#E53935" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/var(--ps-color-error-600, #E53935)/var(--danger)/g' "$FILE"
    echo -e "${GREEN}✓${NC} Remplacé #E53935 → var(--danger) ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # Gray-100
  COUNT=$(grep -c "#F3F6F9" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/var(--ps-color-neutral-100, #F3F6F9)/var(--gray-100)/g' "$FILE"
    echo -e "${GREEN}✓${NC} Remplacé #F3F6F9 → var(--gray-100) ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # Focus ring (blue)
  COUNT=$(grep -c "#0B5FFF" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/var(--ps-color-interactive-focus-outline, #0B5FFF)/var(--primary)/g' "$FILE"
    echo -e "${GREEN}✓${NC} Remplacé #0B5FFF → var(--primary) ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
else
  echo -e "${RED}✗${NC} Fichier non trouvé: $FILE"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔧 Phase 5: card.md (1 hex code)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

FILE="docs/02-composants/02-molecules/card.md"
if [ -f "$FILE" ]; then
  COUNT=$(grep -c "#EBEDEF" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/#EBEDEF/var(--border-default)/g' "$FILE"
    sed -i 's/`1.5px solid var(--border-default)`/`var(--border-size-15) solid var(--border-default)`/g' "$FILE"
    echo -e "${GREEN}✓${NC} Remplacé #EBEDEF → var(--border-default) ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
else
  echo -e "${RED}✗${NC} Fichier non trouvé: $FILE"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔧 Phase 6: carousel.md (4 hex codes)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

FILE="docs/02-composants/02-molecules/carousel.md"
if [ -f "$FILE" ]; then
  # Gray-50
  COUNT=$(grep -c "#F9F9FB" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/#F9F9FB/var(--gray-50)/g' "$FILE"
    echo -e "${GREEN}✓${NC} Remplacé #F9F9FB → var(--gray-50) ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # Primary
  COUNT=$(grep -c "#00915A" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/#00915A/var(--primary)/g' "$FILE"
    echo -e "${GREEN}✓${NC} Remplacé #00915A → var(--primary) ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # Secondary (typo: A22B66 vs A12B66)
  COUNT=$(grep -c "#A22B66" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/#A22B66/var(--secondary)/g' "$FILE"
    echo -e "${GREEN}✓${NC} Remplacé #A22B66 → var(--secondary) ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
  
  # White
  COUNT=$(grep -c "#FFFFFF" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    sed -i 's/#FFFFFF/var(--white)/g' "$FILE"
    echo -e "${GREEN}✓${NC} Remplacé #FFFFFF → var(--white) ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
else
  echo -e "${RED}✗${NC} Fichier non trouvé: $FILE"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Résumé"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo -e "${GREEN}Corrections appliquées:${NC} $TOTAL_REPLACEMENTS remplacements"
echo -e "${YELLOW}Backup disponible:${NC} $BACKUP_DIR"
echo ""
echo "📋 Prochaines étapes:"
echo "  1. Vérifier les changements: git diff docs/02-composants/"
echo "  2. Valider manuellement icon.md (codes hex dans section visuelle)"
echo "  3. Tester Storybook: npm run watch"
echo "  4. Commiter: git add docs/ && git commit -m 'docs: Fix hardcoded colors (P1)'"
echo ""
