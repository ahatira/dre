# Build Assets Strategy

## Overview

This document clarifies which files are **source of authority** vs **generated artifacts** in the `ui_suite_bnppre` theme.

**Golden Rule**: Never edit generated files directly. Always modify source files and rebuild.

---

## Source Files (Version Controlled)

### 1. SCSS Sources → `assets/scss/`

```
assets/scss/
├── styles.scss              → compiles to assets/css/styles.css
├── styles-ps.scss          → compiles to assets/css/styles-ps.css
├── ckeditor5.scss          → compiles to assets/css/ckeditor5.css
├── _bootstrap.scss         (Bootstrap customization)
├── _variables.scss         (BNPPRE design tokens)
├── _fonts.scss             (Font-face declarations)
├── _configuration-import.scss
├── components/             (Global component styles)
│   ├── _accordion.scss
│   ├── _breadcrumb.scss
│   ├── _buttons.scss
│   └── ...
└── form/
    └── form-required.scss  → compiles to assets/css/form/form-required.css
```

**To modify styles**: Edit `.scss` files → run `npm run build:css`

### 2. Component Styles → `components/**/styles/`

```
components/
├── accordion/
│   └── styles/
│       └── accordion.scss  → compiles in place to accordion.css
├── alert/
│   └── styles/
│       └── alert.scss      → compiles in place to alert.css
└── ...
```

**To modify component styles**: Edit `.scss` files → run `npm run build:css`

### 3. Custom JavaScript → `assets/js/`

```
assets/js/
├── accessibility.js        (custom accessibility features)
├── contextual/
│   └── contextual.js
├── layout-builder/
│   └── layout-builder.js
└── ...
```

**These are NOT compiled** — they are source files loaded directly by Drupal.

### 4. BNPPRE Icons Source → `assets/icons/` (raw SVG)

```
assets/icons/
├── ad/
│   ├── air-conditioning.svg
│   └── ...
├── generic/
│   ├── add.svg
│   └── ...
└── ... (12 categories total)
```

**To add/modify icons**:
1. Add raw SVG to appropriate category folder
2. Run `npm run build:bnppre-icons:svgo` → optimize with SVGO
3. Run `npm run build:bnppre-icons` → normalize naming + remove fills
4. Validate: `npm run build:bnppre-icons:check`

---

## Generated Files (NOT Version Controlled)

### 1. Compiled CSS → `assets/css/`

```
assets/css/
├── styles.css              (generated from assets/scss/styles.scss)
├── styles-ps.css           (generated from assets/scss/styles-ps.scss)
├── ckeditor5.css           (generated from assets/scss/ckeditor5.scss)
├── form/
│   └── form-required.css   (generated from assets/scss/form/form-required.scss)
└── ...
```

**⚠️ Never edit**: These files are regenerated on every build.

### 2. Component CSS → `components/**/styles/*.css`

```
components/accordion/styles/accordion.css       (generated from .scss)
components/alert/styles/alert.css              (generated from .scss)
...
```

**⚠️ Never edit**: Compiled from `.scss` in same directory.

### 3. CSS Sourcemaps → `*.css.map`

Generated only in development mode (`npm run dev`) for debugging.

### 4. Copied Bootstrap Assets → `assets/js/bootstrap/`

```
assets/js/bootstrap/
├── bootstrap.min.js        (copied from node_modules/bootstrap)
└── bootstrap.bundle.min.js (copied from node_modules/bootstrap)
```

**Source**: `node_modules/bootstrap/dist/js/`
**Copy script**: `scripts/copy-bootstrap.js`

### 5. node_modules/

**⚠️ Never commit**: 350+ packages (~several hundred MB). Regenerated via `npm install`.

---

## Build Commands

### Full Build (CI/Production)
```bash
npm install          # Install all devDependencies
npm run build        # Run all build steps
```

### Development Workflow
```bash
npm install          # One-time setup
npm run dev          # Watch mode: auto-compile SCSS on save
```

### Individual Build Steps
```bash
npm run build:css                    # Compile all SCSS → CSS
npm run build:bootstrap              # Copy Bootstrap JS
npm run build:icons                  # Copy Bootstrap Icons
npm run build:bnppre-icons:svgo      # Optimize BNPPRE SVGs with SVGO
npm run build:bnppre-icons           # Normalize BNPPRE icon naming/attributes
```

### Validation
```bash
npm run build:bnppre-icons:check     # Validate BNPPRE icons compliance
npm run build:theme-yaml:check       # Validate breakpoint sync
```

---

## Build Tool: Gulp

**Current role**: Orchestrates SCSS compilation with autoprefixer and sourcemaps.

**What it does**:
1. Compile `assets/scss/**/*.scss` → `assets/css/`
2. Compile `components/**/styles/*.scss` → in place
3. Add vendor prefixes (autoprefixer)
4. Generate sourcemaps (dev mode only)
5. Watch for changes (dev mode)

**Why Gulp?** Historical choice. The current tasks are simple enough that Sass CLI + npm scripts could replace it.

**Future consideration**: If build complexity remains low, migrating to direct Sass CLI would reduce dependencies (350 packages → ~50).

---

## .gitignore Strategy

The `.gitignore` file at theme root excludes:
- `node_modules/` — Build tools only, not deployment artifacts
- `assets/css/` — Generated from SCSS sources
- `components/**/styles/*.css` — Generated from SCSS sources

**What IS committed**:
- `package.json` + `package-lock.json` — Dependency manifest
- `assets/scss/` — Source SCSS files
- `components/**/styles/*.scss` — Component SCSS sources
- `assets/js/` — Custom JavaScript (not compiled)
- `assets/icons/` — Processed BNPPRE SVG icons

---

## Troubleshooting

### CSS changes not reflecting?
1. Check you edited `.scss` file, not `.css`
2. Run `npm run build:css`
3. Clear Drupal cache: `drush cr`

### Icons not normalized?
1. Check naming: lowercase, ASCII only, max 30 chars
2. Run full icon pipeline: `npm run build:bnppre-icons:svgo && npm run build:bnppre-icons`
3. Validate: `npm run build:bnppre-icons:check`

### Build fails after git pull?
```bash
npm install  # Dependencies may have changed
npm run build
```

### node_modules accidentally committed?
```bash
git rm -r --cached node_modules
git commit -m "Remove node_modules from version control"
```

---

## Summary

| Directory | Type | Source of Truth | Committed? |
|-----------|------|----------------|-----------|
| `assets/scss/` | SCSS source | ✅ Edit here | ✅ Yes |
| `assets/css/` | Generated CSS | ❌ Never edit | ❌ No (.gitignore) |
| `assets/js/` | Custom JS | ✅ Edit here | ✅ Yes |
| `assets/icons/` | Processed SVG | ✅ After build | ✅ Yes |
| `components/**/styles/*.scss` | SCSS source | ✅ Edit here | ✅ Yes |
| `components/**/styles/*.css` | Generated CSS | ❌ Never edit | ❌ No (.gitignore) |
| `node_modules/` | Dependencies | ❌ Never edit | ❌ No (.gitignore) |
| `package.json` | Manifest | ✅ Edit carefully | ✅ Yes |
| `gulpfile.js` | Build config | ✅ Edit carefully | ✅ Yes |

**Simple rule**: If it's generated by a build command, don't commit it or edit it.
