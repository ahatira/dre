# About PS Theme

PS Theme is a custom Drupal theme which is compatible with Drupal 10 and 11. PS Theme is the
default front-end theme for BNP Paribas RealEstate.

PS Theme is built using [Storybook](https://storybook.js.org/) (HTML edition), and [Vite](https://vitejs.dev/) (Vanilla JS edition), with the help of many NodeJS packages to improve automation and make use of the latest Front-End tooling. See `package.json` for specifics about packages being used.

## JavaScript (Drupal-friendly behaviors)

All component scripts MUST be Drupal-ready:

- Use `Drupal.behaviors` and `once()` to prevent multiple attachments and support Ajax/BigPipe re-render.
- Scope DOM queries to the `context` passed to the behavior.
- Register built files in `ps.libraries.yml` with dependencies: `core/drupal`, `core/drupalSettings`, `core/once`.
- Avoid global `document` listeners; attach events to elements discovered in `context`.
- Dispatch custom events when helpful (e.g., `accordion:show|shown|hide|hidden`).

Example skeleton:

```js
/** @file Accordion behavior */
((Drupal, once) => {
  Drupal.behaviors.psAccordion = {
    attach(context) {
      once('ps-accordion', '[data-accordion]', context).forEach((root) => {
        root.querySelectorAll('[data-accordion-trigger]').forEach((trigger) => {
          trigger.addEventListener('click', () => this.toggleItem(root, trigger));
          trigger.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
              e.preventDefault();
              this.toggleItem(root, trigger);
            }
          });
        });
      });
    },
    toggleItem(root, trigger) {
      // ...
    },
  };
})(Drupal, once);
```

## JavaScript bundling standard

- Single Drupal bundle: `dist/js/scripts.js`.
- Aggregator: `source/patterns/scripts.js` eagerly imports all component behaviors via `import.meta.glob`. Any new `*.js` behavior placed under `source/patterns/**` is auto-included; no config changes needed.
- Vendors: All modules from `node_modules` are emitted into `dist/js/vendors/vendors.js` using Rollup `manualChunks`. If you need a separate vendor split, extend `manualChunks` in `vite.config.js`.

## Running the project

There are several custom npm commands that allows developers to build and run different
tasks during and after development. These commands can be found in `package.json`.
The most common ones to use include:

- `npm run build`: This is the command that builds all your local assets and builds your project. This should be the first command to be executed if you are building your project for the first time.

- `npm run watch`: This will run both `npm run vite:watch` and `npm run storybook:dev`. This is the most common command to run while working with Surface during development. Among the tasks the watch command runs, are:
  - Cleaning out the `dist` folder and compiling a fresh copy of all production code.
  - Linting (CSS and JS) files to ensure code standards are met.
  - Watching for changes to CSS, JS, and Images and compiling them if needed.
  - Recursively globbing through all CSS and JS files within the source directory.

> **NOTE**: Most tasks included in the watch command can be found in `vite.config.js`.

- `npm run storybook:build`: This command will build a local/static instance of Storybook for production in your theme's `/storybook` directory. The `npm run build` command should be executed prior to the `storybook:build` command to ensure all required assets are available before building Storybook.

## Design system

PS Theme uses [Storybook](https://storybook.js.org/) as its design system and that's where all components on the sites are originally built and maintained. Storybook can be accessed on its own by running `npm run watch` and navigating to `http://localhost:6006`.

## Atomic Design Methodology

Although Surface adheres to the Atomic Design methodology, it does not use the same naming conventions for naming its patterns. Our naming convention for the top level categories are:

- **Elements** - equivalent to Atoms
- **Components** - equivalent to Molecules
- **Collections** - equivalent to Organisms
- **Layouts** - equivalent to templates
- **Pages** - same

## SVG Icon System

PS Theme uses an optimized **SVG sprite system** supporting **3 access patterns** for 141 semantic icons:

### Icon Source & Build

```bash
# Icon source files (development only)
source/icons-source/          # 141 SVG source files
source/assets/icons/icons-sprite.svg  # Generated sprite (production asset)

# Auto-generated outputs
source/props/icons-generated.css              # CSS with all 141 [data-icon] rules
source/patterns/documentation/icons-registry.json  # Icon metadata + categories

# Build commands
npm run build                 # Generates all outputs (CSS, sprite, registry)
npm run watch                 # Auto-regenerates on file changes during development
```

### 3 Icon Access Patterns

All 3 patterns work simultaneously and access the same icon source:

#### Pattern 1: Twig Component (Recommended for consistency)

Use the `icon` element component for type-safe icon rendering with automatic SVG fallback:

```twig
{# In any Twig template #}
{% include '@elements/icon/icon.twig' with {
  icon: 'check',           # Icon name (required)
  size: 'md',              # Size: xs, sm, md, lg, xl (optional, default: md)
  class: 'my-custom-class' # Extra CSS classes (optional)
} only %}
```

**Benefits**:
- Type-safe icon names validated against registry
- Consistent sizing via `size` prop
- Automatic SVG fallback for unsupported browsers
- Integrates with component styling system

**Supported icon names** (141 total):
`accessibility`, `account`, `alert`, `arrow-right`, `check`, `calendar`, `search`, etc.  
See `source/patterns/documentation/icons-registry.json` for complete list.

---

#### Pattern 2: data-icon Attribute (Direct HTML - 141 icons!)

Use HTML `data-icon` attribute for lightweight, direct icon rendering:

```html
<!-- Simple span element with data-icon -->
<span data-icon="check"></span>

<!-- In combination with other classes -->
<span class="button__icon" data-icon="arrow-right"></span>

<!-- All 141 icons available -->
<span data-icon="accessibility"></span>
<span data-icon="account"></span>
<span data-icon="air-conditioning"></span>
<!-- ... and 138 more -->
```

**CSS Styling** (auto-generated):
```css
/* Generated in source/props/icons-generated.css */
[data-icon] {
  display: inline-block;
  width: 1em;
  height: 1em;
  background-repeat: no-repeat;
  background-position: center;
  background-size: contain;
}

[data-icon="check"] { 
  background-image: url('/icons/icons-sprite.svg#icon-check'); 
}
/* ... 140 more icon rules */
```

**Benefits**:
- Minimal HTML markup
- Direct CSS control
- Perfect for component libraries
- Inherits parent color via `currentColor`

**Example in components**:
```html
<!-- Button with icon -->
<button class="ps-button">
  <span class="ps-button__icon" data-icon="check"></span>
  Save
</button>

<!-- Badge with icon -->
<div class="ps-badge">
  <span class="ps-badge__icon" data-icon="award"></span>
  Premium
</div>
```

---

#### Pattern 3: SVG with `<use>` (Full control)

Use SVG `<use>` element for advanced styling and manipulation:

```html
<!-- SVG sprite reference -->
<svg width="24" height="24" viewBox="0 0 24 24">
  <use href="/icons/icons-sprite.svg#icon-check"></use>
</svg>

<!-- With custom styling -->
<svg class="custom-icon" width="32" height="32" viewBox="0 0 24 24">
  <use href="/icons/icons-sprite.svg#icon-arrow-right"></use>
</svg>
```

**Benefits**:
- Full SVG control (transforms, animations, etc.)
- Custom sizing and styling
- Direct sprite reference
- Best for complex icon interactions

**CSS Example**:
```css
.custom-icon {
  fill: var(--primary);
  transition: transform 200ms ease;
}

.custom-icon:hover {
  transform: translateX(4px);
}
```

---

### Icon Categories (Auto-Discovered)

The registry JSON automatically categorizes all 141 icons:

```json
{
  "generated": "2025-12-08T12:16:11.959Z",
  "total": 141,
  "names": ["accessibility", "account", ...],
  "categories": {
    "ui": ["alert", "check", "info", ...],
    "navigation": ["arrow-down", "arrow-left", ...],
    "forms": ["checkbox-off", "checkbox-on", ...],
    "communication": ["email", "phone", "share"],
    "media": ["download", "upload"],
    "business": ["map", "pin", ...]
  }
}
```

Find this file at: `source/patterns/documentation/icons-registry.json`

---

### Best Practices

| Use Case | Recommended Pattern | Why |
|----------|-------------------|-----|
| In Twig templates | Pattern 1 (Component) | Type-safe, consistent, maintainable |
| Button/Badge icons | Pattern 2 (data-icon) | Lightweight, CSS-controlled |
| Complex interactions | Pattern 3 (SVG `<use>`) | Full SVG manipulation |
| Icon libraries | Pattern 2 (data-icon) | Easy to scale, auto-updated |
| Animation heavy | Pattern 3 (SVG `<use>`) | Direct transform control |

---

### Adding New Icons

To add a new icon:

1. **Create SVG file**: Drop new SVG in `source/icons-source/` (e.g., `my-icon.svg`)
2. **Verify format**: Ensure SVG has proper viewBox attribute
3. **Rebuild**: Run `npm run build`
4. **Verify**: New icon appears in:
   - `source/props/icons-generated.css` (auto-generated)
   - `source/patterns/documentation/icons-registry.json` (metadata)
5. **Use**: Access via any of 3 patterns immediately

```bash
# Add icon source
echo '<svg viewBox="0 0 24 24"><circle r="10"/></svg>' > source/icons-source/my-icon.svg

# Rebuild (generates CSS + registry)
npm run build

# Use in templates
{% include '@elements/icon/icon.twig' with { icon: 'my-icon' } only %}
<!-- or -->
<span data-icon="my-icon"></span>
```

---

### Features

- ✅ **141 semantic icon names** (fully indexed)
- ✅ **Auto-compiled from source SVGs** via `scripts/build-icons.mjs`
- ✅ **3 access patterns** (Twig component, data-icon attribute, SVG direct)
- ✅ **Auto-generated CSS** with all 141 rules (zero manual maintenance)
- ✅ **Registry-based validation** for icon discovery
- ✅ **Source files excluded from dist** (only compiled sprite shipped)
- ✅ **Automatic watch mode** during development
- ✅ **CSS-controlled styling** (inherits `currentColor`)
- ✅ **Zero breaking changes** from existing icon system

## Development approach

PS Theme is built using the latest development practices for CSS, JS, and Twig. Within Surface's Storybook, all components are built using BEM methodology for selector classes and ES6 for Javascript.

## Available components

For a simple demonstration of how to build components in Storybook and integrate them with Drupal, we are sharing a couple of components we use on our projects. These components are:

### Elements

- Breadcrum, Button, Date, Date badge, Eyebrow, Images, Readtime, Title

### Components

- Callout, Card, Featured card, Quote

### Layouts

- Block

### Theme

- ckeditor


## Demo of static instance of Storybook

[Static Surface theme built with Storybook](https://dev-ucla-surface-training.pantheonsite.io/themes/custom/surface/storybook/?path=/docs/getting-started-intro--docs)

## About Drupal Theming

For demo purposes, we have included Drupal template suggestions inside `templates/`, which also include examples of how a particular Drupal entity (i.e. content type) is integrated with a Storybook component.

For more information, see Drupal.org [theming guide](https://www.drupal.org/docs/develop/theming-drupal).

Upstream Surface was built with 🩵 by the good folks at [BNP Paribas](https://it.uclahealth.org/about/dgit/teams/web-development).

For a walkthrough on how this project was built, along with other related goodies, take a look a the [blog series](https://mariohernandez.io/series/storybook/) by Mario Hernandez.
