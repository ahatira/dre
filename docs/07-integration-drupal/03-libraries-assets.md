# Libraries & Assets

**Configurer CSS/JS** dans `ps.libraries.yml` pour charger composants Storybook dans Drupal

---

## 🎯 Objectif

Définir libraries Drupal (CSS + JS) pour :
1. **Charger assets compilés** (Vite → `dist/`)
2. **Gérer dépendances** (Drupal core, jQuery, vendors)
3. **Attacher libraries par composant** (modulaire vs global)

---

## 📁 Architecture assets

```
ps_theme/
├── source/                    # Sources Storybook
│   ├── patterns/              # Composants (.twig, .css, .jsx)
│   │   ├── elements/
│   │   ├── components/
│   │   └── collections/
│   └── props/                 # Tokens CSS
│       ├── colors.css
│       ├── sizes.css
│       └── index.css
│
├── dist/                      # Assets compilés (npm run build)
│   ├── css/
│   │   ├── styles.css         # CSS global (tous tokens + composants)
│   │   └── styles.css.map     # Sourcemap debug
│   ├── js/
│   │   ├── vendors/
│   │   │   └── vendors.js     # Vendors (A11y Dialog, etc.)
│   │   ├── accordion.js       # JS par composant
│   │   ├── alert.js
│   │   └── modal.js
│   └── icons/
│       └── icons-sprite.svg   # Sprite SVG
│
└── ps.libraries.yml           # Définitions libraries Drupal
```

**Workflow** :
1. **Development** : `npm run watch` → Hot reload Storybook
2. **Build** : `npm run build` → Vite compile `source/` → `dist/`
3. **Drupal** : `ps.libraries.yml` référence fichiers `dist/`

---

## 📋 Anatomie library Drupal

### Structure base

```yaml
nom_library:
  version: VERSION
  css:
    theme:                     # Ordre chargement (base < layout < component < state < theme)
      dist/css/styles.css: {}  # Chemin relatif thème
  js:
    dist/js/script.js: {}
  dependencies:                # Libraries requises (chargées AVANT)
    - core/drupal
    - core/once
```

**Ordres CSS** (du moins au plus spécifique) :
- **base** : Resets, normalize, typography
- **layout** : Grilles, containers
- **component** : Composants UI
- **state** : États interactifs (hover, focus)
- **theme** : Surcharges thème

**Version** :
- `VERSION` : Utilise version thème (`ps.info.yml`)
- `1.x` : Version spécifique (cache busting)

---

## 🛠️ Library 1 : Global (CSS tokens + base)

### `ps.libraries.yml`

```yaml
# Library globale (chargée sur TOUTES les pages)
global:
  version: VERSION
  css:
    base:
      dist/css/styles.css: {}  # Tous tokens + composants
  dependencies:
    - core/normalize          # Reset CSS Drupal
```

**Contenu `dist/css/styles.css`** (compilé par Vite) :
- Tokens CSS (`source/props/index.css`) : colors, sizes, fonts, shadows, animations
- CSS composants (`source/patterns/**/*.css`) : button, badge, card, etc.

**Attachement automatique** (configuré `ps.info.yml`) :
```yaml
libraries:
  - ps/global                 # Chargée automatiquement toutes pages
```

**Avantages** :
- ✅ Tous tokens disponibles partout
- ✅ Un seul fichier CSS (HTTP/2 efficient)
- ✅ Gzip compresse répétitions tokens

**Inconvénients** :
- ⚠️ CSS non utilisé chargé (acceptable < 100 KB)

---

## 🛠️ Library 2 : Vendors (Dépendances tierces)

### `ps.libraries.yml`

```yaml
# Vendors : Librairies JavaScript tierces
vendors:
  version: VERSION
  js:
    dist/js/vendors/vendors.js: {}
  dependencies:
    - core/drupal             # Drupal global object
    - core/drupalSettings     # drupalSettings object
    - core/once               # once() utility (idempotence)
```

**Contenu `dist/js/vendors/vendors.js`** :
- **A11y Dialog** : Modals accessibles
- **Swiper** : Carousels tactiles
- **GLightbox** : Lightbox images

**Dépendances Drupal Core** :
- `core/drupal` : `window.Drupal` object (behaviors, t(), formatPlural())
- `core/drupalSettings` : `window.drupalSettings` (config PHP → JS)
- `core/once` : `once()` utility (évite initialisation multiple même élément)

---

## 🛠️ Library 3 : Composant (JS interactif)

### Pattern : 1 library = 1 composant JS

