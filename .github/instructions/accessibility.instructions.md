---
title: Accessibility Standards
version: 3.0.0
lastUpdated: 2025-12-05
applyTo:
  - "**/*.twig"
  - "**/*.css"
  - "**/*.js"
priority: HIGH
related:
  - components.instructions.md
  - templates.instructions.md
status: ACTIVE
---

# Accessibility Standards - PS Theme

**Scope**: WCAG 2.2 AA compliance, keyboard, ARIA, screen readers

---

## 📖 When to Use This File

**Use this file when you need to:**
- ✅ Verify **WCAG 2.2 Level AA compliance** (contrast, keyboard, ARIA)
- ✅ Implement **keyboard navigation** (Tab, Arrow keys, Enter, Escape)
- ✅ Add **ARIA attributes** (labels, roles, states)
- ✅ Ensure **focus-visible** indicators (all interactive elements)
- ✅ Test with **screen readers** (NVDA, VoiceOver, JAWS)
- ✅ Check **contrast ratios** (4.5:1 text, 3:1 UI components)

**DO NOT use this file for:**
- ❌ Learning **CSS implementation** (see: css.instructions.md)
- ❌ Writing **Twig templates** (see: templates.instructions.md)
- ❌ Understanding **component structure** (see: components.instructions.md)
- ❌ Creating **JavaScript behaviors** (see: javascript.instructions.md)
- ❌ Following **complete workflows** (see: workflows.instructions.md)

**Audience**: Developers implementing accessible components, QA testers, AI agents validating accessibility

---

## 🎯 Core Requirements

**All components MUST meet WCAG 2.2 Level AA**:
- Contrast ratios (text, UI components)
- Keyboard navigation
- Focus indicators
- ARIA attributes
- Screen reader support
- Semantic HTML

---

## 🎨 Contrast Ratios (WCAG 2.2 AA)

### Text Contrast

| Content Type | Minimum Ratio | Example Tokens |
|--------------|---------------|----------------|
| Normal text (<18px / <14px bold) | **4.5:1** | `--gray-900` on `--white` |
| Large text (≥18px / ≥14px bold) | **3:1** | `--gray-700` on `--white` |

```css
/* ✅ GOOD - Meets 4.5:1 */
.ps-text {
  color: var(--gray-900); /* hsl(222, 47%, 11%) on white = 14.8:1 */
}

/* ⚠️ MODERATE - Meets 3:1 for large text only */
.ps-heading {
  color: var(--gray-600); /* hsl(215, 19%, 35%) on white = 4.1:1 */
  font-size: var(--font-size-4); /* 22px = large */
}

/* ❌ BAD - Fails AA */
.ps-text {
  color: var(--gray-400); /* hsl(215, 20%, 65%) on white = 2.4:1 - FAIL */
}
```

### UI Component Contrast

| Element Type | Minimum Ratio | Example |
|--------------|---------------|---------|
| Borders | **3:1** | `--gray-400` border on `--white` |
| Icons | **3:1** | Icon color vs background |
| Focus indicators | **3:1** | Outline vs background |
| Active UI components | **3:1** | Button border, toggle track |

```css
/* ✅ GOOD - UI component contrast */
.ps-button {
  border: var(--border-size-1) solid var(--gray-400); /* 3.1:1 - PASS */
}

.ps-checkbox {
  border: var(--border-size-2) solid var(--gray-500); /* 4.7:1 - PASS */
}
```

### Exceptions

**Disabled elements**: No minimum contrast required (WCAG exemption).

```css
.ps-button:disabled {
  opacity: 0.5; /* Allowed - disabled state */
  color: var(--gray-400); /* Allowed - disabled state */
}
```

---

## ⌨️ Keyboard Navigation

### Focus Management (MANDATORY)

**ALL interactive elements MUST be keyboard accessible**:
- Buttons, links, inputs, selects
- Custom controls (toggles, tabs, accordions)
- Dropdowns, modals, tooltips

```css
/* ✅ MANDATORY - Focus-visible for all interactives */
.ps-button {
  &:focus-visible {
    outline: var(--border-size-2) solid var(--secondary);
    outline-offset: var(--border-size-2);
  }
}

.ps-link {
  &:focus-visible {
    outline: var(--border-size-2) solid var(--blue-600);
    outline-offset: var(--border-size-1);
  }
}

/* ❌ WRONG - Missing focus-visible */
.ps-button {
  &:hover {
    background: var(--primary-hover);
  }
  /* Missing :focus-visible ! */
}
```

### Focus Order

**Ensure logical tab order**:

```html
<!-- ✅ GOOD - Natural DOM order = tab order -->
<form>
  <label for="name">Name</label>
  <input id="name" type="text" /> <!-- Tab 1 -->
  
  <label for="email">Email</label>
  <input id="email" type="email" /> <!-- Tab 2 -->
  
  <button type="submit">Submit</button> <!-- Tab 3 -->
</form>

<!-- ❌ BAD - tabindex override (avoid unless necessary) -->
<button tabindex="2">Second</button>
<button tabindex="1">First</button>
```

### Skip to Content

