# Card Component - Extensible Blocks Architecture

**Date**: 2025-12-10  
**Version**: 2.0.0  
**Status**: ✅ Implemented & Validated

---

## 🎯 Objectif

Concevoir une architecture de composant Card **générique et extensible** capable de créer tous les types de cartes requis pour BNP Paribas Real Estate via **composition Twig blocks** plutôt que par variants spécialisés.

---

## 📊 Analyse: 8 Types de Cards Identifiés

Basé sur les maquettes fournies et les besoins métier BNP Real Estate :

| # | Type | Contenu Clé | Layout | Particularités |
|---|------|-------------|--------|----------------|
| 1 | **Publication Card** | Location+Date \| Image \| Title \| Desc \| Link | Vertical | Simple, générique |
| 2 | **News Card** | Tag+Date \| Image \| Title \| Desc \| Link | Vertical | Badge coloré en header |
| 3 | **Product/Property Simple** | Image \| Price+Surface \| Title \| Location \| Link \| Favorite | Vertical | Favorite icon overlay |
| 4 | **Offer Card Vertical** | Carousel \| Badges+Actions \| Title \| Location \| Surface \| Price \| Link | Vertical | Complex overlay (badges, actions) |
| 5 | **Offer Card Horizontal** | Identique à #4 | Horizontal | Layout adaptatif (60/40 split) |
| 6 | **CTA Card** | Title \| Description \| Button | Vertical | Pas d'image, fond coloré |
| 7 | **Solution/Service Card** | Icon \| Title \| Description \| Link | Vertical | Icon large en header |
| 8 | **Study/Trendbook Card** | Tag+Date \| Illustration \| Title \| Desc \| Button | Vertical/Horizontal | Image illustrée premium |

---

## 🏗️ Architecture Proposée et Implémentée

### **6 Twig Blocks Extensibles**

```twig
{% embed '@components/card/card.twig' with {
  variant: 'elevated',
  layout: 'vertical',
  radius: 'md'
} only %}

  {% block media %}
    {# Zone image/media (optional) #}
    {% include '@elements/image/image.twig' with {...} %}
  {% endblock %}
  
  {% block media_overlay %}
    {# Content overlaid sur l'image (badges, actions, navigation) #}
    <div class="badges">...</div>
    <div class="actions">...</div>
  {% endblock %}
  
  {% block header %}
    {# Metadata en haut (tags, dates, location) #}
    {% include '@elements/badge/badge.twig' with {...} %}
    <h3>{{ title }}</h3>
  {% endblock %}
  
  {% block content %}
    {# Wrapper principal (par défaut contient body) #}
  {% endblock %}
  
  {% block body %}
    {# Contenu textuel principal #}
    <p>{{ description }}</p>
    <div class="specs">...</div>
  {% endblock %}
  
  {% block footer %}
    {# Actions/CTAs en bas #}
    {% include '@elements/button/button.twig' with {...} %}
  {% endblock %}

{% endembed %}
```

### **Structure HTML Générée**

```html
<article class="ps-card ps-card--elevated ps-card--radius-md">
  <div class="ps-card__media">
    <!-- block media content -->
    <!-- block media_overlay content -->
  </div>
  <div class="ps-card__content">
    <!-- block header content -->
    <div class="ps-card__body">
      <!-- block body content -->
    </div>
    <!-- block footer content -->
  </div>
</article>
```

---

## 🎨 CSS Architecture - 3 Layers

### **Layer 1: Global Tokens** (from `source/props/`)
```css
--white, --gray-200, --shadow-2, --size-4, --radius-4, --duration-fast
```

### **Layer 2: Component Variables** (`.ps-card`)
```css
--ps-card-bg: var(--white);
--ps-card-padding-y: var(--size-8);
--ps-card-border-radius: 0;
--ps-card-hover-shadow: var(--shadow-3);
```

### **Layer 3: Modifiers** (`.ps-card--*`)
```css
.ps-card--small { --ps-card-padding-y: var(--size-4); }
.ps-card--radius-md { --ps-card-border-radius: var(--radius-4); }
.ps-card--elevated { box-shadow: var(--shadow-2); }
```

---

## 📐 Props Configurables

| Prop | Type | Default | Values |
|------|------|---------|--------|
| `variant` | string | `default` | default \| outlined \| flat \| elevated |
| `layout` | string | `vertical` | vertical \| horizontal |
| `size` | string | `medium` | small \| medium \| large |
| `radius` | string | `none` | none \| sm \| md \| lg |
| `imagePosition` | string | `top` | top \| bottom \| left \| right |
| `url` | string | — | Rend la card cliquable (`<a>` au lieu de `<article>`) |
| `image` | object | — | Simple image object (src, alt, ratio) pour Storybook |
| `header` | string/html | — | HTML header content (pour Storybook) |
| `body` | string/html | — | HTML body content (pour Storybook) |
| `footer` | string/html | — | HTML footer content (pour Storybook) |
| `attributes` | object | — | Additional HTML attributes |

---

## 🔧 Exemples d'Implémentation

### Exemple 1: Offer Card Vertical (Complex)

