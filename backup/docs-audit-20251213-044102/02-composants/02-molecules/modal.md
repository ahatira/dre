# Modal (Molecule)

**Niveau Atomic Design** : Molecule / Overlay  
**Catégorie** : Dialog  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Fenêtre modale accessible affichant du contenu superposé à la page principale. Utilise `<dialog>` HTML ou un `div[role="dialog"]` avec gestion du focus (focus trap), fermeture sur Escape, backdrop cliquable, et ARIA (`aria-modal`, `aria-labelledby`, `aria-describedby`). Options de taille, déclencheur, et contenu personnalisable.

---

## 🎨 Aperçu visuel

```
[Backdrop semi-transparent]
  ┌──────────────────────┐
  │  [X]                 │
  │  Titre du modal      │
  │  ──────────────────  │
  │  Contenu...          │
  │                      │
  │  [Annuler] [Valider] │
  └──────────────────────┘
```

---

## 🏗️ Structure BEM

```html
<dialog class="ps-modal ps-modal--medium" id="modal-1" aria-labelledby="modal-1-title" aria-describedby="modal-1-desc" data-modal>
  <div class="ps-modal__container">
    <div class="ps-modal__header">
      <h2 class="ps-modal__title" id="modal-1-title">Titre du modal</h2>
      <button class="ps-modal__close" type="button" aria-label="Fermer" data-modal-close>
        <span class="ps-modal__close-icon" data-icon="close" aria-hidden="true"></span>
      </button>
    </div>
    <div class="ps-modal__body" id="modal-1-desc">
      <p>Contenu du modal...</p>
    </div>
    <div class="ps-modal__footer">
      <button class="ps-button ps-button--secondary" type="button" data-modal-close>Annuler</button>
      <button class="ps-button ps-button--primary" type="button">Valider</button>
    </div>
  </div>
</dialog>
```

### Classes BEM

```
ps-modal                                  // Block (<dialog> ou div[role=dialog])
  ps-modal__container                     // Conteneur interne
  ps-modal__header                        // En-tête
  ps-modal__title                         // Titre (h2–h4)
  ps-modal__close                         // Bouton fermeture
  ps-modal__close-icon                    // Icône fermeture
  ps-modal__body                          // Contenu principal
  ps-modal__footer                        // Actions (boutons)

Modificateurs :
  ps-modal--small                         // ~400px max-width
  ps-modal--medium                        // ~600px (par défaut)
  ps-modal--large                         // ~800px
  ps-modal--fullscreen                    // Plein écran (mobile)
  ps-modal--no-backdrop                   // Sans backdrop cliquable
  is-open                                 // État ouvert (si div+JS)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Modal'
status: stable
group: molecules
description: 'Modal accessible avec dialog, focus trap, backdrop, et fermeture Escape/click.'

props:
  type: object
  properties:
    id:
      type: string
      description: 'Identifiant unique (génère aria-labelledby/describedby)'
    title:
      type: string
      title: Titre
    content:
      type: string
      title: Contenu HTML
    footer:
      type: string
      title: HTML du footer (boutons)
      description: 'Si vide, pas de footer'
    size:
      type: string
      enum: ['small','medium','large','fullscreen']
      default: 'medium'
    closeLabel:
      type: string
      default: 'Fermer'
    showCloseButton:
      type: boolean
      default: true
    backdropDismiss:
      type: boolean
      default: true
    headingLevel:
      type: string
      enum: ['h2','h3','h4']
      default: 'h2'
    open:
      type: boolean
      default: false
      description: 'État initial (pour <dialog open>)'
    attributes:
      type: Drupal\Core\Template\Attribute
  required:
    - id
    - title
    - content
```

---

## 🎭 Variants

- `size`: `small`|`medium`|`large`|`fullscreen`.
- `showCloseButton`: afficher/masquer le bouton X.
- `backdropDismiss`: fermer au clic sur le backdrop.
- `open`: état initial ouvert (pour pré-render).

---

## 🎨 Design Tokens

