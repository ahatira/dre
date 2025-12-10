# Card

**Generic flexible container with extensible Twig blocks** providing visual structure (border, padding, shadow) and layout variants. Designed for maximum composition flexibility through `{% embed %}` pattern.

## Architecture Philosophy

Card is **NOT a specialized component**. It's a **generic container** that:

✅ Provides visual structure (border, radius, shadow, padding)  
✅ Offers layout variants (vertical, horizontal)  
✅ Exposes Twig blocks for content composition  
❌ Does NOT impose content structure (no hardcoded titles, prices, badges)  
❌ Does NOT include business logic (no favorites, status handling)

**Use composition** to create specialized cards (OfferCard, NewsCard, etc.) via `{% embed %}`.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| **variant** | string | `'default'` | Visual appearance (default, outlined, flat, elevated) |
| **layout** | string | `'vertical'` | Layout orientation (vertical, horizontal) |
| **size** | string | `'medium'` | Content padding size (small, medium, large) |
| **radius** | string | `'none'` | Border radius amount (none, sm, md, lg) |
| **imagePosition** | string | `'top'` | Image position (top/bottom for vertical, left/right for horizontal) |
| **url** | string | — | Optional URL (renders card as clickable `<a>` element) |
| **image** | object | — | Simple image object for Storybook (src, alt, ratio) |
| **header** | string/html | — | Header content HTML (for Storybook - use block in Drupal) |
| **body** | string/html | — | Body content HTML (for Storybook - use block in Drupal) |
| **footer** | string/html | — | Footer content HTML (for Storybook - use block in Drupal) |
| **content** | string/html | — | Fallback content (alternative to body) |
| **attributes** | object | — | Additional HTML attributes |

## Twig Blocks (Drupal Composition)

Card exposes **6 extensible blocks** for advanced composition:

| Block | Description | Usage |
|-------|-------------|-------|
| **media** | Image/media zone (optional) | Override to include custom image components |
| **media_overlay** | Content overlaid on media | For badges, actions, navigation over image |
| **header** | Top metadata section | Tags, dates, location, status badges |
| **content** | Main content wrapper | Default block containing body |
| **body** | Main text content | Titles, descriptions, metadata |
| **footer** | Bottom actions/CTAs | Buttons, links, pricing |

## CSS Variables (3-Layer System)

Card uses a **3-layer CSS variable architecture** for flexible theming and context-specific customization:

### Layer 1: Global Tokens (from `source/props/`)

Foundation design tokens used throughout the project:

```css
/* Colors */
--white, --gray-200, --gray-300, --shadow-2, --shadow-3, --shadow-4

/* Sizing/Spacing */
--size-3, --size-4, --size-5, --size-6, --size-8

/* Corners */
--radius-2, --radius-4, --radius-6

/* Animations */
--duration-fast, --ease-3

/* Focus/Interactive */
--secondary, --border-size-1, --border-size-2
```

### Layer 2: Component-Scoped Variables (in `card.css`)

Card-specific variables that reference Layer 1 tokens. These are the **primary customization points**:

```css
.ps-card {
  /* Visual defaults */
  --ps-card-bg: var(--white);
  --ps-card-border-width: var(--border-size-1);
  --ps-card-border-color: var(--gray-200);
  --ps-card-border-radius: 0;
  
  /* Spacing defaults (medium) */
  --ps-card-padding-y: var(--size-8);  /* 32px (adjusted from Figma 30px) */
  --ps-card-padding-x: var(--size-6);  /* 24px */
  --ps-card-gap: var(--size-4);        /* 16px */
  
  /* Interactive states */
  --ps-card-hover-shadow: var(--shadow-3);
  --ps-card-hover-transform: translateY(-2px);
  --ps-card-active-transform: translateY(-1px);
  --ps-card-focus-outline-width: var(--border-size-2);
  --ps-card-focus-outline-color: var(--secondary);
  --ps-card-focus-outline-offset: var(--border-size-2);
  
  /* Transitions */
  --ps-card-transition-duration: var(--duration-fast);
  --ps-card-transition-easing: var(--ease-3);
  
  /* Layout proportions */
  --ps-card-horizontal-image-width: 40%;
  --ps-card-horizontal-image-min-height: 12.5rem; /* 200px */
  --ps-card-horizontal-content-width: 60%;
}
```

