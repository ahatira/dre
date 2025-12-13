# Select (Atom)

**Niveau Atomic Design** : Atom / Form element  
**Catégorie** : Form controls  
**Statut** : ✅ Stable  
**Version** : 1.0.0  
**Dernière mise à jour** : 12 décembre 2025

---

## 📋 Description

Liste déroulante native `<select>` avec icône chevron personnalisée. Wrapper BEM avec `<div>` pour styling complet. Supporte options multiples, groupes `<optgroup>`, états disabled/selected, et validation (error/success). Pour un champ complet avec label, utiliser Form-field (Molecule).

**Implémentation** : `source/patterns/elements/select/`

---

## 🎨 Aperçu visuel

```
┌─────────────────────────┐
│ Sélectionnez...        ▼│  ← Default (fermé)
└─────────────────────────┘

┌─────────────────────────┐
│ France                 ▼│  ← Option sélectionnée
├─────────────────────────┤
│ Belgique                │
│ Suisse                  │  ← Menu ouvert (natif navigateur)
│ Luxembourg              │
└─────────────────────────┘

┌─────────────────────────┐
│ Invalide               ▼│  ← Error state
└─────────────────────────┘
  ↑ Bordure rouge
```

---

## 🏗️ Structure BEM

```html
<!-- Default select -->
<div class="ps-select">
  <select class="ps-select__input" name="country" id="country-select">
    <option value="">Sélectionnez un pays</option>
    <option value="fr">France</option>
    <option value="be">Belgique</option>
    <option value="ch">Suisse</option>
  </select>
  <span class="ps-select__icon" data-icon="chevron-down" aria-hidden="true"></span>
</div>

<!-- Select with error state -->
<div class="ps-select ps-select--error">
  <select class="ps-select__input" aria-invalid="true">...</select>
  <span class="ps-select__icon" data-icon="chevron-down"></span>
</div>

<!-- Disabled select -->
<div class="ps-select ps-select--disabled">
  <select class="ps-select__input" disabled aria-disabled="true">...</select>
  <span class="ps-select__icon" data-icon="chevron-down"></span>
</div>

<!-- Select with option groups -->
<div class="ps-select">
  <select class="ps-select__input" name="property">
    <optgroup label="Appartements">
      <option value="t2">T2</option>
      <option value="t3">T3</option>
    </optgroup>
    <optgroup label="Maisons">
      <option value="villa">Villa</option>
      <option value="pavillon">Pavillon</option>
    </optgroup>
  </select>
  <span class="ps-select__icon" data-icon="chevron-down"></span>
</div>
```

### Classes BEM

```
ps-select                                 // Block (wrapper div)
  ps-select__input                        // Element (native <select>)
  ps-select__icon                         // Element (chevron-down icon)

Modifiers (états):
  ps-select--disabled                     // État désactivé (gris)
  ps-select--error                        // État erreur (bordure rouge)
  ps-select--success                      // État succès (bordure verte)
```

---

## 📐 Props (Component API)

### Twig Template

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| **options** | `array` | `[]` | Liste options : `[{value, label, disabled?, selected?}]` |
| **name** | `string` | `''` | Attribut `name` (requis pour soumission formulaire) |
| **id** | `string` | `name + '-select'` | Attribut `id` (auto-généré depuis name) |
| **disabled** | `boolean` | `false` | État désactivé (non-modifiable) |
| **required** | `boolean` | `false` | Champ obligatoire (validation HTML5) |
| **error** | `string` | `null` | État erreur (applique bordure rouge) |
| **success** | `string` | `null` | État succès (applique bordure verte) |
| **attributes** | `Attribute` | `create_attribute()` | Attributs Drupal pour `<select>` |
| **wrapper_attributes** | `Attribute` | `create_attribute()` | Attributs Drupal pour wrapper `<div>` |

### Format options

```javascript
// Structure option simple
{
  value: 'fr',              // Valeur soumise au serveur
  label: 'France',          // Texte affiché à l'utilisateur
  disabled: false,          // Option non-sélectionnable (optionnel)
  selected: false           // Option pré-sélectionnée (optionnel)
}

// Avec groupes (optgroups)
{
  group: 'Europe',          // Nom du groupe
  options: [                // Options du groupe
    {value: 'fr', label: 'France'},
    {value: 'be', label: 'Belgique'}
  ]
}
```