- Typo: `--ps-font-family-primary`, `--ps-font-size-base`, `--ps-font-weight-semibold`
- Couleurs: `--ps-color-neutral-900` (titre), `--ps-color-neutral-700` (corps), `--ps-color-neutral-100` (bg header/footer)
- Backdrop: `rgba(0, 0, 0, 0.5)` ou `--ps-color-overlay-backdrop`
- Bordures: `--ps-border-radius-md`, `--ps-border-width-default`
- Espacements: `--ps-spacing-4|5|6`
- Ombres: `--ps-shadow-lg` (modal suspendu)
- Z-index: `--ps-z-index-modal` (ex: 1000)

Proposition si manquant: `--ps-color-overlay-backdrop`, `--ps-z-index-modal`.

---

## 🔧 Template Twig

```twig
{#
 * Template for Modal molecule.
 * Variables: voir API YAML
 #}

{% set heading = headingLevel|default('h2') %}
{% set size = size|default('medium') %}
{% set modal_id = id %}
{% set title_id = modal_id ~ '-title' %}
{% set desc_id = modal_id ~ '-desc' %}
{% set root_classes = [
  'ps-modal',
  'ps-modal--' ~ size
] %}

<dialog {{ attributes.addClass(root_classes) }} id="{{ modal_id }}" aria-labelledby="{{ title_id }}" aria-describedby="{{ desc_id }}" data-modal {% if backdropDismiss|default(true) %}data-backdrop-dismiss{% endif %} {% if open %}open{% endif %}>
  <div class="ps-modal__container">
    <div class="ps-modal__header">
      <{{ heading }} class="ps-modal__title" id="{{ title_id }}">{{ title }}</{{ heading }}>
      {% if showCloseButton|default(true) %}
        <button class="ps-modal__close" type="button" aria-label="{{ closeLabel|default('Fermer') }}" data-modal-close>
          <span class="ps-modal__close-icon" data-icon="close" aria-hidden="true"></span>
        </button>
      {% endif %}
    </div>
    <div class="ps-modal__body" id="{{ desc_id }}">
      {{ content|raw }}
    </div>
    {% if footer %}
      <div class="ps-modal__footer">
        {{ footer|raw }}
      </div>
    {% endif %}
  </div>
</dialog>
```

---

## 🎨 Styles SCSS

```scss
.ps-modal {
  font-family: var(--ps-font-family-primary);
  color: var(--ps-color-neutral-900, #111);
  border: none;
  padding: 0;
  max-width: 600px;
  width: 90vw;
  border-radius: var(--ps-border-radius-md, 8px);
  box-shadow: var(--ps-shadow-lg, 0 10px 25px rgba(0, 0, 0, 0.2));

  &::backdrop {
    background: rgba(0, 0, 0, 0.5);
  }

  &__container {
    display: flex; flex-direction: column;
  }

  &__header {
    display: flex; align-items: center; justify-content: space-between; gap: var(--ps-spacing-3, 12px);
    padding: var(--ps-spacing-5, 20px) var(--ps-spacing-5, 20px) var(--ps-spacing-4, 16px);
    background: var(--ps-color-neutral-100, #F3F6F9);
    border-bottom: var(--ps-border-width-default, 1px) solid var(--ps-color-neutral-300, #D2D7DB);
  }

  &__title {
    margin: 0; font-weight: var(--ps-font-weight-semibold, 600);
  }

  &__close {
    background: none; border: none; cursor: pointer; padding: var(--ps-spacing-2, 8px); line-height: 1;
    &:focus-visible { outline: var(--ps-border-width-focus, 2px) solid var(--ps-color-interactive-focus-outline, #0B5FFF); outline-offset: 2px; }
  }
  &__close-icon { width: 20px; height: 20px; }

  &__body {
    padding: var(--ps-spacing-5, 20px);
    overflow-y: auto;
    max-height: 70vh;
  }

  &__footer {
    display: flex; align-items: center; justify-content: flex-end; gap: var(--ps-spacing-3, 12px);
    padding: var(--ps-spacing-4, 16px) var(--ps-spacing-5, 20px);
    background: var(--ps-color-neutral-100, #F3F6F9);
    border-top: var(--ps-border-width-default, 1px) solid var(--ps-color-neutral-300, #D2D7DB);
  }

  // Sizes
  &--small { max-width: 400px; }
  &--medium { max-width: 600px; }
  &--large { max-width: 800px; }
  &--fullscreen {
    width: 100vw; height: 100vh; max-width: none; border-radius: 0;
    .ps-modal__body { max-height: none; }
  }
}
```

