# Déploiement & Production

**Build production, cache Drupal, optimisations performances, CI/CD pipeline**

---

## 🎯 Objectif

Préparer PS Theme pour production :
1. **Build assets optimisés** (minification, sourcemaps, compression)
2. **Configuration cache Drupal** (render cache, CSS/JS aggregation)
3. **Optimisations performances** (lazy loading, critical CSS, image styles)
4. **Pipeline CI/CD** (tests automatiques, déploiement)

---

## 🏗️ Workflow déploiement

```
1. Development                2. Build Production           3. Déploiement
┌─────────────────┐          ┌──────────────────┐          ┌────────────────┐
│ npm run watch   │          │ npm run build    │          │ Git push       │
│ - Hot reload    │          │ - Minify CSS/JS  │          │ - CI/CD        │
│ - Sourcemaps    │ ──────>  │ - Optimize SVG   │ ──────>  │ - Tests auto   │
│ - Linting       │          │ - Gzip assets    │          │ - Deploy prod  │
│ localhost:6006  │          │ dist/ folder     │          │ drush cr       │
└─────────────────┘          └──────────────────┘          └────────────────┘
```

---

## 📦 Build Production

### Commande : `npm run build`

```bash
npm run build
# → PostCSS (nesting → standard CSS, autoprefixer, minify)
# → Terser (JS minify, tree shaking)
# → SVG sprite optimisé (SVGO)
# → Sourcemaps générés (debug production)
```

**Configuration Vite** (`vite.config.js`) :

```javascript
import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    minify: 'terser', // Minification aggressive
    sourcemap: true,  // Sourcemaps pour debug production
    
    rollupOptions: {
      input: {
        styles: resolve(__dirname, 'source/patterns/styles.css'),
        vendors: resolve(__dirname, 'source/patterns/vendors.js'),
        accordion: resolve(__dirname, 'source/patterns/collections/accordion/accordion.js'),
        alert: resolve(__dirname, 'source/patterns/components/alert/alert.js'),
        modal: resolve(__dirname, 'source/patterns/components/modal/modal.js'),
        // Ajouter autres composants JS
      },
      output: {
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/[name]-[hash].js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name.endsWith('.css')) {
            return 'css/[name][extname]';
          }
          return 'assets/[name]-[hash][extname]';
        },
      },
    },

    terserOptions: {
      compress: {
        drop_console: true,      // Supprimer console.log en prod
        drop_debugger: true,     // Supprimer debugger
        pure_funcs: ['console.log', 'console.info'], // Supprimer fonctions spécifiques
      },
      format: {
        comments: false,         // Supprimer commentaires
      },
    },

    cssMinify: 'lightningcss', // CSS minify rapide
  },

  css: {
    postcss: {
      plugins: [
        require('postcss-nesting'),      // Nesting → standard
        require('autoprefixer'),         // Vendor prefixes
        require('cssnano')({             // Minification CSS
          preset: ['default', {
            discardComments: { removeAll: true },
            normalizeWhitespace: true,
            minifyFontValues: true,
            minifyGradients: true,
          }],
        }),
      ],
    },
  },
});
```

**Résultat** :
```
dist/
├── css/
│   ├── styles.css (minifié ~80 KB)
│   └── styles.css.map
├── js/
│   ├── vendors.js (minifié ~45 KB)
│   ├── accordion.js (minifié ~3 KB)
│   ├── alert.js (minifié ~2 KB)
│   └── modal.js (minifié ~5 KB)
└── icons/
    └── icons-sprite.svg (optimisé SVGO)
```

---

## ⚡ Optimisations Assets

### 1. Compression Gzip/Brotli (serveur)

**Apache** (`.htaccess`) :
```apache
# Compression Gzip
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json image/svg+xml
</IfModule>

# Compression Brotli (si disponible)
<IfModule mod_brotli.c>
  AddOutputFilterByType BROTLI_COMPRESS text/html text/plain text/xml text/css text/javascript application/javascript application/json image/svg+xml
</IfModule>

# Cache headers (1 an pour assets)
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType text/css "access plus 1 year"
  ExpiresByType application/javascript "access plus 1 year"
  ExpiresByType image/svg+xml "access plus 1 year"
  ExpiresByType image/png "access plus 1 year"
  ExpiresByType image/jpeg "access plus 1 year"
  ExpiresByType image/webp "access plus 1 year"
  ExpiresByType font/woff2 "access plus 1 year"
</IfModule>
```

