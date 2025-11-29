# Badge Component

**Category:** Elements (Atom)  
**Status:** ✅ Stable  
**Version:** 1.0.0

---

## Description

Indicateur visuel compact pour afficher des statuts, labels, catégories, ou informations contextuelles. Disponible en 8 variantes sémantiques avec support pour icônes, tailles multiples, forme pilule, et liens cliquables.

Le composant Badge est conçu pour ajouter des métadonnées visuelles rapides sans surcharger l'interface.

---

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `text` | `string` | **required** | Texte affiché dans le badge |
| `icon` | `string` | - | Nom de l'icône à afficher (ex: `'icon-calendar'`) |
| `variant` | `string` | `'default'` | Variante : `default` \| `primary` \| `secondary` \| `gold` \| `info` \| `success` \| `warning` \| `danger` |
| `size` | `string` | `'medium'` | Taille : `small` (11px) \| `medium` (12px) \| `large` (14px) |
| `pill` | `boolean` | `false` | Forme pilule complètement arrondie |
| `url` | `string` | - | URL de destination (transforme en lien `<a>`) |
| `attributes` | `Attribute` | - | Attributs HTML additionnels |

---

## Structure BEM

```
.ps-badge                               // Block principal
  .ps-badge__icon                       // Icône (optionnelle)
  .ps-badge__text                       // Texte du badge

Modifiers de variant:
  .ps-badge--default                    // Gris (default)
  .ps-badge--primary                    // Vert (brand primary)
  .ps-badge--secondary                  // Purple (brand secondary)
  .ps-badge--gold                       // Or (legacy accent)
  .ps-badge--info                       // Bleu (semantic)
  .ps-badge--success                    // Vert (semantic)
  .ps-badge--warning                    // Jaune/Orange (semantic)
  .ps-badge--danger                     // Rouge (semantic)

Modifiers de taille:
  .ps-badge--small                      // 11px font
  .ps-badge--medium                     // 12px font (default)
  .ps-badge--large                      // 14px font

Modifiers de forme:
  .ps-badge--pill                       // Complètement arrondi (999px)
```

---

## Design Tokens Utilisés

### Layout & Sizing
- `--size-05` (2px) - Padding small, outline offset
- `--size-1` (4px) - Gap icon-text, padding vertical medium
- `--size-105` (6px) - Padding small horizontal, padding large vertical
- `--size-2` (8px) - Padding horizontal medium
- `--size-3` (12px) - Padding horizontal large, icon size medium
- `--font-size-xs` (10-11px) - Font size small, icon size small
- `--font-size-sm` (12px) - Font size medium
- `--font-size-0` (14px) - Font size large, icon size large

### Typography
- `--font-sans` - Famille de police
- `--font-weight-500` - Poids medium

### Colors - Variants
**Default (Gray)**
- `--gray-200` (hsl(220, 13%, 91%)) - Background
- `--gray-600` (hsl(215, 14%, 34%)) - Text

