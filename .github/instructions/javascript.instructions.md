---
applyTo:
  - "**/*.js"
  - ".storybook/**/*"
---

# JavaScript Standards - PS Theme

**Version**: 3.0.0  
**Date**: 2025-12-05  
**Scope**: ES6 modules, Drupal behaviors, Storybook integration

---

## 🎯 Core Principles

1. **Progressive enhancement**: Components work without JS; JS adds interaction only
2. **Simplicity first**: Use simple init function + `once()` for trivial behaviors; use class only for stateful components
3. **Modular ES modules**: One logical unit per component (no globals, no jQuery)
4. **Drupal behaviors**: Idempotent `attach` using `once()`; proper `detach` for cleanup
5. **Accessibility-first**: Keyboard support, ARIA, focus management
6. **Configuration**: `data-*` for per-instance options; `drupalSettings` for global defaults
7. **Cleanup**: Use `AbortController` for complex cases to avoid memory leaks
8. **Security**: Never inject unsanitized HTML via `innerHTML`

---

## 📁 File Structure

```
source/patterns/{level}/{component}/
├── {component}.js            # ES module behavior (optional, only if needed)
└── ...
```

**When to create `.js` file**:
- ✅ Interactive behavior (dropdowns, accordions, modals, tabs)
- ✅ Stateful components (toggles, counters, carousels)
- ✅ Keyboard navigation (arrow keys, Escape handling)
- ✅ AJAX interactions, timers, animations
- ❌ Static display components (no JS needed)

---

## 🏗 Component Class Pattern

### When to Use Class

Use a **class** for:
- Multi-listener stateful components (accordions, menus, sliders)
- Components requiring cleanup (timers, observers, event listeners)
- Complex state management

```js
// source/patterns/components/{component}/{component}.js
export class PsComponent {
  constructor(root, options = {}) {
    this.root = root;
    this.options = { ...PsComponent.defaults, ...options };
    this.controllers = []; // AbortController instances
    this.initialized = false;
  }

  static defaults = {
    timeout: 150,
    dismissible: false,
    keys: ['Escape', 'Enter'],
  };

  init() {
    if (this.initialized) return;
    this.initialized = true;
    
    const ac = new AbortController();
    this.controllers.push(ac);
    
    // Example: Keyboard listener
    this.root.addEventListener('keydown', this.onKeyDown.bind(this), {
      signal: ac.signal,
    });
    
    // Example: Click listener
    const button = this.root.querySelector('[data-component-trigger]');
    if (button) {
      button.addEventListener('click', this.onClick.bind(this), {
        signal: ac.signal,
      });
    }
  }

  onKeyDown(e) {
    if (e.key === 'Escape') {
      this.close();
    }
  }

  onClick(e) {
    this.toggle();
  }

  toggle() {
    // Toggle logic
  }

  close() {
    // Close logic
  }

  destroy() {
    this.controllers.forEach((c) => c.abort());
    this.controllers = [];
    this.initialized = false;
  }
}
```

### When to Use Simple Function

Use a **simple function** for:
- One-time initialization (single listener, no state)
- Simple dismissible elements
- Basic click handlers

```js
// source/patterns/components/alert/alert.js
export function initAlert(context = document) {
  const alerts = context.querySelectorAll('[data-dismissible="true"]');
  
  alerts.forEach((alert) => {
    const button = alert.querySelector('[data-alert-dismiss]');
    if (!button) return;
    
    button.addEventListener('click', () => {
      alert.remove();
    }, { once: true }); // Cleanup automatic with { once: true }
  });
}
```

---

## 🔧 Drupal Behaviors Pattern

### Behavior Registration

