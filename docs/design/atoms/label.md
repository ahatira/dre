# Label (Atom)

**Niveau Atomic Design** : Atom / Form  
**Catégorie** : Form control  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Libellé de champ de formulaire. Associe clairement une étiquette à un champ via l’attribut `for` et gère l’indication de champ requis.

---

## 🎨 Aperçu visuel

```
Label de champ
* Label requis
Label (désactivé)
```

---

## 🏗️ Structure BEM

```html
<label class="ps-label ps-label--required" for="field-id">
  <span class="ps-label__text">Votre nom</span>
  <span class="ps-label__required" aria-hidden="true">*</span>
  <span class="visually-hidden">(champ obligatoire)</span>
</label>
```

### Classes BEM

```
ps-label                         // Block principal
  ps-label__text                // Texte du label
  ps-label__required            // Indicateur requis

Modificateurs d'état :
  ps-label--required            // Champ requis
  ps-label--disabled            // Champ désactivé
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Label'
status: stable
group: atoms
description: 'Label de champ de formulaire accessible avec indicateur requis.'

props:
  type: object
  properties:
    text:
      type: string
      title: Texte
      description: Intitulé du label
    forId:
      type: string
      title: For
      description: Attribut for liant le label au champ ciblé
    required:
      type: boolean
      title: Requis
      default: false
    disabled:
      type: boolean
      title: Désactivé
      default: false
    attributes:
      type: Drupal\Core\Template\Attribute
      title: Attributs HTML additionnels
  required:
    - text
```

---

## 🎭 Variants

- `required: true` ajoute l’astérisque visuel et le texte caché.
- `disabled: true` applique un style atténué.

---

## 🎨 Design Tokens

- Typo: `--ps-font-family-primary`, `--ps-font-size-sm|base`, `--ps-font-weight-medium|bold`
- Couleur: `--ps-color-text`, `--ps-color-text-muted`, `--ps-color-error`
- Spacing: `--ps-spacing-2` (espacement avec le champ)

Si `--ps-color-text-muted` n’existe pas, proposer `colors.text.muted`.

---

## 🔧 Template Twig

```twig
{#
 * Template for Label atom.
 * Variables:
 * - text: string (requis)
 * - forId: string (id du champ ciblé)
 * - required: bool
 * - disabled: bool
 * - attributes: Attribute
 #}

{% set classes = [
  'ps-label',
  required ? 'ps-label--required',
  disabled ? 'ps-label--disabled'
] %}

<label {{ attributes.addClass(classes) }} {% if forId %}for="{{ forId }}"{% endif %}>
  <span class="ps-label__text">{{ text }}</span>
  {% if required %}
    <span class="ps-label__required" aria-hidden="true">*</span>
    <span class="visually-hidden">(champ obligatoire)</span>
  {% endif %}
</label>
```

---

## 🎨 Styles SCSS

```scss
.ps-label {
  display: inline-flex;
  align-items: baseline;
  gap: var(--ps-spacing-1, 4px);
  margin-bottom: var(--ps-spacing-2, 8px);

  color: var(--ps-color-text, #1F2A33);
  font-family: var(--ps-font-family-primary);
  font-size: var(--ps-font-size-sm, 14px);
  font-weight: var(--ps-font-weight-medium, 500);

  &--disabled { color: var(--ps-color-text-muted, #6B7780); }

  &__required { color: var(--ps-color-error, #EB3636); }
}
```

---

## ♿ Accessibilité

- Attribut `for` requis et doit correspondre à l’id du champ.
- Indication `(champ obligatoire)` annoncée par les lecteurs d’écran.
- Ne pas se reposer uniquement sur la couleur pour indiquer l’état.

---

## 📱 Comportement responsive

- Styles typographiques adaptatifs via tokens.
- Le layout du formulaire gère l’empilement et la largeur.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-label/ps-label.twig' with { text: 'Email', forId: 'edit-email', required: true } %}
{% include '@ps_theme/ps-label/ps-label.twig' with { text: 'Téléphone', forId: 'edit-phone', disabled: true } %}
```

---

## 📚 Ressources

- Design tokens: `/design/tokens/typography.yml`, `/design/tokens/colors.yml`
- Composants liés: Field, Form-field

---

Dernière mise à jour : 28 novembre 2025  
Contributeurs : Design System Team
