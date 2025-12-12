# Integration Drupal

**Guides complets pour intégrer PS Theme (Storybook) dans Drupal 10/11**

---

## 🎯 Objectif

Ces guides documentent l'intégration **complète** des composants Storybook PS Theme dans Drupal :
- Mapping composants → templates Drupal
- Configuration libraries CSS/JS
- Form API avec composants PS
- Preprocess hooks (transformation données)
- Déploiement production

---

## 📚 Guides disponibles (6)

### 1. [Vue d'ensemble](./01-vue-ensemble.md) (~450 lignes)

**Architecture et workflow général**

- 🏗️ **Architecture** : Development (Storybook) → Build (Vite) → Production (Drupal)
- 🔄 **Workflow** : 3 phases (development isolé, compilation assets, intégration templates)
- 🧩 **Composants clés** : ps.info.yml (namespaces SDC), ps.libraries.yml (CSS/JS), ps.theme (preprocess), templates/ (Twig)
- 🔗 **Namespaces SDC** : @elements/, @components/, @collections/ → source/patterns/
- 🚨 **Différences Twig** : Arrow functions ❌, JavaScript methods ❌, create_attribute() ✅
- 📊 **Checklist** : Configuration, tests (responsive, a11y, JS fonctionnel)
- 🔧 **Commandes** : npm (watch, build, generate), drush (cr, theme:install, config:set)

**Quand consulter** : Avant de commencer intégration (comprendre architecture globale)

---

### 2. [Templates Drupal](./02-templates.md) (~650 lignes)

**Mapper composants Storybook → Templates Drupal**

- 📋 **18 templates essentiels** : Layout (page, region, block), Navigation (menu, breadcrumb, pager), Content (node, field, taxonomy), Forms (form, form-element, input, select, textarea)
- 🛠️ **5 templates complets** : 
  - **page.html.twig** : Structure page (header, breadcrumb, content, footer)
  - **node.html.twig** : Mapping ViewMode → Card (teaser) ou HTML (full)
  - **form-element.html.twig** : Form API → form-field molecule
  - **menu.html.twig** : Navigation récursive avec icons
  - **breadcrumb.html.twig** : Fil d'Ariane accessible (aria-label, aria-current)
- 📊 **Checklist création** : Identifier composants, mapper variables, attacher libraries, tester
- 🚨 **Pièges courants** : Oublier `only`, `attach_library()`, namespace incorrect, clear cache
- 📋 **Priorités** : P0 (page, form-element, node), P1 (menu, breadcrumb, field), P2 (pager, views, inputs)

**Quand consulter** : Création/modification templates Drupal (override Twig)

---

### 3. [Libraries & Assets](./03-libraries-assets.md) (~600 lignes)

**Configurer CSS/JS dans ps.libraries.yml**

- 📁 **Architecture assets** : source/ → npm build → dist/ → ps.libraries.yml
- 📋 **Anatomie library** : version, css (base/theme), js, dependencies (core/drupal, core/once)
- 🛠️ **4 types libraries** :
  - **Global** : CSS tokens + composants (chargé automatiquement)
  - **Vendors** : JS tiers (A11y Dialog, Swiper) avec dépendances Drupal Core
  - **Composants** : JS par composant (accordion, alert, modal) chargé conditionnellement
  - **CKEditor** : Styles WYSIWYG (headings, links, blockquotes)
- 📊 **2 stratégies** : Global CSS + JS conditionnel (RECOMMANDÉ) vs CSS+JS granulaire (avancé)
- 🚨 **Pièges courants** : Dépendances manquantes, ordre CSS incorrect, chemins absolus, VERSION oublié
- 🎯 **Exemple complet** : ps.libraries.yml production-ready (~90 lignes)

**Quand consulter** : Configuration libraries, ajout composants JS, troubleshooting CSS/JS

---

### 4. [Drupal Forms](./04-drupal-forms.md) (~750 lignes)

**Intégrer Form API avec composants PS Theme**

