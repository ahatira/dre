# Carousel (Molecule)

**Niveau Atomic Design** : Molecule / Media Gallery  
**Catégorie** : Content slider  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Carousel responsive pour images ou cartes. Inclut boutons précédent/suivant, pagination (bullets), swipe tactile, clavier (ArrowLeft/Right, Home/End), et a11y (roving tabindex, aria-roledescription). Supporte variantes: "cards", "images", "auto-height", "loop" (opt-in).

---

## 🏗️ Structure BEM

```html
<section class="ps-carousel ps-carousel--images" aria-label="Galerie de photos" data-carousel>
  <div class="ps-carousel__viewport" tabindex="0" aria-live="polite" aria-atomic="true">
    <ul class="ps-carousel__track">
      <li class="ps-carousel__slide is-active">
        <img class="ps-carousel__image" src="/media/1.jpg" alt="Salon" />
      </li>
      <li class="ps-carousel__slide">
        <img class="ps-carousel__image" src="/media/2.jpg" alt="Cuisine" />
      </li>
      <li class="ps-carousel__slide">
        <img class="ps-carousel__image" src="/media/3.jpg" alt="Chambre" />
      </li>
    </ul>
  </div>
  <div class="ps-carousel__controls">
    <button class="ps-carousel__prev" type="button" aria-label="Précédent" data-carousel-prev>
      <svg class="ps-carousel__icon" aria-hidden="true"><use href="#icon-chevron-left"></use></svg>
    </button>
    <button class="ps-carousel__next" type="button" aria-label="Suivant" data-carousel-next>
      <svg class="ps-carousel__icon" aria-hidden="true"><use href="#icon-chevron-right"></use></svg>
    </button>
  </div>
  <div class="ps-carousel__pagination" role="tablist" aria-label="Slides">
    <button class="ps-carousel__bullet is-active" role="tab" aria-selected="true" aria-controls="slide-1" data-carousel-goto="0"></button>
    <button class="ps-carousel__bullet" role="tab" aria-selected="false" aria-controls="slide-2" data-carousel-goto="1"></button>
    <button class="ps-carousel__bullet" role="tab" aria-selected="false" aria-controls="slide-3" data-carousel-goto="2"></button>
  </div>
</section>
```

### Classes BEM

```
ps-carousel                               // Block
  ps-carousel__viewport                   // Overflow hidden + focusable
  ps-carousel__track                      // Slides container (flex)
  ps-carousel__slide                      // Slide item
  ps-carousel__image                      // Image in slide
  ps-carousel__controls                   // Prev/next buttons
  ps-carousel__prev | __next              // Controls buttons
  ps-carousel__pagination                 // Bullets container
  ps-carousel__bullet                     // Bullet button

Modificateurs :
  ps-carousel--images                     // Images mode
  ps-carousel--cards                      // Cards mode
  ps-carousel--auto-height                // Auto adjust height
  ps-carousel--loop                       // Loop sliding (optional)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Carousel'
status: stable
group: molecules
description: 'Carousel responsive pour images ou cartes.'

props:
  type: object
  properties:
    variant:
      type: string
      enum: ['images','cards']
      default: 'images'
    slides:
      type: array
      items:
        type: object
        properties:
          id:
            type: string
          image:
            type: object
            properties:
              src: { type: string }
              alt: { type: string }
          card:
            type: object
            description: 'Contenu HTML/Twig de carte'
          ariaLabel:
            type: string
        required: ['id']
    loop:
      type: boolean
      default: false
    autoHeight:
      type: boolean
      default: false
    ariaLabel:
      type: string
      default: 'Carousel'
    attributes:
      type: Drupal\\Core\\Template\\Attribute
  required:
    - slides
```

---

## 🎭 Variants

- **Mode** : `images` | `cards`.
- **Comportement** : `loop` (opt-in), `autoHeight`.

---

## 🎨 Design Tokens

- Couleurs: `--ps-color-neutral-0`, `--ps-color-neutral-800`, `--ps-color-neutral-300`
- Espacements: `--ps-spacing-2|3`
- Bordures: `--ps-border-radius-md`
- Ombres: `--ps-shadow-sm`

---

## 🔧 Template Twig

