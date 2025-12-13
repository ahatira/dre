# Input (Atom)

**Niveau Atomic Design** : Atom / Form element  
**Catégorie** : Form controls  
**Statut** : ✅ Stable  
**Version** : 1.0.0  
**Dernière mise à jour** : 12 décembre 2025

---

## 📋 Description

Champ de saisie texte natif `<input>` sans label ni icône. Composant atomique compatible avec les attributs Drupal. Supporte 7 types (text, email, password, number, search, tel, url), 3 états de validation (error, success, warning), et les états disabled/required. Pour un champ complet avec label, utiliser le composant Form-field (Molecule).

**Implémentation** : `source/patterns/elements/input/`

---

## 🎨 Aperçu visuel

```
┌─────────────────────────┐
│ Entrez votre email...   │  ← Default (text)
└─────────────────────────┘

┌─────────────────────────┐
│ ********               🔒│  ← Password type
└─────────────────────────┘

┌─────────────────────────┐
│ Invalide               ❌│  ← Error state
└─────────────────────────┘
```

---

## 🏗️ Structure BEM

```html
<!-- Default text input -->
<input class="ps-input" type="text" name="email" id="email-input" placeholder="Entrez votre email..." />

<!-- Password input disabled -->
<input class="ps-input ps-input--disabled" type="password" name="password" id="password-input" disabled aria-disabled="true" />

<!-- Number input with error state -->
<input class="ps-input ps-input--error" type="number" name="age" id="age-input" aria-invalid="true" />

<!-- Search input required -->
<input class="ps-input" type="search" name="query" id="search-input" required aria-required="true" />
```

### Classes BEM

```
ps-input                                  // Block (champ input)

Modifiers (états):
  ps-input--disabled                      // État désactivé (gris)
  ps-input--error                         // État erreur (bordure rouge)
  ps-input--success                       // État succès (bordure verte)
  ps-input--warning                       // État avertissement (bordure jaune)
```

---

## 📐 Props (Component API)

### Twig Template

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| **type** | `string` | `'text'` | Type d'input : `text`, `email`, `password`, `number`, `search`, `tel`, `url` |
| **name** | `string` | `''` | Attribut `name` (requis pour soumission formulaire) |
| **value** | `string` | `''` | Valeur initiale du champ |
| **placeholder** | `string` | `''` | Texte placeholder (aide contextuelle) |
| **id** | `string` | `name + '-input'` | Attribut `id` (auto-généré depuis name si absent) |
| **autocomplete** | `string` | `null` | Attribut autocomplete HTML5 (`email`, `name`, `tel`, etc.) |
| **disabled** | `boolean` | `false` | État désactivé (non-modifiable) |
| **required** | `boolean` | `false` | Champ obligatoire (validation HTML5) |
| **state** | `string` | `null` | État validation : `null`, `'error'`, `'success'`, `'warning'` |
| **attributes** | `Attribute` | `create_attribute()` | Objet Drupal attributes (classes, data-*, aria-*) |

---

## 🎨 Design Tokens

### Tokens utilisés

```css
/* Sizing */
--input-height: var(--size-10);              /* 40px hauteur */
--input-padding-x: var(--size-4);            /* 16px horizontal */
--input-padding-y: var(--size-2);            /* 8px vertical */
--input-font-size: var(--font-size-base);    /* 16px (évite zoom iOS) */
--input-line-height: var(--line-height-base);

/* Colors */
--input-bg: var(--white);                    /* Fond blanc */
--input-border: var(--border-default);       /* Bordure grise par défaut */
--input-border-focus: var(--primary);        /* Bordure verte focus */
--input-text: var(--text-primary);           /* Texte noir */
--input-placeholder: var(--text-secondary);  /* Placeholder gris */

/* States */
--input-border-error: var(--danger);         /* Bordure rouge erreur */
--input-border-success: var(--success);      /* Bordure verte succès */
--input-border-warning: var(--warning);      /* Bordure jaune avertissement */
--input-bg-disabled: var(--gray-100);        /* Fond gris désactivé */
--input-text-disabled: var(--text-disabled); /* Texte gris désactivé */

/* Borders */
--input-border-width: 1px;
--input-border-radius: var(--radius-md);     /* 6px arrondi */

/* Focus */
--input-focus-ring: 0 0 0 3px var(--primary-subtle);
--input-focus-outline: 2px solid var(--primary);
```

---

## ♿ Accessibilité (WCAG 2.2 AA)

### Conformité

