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
    <span class="ps-link__icon" data-icon="{{ icon }}" aria-hidden="true"></span>
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
--link-color: var(--primary)
--link-hover: var(--primary-hover)
--link-active: var(--primary-active)
--link-visited: #8E2A68
--link-transition-duration: var(--duration-fast)
```

## SCSS (states mapping)
```scss
.ps-link {
  color: var(--primary);
  transition: color var(--duration-fast) var(--ease-3);

  &:hover { color: var(--primary-hover); }
  &:active { color: var(--primary-active); }
  &:visited { color: var(--secondary); }

  &--disabled,
  &[aria-disabled='true'] {
    color: var(--text-disabled);
    pointer-events: none;
  }

  &--purple {
    color: var(--secondary);
    &:hover { color: var(--secondary-hover); }
    &:active { color: var(--secondary-active); }
    &:visited { color: var(--secondary); }
  }

  &--white {
    color: var(--text-inverse);
    &:hover { color: var(--overlay-brand-light); }
    &:active { color: var(--overlay-brand-medium); }
    &:visited { color: var(--text-inverse); }
  }

  &--purple&--disabled,
  &--purple[aria-disabled='true'] { color: var(--text-disabled); }

  &--white&--disabled,
  &--white[aria-disabled='true'] { color: var(--text-disabled); }
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
