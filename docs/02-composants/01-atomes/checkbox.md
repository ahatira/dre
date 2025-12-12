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

## Tokens (réels)
- Couleurs : `--primary`, `--primary-hover`, `--primary-active`, `--primary-text`, `--border-default`, `--border-error`, `--text-inverse`
- Taille/espacement : `--size-5` (20px) pour la case, `--size-2|3` pour les gaps éventuels
- Bordures : `--border-size-2` (2px) pour le trait, rayon `--radius-2` (4px)
- État disabled : `--text-disabled` et opacité contrôlée dans le composant

## CSS Variables (exemple aligé tokens)
```scss
--checkbox-size: var(--size-5);            /* 20px */
--checkbox-border-width: var(--border-size-2);
--checkbox-border-color: var(--border-default);
--checkbox-radius: var(--radius-2);
--checkbox-checkmark-color: var(--text-inverse);
--checkbox-color: var(--primary);
```

## A11y
- Indicateurs focus
- Zone cliquable label + input