**Provide skip link on every page**:

```html
<a href="#main-content" class="ps-skip-link">
  Skip to main content
</a>

<main id="main-content">
  <!-- Page content -->
</main>
```

```css
.ps-skip-link {
  position: absolute;
  top: var(--size-2);
  left: var(--size-2);
  transform: translateY(-150%);
  
  &:focus-visible {
    transform: translateY(0); /* Show on focus */
  }
}
```

---

## 🏷️ ARIA Attributes

### When to Use ARIA

**ARIA Rules**:
1. **No ARIA is better than bad ARIA**
2. **Use semantic HTML first** (prefer `<button>` over `<div role="button">`)
3. **Add ARIA only when semantic HTML insufficient**

### Common ARIA Patterns

#### Button-like Elements

```html
<!-- ✅ BEST - Native button -->
<button class="ps-button">Click me</button>

<!-- ⚠️ OK - Custom element with ARIA -->
<div class="ps-button" role="button" tabindex="0">
  Click me
</div>
```

#### Disclosure (Expandable)

```html
<button class="ps-accordion__trigger"
  aria-expanded="false"
  aria-controls="panel-1"
>
  Section Title
</button>

<div id="panel-1" class="ps-accordion__panel" hidden>
  Panel content
</div>
```

```js
// Toggle
trigger.addEventListener('click', () => {
  const expanded = trigger.getAttribute('aria-expanded') === 'true';
  trigger.setAttribute('aria-expanded', !expanded);
  panel.hidden = expanded;
});
```

#### Labels

```html
<!-- ✅ GOOD - Visible label with for/id -->
<label for="email-input">Email Address</label>
<input id="email-input" type="email" />

<!-- ✅ GOOD - aria-label when no visible label -->
<button aria-label="Close dialog">
  <span data-icon="close"></span>
</button>

<!-- ✅ GOOD - aria-labelledby (reference another element) -->
<div role="dialog" aria-labelledby="dialog-title">
  <h2 id="dialog-title">Confirm Action</h2>
  <p>Are you sure?</p>
</div>
```

#### Descriptions

```html
<!-- aria-describedby for additional context -->
<input
  id="password"
  type="password"
  aria-describedby="password-hint"
/>
<span id="password-hint" class="ps-form-field__helper">
  Must be at least 8 characters
</span>
```

#### Live Regions

```html
<!-- Announce dynamic changes to screen readers -->
<div class="ps-alert" role="alert">
  Form submitted successfully!
</div>

<!-- Polite (non-urgent) -->
<div class="ps-status" role="status" aria-live="polite">
  Saving...
</div>

<!-- Assertive (urgent) -->
<div class="ps-error" role="alert" aria-live="assertive">
  Error: Connection lost
</div>
```

#### Hidden Content

```html
<!-- Visually hidden but accessible to screen readers -->
<span class="ps-visually-hidden">
  New messages
</span>

<!-- Hidden from everyone (including screen readers) -->
<div hidden>
  Not visible or accessible
</div>

<!-- aria-hidden (hide from screen readers but visible) -->
<span aria-hidden="true" data-icon="decorative"></span>
```

---

## 🖱️ Keyboard Interactions

### Standard Keys

| Element | Keys | Action |
|---------|------|--------|
| Button | Space, Enter | Activate |
| Link | Enter | Follow |
| Checkbox | Space | Toggle |
| Radio | Arrow keys | Select |
| Select | Arrow keys, Space | Open/navigate |
| Tab | Arrow keys | Navigate tabs |

### Custom Components

#### Dropdown/Menu

```js
dropdown.addEventListener('keydown', (e) => {
  const items = Array.from(dropdown.querySelectorAll('[role="menuitem"]'));
  const current = items.indexOf(document.activeElement);
  
  switch (e.key) {
    case 'ArrowDown':
      e.preventDefault();
      items[(current + 1) % items.length].focus();
      break;
    
    case 'ArrowUp':
      e.preventDefault();
      items[(current - 1 + items.length) % items.length].focus();
      break;
    
    case 'Home':
      e.preventDefault();
      items[0].focus();
      break;
    
    case 'End':
      e.preventDefault();
      items[items.length - 1].focus();
      break;
    
    case 'Escape':
      closeDropdown();
      trigger.focus();
      break;
  }
});
```

#### Accordion

```js
trigger.addEventListener('keydown', (e) => {
  if (e.key === 'Enter' || e.key === ' ') {
    e.preventDefault();
    trigger.click();
  }
});
```

#### Modal

```js
// Escape key closes modal
modal.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    closeModal();
    openButton.focus(); // Return focus
  }
});

// Trap focus inside modal
modal.addEventListener('keydown', (e) => {
  if (e.key === 'Tab') {
    const focusable = modal.querySelectorAll('a, button, input, [tabindex="0"]');
    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    
    if (e.shiftKey && document.activeElement === first) {
      e.preventDefault();
      last.focus();
    } else if (!e.shiftKey && document.activeElement === last) {
      e.preventDefault();
      first.focus();
    }
  }
});
```

---

## 🏗️ Semantic HTML

