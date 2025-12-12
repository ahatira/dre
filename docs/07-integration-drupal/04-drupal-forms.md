# Drupal Forms

**Intégrer Form API Drupal** avec composants PS Theme (form-field, input, select, textarea)

---

## 🎯 Objectif

Mapper **Form API Drupal** (render arrays PHP) → **Composants PS Theme** (Twig templates) pour créer formulaires accessibles, validés, et stylisés avec design system.

---

## 🏗️ Architecture Form API

### Workflow création formulaire

```
1. FormBase (PHP)                    2. Render Array                 3. Template Twig
┌─────────────────────┐             ┌──────────────────┐            ┌────────────────┐
│ buildForm()         │             │ Form Element     │            │ form-element   │
│ - #type             │ ──────────> │ #title           │ ─────────> │ .html.twig     │
│ - #title            │             │ #default_value   │            │ (form-field)   │
│ - #required         │             │ #attributes      │            │                │
│ validateForm()      │             └──────────────────┘            └────────────────┘
│ submitForm()        │
└─────────────────────┘
                │
                │ 4. Validation
                v
        ┌──────────────────┐
        │ Messages erreur  │
        │ setErrorByName() │
        └──────────────────┘
```

**Étapes** :
1. **Définir formulaire** : `buildForm()` retourne render array
2. **Render array** : Drupal génère HTML via templates
3. **Templates Twig** : Incluent composants PS Theme
4. **Validation** : `validateForm()` + `setErrorByName()` pour erreurs

---

## 📋 Mapping Form API → Composants PS

### Tableau correspondance

| Form API `#type` | Composant PS | Template Drupal | Props mappées |
|------------------|--------------|-----------------|---------------|
| **textfield** | Input (atom) | `input.html.twig` | type="text", name, value, placeholder |
| **email** | Input (atom) | `input.html.twig` | type="email", name, value, required |
| **password** | Input (atom) | `input.html.twig` | type="password", name, autocomplete |
| **tel** | Input (atom) | `input.html.twig` | type="tel", name, pattern |
| **number** | Input (atom) | `input.html.twig` | type="number", min, max, step |
| **search** | Input (atom) | `input.html.twig` | type="search", name, placeholder |
| **url** | Input (atom) | `input.html.twig` | type="url", name, placeholder |
| **select** | Select (atom) | `select.html.twig` | options array, name, required |
| **textarea** | Textarea (atom) | `textarea.html.twig` | rows, name, value, maxlength |
| **checkboxes** | Checkboxes (molecule) | `checkboxes.html.twig` | options array, legend |
| **radios** | Radios (molecule) | `radios.html.twig` | options array, legend, default |
| **submit** | Button (atom) | `input--submit.html.twig` | variant="primary", text |

---

## 🛠️ Exemple 1 : Formulaire contact (FormBase)

### Fichier : `src/Form/ContactForm.php`

