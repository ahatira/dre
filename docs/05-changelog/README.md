# Changelog - PS Theme

**Historique complet** des modifications, implémentations, et améliorations du projet PS Theme.

> **Format** : Chronologique inversé (plus récent en haut)  
> **Conventions** : Commits structurés (type/scope + body français)

---

## 📅 2025-12-12 : Création specs formulaires (Phase A - Jour 1-2)

**Type** : Documentation formulaires (P0 - CRITIQUE Drupal)  
**Fichiers créés** : 7 specs complètes (3 atoms + 4 molecules)

### 🎯 Objectif

Documenter composants formulaires existants (input, select, textarea, form, form-field, checkboxes, radios) pour **débloquer intégration Drupal Form API** (critère bloquant production).

### 📚 Specs créées

**Atoms (3)** :

1. **input.md** (~650 lignes)
   - 7 types HTML5 (text, email, password, number, search, tel, url)
   - 4 états validation (default, error, success, warning)
   - 7 cas d'usage (email, search, number, tel, password, disabled, Drupal)
   - Validation HTML5 (minlength, maxlength, pattern, required)
   - Compatibilité Drupal Form API (templates, preprocess)
   - Props : type, name, value, placeholder, id, autocomplete, disabled, required, state, attributes
   - Tokens : 20 (sizing, colors, states, borders, focus)
   - Accessibilité WCAG 2.2 AA (8 critères ✅)

2. **select.md** (~650 lignes)
   - Wrapper BEM (ps-select) + native `<select>` + icône chevron
   - Support `<optgroup>` pour catégories
   - 3 états validation (default, error, success)
   - 7 cas d'usage (pays, groupes, pré-sélection, devise, erreur, disabled, Drupal)
   - Navigation clavier (Tab, Espace/Flèches, Enter)
   - Props : options, name, id, disabled, required, error, success, attributes, wrapper_attributes
   - Tokens : 18 (sizing, colors, icon, states, focus)
   - Accessibilité WCAG 2.2 AA (7 critères ✅)

3. **textarea.md** (~650 lignes)
   - Texte multiligne avec redimensionnement vertical
   - Support compteur caractères (maxlength + JS)
   - 4 états validation (default, error, success, warning)
   - 7 cas d'usage (contact, description, compteur, notes, erreur, auto-expand, Drupal)
   - Validation HTML5 (minlength, maxlength, required)
   - Props : name, id, value, placeholder, disabled, required, rows, state, attributes
   - Tokens : 18 (sizing, colors, states, borders, focus, resize)
   - Accessibilité WCAG 2.2 AA (8 critères ✅)

**Molecules (4)** :

1. **form.md** (~500 lignes)
   - Wrapper `<form>` Drupal minimaliste
   - Support méthodes HTTP (GET/POST), action, enctype
   - 3 variants layout (default, inline, filter)
   - 7 cas d'usage (contact, recherche, Drupal Form API, filtres, newsletter, upload, validation)
   - Props : attributes, children (slot)
   - Tokens : 8 (layout, inline, colors optionnels)
   - Accessibilité WCAG 2.2 AA (5 critères ✅, role="search")

2. **form-field.md** (~750 lignes)
   - Champ complet : Label + Input/Select/Textarea + Helper + Error
   - Support 9 types (text, email, password, number, search, tel, url, select, textarea)
   - États validation (error remplace helper)
   - 7 cas d'usage (email, select, textarea, tel, prix, disabled, Drupal)
   - Props : label, type, name, id, value, placeholder, required, disabled, helper, error, rows, options, attributes
   - Tokens : 16 (layout, label, required, helper, error, control)
   - Accessibilité WCAG 2.2 AA (7 critères ✅, aria-invalid, aria-describedby, role="alert")

3. **checkboxes.md** (~650 lignes)
   - Wrapper `<fieldset>` pour groupe de checkboxes
   - Support légende (`<legend>`), layout vertical/inline
   - Validation groupe (au moins une option cochée)
   - 7 cas d'usage (préférences, équipements, requis, disabled, helper, Drupal, indeterminate)
   - Props : attributes, children (slot)
   - Tokens : 12 (layout vertical/inline, legend, error)
   - Accessibilité WCAG 2.2 AA (4 critères ✅, fieldset + legend, aria-required)