### Use Proper Elements

```html
<!-- ✅ GOOD - Semantic HTML -->
<header class="ps-header">
  <nav class="ps-nav" aria-label="Main navigation">
    <ul>
      <li><a href="/">Home</a></li>
    </ul>
  </nav>
</header>

<main class="ps-main">
  <article class="ps-article">
    <h1>Article Title</h1>
    <p>Content...</p>
  </article>
</main>

<footer class="ps-footer">
  <!-- Footer content -->
</footer>

<!-- ❌ BAD - Divs for everything -->
<div class="header">
  <div class="nav">
    <div class="link">Home</div>
  </div>
</div>
```

### Headings Hierarchy

```html
<!-- ✅ GOOD - Logical hierarchy -->
<h1>Page Title</h1>
  <h2>Section 1</h2>
    <h3>Subsection 1.1</h3>
    <h3>Subsection 1.2</h3>
  <h2>Section 2</h2>

<!-- ❌ BAD - Skipped levels -->
<h1>Title</h1>
  <h3>Subsection</h3> <!-- Skipped h2 -->
```

---

## 🔊 Screen Reader Support

### Accessible Names

**Every interactive element needs an accessible name**:

```html
<!-- ✅ GOOD - Text content -->
<button>Submit Form</button>

<!-- ✅ GOOD - aria-label -->
<button aria-label="Close dialog">
  <span data-icon="close"></span>
</button>

<!-- ✅ GOOD - aria-labelledby -->
<button aria-labelledby="label-text">
  <span id="label-text">Save Changes</span>
  <span data-icon="save"></span>
</button>

<!-- ❌ BAD - No accessible name -->
<button>
  <span data-icon="close"></span>
</button>
```

### Alt Text

```html
<!-- ✅ GOOD - Descriptive alt -->
<img src="property.jpg" alt="Modern 3-bedroom apartment with balcony overlooking downtown" />

<!-- ✅ GOOD - Decorative (empty alt) -->
<img src="decorative-line.svg" alt="" />

<!-- ❌ BAD - Missing alt -->
<img src="property.jpg" />

<!-- ❌ BAD - Redundant alt -->
<img src="photo.jpg" alt="Photo" />
```

### Visually Hidden Class

```css
.ps-visually-hidden {
  position: absolute;
  width: 1px;
  height: 1px;
  margin: -1px;
  padding: 0;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}
```

```html
<!-- Provide context for screen readers -->
<button>
  <span data-icon="search"></span>
  <span class="ps-visually-hidden">Search properties</span>
</button>
```

---

## ✅ Accessibility Checklist

### Every Component Must Have:

- [ ] **Contrast**: Text 4.5:1, UI 3:1 (WCAG AA)
- [ ] **Focus-visible**: All interactives have visible focus indicator
- [ ] **Keyboard**: Fully keyboard navigable (Tab, Arrow keys, Enter, Space, Escape)
- [ ] **ARIA**: Appropriate roles, labels, states (when semantic HTML insufficient)
- [ ] **Semantic HTML**: Use proper elements (`<button>`, `<nav>`, `<main>`, etc.)
- [ ] **Accessible name**: Every interactive has text or `aria-label`
- [ ] **Alt text**: Images have descriptive `alt` (or `alt=""` if decorative)
- [ ] **Headings**: Logical hierarchy (no skipped levels)
- [ ] **Screen reader**: Test with NVDA (Windows) or VoiceOver (Mac)

### Testing Tools

- **Browser DevTools**: Inspect ARIA attributes, contrast checker
- **axe DevTools**: Automated accessibility testing (browser extension)
- **Lighthouse**: Accessibility audit in Chrome DevTools
- **Screen readers**: NVDA (Windows), VoiceOver (Mac), JAWS (Windows)

---

## 🚫 Anti-Patterns

### 1. No Focus Indicator

```css
❌ button:focus { outline: none; } /* NEVER remove focus outline without alternative */
```

### 2. Low Contrast

```css
❌ color: var(--gray-300); /* 2.1:1 on white - FAIL AA */
```

### 3. Keyboard Trap

```js
❌ // Modal that doesn't handle Escape key or focus trapping
```

### 4. Missing Alt Text

```html
❌ <img src="property.jpg" /> <!-- Missing alt -->
```

### 5. Divs as Buttons

```html
❌ <div onclick="submit()">Submit</div>
✅ <button type="button" onclick="submit()">Submit</button>
```

### 6. Empty Buttons

```html
❌ <button><span data-icon="close"></span></button>
✅ <button aria-label="Close"><span data-icon="close"></span></button>
```

### 7. Redundant ARIA

```html
❌ <button role="button">Click</button> <!-- Redundant role -->
✅ <button>Click</button>
```

---

## 🔗 Cross-References

- **CSS Standards**: `instructions/css.instructions.md` (Focus-visible)
- **JavaScript Standards**: `instructions/javascript.instructions.md` (Keyboard patterns)
- **Templates**: `instructions/templates.instructions.md` (ARIA in Twig)

---

**Last Updated**: 2025-12-05  
**Maintainers**: Design System Team
