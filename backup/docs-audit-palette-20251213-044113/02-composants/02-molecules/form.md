# Form (Molecule)

**Niveau Atomic Design** : Molecule / Form container  
**Catégorie** : Forms  
**Statut** : ✅ Stable  
**Version** : 1.0.0  
**Dernière mise à jour** : 12 décembre 2025

---

## 📋 Description

Wrapper `<form>` Drupal minimaliste pour conteneurs de formulaires. Gère méthode HTTP (GET/POST), action, attributs Drupal, et slot `children` pour contenu (champs, boutons). Composant simpliste délibérément : la logique complexe (validation, états, messages) est gérée par Drupal Form API et les composants enfants (Form-field).

**Implémentation** : `source/patterns/components/form/`

---

## 🎨 Aperçu visuel

```
╔════════════════════════════════╗
║ <form method="POST">           ║
║                                ║
║  [Label]                       ║
║  ┌──────────────────────────┐ ║
║  │ Input field              │ ║
║  └──────────────────────────┘ ║
║                                ║
║  [Label]                       ║
║  ┌──────────────────────────┐ ║
║  │ Textarea                 │ ║
║  │                          │ ║
║  └──────────────────────────┘ ║
║                                ║
║  [ Submit Button ]             ║
║                                ║
║ </form>                        ║
╚════════════════════════════════╝
```

---

## 🏗️ Structure BEM

```html
<!-- Default form (POST) -->
<form class="ps-form" method="POST" action="/submit">
  <!-- Children: fields, buttons, etc. -->
</form>

<!-- Search form (GET) -->
<form class="ps-form ps-form--search" method="GET" action="/search" role="search">
  <!-- Search fields -->
</form>

<!-- Form with Drupal attributes -->
<form class="ps-form" method="POST" data-drupal-selector="contact-form" novalidate>
  <!-- Form fields -->
</form>

<!-- Inline form (horizontal layout) -->
<form class="ps-form ps-form--inline" method="POST">
  <input type="email" class="ps-input" />
  <button type="submit" class="ps-button">Submit</button>
</form>
```

### Classes BEM

```
ps-form                                   // Block (form wrapper)

Modifiers (layout):
  ps-form--inline                         // Layout horizontal (champs côte à côte)
  ps-form--search                         // Formulaire recherche (rôle search)
  ps-form--filter                         // Formulaire filtres (sidebar)
```

---

## 📐 Props (Component API)

### Twig Template

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| **attributes** | `Attribute` | `create_attribute()` | Objet Drupal attributes (method, action, class, data-*, novalidate) |
| **children** | `slot` | `''` | Contenu du formulaire (champs, boutons, messages) |

**Note** : Composant volontairement minimaliste. Drupal Form API gère automatiquement `method`, `action`, `enctype`, `novalidate`, etc.

---

## 🎨 Design Tokens

### Tokens utilisés

```css
/* Layout */
--form-max-width: var(--content-width-sm);    /* 640px largeur max (centré) */
--form-gap: var(--size-6);                     /* 24px espacement vertical entre champs */
--form-padding: var(--size-8);                 /* 32px padding interne (sur mobile) */

/* Inline variant */
--form-inline-gap: var(--size-4);              /* 16px espacement horizontal */
--form-inline-align: center;                   /* Alignement vertical */

/* Colors */
--form-bg: var(--white);                       /* Fond blanc (si encadré) */
--form-border: var(--border-default);          /* Bordure légère (optionnelle) */
--form-shadow: var(--shadow-sm);               /* Ombre légère (optionnelle) */
```

---

## ♿ Accessibilité (WCAG 2.2 AA)

### Conformité

