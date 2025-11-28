# ps-badge

Atom: Compact indicator for status/labels

## API

- `text` (string) - Badge text **required**
- `icon` (string) - Icon name (optional)
- `variant` (string) - default|primary|secondary|gold|info|success|warning|danger (default: default)
- `pill` (boolean) - Rounded pill shape (default: false)
- `url` (string) - Link URL (renders `<a>`)
- `size` (string) - small|medium|large (default: medium)

## Variants

- **default**: bg=gray-200, color=gray-600
- **primary**: bg=brand-primary (#00915A), color=white
- **secondary**: bg=purple (#A12B66), color=white
- **gold**: bg=#D1AE6E, color=white
- **info**: bg=#B3E5FC, color=#0277BD
- **success**: bg=#C5F4E9, color=#0E7A5F
- **warning**: bg=#FFE0B2, color=#E65100
- **danger**: bg=#FFCDD2, color=#C62828

## BEM

- Block: `ps-badge`
- Elements: `ps-badge__icon`, `ps-badge__text`
- Modifiers: `--default`, `--primary`, `--secondary`, `--gold`, `--info`, `--success`, `--warning`, `--danger`, `--small`, `--medium`, `--large`, `--pill`

## Usage

```twig
{# Simple badge #}
{% include '@ps_theme/badge/badge.twig' with { text: 'Label' } %}

{# Primary with icon #}
{% include '@ps_theme/badge/badge.twig' with { 
  text: 'Primary', 
  variant: 'primary',
  icon: 'check'
} %}

{# Pill link #}
{% include '@ps_theme/badge/badge.twig' with { 
  text: 'View more', 
  variant: 'gold',
  pill: true,
  url: '/items'
} %}
```

## Tokens

Uses `--gray-*`, `--brand-primary`, `--bnp-accent-magenta`, `--white`, `--size-*`, `--font-*`, `--radius-*`, `--ease-*`.
