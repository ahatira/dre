# Text (Atom)

**Niveau Atomic Design** : Atom / Typography  
**Catégorie** : Text  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Texte courant pour paragraphes, descriptions, légendes et contenus riches. Variants de taille et d’emphase pour correspondre aux styles UI.

---

## 🎨 Aperçu visuel

```
Body — Texte standard de paragraphe.
Small — Texte d’aide ou légende.
Large — Texte mis en avant (lead).
```

---

## 🏗️ Structure BEM

```html
<p class="ps-text ps-text--body ps-text--align-left">
  <span class="ps-text__content">Texte de paragraphe…</span>
</p>
```

### Classes BEM

```
ps-text                           // Block principal
  ps-text__content                // Contenu textuel

Modificateurs de variant :
  ps-text--body                   // Paragraphe par défaut
  ps-text--small                  // Petit texte / aide
  ps-text--large                  // Grand texte / lead

Modificateurs d'état :
  ps-text--muted                  // Couleur atténuée
  ps-text--strong                 // Force (bold)

Modificateurs d'alignement :
  ps-text--align-left|center|right
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Text'
status: stable
group: atoms
description: 'Texte typographique pour paragraphes avec variants, alignements et emphases.'

props:
  type: object
  properties:
    text:
      type: string
      title: Texte
      description: Contenu textuel
    variant:
      type: string
      title: Variant
      enum: ['body','small','large']
      default: 'body'
    tag:
      type: string
      title: Tag HTML
      enum: ['p','span','div']
      default: 'p'
    align:
      type: string
      title: Alignement
      enum: ['left','center','right']
      default: 'left'
    muted:
      type: boolean
      title: Atténué
      default: false
    strong:
      type: boolean
      title: Fort (bold)
      default: false
    attributes:
      type: Drupal\Core\Template\Attribute
      title: Attributs HTML additionnels
  required:
    - text
```

---

## 🎭 Variants

- `body` (défaut), `small` (légende/aide), `large` (lead).
- `muted: true` pour texte secondaire.
- `strong: true` pour emphase.

---

## 🎨 Design Tokens (réels)

- Typo : `--font-body`, tailles `--font-size-1` (body), `--font-size-0` (small), `--font-size-2` (large), graisse `--font-weight-400|700`, line-height `--leading-normal|loose`
- Couleur : `--text-primary`, texte atténué `--text-secondary`
- Spacing (marges contextuelles) : `--size-4` recommandé en marge basse

---

## 🔧 Template Twig

```twig
{#
 * Template for Text atom.
 * Variables:
 * - text: string (requis)
 * - variant: 'body'|'small'|'large'
 * - tag: 'p'|'span'|'div'
 * - align: 'left'|'center'|'right'
 * - muted: bool
 * - strong: bool
 * - attributes: Attribute
 #}

{% set variant = variant|default('body') %}
{% set tag = tag|default('p') %}
{% set align = align|default('left') %}
{% set classes = [
  'ps-text',
  'ps-text--' ~ variant,
  'ps-text--align-' ~ align,
  muted ? 'ps-text--muted',
  strong ? 'ps-text--strong'
] %}

<{{ tag }} {{ attributes.addClass(classes) }}>
  <span class="ps-text__content">{{ text }}</span>
</{{ tag }}>
```

---

## 🎨 Styles SCSS

```scss
.ps-text {
  margin: 0 0 var(--size-4);
  color: var(--text-primary);
  font-family: var(--font-body);
  font-weight: var(--font-weight-400);
  line-height: var(--leading-normal);

  &--body { font-size: var(--font-size-1); }
  &--small { font-size: var(--font-size-0); }
  &--large { font-size: var(--font-size-2); line-height: var(--leading-loose); }

  &--muted { color: var(--text-secondary); }
  &--strong { font-weight: var(--font-weight-700); }

  &--align-left { text-align: left; }
  &--align-center { text-align: center; }
  &--align-right { text-align: right; }
}
```

---

## ♿ Accessibilité

- Contraste de `--text-primary` vs fond ≥ 4.5:1.
- Le tag HTML doit être sémantique selon le contexte (`p` pour paragraphe).

---

## 📱 Comportement responsive

- Échelles de typo responsives via tokens (`--font-size-*`).
- Limiter la longueur de ligne (~65–75 caractères) via styles de layout si nécessaire.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-text/ps-text.twig' with { text: 'Description du bien', variant: 'body' } %}
{% include '@ps_theme/ps-text/ps-text.twig' with { text: 'Note: informations non contractuelles.', variant: 'small', muted: true } %}
{% include '@ps_theme/ps-text/ps-text.twig' with { text: 'Découvrez nos offres', variant: 'large', strong: true, align: 'center' } %}
```

---

## 📚 Ressources

- Design tokens: `/design/tokens/typography.yml`, `/design/tokens/colors.yml`
- Composants liés: Heading, Label, Card

---

Dernière mise à jour : 28 novembre 2025  
Contributeurs : Design System Team
