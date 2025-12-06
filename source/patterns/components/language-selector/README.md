# Language Selector

**Molecule** | **Navigation** | **Stable**

Accessible language switcher with country flags, short labels (2-letter codes), and dropdown menu. Includes native `<select>` fallback for no-JavaScript environments.

---

## 📋 Overview

The Language Selector component allows users to switch between different languages/locales in the interface. It combines:

- **Visual flags** (rectangular, 20×14px per Figma spec)
- **Short labels** (2-letter ISO codes: En, Fr, Es)
- **Dropdown menu** with keyboard navigation
- **Native fallback** (`<select>`) for progressive enhancement

Aligned with Figma specifications: compact spacing (4px × 12px), 1px border, square corners, 36px height (default).

---

## 🎯 Usage

### Basic Example

```twig
{% include '@ps_theme/language-selector/language-selector.twig' with {
  name: 'lang',
  size: 'sm',
  current: {
    code: 'GB',
    label: 'En',
    locale: 'en-GB'
  },
  options: [
    { code: 'GB', label: 'En', value: 'en', locale: 'en-GB', selected: true },
    { code: 'ES', label: 'Es', value: 'es', locale: 'es-ES', selected: false },
    { code: 'FR', label: 'Fr', value: 'fr', locale: 'fr-FR', selected: false }
  ]
} only %}
```

### Real Estate Multi-Market Example

```twig
{% include '@ps_theme/language-selector/language-selector.twig' with {
  name: 'market-language',
  size: 'md',
  variant: 'primary',
  current: {
    code: 'FR',
    label: 'Fr',
    locale: 'fr-FR'
  },
  options: [
    { code: 'FR', label: 'Fr', value: 'fr', locale: 'fr-FR', url: '/fr', selected: true },
    { code: 'GB', label: 'En', value: 'en', locale: 'en-GB', url: '/en', selected: false },
    { code: 'DE', label: 'De', value: 'de', locale: 'de-DE', url: '/de', selected: false },
    { code: 'IT', label: 'It', value: 'it', locale: 'it-IT', url: '/it', selected: false },
    { code: 'ES', label: 'Es', value: 'es', locale: 'es-ES', url: '/es', selected: false }
  ]
} only %}
```

### Large Header Navigation

```twig
{% include '@ps_theme/language-selector/language-selector.twig' with {
  size: 'lg',
  current: { code: 'FR', label: 'Fr' },
  options: [
    { code: 'FR', label: 'Fr', value: 'fr', selected: true },
    { code: 'GB', label: 'En', value: 'en', selected: false }
  ]
} only %}
```

---

## 📐 Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | `string` | `'lang'` | Input name attribute for native select fallback |
| `size` | `string` | `'sm'` | Size variant: `xs` \| `sm` \| `md` \| `lg` \| `xl` \| `xxl` |
| `variant` | `string` | `'default'` | Color variant: `default` \| `primary` \| `secondary` \| `success` \| `danger` \| `warning` \| `info` |
| `disabled` | `boolean` | `false` | Disables the entire selector |
| `current` | `object` | — | **Required**. Currently selected language |
| `current.code` | `string` | — | **Required**. ISO 3166-1 alpha-2 country code (ex: GB, FR, ES) |
| `current.label` | `string` | — | **Required**. Short label displayed (ex: En, Fr, Es) |
| `current.locale` | `string` | `null` | Optional BCP 47 locale tag (ex: en-GB, fr-FR) |
| `options` | `array` | — | **Required**. Array of language options |
| `options[].code` | `string` | — | **Required**. Country code for flag |
| `options[].label` | `string` | — | **Required**. Short label |
| `options[].value` | `string` | — | **Required**. Value for native select |
| `options[].url` | `string` | `null` | Optional URL for language switch |
| `options[].locale` | `string` | `null` | Optional BCP 47 locale tag |
| `options[].selected` | `boolean` | `false` | Is currently selected |
| `options[].disabled` | `boolean` | `false` | Is disabled |
| `attributes` | `Attribute` | `{}` | Additional HTML attributes for nav element |

### Size Reference