```php
<?php

namespace Drupal\ps_theme\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Formulaire contact immobilier
 */
class ContactForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ps_contact_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    // Nom (textfield → Input type="text")
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nom complet'),
      '#required' => TRUE,
      '#placeholder' => $this->t('Jean Dupont'),
      '#attributes' => [
        'autocomplete' => 'name',
      ],
    ];

    // Email (email → Input type="email")
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Adresse email'),
      '#required' => TRUE,
      '#placeholder' => $this->t('jean.dupont@example.com'),
      '#description' => $this->t('Nous ne partagerons jamais votre email.'),
      '#attributes' => [
        'autocomplete' => 'email',
      ],
    ];

    // Téléphone (tel → Input type="tel")
    $form['phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Téléphone'),
      '#placeholder' => $this->t('+33 1 23 45 67 89'),
      '#attributes' => [
        'autocomplete' => 'tel',
        'pattern' => '[0-9+\s\-()]+',
      ],
    ];

    // Type bien (select → Select)
    $form['property_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type de bien recherché'),
      '#required' => TRUE,
      '#options' => [
        '' => $this->t('-- Sélectionner --'),
        'apartment' => $this->t('Appartement'),
        'house' => $this->t('Maison'),
        'office' => $this->t('Bureau'),
        'land' => $this->t('Terrain'),
      ],
      '#default_value' => '',
    ];

    // Budget (number → Input type="number")
    $form['budget'] = [
      '#type' => 'number',
      '#title' => $this->t('Budget maximum (€)'),
      '#min' => 0,
      '#step' => 10000,
      '#placeholder' => $this->t('300000'),
      '#attributes' => [
        'data-currency' => 'EUR',
      ],
    ];

    // Message (textarea → Textarea)
    $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Votre message'),
      '#required' => TRUE,
      '#rows' => 6,
      '#placeholder' => $this->t('Décrivez votre projet immobilier...'),
      '#maxlength' => 1000,
      '#description' => $this->t('Maximum 1000 caractères.'),
    ];

    // Services (checkboxes → Checkboxes)
    $form['services'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Services souhaités'),
      '#options' => [
        'valuation' => $this->t('Estimation gratuite'),
        'visit' => $this->t('Visite personnalisée'),
        'financing' => $this->t('Conseil financement'),
        'notary' => $this->t('Accompagnement notaire'),
      ],
    ];

    // Contact préféré (radios → Radios)
    $form['contact_preference'] = [
      '#type' => 'radios',
      '#title' => $this->t('Moyen de contact préféré'),
      '#options' => [
        'email' => $this->t('Email'),
        'phone' => $this->t('Téléphone'),
        'sms' => $this->t('SMS'),
      ],
      '#default_value' => 'email',
    ];

    // RGPD (checkbox → Checkbox)
    $form['consent'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('J\'accepte la politique de confidentialité'),
      '#required' => TRUE,
    ];

    // Bouton submit (submit → Button)
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Envoyer ma demande'),
      '#attributes' => [
        'class' => ['ps-button', 'ps-button--primary', 'ps-button--lg'],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validation téléphone (format français)
    $phone = $form_state->getValue('phone');
    if ($phone && !preg_match('/^(\+33|0)[1-9](\d{2}){4}$/', str_replace([' ', '-', '(', ')'], '', $phone))) {
      $form_state->setErrorByName('phone', $this->t('Format téléphone invalide. Attendu : +33 1 23 45 67 89'));
    }

    // Validation budget (minimum 50 000€)
    $budget = $form_state->getValue('budget');
    if ($budget && $budget < 50000) {
      $form_state->setErrorByName('budget', $this->t('Le budget minimum est de 50 000 €'));
    }

    // Validation message (minimum 20 caractères)
    $message = trim($form_state->getValue('message'));
    if (strlen($message) < 20) {
      $form_state->setErrorByName('message', $this->t('Votre message doit contenir au moins 20 caractères.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Récupérer valeurs
    $values = $form_state->getValues();

    // Envoyer email (Mail API)
    $mailManager = \Drupal::service('plugin.manager.mail');
    $mailManager->mail(
      'ps_theme',
      'contact',
      'contact@example.com',
      'fr',
      [
        'name' => $values['name'],
        'email' => $values['email'],
        'phone' => $values['phone'],
        'property_type' => $values['property_type'],
        'budget' => $values['budget'],
        'message' => $values['message'],
        'services' => array_filter($values['services']),
        'contact_preference' => $values['contact_preference'],
      ]
    );

    // Message succès
    $this->messenger()->addStatus($this->t('Votre demande a été envoyée avec succès. Nous vous répondrons sous 24h.'));

    // Redirection
    $form_state->setRedirect('ps_theme.contact.confirmation');
  }

}
```

---

## 🛠️ Exemple 2 : Template form-element (Mapping)

### Fichier : `templates/form/form-element.html.twig`

