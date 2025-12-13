# Offer Card (Molecule)

**Niveau Atomic Design** : Molecule / Specialized Card  
**Catégorie** : Real Estate / Business  
**Statut** : ✅ Stable  
**Version** : 1.0.0

---

## 📋 Description

Composant spécialisé pour les annonces immobilières qui étend le conteneur Card générique avec du contenu et un style spécifiques aux offres. Affiche propriétés, bureaux, et locaux commerciaux avec surface, prix, métadonnées, badges de statut et actions.

**Implémentation** : `source/patterns/components/offer-card/`

---

## 🎨 Aperçu visuel

```
┌─────────────────────────────────┐
│  [Image propriété]              │
│  👁️ Vu  ⭐ Exclusivité           │
│                         🔖 ❤️    │
├─────────────────────────────────┤
│  Rent Offices MADRID            │
│  📍 Madrid                      │
│  611.3 m²                       │
│                                 │
│  20 000 € HT/HC/m²/an           │
│  [View property →]              │
└─────────────────────────────────┘
```

---

## 🏗️ Structure BEM

```html
<div class="ps-card ps-card--offer">
  <div class="ps-card__image">
    <img src="..." alt="..." />
  </div>
  
  <div class="ps-card__content">
    <div class="ps-offer-card__header">
      <div class="ps-offer-card__badges">
        <span class="ps-offer-card__badge ps-offer-card__badge--viewed">
          <span class="ps-offer-card__badge-icon" data-icon="eye"></span>
          Vu
        </span>
        <span class="ps-offer-card__badge ps-offer-card__badge--gold">
          <span class="ps-offer-card__badge-icon" data-icon="star"></span>
          Exclusivité
        </span>
      </div>
      <div class="ps-offer-card__actions">
        <button class="ps-offer-card__action" data-icon="bookmark"></button>
        <button class="ps-offer-card__action" data-icon="heart"></button>
      </div>
    </div>
    
    <div class="ps-offer-card__body">
      <h3 class="ps-offer-card__title">Rent Offices MADRID</h3>
      <div class="ps-offer-card__meta">
        <span class="ps-offer-card__meta-item">
          <span class="ps-offer-card__meta-icon" data-icon="pin-map"></span>
          <span class="ps-offer-card__meta-text">Madrid</span>
        </span>
      </div>
      <div class="ps-offer-card__surface">611.3 m²</div>
    </div>
    
    <div class="ps-offer-card__footer">
      <div class="ps-offer-card__price">20 000 € HT/HC/m²/an</div>
      <a href="#" class="ps-link">View property →</a>
    </div>
  </div>
</div>
```

### Classes BEM

```
ps-card (base from Card component)
  ps-card__image
  ps-card__content
  ps-card--offer                       // Modifier pour style offer

ps-offer-card__header                  // En-tête (badges + actions)
  ps-offer-card__badges                // Container badges
    ps-offer-card__badge               // Badge individuel
      ps-offer-card__badge--viewed     // Badge "Vu" (gris)
      ps-offer-card__badge--gold       // Badge "Exclusivité" (or)
      ps-offer-card__badge-icon        // Icône badge (data-icon)
  ps-offer-card__actions               // Actions (save, favorite)
    ps-offer-card__action              // Bouton action

ps-offer-card__body                    // Corps (titre, meta, surface)
  ps-offer-card__title                 // Titre propriété
  ps-offer-card__meta                  // Métadonnées (location, etc.)
    ps-offer-card__meta-item           // Item meta
      ps-offer-card__meta-icon         // Icône meta (data-icon)
      ps-offer-card__meta-text         // Texte meta
  ps-offer-card__surface               // Surface (m²)

ps-offer-card__footer                  // Footer (prix + CTA)
  ps-offer-card__price                 // Prix
  (+ link component)                   // CTA link
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Offer Card'
status: stable
group: components
description: 'Carte spécialisée pour annonces immobilières avec badges, prix et métadonnées.'

props:
  type: object
  required:
    - title
  properties:
    layout:
      type: string
      enum: ['vertical','horizontal']
      default: 'vertical'
      title: Orientation layout
    title:
      type: string
      title: Titre de la propriété
    surface:
      type: string
      title: Surface (ex "611.3 m²")
    price:
      type: string
      title: Prix (ex "20 000 € HT/HC/m²/an")
    image:
      type: object
      title: Image propriété
      properties:
        url:
          type: string
        alt:
          type: string
    meta:
      type: array
      title: Métadonnées (location, etc.)
      items:
        type: object
        properties:
          icon:
            type: string
            description: 'Nom icône (sans préfixe icon-)'
          text:
            type: string
    status:
      type: object
      title: Badges de statut
      properties:
        viewed:
          type: boolean
          default: false
        exclusivity:
          type: boolean
          default: false
    cta:
      type: object
      title: Call-to-action
      properties:
        text:
          type: string
        url:
          type: string
    url:
      type: string
      title: URL de la carte (clickable)
    attributes:
      type: Drupal\Core\Template\Attribute
```