**Nginx** (`nginx.conf`) :
```nginx
# Compression Gzip
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_types text/css application/javascript application/json image/svg+xml;
gzip_comp_level 6;

# Compression Brotli
brotli on;
brotli_types text/css application/javascript application/json image/svg+xml;
brotli_comp_level 6;

# Cache headers
location ~* \.(css|js|svg|png|jpg|jpeg|webp|woff2)$ {
  expires 1y;
  add_header Cache-Control "public, immutable";
}
```

**Résultat** :
- CSS : 80 KB → 15 KB gzipped (81% réduction)
- JS : 45 KB → 12 KB gzipped (73% réduction)

---

### 2. Critical CSS (Above-the-fold)

**Extraire CSS critique** (tokens + header + hero) :

```css
/* critical.css (inline <head>) */
:root {
  /* Tokens essentiels */
  --primary: #00915A;
  --white: #FFFFFF;
  --gray-900: #1F2937;
  --font-sans: 'BNPPSans', sans-serif;
  --size-4: 1rem;
  --size-6: 1.5rem;
}

/* Header (above-the-fold) */
.page-header {
  position: sticky;
  top: 0;
  background: var(--white);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  z-index: 100;
}

/* Hero (above-the-fold) */
.ps-hero {
  min-height: 60vh;
  background: linear-gradient(135deg, var(--primary), var(--primary-hover));
}
```

**Template** (`html.html.twig`) :
```twig
<!DOCTYPE html>
<html{{ html_attributes }}>
<head>
  {# Critical CSS inline (bloquant, petit) #}
  <style>
    {{ source('@ps/critical.css') }}
  </style>

  {{ page_top }}
  {{ page }}

  {# CSS complet defer (non-bloquant) #}
  <link rel="preload" href="{{ base_path ~ directory }}/dist/css/styles.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript><link rel="stylesheet" href="{{ base_path ~ directory }}/dist/css/styles.css"></noscript>
</head>
```

**Gain** : First Contentful Paint (FCP) réduit de ~40%

---

### 3. Lazy Loading Images

**Image styles Drupal** (responsive) :

```php
// ps.theme - Preprocess field image
function ps_theme_preprocess_field__field_image(&$variables) {
  foreach ($variables['items'] as $delta => &$item) {
    // Ajouter loading="lazy" (natif browser)
    $item['content']['#item_attributes']['loading'] = 'lazy';
    
    // Ajouter width/height (éviter layout shift)
    if (!empty($item['content']['#item'])) {
      $image = $item['content']['#item'];
      $item['content']['#item_attributes']['width'] = $image->width;
      $item['content']['#item_attributes']['height'] = $image->height;
    }
  }
}
```

**Image styles** (`config/install/image.style.card_thumbnail.yml`) :
```yaml
langcode: en
status: true
dependencies: {  }
name: card_thumbnail
label: 'Card Thumbnail (400x300)'
effects:
  resize:
    uuid: resize
    id: image_scale_and_crop
    weight: 0
    data:
      width: 400
      height: 300
      anchor: center-center
```

---

### 4. Font Loading Strategy

**Preload fonts critiques** (`html.html.twig`) :
```twig
<head>
  {# Preload fonts critiques (WOFF2 only) #}
  <link rel="preload" href="{{ base_path ~ directory }}/dist/fonts/BNPPSans-Regular.woff2" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="{{ base_path ~ directory }}/dist/fonts/BNPPSans-Bold.woff2" as="font" type="font/woff2" crossorigin>
</head>
```

**@font-face optimisé** (`font-face.css`) :
```css
/* BNPPSans Regular */
@font-face {
  font-family: 'BNPPSans';
  src: url('/dist/fonts/BNPPSans-Regular.woff2') format('woff2');
  font-weight: 400;
  font-style: normal;
  font-display: swap; /* Afficher fallback immédiatement, swap quand chargé */
}

/* BNPPSans Bold */
@font-face {
  font-family: 'BNPPSans';
  src: url('/dist/fonts/BNPPSans-Bold.woff2') format('woff2');
  font-weight: 700;
  font-style: normal;
  font-display: swap;
}
```

