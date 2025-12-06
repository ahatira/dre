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

## Tokens (réels)
- Couleurs : `--primary`, `--primary-hover`, `--primary-active`, `--border-default`, `--border-error`, `--text-inverse`
- Bordure : `--border-size-2`, rayon `--radius-round` (cercle)
- Tailles : case `--size-5` (20px), dot `--size-3` (12px) ou `--size-2` (8px) selon design

## CSS Variables (exemple aligné tokens)
```scss
--radio-size: var(--size-5);          /* 20px */
--radio-dot-size: var(--size-3);      /* 12px */
--radio-border-width: var(--border-size-2);
--radio-border-color: var(--border-default);
--radio-color: var(--primary);
--radio-dot-color: var(--text-inverse);
```

## A11y
- Groupes via `ps-radio-group` (molecule)