```yaml
# Accordion (toggle panels)
accordion:
  version: VERSION
  js:
    dist/js/accordion.js: {}
  dependencies:
    - ps/vendors              # Require vendors.js AVANT accordion.js

# Alert (dismiss messages)
alert:
  version: VERSION
  js:
    dist/js/alert.js: {}
  dependencies:
    - core/drupal
    - core/once

# Modal (dialog overlay)
modal:
  version: VERSION
  js:
    dist/js/modal.js: {}
  dependencies:
    - ps/vendors              # A11y Dialog
    - core/drupal
    - core/once
```

**Attachement conditionnel** (template Twig) :
```twig
{# Attacher library seulement si composant utilisé #}
{% if has_accordion %}
  {{ attach_library('ps/accordion') }}
{% endif %}

{# Ou attacher dans render array (preprocess) #}
$variables['#attached']['library'][] = 'ps/accordion';
```

---

## 🛠️ Library 4 : CKEditor (Styles WYSIWYG)

### `ps.libraries.yml`

```yaml
# CKEditor : Styles éditeur texte riche
ckeditor:
  version: VERSION
  css:
    theme:
      dist/css/ckeditor.css: {}
```

**Configuration `ps.info.yml`** :
```yaml
ckeditor5-stylesheets:
  - dist/css/global.css       # Tokens + base (déjà existant)
  - dist/css/ckeditor.css     # Styles éditeur (à créer)
```

**Styles éditeur** (`source/patterns/ckeditor.css`) :
```css
/* Headings CKEditor */
.ck-content h2 { font-size: var(--font-size-2xl); }
.ck-content h3 { font-size: var(--font-size-xl); }

/* Links CKEditor */
.ck-content a {
  color: var(--primary);
  text-decoration: underline;
}

/* Lists CKEditor */
.ck-content ul { list-style: disc; }
.ck-content ol { list-style: decimal; }

/* Blockquotes CKEditor */
.ck-content blockquote {
  border-left: 4px solid var(--primary);
  padding-left: var(--size-4);
}
```

---

## 📊 Stratégies chargement

### Stratégie 1 : Global CSS + JS conditionnel (RECOMMANDÉ)

**CSS** : Un fichier global (`styles.css` ~50-100 KB gzipped)  
**JS** : Libraries par composant (chargées si utilisées)

```yaml
global:
  css:
    base:
      dist/css/styles.css: {}  # TOUS composants (HTTP/2 efficient)

accordion:
  js:
    dist/js/accordion.js: {}   # SEULEMENT si accordion présent
```

**Avantages** :
- ✅ CSS prévisible (tous tokens disponibles)
- ✅ JS optimisé (chargé à la demande)
- ✅ Maintenance simple (un seul CSS)

**Inconvénients** :
- ⚠️ CSS non utilisé chargé (mineur avec HTTP/2 + Gzip)

---

### Stratégie 2 : CSS + JS par composant (Avancé)

**CSS + JS** : Libraries granulaires (chargées si utilisées)

```yaml
button:
  css:
    component:
      dist/css/button.css: {}
  # Pas de JS (composant statique)

accordion:
  css:
    component:
      dist/css/accordion.css: {}
  js:
    dist/js/accordion.js: {}
```

**Avantages** :
- ✅ Taille minimale par page (seulement composants utilisés)

**Inconvénients** :
- ⚠️ Complexité maintenance (80+ libraries)
- ⚠️ Requêtes HTTP multiples (mitigé HTTP/2)
- ⚠️ Tokens dupliqués (chaque CSS doit inclure tokens utilisés)

**Quand utiliser** :
- Site haute performance (LCP critique)
- Composants très lourds isolés (> 50 KB)

---

## 🔧 Commandes build

### Development (hot reload)

```bash
npm run watch
# → Vite watch mode + Storybook http://localhost:6006
# → Modifications source/ compilées automatiquement vers dist/
```

### Production (minifié + sourcemaps)

```bash
npm run build
# → PostCSS (nesting, autoprefixer, minify)
# → Terser (JS minify)
# → dist/css/styles.css (minifié)
# → dist/css/styles.css.map (sourcemap debug)
```

### Clear cache Drupal

```bash
drush cr
# → Vider cache Drupal (découvrir nouvelles libraries)
# → OBLIGATOIRE après modif ps.libraries.yml
```

---

## 🚨 Pièges courants

### 1. Oublier dépendances

```yaml
# ❌ accordion.js utilise vendors (A11y Dialog) mais pas déclaré
accordion:
  js:
    dist/js/accordion.js: {}
  # MANQUE dependencies: - ps/vendors

# ✅ Déclarer dépendance (vendors chargé AVANT accordion)
accordion:
  js:
    dist/js/accordion.js: {}
  dependencies:
    - ps/vendors
```

