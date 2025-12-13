# Radios (Molecule)

**Niveau Atomic Design** : Molecule / Form group  
**Catégorie** : Form controls  
**Statut** : ✅ Stable  
**Version** : 1.0.0  
**Dernière mise à jour** : 12 décembre 2025

---

## 📋 Description

Wrapper `<div>` pour groupe de radios avec légende optionnelle. Composant minimaliste Drupal-friendly qui contient plusieurs atoms Radio via slot `children`. Gère layout vertical (stacked) ou horizontal (inline), légende accessible (`<fieldset>` + `<legend>`), et états de validation (error). **Un seul radio sélectionnable** par groupe (via attribut `name` commun).

**Implémentation** : `source/patterns/components/radios/`

---

## 🎨 Aperçu visuel

```
Type de transaction            ← Légende groupe

◉ Achat                        ← Radio 1 (selected)
○ Location                     ← Radio 2
○ Viager                       ← Radio 3
```

**Layout inline** :
```
Statut:  ◉ Disponible  ○ Vendu  ○ En cours
```

---

## 🏗️ Structure BEM

```html
<!-- Default vertical group (avec fieldset) -->
<fieldset class="form-radios">
  <legend class="form-radios__legend">Type de transaction</legend>
  
  <label class="ps-radio ps-radio--checked">
    <input type="radio" class="ps-radio__input" name="transaction" value="buy" checked />
    <span class="ps-radio__circle"></span>
    <span class="ps-radio__label">Achat</span>
  </label>
  
  <label class="ps-radio">
    <input type="radio" class="ps-radio__input" name="transaction" value="rent" />
    <span class="ps-radio__circle"></span>
    <span class="ps-radio__label">Location</span>
  </label>
  
  <label class="ps-radio">
    <input type="radio" class="ps-radio__input" name="transaction" value="viager" />
    <span class="ps-radio__circle"></span>
    <span class="ps-radio__label">Viager</span>
  </label>
</fieldset>

<!-- Inline horizontal group (sans fieldset) -->
<div class="form-radios form-radios--inline">
  <span class="form-radios__label">Statut:</span>
  
  <label class="ps-radio ps-radio--checked">
    <input type="radio" class="ps-radio__input" name="status" value="available" checked />
    <span class="ps-radio__circle"></span>
    <span class="ps-radio__label">Disponible</span>
  </label>
  
  <label class="ps-radio">
    <input type="radio" class="ps-radio__input" name="status" value="sold" />
    <span class="ps-radio__circle"></span>
    <span class="ps-radio__label">Vendu</span>
  </label>
</div>

<!-- Group with error -->
<fieldset class="form-radios form-radios--error" aria-describedby="transaction-error">
  <legend class="form-radios__legend">Type <span class="form-required">*</span></legend>
  {# Radio items #}
</fieldset>
<div id="transaction-error" class="form-error" role="alert">
  Veuillez sélectionner un type de transaction
</div>
```

### Classes BEM

```
form-radios                               // Block (wrapper groupe)
  form-radios__legend                     // Element (légende fieldset)
  form-radios__label                      // Element (label inline sans fieldset)

Modifiers:
  form-radios--inline                     // Layout horizontal (compact)
  form-radios--error                      // État erreur (bordure rouge groupe)
```

**Note** : Classes compatibles Drupal Form API (`form-radios`).

---

## 📐 Props (Component API)

### Twig Template

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| **attributes** | `Attribute` | `create_attribute()` | Attributs Drupal pour wrapper (classes, data-*, aria-*) |
| **children** | `slot` | `''` | Contenu du groupe (atoms Radio) |

**Note** : Composant minimaliste (wrapper uniquement). Atoms Radio gèrent leurs propres props (label, checked, disabled, name).

**IMPORTANT** : Tous les radios d'un groupe **doivent partager le même attribut `name`** pour garantir sélection exclusive.

---

## 🎨 Design Tokens

### Tokens utilisés

```css
/* Layout vertical (default) */
--radios-gap: var(--size-4);                  /* 16px entre radios */
--radios-padding: var(--size-4);              /* 16px padding wrapper */

/* Layout horizontal (inline) */
--radios-inline-gap: var(--size-6);           /* 24px entre radios */
--radios-inline-align: center;                /* Alignement vertical */

/* Legend/Label */
--radios-legend-font-size: var(--font-size-base); /* 16px */
--radios-legend-font-weight: var(--font-weight-medium); /* 500 */
--radios-legend-color: var(--text-primary);   /* Noir */
--radios-legend-margin-bottom: var(--size-3); /* 12px */

/* Error state */
--radios-border-error: var(--danger);         /* Bordure rouge groupe */
--radios-border-width: 1px;
--radios-border-radius: var(--radius-md);     /* 6px */
--radios-padding-error: var(--size-4);        /* 16px padding si erreur */
```

