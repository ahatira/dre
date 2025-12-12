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

- Couleurs: `--primary`, `--primary-hover`, `--white`, `--border-focus`
- Typo: `--font-sans`, `--font-size-1`, `--font-weight-500`, `--leading-normal`
- Spacing: `--size-3`, `--size-4`
- Ombre (optionnel): `--shadow-3`
- Z-index: `--layer-important`

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
  --skip-link-top: var(--size-4);
  --skip-link-left: var(--size-4);
  --skip-link-z-index: var(--layer-important);
  --skip-link-padding-y: var(--size-3);
  --skip-link-padding-x: var(--size-4);
  --skip-link-bg: var(--primary);
  --skip-link-hover-bg: var(--primary-hover);
  --skip-link-color: var(--white);
  --skip-link-border-radius: var(--radius-2);
  --skip-link-shadow: var(--shadow-3);
  --skip-link-font-family: var(--font-sans);
  --skip-link-font-size: var(--font-size-1);
  --skip-link-font-weight: var(--font-weight-500);
  --skip-link-line-height: var(--leading-normal);
  --skip-link-transition-duration: var(--duration-fast);
  --skip-link-transition-timing: var(--ease-3);
  --skip-link-focus-outline-width: var(--border-size-2);
  --skip-link-focus-outline-color: var(--border-focus);
  --skip-link-focus-outline-offset: var(--border-size-2);
  --skip-link-hidden-offset-y: -150%;

  position: absolute;
  top: var(--skip-link-top);
  left: var(--skip-link-left);
  z-index: var(--skip-link-z-index);
  transform: translateY(var(--skip-link-hidden-offset-y));

  padding: var(--skip-link-padding-y) var(--skip-link-padding-x);
  background-color: var(--skip-link-bg);
  color: var(--skip-link-color);
  border-radius: var(--skip-link-border-radius);
  box-shadow: var(--skip-link-shadow);

  font-family: var(--skip-link-font-family);
  font-size: var(--skip-link-font-size);
  font-weight: var(--skip-link-font-weight);
  line-height: var(--skip-link-line-height);
  text-decoration: none;
  white-space: nowrap;

  transition: transform var(--skip-link-transition-duration) var(--skip-link-transition-timing);

  &:focus-visible {
    transform: translateY(0);
    outline: var(--skip-link-focus-outline-width) solid var(--skip-link-focus-outline-color);
    outline-offset: var(--skip-link-focus-outline-offset);
  }

  &:hover { background-color: var(--skip-link-hover-bg); }
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
