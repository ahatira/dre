# FormElement (Molecule)

Type: Molecule / Component
Rôle: Regroupe label + field + helper + error (remplace l'ancien FormField).
Statut: ✅ Stable
Version: 1.1.0

---

## BEM
```
ps-form-element
  ps-form-element__label
  ps-form-element__input-wrapper
  ps-form-element__helper
  ps-form-element__error
  ps-form-element__error-icon

Modificateurs:
  ps-form-element--required | --error | --disabled
```

## API (YAML)
```yaml
name: 'PS FormElement'
status: stable
group: molecules
props:
  type: object
  properties:
    label: { type: string }
    type: { type: string, description: 'Field type forwarded to ps-field' }
    value: { type: string }
    placeholder: { type: string }
    helper: { type: string }
    error: { type: string }
    required: { type: boolean, default: false }
    disabled: { type: boolean, default: false }
    icon: { type: string }
    iconPosition: { type: string, default: 'right' }
    options: { type: array, description: 'Select options when type = select' }
    rows: { type: number, description: 'Textarea rows when type = textarea' }
    attributes: { type: Drupal\Core\Template\Attribute }
  required: ['type']
```

## Twig
```twig
<div class="ps-form-element {{ required ? 'ps-form-element--required' : '' }} {{ error ? 'ps-form-element--error' : '' }} {{ disabled ? 'ps-form-element--disabled' : '' }}">
  {% include '@elements/label/label.twig' with {
    text: label,
    forId: id,
    required: required,
    disabled: disabled,
    attributes: create_attribute().addClass('ps-form-element__label')
  } only %}

  <div class="ps-form-element__input-wrapper">
    {% include '@elements/field/field.twig' with field only %}
  </div>

  {% if helper and not error %}
    <div class="ps-form-element__helper">{{ helper }}</div>
  {% endif %}

  {% if error %}
    <div class="ps-form-element__error" role="alert">
      <svg class="ps-form-element__error-icon" viewBox="0 0 24 24" aria-hidden="true" width="16" height="16">
        <circle cx="12" cy="12" r="11" fill="none" stroke="currentColor" stroke-width="2"/>
        <path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
      <span>{{ error }}</span>
    </div>
  {% endif %}
</div>
```

## Variants
- Required vs facultatif
- État d'erreur (helper masqué)
- Désactivé

## Tokens
- Typography.label
- Spacing.stack.form_element_to_field

## A11y
- Label associé via `for`/`id`
- Helper lié via `aria-describedby` sauf si erreur
- Erreur en `role="alert"` avec `aria-live="polite"`

## Exemple
```twig
{% include '@components/form-element/form-element.twig' with {
  label: 'Votre email',
  type: 'email',
  placeholder: 'exemple@domaine.com',
  helper: 'Nous ne partagerons pas votre email.',
  required: true
} only %}
```