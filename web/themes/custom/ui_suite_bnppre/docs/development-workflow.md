# Development Workflow

---

## Prerequisites

| Tool | Required version | Check |
|------|-----------------|-------|
| PHP | ^8.2 | `php -v` |
| Composer | ^2 | `composer -V` |
| Node.js | ^18 | `node -v` |
| npm | ^9 | `npm -v` |
| Drush | ^13 | `vendor/bin/drush --version` |

---

## First Install

If you want a fresh local Drupal site from the project root, you can run `make install-site` first. This helper installs Drupal, enables the project's modules and themes, adds the configured site languages, and rebuilds caches. It does not import demo content or create menus.

```bash
# 1. Project root — PHP dependencies
composer install

# 2. Optional: fresh local Drupal install with project modules/themes
make install-site

# 3. Theme directory — Node dependencies
cd web/themes/custom/ui_suite_bnppre
npm install

# 4. Full asset build
npm run build

# 5. If you did not use make install-site, enable theme manually
vendor/bin/drush theme:enable ui_suite_bnppre

# 6. Project root — clear caches
vendor/bin/drush cr
```

---

## Build Commands

All commands run from the **theme directory** (`web/themes/custom/ui_suite_bnppre`).

### Full Build

```bash
npm run build
```

Runs in order: `build:css` → `build:bootstrap` → `build:icons` → `build:bnppre-icons:svgo` → `build:bnppre-icons`.

Use this for CI, production deployments, and after `git pull`.

### Individual Steps

| Command | What it does | When to use |
|---------|-------------|-------------|
| `npm run build:css` | Compile all SCSS → CSS (Gulp + autoprefixer) | After editing any `.scss` file |
| `npm run build:bootstrap` | Copy Bootstrap JS from `node_modules` to `assets/js/bootstrap/` | After updating `bootstrap` npm package |
| `npm run build:icons` | Copy Bootstrap Icons SVGs to `web/libraries/bootstrap-icons/icons/` | After updating `bootstrap-icons` npm package |
| `npm run build:bnppre-icons:svgo` | Optimize BNPPRE SVGs with SVGO (reduces size, removes metadata) | After adding raw SVGs to `assets/icons/` |
| `npm run build:bnppre-icons` | Normalize BNPPRE icon filenames + remove hardcoded fills | After SVGO step |

### Development (Watch Mode)

```bash
npm run dev
# or
npm run watch
```

Starts Gulp in watch mode: SCSS files are recompiled automatically on save. Does not rebuild icons or copy Bootstrap assets.

### Validation

```bash
npm run build:bnppre-icons:check   # Validate icon naming + SVG compliance
npm run build:theme-yaml:check     # Validate .info.yml / breakpoints sync
```

Run both before committing icon or metadata changes.

---

## SCSS Architecture

### Source → Output Map

```
assets/scss/
├── styles.scss          → assets/css/styles.css         (Realestate variant)
├── styles-ps.scss       → assets/css/styles-ps.css      (Property Search variant)
├── ckeditor5.scss       → assets/css/ckeditor5.css
└── form/
    └── form-required.scss → assets/css/form/form-required.css

components/{name}/styles/
└── {name}.scss          → components/{name}/styles/{name}.css
```

### Import Layer Order (within styles.scss)

```scss
// 1. Design tokens — override Bootstrap variables BEFORE import
@import 'variables';          // BNPPRE color palette, spacing, fonts
@import 'configuration-import';

// 2. Bootstrap — compiled with BNPPRE token overrides
@import 'bootstrap';          // Full Bootstrap 5 SCSS

// 3. Fonts
@import 'fonts';              // BNPPSans + OpenSans @font-face

// 4. Component overrides — additive, Bootstrap-first
@import 'components/brand-foundations';
@import 'components/breadcrumb';
@import 'components/buttons';
@import 'components/header';
@import 'components/typography';
@import 'components/accordion';
```