---

## 🎭 Variants

### Layout
- **vertical** (défaut) : Image en haut, contenu en bas (mobile-friendly)
- **horizontal** : Image à gauche (40%), contenu à droite (60%)

### Badges de statut
- **viewed** : Badge "Vu" (gris) avec icône eye
- **exclusivity** : Badge "Exclusivité" (or) avec icône star

### Actions
- **bookmark** : Sauvegarder la propriété (icône bookmark)
- **heart** : Ajouter aux favoris (icône heart)

---

## 🎨 Design Tokens

**Note** : Composant actuellement en cours d'alignement tokens. Certaines valeurs suivent encore exactement Figma et seront migrées vers tokens PS dans une prochaine itération.

### Typographie
- **Title** : `--font-size-1` (16px), `--font-weight-400`
- **Surface** : `--font-size-1` (16px), `--font-weight-700`
- **Price** : `--font-size-3` (20px), `--font-weight-700`
- **Meta** : `--font-size-0` (14px), `--font-weight-400`
- **Badges** : `--font-size-0` (14px), `--font-weight-400`

### Spacing
- **Content padding** : `30px 24px` (hérité de Card medium size)
- **Badges gap** : `--size-2` (8px)
- **Actions gap** : `12px` (à tokeniser)
- **Footer gap** : `9px` (à tokeniser)

### Icônes
- **Badge icon** : 12px
- **Meta icon** : 16px (--size-4)
- **Action icon** : 24px (--size-6)

### Couleurs
- **Badge viewed** : Gris neutre (--gray-400)
- **Badge gold** : Or/accent premium (token `--gold`)
- **Price** : Texte primaire (--gray-900)
- **Actions** : Interactif (--primary hover)

---

## 🔧 Template Twig

Voir implémentation complète : `source/patterns/components/offer-card/offer-card.twig`

Le composant compose le Card générique via `embed` Twig :

```twig
{% embed '@components/card/card.twig' with {
  layout: layout|default('vertical'),
  radius: 'md',
  classes: ['ps-card--offer']|merge(classes|default([]))
} only %}
  
  {% block image %}
    {% if image %}
      {% include '@elements/image/image.twig' with {
        src: image.url,
        alt: image.alt,
        ratio: '16x9'
      } only %}
    {% endif %}
  {% endblock %}
  
  {% block content %}
    {# Header: badges + actions #}
    <div class="ps-offer-card__header">...</div>
    
    {# Body: title + meta + surface #}
    <div class="ps-offer-card__body">...</div>
    
    {# Footer: price + CTA #}
    <div class="ps-offer-card__footer">...</div>
  {% endblock %}
  
{% endembed %}
```

---

## ♿ Accessibilité

### Conformité WCAG 2.2 AA

- **Semantic HTML** : `<article>` (via Card) pour item autonome
- **Heading hierarchy** : `<h3>` pour titre (ajustable via prop)
- **Image alt** : Alt text obligatoire sur image propriété
- **ARIA labels** : Boutons actions avec `aria-label` descriptifs
  - "Save property" pour bookmark
  - "Add to favorites" pour heart
- **Badge ARIA** : `role="status"` pour badges "Vu"/"Exclusivité" si dynamiques
- **Keyboard navigation** : Actions clavier-accessible (boutons natifs)
- **Focus visible** : Outline focus sur actions et CTA
- **Color contrast** : Tous les textes WCAG AA (4.5:1 minimum)
- **Touch targets** : Boutons actions ≥ 44×44px (WCAG 2.5.5)

---

## 📱 Comportement responsive

### Mobile (< 768px)
- Layout vertical forcé
- Image pleine largeur (16:9 aspect ratio)
- Actions empilées si nécessaire
- Font sizes préservées (lisibilité)

### Tablet (768px - 1024px)
- Horizontal layout optionnel
- Image 40% / contenu 60%
- Badges inline

### Desktop (> 1024px)
- Horizontal layout recommandé pour grilles
- Hover states sur actions (scale, color change)
- CTA transition fluide

---

## 🎯 Composition Atomique

