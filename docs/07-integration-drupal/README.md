# Intégration Drupal

**Guide complet** pour intégrer PS Theme dans Drupal 10/11

---

## 🎯 Objectif

Connecter les composants Storybook (source/patterns/) avec les templates Drupal (templates/) via libraries, preprocess functions, et Form API.

---

## 📚 Contenu

### [1. Vue d'ensemble](./01-vue-ensemble.md)
**10 minutes** | Architecture, stack, workflow Drupal

- Architecture thème Drupal custom
- Stack technique (Twig, YAML, Preprocess)
- Namespaces SDC (Single Directory Components)
- Workflow développement Storybook → Drupal
- Différences Twig Storybook vs Drupal

### [2. Templates Drupal](./02-templates.md)
**Guide complet** | Mapping composants → Templates Drupal

- Templates essentiels (page, region, node, field)
- Mapping composants Storybook → Templates
- Override templates (theme suggestions)
- Include composants via namespaces
- Passer variables Drupal → Props composants
- Exemples complets

### [3. Libraries & Assets](./03-libraries-assets.md)
**Configuration** | CSS, JS, dépendances, build

- Structure ps.libraries.yml
- Attacher libraries aux templates
- Dépendances entre libraries
- Build assets (npm run build)
- Cache Drupal (rebuild, clear)
- Environnements (dev, prod)

### [4. Drupal Forms](./04-drupal-forms.md)
**Form API** | Intégration formulaires, validation, états

- Form API Drupal basics
- Render elements vs Composants
- Mapping Form API → Composants form
- Form states (error, success, disabled)
- Validation messages
- Exemples formulaires (search, contact, user)

### [5. Preprocess Functions](./05-preprocess.md)
**Hooks Drupal** | Altérer données, ajouter variables

- Fichier ps.theme (hooks)
- Preprocess hooks (node, field, page)
- Ajouter classes BEM automatiquement
- Mapper data Drupal → Props composants
- ViewModes → Variantes composants
- Exemples complets (card, alert, menu)

### [6. Déploiement](./06-deploiement.md)
**Production** | Build, sync, cache, CI/CD

- Build production (npm run build)
- Sync assets (dist/)
- Cache Drupal (config, render, asset)
- Drush commands
- CI/CD pipeline (GitHub Actions)
- Checklist déploiement

---

## 🚀 Quick Start (30 minutes)

### Étape 1 : Vérifier configuration

```bash
# Vérifier namespaces SDC
grep -A10 "components:" ps.info.yml

# Vérifier libraries
grep "^[a-z]" ps.libraries.yml | head -10

# Vérifier templates Drupal
find templates/ -name "*.twig"
```

**Résultat attendu** :
- ✅ 7 namespaces configurés (ps, images, base, elements, layouts, components, collections)
- ✅ 80+ libraries définies
- ✅ 2+ templates Drupal (block, breadcrumb)

---

### Étape 2 : Créer premier template

**Fichier** : `templates/layout/page.html.twig`

```twig
{#
/**
 * @file
 * Template pour page complète Drupal
 * 
 * Variables disponibles:
 * - page: Régions (header, content, footer, etc.)
 * - node: Contenu actuel (si page node)
 * - user: Utilisateur connecté
 */
#}

<div class="page-wrapper">
  {# Header #}
  {% if page.header %}
    <header class="page-header">
      {{ page.header }}
    </header>
  {% endif %}

  {# Content principal #}
  <main class="page-content">
    {% if page.content %}
      {{ page.content }}
    {% endif %}
  </main>

  {# Footer #}
  {% if page.footer %}
    <footer class="page-footer">
      {{ page.footer }}
    </footer>
  {% endif %}
</div>
```

---

### Étape 3 : Inclure composant Storybook

**Utiliser namespace SDC** (`@elements/`, `@components/`, etc.)

```twig
{# Dans n'importe quel template Drupal #}

{# Inclure button atom #}
{% include '@elements/button/button.twig' with {
  text: 'En savoir plus',
  variant: 'primary',
  href: node.url,
} only %}

{# Inclure card molecule #}
{% include '@components/card/card.twig' with {
  title: node.title,
  description: node.body.value|striptags|truncate(150),
  image: node.field_image.entity.uri.value,
  cta_text: 'Voir le bien',
  cta_href: node.url,
} only %}
```

---

### Étape 4 : Attacher library CSS/JS

**Dans le template** :

```twig
{# Attacher library button #}
{{ attach_library('ps/button') }}

{# Utiliser le composant #}
{% include '@elements/button/button.twig' with {...} only %}
```

**Ou via ps.theme** (preprocess) :

```php
<?php
/**
 * Implements hook_preprocess_node().
 */
function ps_preprocess_node(&$variables) {
  // Attacher library card pour tous les nodes
  $variables['#attached']['library'][] = 'ps/card';
}
```

---

### Étape 5 : Tester

```bash
# Build assets
npm run build

# Clear cache Drupal
drush cr

# Vérifier la page
# → http://localhost/drupal
```

---

## 🎓 Concepts Clés

### SDC (Single Directory Components)

