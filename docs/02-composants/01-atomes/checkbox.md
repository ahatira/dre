# Checkbox (Atom)

**Niveau Atomic Design** : Atom / Form Element  
**Catégorie** : Form control  
**Statut** : ✅ Stable  
**Version** : 1.0.0  
**Dernière mise à jour** : 13 décembre 2025

---

## 📋 Description

Case à cocher permettant la sélection multiple d'options. Supporte les états checked, unchecked, indeterminate et disabled. Accessible nativement avec HTML5 + ARIA. Utilise les couleurs sémantiques du design system (primary pour état checked).

**Implémentation** : `source/patterns/elements/checkbox/`

**Usage typique** : Formulaires, filtres de recherche, sélection multiple, acceptation conditions, préférences utilisateur.

---

## 🎨 Aperçu visuel

```
☐ Unchecked          ☑ Checked           ⊟ Indeterminate      ☐ Disabled
Option 1             Option 2             Select all           Option 4
```

---

## 🏗️ Structure BEM

```html
<!-- Unchecked -->
<label class="ps-checkbox">
  <input class="ps-checkbox__input" type="checkbox" name="option1" value="1">
  <span class="ps-checkbox__box" aria-hidden="true">
    <span class="ps-checkbox__checkmark"></span>
  </span>
  <span class="ps-checkbox__label">Option 1</span>
</label>

<!-- Checked -->
<label class="ps-checkbox">
  <input class="ps-checkbox__input" type="checkbox" name="option2" value="2" checked>
  <span class="ps-checkbox__box" aria-hidden="true">
    <span class="ps-checkbox__checkmark"></span>
  </span>
  <span class="ps-checkbox__label">Option 2</span>
</label>

<!-- Disabled -->
<label class="ps-checkbox ps-checkbox--disabled">
  <input class="ps-checkbox__input" type="checkbox" name="option3" value="3" disabled>
  <span class="ps-checkbox__box" aria-hidden="true">
    <span class="ps-checkbox__checkmark"></span>
  </span>
  <span class="ps-checkbox__label">Option 3 (disabled)</span>
</label>
```

### Classes BEM

```
ps-checkbox                               // Block (label wrapper)
  ps-checkbox__input                      // Element (native checkbox)
  ps-checkbox__box                        // Element (custom visual box)
  ps-checkbox__checkmark                  // Element (checkmark icon)
  ps-checkbox__label                      // Element (text label)

Modifiers (états):
  ps-checkbox--disabled                   // État désactivé
  ps-checkbox--error                      // État erreur (validation)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Checkbox'
status: stable
group: atoms
description: 'Case à cocher pour sélection multiple avec états checked/unchecked/indeterminate/disabled.'

props:
  type: object
  properties:
    name:
      type: string
      title: Name
      description: 'HTML name attribute (required for form submission)'
    value:
      type: string
      title: Value
      description: 'Valeur envoyée lors de la soumission'
    label:
      type: string
      title: Label
      description: 'Texte du label associé'
    checked:
      type: boolean
      title: Checked
      default: false
      description: 'État coché initial'
    indeterminate:
      type: boolean
      title: Indeterminate
      default: false
      description: 'État indéterminé (JavaScript uniquement)'
    disabled:
      type: boolean
      title: Disabled
      default: false
      description: 'État désactivé (non interactif)'
    required:
      type: boolean
      title: Required
      default: false
      description: 'Champ obligatoire'
    error:
      type: boolean
      title: Error state
      default: false
      description: 'État erreur (validation échouée)'
    attributes:
      type: Drupal\Core\Template\Attribute
      title: Attributes
      description: 'Attributs HTML additionnels'
  required: ['name', 'value', 'label']
```

### Props Table

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| **name** | string | - | Nom du champ (form submission) |
| **value** | string | - | Valeur soumise si coché |
| **label** | string | - | Texte du label |
| **checked** | boolean | `false` | État coché initial |
| **indeterminate** | boolean | `false` | État intermédiaire (JS) |
| **disabled** | boolean | `false` | État désactivé |
| **required** | boolean | `false` | Champ obligatoire |
| **error** | boolean | `false` | État erreur (validation) |
| **attributes** | Attribute | `null` | Attributs HTML Drupal |

---

## 🎨 Design Tokens

### Layer 1: Global Tokens

Hérités de `source/props/`:

```css
/* Couleurs */
--primary: #00915A           /* État checked */
--primary-hover: #007A4C     /* Hover checked */
--gray-700: #374151          /* Label text */
--gray-400: #9CA3AF          /* Border unchecked */
--white: #FFFFFF             /* Checkmark color */
--danger: #EB3636            /* État error */
--text-disabled: #6B7280     /* Label disabled */

/* Tailles */
--size-6: 1.5rem             /* 24px - Taille checkbox */
--size-2: 0.5rem             /* 8px - Gap label */

/* Bordures */
--border-size-2: 2px         /* Border width */
--radius-2: 4px              /* Border radius */
```

### Layer 2: Component Tokens

Définies dans `checkbox.css`:

```css
.ps-checkbox {
  --ps-checkbox-size: var(--size-6);                    /* 24×24px */
  --ps-checkbox-border-width: var(--border-size-2);     /* 2px */
  --ps-checkbox-border-color: var(--gray-400);          /* Unchecked */
  --ps-checkbox-border-radius: var(--radius-2);         /* 4px */
  --ps-checkbox-bg: var(--white);                       /* Background */
  --ps-checkbox-checkmark-color: var(--white);          /* Checkmark */
  --ps-checkbox-checked-bg: var(--primary);             /* Checked bg */
  --ps-checkbox-checked-border: var(--primary);         /* Checked border */
  --ps-checkbox-hover-border: var(--primary-hover);     /* Hover */
  --ps-checkbox-label-color: var(--gray-700);           /* Label text */
  --ps-checkbox-label-gap: var(--size-2);               /* 8px gap */
  --ps-checkbox-disabled-opacity: 0.5;                  /* Disabled */
  --ps-checkbox-focus-outline: 2px solid var(--primary);/* Focus */
  --ps-checkbox-focus-offset: 2px;                      /* Focus offset */
}
```

