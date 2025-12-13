# Form-field (Molecule)

**Niveau Atomic Design** : Molecule / Form component  
**Catégorie** : Forms  
**Statut** : ✅ Stable  
**Version** : 1.0.0  
**Dernière mise à jour** : 12 décembre 2025

---

## 📋 Description

Champ de formulaire complet avec **Label + Input/Select/Textarea + Helper text + Error message**. Composant tout-en-un qui compose les atoms (Input, Select, Textarea, Label) pour créer un champ accessible et validé. Gère 3 types de champs (text/email/password, select, textarea), états de validation (error, success), et affichage conditionnel des messages (helper → erreur si présent).

**Implémentation** : `source/patterns/components/form-field/`

---

## 🎨 Aperçu visuel

```
┌────────────────────────────────────┐
│ Email *                            │  ← Label + required
│ ┌────────────────────────────────┐ │
│ │ vous@exemple.fr                │ │  ← Input
│ └────────────────────────────────┘ │
│ ℹ Format: nom@domaine.fr           │  ← Helper text
└────────────────────────────────────┘

┌────────────────────────────────────┐
│ Email *                            │
│ ┌────────────────────────────────┐ │
│ │ email-invalide               ❌│ │  ← Error state
│ └────────────────────────────────┘ │
│ ⚠ Format email invalide            │  ← Error message
└────────────────────────────────────┘
```

---

## 🏗️ Structure BEM

```html
<!-- Default text field -->
<div class="form-item form-group">
  <label for="email" class="form-label">
    Email
    <span class="form-required">*</span>
  </label>
  <input type="email" class="form-control form-input" id="email" name="email" required />
  <div class="form-helper">Format: nom@domaine.fr</div>
</div>

<!-- Select field with error -->
<div class="form-item form-group">
  <label for="country" class="form-label">Pays</label>
  <select class="form-control form-select" id="country" name="country" aria-invalid="true" aria-describedby="country-error">
    <option value="">Sélectionnez</option>
    <option value="fr">France</option>
  </select>
  <div id="country-error" class="form-error" role="alert">
    Veuillez sélectionner un pays
  </div>
</div>

<!-- Textarea field disabled -->
<div class="form-item form-group">
  <label for="notes" class="form-label">Notes internes</label>
  <textarea class="form-control form-textarea" id="notes" name="notes" rows="4" disabled></textarea>
</div>
```

### Classes BEM

```
form-item                                 // Block (wrapper champ complet)
form-group                                // Alias Drupal/Bootstrap
  form-label                              // Element (label visible)
    form-required                         // Element (asterisk requis)
  form-control                            // Element (input/select/textarea base)
    form-input                            // Variant (input text-like)
    form-select                           // Variant (select)
    form-textarea                         // Variant (textarea)
  form-helper                             // Element (helper text gris)
  form-error                              // Element (error message rouge)
```

**Note** : Classes compatibles Drupal Form API (`form-item`, `form-control`) et Bootstrap (`form-group`).

---

## 📐 Props (Component API)

### Twig Template

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| **label** | `string` | `''` | Texte du label (visible au-dessus du champ) |
| **type** | `string` | `'text'` | Type de champ : `text`, `email`, `password`, `number`, `search`, `tel`, `url`, `select`, `textarea` |
| **name** | `string` | `'field'` | Attribut `name` (requis pour soumission formulaire) |
| **id** | `string` | `name` | Attribut `id` (lien label ↔ input via `for`) |
| **value** | `string` | `''` | Valeur initiale du champ |
| **placeholder** | `string` | `''` | Texte placeholder (aide contextuelle) |
| **required** | `boolean` | `false` | Champ obligatoire (affiche asterisk `*` + validation HTML5) |
| **disabled** | `boolean` | `false` | Champ désactivé (gris, non-modifiable) |
| **helper** | `string` | `''` | Texte d'aide (gris, sous le champ) |
| **error** | `string` | `''` | Message d'erreur (rouge, remplace helper si présent, + `role="alert"`) |
| **rows** | `number` | `4` | Nombre de lignes (textarea uniquement) |
| **options** | `array` | `[]` | Options (select uniquement) : `[{value, label, selected?}]` |
| **attributes** | `Attribute` | `create_attribute()` | Attributs Drupal pour input/select/textarea |

---

