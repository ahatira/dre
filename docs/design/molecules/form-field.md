# FormField (Molecule)

Type: Molecule / Component
Rôle: Regroupe label + field + helper + error.
Statut: ✅ Stable
Version: 1.0.0

---

## BEM
```
ps-form-field
  ps-form-field__label
  ps-form-field__input-wrapper
  ps-form-field__input
  ps-form-field__helper
  ps-form-field__error
  ps-form-field__icon

Modificateurs:
  ps-form-field--required | --error | --disabled
  ps-form-field--inline | --stacked
```

## API (YAML)
```yaml
name: 'PS FormField'
status: stable
group: molecules
props:
  type: object
  properties:
    label: { type: string }
    field: { type: object, description: 'Props pass-through to ps-field' }
    helperText: { type: string }
    error: { type: string }
    required: { type: boolean, default: false }
    disabled: { type: boolean, default: false }
    icon: { type: string }
    attributes: { type: Drupal\Core\Template\Attribute }
  required: ['label','field']
```

## Twig
```twig
<div class="ps-form-field {{ required ? 'ps-form-field--required' }} {{ error ? 'ps-form-field--error' }} {{ disabled ? 'ps-form-field--disabled' }}">
  <label class="ps-form-field__label">{{ label }}</label>
  <div class="ps-form-field__input-wrapper">
    {% include '@ps_theme/ps-field/ps-field.twig' with field %}
    {% if icon %}
      {% include '@ps_theme/ps-icon/ps-icon.twig' with { name: icon, size: 20, ariaLabel: '' } %}
    {% endif %}
  </div>
  {% if helperText %}<div class="ps-form-field__helper">{{ helperText }}</div>{% endif %}
  {% if error %}<div class="ps-form-field__error" role="alert">{{ error }}</div>{% endif %}
</div>
```

## Variants
- Inline vs Stacked
- With icon

## Tokens
- Typography.label
- Spacing.stack.form_field_to_field

## A11y
- Label associé, messages d'erreur `role="alert"`

## Exemple
```twig
{% include '@ps_theme/ps-form-field/ps-form-field.twig' with {
  label: 'Votre email',
  field: { type: 'email', placeholder: 'exemple@domaine.com' },
  helperText: 'Nous ne partagerons pas votre email.',
} %}
```
