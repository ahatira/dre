# Components (UI Patterns)

> The theme's component system is built on **UI Patterns** (SDC-compatible). Each component is a self-contained unit: a Twig template, a typed YAML definition, optional SCSS, and story fixtures.

---

## Component Structure

Every component lives in `components/{name}/` and follows this layout:

```
components/button/
├── button.component.yml   ← Props + slots definition (SDC)
├── button.twig            ← Twig template
├── styles/
│   ├── button.scss        ← Component SCSS source (edit here)
│   └── button.css         ← Generated — never edit
└── stories/
    ├── button.default.story.yml
    └── button.disabled.story.yml
```

**`styles/` directory**: not all components have one. Only components with styles that cannot be expressed with Bootstrap utilities have a dedicated SCSS file.

---

## Component YAML (`*.component.yml`)

Follows the [Single Directory Components](https://www.drupal.org/docs/develop/theming-drupal/using-single-directory-components) (SDC) spec:

```yaml
# components/button/button.component.yml
$schema: 'https://git.drupalcode.org/project/drupal/-/raw/HEAD/core/modules/sdc/src/schemas/metadata.schema.json'
name: Button
status: experimental
description: 'Bootstrap 5 button component.'
props:
  type: object
  properties:
    label:
      type: string
      title: 'Button label'
    variant:
      type: string
      title: 'Bootstrap button variant'
      default: 'primary'
      enum: [primary, secondary, success, danger, warning, info, light, dark, link]
    size:
      type: string
      enum: ['', sm, lg]
    disabled:
      type: boolean
      default: false
slots:
  content:
    title: 'Button content (overrides label)'
```

**Typed props** are validated by Drupal — incorrect types cause render errors in development.

---

## Story Files (`*.story.yml`)

Stories provide fixture data for the UI Patterns library page and can be used in Storybook integrations:

```yaml
# components/button/stories/button.default.story.yml
name: 'Default button'
component: ui_suite_bnppre:button
props:
  label: 'Click me'
  variant: primary
```

---

## Component List

### Layout / Structure

| Component | Description |
|-----------|-------------|
| `accordion` | Bootstrap Accordion wrapper |
| `accordion_item` | Individual accordion panel |
| `card` | Card with image/header/body/footer slots |
| `card_body` | Card body slot |
| `card_group` | Responsive card grid group |
| `card_overlay` | Card with image overlay |

### Navigation

| Component | Description |
|-----------|-------------|
| `breadcrumb` | Bootstrap breadcrumb nav |
| `button` | Bootstrap button with variant/size props |
| `button_group` | Button group with alignment control |
| `button_toolbar` | Toolbar grouping multiple button groups |

### Content

| Component | Description |
|-----------|-------------|
| `alert` | Bootstrap alert with icon + dismiss |
| `badge` | Badge with background/pill variants |
| `blockquote` | Styled blockquote with source/author |

### Forms

| Component | Description |
|-----------|-------------|
| _(form components are handled via Twig templates and preprocess, not SDC components)_ | |

---

## How to Create a New Component

### 1. Create the directory

```bash
mkdir -p components/my_component/stories
mkdir -p components/my_component/styles
```

### 2. Write the YAML definition

```yaml
# components/my_component/my_component.component.yml
$schema: 'https://git.drupalcode.org/project/drupal/-/raw/HEAD/core/modules/sdc/src/schemas/metadata.schema.json'
name: My Component
status: experimental
props:
  type: object
  properties:
    title:
      type: string
      title: Title
    variant:
      type: string
      default: default
```

### 3. Write the Twig template

```twig
{# components/my_component/my_component.twig #}
{% set classes = ['my-component', variant ? 'my-component--' ~ variant] %}
<div{{ attributes.addClass(classes) }}>
  {% if title %}
    <h3 class="my-component__title">{{ title }}</h3>
  {% endif %}
  {{ content }}
</div>
```

**Rules**:
- Use `attributes.addClass()` to extend, never replace.
- Prefer Bootstrap utility classes over custom CSS.
- Keep logic minimal — move data computation to a preprocess hook.

### 4. Add SCSS (if Bootstrap utilities are insufficient)

```scss
// components/my_component/styles/my_component.scss
.my-component {
  // SCSS here — Bootstrap variables available

  &--variant {
    color: var(--bs-primary);
  }
}
```

Then rebuild: `npm run build:css`.

### 5. Add a story

```yaml
# components/my_component/stories/my_component.default.story.yml
name: Default
component: ui_suite_bnppre:my_component
props:
  title: 'My Component Title'
  variant: default
```

### 6. Clear cache

```bash
vendor/bin/drush cr
```

The component is now available in:
- UI Patterns Library: `/admin/appearance/ui/patterns`
- Layout Builder block library (if `ui_patterns_blocks` is enabled)
- Twig: `{{ include('ui_suite_bnppre:my_component', { title: 'Hello' }) }}`

---

## Using a Component in Twig

```twig
{# Inline include #}
{{ include('ui_suite_bnppre:button', {
  label: 'Submit',
  variant: 'primary',
}) }}

{# With slot override #}
{% embed 'ui_suite_bnppre:card' %}
  {% block content %}
    <p>Custom card body content.</p>
  {% endblock %}
{% endembed %}
```

---

## Component vs Template Decision

| Scenario | Use |
|----------|-----|
| Reusable UI pattern appearing in 2+ places | Component in `components/` |
| Drupal-specific element (form, node, block) needing Bootstrap markup | Twig template in `templates/` |
| Computed data or logic needed before render | Preprocess hook + Twig template |
| Pure CSS variant of an existing Bootstrap component | Bootstrap utility / SCSS only |
