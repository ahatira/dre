# Heading (Atom)

**Niveau Atomic Design** : Atom / Typography  
**Catégorie** : Text  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Titre typographique sémantique (h1–h6) avec styles cohérents et accessibles. Utilisé pour structurer le contenu et améliorer le SEO et la navigation.

---

## 🎨 Aperçu visuel

```
H1 — Titre principal
H2 — Section
H3 — Sous-section
H4 — Paragraphe titre
H5 — Légende
H6 — Micro-titre
```

---

## 🏗️ Structure BEM

```html
<h2 class="ps-heading ps-heading--h2 ps-heading--align-left">
  <span class="ps-heading__text">Titre de section</span>
</h2>
```

### Classes BEM

```
ps-heading                        // Block principal
  ps-heading__text               // Contenu textuel

Modificateurs de niveau :
  ps-heading--h1..h6            // Styles typographiques associés

Modificateurs d'alignement :
  ps-heading--align-left        // Alignement gauche (défaut)
  ps-heading--align-center      // Alignement centré
  ps-heading--align-right       // Alignement droite

Autres modificateurs :
  ps-heading--visually-hidden   // Titre visible pour lecteurs d'écran uniquement
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Heading'
status: stable
group: atoms
description: 'Titre typographique sémantique h1–h6 avec styles et alignements.'

props:
  type: object
  properties:
    text:
      type: string
      title: Texte
      description: Contenu du titre
    level:
      type: string
      title: Niveau
      enum: ['h1','h2','h3','h4','h5','h6']
      default: 'h2'
    align:
      type: string
      title: Alignement
      enum: ['left','center','right']
      default: 'left'
    visuallyHidden:
      type: boolean
      title: Masqué visuellement
      default: false
    attributes:
      type: Drupal\Core\Template\Attribute
      title: Attributs HTML additionnels
  required:
    - text
```

---

## 🎭 Variants

- `h1` à `h6` avec alignements `left|center|right`.
- `visuallyHidden: true` pour titres uniquement via lecteur d'écran.

---

## 🎨 Design Tokens

Références principales:
- Typo: `--ps-font-family-primary`, `--ps-heading-h1-size..h6`, `--ps-heading-h1-line-height..h6`, `--ps-font-weight-bold`
- Couleur: `--ps-color-text` (défaut)
- Spacing: marges via `--ps-spacing-6` (ex. marge bas)

Si ces variables manquent, proposer:
- `typography.headings.h1..h6` avec taille/line-height/weight
- `typography.align.left|center|right`

---

## 🔧 Template Twig

```twig
{#
 * Template for Heading atom.
 * Variables:
 * - text: string (requis)
 * - level: 'h1'..'h6' (default 'h2')
 * - align: 'left'|'center'|'right'
 * - visuallyHidden: bool
 * - attributes: Attribute
 #}

{% set level = level|default('h2') %}
{% set align = align|default('left') %}
{% set classes = [
  'ps-heading',
  'ps-heading--' ~ level,
  'ps-heading--align-' ~ align,
  visuallyHidden ? 'ps-heading--visually-hidden'
] %}

<{{ level }} {{ attributes.addClass(classes) }}>
  <span class="ps-heading__text">{{ text }}</span>
</{{ level }}>
```

---

## 🎨 Styles SCSS

```scss
.ps-heading {
  margin: 0 0 var(--ps-spacing-6);
  color: var(--ps-color-text, #1F2A33);
  font-family: var(--ps-font-family-primary);
  font-weight: var(--ps-font-weight-bold, 700);

  &--h1 { font-size: var(--ps-heading-h1-size); line-height: var(--ps-heading-h1-line-height); }
  &--h2 { font-size: var(--ps-heading-h2-size); line-height: var(--ps-heading-h2-line-height); }
  &--h3 { font-size: var(--ps-heading-h3-size); line-height: var(--ps-heading-h3-line-height); }
  &--h4 { font-size: var(--ps-heading-h4-size); line-height: var(--ps-heading-h4-line-height); }
  &--h5 { font-size: var(--ps-heading-h5-size); line-height: var(--ps-heading-h5-line-height); }
  &--h6 { font-size: var(--ps-heading-h6-size); line-height: var(--ps-heading-h6-line-height); }

  &--align-left { text-align: left; }
  &--align-center { text-align: center; }
  &--align-right { text-align: right; }

  &--visually-hidden { position: absolute !important; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
}
```

---

## ♿ Accessibilité

- Utiliser le niveau sémantique correct `h1..h6` pour la hiérarchie.
- `visuallyHidden` permet d'ajouter un titre pour la navigation sans impact visuel.
- Focus visible géré par le navigateur si interactif (rarement pertinent ici).

---

## 📱 Comportement responsive

- Les tailles de titre sont responsives via tokens (`--ps-heading-*-size`).
- Les alignements s’adaptent naturellement; ajouter utilitaires si nécessaires.

---

## 🧪 Exemples d'usage

### Drupal Twig

```twig
{% include '@ps_theme/ps-heading/ps-heading.twig' with { text: 'Nos biens', level: 'h2' } %}
{% include '@ps_theme/ps-heading/ps-heading.twig' with { text: 'Trouver un bien', level: 'h1', align: 'center' } %}
{% include '@ps_theme/ps-heading/ps-heading.twig' with { text: 'Navigation', level: 'h2', visuallyHidden: true } %}
```

---

## 📚 Ressources

- Design tokens: `/design/tokens/typography.yml`
- Composants liés: Text, Breadcrumb, Card
- WCAG: Headings and Labels

---

Dernière mise à jour : 28 novembre 2025  
Contributeurs : Design System Team
