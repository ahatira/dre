#!/bin/bash
# Script de correction automatique - Neutral Variant (P2)
# Basé sur docs/DOCS_CONFORMITY_AUDIT.md
# Usage: ./scripts/docs-audit-fix-neutral.sh

set -e

echo "🎯 Correction des variants neutral explicites dans docs/02-composants/"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Backup directory
BACKUP_DIR="backup/docs-audit-neutral-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"

echo "📦 Backup créé: $BACKUP_DIR"
cp -r docs/02-composants "$BACKUP_DIR/"

# Counter
TOTAL_REPLACEMENTS=0

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔧 Phase 1: button.md (1 classe --neutral)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

FILE="docs/02-composants/01-atomes/button.md"
if [ -f "$FILE" ]; then
  COUNT=$(grep -c "ps-button--neutral" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    # Remove --neutral class from examples
    sed -i 's/ps-button ps-button--neutral/ps-button/g' "$FILE"
    
    # Update variant documentation
    sed -i 's/ps-button--neutral">Neutral/ps-button">Neutral (default)/g' "$FILE"
    
    echo -e "${GREEN}✓${NC} Retiré ps-button--neutral ($COUNT occurrences)"
    echo "   → Neutral = omission de variant (état par défaut)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
else
  echo -e "${RED}✗${NC} Fichier non trouvé: $FILE"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔧 Phase 2: table.md (2 classes ps-badge--neutral)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

FILE="docs/02-composants/02-molecules/table.md"
if [ -f "$FILE" ]; then
  COUNT=$(grep -c "ps-badge--neutral" "$FILE" || true)
  if [ "$COUNT" -gt 0 ]; then
    # Remove --neutral class from HTML examples
    sed -i 's/ps-badge ps-badge--neutral/ps-badge/g' "$FILE"
    
    # Remove from YAML/Twig strings
    sed -i "s/'ps-badge ps-badge--neutral'/'ps-badge'/g" "$FILE"
    
    echo -e "${GREEN}✓${NC} Retiré ps-badge--neutral ($COUNT occurrences)"
    echo "   → Badges avec état 'Inactif' utilisent style par défaut"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  fi
else
  echo -e "${RED}✗${NC} Fichier non trouvé: $FILE"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔧 Phase 3: Autres fichiers potentiels"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Search for any remaining --neutral variants in other files
FILES_WITH_NEUTRAL=$(grep -rl "ps-[a-z]*--neutral" docs/02-composants --include="*.md" | grep -v "button.md\|table.md" || true)

if [ -n "$FILES_WITH_NEUTRAL" ]; then
  echo -e "${YELLOW}⚠${NC}  Fichiers supplémentaires avec --neutral détectés:"
  echo "$FILES_WITH_NEUTRAL" | while read -r file; do
    echo "   → $file"
    
    # Count occurrences
    COUNT=$(grep -c "ps-[a-z]*--neutral" "$file" || true)
    
    # Replace (generic pattern)
    sed -i 's/\(ps-[a-z]*\) ps-\([a-z]*\)--neutral/\1/g' "$file"
    
    echo -e "   ${GREEN}✓${NC} Corrigé ($COUNT occurrences)"
    TOTAL_REPLACEMENTS=$((TOTAL_REPLACEMENTS + COUNT))
  done
else
  echo -e "${GREEN}✓${NC} Aucun autre fichier avec --neutral détecté"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔧 Phase 4: Mise à jour documentation neutral"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Add clarification in variant sections
FILES_WITH_VARIANTS=$(grep -rl "## Variants\|### Variants" docs/02-composants --include="*.md" || true)

if [ -n "$FILES_WITH_VARIANTS" ]; then
  echo "Fichiers avec section Variants:"
  echo "$FILES_WITH_VARIANTS" | while read -r file; do
    # Check if file already has neutral clarification
    if ! grep -q "neutral (default - omission)" "$file"; then
      echo -e "${YELLOW}⚠${NC}  $file - Vérification manuelle requise"
      echo "   → Ajouter note: 'Neutral obtenu par omission de variant'"
    fi
  done
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Résumé"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo -e "${GREEN}Corrections appliquées:${NC} $TOTAL_REPLACEMENTS remplacements"
echo -e "${YELLOW}Backup disponible:${NC} $BACKUP_DIR"
echo ""
echo "📋 Règle: Variant neutral = OMISSION de classe"
echo "   ✅ <button class=\"ps-button\">Neutral</button>"
echo "   ❌ <button class=\"ps-button ps-button--neutral\">Neutral</button>"
echo ""
echo "📋 Prochaines étapes:"
echo "  1. Vérifier les changements: git diff docs/02-composants/"
echo "  2. Valider sections 'Variants' (notes explicatives)"
echo "  3. Tester Storybook: npm run watch"
echo "  4. Commiter: git add docs/ && git commit -m 'docs: Fix neutral variant handling (P2)'"
echo ""