**Offer Card = Molecule** qui compose :

### Composants utilisés
- `@components/card/card.twig` (container générique)
- `@elements/image/image.twig` (image propriété)
- `@elements/link/link.twig` (CTA link)
- Icons via système central `data-icon` (badges, meta, actions)

### Dépendances
- Card component (base container)
- Image component (photo propriété)
- Link component (CTA)
- Icons system (`source/props/icons.css`)

---

## 🧪 Exemples d'usage

### Offre basique
```twig
{% include '@components/offer-card/offer-card.twig' with {
  title: 'Rent Offices MADRID',
  surface: '611.3 m²',
  price: '20 000 € HT/HC/m²/an',
  image: {
    url: '/images/property-madrid.jpg',
    alt: 'Modern office building in Madrid'
  },
  meta: [
    { icon: 'pin-map', text: 'Madrid, Spain' }
  ],
  cta: {
    text: 'View property',
    url: '/properties/madrid-office-123'
  }
} only %}
```

### Offre avec badges et actions
```twig
{% include '@components/offer-card/offer-card.twig' with {
  title: 'Sale Commercial Space PARIS',
  surface: '1,245 m²',
  price: '3,500,000 €',
  image: {
    url: '/images/property-paris.jpg',
    alt: 'Commercial space in Paris'
  },
  meta: [
    { icon: 'pin-map', text: 'Paris 8ème' },
    { icon: 'calendar', text: 'Available Q2 2026' }
  ],
  status: {
    viewed: true,
    exclusivity: true
  },
  cta: {
    text: 'Contact agent',
    url: '/contact?property=paris-123'
  }
} only %}
```

### Offre en layout horizontal (grille desktop)
```twig
{% include '@components/offer-card/offer-card.twig' with {
  layout: 'horizontal',
  title: 'Rent Warehouse LYON',
  surface: '2,800 m²',
  price: '8,500 € / month',
  image: {
    url: '/images/warehouse-lyon.jpg',
    alt: 'Warehouse in Lyon'
  },
  meta: [
    { icon: 'pin-map', text: 'Lyon Industrial Zone' },
    { icon: 'warehouse', text: 'Logistics' }
  ],
  url: '/properties/lyon-warehouse-456'
} only %}
```

### Grille d'offres (use case réel)
```twig
<div class="property-grid">
  {% for property in properties %}
    {% include '@components/offer-card/offer-card.twig' with {
      title: property.title,
      surface: property.surface,
      price: property.price,
      image: property.featured_image,
      meta: property.metadata,
      status: {
        viewed: property.user_has_viewed,
        exclusivity: property.is_exclusive
      },
      cta: {
        text: 'View details',
        url: property.url
      },
      url: property.url
    } only %}
  {% endfor %}
</div>
```

---

## 📋 Checklist conformité

- [x] 5 fichiers obligatoires (`.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md`)
- [x] BEM strict avec préfixe `ps-`
- [ ] Tokens uniquement (**⚠️ En cours** : badges, actions gaps à tokeniser)
- [x] HTML minimal (pas de classes pour defaults)
- [x] Modifiers indépendants
- [x] CSS nesting moderne
- [x] Description README ≤ 2 lignes
- [x] Storybook stories avec showcases
- [x] Accessibilité WCAG 2.2 AA
- [x] Composition atomique (Card + Image + Link)
- [x] Icons via système central `data-icon`

---

## 🚧 Travaux futurs

### Migration tokens complète
Aligner toutes les valeurs hardcodées avec tokens PS :
- Badge spacing/sizing
- Actions icon sizes et gaps
- Footer spacing

### Variantes supplémentaires
- **Featured variant** : Highlight pour propriétés premium (border accent, shadow)
- **Compact variant** : Version réduite pour listes denses
- **Card sizes** : Support `small`, `medium`, `large` explicit

### États interactifs enrichis
- **Saved state** : Toggle bookmark avec état persistant
- **Favorited state** : Toggle heart avec état persistant
- **Loading state** : Skeleton pendant chargement données

---

## 📚 Ressources

- **Implémentation** : `source/patterns/components/offer-card/`
- **Base Card** : `source/patterns/components/card/`
- **Storybook** : Stories avec layouts, badges, actions
- **Figma** : Maquette pixel-perfect BNP Paribas Real Estate
- **Tokens** : `source/props/*.css`

---

**Version** : 1.0.0  
**Dernière mise à jour** : 3 décembre 2025  
**Status** : ✅ Stable (migration tokens en cours)  
**Business Context** : BNP Paribas Real Estate - Property listings
