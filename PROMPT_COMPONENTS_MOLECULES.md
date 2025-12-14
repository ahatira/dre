# 🔍 Prompt : Vérification/Création Components (Molecules)

**À copier dans une nouvelle session de chat pour vérifier/créer systematiquement tous les composants Molecules.**

---

## 📋 Instructions Complètes

Vérifie et corrige systematiquement TOUS les composants de la liste Components (Molecules) dans `docs/atomic/components.md`.

### WORKFLOW OBLIGATOIRE pour CHAQUE composant

#### 1. **Vérification existence**
- Exécuter `file_search` pour `source/patterns/components/{nom}/*`
- Si absent → Créer 4 fichiers complets (twig, css, yml, stories)
- Si présent → Vérifier conformité totale avec tous les standards

#### 2. **Standards CRITIQUES à appliquer**

**📖 LECTURE OBLIGATOIRE** : `.github/instructions/` (01-05) + `copilot-instructions.md`

##### **Architecture Token-First (02-component-development.md)**
- ✅ Token-First Workflow : Consommer atoms SANS modifier leur CSS
- ✅ Override tokens via `--ps-{atom}--{prop}` dans consumer CSS
- ✅ Composition : `{% include '@elements/{atom}' with {...} only %}`
- ✅ Utiliser `attributes.addClass()` pour classes additionnelles
- ❌ JAMAIS `baseClass` parameter (FORBIDDEN)
- ❌ JAMAIS modifier CSS des atoms parents directement
- ❌ JAMAIS utility class overuse (structure = CSS, variants = utilities)

##### **CSS Standards (03-technical-implementation.md section 1)**
- ✅ 3-layer architecture (root tokens → component vars → modifiers)
- ✅ Nesting avec `&` (BEM) MANDATORY pour nouveaux composants
- ✅ Tokens exclusivement (ZERO hardcoded values : `#00915A`, `16px`, `150ms ease`)
- ✅ Focus-visible sur tous interactifs (`:focus-visible` avec outline)
- ✅ 3-size system : `small | medium | large` (si applicable)
- ✅ 9 semantic colors : `neutral, primary, secondary, gold, info, warning, success, danger, light, dark` (si styled)
- ✅ Semantic color tokens : `var(--primary)` NOT `var(--green-600)`
- ❌ JAMAIS flat CSS sans nesting (nouveaux composants)
- ❌ JAMAIS color names : `green` → `success`, `red` → `danger`

##### **Twig Standards (03-technical-implementation.md section 2)**
- ✅ Header normalisé : `@param {type} [prop=default] - description`
- ✅ Whitespace control strict : `{%- -%}`, `{{- -}}`
- ✅ `attributes|without('class')` MANDATORY
- ✅ Defaults : `{%- set prop = prop|default('value') -%}`
- ✅ Ternary + null : `condition ? 'class' : null`
- ✅ Composition atoms : `{% include '@elements/{atom}' with {...} only %}`
- ✅ Legacy size mapping : `xs/sm → small, md → medium, lg/xl/xxl → large`
- ❌ JAMAIS arrow functions : `.filter(v => v)` → ternary
- ❌ JAMAIS méthodes JS : `.map()`, `.includes()`, `.filter()` → Drupal incompatible

