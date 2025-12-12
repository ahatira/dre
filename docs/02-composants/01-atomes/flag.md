# Flag (Atom)

**Niveau Atomic Design** : Atom / Media  
**Catégorie** : Locale indicator  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Indicateur visuel de pays/langue sous forme de drapeau. Prévu pour accompagner des libellés (sélecteur de langue, listes de localisations). Ne pas utiliser le drapeau seul pour représenter une langue sans libellé; fournir une alternative textuelle.

Prise en charge duale (Option C):
- Accepte un `code` ISO 3166-1 alpha-2 (FR, GB, DE...) ou un tag BCP 47 via `locale` (fr-FR, en-GB...).
- Le composant mappe `locale` → code pays pour la résolution d’assets (ex: `en-GB` → `gb.svg`).

---

## 🎨 Aperçu visuel

```
[ FR 🇫🇷 ]  [ UK 🇬🇧 ]  [ DE 🇩🇪 ]  [ ES 🇪🇸 ]  [ IT 🇮🇹 ]  [ NL 🇳🇱 ]
```

---

## 🏗️ Structure BEM

```html
<span class="ps-flag ps-flag--md ps-flag--circle" title="France">
  <img class="ps-flag__img" src="/assets/flags/fr.svg" alt="France" width="24" height="24" />
</span>
```

### Classes BEM

```
ps-flag                           // Block wrapper
  ps-flag__img                    // Image SVG/PNG du drapeau

Modificateurs de taille :
  ps-flag--sm|md|lg               // 16|20|24px (par défaut md)

Modificateurs de forme :
  ps-flag--square|rounded|circle  // Carré, coins, cercle

Modificateurs d’état :
  ps-flag--disabled               // Atténué
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Flag'
status: stable
group: atoms
description: 'Indicateur de pays/langue avec tailles et formes.'

props:
  type: object
  properties:
    code:
      type: string
      title: Code pays
      description: Code ISO 3166-1 alpha-2 (ex: FR, GB, DE, ES, IT, NL, IE, PL)
    locale:
      type: string
      title: Tag BCP 47
      description: Tag de langue (ex: fr-FR, en-GB). Si présent, sert à dériver le code pays pour l’asset.
    label:
      type: string
      title: Libellé accessible
      description: Libellé lisible (ex: 'France', 'United Kingdom') pour alt/title
    src:
      type: string
      title: Source SVG/PNG
      description: Chemin explicite du drapeau; par défaut '/assets/flags/{code}.svg'
    size:
      type: string
      title: Taille
      enum: ['sm','md','lg']
      default: 'md'
    shape:
      type: string
      title: Forme
      enum: ['square','rounded','circle']
      default: 'square'
    disabled:
      type: boolean
      title: Désactivé
      default: false
    decorative:
      type: boolean
      title: Décoratif uniquement
      description: Si vrai, cache des lecteurs d'écran (alt vide, aria-hidden)
      default: false
    attributes:
      type: Drupal\Core\Template\Attribute
      title: Attributs HTML additionnels
  required: []
```

---

## 🎭 Variants

- Codes recommandés (extrait): `FR`, `GB`, `DE`, `ES`, `IT`, `NL`, `IE`, `PL`. Utiliser ISO 3166-1 alpha-2 majuscule.
- Tailles: `sm` (16px), `md` (20px), `lg` (24px).
- Formes: `square`, `rounded`, `circle`.

---

## 🎨 Design Tokens (réels)

- Tailles : sm = `--size-4` (16px), md = `--size-5` (20px), lg = `--size-6` (24px)
- Radius : `--radius-1` (square/rounded léger), `--radius-2` (rounded), `--radius-round` (circle)
- Couleurs : héritées de l’image
- Disabled : opacité à définir dans le composant (peut utiliser `0.5` ou `--text-disabled` comme teinte sur le wrapper)

---

## 🔧 Template Twig