- 🏗️ **Architecture Form API** : FormBase (PHP) → Render Array → Template Twig → Validation
- 📋 **Mapping complet** : 12 types Form API → Composants PS (textfield→Input, select→Select, textarea→Textarea, checkboxes→Checkboxes, radios→Radios, submit→Button)
- 🛠️ **3 exemples concrets** :
  - **ContactForm** : Formulaire contact immobilier (9 champs, validation téléphone/budget/message, Mail API)
  - **Templates** : form-element.html.twig (mapping → form-field molecule), input.html.twig (atom)
  - **PropertySearchForm** : Recherche biens (7 filtres, validation prix min<max, redirection params)
- 📊 **Form API props** : 3 tableaux (propriétés communes, input, select/textarea/checkboxes/radios)
- 🚨 **Pièges courants** : Oublier #required, validateForm(), setErrorByName(), mapping options select
- 🎯 **Preprocess** : Ajouter data-error + aria-invalid pour états visuels automatiques

**Quand consulter** : Création formulaires Drupal, validation, intégration Form API

---

### 5. [Preprocess Functions](./05-preprocess.md) (~800 lignes)

**Transformer données Drupal → Props composants PS**

- 🏗️ **Architecture** : Entity Drupal → Preprocess Hook → Variables Twig → Template (include composant)
- 📋 **9 hooks essentiels** : page (classes body), node (ViewMode → Card), field (taxonomy → Badges), menu (icons, active trail), block, form, input (validation), breadcrumb, views
- 🛠️ **5 exemples concrets** :
  - **Node → Card** : Mapper teaser (title, description tronquée, image ImageStyle, badge category, eyebrow date, CTA)
  - **Field → Badges** : Transformer taxonomy terms en badges (props array)
  - **Menu → Icons** : Ajouter chevron-down récursivement, classes active trail
  - **Page → Classes** : Body dynamiques (page-front, user-logged-in, page-node--{type})
  - **Input → Validation** : data-error, aria-invalid, aria-describedby automatiques
- 🔧 **4 utilitaires** : Image URL (ImageStyle), date français, premier paragraphe body, term→variant mapping
- 🚨 **Pièges courants** : hasField() oublié, N+1 queries, t() oublié, référence &$variables
- 🎯 **ps.theme complet** : ~150 lignes production-ready (8 hooks)

**Quand consulter** : Transformation données, mapping ViewMode, ajout classes dynamiques

---

### 6. [Déploiement & Production](./06-deploiement.md) (~850 lignes)

**Build production, cache Drupal, optimisations, CI/CD**

- 🏗️ **Workflow** : Development (watch) → Build Production (minify) → Déploiement (CI/CD)
- 📦 **Build production** : Configuration Vite complète (Terser minify, sourcemaps, CSS Lightning, tree shaking)
- ⚡ **4 optimisations assets** :
  - **Compression** : Gzip/Brotli (Apache + Nginx configs, 81% réduction CSS)
  - **Critical CSS** : Above-the-fold inline (FCP -40%)
  - **Lazy loading** : Images natives + width/height (éviter layout shift)
  - **Fonts** : Preload WOFF2 + font-display swap
- 🗄️ **Cache Drupal** : settings.php production (render cache, CSS/JS aggregation, Twig cache), drush commands, cache tags
- 🔍 **Performance** : Core Web Vitals (LCP/FID/CLS < cibles), outils audit (Lighthouse, WebPageTest, GTmetrix)
- 🚀 **CI/CD** : GitHub Actions complet (test → build → deploy SSH → clear cache → Slack notification)
- 📋 **Checklist déploiement** : 6 sections (avant, serveur, Drupal, assets, post-déploiement)
- 🚨 **Troubleshooting** : 4 problèmes courants (CSS 404, styles manquants, JS non fonctionnel, performance dégradée)

**Quand consulter** : Déploiement production, optimisations performances, setup CI/CD, troubleshooting

---

## 🎓 Parcours d'apprentissage

### Débutant (Première intégration Drupal)

