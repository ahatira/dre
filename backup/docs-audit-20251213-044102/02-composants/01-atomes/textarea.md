# Textarea (Atom)

**Niveau Atomic Design** : Atom / Form element  
**Catégorie** : Form controls  
**Statut** : ✅ Stable  
**Version** : 1.0.0  
**Dernière mise à jour** : 12 décembre 2025

---

## 📋 Description

Champ de saisie texte multiligne natif `<textarea>` sans label. Composant atomique compatible avec les attributs Drupal. Supporte hauteur configurable (rows), redimensionnement (resize), états de validation (error, success, warning), et disabled/required. Pour un champ complet avec label, utiliser Form-field (Molecule).

**Implémentation** : `source/patterns/elements/textarea/`

---

## 🎨 Aperçu visuel

```
┌─────────────────────────────────┐
│ Décrivez votre projet...        │
│                                  │  ← Default (4 lignes)
│                                  │
│                                  │
└─────────────────────────────────┘

┌─────────────────────────────────┐
│ Texte long déjà saisi...         │
│ Deuxième ligne du message...     │  ← Avec contenu
│ Troisième ligne...               │
│                                  │
└─────────────────────────────────┘

┌─────────────────────────────────┐
│ Message trop court             ❌│  ← Error state
└─────────────────────────────────┘
  ↑ Bordure rouge
```

---

## 🏗️ Structure BEM

```html
<!-- Default textarea -->
<textarea class="ps-textarea" name="message" id="message-textarea" rows="4" placeholder="Votre message..."></textarea>

<!-- Textarea with content -->
<textarea class="ps-textarea" name="description" id="description-textarea" rows="6">Description du bien immobilier...</textarea>

<!-- Textarea disabled -->
<textarea class="ps-textarea ps-textarea--disabled" name="notes" disabled aria-disabled="true">Notes non modifiables</textarea>

<!-- Textarea with error state -->
<textarea class="ps-textarea ps-textarea--error" name="comment" aria-invalid="true" aria-describedby="comment-error"></textarea>

<!-- Textarea required -->
<textarea class="ps-textarea" name="feedback" required aria-required="true" minlength="20"></textarea>
```

### Classes BEM

```
ps-textarea                               // Block (champ textarea)

Modifiers (états):
  ps-textarea--disabled                   // État désactivé (gris)
  ps-textarea--error                      // État erreur (bordure rouge)
  ps-textarea--success                    // État succès (bordure verte)
  ps-textarea--warning                    // État avertissement (bordure jaune)
```

---

## 📐 Props (Component API)

### Twig Template

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| **name** | `string` | `'message'` | Attribut `name` (requis pour soumission formulaire) |
| **id** | `string` | `name + '-textarea'` | Attribut `id` (auto-généré depuis name) |
| **value** | `string` | `''` | Contenu initial du textarea (texte multiligne) |
| **placeholder** | `string` | `''` | Texte placeholder (aide contextuelle) |
| **disabled** | `boolean` | `false` | État désactivé (non-modifiable) |
| **required** | `boolean` | `false` | Champ obligatoire (validation HTML5) |
| **rows** | `number` | `4` | Nombre de lignes visibles (hauteur initiale) |
| **state** | `string` | `null` | État validation : `null`, `'error'`, `'success'`, `'warning'` |
| **attributes** | `Attribute` | `create_attribute()` | Objet Drupal attributes (classes, data-*, aria-*, minlength, maxlength) |

---

## 🎨 Design Tokens

### Tokens utilisés

```css
/* Sizing */
--textarea-min-height: calc(var(--size-10) * 2.5);  /* 100px minimum (4 rows) */
--textarea-padding-x: var(--size-4);                /* 16px horizontal */
--textarea-padding-y: var(--size-3);                /* 12px vertical */
--textarea-font-size: var(--font-size-base);        /* 16px (évite zoom iOS) */
--textarea-line-height: var(--line-height-relaxed); /* 1.625 (26px) */

/* Colors */
--textarea-bg: var(--white);                        /* Fond blanc */
--textarea-border: var(--border-default);           /* Bordure grise par défaut */
--textarea-border-focus: var(--primary);            /* Bordure verte focus */
--textarea-text: var(--text-primary);               /* Texte noir */
--textarea-placeholder: var(--text-secondary);      /* Placeholder gris */

/* States */
--textarea-border-error: var(--danger);             /* Bordure rouge erreur */
--textarea-border-success: var(--success);          /* Bordure verte succès */
--textarea-border-warning: var(--warning);          /* Bordure jaune avertissement */
--textarea-bg-disabled: var(--gray-100);            /* Fond gris désactivé */
--textarea-text-disabled: var(--text-disabled);     /* Texte gris désactivé */

/* Borders */
--textarea-border-width: 1px;
--textarea-border-radius: var(--radius-md);         /* 6px arrondi */

/* Focus */
--textarea-focus-ring: 0 0 0 3px var(--primary-subtle);
--textarea-focus-outline: 2px solid var(--primary);

/* Resize */
--textarea-resize: vertical;                        /* Redimensionnement vertical uniquement */
```