## 🎨 Design Tokens

### Tokens utilisés

```css
/* Layout */
--form-field-gap: var(--size-2);              /* 8px entre label et input */
--form-field-margin-bottom: var(--size-6);    /* 24px espacement entre champs */

/* Label */
--form-label-font-size: var(--font-size-sm);  /* 14px */
--form-label-font-weight: var(--font-weight-medium); /* 500 */
--form-label-color: var(--text-primary);      /* Noir */
--form-label-margin-bottom: var(--size-2);    /* 8px */

/* Required asterisk */
--form-required-color: var(--danger);         /* Rouge (asterisk *) */
--form-required-margin-left: var(--size-1);   /* 4px espacement */

/* Helper text */
--form-helper-font-size: var(--font-size-xs); /* 12px */
--form-helper-color: var(--text-secondary);   /* Gris */
--form-helper-margin-top: var(--size-2);      /* 8px */

/* Error message */
--form-error-font-size: var(--font-size-xs);  /* 12px */
--form-error-color: var(--danger);            /* Rouge */
--form-error-margin-top: var(--size-2);       /* 8px */
--form-error-font-weight: var(--font-weight-medium); /* 500 (emphase) */

/* Input (référence atoms) */
--form-control-height: var(--size-10);        /* 40px */
--form-control-padding: var(--size-4);        /* 16px */
--form-control-border: var(--border-default); /* Bordure grise */
--form-control-border-error: var(--danger);   /* Bordure rouge (erreur) */
```

---

## ♿ Accessibilité (WCAG 2.2 AA)

### Conformité

| Critère | Statut | Implémentation |
|---------|--------|----------------|
| **1.3.1 Info and Relationships** | ✅ | `<label for="id">` lié à input via `id` |
| **2.4.6 Headings and Labels** | ✅ | Labels descriptifs + asterisk requis visible |
| **3.3.1 Error Identification** | ✅ | `aria-invalid="true"` + `aria-describedby="error-id"` |
| **3.3.2 Labels or Instructions** | ✅ | Label + helper text + placeholder |
| **3.3.3 Error Suggestion** | ✅ | Messages d'erreur avec conseils explicites |
| **4.1.2 Name, Role, Value** | ✅ | `aria-required`, `aria-invalid`, `aria-describedby` |
| **4.1.3 Status Messages** | ✅ | `role="alert"` sur messages erreur (annonce immédiate) |

### ARIA Attributes

```html
<!-- Champ requis -->
<label for="email" class="form-label">
  Email <span class="form-required">*</span>
</label>
<input id="email" aria-required="true" required />

<!-- Champ avec helper text -->
<input id="email" aria-describedby="email-helper" />
<div id="email-helper" class="form-helper">Format: nom@domaine.fr</div>

<!-- Champ avec erreur -->
<input id="email" aria-invalid="true" aria-describedby="email-error" />
<div id="email-error" class="form-error" role="alert">
  Format email invalide
</div>

<!-- Champ désactivé -->
<input disabled aria-disabled="true" />
```

### Annonces Screen Reader

- **Label** : "Email, required, edit text" (label + champ + état)
- **Helper** : "Email, edit text, Format: nom@domaine.fr" (description)
- **Erreur** : "Email, invalid entry, Format email invalide" (alerte immédiate)
- **Required** : Asterisk `*` annoncé comme "required" ou "asterisk"

---

## 🎯 Cas d'usage

### 1. Champ email requis avec helper

```twig
{% include '@components/form-field/form-field.twig' with {
  label: 'Email',
  type: 'email',
  name: 'email',
  placeholder: 'vous@exemple.fr',
  required: true,
  helper: 'Nous ne partagerons jamais votre email'
} only %}
```

**Contexte** : Formulaire contact, inscription newsletter.

---

### 2. Select pays avec erreur validation

```twig
{% include '@components/form-field/form-field.twig' with {
  label: 'Pays de résidence',
  type: 'select',
  name: 'country',
  options: [
    {value: '', label: 'Sélectionnez un pays'},
    {value: 'fr', label: 'France'},
    {value: 'be', label: 'Belgique'},
    {value: 'ch', label: 'Suisse'}
  ],
  required: true,
  error: 'Veuillez sélectionner un pays'
} only %}
```

**Contexte** : Validation formulaire côté serveur (erreur Drupal).

