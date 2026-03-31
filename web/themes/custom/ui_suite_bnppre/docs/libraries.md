# Library System

> This document covers: how libraries are defined, how the dynamic variant system works, and the full audit of all `libraries-override` entries with their risk levels.

---

## Library Architecture

### Definition File

All theme libraries are declared in `ui_suite_bnppre.libraries.yml`. Key libraries:

| Library | Purpose |
|---------|---------|
| `framework` | Entry point — its dependencies are populated at runtime by `LibraryInfoAlter` |
| `bootstrap` | Bootstrap CSS utilities (form-text) — always loaded |
| `accessibility` | Custom skip-link + focus management JS |
| `component_placeholder` | Placeholder CSS for Layout Builder empty regions |
| `form` | Required form base library |
| `framework_css_bnppre` | CSS variant: Realestate (`assets/css/styles.css`) |
| `framework_css_bnppre_ps` | CSS variant: Property Search (`assets/css/styles-ps.css`) |
| `framework_js` | Bootstrap JS bundle (`assets/js/bootstrap/bootstrap.bundle.min.js`) |
| `drupal.*` | Theme replacements for Drupal core JS libraries |

### Attached in `info.yml`

The following libraries are globally attached to every page:

```yaml
libraries:
  - ui_suite_bnppre/bootstrap
  - ui_suite_bnppre/accessibility
  - ui_suite_bnppre/component_placeholder
  - ui_suite_bnppre/form
```

The `framework` library (which loads Bootstrap CSS + JS) is dynamically injected via `LibraryInfoAlter`.

---

## Dynamic Library Loading (Variant System)

`src/Hook/LibraryInfoAlter.php` intercepts the `framework` library at runtime and adds the selected variant as a dependency:

```php
// Reads from theme settings (stored in config)
$css_library = $this->themeSettings->getSetting('library.css_loading')
    ?? 'ui_suite_bnppre/framework_css_bnppre';  // default: Realestate

$libraries['framework']['dependencies'][] = $css_library;
```

**Admin path**: `Appearance → Settings → UI Suite BNP PRE → Stylesheet`

| Setting value | CSS loaded |
|--------------|-----------|
| `ui_suite_bnppre/framework_css_bnppre` | `assets/css/styles.css` (Realestate) |
| `ui_suite_bnppre/framework_css_bnppre_ps` | `assets/css/styles-ps.css` (Property Search) |
| _(empty)_ | Nothing — child theme must inject its own CSS |

The same mechanism applies to the JS variant (`library.js_loading`).

---

## `libraries-extend` Entries

The theme uses `libraries-extend` to **add** to existing libraries without replacing them. These are defined in `ui_suite_bnppre.info.yml` alongside the overrides.

Key extends:
- `core/drupal.active-link` → adds `active-link-trail.js` (tracking active trail)
- `core/drupal.autocomplete` → adds `drupal.autocomplete` custom library
- `core/drupal.dialog.off_canvas` → adds `drupal.dialog.off_canvas` custom library
- `layout_builder/drupal.layout_builder` → adds `drupal.layout_builder_block_filter`

---

## `libraries-override` — Full Audit

**Summary**: 20 override entries in `ui_suite_bnppre.info.yml`.

| # | Library | Type | Risk |
|---|---------|------|------|
| 1 | `core/drupal.active-link` | JS replacement | 🔴 High |
| 2 | `text/drupal.text` | Full lib replacement | 🔴 High |
| 3 | `core/drupal.dropbutton` | Fully disabled | 🟡 Medium |
| 4 | `layout_builder_browser/modal` | JS replacement | 🟡 Medium |
| 5 | `core/drupal.autocomplete` | CSS disabled | 🟢 Low |
| 6 | `core/drupal.dialog.off_canvas` | 13 CSS disabled | 🟢 Low |
| 7 | `core/drupal.tableheader` | Fully disabled | 🟢 Low |
| 8 | `core/drupal.tablesort` | Fully disabled | 🟢 Low |
| 9 | `content_moderation/content_moderation` | Fully disabled | 🟢 Low |
| 10 | `layout_builder/drupal.layout_builder` | 2 CSS disabled | 🟢 Low |
| 11 | `node/drupal.node.preview` | 1 CSS disabled | 🟢 Low |
| 12 | `system/base` | 2 CSS disabled | 🟢 Low |
| 13 | `paragraphs/drupal.paragraphs.unpublished` | Fully disabled | 🟢 Low |
| 14–20 | Various optional contrib | Fully disabled (defensive) | 🟢 Low |