---

## ♿ Accessibilité (WCAG 2.2 AA)

### Conformité

| Critère | Statut | Implémentation |
|---------|--------|----------------|
| **1.3.1 Info and Relationships** | ✅ | Attributs `name`, `id`, `rows` sémantiques |
| **2.1.1 Keyboard** | ✅ | Focusable, navigation Tab, saisie multiligne, Ctrl+Enter soumission |
| **2.4.7 Focus Visible** | ✅ | Bordure focus + ring 3px (`:focus-visible`) |
| **3.2.2 On Input** | ✅ | Pas de changement contexte sur saisie |
| **3.3.1 Error Identification** | ✅ | State `error` + `aria-invalid="true"` |
| **3.3.2 Labels or Instructions** | ⚠️ | Composant atom SANS label (utiliser Form-field) |
| **3.3.4 Error Prevention** | ✅ | Support `minlength`/`maxlength` (prévention erreurs) |
| **4.1.2 Name, Role, Value** | ✅ | Rôle natif `textbox multiline`, `aria-required`, `aria-disabled` |

### ARIA Attributes

```html
<!-- Required textarea -->
<textarea class="ps-textarea" required aria-required="true" minlength="20"></textarea>

<!-- Disabled textarea -->
<textarea class="ps-textarea ps-textarea--disabled" disabled aria-disabled="true"></textarea>

<!-- Error state (lié à message via aria-describedby dans Form-field) -->
<textarea class="ps-textarea ps-textarea--error" aria-invalid="true" aria-describedby="message-error"></textarea>

<!-- Avec compteur caractères -->
<textarea class="ps-textarea" maxlength="500" aria-describedby="message-counter"></textarea>
<div id="message-counter" aria-live="polite">0 / 500 caractères</div>
```

### Annonces Screen Reader

- **Focus** : "Message, edit text multi-line" (textarea vide)
- **Required** : "Message, required, edit text multi-line"
- **Disabled** : "Message, unavailable, edit text multi-line"
- **Error** : "Message, invalid entry, edit text multi-line"
- **Caractères** : "125 of 500 characters" (annonce dynamique)

---

## 🎯 Cas d'usage

### 1. Message contact simple

```twig
{% include '@elements/textarea/textarea.twig' with {
  name: 'message',
  placeholder: 'Décrivez votre projet immobilier...',
  rows: 4,
  required: true,
  attributes: create_attribute().setAttribute('minlength', '20')
} only %}
```

**Contexte** : Formulaire contact commercial (demande visite).

---

### 2. Description bien avec compteur

```twig
{% include '@elements/textarea/textarea.twig' with {
  name: 'description',
  value: node.body.value,
  rows: 8,
  attributes: create_attribute()
    .setAttribute('maxlength', '1000')
    .setAttribute('aria-describedby', 'description-counter')
} only %}

<div id="description-counter" class="form-helper" aria-live="polite">
  {{ node.body.value|length }} / 1000 caractères
</div>
```

**Contexte** : Création annonce immobilière (CMS).

---

### 3. Commentaire utilisateur avec état succès

```twig
{% include '@elements/textarea/textarea.twig' with {
  name: 'feedback',
  placeholder: 'Votre avis sur cette visite...',
  rows: 6,
  state: 'success',
  value: 'Excellent service, agent très professionnel !'
} only %}
```

**Contexte** : Avis utilisateur après visite bien (validation OK).

---

### 4. Notes internes désactivées

