# Architecture

> **Theme**: `ui_suite_bnppre` · **Drupal**: 11 · **Bootstrap**: 5.3.3

---

## Design Philosophy

### Standalone (no base theme)

```yaml
# ui_suite_bnppre.info.yml
base theme: false
```

The theme does **not** extend `ui_suite_bootstrap` or any other theme. All Bootstrap integration, component overrides, and Drupal library replacements are self-contained. This means:

- No cascading from a parent theme's templates or assets.
- Full control over the render pipeline.
- Starterkit child themes inherit from this theme directly.

### Bootstrap-First

Bootstrap utilities, components, and variables are used before any custom CSS is written. Custom SCSS is additive, not replacement-based.

### UI Suite Ecosystem

The theme is built around three UI Suite modules:

| Module | Role |
|--------|------|
| `ui_patterns` | Component-based rendering (Twig components with typed props) |
| `ui_styles` | Style selectors in the editor (classes, variants) |
| `ui_icons` | Icon picker powered by the BNPPRE SVG pack |

---

## Module Dependencies

Declared in `ui_suite_bnppre.info.yml`:

```yaml
dependencies:
  - languageicons:languageicons
  - ui_patterns:ui_patterns
  - ui_styles:ui_styles
```

| Dependency | Type | Purpose |
|-----------|------|---------|
| `languageicons` | Contrib module | Language switcher with flag icons |
| `ui_patterns` | Contrib module | Enables `components/*.component.yml` + SDC rendering |
| `ui_styles` | Contrib module | Style plugin for Layout Builder, Paragraphs, etc. |

Optional integrations (no hard dependency, conditional PHP hooks):
- `commerce` (Commerce cart + checkout templates)
- `media_library` (custom templates + JS)
- `layout_builder` + `layout_builder_browser`
- `paragraphs`
- `content_moderation`
- `fences`
- `search`
- `views`

---

## Region Model

The theme defines **14 regions**, organized in four zones:

### Header Zone

```
┌──────────────────────────────────────────────────────┐
│  brand            │  switcher                         │  ← Branding + language
├──────────────────────────────────────────────────────┤
│  navigation                                         │  ← Desktop nav
├──────────────────────────────────────────────────────┤
│  actions          │  header (Top Bar)                │  ← Header actions + top bar
└──────────────────────────────────────────────────────┘
```

### Content Zone

```
┌────────────────────────────────────────┐
│  highlighted                           │  ← Messages, status
├────────────────────────────────────────┤
│  help                                  │  ← Drupal help text
├──────────────┬─────────────────────────┤
│ sidebar_first │     content            │  ← Main content + sidebars
│               │                        │
│ sidebar_second│                        │
└──────────────┴─────────────────────────┘
```

### Footer Zone

```
┌──────────────────────────────────────────────────────┐
│  footer                                               │
└──────────────────────────────────────────────────────┘
```

### System Zones
- `page_top` — Drupal toolbar, toolbar-tray
- `page_bottom` — Scripts, debug panels

---

## CSS Variant System

The theme ships two CSS output variants selectable in admin settings:

| Variant | Library key | SCSS entry point | Use case |
|---------|------------|-----------------|---------|
| **Realestate** (default) | `ui_suite_bnppre/framework_css_bnppre` | `assets/scss/styles.scss` | BNP Paribas Real Estate corporate sites |
| **Property Search** | `ui_suite_bnppre/framework_css_bnppre_ps` | `assets/scss/styles-ps.scss` | Property search portals |

**How switching works**: The admin form (`ThemeSettings.php`) persists the selected library key. `LibraryInfoAlter.php` dynamically injects the selected CSS library as a dependency of the `framework` library at runtime — no template change required.

```php
// src/Hook/LibraryInfoAlter.php (simplified)
$css_library = $this->themeSettings->getSetting('library.css_loading')
    ?? 'ui_suite_bnppre/framework_css_bnppre';
$libraries['framework']['dependencies'][] = $css_library;
```

**Setting `css_loading` to empty** disables automatic CSS loading, allowing a child theme to inject its own stylesheet via `libraries-extend`.

---

## Starterkit System

This theme is a starterkit source (`starterkit: true`). Running the Drupal starterkit command scaffolds a new child theme pre-configured to extend `ui_suite_bnppre`.

```bash
# From Drupal project root
php web/core/scripts/drupal generate-theme my_child_theme \
    --starterkit ui_suite_bnppre \
    --path web/themes/custom
```

The starterkit template lives in `starterkits/ui_suite_bnppre_starterkit/` and includes:
- Pre-configured block layout YML files
- Theme settings starting point
- Schema stub

---

## Layer Diagram

```
┌─────────────────────────────────────────────────────────┐
│                  Drupal Render Pipeline                  │
├─────────────────────────────────────────────────────────┤
│  src/Hook/         OOP hook classes (#[Hook] attributes) │
│  ├── Preprocess*   Variables → Twig                      │
│  ├── *Alter        Suggestions, libraries, elements      │
│  └── *Settings     Theme form, dynamic library loading   │
├─────────────────────────────────────────────────────────┤
│  templates/        84 Twig overrides (→ Bootstrap HTML)  │
├─────────────────────────────────────────────────────────┤
│  components/       40+ UI Patterns components            │
│                    (Twig + component.yml + stories)       │
├─────────────────────────────────────────────────────────┤
│  assets/           CSS (compiled) · JS · Fonts · Icons   │
│  ├── scss/         Source — edit here                    │
│  └── css/          Generated — never edit                │
└─────────────────────────────────────────────────────────┘
```