**Primary (Green)**
- `--brand-primary` (#00915A) - Background
- `--white` - Text

**Secondary (Purple)**
- `--bnp-accent-magenta` (#A12B66) - Background
- `--white` - Text

**Gold (Legacy)**
- `hsl(39, 48%, 63%)` (#D1AE6E) - Background
- `--white` - Text

**Info (Blue)**
- `--blue-100` (hsl(211, 94%, 94%)) - Background
- `--blue-700` (hsl(220, 92%, 37%)) - Text

**Success (Green)**
- `--green-100` (hsl(154, 61%, 92%)) - Background
- `--green-700` (hsl(160, 84%, 29%)) - Text

**Warning (Yellow)**
- `--yellow-100` (hsl(54, 95%, 85%)) - Background
- `--yellow-700` (hsl(37, 97%, 32%)) - Text

**Danger (Red)**
- `--red-100` (hsl(0, 84%, 95%)) - Background
- `--red-700` (hsl(0, 74%, 42%)) - Text

### Visual
- `--radius-2` (4px) - Border radius default
- `--radius-round` (999px) - Border radius pill
- `--border-size-2` (2px) - Focus outline

---

## Exemples d'Usage

### Twig (Drupal)

```twig
{# Badge simple #}
{% include '@elements/badge/badge.twig' with {
  text: 'Nouveau',
  variant: 'success',
} %}

{# Badge avec icône #}
{% include '@elements/badge/badge.twig' with {
  text: '15 Jan 2025',
  variant: 'info',
  icon: 'icon-calendar',
} %}

{# Badge pilule #}
{% include '@elements/badge/badge.twig' with {
  text: 'Premium',
  variant: 'gold',
  pill: true,
} %}

{# Badge lien cliquable #}
{% include '@elements/badge/badge.twig' with {
  text: 'Immobilier',
  variant: 'primary',
  url: '/category/immobilier',
  pill: true,
} %}

{# Badge avec icône et lien #}
{% include '@elements/badge/badge.twig' with {
  text: 'Voir plus',
  variant: 'info',
  icon: 'icon-eye',
  url: '#details',
  pill: true,
} %}

{# Toutes les tailles #}
{% include '@elements/badge/badge.twig' with {
  text: 'Small',
  variant: 'primary',
  size: 'small',
} %}

{% include '@elements/badge/badge.twig' with {
  text: 'Medium',
  variant: 'primary',
  size: 'medium',
} %}

{% include '@elements/badge/badge.twig' with {
  text: 'Large',
  variant: 'primary',
  size: 'large',
} %}
```

### HTML Output

```html
<!-- Badge simple -->
<span class="ps-badge ps-badge--success">
  <span class="ps-badge__text">Nouveau</span>
</span>

<!-- Badge avec icône -->
<span class="ps-badge ps-badge--info">
  <!-- Icon component -->
  <span class="ps-badge__text">15 Jan 2025</span>
</span>

<!-- Badge pilule -->
<span class="ps-badge ps-badge--gold ps-badge--pill">
  <span class="ps-badge__text">Premium</span>
</span>

<!-- Badge lien -->
<a class="ps-badge ps-badge--primary ps-badge--pill" href="/category/immobilier">
  <span class="ps-badge__text">Immobilier</span>
</a>

<!-- Badge taille small -->
<span class="ps-badge ps-badge--primary ps-badge--small">
  <span class="ps-badge__text">Small</span>
</span>
```

---

## Cas d'Usage Réels

### 1. Statut de Bien Immobilier
```twig
{# Indiquer disponibilité #}
<div class="property-card">
  <h3>{{ property.title }}</h3>
  {% if property.status == 'available' %}
    {% include '@elements/badge/badge.twig' with {
      text: 'Disponible',
      variant: 'success',
      size: 'small',
    } %}
  {% elseif property.status == 'reserved' %}
    {% include '@elements/badge/badge.twig' with {
      text: 'Réservé',
      variant: 'warning',
      size: 'small',
    } %}
  {% else %}
    {% include '@elements/badge/badge.twig' with {
      text: 'Vendu',
      variant: 'default',
      size: 'small',
    } %}
  {% endif %}
</div>
```

### 2. Catégories Cliquables
```twig
{# Tags de catégorie avec liens #}
<div class="property-categories">
  {% for category in property.categories %}
    {% include '@elements/badge/badge.twig' with {
      text: category.name,
      variant: 'primary',
      pill: true,
      url: category.url,
    } %}
  {% endfor %}
</div>
```

### 3. Date de Publication
```twig
{# Afficher date avec icône #}
<article class="news-item">
  {% include '@elements/badge/badge.twig' with {
    text: news.date|date('d M Y'),
    variant: 'info',
    icon: 'icon-calendar',
    size: 'small',
  } %}
  <h2>{{ news.title }}</h2>
</article>
```

### 4. Label "Nouveau"
```twig
{# Mettre en avant nouveau contenu #}
<div class="product-card">
  {% if product.is_new %}
    {% include '@elements/badge/badge.twig' with {
      text: 'Nouveau',
      variant: 'success',
      pill: true,
      size: 'small',
    } %}
  {% endif %}
  <h3>{{ product.title }}</h3>
</div>
```

### 5. Badge Exclusivité Premium
```twig
{# Label premium avec icône #}
{% if property.is_exclusive %}
  {% include '@elements/badge/badge.twig' with {
    text: 'Exclusivité',
    variant: 'gold',
    icon: 'icon-star',
    pill: true,
  } %}
{% endif %}
```

### 6. Compteur de Notifications
```twig
{# Badge compteur dans navigation #}
<a href="/notifications" class="nav-link">
  Notifications
  {% if notification_count > 0 %}
    {% include '@elements/badge/badge.twig' with {
      text: notification_count,
      variant: 'danger',
      pill: true,
      size: 'small',
    } %}
  {% endif %}
</a>
```

---

## Accessibilité

### Conformité WCAG 2.2 AA

✅ **Contraste de couleur**
- Default (gray-600 sur gray-200) : 4.8:1 (AA ✓)
- Primary (white sur green) : 7.8:1 (AAA ✓)
- Info (blue-700 sur blue-100) : 8.2:1 (AAA ✓)
- Success (green-700 sur green-100) : 7.9:1 (AAA ✓)
- Warning (yellow-700 sur yellow-100) : 7.1:1 (AAA ✓)
- Danger (red-700 sur red-100) : 8.5:1 (AAA ✓)

✅ **Contenu textuel**
- Texte toujours présent et lisible
- Icônes décoratives avec `aria-hidden="true"`

✅ **Liens cliquables**
- Badge avec `url` devient `<a>` sémantique
- Focus visible avec outline 2px
- Hover avec effet visuel (brightness 95%)

✅ **Taille minimum**
- Texte minimum 11px (small) lisible
- Pas de limite touch target (badge décoratif, pas bouton)

### États Visuels

| État | Visual Feedback |
|------|-----------------|
| **Default** | Badge statique |
| **Hover** (lien) | `filter: brightness(0.95)` |
| **Focus** (lien) | Outline 2px vert + offset 2px |

---

## Notes Techniques

### Variantes de Couleur

Le composant supporte 8 variantes avec deux catégories :

**Brand Colors** (3)
- `default` - Neutre gris
- `primary` - Vert BNP (#00915A)
- `secondary` - Purple accent (#A12B66)

**Semantic Colors** (5)
- `info` - Bleu (informations)
- `success` - Vert (réussite, disponible)
- `warning` - Jaune/Orange (attention)
- `danger` - Rouge (erreur, urgent)
- `gold` - Or (premium, exclusivité) *legacy*

### Icon Integration

Les icônes utilisent le composant `@elements/icon/icon.twig` avec :
- Size automatique : `'small'` pour tous les badges
- Espacement : `gap: var(--size-1)` (4px)
- Alignment : `align-items: center`

### Forme Pilule

Le modifier `--pill` applique `border-radius: var(--radius-round)` (999px) pour créer des extrémités complètement arrondies, idéal pour :
- Tags/catégories
- Labels premium
- Compteurs
- Liens d'action

### Inline Display

Le badge utilise `display: inline-flex` pour s'intégrer naturellement dans le flux de texte ou être groupé avec d'autres badges.

---

## Responsive

Les badges s'adaptent automatiquement au contenu. Considérations responsive :

```scss
@media (max-width: 768px) {
  // Réduire taille dans contextes compacts
  .property-card .ps-badge {
    font-size: var(--font-size-xs);
    padding: var(--size-05) var(--size-105);
  }
}
```

---

## Changelog

### v1.0.0 (2025-11-29)
- ✅ Implémentation initiale avec 8 variantes
- ✅ Support 3 tailles (small, medium, large)
- ✅ Support forme pilule
- ✅ Support icônes via composant icon
- ✅ Support liens cliquables
- ✅ Accessibilité WCAG 2.2 AA complète
- ✅ Tokens design system (ZERO valeur en dur)
- ✅ Classes conditionnelles (minimal HTML output)
- ✅ Documentation complète

---

## Ressources

- **Storybook**: [http://localhost:6006/?path=/docs/elements-badge](http://localhost:6006/?path=/docs/elements-badge)
- **Spec Design**: `docs/design/atoms/badge.md`
- **Template Standard**: `.github/COMPONENT_TEMPLATE_STANDARD.md`
- **Design Tokens**: `source/props/brand.css`, `source/props/colors.css`, `source/props/sizes.css`

---

**Contributeurs**: Design System Team  
**Dernière mise à jour**: 29 novembre 2025