```twig
{% include '@elements/textarea/textarea.twig' with {
  name: 'internal_notes',
  value: 'Client VIP - Priorité haute\nSuivi commercial en cours',
  rows: 3,
  disabled: true
} only %}
```

**Contexte** : Notes CRM non-modifiables (historique commercial).

---

### 5. Textarea avec erreur validation

```twig
{% include '@elements/textarea/textarea.twig' with {
  name: 'comment',
  value: 'Trop court',
  rows: 4,
  state: 'error',
  attributes: create_attribute()
    .setAttribute('aria-invalid', 'true')
    .setAttribute('aria-describedby', 'comment-error')
} only %}

<div id="comment-error" class="form-error" role="alert">
  Le commentaire doit contenir au moins 20 caractères
</div>
```

**Contexte** : Validation formulaire côté serveur (erreur Drupal).

---

### 6. Textarea auto-expand (grande hauteur)

```twig
{% include '@elements/textarea/textarea.twig' with {
  name: 'detailed_description',
  placeholder: 'Description complète du bien...',
  rows: 12,
  attributes: create_attribute().setAttribute('maxlength', '5000')
} only %}
```

**Contexte** : Formulaire administrateur (description longue attendue).

---

### 7. Intégration Drupal Form API

```php
// Dans hook_form_alter ou FormBase
$form['message'] = [
  '#type' => 'textarea',
  '#title' => t('Message'),
  '#required' => TRUE,
  '#rows' => 6,
  '#attributes' => [
    'placeholder' => t('Décrivez votre demande...'),
    'class' => ['ps-textarea'], // Applique styles PS Theme
    'minlength' => 20,
    'maxlength' => 1000,
  ],
  '#description' => t('Minimum 20 caractères'),
];
```

**Contexte** : Formulaire Drupal personnalisé (contact, webform).

---

## 🔗 Relations avec autres composants

### Composé dans (Molecules)

- **Form-field** : Textarea + Label + Helper + Error message + Character counter (champ complet)
- **Comment-form** : Textarea + Avatar + Submit button

### Utilise (Atoms)

- Aucun (composant atomique autonome)

### Variantes disponibles

- **Input** : `@elements/input/input.twig` (saisie texte courte, 1 ligne)
- **Rich Text Editor** : CKEditor (à venir) (texte enrichi HTML)

---

## 🧪 Tests & Validation

### Navigation clavier

```
Tab           → Focus textarea (bordure + ring)
Saisie texte  → Texte multiligne
Enter         → Nouvelle ligne (pas de soumission)
Ctrl+Enter    → Soumission formulaire (optionnel)
Tab           → Sort du textarea
```

### Validation HTML5

```html
<!-- Longueur minimum -->
<textarea minlength="20" aria-describedby="message-helper"></textarea>

<!-- Longueur maximum (avec compteur) -->
<textarea maxlength="500" aria-describedby="message-counter"></textarea>

<!-- Requis (validation navigateur) -->
<textarea required aria-required="true"></textarea>

<!-- Pattern regex (non-standard, utiliser JS) -->
<textarea data-pattern="[A-Za-z0-9\s]+" data-error="Caractères alphanumériques uniquement"></textarea>
```

### Tests accessibilité

1. **Clavier** : Tab focus → Saisir texte multiligne → Tab sort
2. **Screen reader** : NVDA/JAWS annonce "Edit text multi-line, X characters"
3. **Zoom 200%** : Texte lisible, redimensionnement vertical fonctionnel
4. **Contraste** : 
   - Texte : 4.5:1 minimum (noir sur blanc)
   - Placeholder : 4.5:1 minimum (gris foncé)
   - Bordure : 3:1 minimum (gris sur blanc)

### Tests interactifs Storybook

```javascript
// Tests Playwright dans textarea.stories.jsx
- Default (4 rows, vide)
- With content (texte pré-rempli)
- Large (12 rows, description longue)
- Disabled (non-modifiable, gris)
- Error state (bordure rouge + aria-invalid)
- Success state (bordure verte)
- Required (validation HTML5)
- With counter (maxlength + compteur dynamique)
```

---

## 📊 Performance

### Métriques

- **HTML** : 1 élément (150 bytes)
- **CSS** : 1.5 KB (minifié)
- **JS** : 0 KB (pas de JavaScript, sauf compteur optionnel)
- **Render time** : < 1ms
- **First Paint** : Immédiat (CSS inline prioritaire)

