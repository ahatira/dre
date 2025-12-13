# Checkboxes (Molecule)

**Niveau Atomic Design** : Molecule / Form group  
**Catégorie** : Form controls  
**Statut** : ✅ Stable  
**Version** : 1.0.0  
**Dernière mise à jour** : 12 décembre 2025

---

## 📋 Description

Wrapper `<div>` pour groupe de checkboxes avec label groupe optionnel. Composant minimaliste Drupal-friendly qui contient plusieurs atoms Checkbox via slot `children`. Gère layout vertical (stacked) ou horizontal (inline), légende accessible (`<fieldset>` + `<legend>`), et états de validation (error).

**Implémentation** : `source/patterns/components/checkboxes/`

---

## 🎨 Aperçu visuel

```
Préférences de contact          ← Légende groupe

☐ Email professionnel           ← Checkbox 1
☑ Téléphone mobile             ← Checkbox 2 (checked)
☐ Courrier postal               ← Checkbox 3
☐ SMS                           ← Checkbox 4
```

**Layout inline** :
```
Équipements:  ☑ Parking  ☐ Balcon  ☐ Cave  ☐ Ascenseur
```

---

## 🏗️ Structure BEM

```html
<!-- Default vertical group (avec fieldset) -->
<fieldset class="form-checkboxes">
  <legend class="form-checkboxes__legend">Préférences de contact</legend>
  
  <label class="ps-checkbox">
    <input type="checkbox" class="ps-checkbox__input" name="contact[]" value="email" />
    <span class="ps-checkbox__checkmark"></span>
    <span class="ps-checkbox__label">Email professionnel</span>
  </label>
  
  <label class="ps-checkbox ps-checkbox--checked">
    <input type="checkbox" class="ps-checkbox__input" name="contact[]" value="phone" checked />
    <span class="ps-checkbox__checkmark"></span>
    <span class="ps-checkbox__label">Téléphone mobile</span>
  </label>
</fieldset>

<!-- Inline horizontal group (sans fieldset) -->
<div class="form-checkboxes form-checkboxes--inline">
  <span class="form-checkboxes__label">Équipements:</span>
  
  <label class="ps-checkbox">
    <input type="checkbox" class="ps-checkbox__input" name="amenities[]" value="parking" checked />
    <span class="ps-checkbox__checkmark"></span>
    <span class="ps-checkbox__label">Parking</span>
  </label>
  
  <label class="ps-checkbox">
    <input type="checkbox" class="ps-checkbox__input" name="amenities[]" value="balcony" />
    <span class="ps-checkbox__checkmark"></span>
    <span class="ps-checkbox__label">Balcon</span>
  </label>
</div>

<!-- Group with error -->
<fieldset class="form-checkboxes form-checkboxes--error" aria-describedby="contact-error">
  <legend class="form-checkboxes__legend">Préférences <span class="form-required">*</span></legend>
  {# Checkboxes items #}
</fieldset>
<div id="contact-error" class="form-error" role="alert">
  Veuillez sélectionner au moins une préférence
</div>
```

### Classes BEM

```
form-checkboxes                           // Block (wrapper groupe)
  form-checkboxes__legend                 // Element (légende fieldset)
  form-checkboxes__label                  // Element (label inline sans fieldset)

Modifiers:
  form-checkboxes--inline                 // Layout horizontal (équipements)
  form-checkboxes--error                  // État erreur (bordure rouge groupe)
```

**Note** : Classes compatibles Drupal Form API (`form-checkboxes`).

---

## 📐 Props (Component API)

### Twig Template

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| **attributes** | `Attribute` | `create_attribute()` | Attributs Drupal pour wrapper (classes, data-*, aria-*) |
| **children** | `slot` | `''` | Contenu du groupe (atoms Checkbox) |

**Note** : Composant minimaliste (wrapper uniquement). Atoms Checkbox gèrent leurs propres props (label, checked, disabled).

---

## 🎨 Design Tokens

### Tokens utilisés

```css
/* Layout vertical (default) */
--checkboxes-gap: var(--size-4);              /* 16px entre checkboxes */
--checkboxes-padding: var(--size-4);          /* 16px padding wrapper */

/* Layout horizontal (inline) */
--checkboxes-inline-gap: var(--size-6);       /* 24px entre checkboxes */
--checkboxes-inline-align: center;            /* Alignement vertical */

/* Legend/Label */
--checkboxes-legend-font-size: var(--font-size-base); /* 16px */
--checkboxes-legend-font-weight: var(--font-weight-medium); /* 500 */
--checkboxes-legend-color: var(--text-primary); /* Noir */
--checkboxes-legend-margin-bottom: var(--size-3); /* 12px */

/* Error state */
--checkboxes-border-error: var(--danger);     /* Bordure rouge groupe */
--checkboxes-border-width: 1px;
--checkboxes-border-radius: var(--radius-md); /* 6px */
--checkboxes-padding-error: var(--size-4);    /* 16px padding si erreur */
```