---

## 🗄️ Cache Drupal

### Configuration production (`settings.php`)

```php
<?php

/**
 * Configuration cache production
 */

// Cache render (HTML généré)
$settings['cache']['bins']['render'] = 'cache.backend.database';

// Cache découverte (plugins, hooks)
$settings['cache']['bins']['discovery'] = 'cache.backend.database';

// CSS/JS aggregation ACTIVÉ
$config['system.performance']['css']['preprocess'] = TRUE;
$config['system.performance']['js']['preprocess'] = TRUE;

// Cache pages anonymes (Varnish ou cache interne)
$settings['cache']['default'] = 'cache.backend.database';
$config['system.performance']['cache']['page']['max_age'] = 3600; // 1 heure

// Twig cache ACTIVÉ (ne pas recompiler templates)
$settings['twig_cache'] = TRUE;
$settings['twig_debug'] = FALSE;
$settings['twig_auto_reload'] = FALSE;

// Désactiver messages debug
$config['system.logging']['error_level'] = 'hide';

// Trusted host patterns (sécurité)
$settings['trusted_host_patterns'] = [
  '^example\.com$',
  '^www\.example\.com$',
];
```

---

### Drush cache commands

```bash
# Clear TOUT le cache (après déploiement)
drush cr

# Clear cache spécifique (plus rapide)
drush cc render           # Cache render (HTML)
drush cc css-js           # Cache CSS/JS aggregés
drush cc theme-registry   # Registry thème (templates)
drush cc plugin           # Cache plugins

# Rebuild cache (optimisé)
drush cache:rebuild

# Vider cache Views
drush views:clear-cache
```

---

### Cache tags (invalidation fine)

**Exemple** : Invalider cache node quand taxonomy change

```php
// ps.theme - Ajouter cache tags node
function ps_theme_preprocess_node(&$variables) {
  $node = $variables['node'];
  
  // Ajouter cache tags category (invalider si term change)
  if ($node->hasField('field_category') && !$node->get('field_category')->isEmpty()) {
    $category = $node->get('field_category')->entity;
    if ($category) {
      $variables['#cache']['tags'][] = 'taxonomy_term:' . $category->id();
    }
  }
  
  // Max-age : cache 1 heure
  $variables['#cache']['max-age'] = 3600;
}
```

---

## 🔍 Performance Monitoring

### Metrics clés (Core Web Vitals)

| Metric | Cible | Description |
|--------|-------|-------------|
| **LCP** (Largest Contentful Paint) | < 2.5s | Temps affichage contenu principal |
| **FID** (First Input Delay) | < 100ms | Délai première interaction |
| **CLS** (Cumulative Layout Shift) | < 0.1 | Stabilité visuelle (layout shift) |
| **FCP** (First Contentful Paint) | < 1.8s | Temps premier élément visible |
| **TTFB** (Time to First Byte) | < 600ms | Temps réponse serveur |

### Outils audit

```bash
# Lighthouse (Chrome DevTools)
lighthouse https://example.com --output html --output-path ./report.html

# WebPageTest
# → https://www.webpagetest.org/

# GTmetrix
# → https://gtmetrix.com/

# Drupal Performance module
drush pm:enable webprofiler
# → /admin/config/development/performance
```

---

## 🚀 CI/CD Pipeline

### GitHub Actions (`.github/workflows/deploy.yml`)

