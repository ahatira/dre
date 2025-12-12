# Vue d'ensemble - Intégration Drupal

**Architecture et workflow** pour intégrer PS Theme dans Drupal 10/11

---

## 🎯 Objectif

Connecter les **composants Storybook** (développés isolément) avec **Drupal** (production) via templates Twig, libraries YAML, et preprocess functions PHP.

---

## 🏗️ Architecture système

### Stack technique

```
┌─────────────────────────────────────────────────────────┐
│ DÉVELOPPEMENT (Storybook)                                │
├─────────────────────────────────────────────────────────┤
│ source/patterns/          ← Composants sources           │
│   ├── elements/           ← Atoms (button, input, etc.)  │
│   ├── components/         ← Molecules (card, alert, etc.)│
│   ├── collections/        ← Organisms (header, footer)   │
│   ├── layouts/            ← Templates (page layouts)     │
│   └── pages/              ← Pages (home, search)         │
│                                                           │
│ source/props/             ← Design tokens CSS            │
│   ├── colors.css          ← 88 tokens couleur           │
│   ├── sizes.css           ← 33+ tokens espacement       │
│   └── fonts.css           ← 60 tokens typographie       │
└─────────────────────────────────────────────────────────┘
                             ↓
                    npm run build (Vite)
                             ↓
┌─────────────────────────────────────────────────────────┐
│ ASSETS COMPILÉS                                          │
├─────────────────────────────────────────────────────────┤
│ dist/css/styles.css       ← CSS global compilé          │
│ dist/js/vendors.js        ← JavaScript vendors          │
│ dist/icons/sprite.svg     ← Icônes sprite SVG           │
└─────────────────────────────────────────────────────────┘
                             ↓
                    Drupal utilise via
                             ↓
┌─────────────────────────────────────────────────────────┐
│ PRODUCTION (Drupal)                                      │
├─────────────────────────────────────────────────────────┤
│ templates/                ← Templates Drupal Twig        │
│   ├── layout/             ← page, region, html          │
│   ├── navigation/         ← menu, breadcrumb, pager     │
│   ├── content/            ← node, field, taxonomy       │
│   └── form/               ← form, form-element, input   │
│                                                           │
│ ps.libraries.yml          ← Définitions CSS/JS          │
│ ps.theme                  ← Preprocess functions PHP    │
│ ps.info.yml               ← Configuration thème         │
└─────────────────────────────────────────────────────────┘
```

---

## 🔄 Workflow développement → production

### 1. Développement isolé (Storybook)

```bash
# Développer composant dans Storybook
npm run watch
# → http://localhost:6006

# Créer composant (scaffolding)
npm run generate:pattern -- --type=element --name="Button"

# Fichiers générés :
# source/patterns/elements/button/
#   ├── button.twig        ← Template
#   ├── button.css         ← Styles (tokens uniquement)
#   ├── button.yml         ← Props par défaut
#   ├── button.stories.jsx ← Documentation Storybook
#   └── README.md          ← Spec complète
```

**Avantages** :
- ✅ Développement rapide (hot reload)
- ✅ Isolation complète (pas de dépendances Drupal)
- ✅ Documentation automatique (Autodocs)
- ✅ Tests visuels (variantes, états, responsive)

---

### 2. Compilation assets (Build)

```bash
# Compiler CSS/JS pour production
npm run build

# Génère :
# dist/css/styles.css     ← CSS global (tous composants + tokens)
# dist/js/vendors.js      ← JavaScript vendors bundle
# dist/icons/sprite.svg   ← Icônes SVG sprite
```

**Processus Vite** :
1. Parse tous `*.css` dans `source/patterns/` et `source/props/`
2. Compile PostCSS (nesting, custom properties)
3. Minifie et optimise
4. Génère sourcemaps (dev only)
5. Output dans `dist/`

---

### 3. Intégration Drupal (Templates)

```twig
{# templates/content/node--article.html.twig #}

{# Attacher library CSS/JS #}
{{ attach_library('ps/card') }}

{# Inclure composant Storybook via namespace #}
{% include '@components/card/card.twig' with {
  title: node.title.value,
  description: node.body.value|striptags|truncate(150),
  image: node.field_image.entity.uri.value,
  cta_text: 'Lire la suite',
  cta_href: url('<front>') ~ node.url,
  badge_text: node.field_category.entity.name.value,
  badge_variant: 'primary'
} only %}
```