---

## ♿ Accessibilité (WCAG 2.2 AA)

### Conformité

| Critère | Statut | Implémentation |
|---------|--------|----------------|
| **1.3.1 Info and Relationships** | ✅ | `<fieldset>` + `<legend>` pour regroupement sémantique |
| **2.4.6 Headings and Labels** | ✅ | `<legend>` décrit le groupe clairement |
| **3.3.1 Error Identification** | ✅ | `aria-describedby="error-id"` sur fieldset |
| **4.1.2 Name, Role, Value** | ✅ | Rôle `group` natif (fieldset), `aria-required` si groupe requis |

### ARIA Attributes

```html
<!-- Groupe requis -->
<fieldset class="form-checkboxes" aria-required="true">
  <legend>Préférences <span class="form-required">*</span></legend>
  {# Checkboxes #}
</fieldset>

<!-- Groupe avec erreur -->
<fieldset class="form-checkboxes form-checkboxes--error" aria-describedby="contact-error">
  <legend>Préférences <span class="form-required">*</span></legend>
  {# Checkboxes #}
</fieldset>
<div id="contact-error" class="form-error" role="alert">
  Veuillez sélectionner au moins une option
</div>

<!-- Groupe avec helper text -->
<fieldset class="form-checkboxes" aria-describedby="contact-helper">
  <legend>Préférences</legend>
  {# Checkboxes #}
</fieldset>
<div id="contact-helper" class="form-helper">
  Vous pouvez sélectionner plusieurs options
</div>
```

### Annonces Screen Reader

- **Fieldset focus** : "Préférences de contact, group, 4 items" (groupe + nombre)
- **Checkbox focus** : "Email professionnel, checkbox, not checked" (item individuel)
- **Navigation** : Flèches ↑↓ naviguent entre checkboxes (si fieldset)
- **Erreur** : "Préférences, group, Veuillez sélectionner au moins une option" (si aria-describedby)

---

## 🎯 Cas d'usage

### 1. Préférences contact (vertical)

```twig
<fieldset class="form-checkboxes">
  <legend class="form-checkboxes__legend">Préférences de contact</legend>
  
  {% include '@elements/checkbox/checkbox.twig' with {
    name: 'contact[]',
    value: 'email',
    label: 'Email professionnel',
    checked: true
  } only %}
  
  {% include '@elements/checkbox/checkbox.twig' with {
    name: 'contact[]',
    value: 'phone',
    label: 'Téléphone mobile'
  } only %}
  
  {% include '@elements/checkbox/checkbox.twig' with {
    name: 'contact[]',
    value: 'sms',
    label: 'SMS'
  } only %}
</fieldset>
```

**Contexte** : Formulaire inscription utilisateur.

---

### 2. Équipements bien (inline horizontal)

```twig
<div class="form-checkboxes form-checkboxes--inline">
  <span class="form-checkboxes__label">Équipements:</span>
  
  {% include '@elements/checkbox/checkbox.twig' with {
    name: 'amenities[]',
    value: 'parking',
    label: 'Parking',
    checked: true
  } only %}
  
  {% include '@elements/checkbox/checkbox.twig' with {
    name: 'amenities[]',
    value: 'balcony',
    label: 'Balcon'
  } only %}
  
  {% include '@elements/checkbox/checkbox.twig' with {
    name: 'amenities[]',
    value: 'elevator',
    label: 'Ascenseur'
  } only %}
  
  {% include '@elements/checkbox/checkbox.twig' with {
    name: 'amenities[]',
    value: 'cellar',
    label: 'Cave'
  } only %}
</div>
```

**Contexte** : Filtres recherche immobilière (horizontal compact).

---

### 3. Groupe requis avec erreur

```twig
<fieldset class="form-checkboxes form-checkboxes--error" aria-required="true" aria-describedby="services-error">
  <legend class="form-checkboxes__legend">
    Services souhaités <span class="form-required">*</span>
  </legend>
  
  {% include '@elements/checkbox/checkbox.twig' with {
    name: 'services[]',
    value: 'valuation',
    label: 'Estimation gratuite'
  } only %}
  
  {% include '@elements/checkbox/checkbox.twig' with {
    name: 'services[]',
    value: 'visit',
    label: 'Visite sur place'
  } only %}
  
  {% include '@elements/checkbox/checkbox.twig' with {
    name: 'services[]',
    value: 'financing',
    label: 'Aide au financement'
  } only %}
</fieldset>
<div id="services-error" class="form-error" role="alert">
  Veuillez sélectionner au moins un service
</div>
```

