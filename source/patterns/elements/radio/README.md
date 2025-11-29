# Radio

**Version**: 1.0.0  
**Status**: ✅ Stable  
**Type**: Atom / Element

Bouton radio pour sélection unique dans un groupe d'options. Utilise les icon fonts BNP RE pour un rendu pixel perfect.

---

## Props

| Prop | Type | Default | Required | Description |
|------|------|---------|----------|-------------|
| `name` | `string` | `'option'` | ✅ | Nom du groupe radio (même name = sélection unique) |
| `value` | `string` | `'1'` | ✅ | Valeur unique de ce radio |
| `label` | `string` | `'Option label'` | ❌ | Texte affiché à côté du radio |
| `checked` | `boolean` | `false` | ❌ | État coché initial |
| `disabled` | `boolean` | `false` | ❌ | État désactivé |

---

## BEM Structure

```
.ps-radio                    ← Container <label>
  .ps-radio__input           ← Input radio (visually hidden)
  .ps-radio__circle          ← Icon circle (renders via ::before)
  .ps-radio__label           ← Text label

Modifiers:
  .ps-radio--disabled        ← Disabled state
```

---

## Design Tokens Used

### Colors
- `--brand-primary` — Couleur checked (vert BNP)
- `--ps-color-neutral-700` — Couleur unchecked
- `--ps-color-neutral-900` — Texte label
- `--ps-color-primary-600` — Focus outline

### Spacing
- `--size-2` (8px) — Gap entre circle et label
- `--size-5` (20px) — Taille du circle

### Typography
- `--font-sans` — Police label
- `--font-size-1` (14px) — Taille label
- `--font-size-2` (16px) — Taille icon
- `--leading-6` (24px) — Line-height label

### Transitions
- `--transition-fast` (0.15s) — Durée transitions

---

## Usage Examples

### Basic

```twig
{% include '@elements/radio/radio.twig' with {
  name: 'plan',
  value: 'basic',
  label: 'Plan Basic',
} %}
```

### Checked

```twig
{% include '@elements/radio/radio.twig' with {
  name: 'plan',
  value: 'premium',
  label: 'Plan Premium',
  checked: true,
} %}
```

### Disabled

```twig
{% include '@elements/radio/radio.twig' with {
  name: 'plan',
  value: 'enterprise',
  label: 'Plan Enterprise (bientôt disponible)',
  disabled: true,
} %}
```

### Radio Group

```twig
<fieldset>
  <legend>Choisissez votre plan</legend>
  {% include '@elements/radio/radio.twig' with {
    name: 'plan',
    value: 'basic',
    label: 'Plan Basic',
  } %}
  {% include '@elements/radio/radio.twig' with {
    name: 'plan',
    value: 'premium',
    label: 'Plan Premium',
    checked: true,
  } %}
  {% include '@elements/radio/radio.twig' with {
    name: 'plan',
    value: 'enterprise',
    label: 'Plan Enterprise',
  } %}
</fieldset>
```

---

## Real-World Use Cases

1. **Sélection de plan** — Basic, Premium, Enterprise
2. **Type de compte** — Particulier, Professionnel
3. **Mode de livraison** — Standard, Express, Retrait
4. **Préférences** — Oui, Non, Peut-être

---

## Accessibility

- ✅ Native `<input type="radio">` pour support clavier
- ✅ `aria-hidden="true"` sur le circle décoratif
- ✅ Label cliquable via `<label>` wrapper
- ✅ Focus visible via `outline` sur `:focus-visible`
- ✅ Disabled via attribut natif `disabled`
- ⚠️ **Grouper via `<fieldset>` + `<legend>`** pour clarté contextuelle

---

## Browser Support

✅ Tous navigateurs modernes (Chrome, Firefox, Safari, Edge)  
✅ Icon font BNP RE requis

---

## Notes

- **Icon Font**: Utilise `\e86a` (unchecked) et `\e869` (checked) de `bnpre-icons`
- **No extra markup**: Circle rendu via `::before` pseudo-element
- **Group behavior**: Radios avec même `name` forment un groupe de sélection unique
- **Minimal HTML**: Pas de classes conditionnelles superflues
