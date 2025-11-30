# JavaScript Standards - PS Theme (Drupal 11+)

Version: 1.0.0
Date: 2025-11-30
Scope: Component-level behaviors for Storybook + Drupal 11

---

## Goals
\n- Progressive enhancement: components work without JS; JS adds interaction only when present.
- Simplicity first: Use a single init function + `once()` for trivial behaviors (one click listener, no state). Use a class only for multi-listener stateful components (accordions, menus, sliders, timers) or when cleanup is required.
- Modular ES modules: one logical unit per component root (no globals, no jQuery).
- Drupal behaviors: idempotent `attach` using `once()`; proper `detach` (trigger = 'unload') for cleanup.
- Accessibility-first: keyboard support, ARIA, focus management, Escape handling where appropriate.
- Configuration: `data-*` for per-instance options; `drupalSettings` for global defaults (namespaced under `drupalSettings.psTheme.components`).
- Cleanup: Use `AbortController` for complex cases; avoid memory leaks.
- Security: Never inject unsanitized HTML via `innerHTML` from external sources.

---

## File Layout

```
source/patterns/{level}/{component}/
├── {component}.twig
├── {component}.css
├── {component}.yml
├── {component}.stories.jsx
├── {component}.js            # ES module behavior (optional per need)
└── README.md
```

---

## Component Class Pattern

```js
// source/patterns/elements/{component}/{component}.js
export class PsComponent {
  constructor(root, options = {}) {
    this.root = root;
    this.options = { ...PsComponent.defaults, ...options };
    this.controllers = []; // AbortController instances
    this.initialized = false;
  }

  static defaults = {
    // e.g., selectors, durations (ms), keys, etc.
  };

  init() {
    if (this.initialized) return;
    this.initialized = true;
    const ac = new AbortController();
    this.controllers.push(ac);
    // Example listener
    this.root.addEventListener('keydown', this.onKeyDown.bind(this), { signal: ac.signal });
  }

  onKeyDown(e) {
    // Implement keyboard logic
  }

  destroy() {
    this.controllers.forEach((c) => c.abort());
    this.controllers = [];
    this.initialized = false;
  }
}
```

- Use `AbortController` for all listeners to simplify cleanup.
- Never rely on global state; store instance on root `__psInstance`.

---

## Drupal Behaviors (Attach/Detach)

```js
// source/patterns/base/behaviors.js (bundled by Vite)
import { PsComponent as PsBadge } from '../elements/badge/badge.js';

import { once } from 'drupal/once'; // Drupal 11+ ES module form (Storybook mock provides global once fallback)

(function (Drupal, drupalSettings) {
  Drupal.behaviors.psBadge = {
    attach(context) {
      // Simple pattern: function + once (no class needed)
      once('psBadge', '.ps-badge[data-dismissible="true"]', context).forEach((root) => {
        const btn = root.querySelector('[data-badge-dismiss]');
        if (!btn) return;
        btn.addEventListener('click', () => root.remove());
      });
    },
  };

  Drupal.behaviors.psComplexExample = {
    attach(context) {
      const globalCfg = drupalSettings.psTheme?.components?.complexExample || {};
      once('psComplexExample', '.ps-complex-example', context).forEach((root) => {
        const localCfg = parseOptions(root);
        const inst = new PsComponent(root, { ...globalCfg, ...localCfg });
        inst.init();
        root.__psInstance = inst; // trace instance
      });
    },
    detach(context, settings, trigger) {
      if (trigger !== 'unload') return;
      context.querySelectorAll('.ps-complex-example').forEach((root) => {
        if (root.__psInstance) {
          root.__psInstance.destroy();
          root.__psInstance = null;
        }
      });
    },
  };

  function parseOptions(root) {
    return {
      timeout: Number(root.dataset.timeout) || 150,
      dismissible: root.dataset.dismissible === 'true',
    };
  }
})(Drupal, drupalSettings);
```

- Behavior keys: `Drupal.behaviors.ps{Component}`.
- Idempotency: skip if `__psInstance` exists.
- Detach: cleanup when fragment is removed (AJAX, BigPipe).

---

## Accessibility Patterns

- Button-like: Space/Enter activate; `role="button"` (if not `<button>`), `tabindex="0"`, `aria-disabled`.
- Disclosure: `aria-expanded`, `aria-controls`; manage Escape and focus targets.
- Dismissible: Close button labelled; `aria-live` for announcements if needed.
- Menu/Combobox: Arrow keys, `aria-activedescendant`, roving tabindex.
- Accordion: Toggle `aria-expanded` on headers; ensure panel has `role="region"` and `aria-labelledby` referencing header id.
- Dismissible badge/toast: Close button has `aria-label="Close"`; removal should move focus sensibly (back to triggering control when appropriate).

---

## Storybook (HTML Edition)

- Initialize component instances inside stories when interactions are showcased.

```js
// source/patterns/elements/{component}/init-storybook.js
import { PsComponent as PsX } from './{component}.js'; // Only if class-based
export function initStorybook(container = document) {
  container.querySelectorAll('.ps-{component}').forEach((root) => {
    if (!root.__psInstance) {
      const inst = new PsX(root);
      inst.init();
      root.__psInstance = inst;
    }
  });
}
```

```jsx
// {component}.stories.jsx
import twig from './{component}.twig';
import data from './{component}.yml';
import { initStorybook } from './init-storybook.js';

export const Interactive = {
  render: (args) => {
    const html = twig(args);
    setTimeout(() => initStorybook(document), 0);
    return html;
  },
};
```

---

## Libraries (`ps.libraries.yml`)

- Declare JS bundle per group or component, with core dependencies.

```yaml
ps_theme.components:
  js:
    dist/components.js: {}
  css:
    theme:
      dist/components.css: {}
  dependencies:
    - core/drupal
    - core/drupalSettings
```

Attach via `{{ attach_library('ps_theme/components') }}` in Twig templates when needed.

---

## Configuration via Data Attributes

- Boolean: `data-dismissible="true"` → `true`
- Number: `data-timeout="150"` → `150` ms
- String: `data-target="#id"`
- List: `data-keys="Enter,Space"` → `["Enter","Space"]`

Provide sensible defaults in `static defaults`.

### Merging Order (Priority High → Low)

1. Explicit arguments passed in JS (if any)
2. `data-*` attributes on the root element
3. `drupalSettings.psTheme.components.{component}` global defaults
4. `static defaults` inside class

This ensures server-level defaults are overridable per instance without code changes.

---

## Testing & Quality

- Lint with Biome; no unused variables; prefer `const`/`let`.
- Keep functions pure where possible; side effects limited to DOM.
- Storybook interactive stories for behavior coverage.

---

## Anti-Patterns

- Inline event handlers (`onclick`).
- jQuery usage.
- Global singletons.
- Overwriting innerHTML with templates; prefer Twig-rendered HTML.
- Trapping focus without accessible purpose.
- Using `querySelectorAll('body *')` broad scans (performance risk) — prefer scoped selectors.
- Re-initializing same root without `once()` or guard.
- Writing CSS classes from JS to switch variants that already have BEM modifiers (prefer initial markup or toggle semantic state classes only).

---

Maintainers: Design System Team
Last updated: 2025-11-30