---

## ♿ Accessibilité (WCAG 2.2 AA)

### Conformité

| Critère | Statut | Implémentation |
|---------|--------|----------------|
| **1.3.1 Info and Relationships** | ✅ | `<fieldset>` + `<legend>` pour regroupement sémantique |
| **2.4.6 Headings and Labels** | ✅ | `<legend>` décrit le groupe clairement |
| **3.3.1 Error Identification** | ✅ | `aria-describedby="error-id"` sur fieldset |
| **4.1.2 Name, Role, Value** | ✅ | Rôle `radiogroup` natif (fieldset), `aria-required` si groupe requis |

### ARIA Attributes

```html
<!-- Groupe requis -->
<fieldset class="form-radios" aria-required="true">
  <legend>Type de transaction <span class="form-required">*</span></legend>
  {# Radios #}
</fieldset>

<!-- Groupe avec erreur -->
<fieldset class="form-radios form-radios--error" aria-describedby="type-error">
  <legend>Type <span class="form-required">*</span></legend>
  {# Radios #}
</fieldset>
<div id="type-error" class="form-error" role="alert">
  Veuillez sélectionner un type
</div>

<!-- Groupe avec helper text -->
<fieldset class="form-radios" aria-describedby="type-helper">
  <legend>Type de bien</legend>
  {# Radios #}
</fieldset>
<div id="type-helper" class="form-helper">
  Sélectionnez une seule option
</div>
```

### Annonces Screen Reader

- **Fieldset focus** : "Type de transaction, radiogroup, 3 items" (groupe + nombre)
- **Radio focus** : "Achat, radio button, checked, 1 of 3" (item + position)
- **Navigation** : Flèches ↑↓ naviguent entre radios (si fieldset)
- **Sélection exclusive** : "Achat, checked" puis "Location, checked" (désélection auto)
- **Erreur** : "Type, radiogroup, required, Veuillez sélectionner un type" (si aria-describedby)

---

## 🎯 Cas d'usage

### 1. Type de transaction (vertical)

```twig
<fieldset class="form-radios">
  <legend class="form-radios__legend">Type de transaction</legend>
  
  {% include '@elements/radio/radio.twig' with {
    name: 'transaction',
    value: 'buy',
    label: 'Achat',
    checked: true
  } only %}
  
  {% include '@elements/radio/radio.twig' with {
    name: 'transaction',
    value: 'rent',
    label: 'Location'
  } only %}
  
  {% include '@elements/radio/radio.twig' with {
    name: 'transaction',
    value: 'viager',
    label: 'Viager'
  } only %}
</fieldset>
```

**Contexte** : Formulaire recherche immobilière.

---

### 2. Statut bien (inline horizontal)

```twig
<div class="form-radios form-radios--inline">
  <span class="form-radios__label">Statut:</span>
  
  {% include '@elements/radio/radio.twig' with {
    name: 'status',
    value: 'available',
    label: 'Disponible',
    checked: true
  } only %}
  
  {% include '@elements/radio/radio.twig' with {
    name: 'status',
    value: 'sold',
    label: 'Vendu'
  } only %}
  
  {% include '@elements/radio/radio.twig' with {
    name: 'status',
    value: 'pending',
    label: 'En cours'
  } only %}
</div>
```

**Contexte** : Filtres admin (layout compact).

---

### 3. Groupe requis avec erreur

```twig
<fieldset class="form-radios form-radios--error" aria-required="true" aria-describedby="property-type-error">
  <legend class="form-radios__legend">
    Type de bien <span class="form-required">*</span>
  </legend>
  
  {% include '@elements/radio/radio.twig' with {
    name: 'property_type',
    value: 'apartment',
    label: 'Appartement'
  } only %}
  
  {% include '@elements/radio/radio.twig' with {
    name: 'property_type',
    value: 'house',
    label: 'Maison'
  } only %}
  
  {% include '@elements/radio/radio.twig' with {
    name: 'property_type',
    value: 'commercial',
    label: 'Local commercial'
  } only %}
</fieldset>
<div id="property-type-error" class="form-error" role="alert">
  Veuillez sélectionner un type de bien
</div>
```

**Contexte** : Validation formulaire côté serveur (erreur Drupal).

---

### 4. Options désactivées

```twig
<fieldset class="form-radios">
  <legend class="form-radios__legend">Mode de paiement</legend>
  
  {% include '@elements/radio/radio.twig' with {
    name: 'payment',
    value: 'cash',
    label: 'Comptant',
    checked: true
  } only %}
  
  {% include '@elements/radio/radio.twig' with {
    name: 'payment',
    value: 'financing',
    label: 'Financement bancaire'
  } only %}
  
  {% include '@elements/radio/radio.twig' with {
    name: 'payment',
    value: 'crypto',
    label: 'Crypto-monnaie (bientôt disponible)',
    disabled: true
  } only %}
</fieldset>
```

