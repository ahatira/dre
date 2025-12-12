# Audit de Cohérence - PS Theme

**Date** : 12 décembre 2025  
**Objectif** : Vérifier la cohérence complète avant intégration Drupal finale  
**Statut** : 🔍 En cours d'analyse

---

## 📊 État Actuel

### Composants

| Niveau | Implémentés | Specs | Écart | % Complet |
|--------|-------------|-------|-------|-----------|
| **Elements** (atoms) | 21 | 19 | +2 | 110% ⚠️ |
| **Components** (molecules) | 26 | 21 | +5 | 124% ⚠️ |
| **Collections** (organisms) | 2 | 13 | -11 | 15% |
| **Layouts** (templates) | 1 | 8 | -7 | 13% |
| **Pages** | 1 | 8 | -7 | 13% |
| **TOTAL** | **51** | **69** | **-18** | **74%** |

### Tokens Design

| Fichier | Tokens | Documenté | Statut |
|---------|--------|-----------|--------|
| `colors.css` | 88 | ✅ docs/03-tokens/couleurs.md | OK |
| `sizes.css` | 33+ | ✅ docs/03-tokens/espacements.md | OK |
| `fonts.css` | 60 | ✅ docs/03-tokens/typographie.md | OK |
| `borders.css` | 14 | ✅ docs/03-tokens/autres.md | OK |
| `shadows.css` | 12 | ✅ docs/03-tokens/autres.md | OK |
| `animations.css` | 6 | ✅ docs/03-tokens/autres.md | OK |
| `easing.css` | 35 | ✅ docs/03-tokens/autres.md | OK |
| `zindex.css` | 9 | ✅ docs/03-tokens/autres.md | OK |
| `media.css` | 7 | ✅ docs/03-tokens/autres.md | OK |
| **TOTAL** | **264** | **✅ 100%** | **OK** |

### Intégration Drupal

| Aspect | État | Statut |
|--------|------|--------|
| **Templates Drupal** | 2 fichiers (block, breadcrumb) | ⚠️ Incomplet |
| **Libraries YAML** | 80 entrées | ✅ OK |
| **Namespaces** | 7 configurés (ps.info.yml) | ✅ OK |
| **Regions** | 9 configurées | ✅ OK |
| **CKEditor styles** | 2 CSS (global, button) | ⚠️ Incomplet |

---

## 🚨 Incohérences Détectées

### 1. Composants implémentés SANS spec (PRIORITÉ HAUTE)