**Why Layer 2?** Centralizes component behavior and enables context-specific overrides without changing base styles.

### Layer 3: Context Overrides (Modifiers)

Each modifier class overrides Layer 2 variables for independent, non-dependent modifiers:

```css
/* Size modifiers override spacing variables */
.ps-card--small {
  --ps-card-padding-y: var(--size-4);  /* 16px */
  --ps-card-padding-x: var(--size-4);  /* 16px */
  --ps-card-gap: var(--size-3);        /* 12px */
}

.ps-card--large {
  --ps-card-padding-y: var(--size-8);  /* 32px */
  --ps-card-padding-x: var(--size-8);  /* 32px */
  --ps-card-gap: var(--size-5);        /* 20px */
}

/* Visual variants override visual variables */
.ps-card--outlined {
  --ps-card-border-width: var(--border-size-2);
  --ps-card-border-color: var(--gray-300);
}

.ps-card--flat {
  --ps-card-border-width: 0;
  --ps-card-border-color: transparent;
}

.ps-card--elevated {
  --ps-card-border-width: 0;
  --ps-card-border-color: transparent;
  box-shadow: var(--shadow-2);
}
```

**Benefit**: Modifiers are **fully independent** — combine any size + variant + layout without specificity issues.

## BEM Structure

```
ps-card                                 (block: container, article or a)
├── ps-card__image                      (element: image zone)
└── ps-card__content                    (element: content wrapper)
    ├── ps-card__header                 (element: header zone)
    ├── ps-card__body                   (element: body zone)
    └── ps-card__footer                 (element: footer zone)

Modifiers (independent, combinable):
├── .ps-card--outlined                  (visual: thick border)
├── .ps-card--flat                      (visual: no border)
├── .ps-card--elevated                  (visual: shadow)
├── .ps-card--horizontal                (layout: flex-direction row)
├── .ps-card--image-end                 (layout: image at end)
├── .ps-card--small                     (size: 16px padding)
├── .ps-card--medium                    (size: 32px padding - default)
├── .ps-card--large                     (size: 32px extended padding)
├── .ps-card--radius-sm                 (radius: 4px)
├── .ps-card--radius-md                 (radius: 8px)
└── .ps-card--radius-lg                 (radius: 16px)
```

## Usage Examples

### Pattern 1: Direct Props (Storybook, Simple Cases)

Pass content as HTML strings. Best for Storybook and quick implementations:

```twig
{% include '@components/card/card.twig' with {
  radius: 'md',
  variant: 'elevated',
  image: '<img src="/property.jpg" alt="Property" />',
  header: '<h3>Modern Office</h3>',
  body: '<p>Premium office space in Paris...</p>',
  footer: '<span>€850,000</span>'
} only %}
```

### Pattern 2: Twig Embed Blocks (Drupal, Complex Composition)

Use `{% embed %}` for complex layouts with child components:

```twig
{% embed '@components/card/card.twig' with {
  layout: 'horizontal',
  radius: 'md',
  variant: 'elevated',
  url: '/property/123'
} only %}
  
  {% block image %}
    {% include '@elements/image/image.twig' with {
      src: '/images/property.jpg',
      alt: 'Property photo',
      ratio: '16x9'
    } only %}
  {% endblock %}
  
  {% block header %}
    {% include '@elements/badge/badge.twig' with {
      text: 'DISPONIBLE',
      color: 'success'
    } only %}
    <h3>Bureau Premium La Défense</h3>
  {% endblock %}
  
  {% block body %}
    <p>Surface de bureaux premium dans immeuble classé.</p>
    <div class="specs">
      <span>📏 611 m²</span>
      <span>📍 Paris</span>
    </div>
  {% endblock %}
  
  {% block footer %}
    <div class="price-cta">
      <strong>2 850 000 €</strong>
      <span class="cta">Voir détails →</span>
    </div>
  {% endblock %}
  
{% endembed %}
```

