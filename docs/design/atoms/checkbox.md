# Checkbox (Atom)

Niveau: Atom / Element
Rôle: Case à cocher avec états.
Statut: ✅ Stable
Version: 1.0.0

---

## BEM
```
ps-checkbox
  ps-checkbox__input
  ps-checkbox__box
  ps-checkbox__checkmark
  ps-checkbox__label

Modificateurs:
  ps-checkbox--checked | --unchecked | --disabled | --indeterminate
  ps-checkbox--inline | --stacked
```

## API
```yaml
name: 'PS Checkbox'
status: stable
group: atoms
props:
  type: object
  properties:
    name: { type: string }
    value: { type: string }
    label: { type: string }
    checked: { type: boolean, default: false }
    indeterminate: { type: boolean, default: false }
    disabled: { type: boolean, default: false }
    attributes: { type: Drupal\Core\Template\Attribute }
  required: ['name','value']
```

## Twig
```twig
<label class="ps-checkbox {{ disabled ? 'ps-checkbox--disabled' }}">
  <input class="ps-checkbox__input" type="checkbox" name="{{ name }}" value="{{ value }}" {% if checked %}checked{% endif %} {% if disabled %}disabled aria-disabled="true"{% endif %} />
  <span class="ps-checkbox__box" aria-hidden="true"></span>
  <span class="ps-checkbox__label">{{ label }}</span>
</label>
```

## Tokens
- `borders.*`, `color.semantic.*`

## CSS Variables
```scss
--ps-checkbox-size: 20px
--ps-primary: #00915A
--ps-border-default: #D6DBDE
--ps-border-width: 2px
--ps-border-radius: 2px
--ps-checkbox-checkmark-color: #FFFFFF
```

## A11y
- Indicateurs focus
- Zone cliquable label + input