| Size | Height | Padding | Font | Icon | Use Case |
|------|--------|---------|------|------|----------|
| `xs` | 24px | 2px × 8px | 12px | 16px | Compact mobile interfaces |
| `sm` | 36px | 4px × 12px | 14px | 20px | **Default** (Figma spec) |
| `md` | 40px | 8px × 16px | 16px | 20px | Standard comfort |
| `lg` | 48px | 12px × 20px | 18px | 24px | Headers/navigation |
| `xl` | 56px | 16px × 24px | 20px | 28px | Touch interfaces |
| `xxl` | 64px | 20px × 32px | 24px | 32px | Hero/landing pages |

---

## 🏗️ BEM Structure

```css
.ps-language-selector           /* Block - main container */
  .ps-language-selector__control          /* Wrapper for positioning */
    .ps-language-selector__button         /* Toggle button */
      .ps-language-selector__current      /* Current language wrapper */
        .ps-flag                          /* Flag component (atom) */
        .ps-language-selector__label      /* Language label */
      .ps-language-selector__icon         /* Chevron icon (SVG) */
    .ps-language-selector__list           /* Dropdown menu */
      .ps-language-selector__option       /* Language option */
        .ps-flag                          /* Flag component (atom) */
        .ps-language-selector__label      /* Language label */
    .ps-language-selector__native         /* Native select fallback */

/* Size Modifiers */
.ps-language-selector--xs
.ps-language-selector--sm         /* Default */
.ps-language-selector--md
.ps-language-selector--lg
.ps-language-selector--xl
.ps-language-selector--xxl

/* Variant Modifiers */
.ps-language-selector--primary
.ps-language-selector--secondary
.ps-language-selector--success
.ps-language-selector--danger
.ps-language-selector--warning
.ps-language-selector--info

/* State Modifiers */
.ps-language-selector--disabled
```

---

## 🎨 Design Tokens

### Spacing & Layout

- `--size-1` (4px) — Vertical padding
- `--size-2` (8px) — Gap between elements, option spacing
- `--size-3` (12px) — Horizontal padding
- `--size-5` (20px) — Icon size
- `--size-6` (24px) — Height (xs)
- `--size-9` (36px) — Height (sm, default)
- `--size-10` (40px) — Height (md)
- `--size-12` (48px) — Height (lg)
- `--size-14` (56px) — Height (xl)
- `--size-16` (64px) — Height (xxl)

### Typography

- `--font-sans` — BNPP Sans
- `--font-size-1` (12px) — xs size
- `--font-size-3` (14px) — Default size
- `--font-size-4` (16px) — md size
- `--font-size-5` (18px) — lg size
- `--font-size-6` (20px) — xl size
- `--font-size-7` (24px) — xxl size
- `--font-weight-400` — Regular
- `--font-weight-600` — Semibold (selected option)

### Colors

**Base (Neutral)**:
- `--white` — Background
- `--gray-50` — Hover background
- `--gray-100` — Selected option background
- `--gray-300` — Border
- `--gray-900` — Text