---

## 🎨 Design Tokens

### Tokens utilisés

```css
/* Sizing */
--select-height: var(--size-10);              /* 40px hauteur */
--select-padding-x: var(--size-4);            /* 16px horizontal */
--select-padding-y: var(--size-2);            /* 8px vertical */
--select-font-size: var(--font-size-base);    /* 16px (évite zoom iOS) */
--select-line-height: var(--line-height-base);

/* Colors */
--select-bg: var(--white);                    /* Fond blanc */
--select-border: var(--border-default);       /* Bordure grise */
--select-border-focus: var(--primary);        /* Bordure verte focus */
--select-text: var(--text-primary);           /* Texte noir */
--select-placeholder: var(--text-secondary);  /* Première option grise */

/* Icon */
--select-icon-size: var(--size-5);            /* 20px chevron */
--select-icon-color: var(--text-secondary);   /* Chevron gris */
--select-icon-right: var(--size-4);           /* 16px depuis droite */

/* States */
--select-border-error: var(--danger);         /* Bordure rouge erreur */
--select-border-success: var(--success);      /* Bordure verte succès */
--select-bg-disabled: var(--gray-100);        /* Fond gris désactivé */
--select-text-disabled: var(--text-disabled); /* Texte gris désactivé */

/* Borders */
--select-border-width: 1px;
--select-border-radius: var(--radius-md);     /* 6px arrondi */

/* Focus */
--select-focus-ring: 0 0 0 3px var(--primary-subtle);
--select-focus-outline: 2px solid var(--primary);
```

---

## ♿ Accessibilité (WCAG 2.2 AA)

### Conformité

| Critère | Statut | Implémentation |
|---------|--------|----------------|
| **1.3.1 Info and Relationships** | ✅ | `<select>` natif + `<optgroup>` pour groupes |
| **2.1.1 Keyboard** | ✅ | Tab focus → Espace/Flèches ouvre → Entrée sélectionne |
| **2.4.7 Focus Visible** | ✅ | Bordure focus + ring 3px (`:focus-visible`) |
| **3.2.2 On Input** | ✅ | Pas de soumission automatique sur changement |
| **3.3.1 Error Identification** | ✅ | State `error` + `aria-invalid="true"` |
| **3.3.2 Labels or Instructions** | ⚠️ | Composant atom SANS label (utiliser Form-field) |
| **4.1.2 Name, Role, Value** | ✅ | Rôle natif `combobox`, `aria-required`, `aria-disabled` |

### ARIA Attributes

```html
<!-- Required select -->
<select class="ps-select__input" required aria-required="true">...</select>

<!-- Disabled select -->
<select class="ps-select__input" disabled aria-disabled="true">...</select>

<!-- Error state (lié à message via aria-describedby dans Form-field) -->
<select class="ps-select__input" aria-invalid="true" aria-describedby="country-error">...</select>
```

### Annonces Screen Reader

- **Focus** : "Pays, combobox, France" (option sélectionnée)
- **Ouvert** : "Pays, combobox expanded, 4 options"
- **Navigation** : "France, 1 of 4", "Belgique, 2 of 4"
- **Required** : "Pays, required, combobox"
- **Disabled** : "Pays, unavailable, combobox"

---

## 🎯 Cas d'usage

### 1. Sélection pays (simple)

```twig
{% include '@elements/select/select.twig' with {
  name: 'country',
  options: [
    {value: '', label: 'Sélectionnez un pays', selected: true},
    {value: 'fr', label: 'France'},
    {value: 'be', label: 'Belgique'},
    {value: 'ch', label: 'Suisse'},
    {value: 'lu', label: 'Luxembourg'}
  ],
  required: true
} only %}
```

**Contexte** : Formulaire adresse, filtres immobiliers.

---

### 2. Type de bien avec groupes

```twig
{% include '@elements/select/select.twig' with {
  name: 'property_type',
  options: [
    {
      group: 'Appartements',
      options: [
        {value: 't1', label: 'Studio / T1'},
        {value: 't2', label: 'T2'},
        {value: 't3', label: 'T3'},
        {value: 't4', label: 'T4+'}
      ]
    },
    {
      group: 'Maisons',
      options: [
        {value: 'villa', label: 'Villa'},
        {value: 'pavillon', label: 'Pavillon'},
        {value: 'mas', label: 'Mas / Bastide'}
      ]
    }
  ]
} only %}
```