**Elements manquants dans docs/02-composants/01-atomes/** :

- ❌ **input.md** (implémenté : `source/patterns/elements/input/`)
- ❌ **select.md** (implémenté : `source/patterns/elements/select/`)
- ❌ **textarea.md** (implémenté : `source/patterns/elements/textarea/`)

**Raison** : Specs utilisent "field.md" générique, mais implémentations séparées

**Impact** : Documentation incomplète, développeurs ne trouvent pas les specs

**Action recommandée** :
- [ ] Créer `input.md` avec props, variants, a11y
- [ ] Créer `select.md` avec options, groups, états
- [ ] Créer `textarea.md` avec rows, resize, validation
- [ ] OU fusionner implémentations sous "field" générique

---

### 2. Composants implémentés non listés dans INDEX

**Components (molecules) non documentés** :

- ❌ **card-offer-search** (implémenté mais pas dans specs)
- ❌ **card-offer-slide** (implémenté mais pas dans specs)
- ❌ **checkboxes** (implémenté, spec existe : checkbox atom)
- ❌ **form** (implémenté mais pas dans specs)
- ❌ **form-field** (implémenté, spec existe : form-element)
- ❌ **radios** (implémenté, spec existe : radio atom)

**Impact** : Index composants (`docs/02-composants/README.md`) incomplet

**Action recommandée** :
- [ ] Mettre à jour `docs/02-composants/README.md` avec 51 composants réels
- [ ] Créer specs manquantes (card-offer-*, form)
- [ ] Clarifier atoms vs molecules (checkboxes/radios)

---

### 3. Manque de specs form (CRITIQUE pour Drupal)

**Composants form essentiels à documenter** :

- ❌ **form.md** (molecule) - Container formulaire
- ❌ **form-field.md** (molecule) - Label + Input + Error
- ❌ **input.md** (atom) - Champ texte
- ❌ **select.md** (atom) - Liste déroulante
- ❌ **textarea.md** (atom) - Zone texte multiligne
- ❌ **checkboxes.md** (molecule) - Groupe checkboxes
- ❌ **radios.md** (molecule) - Groupe radios

**Impact** : Intégration Drupal Forms impossible sans specs

**Action recommandée** :
- [ ] PRIORITÉ 1 : Créer toutes les specs form
- [ ] Documenter intégration Drupal Form API
- [ ] Ajouter exemples validation, états (error, success)

---

### 4. Templates Drupal incomplets (CRITIQUE)

**Templates Drupal existants** : 2 seulement (block, breadcrumb)

**Templates manquants pour Drupal** :

- ❌ `templates/layout/page.html.twig`
- ❌ `templates/layout/region.html.twig`
- ❌ `templates/navigation/menu.html.twig`
- ❌ `templates/navigation/menu-local-task.html.twig`
- ❌ `templates/field/field.html.twig`
- ❌ `templates/node/node.html.twig`
- ❌ `templates/views/views-view.html.twig`
- ❌ `templates/form/form.html.twig`
- ❌ `templates/form/form-element.html.twig`

**Impact** : Intégration Drupal bloquée, thème non fonctionnel

**Action recommandée** :
- [ ] PRIORITÉ 0 : Créer tous les templates Drupal essentiels
- [ ] Mapper composants Storybook → Templates Drupal
- [ ] Documenter overrides Drupal dans docs/

---

### 5. CKEditor styles insuffisants

**Styles CKEditor actuels** : 2 CSS (global, button)

**Styles manquants** :

- ❌ Headings (h1-h6)
- ❌ Links (styles, external)
- ❌ Lists (ul, ol)
- ❌ Tables
- ❌ Blockquotes
- ❌ Images (alignments, captions)

**Impact** : Éditeurs Drupal ne voient pas les vrais styles

**Action recommandée** :
- [ ] Ajouter tous les éléments éditables dans `ckeditor5-stylesheets`
- [ ] Créer preview Storybook pour éditeurs

---

### 6. Documentation intégration Drupal manquante

**Docs manquantes** :

- ❌ Guide intégration Drupal (templates, libraries, preprocess)
- ❌ Mapping composants → Templates Drupal
- ❌ Guide Drupal Form API
- ❌ Guide Drupal Twig functions/filters
- ❌ Guide déploiement thème

**Impact** : Développeurs Drupal perdus, pas de pont Storybook ↔ Drupal

**Action recommandée** :
- [ ] Créer `docs/07-integration-drupal/`
- [ ] 5 guides : Templates, Libraries, Forms, Preprocess, Déploiement

---

## ✅ Points Positifs

### Tokens complets

✅ 264 tokens documentés (100%)  
✅ Architecture 3 couches claire  
✅ WCAG 2.2 AA compliance documentée  
✅ Exemples d'usage CSS

### Guides développement solides

✅ 4 guides pratiques (démarrage, création, composition, tests)  
✅ Workflow 11 étapes documenté  
✅ Token-First expliqué (3 STEPs)  
✅ Audit 100 points + troubleshooting

### Composants core implémentés

✅ 6 atoms parfaits (badge, button, icon, avatar, divider, link)  
✅ Base solide pour composer molecules  
✅ Storybook fonctionnel avec Autodocs

---

## 📋 Plan d'Action (3 Phases)

### Phase A : Compléter Documentation (1 semaine)

**PRIORITÉ 0 : Specs Form (CRITIQUE)**

- [ ] Créer `docs/02-composants/01-atomes/input.md`
- [ ] Créer `docs/02-composants/01-atomes/select.md`
- [ ] Créer `docs/02-composants/01-atomes/textarea.md`
- [ ] Créer `docs/02-composants/02-molecules/form.md`
- [ ] Créer `docs/02-composants/02-molecules/form-field.md`
- [ ] Créer `docs/02-composants/02-molecules/checkboxes.md`
- [ ] Créer `docs/02-composants/02-molecules/radios.md`

**PRIORITÉ 1 : Mettre à jour INDEX**

- [ ] Corriger `docs/02-composants/README.md` (51 composants réels vs 87 prévus)
- [ ] Lister composants implémentés par niveau (21+26+2+1+1)
- [ ] Ajouter statut "Implémenté sans spec" pour card-offer-*, form
- [ ] Mettre à jour progression : 51/87 (59% vs 7%)

**PRIORITÉ 2 : Guide Intégration Drupal**

- [ ] Créer `docs/07-integration-drupal/README.md`
- [ ] Guide 1 : Templates Drupal (mapping composants)
- [ ] Guide 2 : Libraries & Assets (CSS, JS, dépendances)
- [ ] Guide 3 : Drupal Forms (Form API, validation, états)
- [ ] Guide 4 : Preprocess functions (alter data, add variables)
- [ ] Guide 5 : Déploiement (build, sync, cache)

---

### Phase B : Intégration Drupal (2-3 semaines)

**PRIORITÉ 0 : Templates essentiels**

- [ ] `templates/layout/page.html.twig` (wrapper page complète)
- [ ] `templates/layout/region.html.twig` (régions thème)
- [ ] `templates/navigation/menu.html.twig` (menus Drupal)
- [ ] `templates/field/field.html.twig` (champs contenus)
- [ ] `templates/node/node.html.twig` (contenus)
- [ ] `templates/form/form.html.twig` (formulaires)
- [ ] `templates/form/form-element.html.twig` (éléments form)

**PRIORITÉ 1 : Preprocess functions**

- [ ] Créer `ps.theme` (preprocess hooks)
- [ ] Mapper data Drupal → Props composants
- [ ] Gérer variantes via ViewModes
- [ ] Ajouter classes BEM automatiquement

**PRIORITÉ 2 : CKEditor styles**

- [ ] Ajouter tous CSS éditables dans `ps.info.yml`
- [ ] Tester preview WYSIWYG
- [ ] Documenter styles disponibles éditeurs

---

### Phase C : Compléter Composants (4-6 semaines)

**Organisms (priorité collections/)** :

- [ ] Header (navigation principale)
- [ ] Footer (liens, copyright)
- [ ] Hero (bandeau accueil)
- [ ] Search form (recherche biens)
- [ ] Filter panel (filtres recherche)

**Templates (layouts/)** :

- [ ] Page container (wrapper principal)
- [ ] Two column (sidebar + content)
- [ ] Full width (pleine largeur)
- [ ] Hero layout (hero + content)

**Pages** :

- [ ] Home page (accueil)
- [ ] Property search (recherche biens)
- [ ] Property detail (détail bien)

---

## 🎯 Objectifs Finaux (3 mois)

### Documentation

- ✅ 87 composants specs complètes
- ✅ 7 sections docs (+ 07-integration-drupal/)
- ✅ Guides intégration Drupal (5 guides)
- ✅ Changelog mis à jour

### Implémentation

- ✅ 87 composants implémentés (100%)
- ✅ Templates Drupal complets (~20 fichiers)
- ✅ Preprocess functions (ps.theme)
- ✅ CKEditor styles complets

### Intégration Drupal

- ✅ Thème Drupal 10/11 fonctionnel
- ✅ Form API intégrée
- ✅ Regions configurées
- ✅ Menus + navigation
- ✅ Contenus + champs
- ✅ Build automatisé (CI/CD)

### Qualité

- ✅ 100% composants score ≥ 90/100
- ✅ WCAG 2.2 AA compliance
- ✅ Tests automatisés (Jest, Pa11y)
- ✅ Documentation complète

---

## 🚀 Actions Immédiates (Cette Semaine)

### Jour 1-2 : Specs Form (CRITIQUE)

1. Créer 3 specs atoms : input, select, textarea
2. Créer 4 specs molecules : form, form-field, checkboxes, radios
3. Documenter intégration Drupal Form API

### Jour 3-4 : Guide Intégration Drupal

1. Créer `docs/07-integration-drupal/README.md`
2. Guide mapping composants → Templates
3. Guide libraries & assets

### Jour 5 : Templates Drupal essentiels

1. `templates/layout/page.html.twig`
2. `templates/form/form-element.html.twig`
3. Tester intégration basique

---

## 📞 Support & Ressources

### Documentation existante

- **Instructions** : `.github/instructions/` (v4.0.0)
- **Prompts AI** : `.github/prompts/` (13 prompts)
- **Docs** : `docs/` (83 fichiers)

### Références Drupal

- **Drupal Theming Guide** : https://www.drupal.org/docs/theming-drupal
- **Twig in Drupal** : https://www.drupal.org/docs/theming-drupal/twig-in-drupal
- **Form API** : https://www.drupal.org/docs/drupal-apis/form-api

---

**Maintainers** : Design System Team  
**Last Updated** : 2025-12-12
