# Eyebrow (Atom)

**Niveau Atomic Design** : Atom / Text  
**Catégorie** : Label / Kicker  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Texte court placé au-dessus d'un titre principal pour fournir un contexte ou une catégorie (ex: "Actualités", "Nouveauté", "Étude de cas"). Généralement en petite taille, majuscules, et couleur secondaire. Peut inclure une icône ou un séparateur visuel (trait, point).

---

## 🎨 Aperçu visuel

```
NOUVEAUTÉ
──────────
Grand titre principal
```

---

## 🏗️ Structure BEM

```html
<span class="ps-eyebrow ps-eyebrow--primary ps-eyebrow--uppercase">
  <svg class="ps-eyebrow__icon" aria-hidden="true"><use href="#icon-star"></use></svg>
  <span class="ps-eyebrow__text">Nouveauté</span>
</span>

<span class="ps-eyebrow ps-eyebrow--neutral ps-eyebrow--with-line">
  <span class="ps-eyebrow__text">Étude de cas</span>
</span>
```

### Classes BEM

```
ps-eyebrow                                // Block
  ps-eyebrow__icon                        // Icône optionnelle
  ps-eyebrow__text                        // Texte
  ps-eyebrow__line                        // Ligne décorative (si variant)

Modificateurs :
  ps-eyebrow--primary                     // Couleur primaire (vert)
  ps-eyebrow--secondary                   // Couleur secondaire (gris)
  ps-eyebrow--accent                      // Couleur accent (bleu)
  ps-eyebrow--neutral                     // Couleur neutre (gris clair)
  
  ps-eyebrow--uppercase                   // Texte en majuscules
  ps-eyebrow--bold                        // Gras
  ps-eyebrow--with-line                   // Avec ligne horizontale
  ps-eyebrow--with-dot                    // Avec point décoratif
  
  ps-eyebrow--small                       // Petite taille
  ps-eyebrow--medium                      // Taille moyenne (défaut)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Eyebrow'
status: stable
group: atoms
description: 'Texte court contextuel placé au-dessus d'un titre (kicker, category label).'

props:
  type: object
  properties:
    text:
      type: string
      title: Texte
    variant:
      type: string
      enum: ['primary','secondary','accent','neutral']
      default: 'neutral'
    size:
      type: string
      enum: ['small','medium']
      default: 'medium'
    uppercase:
      type: boolean
      default: true
    bold:
      type: boolean
      default: false
    withLine:
      type: boolean
      default: false
      description: 'Ajouter une ligne horizontale décorative'
    withDot:
      type: boolean
      default: false
      description: 'Ajouter un point décoratif'
    icon:
      type: string
      description: 'Nom d'icône optionnel'
    attributes:
      type: Drupal\Core\Template\Attribute
  required:
    - text
```

---

## 🎭 Variants

- **Couleurs** : `primary`|`secondary`|`accent`|`neutral`.
- **Tailles** : `small`|`medium`.
- **Styles** : `uppercase` (majuscules), `bold` (gras).
- **Décorations** : `withLine` (ligne horizontale), `withDot` (point).
- **Icône** : optionnelle (ex: étoile, tag).

---

## 🎨 Design Tokens

- Typo: `--ps-font-family-primary`, `--ps-font-size-xs|sm`, `--ps-font-weight-semibold`, `--ps-letter-spacing-wide`
- Couleurs par variante:
  - Primary: `--ps-color-primary-600`
  - Secondary: `--ps-color-neutral-600`
  - Accent: `--ps-color-info-600`
  - Neutral: `--ps-color-neutral-500`
- Ligne décorative: `--ps-color-neutral-300` (ligne), `--ps-spacing-2` (gap)
- Espacements: `--ps-spacing-1|2` (gap icône/texte)

Proposition si manquant: `--ps-letter-spacing-wide` (0.05em pour majuscules).

---

## 🔧 Template Twig