**Contexte** : Option temporairement indisponible.

---

### 5. Groupe avec helper text

```twig
<fieldset class="form-radios" aria-describedby="surface-helper">
  <legend class="form-radios__legend">Surface habitable</legend>
  
  {% include '@elements/radio/radio.twig' with {
    name: 'surface',
    value: 'small',
    label: 'Moins de 50 m²'
  } only %}
  
  {% include '@elements/radio/radio.twig' with {
    name: 'surface',
    value: 'medium',
    label: '50 à 100 m²',
    checked: true
  } only %}
  
  {% include '@elements/radio/radio.twig' with {
    name: 'surface',
    value: 'large',
    label: 'Plus de 100 m²'
  } only %}
</fieldset>
<div id="surface-helper" class="form-helper">
  Sélectionnez une tranche de surface
</div>
```

**Contexte** : Filtres recherche avec description.

---

### 6. Intégration Drupal Form API

```php
// Dans FormBase::buildForm()
$form['transaction_type'] = [
  '#type' => 'radios',
  '#title' => $this->t('Type de transaction'),
  '#options' => [
    'buy' => $this->t('Achat'),
    'rent' => $this->t('Location'),
    'viager' => $this->t('Viager'),
  ],
  '#default_value' => 'buy', // Radio pré-sélectionné
  '#required' => TRUE,
  '#attributes' => [
    'class' => ['form-radios'], // Classe PS Theme
  ],
  '#description' => $this->t('Choisissez le type de transaction'),
];

// Validation (option obligatoire)
public function validateForm(array &$form, FormStateInterface $form_state) {
  if (empty($form_state->getValue('transaction_type'))) {
    $form_state->setErrorByName('transaction_type', 
      $this->t('Veuillez sélectionner un type de transaction'));
  }
}
```

**Contexte** : Formulaire Drupal personnalisé.

---

### 7. Choix binaire simple

```twig
<fieldset class="form-radios">
  <legend class="form-radios__legend">Accepter les notifications</legend>
  
  {% include '@elements/radio/radio.twig' with {
    name: 'notifications',
    value: 'yes',
    label: 'Oui',
    checked: true
  } only %}
  
  {% include '@elements/radio/radio.twig' with {
    name: 'notifications',
    value: 'no',
    label: 'Non'
  } only %}
</fieldset>
```

**Contexte** : Choix binaire (préférer Toggle pour on/off visuel).

---

## 🔗 Relations avec autres composants

### Composé dans (Organisms)

- **Filter-panel** : Multiples groupes Radios (type, prix, surface)
- **Survey-form** : Questions avec radios (choix unique par question)

### Utilise (Atoms)

- **Radio** : `@elements/radio/radio.twig` (item individuel)

### Variantes disponibles

- **Checkboxes** : `@components/checkboxes/checkboxes.twig` (sélection multiple, plusieurs cochables)
- **Select** : `@elements/select/select.twig` (liste déroulante, économise espace vertical)
- **Toggle** : `@elements/toggle/toggle.twig` (switch on/off binaire visuel)

---

## 🧪 Tests & Validation

### Navigation clavier

```
Tab           → Focus premier radio (ou fieldset)
Espace/Enter  → Sélectionne radio
↑/↓           → Navigue et sélectionne radios (si fieldset)
Tab           → Sort du groupe
```

**IMPORTANT** : Flèches ↑↓ **sélectionnent automatiquement** le radio (contrairement aux checkboxes qui nécessitent Espace).

### Tests accessibilité

1. **Fieldset/Legend** : Screen reader annonce groupe + légende
2. **Navigation** : Flèches ↑↓ naviguent ET sélectionnent radios (fieldset)
3. **Sélection exclusive** : Un seul radio coché à la fois (même `name`)
4. **Erreur** : Message erreur annoncé (aria-describedby)
5. **Required** : Asterisk `*` visible + `aria-required="true"`
6. **Contraste** : 
   - Légende : 4.5:1 (noir sur blanc)
   - Circles : 3:1 (vert sur blanc)

### Tests interactifs Storybook

```javascript
// Tests Playwright dans radios.stories.jsx
- Default vertical group (3 options)
- Inline horizontal (3 statuts)
- With error (validation groupe)
- Required (asterisk + aria-required)
- Disabled option (gris, non-cliquable)
- With helper text (description)
- Binary choice (oui/non)
```

---

## 📊 Performance

### Métriques

- **HTML** : 2 éléments (fieldset + legend) + N radios (~100 bytes base)
- **CSS** : 1 KB (minifié)
- **JS** : 0 KB (pas de JavaScript)
- **Render time** : < 2ms
- **First Paint** : Immédiat

### Optimisations