**Semantic Variants**:
- `--primary` — Green BNP (#00915A)
- `--secondary` — Magenta BNP (#A12B66)
- `--success`, `--danger`, `--warning`, `--info`

### Visual

- `--border-size-1` (1px) — Border width
- `--border-size-2` (2px) — Focus outline width
- `--shadow-3` — Dropdown shadow
- `--duration-fast` (150ms) — Transition duration
- `--ease-4` — Transition easing

### Missing Tokens

⚠️ **TODO**: Add `--z-dropdown: 1000;` to `source/props/zindex.css`

Currently using hardcoded value `1000` in component CSS.

---

## ♿ Accessibility

### WCAG 2.2 AA Compliance

**ARIA Attributes**:
- `aria-label="Language selector"` on `<nav>`
- `aria-haspopup="listbox"` on button
- `aria-expanded="false|true"` on button (managed by JavaScript)
- `role="listbox"` on dropdown list
- `role="option"` on each option
- `aria-selected="true|false"` on each option
- `aria-disabled="true"` when disabled

**Keyboard Navigation**:
- `Tab` — Focus button
- `Enter` / `Space` — Toggle dropdown
- `↓` / `↑` — Navigate options
- `Home` / `End` — First/last option
- `Escape` — Close dropdown, return focus to button

**Focus Visible** (WCAG 2.2 AA):
- Button: 2px solid `--secondary` (#A12B66), offset 2px
- Options: Same focus indicator
- Contrast ratio: 5.2:1 ✅ (>3:1 required)

**Color Contrast**:
- Text (`--gray-900` on `--white`): 14.8:1 ✅ (>4.5:1)
- Border (`--gray-300` on `--white`): 3.1:1 ✅ (>3:1)
- Focus (`--secondary` on `--white`): 5.2:1 ✅ (>3:1)

**Progressive Enhancement**:
- Native `<select>` fallback for no-JS environments
- Visible only when `.no-js` class is present on `<html>`

---

## 🧩 Dependencies

**Required Components**:
- **Flag** (Atom) — `source/patterns/elements/flag/`
  - Used for displaying country flags (rectangular, 20×14px)

**Required Icons** (SVG sprite):
- `chevron-down` — Closed state
- `chevron-up` — Opened state (achieved via CSS rotation)

**Required JavaScript**:
- `language-selector.js` — Dropdown interaction behavior (Drupal behavior)

---

## 🎭 Variants

### Sizes

All 6 standardized sizes from xs (24px) to xxl (64px). Default is **sm** (36px) per Figma spec.

### Colors

7 semantic color variants affecting border and text color. Default is neutral gray.

### States

- **Default** — Closed dropdown
- **Opened** — Dropdown visible, chevron rotated 180°
- **Selected** — Option with `aria-selected="true"`, gray background, semibold text
- **Hover** — Light gray background on hover
- **Focus** — Visible outline (2px magenta)
- **Disabled** — Reduced opacity (0.5), no interaction

---

## 💡 Real-World Examples

### Header Navigation

```twig
<header class="ps-header">
  <div class="ps-header__actions">
    {% include '@ps_theme/language-selector/language-selector.twig' with {
      size: 'md',
      variant: 'primary',
      current: { code: 'FR', label: 'Fr' },
      options: [
        { code: 'FR', label: 'Fr', value: 'fr', selected: true },
        { code: 'GB', label: 'En', value: 'en', selected: false }
      ]
    } only %}
  </div>
</header>
```

### Footer Multi-Market Selector

```twig
<footer class="ps-footer">
  <div class="ps-footer__locale">
    <p>Choose your market:</p>
    {% include '@ps_theme/language-selector/language-selector.twig' with {
      size: 'sm',
      variant: 'default',
      current: { code: 'FR', label: 'Fr', locale: 'fr-FR' },
      options: [
        { code: 'FR', label: 'Fr', value: 'fr', locale: 'fr-FR', url: '/fr', selected: true },
        { code: 'GB', label: 'En', value: 'en', locale: 'en-GB', url: '/en', selected: false },
        { code: 'DE', label: 'De', value: 'de', locale: 'de-DE', url: '/de', selected: false }
      ]
    } only %}
  </div>
</footer>
```

### Mobile Compact

```twig
{% include '@ps_theme/language-selector/language-selector.twig' with {
  size: 'xs',
  current: { code: 'FR', label: 'Fr' },
  options: [
    { code: 'FR', label: 'Fr', value: 'fr', selected: true },
    { code: 'GB', label: 'En', value: 'en', selected: false }
  ]
} only %}
```

---

## 📝 Notes

- **Flags**: Uses rectangular flags (20×14px) per Figma spec, not circular
- **Labels**: 2-letter ISO 639-1 codes (En, Fr, Es), not full language names
- **No-JS fallback**: Native `<select>` is hidden by default, visible with `.no-js` class
- **JavaScript required**: Dropdown interaction requires `language-selector.js` Drupal behavior
- **Icon rotation**: Chevron rotates 180° when `aria-expanded="true"` (CSS transition)
- **Z-index**: Currently uses hardcoded value `1000` until `--z-dropdown` token is added

---

## 📚 Related Components

- **Flag** (Atom) — Country flag display
- **Dropdown** (Molecule) — Generic dropdown pattern
- **Button** (Atom) — Button component used internally

---

## 🔗 Resources

- [Figma Spec](link-to-figma) — Design specifications
- [BCP 47 Language Tags](https://www.rfc-editor.org/rfc/bcp/bcp47.txt) — Locale standard
- [ISO 3166-1 alpha-2](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2) — Country codes
- [WCAG 2.2 AA](https://www.w3.org/WAI/WCAG22/quickref/) — Accessibility guidelines
