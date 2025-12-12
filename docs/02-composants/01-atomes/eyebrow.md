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
  <span class="ps-eyebrow__icon" data-icon="star" aria-hidden="true"></span>
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

## 🎨 Design Tokens (réels)

- Typo : `--font-body`, `--font-size-0|1`, `--font-weight-600`, `--tracking-wide` (ou `--tracking-wider`), `--leading-tight`
- Couleurs par variante :
  - Primary : `--primary`
  - Secondary : `--text-secondary`
  - Accent : `--info`
  - Neutral : `--gray-500` ou `--text-secondary`
- Ligne/points décoratifs : couleur `currentColor`, épaisseur `--border-size-1`, espacement `--size-2`
- Espacements internes : `--size-1|2` pour le gap icône/texte/ornements

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
    <span class="ps-eyebrow__icon" data-icon="{{ icon }}" aria-hidden="true"></span>
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
  /* Layer 2: variables composant */
  --eyebrow-gap: var(--size-2);
  --eyebrow-display: inline-flex;
  --eyebrow-font-family: var(--font-condensed);
  --eyebrow-font-size: var(--font-size-1);
  --eyebrow-font-weight: var(--font-weight-500);
  --eyebrow-line-height: 1.2;
  --eyebrow-letter-spacing: var(--tracking-wide);
  --eyebrow-color: var(--text-secondary);
  --eyebrow-icon-size: var(--size-3);
  --eyebrow-line-width: var(--size-10);
  --eyebrow-line-height: var(--size-05);
  --eyebrow-line-opacity: 0.3;
  --eyebrow-dot-size: 1.2em;
  --eyebrow-dot-opacity: 0.5;

  display: var(--eyebrow-display);
  align-items: center;
  gap: var(--eyebrow-gap);
  font-family: var(--eyebrow-font-family);
  font-size: var(--eyebrow-font-size);
  font-weight: var(--eyebrow-font-weight);
  letter-spacing: var(--eyebrow-letter-spacing);
  line-height: var(--eyebrow-line-height);
  color: var(--eyebrow-color);

  &__icon { width: var(--eyebrow-icon-size); height: var(--eyebrow-icon-size); flex-shrink: 0; }
  &__text { font: inherit; color: inherit; }
  &__line { width: var(--eyebrow-line-width); height: var(--eyebrow-line-height); background: currentColor; opacity: var(--eyebrow-line-opacity); }
  &__dot { font-size: var(--eyebrow-dot-size); opacity: var(--eyebrow-dot-opacity); }

  // Tailles
  &--small {
    --eyebrow-font-size: var(--font-size-0);
    --eyebrow-icon-size: var(--size-2);
    --eyebrow-line-width: var(--size-8);
    --eyebrow-line-height: var(--border-size-1);
  }

  // Styles texte
  &--uppercase { text-transform: uppercase; letter-spacing: var(--tracking-wider); }
  &--bold { --eyebrow-font-weight: var(--font-weight-600); }

  // Variantes sémantiques
  &--primary { --eyebrow-color: var(--primary); }
  &--secondary { --eyebrow-color: var(--secondary); }
  &--info { --eyebrow-color: var(--info); }
  &--muted { --eyebrow-color: var(--text-disabled); }

  // Décorations
  &--with-line { & .ps-eyebrow__line { flex: 1; margin-left: var(--eyebrow-gap); } }
  &--with-dot { & .ps-eyebrow__dot { color: currentColor; } }
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