**Key difference**: `{% embed %}` allows Twig blocks; `{% include %}` uses props only.

## Complete Card Types Reference

Based on BNP Paribas Real Estate design requirements, here are **8 common card types** all achievable with the base Card component via block composition:

### 1. Publication Card

**Use case**: Generic publications, articles, white papers

```twig
{% embed '@components/card/card.twig' with {
  url: '/publications/office-trends-2025',
  layout: 'vertical',
  radius: 'md'
} only %}
  {% block media %}
    <img src="/office-space.jpg" alt="Modern office interior" />
  {% endblock %}
  
  {% block header %}
    <div class="meta">
      <span><span data-icon="pin-map"></span> Paris</span>
      <span>15 Nov 2025</span>
    </div>
  {% endblock %}
  
  {% block body %}
    <h3>Publication title</h3>
    <p>Lorem ipsum dolor sit amet consectetur. Nunc sit a quis amet est. Nulla commodo adipiscing.</p>
  {% endblock %}
  
  {% block footer %}
    <span class="ps-link">
      Lire l'étude
      <span data-icon="arrow-right"></span>
    </span>
  {% endblock %}
{% endembed %}
```

### 2. News Card

**Use case**: Blog posts, press releases, news articles

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
    <p>Lorem ipsum dolor sit amet consectetur. Ut nulla convallis tincidunt lacinia volutpat diam pharetra...</p>
  {% endblock %}
  
  {% block footer %}
    <span class="ps-link">Lire la suite <span data-icon="arrow-right"></span></span>
  {% endblock %}
{% endembed %}
```

### 3. Product/Property Card (Simple)

**Use case**: Basic property listings, quick previews

```twig
{% embed '@components/card/card.twig' with {
  url: '/property/123',
  layout: 'vertical',
  variant: 'outlined'
} only %}
  {% block media %}
    <img src="/building.jpg" alt="Office building exterior" />
  {% endblock %}
  
  {% block media_overlay %}
    <button class="favorite-btn" data-icon="heart" aria-label="Add to favorites"></button>
  {% endblock %}
  
  {% block body %}
    <div class="price-surface">
      <strong>2 500 000 €</strong>
      <span>• 450 m²</span>
    </div>
    <h3>Product title</h3>
    <div class="location">
      <span data-icon="pin-map"></span> Location
    </div>
  {% endblock %}
  
  {% block footer %}
    <span class="ps-link">Consulter l'annonce <span data-icon="arrow-right"></span></span>
  {% endblock %}
{% endembed %}
```

### 4. Offer Card (Vertical - Complex)

**Use case**: Full property listings with status badges, actions, detailed metadata

```twig
{% embed '@components/card/card.twig' with {
  url: '/property/456',
  layout: 'vertical'
} only %}
  {% block media %}
    {# Carousel navigation would go here #}
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

### 5. Offer Card (Horizontal)

**Use case**: List view for property search results

```twig
{% embed '@components/card/card.twig' with {
  url: '/property/789',
  layout: 'horizontal'
} only %}
  {% block media %}
    <img src="/madrid-office.jpg" alt="Rent Offices Madrid" />
  {% endblock %}
  
  {% block media_overlay %}
    {# Same badges/actions as vertical #}
    <div class="badges-actions">
      <div class="badges">
        <span class="badge badge--neutral"><span data-icon="eye"></span> Already viewed</span>
        <span class="badge badge--gold"><span data-icon="star"></span> Exclusivity</span>
      </div>
      <div class="actions">
        <button data-icon="bookmark"></button>
        <button data-icon="heart"></button>
      </div>
    </div>
  {% endblock %}
  
  {% block body %}
    <h3>Rent Offices MADRID Barrio de Chamberí</h3>
    <div class="meta"><span data-icon="pin-map"></span> 28010 MADRID</div>
    <div class="surface">611.3 m²</div>
    <div class="price">20 000 € HT/HC/m²/an</div>
  {% endblock %}
  
  {% block footer %}
    <span class="ps-link">View the property <span data-icon="arrow-right"></span></span>
  {% endblock %}
{% endembed %}
```

### 6. CTA Card (Call-to-Action)

**Use case**: Interactive prompts, calculators, service offerings

```twig
{% embed '@components/card/card.twig' with {
  variant: 'flat',
  radius: 'none',
  size: 'large'
} only %}
  {% block body %}
    <h3>Calculate the target surface area of your future offices !</h3>
    <p>Quick and easy to use, the surface area calculator lets you define the m2 surface area you need in just a few clicks.</p>
  {% endblock %}
  
  {% block footer %}
    {% include '@elements/button/button.twig' with {
      label: 'Start calculator',
      variant: 'outlined',
      color: 'primary'
    } only %}
  {% endblock %}
{% endembed %}
```

### 7. Solution/Service Card

**Use case**: Service offerings, solution categories, icon-based navigation

```twig
{% embed '@components/card/card.twig' with {
  variant: 'flat',
  radius: 'none',
  size: 'medium'
} only %}
  {% block header %}
    <div class="icon-large" data-icon="briefcase"></div>
  {% endblock %}
  
  {% block body %}
    <h3>Solutions title</h3>
    <p>Solutions details</p>
  {% endblock %}
  
  {% block footer %}
    <span class="ps-link">Consulter les solutions <span data-icon="arrow-right"></span></span>
  {% endblock %}
{% endembed %}
```

### 8. Study/Trendbook Card

**Use case**: Premium publications, market studies, trend reports

```twig
{# Vertical variant #}
{% embed '@components/card/card.twig' with {
  url: '/studies/offices-new-chapter',
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
    <img src="/trendbook-offices.jpg" alt="Offices: A new chapter" />
  {% endblock %}
  
  {% block body %}
    <h3>Study title</h3>
    <p>Lorem ipsum dolor sit amet consectetur. Enim fames hendrerit amet nibh tempus sit nibh facilisis. Viverra tincidunt risus a non.</p>
  {% endblock %}
  
  {% block footer %}
    {% include '@elements/button/button.twig' with {
      label: "Télécharger l'étude",
      variant: 'outlined',
      color: 'primary'
    } only %}
  {% endblock %}
{% endembed %}

{# Horizontal variant #}
{% embed '@components/card/card.twig' with {
  url: '/studies/offices-new-chapter',
  layout: 'horizontal',
  imagePosition: 'left'
} only %}
  {% block media %}
    <img src="/trendbook-offices.jpg" alt="Offices: A new chapter" />
  {% endblock %}
  
  {% block header %}
    {% include '@elements/badge/badge.twig' with {
      label: 'TAG LABEL',
      color: 'primary'
    } only %}
    <span class="date">15 Dec 2025</span>
  {% endblock %}
  
  {% block body %}
    <h3>Study title</h3>
    <p>Lorem ipsum dolor sit amet consectetur. Enim fames hendrerit amet nibh tempus sit nibh facilisis.</p>
  {% endblock %}
  
  {% block footer %}
    {% include '@elements/button/button.twig' with {
      label: "Télécharger l'étude",
      variant: 'outlined'
    } only %}
  {% endblock %}
{% endembed %}
```

## Real Estate Use Cases (Extended Examples)

### Property Listing Card (Full Featured)

```twig
{% embed '@components/card/card.twig' with {
  url: '/property/123',
  variant: 'elevated',
  radius: 'md'
} only %}
  {% block media %}
    <img src="/office-building.jpg" alt="Bureau moderne" />
  {% endblock %}
  
  {% block header %}
    <span class="badge badge--success">DISPONIBLE</span>
    <h3>Bureau Prestige La Défense</h3>
  {% endblock %}
  
  {% block body %}
    <p>Surface de bureaux premium dans immeuble classé, finitions haut de gamme, terrasse privative.</p>
    <div class="property-specs">
      <span>📏 Surface: 611 m²</span>
      <span>🚇 Métro: 2 min</span>
      <span>🅿️ Parking: 15 places</span>
    </div>
  {% endblock %}
  
  {% block footer %}
    <div class="price-footer">
      <div>
        <small>Prix de vente</small>
        <strong class="price">2 850 000 €</strong>
      </div>
      <span class="cta">Voir détails →</span>
    </div>
  {% endblock %}
{% endembed %}
```

### Agent Contact Card

```twig
{% embed '@components/card/card.twig' with {
  variant: 'elevated',
  radius: 'lg',
  size: 'small'
} only %}
  {% block media %}
    <div class="agent-avatar">
      <img src="/agents/sophie-martin.jpg" alt="Sophie Martin" />
    </div>
  {% endblock %}
  
  {% block header %}
    <h3>Sophie Martin</h3>
    <p class="role">Conseillère Senior</p>
  {% endblock %}
  
  {% block body %}
    <p>Spécialiste immobilier tertiaire Paris & Île-de-France</p>
    <div class="contact">
      <div>📧 sophie.martin@bnpparibas.com</div>
      <div>📱 +33 6 12 34 56 78</div>
    </div>
  {% endblock %}
  
  {% block footer %}
    <button class="btn btn--primary btn--block">Prendre rendez-vous</button>
  {% endblock %}
{% endembed %}
```

## Composition: Creating Specialized Cards

For domain-specific cards (PropertyCard, NewsCard, AgentCard), embed Card to inherit its flexibility:

```twig
{# Components: property-card.twig (specialized component) #}
{%- set variant = variant|default('elevated') -%}
{%- set radius = radius|default('md') -%}

{% embed '@components/card/card.twig' with {
  url: property.url,
  variant: variant,
  radius: radius
} only %}
  
  {% block image %}
    {% include '@elements/image/image.twig' with {
      src: property.image.url,
      alt: property.image.alt,
      ratio: '4x3'
    } only %}
  {% endblock %}
  
  {% block header %}
    {% if property.status %}
      {% include '@elements/badge/badge.twig' with {
        text: property.status,
        color: property.status_color
      } only %}
    {% endif %}
    <h3>{{ property.title }}</h3>
  {% endblock %}
  
  {% block body %}
    <p>{{ property.description }}</p>
    {% if property.features %}
      <div class="features">
        {% for feature in property.features %}
          <span>{{ feature.icon }} {{ feature.value }}</span>
        {% endfor %}
      </div>
    {% endif %}
  {% endblock %}
  
  {% block footer %}
    <div class="price-cta">
      <strong class="price">{{ property.price }}</strong>
      <span class="cta">Voir détails →</span>
    </div>
  {% endblock %}
  
{% endembed %}
```

**Benefit**: Specialized cards inherit all Card flexibility (layouts, sizes, variants) automatically.

## Atoms Composition

Card commonly composes these atomic components:

- **`@elements/image/image.twig`** — Responsive images with aspect ratios
- **`@elements/badge/badge.twig`** — Status/category labels
- **`@elements/button/button.twig`** — Action buttons
- **`@elements/heading/heading.twig`** — Semantic heading levels
- **`@elements/text/text.twig`** — Typography (body text, small text)
- **`@elements/link/link.twig`** — Navigation links and CTAs

## Accessibility

Card meets **WCAG 2.2 Level AA**:

- **Semantic HTML**: `<article>` (default) or `<a>` when clickable
- **Keyboard navigation**: Clickable cards fully accessible via Tab + Enter
- **Focus indicator**: `:focus-visible` with 2px solid outline (`var(--secondary)`), 2px offset
- **Color contrast**: Border meets 3:1 ratio (UI component requirement)
- **Image alt text**: Delegated to `<img>` element (must be provided by implementer)
- **Link semantics**: When URL provided, entire card is clickable (semantically valid `<a>` element)
- **Screen readers**: Announce as article/link with proper content hierarchy
- **ARIA**: No additional ARIA needed (semantic HTML sufficient)
- **Hover effects**: Visual only, do not convey essential information

### Accessibility Best Practices

```twig
{# ✅ CORRECT: Meaningful alt text #}
{% embed '@components/card/card.twig' with { url: '/property/123' } %}
  {% block image %}
    <img src="/office.jpg" alt="Modern office building with glass facade in La Défense business district" />
  {% endblock %}
{% endembed %}

{# ❌ WRONG: Missing or generic alt text #}
<img src="/office.jpg" alt="image" />
<img src="/office.jpg" />
```

## Responsive Behavior

- **Mobile (< 48rem / 768px)**: Horizontal layout automatically stacks to vertical
- **Image sizing**: Fills container, maintains aspect ratio via `object-fit: cover`
- **Content padding**: Consistent across breakpoints (adjust via `size` prop if needed)
- **Touch targets**: Clickable cards meet 44×44px minimum on mobile
- **Text wrapping**: Inherits parent text styles (no forced font sizes)

## Variants

### Visual Variants
- **default**: Standard border (1px solid gray-200)
- **outlined**: Thick border (2px solid gray-300)
- **flat**: No border
- **elevated**: Box shadow (shadow-2), enhanced on hover (shadow-4)

### Layout Variants
- **vertical** (default): Image top/bottom, content below/above
- **horizontal**: Image left/right (40%), content opposite (60%); stacks on mobile

### Size Variants
- **small**: 16px padding, 12px gap
- **medium** (default): 32px padding, 16px gap
- **large**: 32px extended padding, 20px gap

### Border Radius Variants
- **none** (default): 0 (sharp corners)
- **sm**: 4px (var(--radius-2))
- **md**: 8px (var(--radius-4))
- **lg**: 16px (var(--radius-6))

### Image Position
- **start** (default): Top (vertical) / Left (horizontal)
- **end**: Bottom (vertical) / Right (horizontal)

## Design Notes

- **Generic container**: Card enforces NO content structure. Use blocks for maximum flexibility.
- **Composition over features**: Create specialized cards (PropertyCard, NewsCard) by embedding Card instead of extending it.
- **No enforced typography**: Font sizes, weights, colors are delegated to child components.
- **Clickable pattern**: When `url` provided, entire card is clickable (better UX than small "Click here" links).
- **Progressive enhancement**: Works without JavaScript. Hover/active effects are CSS-only.
- **Modular modifiers**: All modifiers are independent and combinable (no cascading specificity issues).
- **3-Layer CSS system**: Component variables enable flexible theming without editing base styles.

## Browser Support

- **Modern browsers**: Chrome, Firefox, Safari, Edge 79+
- **CSS Features**: Flexbox, CSS Variables (custom properties), `:focus-visible`
- **Graceful degradation**: `:focus-visible` falls back to `:focus` in older browsers
- **No JavaScript required**: Fully functional with CSS only

## Design Tokens Used

### Colors
- `--white` — Background (default)
- `--gray-200` — Border (default variant)
- `--gray-300` — Border (outlined variant)
- `--secondary` — Focus outline color

### Shadows
- `--shadow-2` — Elevated variant base
- `--shadow-3` — Hover elevation
- `--shadow-4` — Elevated variant hover

### Spacing
- `--size-3` — Small gap (12px)
- `--size-4` — Medium gap, small padding (16px)
- `--size-5` — Large gap (20px)
- `--size-6` — Medium padding-x (24px)
- `--size-8` — Medium/large padding-y (32px)

### Borders
- `--border-size-1` — Default border width (1px)
- `--border-size-2` — Outlined border, focus outline (2px)
- `--radius-2` — Small radius (4px)
- `--radius-4` — Medium radius (8px)
- `--radius-6` — Large radius (16px)

### Animations
- `--duration-fast` — Transition duration
- `--ease-3` — Easing function

---

**Component Type**: Molecule  
**Category**: Components  
**Status**: Stable  
**Implemented**: 2025-12-10  
**Last Updated**: 2025-12-10  
**3-Layer CSS Variables**: ✅ Fully migrated  
**Real Estate Context**: ✅ Examples included