**Drupal 10+** supporte les composants dans des dossiers uniques avec namespace.

**Configuration** (`ps.info.yml`) :

```yaml
components:
  namespaces:
    elements: source/patterns/elements
    components: source/patterns/components
    collections: source/patterns/collections
```

**Usage dans templates** :

```twig
{% include '@elements/button/button.twig' with {...} only %}
```

**Avantages** :
- ✅ Composants isolés (1 dossier = 1 composant)
- ✅ Réutilisables (Storybook + Drupal)
- ✅ Namespaces clairs (`@elements/`, `@components/`)

---

### Libraries YAML

**Définir CSS/JS pour chaque composant** (`ps.libraries.yml`) :

```yaml
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

**Attacher dans template** :

```twig
{{ attach_library('ps/button') }}
```

**Avantages** :
- ✅ CSS/JS chargés seulement si composant utilisé
- ✅ Dépendances gérées automatiquement
- ✅ Agrégation + minification (prod)

---

### Preprocess Functions

**Altérer données avant rendu template** (`ps.theme`) :

```php
<?php
/**
 * Implements hook_preprocess_node().
 */
function ps_preprocess_node(&$variables) {
  $node = $variables['node'];
  
  // Mapper ViewMode → Variante composant
  if ($variables['view_mode'] === 'teaser') {
    $variables['card_variant'] = 'horizontal';
  }
  
  // Ajouter classes BEM
  $variables['attributes']['class'][] = 'ps-node';
  $variables['attributes']['class'][] = 'ps-node--' . $node->bundle();
}
```

**Avantages** :
- ✅ Logique PHP séparée des templates
- ✅ Transformation data Drupal → Props composants
- ✅ Classes BEM automatiques

---

## 🚨 Pièges Courants

### 1. Arrow functions en Twig

```twig
{# ❌ MAUVAIS (fonctionne Storybook, PAS Drupal) #}
{% set classes = classes|filter(v => v) %}

{# ✅ CORRECT (compatible Drupal) #}
{% set classes = [
  'ps-button',
  variant != 'primary' ? 'ps-button--' ~ variant : null,
]|join(' ')|trim %}
```

---

### 2. Méthodes JavaScript en Twig

```twig
{# ❌ MAUVAIS (Storybook only) #}
{% set hasIcon = ['check', 'arrow'].includes(icon) %}

{# ✅ CORRECT (Drupal compatible) #}
{% set hasIcon = icon == 'check' or icon == 'arrow' %}
```

---

### 3. Oublier `only` dans include

```twig
{# ❌ RISQUE SÉCURITÉ (toutes variables passées) #}
{% include '@elements/button/button.twig' with {text: 'Click'} %}

{# ✅ SÉCURISÉ (seulement props explicites) #}
{% include '@elements/button/button.twig' with {text: 'Click'} only %}
```

---

### 4. Cache Drupal non vidé

```bash
# ❌ Modifications CSS/Twig non visibles
# → Cache Drupal garde ancienne version

# ✅ Toujours vider cache après modifs
drush cr

# OU désactiver cache render (dev only)
# sites/default/services.yml:
# parameters:
#   twig.config:
#     debug: true
#     cache: false
```

---

## 📋 Checklist Intégration

### Avant de commencer

- [ ] Drupal 10+ installé
- [ ] Thème PS activé
- [ ] Node.js + npm installés
- [ ] `npm install` exécuté
- [ ] `npm run build` passe

### Configuration

- [ ] Namespaces SDC configurés (ps.info.yml)
- [ ] Libraries définies (ps.libraries.yml)
- [ ] Regions configurées (ps.info.yml)
- [ ] Templates Drupal créés (templates/)

### Composants

- [ ] Composants Storybook fonctionnels
- [ ] Assets compilés (dist/css/, dist/js/)
- [ ] Libraries attachées aux templates
- [ ] Variables Drupal → Props mappées

### Tests

- [ ] Page s'affiche correctement
- [ ] CSS appliqués (inspect elements)
- [ ] JS fonctionnels (interactions)
- [ ] Responsive (mobile, tablet, desktop)
- [ ] Accessibilité (WCAG 2.2 AA)

---

## 🔗 Ressources

### Documentation Drupal

- **Theming Guide** : https://www.drupal.org/docs/theming-drupal
- **Twig in Drupal** : https://www.drupal.org/docs/theming-drupal/twig-in-drupal
- **SDC (Components)** : https://www.drupal.org/docs/develop/theming-drupal/using-single-directory-components
- **Libraries** : https://www.drupal.org/docs/theming-drupal/adding-stylesheets-css-and-javascript-js-to-a-drupal-theme
- **Preprocess** : https://www.drupal.org/docs/theming-drupal/modifying-output-with-preprocess-functions

### Commandes Drush

```bash
drush cr              # Clear cache
drush theme:install ps  # Install theme
drush config:set system.theme default ps  # Set default theme
drush pm:list --type=theme  # List themes
drush watchdog:show   # View logs
```

---

**Navigation** : [← 06 Ressources](../06-ressources/) | [Vue d'ensemble →](./01-vue-ensemble.md)