```twig
{% embed '@components/card/card.twig' with {
  url: '/property/456',
  layout: 'vertical'
} only %}
  
  {% block media %}
    {# Carousel d'images #}
    <img src="/madrid-office.jpg" alt="Rent Offices Madrid" />
  {% endblock %}
  
  {% block media_overlay %}
    <div class="badges-actions">
      <div class="badges">
        <span class="badge badge--neutral">
          <span data-icon="eye"></span> Already viewed
        </span>
        <span class="badge badge--gold">
          <span data-icon="star"></span> Exclusivity
        </span>
      </div>
      <div class="actions">
        <button data-icon="bookmark" aria-label="Save"></button>
        <button data-icon="heart" aria-label="Favorite"></button>
      </div>
    </div>
  {% endblock %}
  
  {% block body %}
    <h3>Rent Offices MADRID Barrio de Chamberí</h3>
    <div class="meta">
      <span data-icon="pin-map"></span> 28010 MADRID
    </div>
    <div class="surface">611.3 m²</div>
  {% endblock %}
  
  {% block footer %}
    <div class="price-cta">
      <strong class="price">20 000 € HT/HC/m²/an</strong>
      <span class="ps-link">View the property <span data-icon="arrow-right"></span></span>
    </div>
  {% endblock %}
  
{% endembed %}
```

### Exemple 2: CTA Card (No Image)

```twig
{% embed '@components/card/card.twig' with {
  variant: 'flat',
  size: 'large'
} only %}
  
  {% block body %}
    <h3>Calculate the target surface area of your future offices !</h3>
    <p>Quick and easy to use, the surface area calculator lets you define the m2 surface area you need in just a few clicks.</p>
  {% endblock %}
  
  {% block footer %}
    {% include '@elements/button/button.twig' with {
      label: 'Start calculator',
      variant: 'outlined'
    } only %}
  {% endblock %}
  
{% endembed %}
```

### Exemple 3: News Card

```twig
{% embed '@components/card/card.twig' with {
  url: '/actualites/marche-tertiaire',
  layout: 'vertical'
} only %}
  
  {% block header %}
    {% include '@elements/badge/badge.twig' with {
      label: 'TAG LABEL',
      color: 'primary'
    } only %}
    <span class="date">15 Dec 2025</span>
  {% endblock %}
  
  {% block media %}
    <img src="/team-meeting.jpg" alt="Team collaboration" />
  {% endblock %}
  
  {% block body %}
    <h3>News title</h3>
    <p>Lorem ipsum dolor sit amet consectetur...</p>
  {% endblock %}
  
  {% block footer %}
    <span class="ps-link">Lire la suite <span data-icon="arrow-right"></span></span>
  {% endblock %}
  
{% endembed %}
```

---

## ✅ Validation et Tests

### Build & Compilation
- ✅ `npm run build` : Succès (aucune erreur CSS/Twig)
- ✅ Vite bundle: 453.55 kB gzipped
- ✅ PostCSS: Pas d'erreurs de syntaxe

### Fonctionnalités Testées
- ✅ **Visual Variants**: default, outlined, flat, elevated
- ✅ **Layouts**: vertical, horizontal (+ responsive mobile stacking)
- ✅ **Image Positions**: top, bottom, left, right
- ✅ **Sizes**: small, medium, large
- ✅ **Radius**: none, sm, md, lg
- ✅ **Clickable**: URL prop rend `<a>` avec hover effects
- ✅ **Blocks**: media, media_overlay, header, body, footer tous fonctionnels

### Backward Compatibility
- ✅ Props `header`, `body`, `footer` (HTML strings) pour Storybook
- ✅ Simple `image` object prop pour cas basiques
- ✅ Toutes les stories existantes fonctionnent

---

## 📚 Documentation

### Fichiers Implémentés
- `source/patterns/components/card/card.twig` - Template avec blocks
- `source/patterns/components/card/card.css` - 3-layer CSS architecture
- `source/patterns/components/card/card.yml` - Drupal SDC schema
- `source/patterns/components/card/card.stories.jsx` - 7 stories Storybook
- `source/patterns/components/card/README.md` - Documentation complète (8 types exemples)

### Documentation Externe
- `docs/design/molecules/card.md` - Spécification design
- `docs/ps-design/CHANGELOG.md` - Entrée détaillée refactor
- `docs/design/molecules/cards/*.jpg` - 9 maquettes référence

---

## 🎓 Principes Appliqués

1. **Composition over Configuration**: Blocks Twig > variants spécialisés
2. **Generic Container**: Pas de structure de contenu imposée
3. **Extensibility First**: 6 blocks couvrent tous les cas d'usage
4. **Backward Compatible**: Props support pour Storybook
5. **3-Layer CSS**: Global tokens → Component vars → Modifiers
6. **Semantic HTML**: `<article>` par défaut, `<a>` si clickable
7. **Accessibility**: WCAG 2.2 AA, keyboard nav, focus-visible
8. **Responsive**: Auto-stacking horizontal layouts mobile

---

## 🚀 Prochaines Étapes

### Cards Spécialisées à Créer (via embed)
1. **OfferCard** (Déjà existante - à migrer vers nouvelle architecture)
2. **NewsCard** (Blog/actualités)
3. **AgentCard** (Fiches agents immobiliers)
4. **PropertyCard** (Listings propriétés basiques)
5. **StudyCard** (Publications/trendbooks)

### Organismes à Implémenter
- **CardGrid** (`docs/design/organisms/card-grid.md`) - Grille responsive de cards avec pagination

---

## 📝 Notes de Maintenance

- **Ajouter un nouveau type de card**: Utiliser `{% embed %}` avec les blocks existants, PAS de nouveau variant
- **Modifier le style**: Éditer les variables Layer 2 dans `card.css`, PAS les styles de base
- **Nouvelle position d'image**: Déjà couvert (top/bottom/left/right)
- **Nouveau block**: Ajouter dans `card.twig` + documenter dans README

---

**Maintainers**: Design System Team  
**Last Review**: 2025-12-10  
**Status**: ✅ Production Ready