```twig
{#
/**
 * @file
 * Override form-element : Utiliser form-field molecule PS Theme
 * 
 * Variables disponibles:
 * - element: Render array form element
 * - title: Label champ (#title)
 * - description: Texte helper (#description)
 * - errors: Messages erreur (validation)
 * - required: Boolean champ obligatoire (#required)
 * - disabled: Boolean champ désactivé (#disabled)
 * - type: Type element (#type)
 */
#}

{# Attacher library form-field #}
{{ attach_library('ps/form-field') }}

{# Types supportés par form-field molecule #}
{% set supported_types = ['textfield', 'email', 'password', 'tel', 'number', 'search', 'url', 'select', 'textarea'] %}

{# Si type supporté, utiliser composant form-field #}
{% if element['#type'] in supported_types %}

  {# Mapper type Drupal → type composant #}
  {% set component_type = element['#type'] == 'textfield' ? 'text' : element['#type'] %}

  {# Extraire options si select #}
  {% set options = [] %}
  {% if element['#type'] == 'select' and element['#options'] %}
    {% for key, value in element['#options'] %}
      {% set options = options|merge([{
        value: key,
        label: value,
        selected: key == element['#default_value']
      }]) %}
    {% endfor %}
  {% endif %}

  {# Extraire message erreur (premier seulement) #}
  {% set error_message = errors ? errors|first|render|striptags : '' %}

  {# Utiliser composant form-field (molecule) #}
  {% include '@components/form-field/form-field.twig' with {
    label: title,
    type: component_type,
    name: element['#name'],
    id: element['#id'],
    value: element['#default_value'] ?? element['#value'] ?? '',
    placeholder: element['#placeholder'] ?? '',
    required: required,
    disabled: disabled,
    helper: description ? description|render|striptags : '',
    error: error_message,
    rows: element['#rows'] ?? 4,
    options: options,
    attributes: element['#attributes']
  } only %}

{# Sinon, affichage Drupal par défaut (checkbox, radios, submit) #}
{% else %}
  
  <div{{ attributes.addClass('form-item', 'form-item--' ~ element['#type']) }}>
    
    {# Label (si présent) #}
    {% if title %}
      <label{{ title_attributes.addClass('form-label') }} for="{{ element['#id'] }}">
        {{ title }}
        {% if required %}
          <span class="form-required">*</span>
        {% endif %}
      </label>
    {% endif %}

    {# Element (input, checkbox, radios, submit) #}
    {{ element }}

    {# Helper OU Error (mutuellement exclusif) #}
    {% if errors %}
      <div class="form-error" role="alert">
        {{ errors }}
      </div>
    {% elseif description %}
      <div class="form-helper">
        {{ description }}
      </div>
    {% endif %}

  </div>

{% endif %}
```

**Logique** :
- **Types supportés** (textfield, email, select, etc.) → Composant `form-field` molecule
- **Autres types** (checkbox, radios, submit) → Affichage Drupal par défaut (déjà stylisé via CSS global)

---

## 🛠️ Exemple 3 : Template input (Atom)

### Fichier : `templates/form/input.html.twig`

```twig
{#
/**
 * @file
 * Override input : Utiliser input atom PS Theme
 * 
 * Variables disponibles:
 * - attributes: Classes/attributs input
 * - type: Type input (text, email, password, etc.)
 */
#}

{# Attacher library input (si CSS séparé) #}
{# {{ attach_library('ps/input') }} #}

{# Utiliser composant input (atom) #}
{% include '@elements/input/input.twig' with {
  type: attributes.type.value ?? 'text',
  name: attributes.name.value,
  id: attributes.id.value,
  value: attributes.value.value ?? '',
  placeholder: attributes.placeholder.value ?? '',
  disabled: attributes.disabled.value ?? false,
  required: attributes.required.value ?? false,
  state: attributes['data-error'] ? 'error' : (attributes['data-success'] ? 'success' : null),
  attributes: attributes
} only %}
```

**Note** : Template rarement nécessaire si `form-element.html.twig` utilise déjà `form-field` (qui inclut `input` en interne).

---

## 🛠️ Exemple 4 : Validation avec états visuels

### Preprocess : Ajouter attributs data-error