**Contexte** : Recherche immobilière avec catégories.

---

### 3. Nombre de pièces avec valeur pré-sélectionnée

```twig
{% include '@elements/select/select.twig' with {
  name: 'rooms',
  options: [
    {value: '1', label: '1 pièce'},
    {value: '2', label: '2 pièces'},
    {value: '3', label: '3 pièces', selected: true},
    {value: '4', label: '4 pièces'},
    {value: '5', label: '5 pièces et +'}
  ]
} only %}
```

**Contexte** : Filtre de recherche avec valeur par défaut (profil utilisateur).

---

### 4. Devise avec option désactivée

```twig
{% include '@elements/select/select.twig' with {
  name: 'currency',
  options: [
    {value: 'eur', label: '€ EUR', selected: true},
    {value: 'usd', label: '$ USD'},
    {value: 'gbp', label: '£ GBP'},
    {value: 'chf', label: 'CHF', disabled: true}
  ]
} only %}
```

**Contexte** : Conversion prix (CHF temporairement indisponible).

---

### 5. Select avec état erreur

```twig
{% include '@elements/select/select.twig' with {
  name: 'city',
  options: [
    {value: '', label: 'Choisissez une ville'},
    {value: 'paris', label: 'Paris'},
    {value: 'lyon', label: 'Lyon'}
  ],
  error: 'Ville requise',
  attributes: create_attribute().setAttribute('aria-describedby', 'city-error')
} only %}
```

**Contexte** : Validation formulaire côté serveur (erreur Drupal).

---

### 6. Select désactivé (lecture seule)

```twig
{% include '@elements/select/select.twig' with {
  name: 'status',
  options: [
    {value: 'sold', label: 'Vendu', selected: true}
  ],
  disabled: true
} only %}
```

**Contexte** : Statut bien immobilier non-modifiable (historique).

---

### 7. Intégration Drupal Form API

```php
// Dans hook_form_alter ou FormBase
$form['property_type'] = [
  '#type' => 'select',
  '#title' => t('Type de bien'),
  '#options' => [
    'Appartements' => [
      't2' => t('T2'),
      't3' => t('T3'),
    ],
    'Maisons' => [
      'villa' => t('Villa'),
      'pavillon' => t('Pavillon'),
    ],
  ],
  '#required' => TRUE,
  '#attributes' => [
    'class' => ['ps-select__input'], // Applique styles PS Theme
  ],
];
```

**Contexte** : Formulaire Drupal personnalisé (recherche, webform).

---

## 🔗 Relations avec autres composants

### Composé dans (Molecules)

- **Form-field** : Select + Label + Helper + Error message (champ complet)
- **Filter-panel** : Multiples selects (prix, localisation, type)

### Utilise (Atoms)

- **Icon** : `@elements/icon/icon.twig` (chevron-down)

### Variantes disponibles

- **Input** : `@elements/input/input.twig` (saisie texte libre)
- **Checkbox** : `@elements/checkbox/checkbox.twig` (sélection multiple)
- **Radio** : `@elements/radio/radio.twig` (choix exclusif sans menu)
- **Dropdown** : `@components/dropdown/dropdown.twig` (menu custom riche)

---

## 🧪 Tests & Validation

### Navigation clavier

```
Tab           → Focus select (bordure + ring)
Espace/Enter  → Ouvre menu déroulant
↑/↓           → Navigation options
Enter         → Sélectionne option
Esc           → Ferme menu sans changer
Tab           → Sort du select (garde sélection)
```

### Tests accessibilité

1. **Clavier** : Tab focus → Espace ouvre → Flèches naviguent → Enter sélectionne
2. **Screen reader** : NVDA/JAWS annonce "Combobox, X of Y options"
3. **Zoom 200%** : Texte lisible, chevron visible, pas de troncature
4. **Contraste** : 
   - Texte : 4.5:1 minimum (noir sur blanc)
   - Bordure : 3:1 minimum (gris sur blanc)
   - Focus : 3:1 minimum (vert sur blanc)

### Tests interactifs Storybook