```twig
{% set variant = variant|default('images') %}
{% set root_classes = ['ps-carousel', 'ps-carousel--' ~ variant] %}
{% if loop %}{% set root_classes = root_classes|merge(['ps-carousel--loop']) %}{% endif %}
{% if autoHeight %}{% set root_classes = root_classes|merge(['ps-carousel--auto-height']) %}{% endif %}

<section {{ attributes.addClass(root_classes) }} aria-label="{{ ariaLabel|default('Carousel') }}" data-carousel>
  <div class="ps-carousel__viewport" tabindex="0">
    <ul class="ps-carousel__track">
      {% for slide in slides %}
        <li class="ps-carousel__slide{% if loop.index0 == 0 %} is-active{% endif %}" id="slide-{{ loop.index }}" role="tabpanel" aria-roledescription="slide" aria-label="{{ slide.ariaLabel|default('Slide ' ~ loop.index) }}" tabindex="-1">
          {% if variant == 'images' and slide.image %}
            <img class="ps-carousel__image" src="{{ slide.image.src }}" alt="{{ slide.image.alt }}" />
          {% elseif variant == 'cards' and slide.card %}
            {{ slide.card }}
          {% endif %}
        </li>
      {% endfor %}
    </ul>
  </div>
  <div class="ps-carousel__controls">
    <button class="ps-carousel__prev" type="button" aria-label="Précédent" data-carousel-prev>
      <svg class="ps-carousel__icon" aria-hidden="true"><use href="#icon-chevron-left"></use></svg>
    </button>
    <button class="ps-carousel__next" type="button" aria-label="Suivant" data-carousel-next>
      <svg class="ps-carousel__icon" aria-hidden="true"><use href="#icon-chevron-right"></use></svg>
    </button>
  </div>
  <div class="ps-carousel__pagination" role="tablist" aria-label="Slides">
    {% for slide in slides %}
      <button class="ps-carousel__bullet{% if loop.index0 == 0 %} is-active{% endif %}" role="tab" aria-selected="{{ loop.index0 == 0 ? 'true' : 'false' }}" aria-controls="slide-{{ loop.index }}" data-carousel-goto="{{ loop.index0 }}"></button>
    {% endfor %}
  </div>
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-carousel {
  &__viewport { overflow: hidden; outline: none; }
  &__track { display: flex; transition: transform var(--ps-transition-duration-fast, 0.2s) ease; will-change: transform; }
  &__slide { flex: 0 0 100%; position: relative; }
  &__image { width: 100%; height: auto; display: block; }

  &__controls { display: flex; justify-content: space-between; align-items: center; margin-top: var(--ps-spacing-2, 8px); }
  &__prev, &__next {
    display: inline-flex; align-items: center; justify-content: center;
    width: 36px; height: 36px; border-radius: var(--ps-border-radius-md, 8px);
    border: none; background: var(--ps-color-neutral-100, #F5F7F9);
    cursor: pointer;
    &:hover { background: var(--ps-color-neutral-200, #E8EBEF); }
    &:focus-visible { outline: 2px solid var(--ps-color-interactive-focus-outline, #0B5FFF); outline-offset: 2px; }
  }

  &__pagination { display: flex; gap: var(--ps-spacing-2, 8px); justify-content: center; margin-top: var(--ps-spacing-2, 8px); }
  &__bullet {
    width: 8px; height: 8px; border-radius: 50%; border: none; background: var(--ps-color-neutral-300, #D2D7DB);
    cursor: pointer; position: relative;
    &.is-active { background: var(--ps-color-primary-600, #0DB089); }
    &:focus-visible { outline: 2px solid var(--ps-color-interactive-focus-outline, #0B5FFF); outline-offset: 2px; }
  }

  &--cards {
    .ps-carousel__slide { padding: var(--ps-spacing-3, 12px); }
  }
}
```

---

## ♿ Accessibilité

- `aria-roledescription="slide"` sur les panneaux; pagination role=tablist + bullets role=tab.
- Clavier: `ArrowLeft/Right` pour navigation; `Home/End` pour premier/dernier.
- Roving tabindex sur viewport focusable.
- Icônes avec `aria-hidden`.

---

## 🔌 JavaScript behavior (optionnel)

```js
(function(){
  function initCarousel(root){
    const track = root.querySelector('.ps-carousel__track');
    const slides = Array.from(root.querySelectorAll('.ps-carousel__slide'));
    const bullets = Array.from(root.querySelectorAll('.ps-carousel__bullet'));
    const prev = root.querySelector('[data-carousel-prev]');
    const next = root.querySelector('[data-carousel-next]');
    let index = 0;

    function update(){
      track.style.transform = `translateX(${-index * 100}%)`;
      slides.forEach((s,i)=>s.classList.toggle('is-active', i===index));
      bullets.forEach((b,i)=>{
        b.classList.toggle('is-active', i===index);
        b.setAttribute('aria-selected', i===index ? 'true' : 'false');
      });
      const active = slides[index];
      active?.setAttribute('tabindex','0');
      active?.focus({preventScroll:true});
      root.querySelector('.ps-carousel__viewport')?.setAttribute('aria-label', `Slide ${index+1} of ${slides.length}`);
    }

    prev?.addEventListener('click', ()=>{ index = Math.max(0, index-1); update(); });
    next?.addEventListener('click', ()=>{ index = Math.min(slides.length-1, index+1); update(); });
    bullets.forEach((b)=> b.addEventListener('click', ()=>{ index = Number(b.dataset.carouselGoto)||0; update(); }));

    root.querySelector('.ps-carousel__viewport')?.addEventListener('keydown', (e)=>{
      if(e.key==='ArrowRight'){ next.click(); }
      else if(e.key==='ArrowLeft'){ prev.click(); }
      else if(e.key==='Home'){ index=0; update(); }
      else if(e.key==='End'){ index=slides.length-1; update(); }
    });

    // Basic touch support
    let startX=0; let dx=0;
    root.addEventListener('touchstart', e=>{ startX = e.touches[0].clientX; }, {passive:true});
    root.addEventListener('touchmove', e=>{ dx = e.touches[0].clientX - startX; }, {passive:true});
    root.addEventListener('touchend', ()=>{
      if (dx < -50) next.click(); else if (dx > 50) prev.click(); dx = 0;
    });

    update();
  }

  document.querySelectorAll('[data-carousel]').forEach(initCarousel);
})();
```

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-carousel/ps-carousel.twig' with {
  variant: 'images',
  slides: [
    { id: 's1', image: { src: '/media/1.jpg', alt: 'Salon' } },
    { id: 's2', image: { src: '/media/2.jpg', alt: 'Cuisine' } },
    { id: 's3', image: { src: '/media/3.jpg', alt: 'Chambre' } }
  ]
} %}
```

---

## 📚 Ressources

- WAI-ARIA carousel patterns
- Touch events and keyboard navigation