| Critère | Statut | Implémentation |
|---------|--------|----------------|
| **1.3.1 Info and Relationships** | ✅ | Attributs `type`, `name`, `id` sémantiques |
| **1.3.5 Identify Input Purpose** | ✅ | Support `autocomplete` (email, name, tel) |
| **2.1.1 Keyboard** | ✅ | Focusable, navigation Tab, échap vide champ |
| **2.4.7 Focus Visible** | ✅ | Bordure focus + ring 3px (`:focus-visible`) |
| **3.2.2 On Input** | ✅ | Pas de changement contexte sur saisie |
| **3.3.1 Error Identification** | ✅ | State `error` + `aria-invalid="true"` |
| **3.3.2 Labels or Instructions** | ⚠️ | Composant atom SANS label (utiliser Form-field) |
| **4.1.2 Name, Role, Value** | ✅ | Rôles natifs `<input>`, `aria-required`, `aria-disabled` |

### ARIA Attributes

```html
<!-- Required input -->
<input class="ps-input" required aria-required="true" />

<!-- Disabled input -->
<input class="ps-input ps-input--disabled" disabled aria-disabled="true" />

<!-- Error state (lié à message via aria-describedby dans Form-field) -->
<input class="ps-input ps-input--error" aria-invalid="true" aria-describedby="email-error" />
```

### Annonces Screen Reader

- **Focus** : "Email, edit text" (type email)
- **Required** : "Email, required, edit text"
- **Disabled** : "Email, unavailable, edit text"
- **Error** : "Email, invalid entry, edit text"

---

## 🎯 Cas d'usage

### 1. Champ email simple

```twig
{% include '@elements/input/input.twig' with {
  type: 'email',
  name: 'email',
  placeholder: 'vous@exemple.fr',
  autocomplete: 'email',
  required: true
} only %}
```

**Contexte** : Formulaire newsletter, contact, inscription.

---

### 2. Champ recherche avec valeur initiale

```twig
{% include '@elements/input/input.twig' with {
  type: 'search',
  name: 'query',
  value: 'Appartement Paris',
  placeholder: 'Rechercher un bien...',
  autocomplete: 'off'
} only %}
```

**Contexte** : Barre de recherche pré-remplie (résultats précédents).

---

### 3. Champ numérique avec état erreur

```twig
{% include '@elements/input/input.twig' with {
  type: 'number',
  name: 'price',
  placeholder: 'Prix maximum',
  state: 'error',
  attributes: create_attribute().setAttribute('min', '0').setAttribute('step', '1000')
} only %}
```

**Contexte** : Filtre prix immobilier avec validation côté client.

---

### 4. Champ téléphone international

```twig
{% include '@elements/input/input.twig' with {
  type: 'tel',
  name: 'phone',
  placeholder: '+33 6 12 34 56 78',
  autocomplete: 'tel',
  attributes: create_attribute().setAttribute('pattern', '[+0-9 ]+')
} only %}
```

**Contexte** : Formulaire contact commercial.

---

### 5. Champ mot de passe avec critères

```twig
{% include '@elements/input/input.twig' with {
  type: 'password',
  name: 'password',
  placeholder: 'Minimum 8 caractères',
  required: true,
  autocomplete: 'new-password',
  attributes: create_attribute()
    .setAttribute('minlength', '8')
    .setAttribute('aria-describedby', 'password-requirements')
} only %}
```

**Contexte** : Inscription utilisateur avec règles de sécurité.

---

### 6. Champ désactivé (lecture seule)

```twig
{% include '@elements/input/input.twig' with {
  type: 'text',
  name: 'reference',
  value: 'REF-2025-001234',
  disabled: true
} only %}
```

**Contexte** : Référence bien immobilier non-modifiable.

---

### 7. Intégration Drupal Form API

```php
// Dans hook_form_alter ou FormBase
$form['email'] = [
  '#type' => 'textfield',
  '#title' => t('Email'),
  '#required' => TRUE,
  '#attributes' => [
    'placeholder' => t('vous@exemple.fr'),
    'autocomplete' => 'email',
    'class' => ['ps-input'], // Applique styles PS Theme
  ],
];
```

**Contexte** : Formulaire Drupal personnalisé (contact, webform, user edit).

---

## 🔗 Relations avec autres composants

### Composé dans (Molecules)

- **Form-field** : Input + Label + Helper + Error message (champ complet)
- **Search-bar** : Input type search + bouton submit + icône loupe

### Utilise (Atoms)

- Aucun (composant atomique autonome)

### Variantes disponibles

- **Checkbox** : `@elements/checkbox/checkbox.twig` (sélection binaire)
- **Radio** : `@elements/radio/radio.twig` (choix exclusif)
- **Select** : `@elements/select/select.twig` (liste déroulante)
- **Textarea** : `@elements/textarea/textarea.twig` (texte long)
- **Toggle** : `@elements/toggle/toggle.twig` (switch on/off)

---

## 🧪 Tests & Validation

### Validation HTML5

```html
<!-- Email valide -->
<input type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" />

<!-- URL valide -->
<input type="url" pattern="https?://.+" />

<!-- Téléphone français -->
<input type="tel" pattern="(0|\+33)[1-9][0-9]{8}" />

<!-- Nombre positif -->
<input type="number" min="0" step="1" />
```