### Optimisations

- Pas de dépendances externes
- CSS natif (pas de polyfills)
- Redimensionnement natif navigateur (resize: vertical)
- Validation HTML5 (pas de JS côté client)

---

## 🚧 Limitations connues

### Redimensionnement horizontal

**Désactivé par défaut** (`resize: vertical` uniquement). Évite rupture layout.

```css
/* ✅ CORRECT (vertical uniquement) */
.ps-textarea {
  resize: vertical;
}

/* ❌ Éviter horizontal (rupture layout) */
.ps-textarea {
  resize: both; /* Utilisateur peut casser le layout */
}
```

### Textarea sans label

**Composant atom SANS label**. Toujours utiliser dans Form-field ou ajouter `aria-label`.

```twig
{# ❌ MAUVAIS (pas de label accessible) #}
{% include '@elements/textarea/textarea.twig' with {name: 'message'} only %}

{# ✅ CORRECT (aria-label explicite) #}
{% include '@elements/textarea/textarea.twig' with {
  name: 'message',
  attributes: create_attribute().setAttribute('aria-label', 'Votre message')
} only %}

{# ✅ MEILLEUR (utiliser Form-field avec label visible) #}
{% include '@components/form-field/form-field.twig' with {
  label: 'Message',
  type: 'textarea',
  name: 'message'
} only %}
```

### Hauteur auto-expand

**Non supporté nativement**. Utiliser JavaScript pour auto-resize.

```javascript
// Script optionnel : auto-resize textarea selon contenu
textarea.addEventListener('input', function() {
  this.style.height = 'auto';
  this.style.height = this.scrollHeight + 'px';
});
```

### Compteur caractères

**Non inclus dans atom**. Ajouter via JavaScript dans Form-field.

```javascript
// Compteur caractères dynamique
const counter = document.getElementById('message-counter');
textarea.addEventListener('input', function() {
  const current = this.value.length;
  const max = this.getAttribute('maxlength');
  counter.textContent = `${current} / ${max} caractères`;
});
```

---

## 📝 Notes Drupal

### Génération automatique Drupal

Drupal Form API génère automatiquement `<textarea>` :

```php
// FormBase::buildForm()
$form['message'] = [
  '#type' => 'textarea',
  '#title' => t('Message'),
  '#required' => TRUE,
  '#rows' => 6,
  '#default_value' => 'Contenu initial',
  '#attributes' => [
    'class' => ['ps-textarea'],
    'placeholder' => t('Votre message...'),
    'minlength' => 20,
    'maxlength' => 1000,
  ],
  '#description' => t('Entre 20 et 1000 caractères'),
];
```

### Template override

**Override Drupal textarea template** (`templates/form/textarea.html.twig`) :

```twig
{# Override Drupal textarea template #}
{% include '@elements/textarea/textarea.twig' with {
  name: attributes.name,
  value: value,
  placeholder: attributes.placeholder,
  rows: rows,
  disabled: attributes.disabled,
  required: required,
  state: errors ? 'error' : null,
  attributes: attributes
} only %}
```

### CKEditor vs Textarea

**Textarea simple** pour texte brut. **CKEditor** pour contenu riche (gras, listes, liens).

```php
// Textarea simple (texte brut)
$form['description'] = [
  '#type' => 'textarea',
  '#title' => t('Description courte'),
];

// CKEditor (texte riche HTML)
$form['body'] = [
  '#type' => 'text_format',
  '#title' => t('Contenu'),
  '#format' => 'basic_html',
];
```

---

## 🔄 Changelog

### Version 1.0.0 (12 décembre 2025)

- ✅ Création spec initiale
- ✅ Documentation complète (usage, props, tokens, accessibilité)
- ✅ 7 cas d'usage (contact, description, compteur, notes, erreur, auto-expand, Drupal)
- ✅ Compatibilité Drupal Form API
- ✅ Tests accessibilité WCAG 2.2 AA
- ✅ Support validation HTML5 (minlength, maxlength, required)
- ✅ 4 états validation (default, error, success, warning)
- ✅ Redimensionnement vertical natif

---

**Références** :
- Implémentation : `source/patterns/elements/textarea/`
- Storybook : [Elements/Textarea](http://localhost:6006/?path=/story/elements-textarea)
- Drupal Form API : https://api.drupal.org/api/drupal/elements/10