```php
/**
 * Implements hook_preprocess_input().
 */
function ps_theme_preprocess_input(&$variables) {
  $element = $variables['element'];
  
  // Ajouter attribut data-error si erreur validation
  if (!empty($element['#errors'])) {
    $variables['attributes']['data-error'] = TRUE;
    $variables['attributes']['aria-invalid'] = 'true';
  }
  
  // Ajouter attribut data-success si validation réussie (optionnel)
  if (!empty($element['#validated']) && empty($element['#errors'])) {
    $variables['attributes']['data-success'] = TRUE;
  }
}

/**
 * Implements hook_preprocess_select().
 */
function ps_theme_preprocess_select(&$variables) {
  $element = $variables['element'];
  
  // Même logique pour select
  if (!empty($element['#errors'])) {
    $variables['attributes']['data-error'] = TRUE;
    $variables['attributes']['aria-invalid'] = 'true';
  }
}

/**
 * Implements hook_preprocess_textarea().
 */
function ps_theme_preprocess_textarea(&$variables) {
  $element = $variables['element'];
  
  // Même logique pour textarea
  if (!empty($element['#errors'])) {
    $variables['attributes']['data-error'] = TRUE;
    $variables['attributes']['aria-invalid'] = 'true';
  }
}
```

**Résultat CSS** (automatique via classes) :
```css
/* Input avec erreur (spec input.md) */
.ps-input[data-error],
.ps-input[aria-invalid="true"] {
  border-color: var(--danger);
  background-color: var(--danger-bg-subtle);
}

/* Select avec erreur (spec select.md) */
.ps-select__input[data-error] {
  border-color: var(--danger);
}
```

---

## 📊 Form API : Propriétés clés

### Propriétés communes (tous types)

| Propriété | Type | Exemple | Description |
|-----------|------|---------|-------------|
| **#type** | string | `'textfield'` | Type element (textfield, email, select, etc.) |
| **#title** | string | `'Nom complet'` | Label champ (mappé → `label` prop) |
| **#default_value** | mixed | `'Jean Dupont'` | Valeur par défaut (mappé → `value` prop) |
| **#required** | bool | `TRUE` | Champ obligatoire (astérisque, validation) |
| **#disabled** | bool | `FALSE` | Champ désactivé (non éditable) |
| **#placeholder** | string | `'Entrez votre nom'` | Placeholder input (mappé → `placeholder` prop) |
| **#description** | string | `'Aide contextuelle'` | Texte helper sous champ (mappé → `helper` prop) |
| **#attributes** | array | `['class' => ['custom']]` | Attributs HTML personnalisés |

### Input (textfield, email, password, tel, number, search, url)

| Propriété | Type | Exemple | Description |
|-----------|------|---------|-------------|
| **#maxlength** | int | `100` | Longueur maximale (HTML5 maxlength) |
| **#size** | int | `60` | Largeur visuelle input (attribut size) |
| **#autocomplete** | string | `'email'` | Autocomplétion navigateur (name, email, tel) |
| **#pattern** | string | `'[0-9]+'` | Validation regex HTML5 |
| **#min** | int | `0` | Valeur minimale (type="number") |
| **#max** | int | `1000000` | Valeur maximale (type="number") |
| **#step** | int | `10000` | Incrément (type="number") |

### Select

| Propriété | Type | Exemple | Description |
|-----------|------|---------|-------------|
| **#options** | array | `['key' => 'Label']` | Options select (mappé → `options` prop) |
| **#empty_option** | string | `'-- Sélectionner --'` | Option vide par défaut |
| **#empty_value** | string | `''` | Valeur option vide (default `''`) |
| **#multiple** | bool | `FALSE` | Select multiple (Ctrl+click) |

### Textarea

| Propriété | Type | Exemple | Description |
|-----------|------|---------|-------------|
| **#rows** | int | `6` | Nombre lignes visibles (mappé → `rows` prop) |
| **#cols** | int | `60` | Largeur colonnes (rarement utilisé) |
| **#resizable** | bool | `TRUE` | Resize vertical (CSS resize) |

### Checkboxes / Radios