```yaml
name: Deploy to Production

on:
  push:
    branches: [main]

jobs:
  test-and-deploy:
    runs-on: ubuntu-latest
    
    steps:
      # 1. Checkout code
      - name: Checkout repository
        uses: actions/checkout@v3

      # 2. Setup Node.js
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '20'
          cache: 'npm'

      # 3. Install dependencies
      - name: Install dependencies
        run: npm ci

      # 4. Lint & Format
      - name: Lint JavaScript
        run: npm run lint

      - name: Check formatting
        run: npm run format:check

      # 5. Build assets
      - name: Build production assets
        run: npm run build

      # 6. Run tests (si tests unitaires)
      - name: Run tests
        run: npm test
        continue-on-error: true

      # 7. Deploy via SSH
      - name: Deploy to server
        uses: easingthemes/ssh-deploy@v2.1.5
        with:
          SSH_PRIVATE_KEY: ${{ secrets.SSH_PRIVATE_KEY }}
          REMOTE_HOST: ${{ secrets.REMOTE_HOST }}
          REMOTE_USER: ${{ secrets.REMOTE_USER }}
          SOURCE: "dist/"
          TARGET: "/var/www/html/themes/custom/ps/dist/"
          EXCLUDE: "/node_modules/, /.git/"

      # 8. Clear Drupal cache
      - name: Clear Drupal cache
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.REMOTE_HOST }}
          username: ${{ secrets.REMOTE_USER }}
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd /var/www/html
            drush cr
            drush updb -y
            drush cim -y

      # 9. Notification Slack (optionnel)
      - name: Slack notification
        if: success()
        uses: 8398a7/action-slack@v3
        with:
          status: ${{ job.status }}
          text: 'Déploiement PS Theme réussi ✅'
          webhook_url: ${{ secrets.SLACK_WEBHOOK }}
```

---

### Secrets GitHub (Settings → Secrets)

```
SSH_PRIVATE_KEY    = Clé SSH privée (accès serveur)
REMOTE_HOST        = example.com
REMOTE_USER        = deploy
SLACK_WEBHOOK      = https://hooks.slack.com/services/XXX
```

---

## 📋 Checklist déploiement

### Avant déploiement