1. **[Vue d'ensemble](./01-vue-ensemble.md)** → Comprendre architecture globale
2. **[Templates](./02-templates.md)** → Créer templates P0 (page, form-element, node)
3. **[Libraries](./03-libraries-assets.md)** → Configurer ps.libraries.yml (global + vendors)
4. **Tester** → `npm run build` + `drush cr` + vérifier affichage

### Intermédiaire (Formulaires et données)

1. **[Drupal Forms](./04-drupal-forms.md)** → Créer FormBase avec composants PS
2. **[Preprocess](./05-preprocess.md)** → Mapper données entities → props composants
3. **[Templates](./02-templates.md)** → Templates P1 (menu, breadcrumb, field)
4. **Tester** → Formulaires fonctionnels, nodes affichés correctement

### Avancé (Production et performance)

1. **[Déploiement](./06-deploiement.md)** → Build production optimisé
2. **[Libraries](./03-libraries-assets.md)** → CSS granulaire (si nécessaire)
3. **[Preprocess](./05-preprocess.md)** → Optimiser queries (éviter N+1)
4. **[Déploiement](./06-deploiement.md)** → Setup CI/CD, monitoring performances

---

## 🔍 Guide par tâche

### Créer nouveau composant

1. **Storybook** : Générer pattern (`npm run generate:pattern`)
2. **Templates** : Créer template Drupal (voir [02-templates.md](./02-templates.md))
3. **Libraries** : Ajouter library si JS (voir [03-libraries-assets.md](./03-libraries-assets.md))
4. **Preprocess** : Mapper données si besoin (voir [05-preprocess.md](./05-preprocess.md))

### Créer formulaire

1. **FormBase** : Créer classe PHP (voir [04-drupal-forms.md](./04-drupal-forms.md))
2. **Template** : Override form-element.html.twig (voir [04-drupal-forms.md](./04-drupal-forms.md))
3. **Validation** : Ajouter validateForm() + setErrorByName() (voir [04-drupal-forms.md](./04-drupal-forms.md))
4. **Preprocess** : Ajouter data-error pour états visuels (voir [05-preprocess.md](./05-preprocess.md))

### Optimiser performance

1. **Build** : Configuration Vite optimisée (voir [06-deploiement.md](./06-deploiement.md))
2. **Assets** : Compression Gzip/Brotli + Critical CSS (voir [06-deploiement.md](./06-deploiement.md))
3. **Cache** : settings.php production + cache tags (voir [06-deploiement.md](./06-deploiement.md))
4. **Monitoring** : Lighthouse audit + fix issues (voir [06-deploiement.md](./06-deploiement.md))

### Déployer production

1. **Build** : `npm run build` (voir [06-deploiement.md](./06-deploiement.md))
2. **Serveur** : Configurer Apache/Nginx (voir [06-deploiement.md](./06-deploiement.md))
3. **Drupal** : settings.php + clear cache (voir [06-deploiement.md](./06-deploiement.md))
4. **CI/CD** : GitHub Actions (voir [06-deploiement.md](./06-deploiement.md))

### Débugger problème

| Problème | Consulter | Section |
|----------|-----------|---------|
| CSS/JS 404 | [06-deploiement.md](./06-deploiement.md) | Troubleshooting → CSS/JS non chargés |
| Composant non stylisé | [03-libraries-assets.md](./03-libraries-assets.md) | Pièges courants → Oublier attach_library() |
| Template non utilisé | [02-templates.md](./02-templates.md) | Pièges courants → Clear cache |
| Formulaire erreurs | [04-drupal-forms.md](./04-drupal-forms.md) | Validation + setErrorByName() |
| Données manquantes | [05-preprocess.md](./05-preprocess.md) | Pièges courants → hasField() |
| Performance lente | [06-deploiement.md](./06-deploiement.md) | Troubleshooting → Performance dégradée |

---

## 📊 Métriques documentation

### Couverture intégration Drupal

| Aspect | Statut | Détails |
|--------|--------|---------|
| **Architecture** | ✅ 100% | Vue d'ensemble complète (workflow 3 phases) |
| **Templates** | ✅ 100% | 18 templates documentés (5 exemples complets) |
| **Libraries** | ✅ 100% | 4 types libraries + exemple ps.libraries.yml |
| **Forms** | ✅ 100% | 12 types Form API + 2 FormBase exemples |
| **Preprocess** | ✅ 100% | 9 hooks + 5 exemples + 4 utilitaires |
| **Déploiement** | ✅ 100% | Build + cache + CI/CD + troubleshooting |

### Contenu

- **Total lignes** : ~4,500 lignes (6 guides + README)
- **Exemples code** : 45+ (PHP, Twig, YAML, CSS, JS, Bash, YAML CI/CD)
- **Tableaux** : 20+ (mapping, propriétés, checklist)
- **Diagrammes** : 3 (architecture, workflow, Form API)
- **Checklists** : 6 (templates, libraries, forms, preprocess, déploiement)
- **Troubleshooting** : 8 problèmes + solutions

---

## 🚀 Démarrage rapide

### Prérequis

- Drupal 10 ou 11 installé
- Node.js 18+ installé
- Drush installé (`composer require drush/drush`)
- PS Theme cloné (`git clone ...`)

### Setup (5 minutes)

```bash
# 1. Installer dépendances
cd themes/custom/ps
npm install

# 2. Build assets
npm run build

# 3. Activer thème Drupal
drush theme:install ps
drush config:set system.theme default ps

# 5. Vérifier site
# → http://localhost (ou votre URL Drupal)
```

### Développement

```bash
# Terminal 1 : Watch Storybook (hot reload)
npm run watch
# → http://localhost:6006

# Terminal 2 : Drupal local
# → http://localhost (ou votre URL)

# Après modifications templates/preprocess :
drush cr
```

---

## 🎯 Prochaines étapes

Après avoir consulté ces guides :

1. **[Phase A - Jour 5](../ps-design/INDEX.md)** : Créer templates essentiels (page.html.twig, form-element.html.twig, node.html.twig)
2. **[Phase B](../ps-design/INDEX.md)** : Créer 18 templates Drupal complets + ps.theme avec preprocess
3. **Tests intégration** : Formulaires, nodes, menus, breadcrumb fonctionnels
4. **Optimisations** : Performance (Lighthouse > 90), cache stratégies, CI/CD

---

## 📚 Ressources externes

### Documentation Drupal

- **Theming Guide** : https://www.drupal.org/docs/theming-drupal
- **Twig Templates** : https://www.drupal.org/docs/theming-drupal/twig-in-drupal
- **Form API** : https://api.drupal.org/api/drupal/elements
- **Preprocess Hooks** : https://www.drupal.org/docs/theming-drupal/modifying-variables-in-preprocessors
- **Libraries** : https://www.drupal.org/docs/theming-drupal/adding-stylesheets-css-and-javascript-js-to-a-drupal-theme

### Performance

- **Drupal Performance** : https://www.drupal.org/docs/administering-a-drupal-site/optimizing-drupal-site-performance
- **Core Web Vitals** : https://web.dev/vitals/
- **Lighthouse** : https://developers.google.com/web/tools/lighthouse

### Outils

- **Drush** : https://www.drush.org/
- **Vite** : https://vitejs.dev/
- **PostCSS** : https://postcss.org/

---

## 📝 Maintenance

### Mise à jour guides

Ces guides sont maintenus parallèlement aux composants PS Theme. Lors de modifications :

1. **Nouveau composant** → Mettre à jour [02-templates.md](./02-templates.md) (mapping template)
2. **Nouveau JS** → Mettre à jour [03-libraries-assets.md](./03-libraries-assets.md) (library définition)
3. **Nouveau type Form API** → Mettre à jour [04-drupal-forms.md](./04-drupal-forms.md) (mapping)
4. **Nouveau hook** → Mettre à jour [05-preprocess.md](./05-preprocess.md) (exemple preprocess)
5. **Changement build** → Mettre à jour [06-deploiement.md](./06-deploiement.md) (config Vite)

### Historique

- **2025-12-13** : Création guides intégration Drupal (Phase A - Jour 3-4)
  * 01-vue-ensemble.md (~450 lignes)
  * 02-templates.md (~650 lignes)
  * 03-libraries-assets.md (~600 lignes)
  * 04-drupal-forms.md (~750 lignes)
  * 05-preprocess.md (~800 lignes)
  * 06-deploiement.md (~850 lignes)
  * README.md (~450 lignes)
  * **Total** : ~4,550 lignes

---

**Maintainers** : Design System Team  
**Contact** : Voir [README principal](../../README.md) pour support# 5. Vérifier site
# → http://localhost (ou votre URL Drupal)
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