---

### 3. Textarea description bien

```twig
{% include '@components/form-field/form-field.twig' with {
  label: 'Description du bien',
  type: 'textarea',
  name: 'description',
  placeholder: 'Décrivez les caractéristiques...',
  rows: 8,
  helper: 'Minimum 100 caractères recommandés',
  required: true
} only %}
```

**Contexte** : Formulaire ajout annonce immobilière.

---

### 4. Champ téléphone avec format

```twig
{% include '@components/form-field/form-field.twig' with {
  label: 'Téléphone',
  type: 'tel',
  name: 'phone',
  placeholder: '06 12 34 56 78',
  helper: 'Format: 10 chiffres sans espaces',
  attributes: create_attribute().setAttribute('pattern', '[0-9]{10}')
} only %}
```

**Contexte** : Formulaire demande rappel commercial.

---

### 5. Champ prix avec validation

```twig
{% include '@components/form-field/form-field.twig' with {
  label: 'Prix maximum',
  type: 'number',
  name: 'max_price',
  placeholder: '500000',
  helper: 'Prix en euros (€)',
  attributes: create_attribute()
    .setAttribute('min', '0')
    .setAttribute('step', '1000')
} only %}
```

**Contexte** : Filtre recherche immobilière.

---

### 6. Champ désactivé (lecture seule)

```twig
{% include '@components/form-field/form-field.twig' with {
  label: 'Référence bien',
  type: 'text',
  name: 'reference',
  value: 'REF-2025-001234',
  disabled: true,
  helper: 'Référence générée automatiquement'
} only %}
```

**Contexte** : Formulaire édition annonce (référence non-modifiable).

---

### 7. Intégration Drupal Form API

```php
// Dans FormBase::buildForm()
$form['email'] = [
  '#type' => 'textfield',
  '#title' => $this->t('Email'),
  '#description' => $this->t('Format: nom@domaine.fr'),
  '#required' => TRUE,
  '#attributes' => [
    'placeholder' => $this->t('vous@exemple.fr'),
    'class' => ['form-control', 'form-input'], // Classes PS Theme
  ],
];

// Validation (affiche erreur)
public function validateForm(array &$form, FormStateInterface $form_state) {
  if (!filter_var($form_state->getValue('email'), FILTER_VALIDATE_EMAIL)) {
    $form_state->setErrorByName('email', $this->t('Format email invalide'));
    // → Drupal ajoute automatiquement aria-invalid + error message
  }
}
```

**Contexte** : Formulaire Drupal personnalisé.

---

## 🔗 Relations avec autres composants

### Composé dans (Organisms)

- **Contact-form** : Multiples Form-fields + Button submit
- **Search-form** : Form-field search + Button + Filters
- **User-profile** : Multiples Form-fields (nom, email, tel, adresse)

### Utilise (Atoms)

- **Input** : `@elements/input/input.twig` (type text/email/password/etc.)
- **Select** : `@elements/select/select.twig` (liste déroulante)
- **Textarea** : `@elements/textarea/textarea.twig` (texte multiligne)
- **Label** : `@elements/label/label.twig` (label visible)

### Variantes disponibles

- **Checkboxes group** : `@components/checkboxes/checkboxes.twig` (sélection multiple)
- **Radios group** : `@components/radios/radios.twig` (choix exclusif groupe)

---

## 🧪 Tests & Validation

### Navigation clavier

```
Tab           → Focus input (label lu par SR)
Saisie texte  → Validation HTML5 en temps réel
Tab           → Sort du champ
```

### Tests accessibilité

1. **Label/Input link** : Cliquer label → focus input (via `for="id"`)
2. **Screen reader** : Lit "Email, required, edit text, Format: nom@domaine.fr"
3. **Erreur** : Annonce immédiate "Format email invalide" (role="alert")
4. **Zoom 200%** : Label, input, helper lisibles sans troncature
5. **Contraste** : 
   - Label : 4.5:1 (noir sur blanc)
   - Helper : 4.5:1 (gris foncé sur blanc)
   - Erreur : 4.5:1 (rouge foncé sur blanc)

### Tests interactifs Storybook