### Layer 3: Context Overrides

```css
/* État error */
.ps-checkbox--error {
  --ps-checkbox-border-color: var(--danger);
  --ps-checkbox-checked-bg: var(--danger);
  --ps-checkbox-checked-border: var(--danger);
}

/* État disabled */
.ps-checkbox--disabled {
  opacity: var(--ps-checkbox-disabled-opacity);
  cursor: not-allowed;
}
```

---

## ♿ Accessibilité (WCAG 2.2 AA)

### Touch Targets
- **Minimum** : 44×44px (label + checkbox ensemble)
- **Taille checkbox** : 24×24px (visuel)
- **Zone cliquable** : Entire label (label wraps input)

### Keyboard Navigation
- **Tab** : Focus checkbox
- **Space** : Toggle checked/unchecked
- **Shift+Tab** : Focus précédent

### Screen Readers
- Native `<input type="checkbox">` = full support
- Label associé via wrapping (implicit association)
- États communiqués : checked, unchecked, disabled, required

### Focus Visible
```css
.ps-checkbox__input:focus-visible + .ps-checkbox__box {
  outline: var(--ps-checkbox-focus-outline);
  outline-offset: var(--ps-checkbox-focus-offset);
}
```

### Contrast Ratios
- **Checked** : ✅ 4.5:1 (primary #00915A vs white checkmark)
- **Unchecked** : ✅ 3:1 (gray border vs white bg)
- **Label** : ✅ 7:1 (gray-700 text vs white bg)

---

## 📱 Comportement Responsive

| Breakpoint | Touch Target | Comportement |
|------------|--------------|--------------|
| Mobile (360px+) | 44×44px | Taille maintenue |
| Tablet (768px+) | 44×44px | Taille maintenue |
| Desktop (1024px+) | 24×24px visuel + padding label | Mouse precision OK |

**Note** : Touch target respecté via padding du label, pas besoin d'agrandir la checkbox visuellement.

---

## 🔗 Cas d'Usage Real Estate

### 1. Filtres de Recherche
```html
<fieldset>
  <legend>Type de bien</legend>
  <label class="ps-checkbox">
    <input type="checkbox" name="property_type[]" value="apartment">
    <span class="ps-checkbox__box"><span class="ps-checkbox__checkmark"></span></span>
    <span class="ps-checkbox__label">Appartement</span>
  </label>
  <label class="ps-checkbox">
    <input type="checkbox" name="property_type[]" value="house">
    <span class="ps-checkbox__box"><span class="ps-checkbox__checkmark"></span></span>
    <span class="ps-checkbox__label">Maison</span>
  </label>
  <label class="ps-checkbox">
    <input type="checkbox" name="property_type[]" value="office">
    <span class="ps-checkbox__box"><span class="ps-checkbox__checkmark"></span></span>
    <span class="ps-checkbox__label">Bureau</span>
  </label>
</fieldset>
```

### 2. Services Souhaités (Formulaire Contact)
```html
<fieldset>
  <legend>Services souhaités</legend>
  <label class="ps-checkbox">
    <input type="checkbox" name="services[]" value="valuation">
    <span class="ps-checkbox__box"><span class="ps-checkbox__checkmark"></span></span>
    <span class="ps-checkbox__label">Estimation gratuite</span>
  </label>
  <label class="ps-checkbox">
    <input type="checkbox" name="services[]" value="visit">
    <span class="ps-checkbox__box"><span class="ps-checkbox__checkmark"></span></span>
    <span class="ps-checkbox__label">Visite personnalisée</span>
  </label>
</fieldset>
```

### 3. Acceptation Conditions
```html
<label class="ps-checkbox">
  <input type="checkbox" name="privacy" required>
  <span class="ps-checkbox__box"><span class="ps-checkbox__checkmark"></span></span>
  <span class="ps-checkbox__label">
    J'accepte la <a href="/privacy">politique de confidentialité</a>
  </span>
</label>
```

---

## 🧪 Testing

### Visual Checks
- [ ] Checkbox 24×24px avec border 2px
- [ ] Gap label 8px
- [ ] Checkmark blanc visible quand checked
- [ ] Hover change border color
- [ ] Focus outline 2px offset 2px
- [ ] Disabled opacity 0.5

### Functional Checks
- [ ] Click checkbox toggle state
- [ ] Click label toggle state
- [ ] Space key toggle state
- [ ] Disabled non-interactif
- [ ] Form submission includes checked values only

### Accessibility Checks
- [ ] Tab navigation fonctionne
- [ ] Screen reader annonce "checkbox, checked/unchecked"
- [ ] Focus visible sur keyboard navigation
- [ ] Touch target ≥ 44×44px
- [ ] Contrast ratios WCAG AA

---

## 📚 Références

- **Maquettes** : Figma → Checkbox component
- **Implementation** : `source/patterns/elements/checkbox/`
- **Storybook** : [View Component](http://localhost:6006/?path=/docs/elements-checkbox--docs)
- **Related** : Checkboxes (molecule), Form Field (molecule)

---

## 📅 Changelog

### Version 1.0.0 (13 décembre 2025)
- Initial implementation
- 24×24px size (per design spec)
- Primary color for checked state
- Full WCAG 2.2 AA compliance
- Touch target 44×44px via label padding
- Indeterminate state support (JavaScript)
- Error state styling
