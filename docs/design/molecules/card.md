# Card (Molecule)

Type: Molecule / Component
Rôle: Présenter un contenu avec image, titre, description, meta et actions.
Statut: ✅ Stable
Version: 1.0.0

---

## Variants (déduits des maquettes)
- Product Card
- News Card
- Publications Card
- Solutions Card
- Studies Card
- Push Card (mise en avant)

## BEM
```
ps-card
  ps-card__image
  ps-card__badge
  ps-card__content
  ps-card__eyebrow
  ps-card__title
  ps-card__description
  ps-card__meta
  ps-card__actions
  ps-card__cta

Modificateurs:
  ps-card--product | --news | --publication | --solution | --study | --push
  ps-card--featured | --compact | --horizontal | --vertical
```

## API (YAML)
```yaml
name: 'PS Card'
status: stable
group: molecules
props:
  type: object
  properties:
    image: { type: object, properties: { url: { type: string }, alt: { type: string } } }
    badge: { type: string }
    eyebrow: { type: string }
    title: { type: string }
    description: { type: string }
    meta: { type: array, items: { type: object, properties: { icon: { type: string }, text: { type: string } } } }
    cta: { type: object, properties: { text: { type: string }, url: { type: string }, variant: { type: string } } }
    variant: { type: string, enum: ['product','news','publication','solution','study','push','featured','compact'], default: 'product' }
    layout: { type: string, enum: ['vertical','horizontal'], default: 'vertical' }
    attributes: { type: Drupal\Core\Template\Attribute }
  required: ['title']
```

## Twig
```twig
<article {{ attributes.addClass(['ps-card', 'ps-card--' ~ (variant ?? 'product'), 'ps-card--' ~ (layout ?? 'vertical')]) }}>
  {% if image %}
    <div class="ps-card__image"><img src="{{ image.url }}" alt="{{ image.alt }}" /></div>
  {% endif %}
  <div class="ps-card__content">
    {% if badge %}<span class="ps-card__badge">{{ badge }}</span>{% endif %}
    {% if eyebrow %}<div class="ps-card__eyebrow">{{ eyebrow }}</div>{% endif %}
    <h3 class="ps-card__title">{{ title }}</h3>
    {% if description %}<p class="ps-card__description">{{ description }}</p>{% endif %}
    {% if meta %}
      <ul class="ps-card__meta">
        {% for m in meta %}
          <li class="ps-card__meta-item">
            {% if m.icon %}{% include '@ps_theme/ps-icon/ps-icon.twig' with { name: m.icon, size: 20, ariaLabel: '' } %}{% endif %}
            <span>{{ m.text }}</span>
          </li>
        {% endfor %}
      </ul>
    {% endif %}
    {% if cta %}
      <div class="ps-card__actions">
        {% include '@ps_theme/ps-button/ps-button.twig' with { label: cta.text, url: cta.url, variant: cta.variant ?? 'primary', color: 'green' } %}
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
