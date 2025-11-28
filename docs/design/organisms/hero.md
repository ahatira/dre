# Hero (Organism)

**Niveau Atomic Design** : Organism / Masthead  
**Catégorie** : Featured section  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Section d'en-tête visuelle avec image/vidéo, titre, texte, et CTA(s). Variantes pour alignements, media à gauche/droite, overlay sombre, et support de fond vidéo. Responsif et accessible.

---

## 🏗️ Structure BEM

```html
<section class="ps-hero ps-hero--media-left ps-hero--overlay" aria-labelledby="hero-title">
  <div class="ps-hero__media">
    <img class="ps-hero__image" src="/media/hero.jpg" alt="Quartier résidentiel verdoyant" />
  </div>
  <div class="ps-hero__content">
    <span class="ps-eyebrow ps-eyebrow--accent">Trouver votre bien</span>
    <h1 id="hero-title" class="ps-hero__title">Votre prochain chez-vous</h1>
    <p class="ps-hero__text">Explorez des milliers d'annonces avec filtres avancés.</p>
    <div class="ps-hero__actions">
      <a class="ps-button ps-button--primary" href="#search">Rechercher</a>
      <a class="ps-button ps-button--secondary" href="#discover">Découvrir</a>
    </div>
  </div>
</section>
```

### Classes BEM

```
ps-hero                                    // Block
  ps-hero__media                           // Media wrapper (image/video)
  ps-hero__image                           // Image
  ps-hero__video                           // Video embed
  ps-hero__content                         // Text + actions
  ps-hero__title                           // Heading
  ps-hero__text                            // Paragraph
  ps-hero__actions                         // CTA buttons

Modificateurs :
  ps-hero--media-left | --media-right      // Layout
  ps-hero--overlay                         // Dark overlay on media
  ps-hero--center                          // Centered content
  ps-hero--full                            // Full-width/height
```

---

## 📐 Props (Component API)

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Hero'
status: stable
group: organisms
description: 'Hero section with media, heading, text, and CTAs.'

props:
  type: object
  properties:
    title: { type: string }
    text: { type: string }
    eyebrow: { type: string }
    actions:
      type: array
      items:
        type: object
        properties:
          label: { type: string }
          href: { type: string }
          variant:
            type: string
            enum: ['primary','secondary']
            default: 'primary'
        required: ['label','href']
    media:
      type: object
      properties:
        type:
          type: string
          enum: ['image','video']
          default: 'image'
        image:
          type: object
          properties:
            src: { type: string }
            alt: { type: string }
        video:
          type: object
          properties:
            embedId: { type: string }
            provider:
              type: string
              enum: ['youtube','vimeo']
              default: 'youtube'
    layout:
      type: string
      enum: ['media-left','media-right','center','full']
      default: 'media-left'
    overlay:
      type: boolean
      default: false
    attributes:
      type: Drupal\\Core\\Template\\Attribute
```

---

## 🔧 Template Twig

```twig
{% set layout = layout|default('media-left') %}
{% set classes = ['ps-hero', 'ps-hero--' ~ layout] %}
{% if overlay %}{% set classes = classes|merge(['ps-hero--overlay']) %}{% endif %}

<section {{ attributes.addClass(classes) }} aria-labelledby="hero-title">
  {% if media and media.type == 'image' and media.image %}
    <div class="ps-hero__media">
      <img class="ps-hero__image" src="{{ media.image.src }}" alt="{{ media.image.alt }}" />
    </div>
  {% elseif media and media.type == 'video' and media.video %}
    <div class="ps-hero__media">
      <iframe class="ps-hero__video" src="https://www.youtube.com/embed/{{ media.video.embedId }}" title="{{ title }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
  {% endif %}

  <div class="ps-hero__content">
    {% if eyebrow %}<span class="ps-eyebrow ps-eyebrow--accent">{{ eyebrow }}</span>{% endif %}
    {% if title %}<h1 id="hero-title" class="ps-hero__title">{{ title }}</h1>{% endif %}
    {% if text %}<p class="ps-hero__text">{{ text }}</p>{% endif %}
    {% if actions %}
      <div class="ps-hero__actions">
        {% for action in actions %}
          <a class="ps-button ps-button--{{ action.variant|default('primary') }}" href="{{ action.href }}">{{ action.label }}</a>
        {% endfor %}
      </div>
    {% endif %}
  </div>
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-hero {
  display: grid; gap: var(--ps-spacing-6, 24px);
  align-items: center;

  &__media { position: relative; }
  &__image, &__video { width: 100%; border-radius: var(--ps-border-radius-lg, 12px); box-shadow: var(--ps-shadow-sm, 0 1px 2px rgba(0,0,0,0.08)); }

  &__title { font-size: var(--ps-font-size-3xl, 32px); line-height: 1.2; }
  &__text { font-size: var(--ps-font-size-md, 16px); color: var(--ps-color-neutral-700, #3B4754); }
  &__actions { display: flex; gap: var(--ps-spacing-3, 12px); }

  &--media-left { grid-template-columns: 1fr 1fr; }
  &--media-right { grid-template-columns: 1fr 1fr; direction: rtl; }
  &--center { grid-template-columns: 1fr; text-align: center; justify-items: center; }
  &--full { grid-template-columns: 1fr; }

  &--overlay {
    &::before { content: ""; position: absolute; inset: 0; background: rgba(0,0,0,0.35); pointer-events: none; }
  }

  @media (max-width: 768px) {
    &--media-left, &--media-right { grid-template-columns: 1fr; }
  }
}
```

---

## ♿ Accessibilité

- `aria-labelledby` reliant le titre.
- Texte alternatif pertinent pour l'image.
- Vidéo avec `title` (ou captions si nécessaire).

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-hero/ps-hero.twig' with {
  title: 'Votre prochain chez-vous',
  text: "Explorez des milliers d'annonces.",
  eyebrow: 'Trouver votre bien',
  actions: [
    { label: 'Rechercher', href: '#search', variant: 'primary' },
    { label: 'Découvrir', href: '#discover', variant: 'secondary' }
  ],
  media: { type: 'image', image: { src: '/media/hero.jpg', alt: 'Quartier résidentiel verdoyant' } },
  layout: 'media-left',
  overlay: true
} %}
```

---

## 📚 Ressources

- Composition: atoms (heading, text, button, eyebrow), molecules (video)
- Tokens: typography, spacing, shadows