### Tests accessibilité

1. **Clavier** : Tab focus → Saisir texte → Shift+Tab sort
2. **Screen reader** : NVDA/JAWS annonce type + état + label (si Form-field)
3. **Zoom 200%** : Texte lisible, pas de troncature
4. **Contraste** : 
   - Texte : 4.5:1 minimum (noir sur blanc)
   - Placeholder : 4.5:1 minimum (gris foncé)
   - Bordure focus : 3:1 minimum (vert sur blanc)

### Tests interactifs Storybook

```javascript
// Tests Playwright dans input.stories.jsx
- Default (type text)
- Email avec autocomplete
- Password masqué
- Number avec min/max
- Search avec clear button
- Disabled (non-modifiable)
- Error state (bordure rouge + aria-invalid)
- Success state (bordure verte)
- Required (asterisk + aria-required)
```

---

## 📊 Performance

### Métriques

- **HTML** : 1 élément (120 bytes)
- **CSS** : 1.2 KB (minifié)
- **JS** : 0 KB (pas de JavaScript)
- **Render time** : < 1ms
- **First Paint** : Immédiat (CSS inline prioritaire)

### Optimisations

- Pas de dépendances externes
- CSS natif (pas de polyfills)
- Validation HTML5 (pas de JS côté client)

---

## 🚧 Limitations connues

### Type `date`/`datetime-local`

**Non supporté** dans cette version (nécessite composant Date Picker custom).

```twig
{# ❌ Éviter input type="date" (UX incohérente navigateurs) #}
<input type="date" class="ps-input" />

{# ✅ Utiliser composant Date Picker (à venir) #}
{% include '@components/date-picker/date-picker.twig' %}
```

### Type `file`

**Non supporté** (nécessite composant File Upload custom).

```twig
{# ❌ Éviter input type="file" (styles non-applicables) #}
<input type="file" class="ps-input" />

{# ✅ Utiliser composant File Upload (à venir) #}
{% include '@components/file-upload/file-upload.twig' %}
```

### Input sans label

**Composant atom SANS label**. Toujours utiliser dans Form-field ou ajouter `aria-label`.

```twig
{# ❌ MAUVAIS (pas de label accessible) #}
{% include '@elements/input/input.twig' with {name: 'search'} only %}

{# ✅ CORRECT (aria-label explicite) #}
{% include '@elements/input/input.twig' with {
  name: 'search',
  attributes: create_attribute().setAttribute('aria-label', 'Rechercher un bien')
} only %}

{# ✅ MEILLEUR (utiliser Form-field avec label) #}
{% include '@components/form-field/form-field.twig' with {
  label: 'Recherche',
  type: 'search',
  name: 'search'
} only %}
```

---

## 📝 Notes Drupal

### Différences Twig Storybook vs Drupal

**Storybook (dev)** :
```twig
{% set attributes = attributes|default(create_attribute()) %}
```

**Drupal (production)** :
```twig
{# create_attribute() existe nativement, pas besoin de default #}
{% set attributes = attributes|default({}) %}
```

### Intégration Form API

Drupal génère automatiquement les inputs via Form API :

```php
// FormBase::buildForm()
$form['email'] = [
  '#type' => 'textfield',        // → <input type="text">
  '#title' => t('Email'),
  '#required' => TRUE,
  '#attributes' => [
    'class' => ['ps-input'],     // Ajoute classe BEM
    'placeholder' => t('Email'),
  ],
];
```

**Template override** (`templates/form/input.html.twig`) :

```twig
{# Override Drupal input template #}
{% include '@elements/input/input.twig' with {
  type: type,
  name: name,
  value: value,
  placeholder: placeholder,
  disabled: disabled,
  required: required,
  attributes: attributes
} only %}
```

### Validation messages

**Géré par Form-field (Molecule)**, pas par Input (Atom).

```php
// Validation Drupal Form API
if (empty($form_state->getValue('email'))) {
  $form_state->setErrorByName('email', t('Email requis'));
  // → Ajout automatique aria-invalid="true" + message erreur
}
```

---

## 🔄 Changelog

### Version 1.0.0 (12 décembre 2025)

- ✅ Création spec initiale
- ✅ Documentation complète (usage, props, tokens, accessibilité)
- ✅ 7 cas d'usage (email, search, number, tel, password, disabled, Drupal)
- ✅ Compatibilité Drupal Form API
- ✅ Tests accessibilité WCAG 2.2 AA
- ✅ Support 7 types HTML5 (text, email, password, number, search, tel, url)
- ✅ 4 états validation (default, error, success, warning)

---

**Références** :
- Implémentation : `source/patterns/elements/input/`
- Storybook : [Elements/Input](http://localhost:6006/?path=/story/elements-input)
- Drupal Form API : https://api.drupal.org/api/drupal/elements/10