---

## ♿ Accessibilité

- `<dialog>` natif ou `role="dialog"` + `aria-modal="true"` (empêche l'accès au contenu sous-jacent).
- `aria-labelledby` + `aria-describedby` pour title/body.
- Focus trap : focus initial sur le premier élément interactif ou le bouton fermer ; Tab/Shift+Tab circulent dans le modal uniquement.
- Fermeture : Escape (natif pour `<dialog>`) ou clic backdrop si `backdropDismiss`.
- Retour de focus : restaurer le focus sur le déclencheur après fermeture.

---

## 📱 Comportement responsive

- Largeur fluide (90vw max ou taille fixe selon variant).
- `fullscreen` sur petits écrans (optionnel).
- Scroll interne si contenu déborde (`max-height: 70vh` sur body).

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-modal/ps-modal.twig' with {
  id: 'confirm-delete',
  title: 'Confirmer la suppression',
  content: '<p>Êtes-vous sûr de vouloir supprimer cet élément ?</p>',
  footer: '<button class="ps-button ps-button--secondary" type="button" data-modal-close>Annuler</button><button class="ps-button ps-button--danger" type="button">Supprimer</button>',
  size: 'small',
  showCloseButton: true,
  backdropDismiss: true,
  headingLevel: 'h3'
} %}

<button type="button" onclick="document.getElementById('confirm-delete').showModal()">Ouvrir</button>
```

---

## 🔌 JavaScript behavior (facultatif)

```js
// Minimal accessible Modal behavior for <dialog>
function setupModal(dialog) {
  const backdropDismiss = dialog.hasAttribute('data-backdrop-dismiss');
  const closeBtns = dialog.querySelectorAll('[data-modal-close]');
  
  // Close handlers
  closeBtns.forEach(btn => {
    btn.addEventListener('click', () => dialog.close());
  });

  // Backdrop click (only if backdropDismiss)
  if (backdropDismiss) {
    dialog.addEventListener('click', (e) => {
      if (e.target === dialog) dialog.close();
    });
  }

  // Focus trap (basic: prevent Tab outside dialog)
  dialog.addEventListener('keydown', (e) => {
    if (e.key === 'Tab') {
      const focusables = Array.from(dialog.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'));
      if (focusables.length === 0) return;
      const first = focusables[0];
      const last = focusables[focusables.length - 1];
      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }
  });

  // Return focus to trigger on close
  let trigger = null;
  dialog.addEventListener('cancel', () => {
    if (trigger) trigger.focus();
  });
  dialog.addEventListener('close', () => {
    if (trigger) trigger.focus();
  });

  // Store trigger on open (manual open via showModal)
  const observer = new MutationObserver(() => {
    if (dialog.open && !trigger) trigger = document.activeElement;
  });
  observer.observe(dialog, { attributes: true, attributeFilter: ['open'] });
}

document.querySelectorAll('[data-modal]').forEach(setupModal);

// Helper to open from triggers
document.querySelectorAll('[data-modal-trigger]').forEach(btn => {
  btn.addEventListener('click', () => {
    const target = document.getElementById(btn.getAttribute('data-modal-trigger'));
    if (target) target.showModal();
  });
});
```

---

## 📚 Ressources

- WAI-ARIA Authoring Practices: Dialog (Modal)
- MDN: `<dialog>` element
- Tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`, `/design/tokens/typography.yml`, `/design/tokens/shadows.yml`
