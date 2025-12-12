# Card (Molecule)

**Type** : Molecule / Generic Container  
**Rôle** : Conteneur flexible fournissant structure visuelle et variants de layout  
**Statut** : ✅ Stable  
**Version** : 1.1.0  
**Dernière mise à jour** : 3 décembre 2025

---

## 📋 Description

Card est un **conteneur générique** qui fournit structure visuelle (bordure, padding, shadow) et options de layout. Le contenu est composé librement via Twig blocks, permettant maximum de réutilisabilité. 

**Implémentation** : `source/patterns/components/card/`

### Architecture Philosophy

Card est **PAS** un composant spécialisé. C'est un **conteneur générique** qui :
- ✅ Définit structure visuelle (border, radius, shadow, padding)
- ✅ Fournit variants de layout (vertical, horizontal)
- ✅ Offre options de taille (small, medium, large)
- ❌ N'impose PAS de structure de contenu (pas de title, price, badges prédéfinis)
- ❌ N'inclut PAS de logique métier (pas de favorites, status, etc.)

**Utiliser composition** pour créer cartes spécialisées (OfferCard, NewsCard, etc.) qui embedent Card.

---

## 🎭 Variants

### Visual Variants
- **default** : Style standard avec bordure
- **outlined** : Bordure accentuée
- **flat** : Sans bordure
- **elevated** : Avec ombre portée

### Layout Variants
- **vertical** (défaut) : Image en haut, contenu en bas
- **horizontal** : Image à gauche/droite, contenu à côté

### Size Variants
- **small** : Padding réduit (16px)
- **medium** (défaut) : Padding standard (30px/24px - Figma exact)
- **large** : Padding large (32px)

### Radius Variants
- **none** (défaut) : Pas de border-radius (0)
- **sm** : Petit radius (var(--radius-2))
- **md** : Moyen radius (var(--radius-4))
- **lg** : Grand radius (var(--radius-6))

### Image Position (selon layout)
- **Vertical** : top (défaut) | bottom
- **Horizontal** : left (défaut) | right

---

## 🏗️ Structure BEM

```html
<article class="ps-card ps-card--outlined ps-card--horizontal">
  <div class="ps-card__image">
    <!-- Image/media content -->
  </div>
  <div class="ps-card__content">
    <div class="ps-card__header">
      <!-- Header content -->
    </div>
    <div class="ps-card__body">
      <!-- Main content -->
    </div>
    <div class="ps-card__footer">
      <!-- Footer content -->
    </div>
  </div>
</article>
```

### Classes BEM

```
ps-card (base container)
├── ps-card__image (optional image/media zone)
└── ps-card__content (main content wrapper)
    ├── ps-card__header (optional header zone)
    ├── ps-card__body (optional body zone)
    └── ps-card__footer (optional footer zone)

Modifiers:
├── ps-card--outlined
├── ps-card--flat
├── ps-card--elevated
├── ps-card--horizontal
├── ps-card--small
├── ps-card--large
├── ps-card--radius-none (default, no class)
├── ps-card--radius-sm
├── ps-card--radius-md
├── ps-card--radius-lg
├── ps-card--image-right (horizontal only)
└── ps-card--image-bottom (vertical only)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Card'
status: stable
group: components
description: 'Conteneur générique flexible avec structure visuelle et variants de layout.'

props:
  type: object
  properties:
    variant:
      type: string
      enum: ['default','outlined','flat','elevated']
      default: 'default'
      title: Variant visuel
    layout:
      type: string
      enum: ['vertical','horizontal']
      default: 'vertical'
      title: Orientation layout
    size:
      type: string
      enum: ['small','medium','large']
      default: 'medium'
      title: Taille padding
    radius:
      type: string
      enum: ['none','sm','md','lg']
      default: 'none'
      title: Border radius
    imagePosition:
      type: string
      enum: ['top','bottom','left','right']
      default: 'top'
      title: Position image (dépend du layout)
      description: 'top/bottom pour vertical, left/right pour horizontal'
    url:
      type: string
      title: URL optionnelle (rend la card cliquable)
    attributes:
      type: Drupal\Core\Template\Attribute
```

---

## 🔧 Twig Blocks

Card utilise **Twig blocks** pour composition de contenu :

| Block | Description |
|-------|-------------|
| `image` | Zone image/media (optionnelle) |
| `content` | Contenu principal (block par défaut si pas de header/body/footer) |
| `header` | Section header (optionnelle) |
| `body` | Section body (optionnelle) |
| `footer` | Section footer (optionnelle) |

### Exemple d'utilisation avec blocks

```twig
{% embed '@components/card/card.twig' with {
  layout: 'horizontal',
  radius: 'md',
  size: 'medium'
} only %}
  
  {% block image %}
    {% include '@elements/image/image.twig' with {
      src: '/images/property.jpg',
      alt: 'Property photo',
      ratio: '16x9'
    } only %}
  {% endblock %}
  
  {% block content %}
    <h3>Property Title</h3>
    <p>Description here...</p>
  {% endblock %}
  
  {% block footer %}
    <a href="#">View details →</a>
  {% endblock %}
  
{% endembed %}
```

