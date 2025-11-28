# Link (Atom)

Niveau: Atom / Element
Rôle: Lien texte avec états (hover, active, visited) et option icône.
Statut: ✅ Stable
Version: 1.0.0

---

## BEM
```
ps-link
  ps-link__text
  ps-link__icon

Modificateurs:
  ps-link--green | --default
  ps-link--underline
  ps-link--with-icon
  ps-link--external
  ps-link--purple | --white
  ps-link--disabled
```

## API (YAML)
```yaml
name: 'PS Link'
status: stable
group: atoms
props:
  type: object
  properties:
    text: { type: string, title: Texte }
    url: { type: string, format: uri }
    color: { type: string, enum: ['green','purple','white','default'], default: 'green' }
    underline: { type: boolean, default: true }
    icon: { type: string }
    target: { type: string, enum: ['_self','_blank'], default: '_self' }
    rel: { type: string, default: '' }
    disabled: { type: boolean, default: false }
    attributes: { type: Drupal\Core\Template\Attribute }
  required: ['text','url']
```

## Twig
```twig
{# @ps_theme/ps-link/ps-link.twig #}
{% set classes = [
  'ps-link',
  'ps-link--' ~ (color ?? 'green'),
  underline ? 'ps-link--underline',
  icon ? 'ps-link--with-icon',
  target == '_blank' and not disabled ? 'ps-link--external',
  disabled ? 'ps-link--disabled'
] %}
{% set tag = disabled ? 'span' : 'a' %}
<{{ tag }}
  {{ attributes.addClass(classes) }}
  {% if not disabled %}href="{{ url }}"{% endif %}
  {% if target == '_blank' and not disabled %}target="_blank" rel="noopener noreferrer"{% endif %}
  {% if disabled %}aria-disabled="true"{% endif %}
>
  {% if icon %}
     {% include '@ps_theme/ps-icon/ps-icon.twig' with { name: icon, size: 20 } %}
  {% endif %}
  <span class="ps-link__text">{{ text }}</span>
</{{ tag }}>
```

## Variants
- Couleur: green (par défaut), purple, white, default
- Icône: à gauche/droite (via CSS)
- External: ajoute icône et `rel` sécurisé
- Disabled: `ps-link--disabled` + `aria-disabled="true"`

## Tokens
- `color.interactive.link.*`
- `typography.text.*`

## CSS Variables
```scss
--ps-primary: #00915A
--ps-link-hover: #006B43
--ps-link-active: #004A2D
--ps-link-visited: #8E2A68
--ps-transition-standard: 150ms ease
```

## SCSS (states mapping)
```scss
.ps-link {
  color: var(--ps-link);
  transition: color var(--ps-transition-standard);

  &:hover { color: var(--ps-link-hover); }
  &:active { color: var(--ps-link-active); }
  &:visited { color: var(--ps-link-visited); }

  &--disabled,
  &[aria-disabled='true'] { color: var(--ps-link-disabled); pointer-events: none; }

  &--purple {
    color: var(--ps-link-purple);
    &:hover { color: var(--ps-link-purple-hover); }
    &:active { color: var(--ps-link-purple-active); }
    &:visited { color: var(--ps-link-purple-visited); }
  }

  &--white {
    color: var(--ps-link-inverse);
    &:hover { color: var(--ps-link-inverse-hover); }
    &:active { color: var(--ps-link-inverse-active); }
    &:visited { color: var(--ps-link-inverse-visited); }
  }

  &--purple&--disabled,
  &--purple[aria-disabled='true'] { color: var(--ps-link-purple-disabled); }

  &--white&--disabled,
  &--white[aria-disabled='true'] { color: var(--ps-link-inverse-disabled); }
}
```

## Accessibilité
- Contraste AA (≥ 4.5:1)
- Indicateurs de focus visibles
- Pour _blank_: ajouter texte caché "(ouvre un nouvel onglet)"

## Exemples
```twig
{% include '@ps_theme/ps-link/ps-link.twig' with { text: 'Voir plus', url: '/news', icon: 'arrow-right' } %}
```
