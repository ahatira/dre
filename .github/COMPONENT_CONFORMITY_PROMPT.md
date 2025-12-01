# Component Conformity Audit & Fix Prompt

**Usage**: Copy-paste ce prompt pour auditer et corriger UN composant spécifique du projet PS Theme.

---

## 🎯 Prompt Générique

```
Audite et corrige le composant [NOM_COMPOSANT] ([TYPE: atom/molecule/organism]) selon les règles strictes du projet.

CONTEXTE:
- Projet: PS Theme (Drupal 10/11, BNP Paribas Real Estate)
- Standards: COMPLETE_RULES.md (référence absolue 1000+ lignes)
- Zero Tolerance: 0 hardcoded values (colors, px, rgba, hex)
- Score cible: 100% conformité

STRUCTURE REQUISE (5 fichiers obligatoires):
1. [component].twig - Template avec params commentés
2. [component].css - Styles BEM + nesting + tokens uniquement
3. [component].yml - Données par défaut
4. [component].stories.jsx - Stories Storybook (Autodocs)
5. README.md - 8 sections (Props, BEM, Variants, Tokens, Accessibility, Usage, Examples, References)

CRITÈRES D'AUDIT (score 0-100%):

1. **Structure** (20 points):
   - [ ] 5 fichiers présents
   - [ ] Nomenclature correcte (kebab-case)
   - [ ] Emplacement Atomic Design correct

2. **BEM Strict** (15 points):
   - [ ] Préfixe `ps-` obligatoire
   - [ ] Format: `.ps-block__element--modifier`
   - [ ] Pas de double underscore
   - [ ] Modifiers indépendants (pas de combinaisons requises)

3. **Design Tokens** (30 points - CRITIQUE):
   - [ ] 0 hardcoded colors (#hex, rgb, rgba)
   - [ ] 0 hardcoded px (sauf exceptions WCAG documentées)
   - [ ] 0 hardcoded line-height/letter-spacing
   - [ ] Tous tokens via `var(--token-name)`
   - [ ] Vérifier existence tokens avant création

4. **CSS Nesting** (10 points):
   - [ ] Utilise `&` pour tous les éléments/modifiers
   - [ ] Ordre: Base → Elements → Modifiers → States
   - [ ] Max 3 niveaux de profondeur

5. **Composition Atomic Design** (10 points):
   - [ ] Atoms: pas de composition (autonomes)
   - [ ] Molecules: inclusions atoms via `@elements/`
   - [ ] Organisms: inclusions molecules via `@components/`
   - [ ] Pas de duplication code atoms/molecules

6. **Documentation** (10 points):
   - [ ] README.md 8 sections complètes (EN)
   - [ ] Props table exhaustive
   - [ ] BEM architecture documentée
   - [ ] Tokens utilisés listés
   - [ ] Exemples usage Twig

7. **Accessibilité** (5 points):
   - [ ] WCAG 2.2 AA minimum
   - [ ] focus-visible states
   - [ ] ARIA attributes si nécessaire
   - [ ] Keyboard navigation
   - [ ] Exceptions documentées (visually-hidden)

PROCESSUS:

1. **LECTURE COMPLETE**:
   ```
   - Lire les 5 fichiers du composant
   - Identifier TOUTES les violations
   - Calculer score initial (0-100%)
   ```

2. **DÉTECTION VIOLATIONS**:
   ```bash
   # Regex search hardcoded values
   grep -rn "#[0-9a-fA-F]{3,6}" [component].css    # Hex colors
   grep -rn "rgba\(" [component].css                # RGBA values
   grep -rn "[0-9]+px" [component].css              # Hardcoded px
   ```

3. **CRÉATION TOKENS** (si nécessaire):
   ```css
   /* Vérifier d'abord existence dans source/props/*.css */
   /* Ajouter UNIQUEMENT si absent */
   
   /* colors.css - semantic names */
   --ps-component-color-primary: hsl(162, 72%, 38%);
   
   /* sizes.css - use existing --size-* scale */
   /* shadows.css - use existing --shadow-* scale */
   ```

4. **CORRECTIONS** (multi_replace_string_in_file):
   ```css
   /* ❌ AVANT */
   color: #00915A;
   padding: 16px 24px;
   box-shadow: 0 2px 4px rgba(0,0,0,0.2);
   
   /* ✅ APRÈS */
   color: var(--brand-primary);
   padding: var(--size-4) var(--size-6);
   box-shadow: var(--shadow-2);
   ```

5. **NESTING** (si CSS flat):
   ```css
   /* ❌ AVANT */
   .ps-component { }
   .ps-component__element { }
   .ps-component--modifier { }
   
   /* ✅ APRÈS */
   .ps-component {
     /* Base */
     
     &__element {
       /* Element styles */
     }
     
     &--modifier {
       /* Modifier styles */
     }
   }
   ```

6. **BUILD & VALIDATION**:
   ```bash
   npm run build  # Doit passer (0 errors)
   ```

7. **COMMIT STRUCTURÉ**:
   ```
   fix([component]): replace hardcoded values with tokens (XX% → 100%)
   
   [Component] Component (XX% → 100%):
   - Create N tokens in [file].css
   - Replace X× hardcoded colors with semantic tokens
   - Replace Y× hardcoded px with design tokens
   - Add/improve CSS nesting with & syntax
   - [other fixes]
   - Total: Z violations eliminated ✅
   
   Design Tokens Created:
   - [file].css: [list of tokens]
   
   Build Status: ✅ Compiled successfully (Xs, 0 errors)
   
   Conformity Score: XX% → 100% ✅
   
   References:
   - COMPLETE_RULES.md Section 4 (Design Tokens)
   - [relevant documentation]
   ```

RAPPORT AUDIT (format Markdown):

```markdown
## Audit [Component Name] - [Type]