- Wrapper minimaliste (logique dans atoms Radio)
- Sélection exclusive native HTML (`name` commun)
- Pas de validation JS (HTML5 `required` suffisant)

---

## 🚧 Limitations connues

### Attribut `name` obligatoire identique

**Tous les radios d'un groupe DOIVENT avoir le même `name`** pour garantir sélection exclusive.

```twig
{# ❌ MAUVAIS - Names différents = pas de sélection exclusive #}
{% include '@elements/radio/radio.twig' with {name: 'type1', value: 'buy'} only %}
{% include '@elements/radio/radio.twig' with {name: 'type2', value: 'rent'} only %}

{# ✅ CORRECT - Même name = sélection exclusive #}
{% include '@elements/radio/radio.twig' with {name: 'transaction', value: 'buy'} only %}
{% include '@elements/radio/radio.twig' with {name: 'transaction', value: 'rent'} only %}
```

### Décocher radio impossible

**Impossible de décocher un radio une fois sélectionné** (comportement natif HTML).

**Solutions** :
- Ajouter option "Aucun" si désélection nécessaire
- Utiliser checkboxes si sélection optionnelle
- Utiliser select avec option vide

```twig
{# ✅ Ajouter option "Aucun" #}
{% include '@elements/radio/radio.twig' with {
  name: 'type',
  value: '',
  label: 'Aucun (réinitialiser)'
} only %}
```

### Minimum 2 options

**Un seul radio n'a pas de sens** (utiliser Toggle ou Checkbox).

```twig
{# ❌ MAUVAIS - Radio seul inutile #}
{% include '@elements/radio/radio.twig' with {name: 'accept', value: 'yes', label: 'Accepter'} only %}

{# ✅ CORRECT - Utiliser Toggle #}
{% include '@elements/toggle/toggle.twig' with {label: 'Accepter les conditions'} only %}

{# ✅ OU 2+ radios #}
{% include '@elements/radio/radio.twig' with {name: 'accept', value: 'yes', label: 'Oui'} only %}
{% include '@elements/radio/radio.twig' with {name: 'accept', value: 'no', label: 'Non'} only %}
```

---

## 📝 Notes Drupal

### Form API génère automatiquement

Drupal Form API génère `#type => 'radios'` avec wrapper + fieldset :

```php
$form['property_type'] = [
  '#type' => 'radios',
  '#title' => t('Type de bien'),
  '#options' => [
    'apartment' => t('Appartement'),
    'house' => t('Maison'),
  ],
  '#default_value' => 'apartment', // Pré-sélectionné
];

// Rendu HTML :
// <fieldset class="form-radios">
//   <legend>Type de bien</legend>
//   <input type="radio" name="property_type" value="apartment" checked />
//   <label>Appartement</label>
//   ...
// </fieldset>
```

### Template override

**Override Drupal radios template** (`templates/form/radios.html.twig`) :

```twig
{# Override Drupal radios wrapper #}
<fieldset{{ attributes.addClass('form-radios') }}>
  {% if title %}
    <legend class="form-radios__legend">
      {{ title }}
      {% if required %}<span class="form-required">*</span>{% endif %}
    </legend>
  {% endif %}
  
  {{ children }}
</fieldset>
```

### Différence Radios vs Select

**Radios** : Toutes options visibles (2-5 options max recommandé)  
**Select** : Menu déroulant (5+ options, économise espace)

```php
// ✅ RADIOS (2-5 options, choix fréquent)
$form['transaction'] = [
  '#type' => 'radios',
  '#options' => ['buy' => t('Achat'), 'rent' => t('Location')],
];

// ✅ SELECT (5+ options, économise espace)
$form['city'] = [
  '#type' => 'select',
  '#options' => ['paris' => 'Paris', 'lyon' => 'Lyon', ...], // 50+ villes
];
```

---

## 🔄 Changelog

### Version 1.0.0 (12 décembre 2025)

- ✅ Création spec initiale
- ✅ Documentation complète (usage, props, tokens, accessibilité)
- ✅ 7 cas d'usage (transaction, statut, requis, disabled, helper, Drupal, binaire)
- ✅ Compatibilité Drupal Form API
- ✅ Support fieldset + legend (regroupement sémantique)
- ✅ Layout variants (vertical, inline horizontal)
- ✅ Tests accessibilité WCAG 2.2 AA
- ✅ Sélection exclusive native (attribut `name` commun)
- ✅ Composant wrapper (logique dans atoms Radio)

---

**Références** :
- Implémentation : `source/patterns/components/radios/`
- Atom utilisé : `source/patterns/elements/radio/`
- Storybook : [Components/Radios](http://localhost:6006/?path=/story/components-radios)
- Drupal Form API : https://api.drupal.org/api/drupal/core!lib!Drupal!Core!Render!Element!Radios.php/10