**Mapping data** :
- Variables Drupal (`node.*`) → Props composant
- Namespaces SDC (`@components/`) → Chemin source
- `only` keyword → Sécurité (seulement props explicites)

---

## 🧩 Composants clés système

### 1. ps.info.yml (Configuration thème)

```yaml
# Configuration thème Drupal
name: PS Theme
type: theme
core_version_requirement: ^10
base theme: false

# Library globale chargée partout
libraries:
  - ps/global

# Régions Drupal (zones contenu)
regions:
  header: Header
  content: Content
  footer: Footer

# Namespaces SDC (Single Directory Components)
components:
  namespaces:
    elements: source/patterns/elements
    components: source/patterns/components
    collections: source/patterns/collections
```

**Rôle** :
- Définit métadonnées thème (nom, version, compatibilité)
- Configure régions (zones layout Drupal)
- Déclare namespaces composants (`@elements/`, `@components/`)
- Attache library globale (CSS/JS partout)

---

### 2. ps.libraries.yml (Assets CSS/JS)

```yaml
# Library globale (chargée partout)
global:
  css:
    base:
      dist/css/styles.css: {}

# Library composant spécifique (chargée à la demande)
button:
  css:
    component:
      dist/css/button.css: {}
  js:
    dist/js/button.js: {}
  dependencies:
    - core/drupal
    - ps/global
```

**Rôle** :
- Définit assets CSS/JS par composant
- Gère dépendances (ordre chargement)
- Optimise performances (charge seulement si utilisé)

**Attacher dans template** :
```twig
{{ attach_library('ps/button') }}
```

**Attacher dans preprocess** :
```php
$variables['#attached']['library'][] = 'ps/button';
```

---

### 3. ps.theme (Preprocess functions)

```php
<?php
/**
 * @file
 * Preprocess functions pour PS Theme
 */

/**
 * Implements hook_preprocess_node().
 */
function ps_preprocess_node(&$variables) {
  $node = $variables['node'];
  
  // Mapper ViewMode → Variante composant
  if ($variables['view_mode'] === 'teaser') {
    $variables['card_variant'] = 'horizontal';
  }
  
  // Attacher library automatiquement
  $variables['#attached']['library'][] = 'ps/card';
  
  // Ajouter classes BEM
  $variables['attributes']['class'][] = 'ps-node';
  $variables['attributes']['class'][] = 'ps-node--' . $node->bundle();
}
```

**Rôle** :
- Altérer variables avant rendu template
- Mapper data Drupal → Props composants
- Attacher libraries automatiquement
- Ajouter classes BEM/utilitaires

---

### 4. templates/ (Templates Twig Drupal)

```
templates/
├── layout/
│   ├── page.html.twig              ← Structure page complète
│   ├── region.html.twig            ← Wrapper régions
│   └── html.html.twig              ← <html> wrapper
├── navigation/
│   ├── menu.html.twig              ← Menus navigation
│   ├── breadcrumb.html.twig        ← Fil d'Ariane
│   └── pager.html.twig             ← Pagination
├── content/
│   ├── node.html.twig              ← Nodes (articles, pages)
│   ├── field.html.twig             ← Champs individuels
│   └── taxonomy-term.html.twig     ← Termes taxonomie
└── form/
    ├── form.html.twig              ← Wrapper formulaires
    ├── form-element.html.twig      ← Champs formulaires
    ├── input.html.twig             ← Inputs texte
    └── select.html.twig            ← Selects
```

**Rôle** :
- Override templates Drupal par défaut
- Inclure composants Storybook via namespaces
- Mapper variables Drupal → Props composants

---

## 🔗 Namespaces SDC (Single Directory Components)

### Configuration (ps.info.yml)

```yaml
components:
  namespaces:
    elements: source/patterns/elements
    components: source/patterns/components
    collections: source/patterns/collections
```

### Usage dans templates

```twig
{# Inclure atom Button #}
{% include '@elements/button/button.twig' with {
  text: 'Cliquez ici',
  variant: 'primary'
} only %}

{# Inclure molecule Card #}
{% include '@components/card/card.twig' with {
  title: 'Titre',
  description: 'Description'
} only %}

{# Inclure organism Header #}
{% include '@collections/header/header.twig' with {
  logo: '/images/logo.svg',
  menu_items: menu_items
} only %}
```