| Propriété | Type | Exemple | Description |
|-----------|------|---------|-------------|
| **#options** | array | `['key' => 'Label']` | Options checkbox/radio |
| **#default_value** | array/string | `['key1']` (checkboxes) ou `'key1'` (radios) | Valeurs sélectionnées par défaut |

---

## 🚨 Pièges courants

### 1. Oublier `#required` (validation serveur)

```php
// ❌ Validation seulement client (contournable)
$form['email'] = [
  '#type' => 'email',
  '#attributes' => ['required' => 'required'], // HTML5 only
];

// ✅ Validation serveur + client (sécurisé)
$form['email'] = [
  '#type' => 'email',
  '#required' => TRUE, // Validation Drupal (serveur)
];
```

---

### 2. Oublier `validateForm()` (validation personnalisée)

```php
// ❌ Pas de validation personnalisée (seulement HTML5)
public function validateForm(array &$form, FormStateInterface $form_state) {
  // Vide (validation insuffisante)
}

// ✅ Validation métier (téléphone, budget, message)
public function validateForm(array &$form, FormStateInterface $form_state) {
  $phone = $form_state->getValue('phone');
  if ($phone && !preg_match('/^(\+33|0)[1-9](\d{2}){4}$/', $phone)) {
    $form_state->setErrorByName('phone', $this->t('Format téléphone invalide.'));
  }
}
```

---

### 3. Oublier `setErrorByName()` (message erreur)

```php
// ❌ Erreur silencieuse (utilisateur ne voit rien)
if ($budget < 50000) {
  return; // Pas de message
}

// ✅ Message erreur explicite (UX claire)
if ($budget < 50000) {
  $form_state->setErrorByName('budget', $this->t('Le budget minimum est de 50 000 €'));
}
```

---

### 4. Mapping incorrect options select

```php
// ❌ Options avec clés numériques (valeur ≠ affichage)
$form['type'] = [
  '#type' => 'select',
  '#options' => [
    0 => 'Appartement', // Valeur envoyée : 0 (ambigu)
    1 => 'Maison',
  ],
];

// ✅ Options avec clés string (valeur explicite)
$form['type'] = [
  '#type' => 'select',
  '#options' => [
    'apartment' => $this->t('Appartement'), // Valeur : 'apartment' (clair)
    'house' => $this->t('Maison'),
  ],
];
```

---

## 📋 Checklist intégration formulaire

### Avant de créer

- [ ] Identifier champs requis (specs fonctionnelles)
- [ ] Déterminer types Form API (textfield, email, select, etc.)
- [ ] Lister validations HTML5 (required, minlength, pattern)
- [ ] Lister validations métier (format, plages, dépendances)

### FormBase (PHP)

- [ ] Créer classe `FormBase` (namespace, getFormId, buildForm, validateForm, submitForm)
- [ ] Définir render array (`$form['field']` avec `#type`, `#title`, `#required`, etc.)
- [ ] Ajouter validations serveur (`setErrorByName()`)
- [ ] Implémenter soumission (Mail API, Entity save, redirection)
- [ ] Messages utilisateur (addStatus, addError)

### Templates (Twig)

- [ ] Override `form-element.html.twig` (mapping → form-field molecule)
- [ ] Tester types supportés (textfield, email, select, textarea)
- [ ] Vérifier affichage erreurs (message rouge sous champ)
- [ ] Vérifier helper (message gris sous champ)
- [ ] Tester required (astérisque, validation HTML5)

### Tests

- [ ] Soumettre formulaire vide (erreurs required)
- [ ] Soumettre valeurs invalides (erreurs validation)
- [ ] Soumettre valeurs valides (succès, redirection)
- [ ] Vérifier accessibilité (aria-invalid, aria-describedby, focus erreur)
- [ ] Vérifier responsive (mobile, tablet, desktop)

---

## 🎯 Exemple complet : Formulaire recherche bien

### FormBase : `src/Form/PropertySearchForm.php`