| Critère | Statut | Implémentation |
|---------|--------|----------------|
| **1.3.1 Info and Relationships** | ✅ | Élément `<form>` sémantique + attributs natifs |
| **2.4.1 Bypass Blocks** | ✅ | Support `role="search"` pour formulaires recherche |
| **3.3.1 Error Identification** | ✅ | Gestion via Form-field (composant enfant) |
| **3.3.2 Labels or Instructions** | ✅ | Gestion via Form-field (labels explicites) |
| **4.1.2 Name, Role, Value** | ✅ | Rôle `form` natif, `role="search"` pour recherche |

### ARIA Attributes

```html
<!-- Form avec label accessible -->
<form class="ps-form" method="POST" aria-labelledby="form-title">
  <h2 id="form-title">Contactez-nous</h2>
  <!-- Fields -->
</form>

<!-- Search form avec rôle search -->
<form class="ps-form ps-form--search" method="GET" role="search" aria-label="Recherche de biens">
  <!-- Search fields -->
</form>

<!-- Form avec description -->
<form class="ps-form" method="POST" aria-describedby="form-description">
  <p id="form-description">Tous les champs marqués * sont obligatoires</p>
  <!-- Fields -->
</form>
```

### Annonces Screen Reader

- **Form focus** : "Form, Contactez-nous" (si aria-labelledby)
- **Search form** : "Search, Recherche de biens" (si role="search")
- **Submit** : "Submit button" (bouton soumission)

---

## 🎯 Cas d'usage

### 1. Formulaire contact simple

```twig
{% include '@components/form/form.twig' with {
  attributes: create_attribute()
    .setAttribute('method', 'POST')
    .setAttribute('action', '/contact/submit')
    .setAttribute('aria-labelledby', 'contact-title')
} only %}
  <h2 id="contact-title">Contactez un agent</h2>
  
  {% include '@components/form-field/form-field.twig' with {
    label: 'Nom',
    type: 'text',
    name: 'name',
    required: true
  } only %}
  
  {% include '@components/form-field/form-field.twig' with {
    label: 'Email',
    type: 'email',
    name: 'email',
    required: true
  } only %}
  
  {% include '@components/form-field/form-field.twig' with {
    label: 'Message',
    type: 'textarea',
    name: 'message',
    rows: 6,
    required: true
  } only %}
  
  {% include '@elements/button/button.twig' with {
    text: 'Envoyer',
    type: 'submit'
  } only %}
{% endinclude %}
```

**Contexte** : Page contact commercial.

---

### 2. Barre de recherche (GET)

```twig
{% include '@components/form/form.twig' with {
  attributes: create_attribute()
    .setAttribute('method', 'GET')
    .setAttribute('action', '/search')
    .setAttribute('role', 'search')
    .setAttribute('aria-label', 'Recherche de biens immobiliers')
    .addClass('ps-form--inline')
} only %}
  {% include '@elements/input/input.twig' with {
    type: 'search',
    name: 'q',
    placeholder: 'Appartement, villa, Paris...',
    attributes: create_attribute().setAttribute('aria-label', 'Rechercher un bien')
  } only %}
  
  {% include '@elements/button/button.twig' with {
    text: 'Rechercher',
    type: 'submit',
    icon: 'search'
  } only %}
{% endinclude %}
```

**Contexte** : Header site (recherche globale).

---

### 3. Formulaire Drupal (Form API)

```php
// Dans FormBase::buildForm()
public function buildForm(array $form, FormStateInterface $form_state) {
  $form['#attributes']['class'][] = 'ps-form';
  
  $form['name'] = [
    '#type' => 'textfield',
    '#title' => $this->t('Nom'),
    '#required' => TRUE,
  ];
  
  $form['email'] = [
    '#type' => 'email',
    '#title' => $this->t('Email'),
    '#required' => TRUE,
  ];
  
  $form['actions'] = [
    '#type' => 'actions',
  ];
  
  $form['actions']['submit'] = [
    '#type' => 'submit',
    '#value' => $this->t('Envoyer'),
    '#attributes' => ['class' => ['ps-button', 'ps-button--primary']],
  ];
  
  return $form;
}
```

**Contexte** : Module Drupal custom (contact, configuration).

---