**Date**: YYYY-MM-DD
**Score Initial**: XX%
**Score Final**: YY%
**Statut**: [PARFAIT|EXCELLENT|CORRECTIONS REQUISES|CRITIQUE]

### Structure (X/20)
- [✅/❌] 5 fichiers présents
- [détails]

### BEM Strict (X/15)
- [✅/❌] Préfixe ps-
- [détails violations]

### Design Tokens (X/30)
**Violations détectées**:
- Ligne XX: `color: #00915A` ❌ → `var(--brand-primary)` ✅
- Ligne YY: `padding: 16px` ❌ → `var(--size-4)` ✅
[liste complète]

### CSS Nesting (X/10)
[état actuel]

### Composition (X/10)
[analyse atomic design]

### Documentation (X/10)
[sections manquantes]

### Accessibilité (X/5)
[conformité WCAG]

---

### Plan de Corrections
1. [Action prioritaire 1] - Xmin
2. [Action prioritaire 2] - Ymin
Total: Zmin

### Score Final Attendu: 100%
```

RÈGLES STRICTES:

✅ **FAIRE**:
- Lire `.github/COMPLETE_RULES.md` AVANT de commencer
- Vérifier tokens existants avec `grep -r` dans `source/props/`
- Utiliser `multi_replace_string_in_file` pour corrections parallèles
- Build après CHAQUE modification
- Documenter exceptions WCAG avec références W3C

❌ **NE JAMAIS**:
- Hardcoder colors (#hex, rgb, rgba)
- Hardcoder sizes (px, em, rem sans tokens)
- Créer tokens dupliqués (vérifier d'abord)
- Utiliser noms non-sémantiques (green → primary)
- Mixer anglais/français dans code (EN uniquement)
- Ajouter comments Figma dans CSS (specs → documentation)
- Créer modifiers combinés (`.ps-a.ps-b` interdit)

EXEMPLES RÉFÉRENCES:

**Atom Parfait**: `source/patterns/elements/button/`
**Molecule Parfaite**: `source/patterns/components/form-field/`

---

LANCER AUDIT:

Remplacer [NOM_COMPOSANT] et [TYPE] puis exécuter:
"Audite et corrige le composant [NOM_COMPOSANT] ([TYPE]) selon ce prompt."
```

---

## 📋 Checklist Rapide

Avant de marquer un composant comme conforme (100%), vérifier:

- [ ] `grep -r "#[0-9a-fA-F]" [component].css` → 0 résultats
- [ ] `grep -r "rgba\(" [component].css` → 0 résultats (ou tokens)
- [ ] `grep -r "[0-9]+px" [component].css` → 0 non-justifiés
- [ ] Tous les `&` nesting présents
- [ ] README.md 8 sections complètes
- [ ] `npm run build` → 0 errors
- [ ] Storybook render sans erreurs
- [ ] Git commit structuré

---

## 🎯 Template Réponse Audit

```markdown
**Composant**: [Name] ([Type])
**Score**: XX% → 100% ✅

**Violations trouvées**: X
**Corrections appliquées**: X
**Tokens créés**: X
**Build**: ✅ Success (Xs)
**Commit**: [hash]

**Détails**: [summary 2-3 lignes]
```

---

**Version**: 1.0.0  
**Date**: 2025-12-01  
**Projet**: PS Theme - BNP Paribas Real Estate  
**Référence Absolue**: `.github/COMPLETE_RULES.md`