---

## High-Risk Overrides 🔴

**These replace core JS files.** A Drupal core security update may include a fix to these exact files — this theme's override will silently mask that fix.

**Action required after every `composer update drupal/core`**: diff the theme's file against the new core file and port any security or behavior changes.

### 1. `core/drupal.active-link` — Custom JS replacement

```yaml
core/drupal.active-link:
  js:
    misc/active-link.js: assets/js/misc/active-link.js
```

**File**: `assets/js/misc/active-link.js`

**Why it was overridden**:
- Bootstrap 5 uses `.active` class, not `.is-active` (core default).
- ES6 modernization (arrow functions, `const`).
- Simplified `[hreflang]` selector (removed `[data-drupal-language]` + `li[lang]` targets).

**What to check after core update**: compare `web/core/misc/active-link.js` with `assets/js/misc/active-link.js`.

---

### 2. `text/drupal.text` — Full library replacement

```yaml
text/drupal.text: ui_suite_bnppre/drupal.text
```

**File**: `assets/js/text/text.js` (via custom `drupal.text` library definition)

**Why it was overridden**:
- "Edit summary" / "Hide summary" button restyled with Bootstrap: `btn btn-outline-dark btn-sm float-end`.
- `once()` added to dependencies (Drupal 9+ correctness).
- Removed parentheses around the button in rendered output.

**What to check after core update**: compare `web/core/modules/text/js/text.js` with `assets/js/text/text.js`.

---

## Medium-Risk Overrides 🟡

### 3. `core/drupal.dropbutton: false` — Fully disabled

All CSS and JS for Drupal's dropdown-button component is removed.

**Justification**: front-end-only theme — admin dropbuttons are not styled here.

**⚠️ Never use this theme as the admin theme.** Dropbutton menus (content list operations, bulk actions, Views exposed filters) will be unstyled.

### 4. `layout_builder_browser/modal` — Custom JS replacement

```yaml
layout_builder_browser/modal:
  js:
    js/layout_builder_browser.modal.js: assets/js/layout-builder-browser/layout-builder-browser.modal.js
```

**Why**: Bootstrap modal integration for the block browser.

**Status**: module `layout_builder_browser` is not currently installed — this is a defensive override. Test when enabling the module.

---

## Low-Risk Overrides 🟢 (CSS-only Disables)

These entries disable CSS files that would conflict with Bootstrap's equivalents. No JS is affected; no security risk.

| Override | CSS removed | Bootstrap replacement |
|----------|------------|----------------------|
| `core/drupal.autocomplete` | `autocomplete-loading.module.css` | Bootstrap spinner component |
| `core/drupal.dialog.off_canvas` | 13 off-canvas CSS files | Bootstrap Offcanvas component |
| `core/drupal.tableheader` | Entire library | Not needed (front-end theme) |
| `core/drupal.tablesort` | Entire library | Not needed (front-end theme) |
| `content_moderation/content_moderation` | Entire library | Not needed (front-end) |
| `layout_builder/drupal.layout_builder` | `layout-builder.css`, `off-canvas.css` | Custom `assets/css/layout-builder/layout-builder.css` |
| `node/drupal.node.preview` | `node.preview.css` | No replacement (preview banner removed) |
| `system/base` | `clearfix.module.css`, `container-inline.module.css` | Bootstrap's `.clearfix` utility |
| `paragraphs/drupal.paragraphs.unpublished` | Entire library | Not needed (front-end) |

---

## Adding a New Library Override

**Decision checklist** before adding an override:

1. Is the CSS conflicting visually with Bootstrap? → CSS disable override.
2. Is the JS behavior incompatible with Bootstrap JS (e.g., modal, dropdown, offcanvas)? → JS replacement override.
3. Is this an admin-only library irrelevant to the front-end theme? → Full disable.
4. Is the module optional (not in `dependencies`)? → Defensive override, document it.

After adding an override or extend:

```bash
vendor/bin/drush cr
```

Verify the override is active: Drupal's asset aggregation debug mode (`?nocache=1` + developer toolbar) shows loaded libraries.