### 4. Filtres sidebar (layout vertical)

```twig
{% include '@components/form/form.twig' with {
  attributes: create_attribute()
    .setAttribute('method', 'GET')
    .setAttribute('action', '/properties')
    .addClass('ps-form--filter')
} only %}
  <h3>Filtrer les résultats</h3>
  
  {% include '@components/form-field/form-field.twig' with {
    label: 'Type de bien',
    type: 'select',
    name: 'type',
    options: [{value: '', label: 'Tous'}, {value: 'apt', label: 'Appartement'}]
  } only %}
  
  {% include '@components/form-field/form-field.twig' with {
    label: 'Prix maximum',
    type: 'number',
    name: 'max_price',
    placeholder: '500000'
  } only %}
  
  {% include '@elements/button/button.twig' with {
    text: 'Appliquer',
    type: 'submit',
    variant: 'primary',
    block: true
  } only %}
{% endinclude %}
```

**Contexte** : Page résultats recherche immobilière.

---

### 5. Newsletter inline (horizontal)

```twig
{% include '@components/form/form.twig' with {
  attributes: create_attribute()
    .setAttribute('method', 'POST')
    .setAttribute('action', '/newsletter/subscribe')
    .addClass('ps-form--inline')
} only %}
  {% include '@elements/input/input.twig' with {
    type: 'email',
    name: 'email',
    placeholder: 'Votre email',
    required: true,
    attributes: create_attribute().setAttribute('aria-label', 'Email newsletter')
  } only %}
  
  {% include '@elements/button/button.twig' with {
    text: 'S\'inscrire',
    type: 'submit',
    variant: 'secondary'
  } only %}
{% endinclude %}
```

**Contexte** : Footer site (inscription newsletter).

---

### 6. Upload fichier (multipart)

```twig
{% include '@components/form/form.twig' with {
  attributes: create_attribute()
    .setAttribute('method', 'POST')
    .setAttribute('action', '/upload')
    .setAttribute('enctype', 'multipart/form-data')
} only %}
  {% include '@components/form-field/form-field.twig' with {
    label: 'Documents',
    type: 'file',
    name: 'documents[]',
    attributes: create_attribute().setAttribute('multiple', true)
  } only %}
  
  {% include '@elements/button/button.twig' with {
    text: 'Téléverser',
    type: 'submit'
  } only %}
{% endinclude %}
```

**Contexte** : Formulaire dossier locataire (pièces justificatives).

---

### 7. Form avec validation Drupal

```twig
{# Template Drupal : templates/form/form.html.twig #}
{% include '@components/form/form.twig' with {
  attributes: attributes
} only %}
  {{ children }}
{% endinclude %}
```

```php
// Validation Drupal Form API
public function validateForm(array &$form, FormStateInterface $form_state) {
  if (empty($form_state->getValue('email'))) {
    $form_state->setErrorByName('email', $this->t('Email requis'));
    // → Drupal ajoute automatiquement aria-invalid + message erreur
  }
}
```

**Contexte** : Tout formulaire Drupal (Form API gère validation).

---

## 🔗 Relations avec autres composants

### Composé dans (Organisms)

- **Contact-form** : Form + multiples Form-fields + Button
- **Search-bar** : Form + Input search + Button submit
- **Filter-panel** : Form + multiples Selects/Checkboxes + Button

### Utilise (Molecules/Atoms)

- **Form-field** : `@components/form-field/form-field.twig` (champs complets)
- **Button** : `@elements/button/button.twig` (soumission formulaire)
- **Alert** : `@components/alert/alert.twig` (messages succès/erreur globaux)

---

## 🧪 Tests & Validation

### Navigation clavier

```
Tab           → Focus premier champ
Tab/Shift+Tab → Navigation entre champs
Enter         → Soumission formulaire (si focus dans input/button)
Esc           → Annulation (si modal)
```

### Tests accessibilité