## 🎨 Design Tokens

### Visual
- **Border** : `1.5px solid #EBEDEF` (Figma exact - Grey #6) via `var(--border-size-15)` + `var(--ps-color-border-card)`
- **Border radius** : Customizable via `radius` prop
  - None: `0` (default)
  - Small: `var(--radius-2)` (4px)
  - Medium: `var(--radius-4)` (8px)
  - Large: `var(--radius-6)` (12px)
- **Background** : `var(--white)`
- **Shadow** (hover/elevated) : `var(--shadow-4)`

### Spacing
- **Small padding** : `var(--size-4)` (16px)
- **Medium padding** : `30px 24px` (Figma exact) via `var(--ps-card-padding-y-medium)` + `var(--ps-card-padding-x-medium)`
- **Large padding** : `var(--size-8)` (32px)
- **Content gap** : `var(--size-4)` (16px)
- **Image media horizontal** : 40% width, 60% content (Figma exact) via `var(--ps-card-media-width-horizontal)`

### Typography
- Délégué aux composants enfants (heading, text, link, etc.)

---

## 🧩 Composition Atomique

**Card = Molecule (Generic Container)** qui peut composer :

### Composants souvent utilisés
- `@elements/image/image.twig` - Images
- `@elements/heading/heading.twig` - Titres
- `@elements/text/text.twig` - Textes/descriptions
- `@elements/link/link.twig` - Liens/CTAs
- `@elements/badge/badge.twig` - Badges/labels

### Cartes spécialisées qui composent Card
- `@components/offer-card/offer-card.twig` - Carte immobilière BNP Real Estate
- `@components/news-card/` (à implémenter) - Carte actualités
- `@components/publication-card/` (à implémenter) - Carte publications

**Note** : Pour créer une carte spécialisée, utiliser `{% embed '@components/card/card.twig' %}` avec blocks Twig pour injecter du contenu structuré.

---

## ♿ Accessibilité

- **Semantic HTML** : `<article>` par défaut (ou `<a>` si url fourni)
- **Image alt** : Délégué au block image (obligatoire)
- **Heading hierarchy** : Délégué aux composants enfants
- **Keyboard navigation** : Si url fourni, card devient cliquable (focus outline)
- **ARIA** : `role="article"` implicite, pas d'overrides nécessaires
- **Color contrast** : WCAG AA respecté par défaut (bordure #EBEDEF suffisant)

---

## 🧪 Exemples d'usage

### Card basique avec contenu libre
```twig
{% embed '@components/card/card.twig' with { radius: 'md' } only %}
  {% block content %}
    <h3>Simple Card</h3>
    <p>Free-form content here.</p>
  {% endblock %}
{% endembed %}
```

### Card horizontale avec image
```twig
{% embed '@components/card/card.twig' with {
  layout: 'horizontal',
  radius: 'md',
  size: 'medium'
} only %}
  {% block image %}
    {% include '@elements/image/image.twig' with {
      src: '/path/to/image.jpg',
      alt: 'Description',
      ratio: '1x1'
    } only %}
  {% endblock %}
  
  {% block content %}
    <h3>Property Title</h3>
    <p>Location and details...</p>
  {% endblock %}
{% endembed %}
```

### Card spécialisée (OfferCard compose Card)
```twig
{# OfferCard utilise Card en interne via embed #}
{% include '@components/offer-card/offer-card.twig' with {
  title: 'Office Space Madrid',
  surface: '611 m²',
  price: '20,000 €',
  image: { url: '/madrid.jpg', alt: 'Office' },
  meta: [{ icon: 'pin-map', text: 'Madrid' }]
} only %}
```

---

## 📚 Ressources

- **Implémentation** : `source/patterns/components/card/`
- **Exemples spécialisés** : `source/patterns/components/offer-card/`
- **Storybook** : Stories avec variants, layouts, radius
- **Tokens** : `source/props/*.css`
- **Atomic Design** : Molecule container composable

---

**Version** : 1.1.0  
**Dernière mise à jour** : 3 décembre 2025  
**Status** : ✅ Production-ready (conteneur générique)  
**Note** : Pour cartes métier (real estate, news, etc.), créer composants spécialisés qui composent Card.
      </div>
    {% endif %}
  </div>
</article>
```

## Tokens
- Spacing: `spacing.semantic.card.*`
- Borders: `borders.preset.card.*`

## A11y
- `article` avec titre `h3`
- Alternative text sur images

## Exemples
```twig
{% include '@ps_theme/ps-card/ps-card.twig' with {
  image: { url: '/images/property.jpg', alt: 'Appartement Paris' },
  eyebrow: 'Appartement',
  title: 'Appartement 3 pièces Paris 15e',
  description: 'Proche métro, 65m², lumineux',
  meta: [ { icon: 'pin-map', text: 'Paris 15e' }, { icon: 'surface', text: '65 m²' } ],
  cta: { text: 'Voir le bien', url: '/property/123', variant: 'primary' },
  variant: 'product'
} %}
```