```twig
{#
 * Template for Eyebrow atom.
 * Variables: voir API YAML
 #}

{% set variant = variant|default('neutral') %}
{% set size = size|default('medium') %}
{% set uppercase = uppercase ?? true %}
{% set bold = bold|default(false) %}
{% set withLine = withLine|default(false) %}
{% set withDot = withDot|default(false) %}

{% set root_classes = [
  'ps-eyebrow',
  'ps-eyebrow--' ~ variant,
  'ps-eyebrow--' ~ size,
  uppercase ? 'ps-eyebrow--uppercase',
  bold ? 'ps-eyebrow--bold',
  withLine ? 'ps-eyebrow--with-line',
  withDot ? 'ps-eyebrow--with-dot'
] %}

<span {{ attributes.addClass(root_classes) }}>
  {% if icon %}
    <svg class="ps-eyebrow__icon" aria-hidden="true"><use href="#icon-{{ icon }}"></use></svg>
  {% endif %}
  <span class="ps-eyebrow__text">{{ text }}</span>
  {% if withLine %}
    <span class="ps-eyebrow__line" aria-hidden="true"></span>
  {% endif %}
  {% if withDot %}
    <span class="ps-eyebrow__dot" aria-hidden="true">•</span>
  {% endif %}
</span>
```

---

## 🎨 Styles SCSS

```scss
.ps-eyebrow {
  display: inline-flex; align-items: center; gap: var(--ps-spacing-2, 8px);
  font-family: var(--ps-font-family-primary);
  font-size: var(--ps-font-size-sm, 14px);
  font-weight: var(--ps-font-weight-medium, 500);
  letter-spacing: var(--ps-letter-spacing-wide, 0.05em);
  line-height: 1.2;

  &__icon {
    width: 14px; height: 14px; flex-shrink: 0;
  }

  &__line {
    width: 40px; height: 2px;
    background: currentColor;
    opacity: 0.3;
  }

  &__dot {
    font-size: 1.2em;
    opacity: 0.5;
  }

  // Sizes
  &--small {
    font-size: var(--ps-font-size-xs, 12px);
    .ps-eyebrow__icon { width: 12px; height: 12px; }
    .ps-eyebrow__line { width: 30px; height: 1px; }
  }
  &--medium {
    font-size: var(--ps-font-size-sm, 14px);
  }

  // Styles
  &--uppercase {
    text-transform: uppercase;
  }
  &--bold {
    font-weight: var(--ps-font-weight-semibold, 600);
  }

  // Variants
  &--primary {
    color: var(--ps-color-primary-600, #0DB089);
  }
  &--secondary {
    color: var(--ps-color-neutral-600, #54636F);
  }
  &--accent {
    color: var(--ps-color-info-600, #039BE5);
  }
  &--neutral {
    color: var(--ps-color-neutral-500, #6E7C89);
  }

  // With line variant (full layout)
  &--with-line {
    .ps-eyebrow__text { order: -1; }
    .ps-eyebrow__line { flex: 1; margin-left: var(--ps-spacing-2, 8px); }
  }
}
```

---

## ♿ Accessibilité

- Sémantique : utiliser `<span>` ou `<div>` (pas de heading).
- Ligne/dot décoratifs : `aria-hidden="true"`.
- Contraste texte suffisant (WCAG AA).
- Placé avant le titre principal dans le DOM pour ordre de lecture correct.

---

## 📱 Comportement responsive

- Inline-flex : s'adapte au conteneur.
- Peut passer en `display: block` sur mobiles si besoin de ligne complète.

---

## 🧪 Exemples d'usage

```twig
{# Simple eyebrow #}
{% include '@ps_theme/ps-eyebrow/ps-eyebrow.twig' with {
  text: 'Nouveauté',
  variant: 'primary',
  uppercase: true
} %}

{# With icon #}
{% include '@ps_theme/ps-eyebrow/ps-eyebrow.twig' with {
  text: 'Étude de cas',
  variant: 'accent',
  icon: 'document',
  bold: true
} %}

{# With line #}
{% include '@ps_theme/ps-eyebrow/ps-eyebrow.twig' with {
  text: 'Actualités',
  variant: 'neutral',
  withLine: true,
  size: 'small'
} %}

{# With dot #}
{% include '@ps_theme/ps-eyebrow/ps-eyebrow.twig' with {
  text: 'Blog',
  variant: 'secondary',
  withDot: true
} %}
```

---

## 📚 Ressources

- Figma: Detected in hero and card sections
- Tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`, `/design/tokens/typography.yml`
