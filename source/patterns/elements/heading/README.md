# ps-heading

Atom: Semantic typographic headings (h1-h6)

## API

- `text` (string) - Heading text content **required**
- `level` (string) - h1|h2|h3|h4|h5|h6 (default: h2)
- `align` (string) - left|center|right (default: left)
- `visuallyHidden` (boolean) - Screen reader only (default: false)
- `icon` (string) - Icon name (optional, e.g., 'icon-pin-map')
- `iconPosition` (string) - left|right (default: left)

## Heading Hierarchy

- **h1**: 48px - Main page title
- **h2**: 36px - Section titles
- **h3**: 28px - Subsection titles
- **h4**: 24px - Content block titles
- **h5**: 20px - Small headings
- **h6**: 16px - Micro titles (uppercase)

## BEM

- Block: `ps-heading`
- Elements: `ps-heading__text`
- Modifiers: 
  - Level: `--h1`, `--h2`, `--h3`, `--h4`, `--h5`, `--h6`
  - Alignment: `--align-left`, `--align-center`, `--align-right`
  - Visibility: `--visually-hidden`
  - With icon: `--with-icon`

## Usage

```twig
{# Basic heading #}
{% include '@ps_theme/heading/heading.twig' with { 
  text: 'Section title',
  level: 'h2'
} %}

{# Centered h1 #}
{% include '@ps_theme/heading/heading.twig' with { 
  text: 'Page title',
  level: 'h1',
  align: 'center'
} %}
{# Screen reader only #}
{% include '@ps_theme/heading/heading.twig' with { 
  text: 'Navigation',
  level: 'h2',
  visuallyHidden: true
} %}

{# With icon left #}
{% include '@ps_theme/heading/heading.twig' with { 
  text: 'Localisation',
  level: 'h2',
  icon: 'icon-pin-map'
} %}

{# With icon right #}
{% include '@ps_theme/heading/heading.twig' with { 
  text: 'Voir les détails',
  level: 'h3',
  icon: 'icon-arrow-right',
  iconPosition: 'right'
} %}
```}
```

## Tokens

Uses:
- `--font-sans` (BNPP Sans)
- `--font-size-*` (1/3/5/6/8/10 for h6/h5/h4/h3/h2/h1)
- `--font-weight-600` (h5/h6), `--font-weight-700` (h1-h4)
- `--leading-tight` (1.25), `--leading-snug` (1.375), `--leading-normal` (1.5)
- `--tracking-wide` (0.025em for h6)
- `--gray-900` (#1F2A33)
- `--size-6` (24px margin-bottom)

## Accessibility

- Always use proper semantic hierarchy (h1 → h2 → h3...)
- Only one h1 per page
- Use `visuallyHidden: true` for structural headings that aren't needed visually
- Avoid skipping levels (h1 → h3)

## Typography Scale

```
h1: 48px / 1.25 / 700
h2: 36px / 1.25 / 700
h3: 28px / 1.375 / 700
h4: 24px / 1.375 / 700
h5: 20px / 1.5 / 600
h6: 16px / 1.5 / 600 / uppercase
```