**Rule**: Always define token overrides in `_variables.scss` before the Bootstrap import. Never add rules that duplicate what a Bootstrap utility already provides.

### Key SCSS Files

| File | Purpose |
|------|---------|
| `_variables.scss` | BNP Paribas design tokens: colors, fonts, spacing, breakpoints |
| `_bootstrap.scss` | Bootstrap entry point — all Bootstrap SCSS partials |
| `_configuration-import.scss` | Shared configuration imported before both variants |
| `_fonts.scss` | `@font-face` declarations for BNPPSans and OpenSans |
| `components/_brand-foundations.scss` | Core brand color utilities and custom properties |
| `components/_header.scss` | Header layout, sticky behavior, mega-menu styles |
| `components/_buttons.scss` | Bootstrap button variant extensions |

---

## Generated vs Source Files

**Golden rule**: never edit generated files. They are overwritten on every build.

| Path | Type | Rule |
|------|------|------|
| `assets/scss/**` | Source | ✅ Edit here |
| `assets/js/**` | Source | ✅ Edit here (not compiled) |
| `assets/icons/**` | Source | ✅ Edit here (after build pipeline) |
| `assets/fonts/**` | Static asset | ✅ Committed |
| `assets/css/**` | Generated | ❌ Never edit |
| `components/**/styles/*.css` | Generated | ❌ Never edit |
| `assets/js/bootstrap/**` | Copied | ❌ Never edit (update via npm) |
| `node_modules/` | Dependencies | ❌ Not committed |

---

## After Making Changes

| Change type | Command |
|------------|---------|
| SCSS change | `npm run build:css` |
| PHP hook change | `vendor/bin/drush cr` |
| `.info.yml` / `.libraries.yml` | `vendor/bin/drush cr` |
| Twig template change | `vendor/bin/drush cr` (if Twig cache enabled) |
| Component `.component.yml` | `vendor/bin/drush cr` |
| SVG icon added | `npm run build:bnppre-icons` then `vendor/bin/drush cr` |
| `composer.json` change | `composer install` then `vendor/bin/drush cr` |

---

## Gulp Configuration

**File**: `gulpfile.js`

**Tasks**:
- `default` (watch mode): watches `assets/scss/**/*.scss` and `components/**/styles/*.scss`
- `production` (called by `npm run build:css`): compiles without sourcemaps, with autoprefixer

**Bootstrap Sass deprecation warnings** are silenced in `gulpfile.js` via `sassOptions.quietDeps` and `silenceDeprecations`. This is a known Dart Sass / Bootstrap 5.x transitional issue.

---

## Troubleshooting

### CSS changes not visible

1. Verify you edited a `.scss` file, not the `.css` output.
2. Run `npm run build:css`.
3. Run `vendor/bin/drush cr` (cache may serve stale aggregated CSS).
4. Hard-refresh the browser (`Ctrl+Shift+R`).

### `npm run build` fails with Sass error

```bash
# Check which file has the error
npm run build:css 2>&1 | grep "Error"
```

Common cause: missing Bootstrap variable reference — ensure `_variables.scss` overrides come before `@import 'bootstrap'`.

### Icons not appearing after adding SVGs

1. Confirm SVG is placed in `assets/icons/{category}/`.
2. Run `npm run build:bnppre-icons`.
3. Run `vendor/bin/drush cr`.
4. Check UI Icons admin: `/admin/appearance/ui/icons`.
5. Validate: `npm run build:bnppre-icons:check`.

### Cache not clearing

```bash
# Force full rebuild
vendor/bin/drush cache:rebuild
# Equivalently
vendor/bin/drush cr
```

### Windows path issue with UI Icons previews

The core `IconFinder.php` treats Windows drive paths (`C:\...`) as URL schemes. A local patch exists at `web/core/lib/Drupal/Core/Theme/Icon/IconFinder.php` — do not revert it when upgrading core without checking compatibility.