```js
// source/patterns/base/behaviors.js (bundled by Vite)
import { PsDropdown } from '../components/dropdown/dropdown.js';
import { initAlert } from '../components/alert/alert.js';
import { once } from 'drupal/once'; // Drupal 11+ ES module (Storybook provides global fallback)

(function (Drupal, drupalSettings) {
  
  // Simple pattern: function + once (no class needed)
  Drupal.behaviors.psAlert = {
    attach(context) {
      once('psAlert', '[data-dismissible="true"]', context).forEach((alert) => {
        const button = alert.querySelector('[data-alert-dismiss]');
        if (!button) return;
        button.addEventListener('click', () => alert.remove());
      });
    },
  };
  
  // Complex pattern: class + once (stateful component)
  Drupal.behaviors.psDropdown = {
    attach(context) {
      const globalConfig = drupalSettings.psTheme?.components?.dropdown || {};
      
      once('psDropdown', '.ps-dropdown', context).forEach((root) => {
        // Skip if already initialized
        if (root.__psInstance) return;
        
        // Parse local configuration from data attributes
        const localConfig = {
          timeout: Number(root.dataset.timeout) || undefined,
          dismissible: root.dataset.dismissible === 'true',
        };
        
        // Merge: local overrides global overrides defaults
        const instance = new PsDropdown(root, { ...globalConfig, ...localConfig });
        instance.init();
        
        // Store instance for cleanup
        root.__psInstance = instance;
      });
    },
    
    detach(context, settings, trigger) {
      // Only cleanup on unload (AJAX/BigPipe fragment removal)
      if (trigger !== 'unload') return;
      
      context.querySelectorAll('.ps-dropdown').forEach((root) => {
        if (root.__psInstance) {
          root.__psInstance.destroy();
          root.__psInstance = null;
        }
      });
    },
  };
  
})(Drupal, drupalSettings);
```

### Behavior Keys

```
Drupal.behaviors.ps{Component}
```

**Examples**:
- `Drupal.behaviors.psDropdown`
- `Drupal.behaviors.psAccordion`
- `Drupal.behaviors.psModal`

### Idempotency with `once()`

```js
// ✅ CORRECT - Prevents re-initialization
once('psDropdown', '.ps-dropdown', context).forEach((root) => {
  // This code runs only once per element
});

// ❌ WRONG - Will re-initialize on AJAX
context.querySelectorAll('.ps-dropdown').forEach((root) => {
  // This runs every time, causing duplicate listeners
});
```

---

## ♿ Accessibility Patterns

### Button-Like Elements

```js
// Element with role="button" (not <button>)
element.setAttribute('role', 'button');
element.setAttribute('tabindex', '0');

element.addEventListener('keydown', (e) => {
  if (e.key === ' ' || e.key === 'Enter') {
    e.preventDefault();
    element.click();
  }
});
```

### Disclosure (Expandable/Collapsible)

```js
// Toggle button
trigger.setAttribute('aria-expanded', 'false');
trigger.setAttribute('aria-controls', panelId);

// Panel
panel.setAttribute('id', panelId);
panel.hidden = true;

// Toggle
trigger.addEventListener('click', () => {
  const expanded = trigger.getAttribute('aria-expanded') === 'true';
  trigger.setAttribute('aria-expanded', !expanded);
  panel.hidden = expanded;
});

// Escape key
trigger.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && trigger.getAttribute('aria-expanded') === 'true') {
    trigger.setAttribute('aria-expanded', 'false');
    panel.hidden = true;
    trigger.focus();
  }
});
```

### Dismissible Elements

```js
// Close button
closeButton.setAttribute('aria-label', 'Close alert');

closeButton.addEventListener('click', () => {
  element.remove();
  // Move focus to logical element (e.g., trigger that opened it)
  triggerElement?.focus();
});
```

### Menu/Dropdown Navigation

```js
// Arrow keys
dropdown.addEventListener('keydown', (e) => {
  const items = Array.from(dropdown.querySelectorAll('[role="menuitem"]'));
  const current = document.activeElement;
  const index = items.indexOf(current);
  
  if (e.key === 'ArrowDown') {
    e.preventDefault();
    const next = items[index + 1] || items[0];
    next.focus();
  } else if (e.key === 'ArrowUp') {
    e.preventDefault();
    const prev = items[index - 1] || items[items.length - 1];
    prev.focus();
  } else if (e.key === 'Home') {
    e.preventDefault();
    items[0].focus();
  } else if (e.key === 'End') {
    e.preventDefault();
    items[items.length - 1].focus();
  } else if (e.key === 'Escape') {
    closeDropdown();
    trigger.focus();
  }
});
```

---

## 🎨 Storybook Integration

### Global Import (MANDATORY Pattern)

**⚠️ CRITICAL**: Import behaviors **globally** in `.storybook/preview.js`, NOT in individual `.stories.jsx` files.

**Why?** Drupal.attachBehaviors() decorator timing issue—stories load before behaviors if imported locally.

