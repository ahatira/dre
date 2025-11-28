# Skip Link (Atom)

**Niveau Atomic Design** : Atom / Accessibility  
**Catégorie** : Navigation  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Lien d’évitement permettant d’accéder directement au contenu principal au clavier. Visible au focus uniquement.

---

## 🎨 Aperçu visuel

```
[Focus] Passer au contenu principal
```

---

## 🏗️ Structure BEM

```html
<a class="ps-skip-link" href="#main-content">Passer au contenu principal</a>
```

### Classes BEM

```
ps-skip-link                     // Block unique
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Skip Link'
status: stable
group: atoms
description: 'Lien d’évitement pour accéder au contenu principal (WCAG).'

props:
  type: object
  properties:
    targetId:
      type: string
      title: ID de la cible
      description: Ancre de destination (ex: 'main-content')
      default: 'main-content'
    label:
      type: string
      title: Libellé
      description: Texte du lien
      default: 'Passer au contenu principal'
    attributes:
      type: Drupal\Core\Template\Attribute
      title: Attributs HTML additionnels
```

---

## 🎭 Variants

- Par défaut un lien vers `#main-content`.
- Possibilité de dupliquer pour d’autres zones (navigation, recherche).

---

## 🎨 Design Tokens

- Couleurs: `--ps-color-white`, `--ps-color-primary-green`, `--ps-color-interactive-focus-outline`
- Typo: `--ps-font-family-primary`, `--ps-font-size-base`, `--ps-font-weight-medium`
- Spacing: `--ps-spacing-3`, `--ps-spacing-4`
- Ombre (optionnel): `--ps-shadow-md`

Si `--ps-color-interactive-focus-outline` n’existe pas, proposer `colors.interactive.focus_outline`.

---

## 🔧 Template Twig

```twig
{#
 * Template for Skip Link atom.
 * Variables:
 * - targetId: string (default 'main-content')
 * - label: string
 * - attributes: Attribute
 #}

{% set href = '#' ~ (targetId|default('main-content')) %}
<a class="ps-skip-link" href="{{ href }}" {{ attributes }}>{{ label|default('Passer au contenu principal') }}</a>
```

---

## 🎨 Styles SCSS

```scss
.ps-skip-link {
  position: absolute;
  left: var(--ps-spacing-4, 16px);
  top: var(--ps-spacing-4, 16px);
  transform: translateY(-150%);
  z-index: 1000;

  background: var(--ps-color-primary-green, #00915A);
  color: var(--ps-color-white, #FFFFFF);
  padding: var(--ps-spacing-3, 12px) var(--ps-spacing-4, 16px);
  border-radius: var(--ps-border-radius-sm, 4px);
  text-decoration: none;
  box-shadow: var(--ps-shadow-md, 0 2px 8px rgba(0,0,0,0.15));

  &:focus {
    transform: translateY(0);
    outline: 2px solid var(--ps-color-interactive-focus-outline, #0B5FFF);
    outline-offset: 2px;
  }
}
```

---

## ♿ Accessibilité

- Obligatoire pour conformité WCAG 2.2 AA.
- Doit être le premier lien focusable de la page.
- Cible (ex: `#main-content`) doit correspondre à un id présent et focusable.

---

## 📱 Comportement responsive

- Position fixe en haut à gauche; s’affiche au focus uniquement.
- Compatible avec zoom 200% et thèmes contrastés.

---

## 🧪 Exemples d'usage

```twig
{# Placer dans le header en tout premier #}
{% include '@ps_theme/ps-skip-link/ps-skip-link.twig' with { targetId: 'main-content' } %}
```

---

## 📚 Ressources

- WCAG 2.2: Bypass Blocks (2.4.1)
- Design tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`

---

Dernière mise à jour : 28 novembre 2025  
Contributeurs : Design System Team