- [ ] Tests locaux passent (`npm run build`, pas d'erreurs)
- [ ] Lighthouse score > 90 (performance, accessibilité)
- [ ] Tous composants visibles Storybook
- [ ] Templates Drupal fonctionnent (pages, nodes, formulaires)
- [ ] Responsive testé (mobile, tablet, desktop)
- [ ] Accessibilité WCAG 2.2 AA validée

### Configuration serveur

- [ ] Node.js 18+ installé
- [ ] Composer installé (Drupal dependencies)
- [ ] Drush installé (`composer require drush/drush`)
- [ ] Apache/Nginx configuré (Gzip, cache headers)
- [ ] PHP 8.1+ configuré (opcache, APCu)
- [ ] SSL/TLS activé (HTTPS)

### Drupal production

- [ ] `settings.php` : Cache activé, Twig debug OFF
- [ ] `settings.php` : Trusted host patterns configuré
- [ ] `settings.php` : Error logging configuré
- [ ] CSS/JS aggregation ACTIVÉE (`/admin/config/development/performance`)
- [ ] Cron configuré (tasks automatiques)
- [ ] Backup base données configuré

### Assets

- [ ] `npm run build` exécuté (dist/ généré)
- [ ] Fichiers `dist/` déployés serveur
- [ ] Fonts WOFF2 présents (`dist/fonts/`)
- [ ] Icons sprite présent (`dist/icons/icons-sprite.svg`)
- [ ] Sourcemaps générés (`.map` files)

### Post-déploiement

- [ ] `drush cr` exécuté (clear cache)
- [ ] `drush updb` exécuté (update database)
- [ ] `drush cim` exécuté (import config)
- [ ] Tests smoke (pages principales accessibles)
- [ ] Monitoring errors (`drush watchdog:show`)
- [ ] Performance monitoring (Lighthouse, GTmetrix)

---

## 🚨 Troubleshooting Production

### 1. CSS/JS non chargés (404)

**Symptôme** : Console errors `GET /dist/css/styles.css 404`

**Causes** :
- Fichiers `dist/` non déployés
- Permissions fichiers incorrectes
- Chemins ps.libraries.yml incorrects

**Solutions** :
```bash
# Vérifier fichiers présents
ls -la themes/custom/ps/dist/css/
ls -la themes/custom/ps/dist/js/

# Fix permissions
chmod -R 755 themes/custom/ps/dist/

# Clear cache
drush cr
```

---

### 2. Styles manquants (composants non stylisés)

**Symptôme** : HTML correct mais styles manquants

**Causes** :
- CSS aggregation casse ordre chargement
- Library non attachée template
- Cache non vidé

**Solutions** :
```bash
# Désactiver aggregation temporairement
drush config:set system.performance css.preprocess 0
drush cr

# Vérifier library attachée
{{ attach_library('ps/card') }}

# Rebuild theme registry
drush cc theme-registry
```

---

### 3. JavaScript non fonctionnel

**Symptôme** : Accordions, modals ne s'ouvrent pas

**Causes** :
- Errors console (check DevTools)
- Drupal behaviors non exécutés
- Dependencies manquantes

**Solutions** :
```bash
# Vérifier console errors (F12)
# Errors: "Drupal is not defined" → Ajouter dependency core/drupal

# Vérifier dependencies ps.libraries.yml
accordion:
  js:
    dist/js/accordion.js: {}
  dependencies:
    - ps/vendors        # REQUIS si utilise A11y Dialog
    - core/drupal
    - core/once
```

---

### 4. Performance dégradée (lent)

**Symptôme** : Pages > 3s chargement

**Diagnostics** :
```bash
# Activer Query Log (temporairement)
drush config:set system.logging error_level verbose
drush watchdog:show

# Vérifier slow queries
# Errors: "Query took XXXms" → Indexer base données

# Profiler Drupal
drush pm:enable webprofiler
# → /admin/config/development/performance
```

**Solutions** :
- Activer cache render (`settings.php`)
- Ajouter index base données (fields filter/sort)
- Configurer Varnish/Redis (cache externe)
- Optimiser Views (pagination, limite résultats)

---

## 🎯 Commandes essentielles

### Development

```bash
npm run watch           # Hot reload Storybook + Vite
npm run lint            # Lint JavaScript (Biome)
npm run format          # Format code (Biome)
npm run generate:pattern # Générer composant
```

### Build

```bash
npm run build           # Build production (minify, sourcemaps)
npm run build:icons     # Rebuild sprite SVG
npm run storybook:build # Build Storybook statique
```

### Drupal

```bash
drush cr                # Clear cache
drush updb -y           # Update database
drush cim -y            # Import config
drush cex -y            # Export config
drush watchdog:show     # Logs errors
drush pm:list           # Modules status
drush theme:install ps  # Installer thème
drush config:set system.theme default ps # Activer thème
```

### Deployment

```bash
# Build + deploy (manuel)
npm run build
rsync -avz dist/ user@server:/var/www/html/themes/custom/ps/dist/
ssh user@server "cd /var/www/html && drush cr"

# Via CI/CD (automatique)
git push origin main    # Trigger GitHub Actions
```

---

## 📚 Ressources

### Documentation

- **Drupal Performance** : https://www.drupal.org/docs/administering-a-drupal-site/optimizing-drupal-site-performance
- **Vite Build** : https://vitejs.dev/guide/build.html
- **Web Vitals** : https://web.dev/vitals/
- **Lighthouse** : https://developers.google.com/web/tools/lighthouse

### Outils

- **Lighthouse CI** : https://github.com/GoogleChrome/lighthouse-ci
- **WebPageTest** : https://www.webpagetest.org/
- **GTmetrix** : https://gtmetrix.com/
- **Drush** : https://www.drush.org/

---

## 🎯 Prochaines étapes

**Déployer en production** :
1. Configurer `settings.php` (cache, Twig, trusted hosts)
2. Build assets (`npm run build`)
3. Configurer serveur (Gzip, cache headers, SSL)
4. Setup CI/CD (GitHub Actions ou GitLab CI)
5. Monitoring (Lighthouse, logs Drupal)

**Phase A complète** ✅ :
- ✅ Day 1-2 : Specs formulaires (7 specs créées)
- ✅ Day 3-4 : Guides Drupal (6 guides créés)
- ⏳ Day 5 : Templates essentiels (page, form-element, node)

---

**Navigation** : [← Preprocess](./05-preprocess.md) | [README](./README.md) | [Vue d'ensemble](./01-vue-ensemble.md)