4. **radios.md** (~650 lignes)
   - Wrapper `<fieldset>` pour groupe de radios (sélection exclusive)
   - Support légende, layout vertical/inline
   - Attribut `name` commun OBLIGATOIRE (sélection exclusive)
   - 7 cas d'usage (transaction, statut, requis, disabled, helper, Drupal, binaire)
   - Props : attributes, children (slot)
   - Tokens : 12 (layout vertical/inline, legend, error)
   - Accessibilité WCAG 2.2 AA (4 critères ✅, fieldset + legend, flèches ↑↓ sélectionnent)

### 📊 Métriques

- **Total lignes** : ~4,400 lignes (7 specs)
- **Cas d'usage** : 49 exemples (7 × 7 par spec)
- **Props documentés** : 58 (atoms + molecules)
- **Tokens référencés** : 104 (tous existants, aucun nouveau)
- **Critères WCAG** : 43 validés (tous ✅)
- **Templates Drupal** : Mappings Form API expliqués (7 intégrations)

### 🎯 Impact Drupal

**Débloqué** :
- ✅ Intégration Drupal Form API (`#type => 'textfield/select/textarea/radios/checkboxes'`)
- ✅ Validation côté serveur (setErrorByName → aria-invalid + message)
- ✅ Templates override (form-element.html.twig, select.html.twig, etc.)
- ✅ Preprocess functions (mapper data Drupal → props composants)
- ✅ Classes compatibles (form-item, form-control, form-label, form-error)

**Prochaines étapes** :
- Phase A Jour 3-4 : Créer docs/07-integration-drupal/ (5 guides)
- Phase A Jour 5 : Créer templates Drupal essentiels (page.html.twig, form-element.html.twig)

### ✅ Validation

- [x] 7 specs complètes (structure identique à badge.md référence)
- [x] Format markdown cohérent (sections, tableaux, code blocks)
- [x] Cas d'usage Real Estate (contact, recherche, annonces)
- [x] Accessibilité WCAG 2.2 AA documentée
- [x] Intégration Drupal expliquée (Form API, templates, validation)
- [x] Tokens existants uniquement (aucun nouveau token)
- [x] README composants mis à jour (9/19 atoms, 4/21 molecules)

---

## 📅 2025-12-12 : Restructuration documentation (Phase 2/3)

**Type** : Documentation majeure  
**Fichiers créés** : 11 (README hub + tokens + guide développement)

### 🏗️ Structure

**Nouveau** : `docs/` restructuré en français (6 sections numérotées)

```
docs/
├── README.md (628 lignes) - Hub navigation, quick start, parcours apprentissage
├── 01-presentation/ - Vue d'ensemble projet
├── 02-composants/ - Index 87 composants (6/87 = 7%)
├── 03-tokens/ - Design tokens (176 tokens documentés)
├── 04-guide-developpement/ - Guides pratiques
├── 05-changelog/ - Historique (ce fichier)
└── 06-ressources/ - Maquettes
```

### 📚 Tokens (03-tokens/) - 5 fichiers

1. **README.md** (~200 lignes)
   - Vue d'ensemble : 176 tokens, 9 catégories
   - Architecture 3 couches (Layer 1/2/3)
   - Règles d'utilisation (✅ DO / ❌ DON'T)
   - Commandes recherche (npm, grep)
   - Processus création tokens

2. **couleurs.md** (~500 lignes)
   - 88 tokens couleur (72 sémantiques + 4 texte + 6 bordure + 6 overlay)
   - 8 couleurs sémantiques × 9 états (Bootstrap Base-Modifier pattern)
   - PRIMARY, SECONDARY, SUCCESS, DANGER, WARNING, INFO, GOLD, LIGHT/DARK
   - Conformité WCAG 2.2 AA (tous contrastes documentés)
   - Exemples CSS d'utilisation

3. **espacements.md**
   - 33+ tokens base (--size-0 à --size-96)
   - Pattern 4px (0.25rem) increments
   - 10 tokens fluides (clamp() responsive)
   - 5 tokens contenu (ch-based pour lisibilité)
   - Guide usage (padding, margin, gap)

4. **typographie.md**
   - 60 tokens typo (5 familles + 9 poids + 17 tailles + 18 hauteurs ligne + 6 espaces lettres)
   - BNPP Sans (corporate) + Open Sans (alternative)
   - Échelle harmonieuse ratio ~1.125
   - Font sizes : --font-size--2 (10px) → --font-size-14 (120px)
   - Line heights fixes (rem) + relatives (ratio)

