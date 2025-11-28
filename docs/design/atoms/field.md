# Field (Atom)

Niveau: Atom / Element
Rôle: Champ de base (input/select/textarea) sans label/helper.
Statut: ✅ Stable
Version: 1.0.0

---

## BEM
```
ps-field
  ps-field__input
  ps-field__icon
  ps-field__error

Modificateurs:
  ps-field--text | --number | --email | --search
  ps-field--select | --dropdown
  ps-field--textarea
  ps-field--error | --disabled | --filled | --hover | --focus | --done
  ps-field--icon-left | --icon-right
```

## API (YAML)
```yaml
name: 'PS Field'
status: stable
group: atoms
props:
  type: object
  properties:
    type: { type: string, enum: ['text','number','email','search','select','textarea'], default: 'text' }
    value: { type: string }
    placeholder: { type: string }
    disabled: { type: boolean, default: false }
    error: { type: string }
    icon: { type: string }
    iconPosition: { type: string, enum: ['left','right'], default: 'right' }
    attributes: { type: Drupal\Core\Template\Attribute }
```

## Twig
```twig
{# @ps_theme/ps-field/ps-field.twig #}
{% set classes = [
  'ps-field',
  'ps-field--' ~ (type ?? 'text'),
  error ? 'ps-field--error',
  disabled ? 'ps-field--disabled',
  value ? 'ps-field--filled',
  icon ? 'ps-field--icon-' ~ (iconPosition ?? 'right')
] %}
<div {{ attributes.addClass(classes) }}>
  {% if icon and (iconPosition ?? 'right') == 'left' %}
    {% include '@ps_theme/ps-icon/ps-icon.twig' with { name: icon, size: 20 } %}
  {% endif %}

  {% if type == 'textarea' %}
    <textarea class="ps-field__input" placeholder="{{ placeholder }}" {% if disabled %}disabled aria-disabled="true"{% endif %}>{{ value }}</textarea>
  {% elseif type == 'select' %}
    <div class="ps-field__input" role="combobox" aria-expanded="false" aria-haspopup="listbox">{{ value }}</div>
  {% else %}
    <input class="ps-field__input" type="{{ type }}" value="{{ value }}" placeholder="{{ placeholder }}" {% if disabled %}disabled aria-disabled="true"{% endif %} />
  {% endif %}

  {% if icon and (iconPosition ?? 'right') == 'right' %}
    {% include '@ps_theme/ps-icon/ps-icon.twig' with { name: icon, size: 20 } %}
  {% endif %}

  {% if error %}<div class="ps-field__error" role="alert">{{ error }}</div>{% endif %}
</div>
```

## Variants
- Input: text/number/email/search
- Select/Dropdown (combobox)
- Textarea
- States: default, placeholder, hover, focus, done, error, disabled
- Icon position: left or right

## Tokens
- `borders.preset.field.*`
- `spacing.semantic.field.*`

## CSS Variables
```scss
--ps-border-default: #D6DBDE
--ps-border-focus: #0288D1
--ps-border-error: #EB3636
--ps-field-padding: 8px 12px
--ps-field-height: 40px
--ps-border-width: 2px
```

## Accessibilité
- Rôles/ARIA corrects pour combobox
- Label géré au niveau molecule `form-field`

## Exemples
```twig
{% include '@ps_theme/ps-field/ps-field.twig' with { type: 'search', placeholder: 'Chercher…', icon: 'search' } %}
```