##### **Storybook Standards (03-technical-implementation.md section 3)**
- ✅ `tags: ['autodocs']` MANDATORY (sauf base/* stories)
- ✅ `args: data` direct reference (PAS `{ ...data }` spread operator)
- ✅ `Object.assign({}, data, { prop })` pour overrides
- ✅ argTypes categorized : Content, Appearance, Behavior, Layout, Accessibility
- ✅ Default story + Showcases (patterns réels, NOT generic contexts)
- ✅ Icon registry : `iconsRegistry.names` (array) NOT `.map()` on object
- ❌ JAMAIS context stories : UseCases, EnContexte, InContext, RealEstateContext
- ❌ JAMAIS spread operator : `{ ...data, prop }` → Object.assign

##### **Accessibility (03-technical-implementation.md section 5)**
- ✅ WCAG 2.2 AA minimum
- ✅ Contrast ratios : 4.5:1 text, 3:1 UI components
- ✅ Focus-visible sur tous interactifs (outline + offset)
- ✅ ARIA approprié : role, aria-label, aria-live, aria-expanded, etc.
- ✅ Keyboard navigation complète (Tab, Enter, Escape, arrows)
- ✅ Screen reader testing mental model

##### **Icon System (copilot-instructions.md)**
- ✅ data-icon attribute : `<span data-icon="check" aria-hidden="true"></span>`
- ✅ Icon names WITHOUT prefix : `check` NOT `icon-check`
- ❌ JAMAIS `{% include '@elements/icon' %}` (use data-icon instead)

#### 3. **Validation avant commit**

```bash
npm run build  # DOIT passer (0 errors)
```

- ✅ Conformity audit 100% (04-quality-assurance.md checklist)
- ✅ Vérifier dark mode (light/dark variants sur fonds appropriés)
- ✅ Test tailles (3-size cohérence si applicable)
- ✅ Test keyboard navigation
- ✅ Test focus-visible sur tous interactifs

#### 4. **Commit structuré**

**Format** :
```
type(components): Subject line (max 72 chars)

- Twig: Points détaillés (header, whitespace, composition, defaults)
- CSS: Points détaillés (tokens, nesting, modifiers, focus-visible)
- YML: Defaults documentation
- Stories: argTypes, showcases, no context stories
- Pattern: Token-First composition / 3-size / 9-colors (si applicable)
- References spec: docs/design/... (si existe)
- Progress: X/24 → Y/24 (Z%)
```

**Types** : `feat` (nouveau), `fix` (correction), `refactor` (restructure)

#### 5. **Mise à jour documentation**

- ✅ `docs/atomic/components.md` : Status ❌ → ✅
- ✅ Progress counter : `X/24` → `(X+1)/24` avec pourcentage
- ✅ Commit dédié si batch update

---

## 🎯 ORDRE DE TRAITEMENT (Priorité specs existantes)

### Phase 1 : Vérification Existants (4 composants)
1. **Card** (✅) - Vérifier conformité totale
2. **Breadcrumb** (✅) - Vérifier conformité totale
3. **Alert** (✅) - Vérifier conformité totale
4. **Accordion Item** (✅) - Vérifier conformité totale

### Phase 2 : Specs Documentées (8 composants)
5. **Dropdown** - Spec: `design/pages/search-results/sort-dropdown.md`
6. **Consultant Card** - Spec: `design/pages/property-detail/consultant-card.md`
7. **Gallery Modal** - Spec: `design/pages/property-detail/gallery-modal.md`
8. **Specs List** - Spec: `design/pages/property-detail/specs-sections.md`
9. **Surface Table Row** - Spec: `design/pages/property-detail/surface-table.md`
10. **Map Widget** - Spec: `design/pages/property-detail/location.md`
11. **POI Filter Group** - Spec: `design/pages/property-detail/poi-filters.md`
12. **Travel Time Calculator** - Spec: `design/pages/property-detail/travel-time.md`

### Phase 3 : Composants Génériques (12 composants)

**Forms (4)** :
13. **Form Field** - Input/textarea + label + validation
14. **Checkboxes** - Groupe checkboxes liés
15. **Radios** - Groupe radios liés
16. **Search Bar** - Input + icône + submit

**Navigation (3)** :
17. **Menu Item** - Lien nav avec icône/badge/sous-menu
18. **Pagination** - Navigation pages avec numéros
19. **Language Selector** - Sélecteur langue/pays
20. **Tab** - Bouton onglet navigation tabs

**Feedback (2)** :
21. **Toast** - Notification temporaire auto-dismiss
22. **Tooltip** - Aide contextuelle hover/focus
23. **Modal** - Dialog overlay

**Lists (2)** :
24. **List Item** - Item liste avec icône/texte/actions
25. **Table Row** - Ligne données tableau

**Media (2)** :
26. **Video** - Lecteur vidéo contrôles
27. **Carousel Item** - Slide carrousel

**Interactive (1)** :
28. **Stepper** - Indicateur étapes avec statut

**Other (1)** :
29. **Media Object** - Image/icône + titre + description
30. **Tag List** - Collection tags/chips

**Note** : Contact Form peut être un organism (composition Form Fields)

---

## ⚠️ ZERO TOLERANCE (Rejet automatique)

Ces violations entraînent un rejet systématique :

### Code
- ❌ Hardcoded values : `#00915A`, `16px`, `150ms ease` → Use tokens
- ❌ Missing `attributes` parameter in Twig
- ❌ Missing `attributes|without('class')`
- ❌ Missing `tags: ['autodocs']` in stories
- ❌ Arrow functions in Twig : `.filter(v => v)`
- ❌ JavaScript methods in Twig : `.map()`, `.includes()`
- ❌ Spread operator in stories : `{ ...data, prop }`
- ❌ Flat CSS without nesting (nouveaux composants)
- ❌ Missing focus-visible on interactives
- ❌ Color names instead of semantic : `green` → `success`
- ❌ Icon prefix in code : `icon-check` → `check`
- ❌ Wrong cascade order : Modifiers before base

### Architecture
- ❌ Context stories : UseCases, EnContexte, InContext, RealEstateContext
- ❌ Modifier classes requiring combinations : `.class-a.class-b`
- ❌ Modifying parent component CSS directly (use Token-First override)
- ❌ `baseClass` parameter for composition (use `attributes.addClass()`)
- ❌ Utility class overuse for structure (use semantic component CSS)
- ❌ Editing `source/props/*.css` directly (propose tokens separately)

### Files
- ❌ Missing any of 4 required files : `.twig`, `.css`, `.yml`, `.stories.jsx`
- ❌ README.md files (generator artifact, delete manually)

---

## 📚 Reference Components (Perfect implementations)

Étudier ces composants pour patterns :

**Elements (Atoms)** :
- **Button** - Complete 9-variant styled component, all states, focus-visible
- **Badge** - Simplified structure (no wrapper), pill modifier, icon integration
- **Avatar** - Minimal markup, adaptive sizing, SVG fallback
- **Divider** - Simplicity, orientation variants, optional icon/content
- **Skeleton** - 6 shape variants, 3 animations, ARIA loading states
- **Spinner** - 3-size system, 9 colors, dark mode guidance
- **Logo** - Asset-based, 2 variants, conditional wrapper
- **Progress Bar** - Linear/circular, determinate/indeterminate, striped

**Components (Molecules)** :
- **Card** - Token-First composition, override child tokens
- **Breadcrumb** - Navigation pattern, separator handling
- **Alert** - Feedback pattern, icon integration, dismissible
- **Accordion Item** - Interactive pattern, keyboard navigation

---

## 🚀 COMMENCER PAR

```
Vérification systematique Components (Molecules) - 24 composants dans docs/atomic/components.md.

PHASE 1 : Je commence par vérifier les 4 composants déjà implémentés (Card, Breadcrumb, Alert, Accordion Item) pour conformité totale avec tous les standards...
```

---

## 📋 Checklist Rapide (Par Composant)

**Avant commit** :
- [ ] 4 fichiers créés/vérifiés (twig, css, yml, stories)
- [ ] Twig : Header normalisé, whitespace control, attributes|without('class')
- [ ] CSS : 3-layer tokens, nesting, focus-visible, NO hardcoded values
- [ ] Stories : tags: ['autodocs'], args: data (NO spread), NO context stories
- [ ] Build passes : `npm run build` → 0 errors
- [ ] Dark mode tested (if light/dark variants)
- [ ] Sizes tested (if 3-size system)
- [ ] Keyboard navigation tested (if interactive)
- [ ] Commit structured + docs updated

**Token-First specific** :
- [ ] Atoms composed via `{% include %}` only
- [ ] Child tokens overridden in consumer CSS (`--ps-button--bg: ...`)
- [ ] NO direct modification of atom CSS
- [ ] NO baseClass parameter

---

**Maintainers** : Design System Team  
**Version** : 1.0.0  
**Date** : 2025-12-14