5. **autres.md**
   - Bordures : 6 largeurs (1-5px) + 8 rayons (2-24px + round)
   - Ombres : 6 externes (élévation) + 6 internes
   - Animations : 6 durées (0.1s-1s) + presets (fade, slide, spin, etc.)
   - Easing : 35 courbes (standard, elastic, squish, step)
   - Z-index : 7 layers (0, 1, 10, 20, 30, 40, 50 + auto + important)
   - Breakpoints : 7 media queries (400px → 1440px + toolbar Drupal)

### 🛠️ Guide Développement (04-guide-developpement/) - 5 fichiers

1. **README.md**
   - Navigation : 4 guides (démarrage, création, composition, tests)
   - Parcours apprentissage 3 niveaux (Débuter 1j, Développer 1sem, Composer 2sem)
   - Outils : Scripts npm, VS Code snippets, prompts AI
   - Standards : Méthodologie, règles Zero Tolerance
   - Composants référence (6 × 100/100)

2. **demarrage-rapide.md**
   - Installation (5 min) : Clone, npm install, build, watch
   - Commandes essentielles (développement, génération, utilitaires)
   - Structure projet (arborescence clé, fichiers config)
   - Premier composant (scaffolding 5 min)
   - Storybook (navigation, fonctionnalités)
   - Vérifications (checklist démarrage)
   - Troubleshooting (4 problèmes courants)

3. **creer-composant.md** (Guide complet, 11 étapes)
   - **Phase 1** : Préparation (3 étapes : spec, dépendances, tokens)
   - **Phase 2** : Implémentation (6 étapes : 5 fichiers + validation)
     * Étape 5 : Template Twig (header, defaults, ternaire + null, composition)
     * Étape 6 : CSS nesting (100% tokens, Layer 2 variables, cascade ordre)
     * Étape 7 : YAML (contexte Real Estate, Faker.js)
     * Étape 8 : Stories Storybook (autodocs, argTypes catégorisés)
     * Étape 9 : README.md (Usage, Props, BEM, Tokens, A11y, Examples)
   - **Phase 3** : Validation (2 étapes : audit 100 points, tests finaux)
   - **Phase 4** : Commit (format structuré + changelog)
   - Erreurs courantes : 5 anti-patterns avec solutions
   - Composants référence : 6 implémentations parfaites

4. **composition.md** (Token-First Workflow)
   - Principe : 3-layer architecture (Foundation → Defaults → Overrides)
   - Token-First 3 STEPs : Identifier besoin, Vérifier tokens, Override Layer 3
   - Exemples pratiques :
     * Badge + Icon (atom dans atom)
     * Card + Button (molecule avec atom)
     * Alert + Icon + Button (organism complexe)
   - Anti-patterns : 4 erreurs à éviter (modifier parent, baseClass, hardcoder, utilitaires)
   - Patterns avancés : Composition conditionnelle, multiple enfants, cascade responsive

5. **tests-qualite.md** (Audit 100 points)
   - **Audit conformité** : 10 catégories (Architecture 10, Fichiers 10, Twig 15, CSS 20, Storybook 20, YAML 10, README 10, BEM 5, A11y 5, Token-First 5)
   - Score minimum : 90/100 pour production
   - **Validation build** : npm run build (Biome, CSS, Twig)
   - **Troubleshooting** : 15 erreurs courantes avec solutions
     * Token non trouvé, valeur hardcodée, nesting incorrect
     * Arrow function Twig, couleur non-sémantique, missing 'only'
     * Tags autodocs, syntaxe React, focus-visible, prefix icon-
   - Checklist finale avant commit

### 📝 Changelog (05-changelog/)