```php
<?php

namespace Drupal\ps_theme\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Formulaire recherche biens immobiliers
 */
class PropertySearchForm extends FormBase {

  public function getFormId() {
    return 'ps_property_search_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    
    // Transaction (radios)
    $form['transaction'] = [
      '#type' => 'radios',
      '#title' => $this->t('Type de transaction'),
      '#options' => [
        'buy' => $this->t('Acheter'),
        'rent' => $this->t('Louer'),
      ],
      '#default_value' => 'buy',
      '#required' => TRUE,
    ];

    // Type bien (select)
    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type de bien'),
      '#options' => [
        '' => $this->t('Tous types'),
        'apartment' => $this->t('Appartement'),
        'house' => $this->t('Maison'),
        'office' => $this->t('Bureau'),
        'land' => $this->t('Terrain'),
      ],
    ];

    // Localisation (textfield)
    $form['location'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Ville ou code postal'),
      '#placeholder' => $this->t('Paris, 75001...'),
      '#required' => TRUE,
    ];

    // Prix min (number)
    $form['price_min'] = [
      '#type' => 'number',
      '#title' => $this->t('Prix minimum (€)'),
      '#min' => 0,
      '#step' => 10000,
      '#placeholder' => $this->t('100000'),
    ];

    // Prix max (number)
    $form['price_max'] = [
      '#type' => 'number',
      '#title' => $this->t('Prix maximum (€)'),
      '#min' => 0,
      '#step' => 10000,
      '#placeholder' => $this->t('500000'),
    ];

    // Surface min (number)
    $form['area_min'] = [
      '#type' => 'number',
      '#title' => $this->t('Surface minimum (m²)'),
      '#min' => 0,
      '#step' => 10,
      '#placeholder' => $this->t('50'),
    ];

    // Chambres (select)
    $form['bedrooms'] = [
      '#type' => 'select',
      '#title' => $this->t('Nombre de chambres'),
      '#options' => [
        '' => $this->t('Indifférent'),
        '1' => $this->t('1 chambre'),
        '2' => $this->t('2 chambres'),
        '3' => $this->t('3 chambres'),
        '4' => $this->t('4+ chambres'),
      ],
    ];

    // Submit
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Rechercher'),
      '#attributes' => [
        'class' => ['ps-button', 'ps-button--primary'],
      ],
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validation prix min < prix max
    $price_min = $form_state->getValue('price_min');
    $price_max = $form_state->getValue('price_max');
    
    if ($price_min && $price_max && $price_min >= $price_max) {
      $form_state->setErrorByName('price_max', $this->t('Le prix maximum doit être supérieur au prix minimum.'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Construire paramètres URL pour page résultats
    $params = [
      'transaction' => $form_state->getValue('transaction'),
      'type' => $form_state->getValue('type'),
      'location' => $form_state->getValue('location'),
      'price_min' => $form_state->getValue('price_min'),
      'price_max' => $form_state->getValue('price_max'),
      'area_min' => $form_state->getValue('area_min'),
      'bedrooms' => $form_state->getValue('bedrooms'),
    ];

    // Filtrer valeurs vides
    $params = array_filter($params, function($value) {
      return $value !== '' && $value !== NULL;
    });

    // Redirection vers page résultats avec paramètres
    $url = Url::fromRoute('ps_theme.property_search_results', [], [
      'query' => $params,
    ]);
    $form_state->setRedirectUrl($url);
  }

}
```

---

## 🎯 Prochaines étapes

**Intégrer Form API** :
1. Créer classes `FormBase` dans `src/Form/`
2. Override templates `form-element.html.twig`, `form.html.twig`
3. Ajouter preprocess pour états visuels (data-error, aria-invalid)
4. Tester validations (HTML5 + serveur)

**Poursuivre avec** :
- **[Preprocess](./05-preprocess.md)** → Transformer données PHP → Twig
- **[Déploiement](./06-deploiement.md)** → Build production + cache

---

**Navigation** : [← Libraries](./03-libraries-assets.md) | [README](./README.md) | [Preprocess →](./05-preprocess.md)