```javascript
// Tests Playwright dans form-field.stories.jsx
- Default text field (label + input + helper)
- Email required (asterisk + validation HTML5)
- Select with options (liste déroulante)
- Textarea large (8 rows)
- With error (message rouge + aria-invalid)
- Disabled (gris, non-modifiable)
- All types (text, email, password, number, search, tel, url, select, textarea)
```

---

## 📊 Performance

### Métriques

- **HTML** : 4-5 éléments (wrapper + label + input + helper/error) (~350 bytes)
- **CSS** : 2 KB (minifié)
- **JS** : 0 KB (pas de JavaScript, validation HTML5)
- **Render time** : < 2ms
- **First Paint** : Immédiat

### Optimisations

- Compose atoms existants (Input, Select, Textarea)
- Validation HTML5 native (pas de polyfills)
- Affichage conditionnel (helper OU erreur, pas les deux)

---

## 🚧 Limitations connues

### Un seul message à la fois

**Helper OU Erreur**, pas les deux simultanément.

```twig
{# ✅ CORRECT - Helper affiché si pas d'erreur #}
{% if error %}
  <div class="form-error" role="alert">{{ error }}</div>
{% elseif helper %}
  <div class="form-helper">{{ helper }}</div>
{% endif %}
```

### Types de champs limités

**9 types supportés** : text, email, password, number, search, tel, url, select, textarea.

**Non supportés** (nécessitent composants custom) :
- `date`/`datetime-local` → Date Picker (à venir)
- `file` → File Upload (à venir)
- `checkbox` (single) → Utiliser atom Checkbox
- `radio` (single) → Utiliser atom Radio
- `range` → Range Slider (à venir)

### Checkboxes/Radios groupes

**Utiliser composants dédiés** pour groupes de choix multiples :

```twig
{# ❌ Form-field ne gère PAS les groupes #}
{% include '@components/form-field/form-field.twig' with {type: 'checkbox'} %}

{# ✅ Utiliser composants groupes #}
{% include '@components/checkboxes/checkboxes.twig' %}
{% include '@components/radios/radios.twig' %}
```

---

## 📝 Notes Drupal

### Classes Drupal compatibles

**Form-field utilise classes Drupal standard** :

```css
.form-item         /* Drupal Form API wrapper */
.form-group        /* Bootstrap alias */
.form-label        /* Drupal label */
.form-control      /* Bootstrap input base */
.form-input        /* Drupal textfield */
.form-select       /* Drupal select */
.form-textarea     /* Drupal textarea */
.form-required     /* Drupal asterisk */
.form-helper       /* Drupal description */
.form-error        /* Drupal error message */
```

### Template override Drupal

**Override templates Drupal** pour utiliser Form-field :

```twig
{# templates/form/form-element.html.twig #}
{% include '@components/form-field/form-field.twig' with {
  label: title,
  type: type,
  name: name,
  value: value,
  placeholder: attributes.placeholder,
  required: required,
  disabled: disabled,
  helper: description,
  error: errors[0].message,
  attributes: attributes
} only %}
```

### Form API génère automatiquement

```php
// Drupal génère HTML complet avec form-item, label, input, description
$form['email'] = [
  '#type' => 'textfield',
  '#title' => t('Email'),
  '#description' => t('Helper text'),
  '#required' => TRUE,
];

// Rendu HTML :
// <div class="form-item">
//   <label for="email">Email <span class="form-required">*</span></label>
//   <input type="text" id="email" class="form-control" required />
//   <div class="form-helper">Helper text</div>
// </div>
```

---

## 🔄 Changelog

### Version 1.0.0 (12 décembre 2025)

- ✅ Création spec initiale
- ✅ Documentation complète (usage, props, tokens, accessibilité)
- ✅ 7 cas d'usage (email, select, textarea, tel, prix, disabled, Drupal)
- ✅ Compatibilité Drupal Form API (classes standard)
- ✅ Support 9 types de champs (text, email, password, number, search, tel, url, select, textarea)
- ✅ Validation HTML5 + messages erreur accessibles
- ✅ Tests accessibilité WCAG 2.2 AA
- ✅ Composition atoms (Input, Select, Textarea, Label)

---

**Références** :
- Implémentation : `source/patterns/components/form-field/`
- Storybook : [Components/Form-field](http://localhost:6006/?path=/story/components-form-field)
- Drupal Form API : https://api.drupal.org/api/drupal/core!lib!Drupal!Core!Render!Element!FormElement.php/10