**Avantages** :
- ✅ Chemins courts (`@elements/` vs `../../../source/patterns/elements/`)
- ✅ Réutilisabilité (même composant Storybook + Drupal)
- ✅ Isolation (composants autonomes)
- ✅ Maintenance (renommer dossier sans casser includes)

---

## 🚨 Différences Twig : Storybook vs Drupal

### Arrow functions (❌ NON supporté Drupal)

```twig
{# ❌ MAUVAIS - Fonctionne Storybook, PAS Drupal #}
{% set classes = classes|filter(v => v) %}
{% set items = items|map(i => i.title) %}

{# ✅ CORRECT - Compatible Drupal #}
{% set classes = [
  'ps-button',
  variant ? 'ps-button--' ~ variant : null
]|join(' ')|trim %}
```

### Méthodes JavaScript (❌ NON supporté Drupal)

```twig
{# ❌ MAUVAIS - Storybook only #}
{% set hasIcon = ['check', 'arrow'].includes(icon) %}

{# ✅ CORRECT - Drupal compatible #}
{% set hasIcon = icon == 'check' or icon == 'arrow' %}
```

### create_attribute() (✅ Drupal natif)

```twig
{# Storybook (mock function) #}
{% set attributes = attributes|default(create_attribute()) %}

{# Drupal (fonction native) #}
{% set attributes = attributes|default({}) %}
{# create_attribute() existe déjà dans Drupal #}
```

---

## 📊 Checklist intégration

### Avant de commencer

- [ ] Drupal 10+ installé
- [ ] Node.js 18+ installé
- [ ] Thème PS activé (`drush theme:install ps`)
- [ ] `npm install` exécuté
- [ ] `npm run build` passe sans erreur

### Configuration thème

- [ ] `ps.info.yml` : Namespaces SDC configurés
- [ ] `ps.libraries.yml` : Libraries définies (global + composants)
- [ ] `ps.theme` : Fichier créé (preprocess functions)
- [ ] Templates Drupal : Créés dans `templates/`

### Tests

- [ ] Assets chargés (inspect `<head>` → `styles.css`)
- [ ] Composants affichés (inspect elements → classes BEM)
- [ ] JavaScript fonctionnel (interactions, accordions, modals)
- [ ] Responsive (mobile 375px, tablet 768px, desktop 1440px)
- [ ] Accessibilité (WCAG 2.2 AA, screen readers, clavier)

---

## 🔧 Commandes essentielles

### Développement

```bash
# Développer dans Storybook (hot reload)
npm run watch

# Compiler assets production
npm run build

# Générer composant
npm run generate:pattern -- --type=element --name="Badge"

# Chercher token
npm run tokens:check -- --primary
```

### Drupal (Drush)

```bash
# Clear cache Drupal (TOUJOURS après modif templates/CSS)
drush cr

# Activer thème
drush theme:install ps
drush config:set system.theme default ps

# Lister thèmes
drush pm:list --type=theme

# Voir logs
drush watchdog:show
```

---

## 📚 Ressources

### Documentation Drupal

- **Theming Guide** : https://www.drupal.org/docs/theming-drupal
- **Twig in Drupal** : https://www.drupal.org/docs/theming-drupal/twig-in-drupal
- **SDC (Single Directory Components)** : https://www.drupal.org/docs/develop/theming-drupal/using-single-directory-components
- **Libraries** : https://www.drupal.org/docs/theming-drupal/adding-stylesheets-css-and-javascript-js-to-a-drupal-theme
- **Preprocess** : https://www.drupal.org/docs/theming-drupal/modifying-output-with-preprocess-functions

### Fichiers clés projet

- **Configuration** : `ps.info.yml`, `ps.libraries.yml`
- **Composants** : `source/patterns/` (Storybook)
- **Templates** : `templates/` (Drupal)
- **Assets** : `dist/` (compilés)
- **Tokens** : `source/props/` (design tokens)

---

## 🎯 Prochaines étapes

1. **[Templates Drupal](./02-templates.md)** → Mapper composants Storybook → Templates Drupal
2. **[Libraries & Assets](./03-libraries-assets.md)** → Configurer CSS/JS, dépendances
3. **[Drupal Forms](./04-drupal-forms.md)** → Intégrer Form API avec composants
4. **[Preprocess Functions](./05-preprocess.md)** → Altérer data, ajouter variables
5. **[Déploiement](./06-deploiement.md)** → Build production, cache, CI/CD

---

**Navigation** : [← README](./README.md) | [Templates →](./02-templates.md)