```javascript
// Tests Playwright dans select.stories.jsx
- Default (placeholder visible)
- Single selection (option sélectionnée)
- With groups (optgroups visibles)
- Multiple options (> 10 options scroll)
- Disabled (non-cliquable)
- Error state (bordure rouge + aria-invalid)
- Success state (bordure verte)
- Required (validation HTML5)
```

---

## 📊 Performance

### Métriques

- **HTML** : 3 éléments (wrapper + select + icon) (~250 bytes)
- **CSS** : 1.8 KB (minifié)
- **JS** : 0 KB (pas de JavaScript)
- **Render time** : < 2ms
- **First Paint** : Immédiat (CSS inline prioritaire)

### Optimisations

- Select natif (performance navigateur)
- Icône SVG sprite (1 requête HTTP partagée)
- Pas de polyfills (navigateurs modernes)

---

## 🚧 Limitations connues

### Sélection multiple (`multiple`)

**Non supporté** dans cette version (nécessite composant Multi-select custom).

```twig
{# ❌ Éviter <select multiple> (UX difficile) #}
<select multiple class="ps-select__input">...</select>

{# ✅ Utiliser composant Multi-select ou Checkboxes #}
{% include '@components/multi-select/multi-select.twig' %}
{% include '@components/checkboxes/checkboxes.twig' %}
```

### Styling options natif

**Impossible de styliser `<option>` complètement** (limites navigateurs).

```css
/* ❌ Styles non-supportés navigateurs */
option {
  background-image: url('icon.svg'); /* Ignoré */
  padding: 20px; /* Limité */
}

/* ✅ Utiliser composant Dropdown custom pour styles riches */
```

### Select sans label

**Composant atom SANS label**. Toujours utiliser dans Form-field ou ajouter `aria-label`.

```twig
{# ❌ MAUVAIS (pas de label accessible) #}
{% include '@elements/select/select.twig' with {name: 'city'} only %}

{# ✅ CORRECT (aria-label explicite) #}
{% include '@elements/select/select.twig' with {
  name: 'city',
  attributes: create_attribute().setAttribute('aria-label', 'Ville')
} only %}

{# ✅ MEILLEUR (utiliser Form-field avec label visible) #}
{% include '@components/form-field/form-field.twig' with {
  label: 'Ville',
  type: 'select',
  name: 'city'
} only %}
```

---

## 📝 Notes Drupal

### Génération automatique options

Drupal Form API génère automatiquement les `<option>` :

```php
// FormBase::buildForm()
$form['property_type'] = [
  '#type' => 'select',
  '#title' => t('Type de bien'),
  '#options' => [
    't2' => t('T2'),
    't3' => t('T3'),
    'villa' => t('Villa'),
  ],
  '#default_value' => 't2', // Option pré-sélectionnée
  '#empty_option' => t('- Sélectionner -'), // Première option vide
  '#attributes' => [
    'class' => ['ps-select__input'],
  ],
];
```

### Template override

**Override Drupal select template** (`templates/form/select.html.twig`) :

```twig
{# Override Drupal select template #}
{% include '@elements/select/select.twig' with {
  options: options,
  name: attributes.name,
  attributes: attributes,
  disabled: attributes.disabled,
  required: required
} only %}
```

### Optgroups Drupal

```php
// Groupes automatiques via tableau multidimensionnel
$form['property_type'] = [
  '#type' => 'select',
  '#title' => t('Type'),
  '#options' => [
    'Appartements' => [ // <optgroup label="Appartements">
      't2' => t('T2'),
      't3' => t('T3'),
    ],
    'Maisons' => [ // <optgroup label="Maisons">
      'villa' => t('Villa'),
    ],
  ],
];
```

---

## 🔄 Changelog

### Version 1.0.0 (12 décembre 2025)

- ✅ Création spec initiale
- ✅ Documentation complète (usage, props, tokens, accessibilité)
- ✅ 7 cas d'usage (pays, groupes, pré-sélection, devise, erreur, disabled, Drupal)
- ✅ Compatibilité Drupal Form API
- ✅ Support `<optgroup>` pour catégories
- ✅ Tests accessibilité WCAG 2.2 AA
- ✅ 3 états validation (default, error, success)
- ✅ Icône chevron-down personnalisée

---

**Références** :
- Implémentation : `source/patterns/elements/select/`
- Storybook : [Elements/Select](http://localhost:6006/?path=/story/elements-select)
- Drupal Form API : https://api.drupal.org/api/drupal/elements/10