```twig
{#
 * Template for Flag atom.
 * Variables:
 * - code: string (ISO alpha-2, ex: 'FR')
 * - locale: string (BCP 47, ex: 'fr-FR' ou 'en_GB')
 * - label: string (ex: 'France')
 * - src: string (optional explicit path)
 * - size: 'sm'|'md'|'lg'
 * - shape: 'square'|'rounded'|'circle'
 * - disabled: bool
 * - decorative: bool
 * - attributes: Attribute
 #}

{% set size = size|default('md') %}
{% set shape = shape|default('square') %}
{% set classes = [
  'ps-flag',
  'ps-flag--' ~ size,
  'ps-flag--' ~ shape,
  disabled ? 'ps-flag--disabled'
] %}

{# Normalisation Option C: déduire le code pays depuis locale si code absent #}
{% set locale_norm = locale is defined and locale ? locale|replace({'_':'-'}) : null %}
{% set parts = locale_norm ? locale_norm|split('-') : [] %}
{% set region = parts|length > 1 and parts[1]|length == 2 ? parts[1]|upper : null %}
{% set country_code = code is defined and code ? code|upper : (region ?? null) %}
{% set asset_code = country_code ? country_code|lower : 'xx' %}

{% set img_src = src ?? ('/assets/flags/' ~ asset_code ~ '.svg') %}
{% set alt_text = decorative ? '' : (label ?? country_code ?? 'Flag') %}

<span {{ attributes.addClass(classes) }} title="{{ decorative ? '' : (label ?? code) }}">
  <img class="ps-flag__img" src="{{ img_src }}" alt="{{ alt_text }}" width="{{ size == 'sm' ? 16 : (size == 'lg' ? 24 : 20) }}" height="{{ size == 'sm' ? 16 : (size == 'lg' ? 24 : 20) }}" {% if decorative %}aria-hidden="true"{% endif %} />
</span>
```

---

## 🎨 Styles SCSS

```scss
.ps-flag {
  /* Variables composant (Layer 2) */
  --flag-size: var(--size-5); /* 20px par défaut */
  --flag-aspect-ratio: calc(4 / 3);
  --flag-border-radius: 0;
  --flag-disabled-opacity: 0.5;
  --flag-disabled-grayscale: 0.2;
  --flag-hover-opacity: 0.9;
  --flag-transition-duration: var(--duration-fast);
  --flag-transition-timing: var(--ease-4);

  display: inline-flex;
  align-items: center;
  justify-content: center;
  block-size: var(--flag-size);
  inline-size: calc(var(--flag-size) * var(--flag-aspect-ratio));
  aspect-ratio: var(--flag-aspect-ratio);
  overflow: hidden;
  transition:
    opacity var(--flag-transition-duration) var(--flag-transition-timing),
    filter var(--flag-transition-duration) var(--flag-transition-timing);

  &__img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: var(--flag-border-radius);
  }

  // Tailles
  &--xs { --flag-size: var(--size-3); }
  &--sm { --flag-size: var(--size-4); }
  &--md { --flag-size: var(--size-5); }
  &--lg { --flag-size: var(--size-6); }
  &--xl { --flag-size: var(--size-12); }

  // Formes
  &--rounded { --flag-border-radius: var(--radius-2); }
  &--circle { --flag-aspect-ratio: 1; --flag-border-radius: var(--radius-round); }

  // États
  &:hover:not(&--disabled) { opacity: var(--flag-hover-opacity); }
  &:focus-visible {
    outline: var(--border-size-2) solid var(--border-focus);
    outline-offset: var(--size-1);
  }
  &--disabled { opacity: var(--flag-disabled-opacity); filter: grayscale(var(--flag-disabled-grayscale)); cursor: not-allowed; pointer-events: none; }
}
```

---

## ♿ Accessibilité

- Ne pas utiliser un drapeau seul pour indiquer une langue; toujours fournir un libellé (`label`).
- Si décoratif, utiliser `decorative: true` pour rendre `alt=""` et `aria-hidden`.
- Taille minimale 16px; contraste non applicable (image), mais bouton/lien autour doit respecter les critères.

---

## 📱 Comportement responsive

- Tailles fixes par token; s’intègrent dans le flux.
- Support HiDPI via fichiers SVG.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-flag/ps-flag.twig' with { code: 'FR', label: 'France', size: 'md', shape: 'circle' } %}
{% include '@ps_theme/ps-flag/ps-flag.twig' with { code: 'GB', label: 'United Kingdom', size: 'sm' } %}
{% include '@ps_theme/ps-flag/ps-flag.twig' with { code: 'DE', label: 'Deutschland', decorative: false } %}
```

---

## 📚 Ressources

- ISO 3166-1 alpha-2 country codes
- Design tokens: `/design/tokens/borders.yml`, `/design/tokens/spacing.yml`