- Migration de `docs-to-delete/ps-design/CHANGELOG.md` (ce fichier)
- Restructuration en français avec sections thématiques
- Conservation historique complet (1499 lignes d'origine)

### 🔗 Références croisées

**Documentation technique** :
- Instructions complètes : `.github/instructions/` (v4.0.0, 6 fichiers)
- Prompts AI : `.github/prompts/` (13 prompts qualité)

**Composants implémentés** (6/87 = 7%) :
- Badge : 98/100
- Button : 100/100
- Icon : 100/100
- Avatar : 100/100
- Divider : 100/100
- Link : 100/100

### 🎯 Bénéfices

1. **Navigation claire** : 6 sections numérotées + README hub
2. **Langue cohérente** : 100% français (terminologie normalisée)
3. **Tokens documentés** : 176 tokens avec exemples d'utilisation
4. **Guides pratiques** : 4 guides pour développeurs (démarrage → tests)
5. **Référence complète** : Workflows, standards, troubleshooting centralisés

---

## 📅 2025-12-12 : Restructuration documentation (Phase 1/3)

**Type** : Documentation structurelle  
**Fichiers créés** : 3 READMEs (docs, présentation, composants)  
**Commit** : 4bfd9e4

### Avant/Après

**AVANT** :
```
docs/
├── design/ (87 specs) - Anglais, format mixte
├── ps-design/ (changelog, index) - Duplication
├── archive/ - Contenus obsolètes non archivés
├── fonts/ (7 woff2, ~2MB) - Devrait être dans source/assets/
└── maquettes/ - Ressources design
```

**APRÈS** :
```
docs/
├── README.md (628 lignes) - Hub navigation
├── 01-presentation/
│   └── README.md - Vue d'ensemble projet
├── 02-composants/
│   ├── README.md - Index 87 composants (6/87)
│   ├── 01-atomes/ (19 atoms)
│   ├── 02-molecules/ (20 molecules)
│   ├── 03-organismes/ (12 organisms)
│   ├── 04-templates/ (8 templates)
│   └── 05-pages/ (8 pages)
├── 03-tokens/ (vide, Phase 2)
├── 04-guide-developpement/ (vide, Phase 2)
├── 05-changelog/ (vide, Phase 2)
└── 06-ressources/ (vide)
```

### Nettoyage

- ✅ Suppression `docs/fonts/` (7 woff2, ~2MB)
- ✅ Backup `docs/ → docs-to-delete/` (128 fichiers, 19MB)

### READMEs créés

1. **docs/README.md** (628 lignes)
   - Navigation 6 sections avec descriptions
   - Quick start (3 scénarios)
   - Parcours apprentissage (3 niveaux : 1-2h, 2-4h, 1 semaine)
   - Stats projet (6/87 composants, 100+ tokens, WCAG 2.2 AA)
   - Liens vers instructions techniques et prompts

2. **docs/01-presentation/README.md**
   - Qu'est-ce que PS Theme
   - Objectifs (5 : Cohérence, Efficacité, Accessibilité, Maintenabilité, Qualité)
   - Principes fondamentaux (4 : Atomic Design, Token-First, BEM, Composition)
   - État actuel (6/87 = 7%)
   - Architecture (Stack, structure 5 fichiers)
   - Identité visuelle (couleurs, typo, espacement)

3. **docs/02-composants/README.md**
   - Table progression (5 niveaux avec compteurs)
   - Liste composants implémentés (6 avec scores)
   - Listes à implémenter (priorités High/Medium/Low, dépendances)
   - Format spécification (10 sections standardisées)
   - Workflow implémentation (7 étapes)

---

## 📅 2025-12-12 : Bibliothèque de prompts AI

**Type** : Outillage développement  
**Fichiers créés** : 13 prompts (3,649 lignes)  
**Commit** : eaf6a21

### Structure

`.github/prompts/` avec 4 catégories :

#### 1. Component Creation (3 prompts)
- `create-atom.md` : Workflow 11 étapes, CRITICAL RULES, 3-4h
- `create-molecule.md` : Token-First 4 STEPs, composition, 4-6h
- `create-organism.md` : Composition complexe, responsive, 6-8h

#### 2. Quality Assurance (4 prompts)
- `audit-component.md` : Checklist 100 points (8+1 catégories), 20-30min
- `fix-component.md` : Workflow 6 étapes, priorités P0/P1/P2, 1-2h
- `find-issues.md` : 10 catégories, grep automatisé, 1-3h
- `standardize-legacy.md` : Migration Pattern 1/2/3, métriques, 2-4h

#### 3. Maintenance (3 prompts)
- `create-token.md` : Gouvernance 10 étapes, 5 critères, 2-5 jours
- `refactor-css.md` : Flat → nested, 8 étapes, 30-60min
- `update-storybook.md` : Autodocs fix, argTypes, 30-45min

#### 4. Analysis (3 prompts)
- `analyze-project.md` : Rapport 9 sections, grep metrics, 15-30min
- `check-accessibility.md` : Audit WCAG 2.2 AA, 8 catégories, 20-30min
- `find-issues.md` : Détection systématique, priorisation, 1-3h

### Standards qualité (tous respectés ✅)

- **Utiles** : Résout tâches réelles (création, qualité, analyse)
- **Logiques** : Workflows clairs étape par étape
- **Pertinents** : Alignés PS Theme v4.0.0, référence instructions 01-05
- **Clair** : Instructions explicites, exemples BEFORE/AFTER
- **Concis** : Contenu actionnable, commandes directes
- **Intelligents** : Context-aware, validation, anti-patterns documentés

### Contenu type par prompt

- Section contexte (stack, emplacement, standards)
- Workflow numéroté (étapes séquencées)
- Exemples code (BEFORE/AFTER)
- Commandes validation (npm run, grep)
- Critères succès (✅ checklist)
- Format commit (structure message)
- Estimation temps + difficulté
- Prérequis + prompts liés

### Hub navigation

`README.md` avec 4 tables organisées (Création, Qualité, Maintenance, Analyse)

### Usage

1. Copier prompt → 2. Remplacer placeholders ({COMPONENT_NAME}) → 3. Coller dans AI agent → 4. Suivre workflow

### Bénéfices

- Développement accéléré (3-4h → workflow automatisé)
- Qualité constante (audit 100 points → systématique)
- Contexte réduit (prompts incluent références nécessaires)
- Outil onboarding (nouveaux dévs apprennent standards)
- Efficacité maintenance (patterns refactoring documentés)
- Préservation connaissances (anti-patterns + troubleshooting centralisés)

---

## 📅 2025-12-12 : Instructions v4.0.0 (BREAKING CHANGE)

**Type** : Documentation majeure  
**Réduction** : 17 → 6 fichiers (66%)  
**Commits** : 0a2cbf8 → 9e6de02

### Nouvelle structure

1. **01-core-principles.md** (412 lignes) - Fondations
2. **02-component-development.md** (795 lignes) - Workflow complet
3. **03-technical-implementation.md** (1,282 lignes) - Standards code
4. **04-quality-assurance.md** (828 lignes) - Validation
5. **05-maintenance.md** (840 lignes) - Évolution
6. **README.md** (378 lignes) - Navigation hub

### Fichiers consolidés (21 supprimés)

- atomic-design.instructions.md
- components.instructions.md
- css.instructions.md
- templates.instructions.md
- storybook.instructions.md
- javascript.instructions.md
- accessibility.instructions.md
- workflows.instructions.md
- composition-token-first.instructions.md
- card-inheritance.instructions.md
- CODE_EXAMPLES_STYLE_GUIDE.md
- TERMINOLOGY.md
- TOKEN_CREATION_PROCESS.md
- TROUBLESHOOTING_GUIDE.md
- DECISION_FLOWCHARTS.md
- MIGRATION_GUIDES.md
- base-stories.instructions.md
- core.instructions.md
- icon-system.instructions.md
- multi-expert-mode.instructions.md
- card-inheritance-prompt.md

### Bénéfices

- Navigation simplifiée (6 fichiers numérotés)
- Duplication éliminée (DRY principle)
- Hiérarchie claire (01-05 progression logique)
- Découverte facilitée (README avec scénarios)
- Maintenabilité accrue (source unique par sujet)

---

## 📅 2025-11 : Projet cleanup

**Type** : Maintenance  
**Fichiers supprimés** : 98 (anciens audits, reports, prompts)

### Suppressions

- Anciens audits composants (CARD_AUDIT_REPORT.md, LINK_AUDIT_FINAL_REPORT.md, etc.)
- Reports implémentation (IMPLEMENTATION_COMPLETE.md, BASE_STORIES_STANDARDISATION.md, etc.)
- Prompts legacy (ADD_ICON_SUPPORT_PROMPT.txt, ICON_STORY_FIX_PROMPT.md, etc.)
- Documentation obsolète (archive/, START_HERE.md, etc.)

### Nettoyage dépendances

- Suppression Husky (hooks Git non utilisés)
- Mise à jour package.json (scripts lint/format simplifiés)

---

## 📅 2025-10 : Composants Phase 1-3

**Implémentés** : 6/87 composants (7%)

### Badge (Element)

**Score** : 98/100  
**Date** : 2025-10

#### Caractéristiques

- 8 variantes sémantiques (neutral, primary, secondary, success, danger, warning, info, gold)
- 4 tailles (xs, sm, md, lg)
- Modificateur pill (forme arrondie complète)
- Intégration icon via composition
- WCAG 2.2 AA ✅ (tous contrastes validés)

#### Tokens utilisés

- Couleurs : `--primary`, `--success`, `--danger`, etc. (sémantiques)
- Espacements : `--size-1`, `--size-2` (padding)
- Typographie : `--font-size-0`, `--font-weight-600`
- Bordures : `--radius-2`, `--radius-round`

#### Fichiers

- Spec : `docs/design/atoms/badge.md`
- Implémentation : `source/patterns/elements/badge/`
- Storybook : Elements > Badge

---

### Button (Element)

**Score** : 100/100  
**Date** : 2025-09

#### Caractéristiques

- 6 variantes (primary, secondary, tertiary, ghost, link, danger)
- 4 tailles (xs, sm, md, lg)
- 3 largeurs (auto, full, content)
- États interactifs (hover, active, focus-visible, disabled, loading)
- Intégration icon (left, right, only)
- WCAG 2.2 AA ✅ (focus 3:1, contraste 4.5:1)

#### CSS nesting parfait

- 100% tokens (aucune valeur hardcodée)
- Variables component-scoped (Layer 2)
- Cascade ordre correct (Base → Elements → Modifiers → States)
- Tous modificateurs indépendants

#### Fichiers

- Implémentation : `source/patterns/elements/button/`
- Storybook : Elements > Button

---

### Icon (Element)

**Score** : 100/100  
**Date** : 2025-09

#### Caractéristiques

- Système sprite SVG (`/icons/icons-sprite.svg`)
- 4 tailles (xs 12px, sm 16px, md 20px, lg 24px)
- Coloration via `currentColor` (hérite parent)
- 100+ icônes disponibles (generic, country, univers, etc.)

#### Usage

```twig
{% include '@elements/icon/icon.twig' with {
  icon: 'check',
  size: 'md',
} only %}
```

**⚠️ Important** : Nom sans préfixe `icon-` (auto-ajouté par CSS)

#### Build

```bash
npm run build:icons
# Génère sprite depuis source/icons-source/**/*.svg
```

---

### Avatar (Element)

**Score** : 100/100  
**Date** : 2025-09

#### Caractéristiques

- 5 tailles (xs 24px, sm 32px, md 40px, lg 48px, xl 64px)
- 3 types (image, initiales, fallback SVG)
- Forme ronde par défaut
- Fallback automatique (image → initiales → SVG)

#### Markup minimal

```twig
{% include '@elements/avatar/avatar.twig' with {
  image: '/images/user.jpg',
  name: 'Jean Dupont',
  size: 'md',
} only %}
```

---

### Divider (Element)

**Score** : 100/100  
**Date** : 2025-09

#### Caractéristiques

- 2 orientations (horizontal, vertical)
- 3 styles (solid, dashed, dotted)
- Espacement automatique adaptatif
- Code minimal (simplicité maximale)

---

### Link (Element)

**Score** : 100/100  
**Date** : 2025-09

#### Caractéristiques

- 4 variantes (default, primary, muted, inverted)
- États complets (hover, focus-visible, active, visited)
- Support liens externes (icône auto, target _blank)
- Intégration icon (left, right)
- WCAG 2.2 AA ✅ (underline, focus 2px)

---

## 🎯 Progression actuelle

**Total** : 6/87 composants (7%)

### Par niveau (Atomic Design)

| Niveau | Implémentés | Total | % |
|--------|-------------|-------|---|
| **Atoms** (elements/) | 6 | 19 | 32% |
| **Molecules** (components/) | 0 | 20 | 0% |
| **Organisms** (collections/) | 0 | 12 | 0% |
| **Templates** (layouts/) | 0 | 8 | 0% |
| **Pages** (pages/) | 0 | 8 | 0% |

### Prochaines phases

**Phase 4** : Molecules (card, form-field, alert, etc.)  
**Phase 5** : Organisms (header, footer, navigation, etc.)  
**Phase 6** : Templates + Pages

---

## 🔗 Références

- **Documentation** : `docs/` (restructuré Phase 2)
- **Instructions** : `.github/instructions/` (v4.0.0)
- **Prompts** : `.github/prompts/` (13 prompts)
- **Storybook** : http://localhost:6006

---

**Mainteneurs** : Design System Team  
**Contact** : Voir README projet