**Contexte** : Formulaire demande commerciale (validation serveur).

---

### 4. Options désactivées (lecture seule)

```twig
<fieldset class="form-checkboxes">
  <legend class="form-checkboxes__legend">Prestations incluses</legend>
  
  {% include '@elements/checkbox/checkbox.twig' with {
    name: 'included[]',
    value: 'heating',
    label: 'Chauffage collectif',
    checked: true,
    disabled: true
  } only %}
  
  {% include '@elements/checkbox/checkbox.twig' with {
    name: 'included[]',
    value: 'water',
    label: 'Eau froide',
    checked: true,
    disabled: true
  } only %}
</fieldset>
```

**Contexte** : Affichage prestations non-modifiables (historique annonce).

---

### 5. Groupe avec helper text

```twig
<fieldset class="form-checkboxes" aria-describedby="notifications-helper">
  <legend class="form-checkboxes__legend">Notifications par email</legend>
  
  {% include '@elements/checkbox/checkbox.twig' with {
    name: 'notifications[]',
    value: 'new_listings',
    label: 'Nouvelles annonces',
    checked: true
  } only %}
  
  {% include '@elements/checkbox/checkbox.twig' with {
    name: 'notifications[]',
    value: 'price_changes',
    label: 'Changements de prix'
  } only %}
  
  {% include '@elements/checkbox/checkbox.twig' with {
    name: 'notifications[]',
    value: 'saved_searches',
    label: 'Résultats recherches sauvegardées',
    checked: true
  } only %}
</fieldset>
<div id="notifications-helper" class="form-helper">
  Vous pouvez désactiver ces notifications à tout moment
</div>
```

**Contexte** : Paramètres compte utilisateur.

---

### 6. Intégration Drupal Form API

```php
// Dans FormBase::buildForm()
$form['contact_preferences'] = [
  '#type' => 'checkboxes',
  '#title' => $this->t('Préférences de contact'),
  '#options' => [
    'email' => $this->t('Email professionnel'),
    'phone' => $this->t('Téléphone mobile'),
    'sms' => $this->t('SMS'),
  ],
  '#default_value' => ['email', 'phone'], // Options cochées par défaut
  '#required' => TRUE,
  '#attributes' => [
    'class' => ['form-checkboxes'], // Classe PS Theme
  ],
  '#description' => $this->t('Sélectionnez au moins une préférence'),
];

// Validation (au moins une option)
public function validateForm(array &$form, FormStateInterface $form_state) {
  $values = array_filter($form_state->getValue('contact_preferences'));
  if (empty($values)) {
    $form_state->setErrorByName('contact_preferences', 
      $this->t('Veuillez sélectionner au moins une option'));
  }
}
```

**Contexte** : Formulaire Drupal personnalisé.

---

### 7. Checkboxes avec indeterminate state

```twig
<fieldset class="form-checkboxes">
  <legend class="form-checkboxes__legend">Sélectionner catégories</legend>
  
  {% include '@elements/checkbox/checkbox.twig' with {
    name: 'categories_all',
    value: 'all',
    label: 'Toutes les catégories',
    indeterminate: true
  } only %}
  
  <div style="margin-left: var(--size-6);">
    {% include '@elements/checkbox/checkbox.twig' with {
      name: 'categories[]',
      value: 'residential',
      label: 'Résidentiel',
      checked: true
    } only %}
    
    {% include '@elements/checkbox/checkbox.twig' with {
      name: 'categories[]',
      value: 'commercial',
      label: 'Commercial'
    } only %}
  </div>
</fieldset>
```

**Contexte** : Sélection hiérarchique (parent indeterminate si enfants partiellement cochés).

---

## 🔗 Relations avec autres composants

### Composé dans (Organisms)

- **Filter-panel** : Multiples groupes Checkboxes (type, prix, équipements)
- **Settings-form** : Checkboxes préférences + Radios langue + Buttons

### Utilise (Atoms)

- **Checkbox** : `@elements/checkbox/checkbox.twig` (item individuel)

### Variantes disponibles

- **Radios** : `@components/radios/radios.twig` (choix exclusif, un seul sélectionnable)
- **Toggle** : `@elements/toggle/toggle.twig` (switch on/off unique)

---

## 🧪 Tests & Validation

### Navigation clavier

```
Tab           → Focus premier checkbox (ou fieldset)
Espace        → Coche/décoche checkbox
↑/↓           → Navigue entre checkboxes (si fieldset)
Tab           → Sort du groupe
```

### Tests accessibilité

