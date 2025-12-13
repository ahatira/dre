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

## 🎨 Design Tokens (réels)

- Typo : `--font-heading` (ou `--font-body`), `--font-weight-700`
- Échelle suggérée (rem) à partir des tokens existants :
  - h1 : `--font-size-8` (2.25rem) + `--leading-tight`
  - h2 : `--font-size-7` (2rem) + `--leading-tight`
  - h3 : `--font-size-6` (1.75rem) + `--leading-tight`
  - h4 : `--font-size-5` (1.5rem) + `--leading-normal`
  - h5 : `--font-size-4` (1.375rem) + `--leading-normal`
  - h6 : `--font-size-3` (1.25rem) + `--leading-normal`
- Couleur : `--text-primary` (défaut)
- Spacing (marge basse) : `--size-4` ou `--size-6` selon contexte
- Alignements : `text-align` via classes, pas de token dédié

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
  margin: 0 0 var(--size-4);
  color: var(--text-primary);
  font-family: var(--font-heading);
  font-weight: var(--font-weight-700);

  &--h1 { font-size: var(--font-size-8); line-height: var(--leading-tight); }
  &--h2 { font-size: var(--font-size-7); line-height: var(--leading-tight); }
  &--h3 { font-size: var(--font-size-6); line-height: var(--leading-tight); }
  &--h4 { font-size: var(--font-size-5); line-height: var(--leading-normal); }
  &--h5 { font-size: var(--font-size-4); line-height: var(--leading-normal); }
  &--h6 { font-size: var(--font-size-3); line-height: var(--leading-normal); }

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

- Les tailles de titre sont responsives via l'échelle `--font-size-*` (voir mapping h1..h6 ci-dessus).
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