1. **Clavier** : Tab navigue entre tous les champs, Enter soumet
2. **Screen reader** : Annonce "Form" + label si `aria-labelledby`
3. **Validation HTML5** : Navigateur affiche erreurs (email invalide, champs requis)
4. **Rôle search** : Formulaires recherche avec `role="search"` annoncés correctement

### Tests interactifs Storybook

```javascript
// Tests Playwright dans form.stories.jsx
- Default (form POST vide)
- Contact form (fields + submit)
- Search form (GET + role="search")
- Inline form (newsletter horizontal)
- Filter form (sidebar vertical)
- With validation (error messages)
```

---

## 📊 Performance

### Métriques

- **HTML** : 1 élément `<form>` (50 bytes base)
- **CSS** : 0.5 KB (minifié)
- **JS** : 0 KB (pas de JavaScript, validation HTML5 native)
- **Render time** : < 1ms
- **First Paint** : Immédiat

### Optimisations

- Composant minimaliste (délègue logique à Drupal)
- Pas de dépendances JS
- Validation HTML5 native (pas de polyfills)

---

## 🚧 Limitations connues

### Validation côté client

**Non incluse** dans ce composant. Utiliser :
- **HTML5** : `required`, `pattern`, `minlength` (natif navigateur)
- **Drupal Form API** : Validation côté serveur (recommandé)
- **JavaScript custom** : Validation avancée (si besoin)

```html
<!-- ✅ Validation HTML5 (navigateur) -->
<input type="email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" />

<!-- ✅ Validation Drupal (serveur) -->
$form['email'] = [
  '#type' => 'email',
  '#required' => TRUE,
  '#element_validate' => ['::validateEmail'],
];
```

### Messages globaux

**Non inclus** dans ce composant. Ajouter Alert séparément :

```twig
{% if success_message %}
  {% include '@components/alert/alert.twig' with {
    message: success_message,
    type: 'success'
  } only %}
{% endif %}

{% include '@components/form/form.twig' %}
  {# Form fields #}
{% endinclude %}
```

---

## 📝 Notes Drupal

### Form API génère automatiquement

Drupal Form API génère automatiquement `<form>` avec tous attributs :

```php
// FormBase::buildForm() génère :
// <form method="POST" action="/form/submit" 
//       id="my-form" 
//       data-drupal-selector="my-form"
//       novalidate>

$form['#attributes']['class'][] = 'ps-form'; // Ajoute classe PS Theme
```

### Template override recommandé

**Override template Drupal** (`templates/form/form.html.twig`) :

```twig
{#
 * Override Drupal form template
 * Utilise composant PS Theme form
 #}
{% include '@components/form/form.twig' with {
  attributes: attributes
} only %}
  {{ children }}
{% endinclude %}
```

### Novalidate Drupal

Drupal ajoute `novalidate` par défaut (validation serveur prioritaire).

```html
<!-- Drupal génère automatiquement -->
<form method="POST" novalidate>
  <!-- Validation côté serveur via Form API -->
</form>
```

Pour activer validation HTML5 :

```php
$form['#attributes']['novalidate'] = FALSE; // Supprime novalidate
```

---

## 🔄 Changelog

### Version 1.0.0 (12 décembre 2025)

- ✅ Création spec initiale
- ✅ Documentation complète (usage, props, accessibilité)
- ✅ 7 cas d'usage (contact, recherche, Drupal, filtres, newsletter, upload, validation)
- ✅ Compatibilité Drupal Form API
- ✅ Support `role="search"` pour formulaires recherche
- ✅ Layout variants (default, inline, filter)
- ✅ Tests accessibilité WCAG 2.2 AA
- ✅ Composant minimaliste (délègue logique à Drupal + composants enfants)

---

**Références** :
- Implémentation : `source/patterns/components/form/`
- Storybook : [Components/Form](http://localhost:6006/?path=/story/components-form)
- Drupal Form API : https://api.drupal.org/api/drupal/core!lib!Drupal!Core!Form!FormBase.php/10