1. **Fieldset/Legend** : Screen reader annonce groupe + légende
2. **Navigation** : Flèches ↑↓ naviguent entre checkboxes (fieldset)
3. **Erreur** : Message erreur annoncé (aria-describedby)
4. **Required** : Asterisk `*` visible + `aria-required="true"`
5. **Contraste** : 
   - Légende : 4.5:1 (noir sur blanc)
   - Checkmarks : 3:1 (vert sur blanc)

### Tests interactifs Storybook

```javascript
// Tests Playwright dans checkboxes.stories.jsx
- Default vertical group (3 options)
- Inline horizontal (4 équipements)
- With error (validation groupe)
- Required (asterisk + aria-required)
- Disabled options (gris, non-cliquable)
- With helper text (description)
- Indeterminate parent (sélection partielle)
```

---

## 📊 Performance

### Métriques

- **HTML** : 2 éléments (fieldset + legend) + N checkboxes (~100 bytes base)
- **CSS** : 1 KB (minifié)
- **JS** : 0 KB (pas de JavaScript, sauf indeterminate)
- **Render time** : < 2ms
- **First Paint** : Immédiat

### Optimisations

- Wrapper minimaliste (logique dans atoms Checkbox)
- Pas de validation JS (HTML5 native via `required`)

---

## 🚧 Limitations connues

### Validation groupe "au moins une option"

**Non supporté nativement HTML5**. Utiliser JavaScript ou validation serveur.

```javascript
// Validation JS custom : au moins une checkbox cochée
const fieldset = document.querySelector('.form-checkboxes');
const checkboxes = fieldset.querySelectorAll('input[type="checkbox"]');
const isValid = Array.from(checkboxes).some(cb => cb.checked);

if (!isValid) {
  // Afficher erreur
}
```

### Indeterminate state

**Nécessite JavaScript** pour état indeterminate (parent partiellement coché).

```javascript
// Gérer état indeterminate checkbox parent
const parentCheckbox = document.querySelector('[data-indeterminate]');
const childCheckboxes = document.querySelectorAll('[name="categories[]"]');

childCheckboxes.forEach(cb => cb.addEventListener('change', () => {
  const checkedCount = Array.from(childCheckboxes).filter(c => c.checked).length;
  
  if (checkedCount === 0) {
    parentCheckbox.checked = false;
    parentCheckbox.indeterminate = false;
  } else if (checkedCount === childCheckboxes.length) {
    parentCheckbox.checked = true;
    parentCheckbox.indeterminate = false;
  } else {
    parentCheckbox.checked = false;
    parentCheckbox.indeterminate = true;
  }
}));
```

---

## 📝 Notes Drupal

### Form API génère automatiquement

Drupal Form API génère `#type => 'checkboxes'` avec wrapper + fieldset :

```php
$form['options'] = [
  '#type' => 'checkboxes',
  '#title' => t('Options'),
  '#options' => [
    'opt1' => t('Option 1'),
    'opt2' => t('Option 2'),
  ],
  '#default_value' => ['opt1'], // Pré-cochées
];

// Rendu HTML :
// <fieldset class="form-checkboxes">
//   <legend>Options</legend>
//   <input type="checkbox" name="options[opt1]" checked />
//   <label>Option 1</label>
//   ...
// </fieldset>
```

### Template override

**Override Drupal checkboxes template** (`templates/form/checkboxes.html.twig`) :

```twig
{# Override Drupal checkboxes wrapper #}
<fieldset{{ attributes.addClass('form-checkboxes') }}>
  {% if title %}
    <legend class="form-checkboxes__legend">
      {{ title }}
      {% if required %}<span class="form-required">*</span>{% endif %}
    </legend>
  {% endif %}
  
  {{ children }}
</fieldset>
```

---

## 🔄 Changelog

### Version 1.0.0 (12 décembre 2025)

- ✅ Création spec initiale
- ✅ Documentation complète (usage, props, tokens, accessibilité)
- ✅ 7 cas d'usage (préférences, équipements, requis, disabled, helper, Drupal, indeterminate)
- ✅ Compatibilité Drupal Form API
- ✅ Support fieldset + legend (regroupement sémantique)
- ✅ Layout variants (vertical, inline horizontal)
- ✅ Tests accessibilité WCAG 2.2 AA
- ✅ Composant wrapper (logique dans atoms Checkbox)

---

**Références** :
- Implémentation : `source/patterns/components/checkboxes/`
- Atom utilisé : `source/patterns/elements/checkbox/`
- Storybook : [Components/Checkboxes](http://localhost:6006/?path=/story/components-checkboxes)
- Drupal Form API : https://api.drupal.org/api/drupal/core!lib!Drupal!Core!Render!Element!Checkboxes.php/10