**Résultat erreur** : `Uncaught ReferenceError: A11yDialog is not defined`

---

### 2. Ordre CSS incorrect

```yaml
# ❌ CSS composant en "base" (priorité basse)
button:
  css:
    base:                        # Ordre trop bas
      dist/css/button.css: {}

# ✅ CSS composant en "theme" (priorité haute)
button:
  css:
    theme:                       # Ordre correct
      dist/css/button.css: {}
```

**Résultat erreur** : Styles button surchargés par CSS Drupal core

---

### 3. Chemins absolus (erreur)

```yaml
# ❌ Chemin absolu (ne fonctionne pas)
global:
  css:
    base:
      /themes/custom/ps/dist/css/styles.css: {}

# ✅ Chemin relatif racine thème
global:
  css:
    base:
      dist/css/styles.css: {}
```

---

### 4. Oublier VERSION

```yaml
# ❌ Pas de version (cache navigateur non invalidé)
global:
  css:
    base:
      dist/css/styles.css: {}

# ✅ Version thème (cache invalidé automatiquement)
global:
  version: VERSION              # Lit version ps.info.yml
  css:
    base:
      dist/css/styles.css: {}
```

**Résultat erreur** : Modifications CSS non visibles (cache navigateur)

---

## 📋 Checklist configuration

### Fichier `ps.libraries.yml`

- [ ] Library `global` définie (CSS base)
- [ ] Library `vendors` définie (JS tiers)
- [ ] Libraries composants JS définis (accordion, alert, modal)
- [ ] `version: VERSION` sur chaque library
- [ ] Dépendances `core/drupal`, `core/once` déclarées
- [ ] Ordre CSS correct (`base` < `theme`)

### Fichier `ps.info.yml`

- [ ] `libraries: [ps/global]` (chargement automatique)
- [ ] `ckeditor5-stylesheets` configuré (global.css + ckeditor.css)

### Build & Cache

- [ ] `npm run build` sans erreur
- [ ] Fichiers `dist/css/styles.css` + `dist/js/*.js` générés
- [ ] `drush cr` après modification libraries
- [ ] CSS chargé (inspecter Network tab)
- [ ] JS fonctionne (tester interactions)

---

## 🎯 Exemple complet : ps.libraries.yml

```yaml
# ============================================
# GLOBAL (CSS base - chargé automatiquement)
# ============================================
global:
  version: VERSION
  css:
    base:
      dist/css/styles.css: {}
  dependencies:
    - core/normalize

# ============================================
# VENDORS (JS tiers - A11y Dialog, Swiper)
# ============================================
vendors:
  version: VERSION
  js:
    dist/js/vendors/vendors.js: {}
  dependencies:
    - core/drupal
    - core/drupalSettings
    - core/once

# ============================================
# COMPOSANTS JS (chargés conditionnellement)
# ============================================

# Accordion (toggle panels)
accordion:
  version: VERSION
  js:
    dist/js/accordion.js: {}
  dependencies:
    - ps/vendors
    - core/drupal
    - core/once

# Alert (dismiss messages)
alert:
  version: VERSION
  js:
    dist/js/alert.js: {}
  dependencies:
    - core/drupal
    - core/once

# Modal (dialog overlay)
modal:
  version: VERSION
  js:
    dist/js/modal.js: {}
  dependencies:
    - ps/vendors
    - core/drupal
    - core/once

# Tabs (switch panels)
tabs:
  version: VERSION
  js:
    dist/js/tabs.js: {}
  dependencies:
    - core/drupal
    - core/once

# Carousel (image slider)
carousel:
  version: VERSION
  js:
    dist/js/carousel.js: {}
  dependencies:
    - ps/vendors              # Swiper
    - core/drupal
    - core/once

# ============================================
# CKEDITOR (Styles éditeur WYSIWYG)
# ============================================
ckeditor:
  version: VERSION
  css:
    theme:
      dist/css/ckeditor.css: {}
```

---

## 🎯 Prochaines étapes

**Configurer libraries** :
1. Copier exemple ci-dessus dans `ps.libraries.yml`
2. Adapter selon composants JS existants (`dist/js/`)
3. Clear cache (`drush cr`)
4. Tester chargement (Network tab DevTools)

**Poursuivre avec** :
- **[Drupal Forms](./04-drupal-forms.md)** → Intégrer Form API
- **[Preprocess](./05-preprocess.md)** → Transformer données

---

**Navigation** : [← Templates](./02-templates.md) | [README](./README.md) | [Forms →](./04-drupal-forms.md)
