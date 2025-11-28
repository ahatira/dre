# Radio (Atom)

Niveau: Atom / Element
Rôle: Bouton radio pour sélection unique.
Statut: ✅ Stable
Version: 1.0.0

---

## BEM
```
ps-radio
  ps-radio__input
  ps-radio__circle
  ps-radio__dot
  ps-radio__label

Modificateurs:
  ps-radio--checked | --disabled
  ps-radio--inline | --stacked
```

## API
```yaml
name: 'PS Radio'
status: stable
group: atoms
props:
  type: object
  properties:
    name: { type: string }
    value: { type: string }
    label: { type: string }
    checked: { type: boolean, default: false }
    disabled: { type: boolean, default: false }
    attributes: { type: Drupal\Core\Template\Attribute }
  required: ['name','value']
```

## Twig
```twig
<label class="ps-radio {{ disabled ? 'ps-radio--disabled' }}">
  <input class="ps-radio__input" type="radio" name="{{ name }}" value="{{ value }}" {% if checked %}checked{% endif %} {% if disabled %}disabled aria-disabled="true"{% endif %} />
  <span class="ps-radio__circle" aria-hidden="true"><span class="ps-radio__dot"></span></span>
  <span class="ps-radio__label">{{ label }}</span>
</label>
```

## Tokens
- `borders.radius.full` pour cercle
- `color.semantic.*` pour états

## CSS Variables
```scss
--ps-radio-size: 20px
--ps-primary: #00915A
--ps-border-default: #D6DBDE
--ps-border-width: 2px
--ps-radio-dot-size: 10px
```

## A11y
- Groupes via `ps-radio-group` (molecule)