```js
// .storybook/preview.js
import '../source/patterns/components/dropdown/dropdown.js';
import '../source/patterns/components/accordion/accordion.js';
import '../source/patterns/components/modal/modal.js';
// ... all interactive components
```

```jsx
// component.stories.jsx
// ❌ NEVER import behaviors here
// ❌ import './component.js'; // WRONG - Timing issue

// ✅ Behaviors already loaded globally
export const Interactive = {
  render: (args) => componentTwig(args),
  args: { ...data },
};
```

### Interactive Stories

```jsx
// For components with JS behavior, stories work automatically
// because behaviors are loaded globally and Drupal.attachBehaviors()
// runs on story render

export const Interactive = {
  render: (args) => componentTwig(args),
  args: {
    ...data,
    // Configure interactive behavior
    dismissible: true,
    timeout: 300,
  },
};
```

---

## ⚙️ Configuration

### Data Attributes (Per-Instance)

```html
<div class="ps-component"
  data-timeout="150"
  data-dismissible="true"
  data-target="#panel-id"
  data-keys="Enter,Space"
>
</div>
```

```js
// Parse in behavior
const config = {
  timeout: Number(root.dataset.timeout) || 150,
  dismissible: root.dataset.dismissible === 'true',
  target: root.dataset.target,
  keys: root.dataset.keys?.split(',') || ['Enter', 'Space'],
};
```

### drupalSettings (Global Defaults)

```js
// Drupal: Set in theme or module
drupalSettings.psTheme = {
  components: {
    dropdown: {
      timeout: 200,
      dismissible: true,
    },
  },
};
```

```js
// JS: Access global config
const globalConfig = drupalSettings.psTheme?.components?.dropdown || {};

// Merge priority: Local > Global > Class defaults
const finalConfig = {
  ...PsDropdown.defaults,    // 1. Class defaults
  ...globalConfig,           // 2. Global drupalSettings
  ...localConfig,            // 3. data-* attributes (highest priority)
};
```

---

## 🗑️ Cleanup with AbortController

```js
class PsComponent {
  constructor(root) {
    this.root = root;
    this.controllers = [];
  }

  init() {
    const ac = new AbortController();
    this.controllers.push(ac);

    // All listeners share the same abort signal
    this.root.addEventListener('click', this.onClick, { signal: ac.signal });
    this.root.addEventListener('keydown', this.onKeyDown, { signal: ac.signal });
    
    window.addEventListener('resize', this.onResize, { signal: ac.signal });
  }

  destroy() {
    // Abort all listeners at once
    this.controllers.forEach((c) => c.abort());
    this.controllers = [];
  }
}
```

**Benefits**:
- Single line cleanup (no manual `removeEventListener`)
- No memory leaks
- Works with multiple controllers

---

## 🚫 Anti-Patterns

### 1. jQuery

```js
❌ $('.ps-component').on('click', function() { ... });
✅ element.addEventListener('click', (e) => { ... });
```

### 2. Inline Handlers

```html
❌ <button onclick="handleClick()">Click</button>
```

### 3. Global Singletons

```js
❌ window.myComponent = new Component();
✅ root.__psInstance = new Component(root);
```

### 4. Unsafe innerHTML

```js
❌ element.innerHTML = userInput; // XSS risk
✅ element.textContent = userInput;
```

### 5. Missing once()

```js
❌ context.querySelectorAll('.ps-dropdown').forEach((el) => {
     // Runs every AJAX load - duplicate listeners!
   });

✅ once('psDropdown', '.ps-dropdown', context).forEach((el) => {
     // Runs once per element
   });
```

### 6. No Cleanup

```js
❌ // No detach() method - memory leaks on AJAX

✅ detach(context, settings, trigger) {
     if (trigger !== 'unload') return;
     // Cleanup instances
   }
```

### 7. Local Story Imports

```jsx
❌ // In component.stories.jsx
   import './component.js'; // WRONG - Timing issue

✅ // In .storybook/preview.js (global)
   import '../source/patterns/components/component/component.js';
```

---

## 🔗 Cross-References

- **Accessibility**: `instructions/accessibility.instructions.md`
- **Storybook Format**: `instructions/storybook.instructions.md`
- **Component Structure**: `instructions/components.instructions.md`

---

**Last Updated**: 2025-12-05  
**Maintainers**: Design System Team
