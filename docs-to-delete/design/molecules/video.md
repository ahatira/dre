# Video (Molecule)

**Niveau Atomic Design** : Molecule / Media  
**Catégorie** : Content media  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Conteneur vidéo unifié pour HTML5 et providers (YouTube, Vimeo). Gère l'affichage du poster, titre/caption, contrôles natifs ou custom, responsive ratio, et préférences d'accessibilité (sous-titres, mute par défaut, éviter l'autoplay non sollicité).

---

## 🏗️ Structure BEM

```html
<figure class="ps-video ps-video--16x9 ps-video--html5" aria-labelledby="video-title">
  <div class="ps-video__media">
    <video class="ps-video__element" controls preload="metadata" poster="poster.jpg">
      <source src="movie.mp4" type="video/mp4" />
      <track kind="captions" src="captions.vtt" srclang="fr" label="Français" default />
    </video>
  </div>
  <figcaption class="ps-video__caption">
    <span id="video-title" class="ps-video__title">Visite du bien</span>
    <span class="ps-video__description">Appartement 3 pièces - Paris</span>
  </figcaption>
</figure>
```

Providers:

```html
<figure class="ps-video ps-video--16x9 ps-video--youtube">
  <div class="ps-video__media">
    <iframe class="ps-video__embed" src="https://www.youtube.com/embed/VIDEO_ID" title="YouTube video" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
  </div>
</figure>
```

### Classes BEM

```
ps-video                                 // Block (figure)
  ps-video__media                        // Media wrapper (ratio)
  ps-video__element                      // <video>
  ps-video__embed                        // <iframe> for providers
  ps-video__caption                      // <figcaption>
  ps-video__title                        // Titre
  ps-video__description                  // Description

Modificateurs :
  ps-video--html5                        // Source HTML5
  ps-video--youtube                      // YouTube provider
  ps-video--vimeo                        // Vimeo provider
  ps-video--16x9 | --4x3 | --1x1         // Ratios
  ps-video--muted                        // Muet par défaut
  ps-video--no-autoplay                  // Désactiver autoplay
  ps-video--rounded                      // Bords arrondis
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Video'
status: stable
group: molecules
description: 'Conteneur vidéo pour HTML5, YouTube et Vimeo.'

props:
  type: object
  properties:
    provider:
      type: string
      enum: ['html5','youtube','vimeo']
      default: 'html5'
    sources:
      type: array
      description: 'Liste de sources (HTML5)'
      items:
        type: object
        properties:
          src:
            type: string
          type:
            type: string
        required: ['src']
    videoAttrs:
      type: object
      description: 'Attributs pour <video> (controls, preload, poster, muted)'
    embedId:
      type: string
      description: 'ID vidéo pour YouTube/Vimeo'
    ratio:
      type: string
      enum: ['16x9','4x3','1x1']
      default: '16x9'
    title:
      type: string
    description:
      type: string
    caption:
      type: string
      description: 'Texte de légende (alternative à title/description)'
    tracks:
      type: array
      description: 'Sous-titres VTT pour HTML5'
      items:
        type: object
        properties:
          kind:
            type: string
            default: 'captions'
          src:
            type: string
          srclang:
            type: string
          label:
            type: string
          default:
            type: boolean
    attributes:
      type: Drupal\\Core\\Template\\Attribute
  required:
    - provider
```

---

## 🎭 Variants

- **Providers** : `html5` | `youtube` | `vimeo`.
- **Ratios** : `16x9` | `4x3` | `1x1`.
- **Styles** : `rounded`.
- **Comportement** : `muted`, `no-autoplay`.

---

## 🎨 Design Tokens

- Couleurs: `--ps-color-neutral-900` (fond), `--ps-color-neutral-0` (icônes/texte), `--ps-color-neutral-300` (bordure)
- Rayons: `--ps-border-radius-lg`
- Ombres: `--ps-shadow-sm|md`
- Espacements: `--ps-spacing-2|3`

---

## 🔧 Template Twig

```twig
{% set provider = provider|default('html5') %}
{% set ratio = ratio|default('16x9') %}
{% set root_classes = ['ps-video', 'ps-video--' ~ ratio, 'ps-video--' ~ provider] %}

<figure {{ attributes.addClass(root_classes) }}>
  <div class="ps-video__media">
    {% if provider == 'html5' %}
      {# Safely render <video> with common attributes #}
      <video class="ps-video__element"
        {% set v = videoAttrs|default({}) %}
        {% if v.controls is not defined or v.controls %}controls{% endif %}
        {% if v.poster %}poster="{{ v.poster }}"{% endif %}
        {% if v.preload %}preload="{{ v.preload }}"{% endif %}
        {% if v.muted %}muted{% endif %}
        {% if v.autoplay %}autoplay{% endif %}
        playsinline
      >
        {% for source in sources %}
          <source src="{{ source.src }}" {% if source.type %}type="{{ source.type }}"{% endif %} />
        {% endfor %}
        {% for track in tracks %}
          <track kind="{{ track.kind|default('captions') }}" src="{{ track.src }}" srclang="{{ track.srclang }}" label="{{ track.label }}" {% if track.default %}default{% endif %} />
        {% endfor %}
      </video>
    {% elseif provider == 'youtube' %}
      <iframe class="ps-video__embed" src="https://www.youtube.com/embed/{{ embedId }}" title="{{ title }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    {% elseif provider == 'vimeo' %}
      <iframe class="ps-video__embed" src="https://player.vimeo.com/video/{{ embedId }}" title="{{ title }}" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
    {% endif %}
  </div>
  {% if caption or title or description %}
    <figcaption class="ps-video__caption">
      {% if caption %}
        {{ caption }}
      {% else %}
        {% if title %}<span class="ps-video__title">{{ title }}</span>{% endif %}
        {% if description %}<span class="ps-video__description">{{ description }}</span>{% endif %}
      {% endif %}
    </figcaption>
  {% endif %}
</figure>
```

---

## 🎨 Styles SCSS

```scss
.ps-video {
  &__media {
    position: relative;
    width: 100%;
  }

  &--16x9 &__media::before,
  &--4x3 &__media::before,
  &--1x1 &__media::before {
    content: "";
    display: block;
  }

  &--16x9 &__media::before { padding-top: 56.25%; }
  &--4x3 &__media::before { padding-top: 75%; }
  &--1x1 &__media::before { padding-top: 100%; }

  &__element,
  &__embed {
    position: absolute; inset: 0; width: 100%; height: 100%;
    border: 0; border-radius: var(--ps-border-radius-lg, 12px);
    box-shadow: var(--ps-shadow-sm, 0 1px 2px rgba(0,0,0,0.08));
    background: #000;
  }

  &--rounded .ps-video__element,
  &--rounded .ps-video__embed {
    border-radius: var(--ps-border-radius-lg, 12px);
  }

  &__caption {
    margin-top: var(--ps-spacing-2, 8px);
    color: var(--ps-color-neutral-800, #1F2A33);
    font-size: var(--ps-font-size-sm, 14px);
  }
}
```

---

## ♿ Accessibilité

- Toujours fournir `title` et/ou `caption` décrivant le contenu.
- Inclure pistes `track kind="captions"` pour HTML5 quand possible.
- Éviter l'autoplay non sollicité; si autoplay, combiner `muted` + `playsinline` et respecter préférences utilisateur.
- Fournir `title` sur iframes (YouTube/Vimeo).

---

## 📱 Responsive

- Ratio container maintient la responsivité (object-fit/iframe fill).
- Légende place en dessous, wraps naturellement.

---

## 🧪 Exemples d'usage

```twig
{# HTML5 #}
{% include '@ps_theme/ps-video/ps-video.twig' with {
  provider: 'html5',
  ratio: '16x9',
  videoAttrs: { controls: true, preload: 'metadata', poster: '/media/poster.jpg' },
  sources: [
    { src: '/media/movie.mp4', type: 'video/mp4' },
    { src: '/media/movie.webm', type: 'video/webm' }
  ],
  tracks: [
    { src: '/media/captions-fr.vtt', srclang: 'fr', label: 'Français', default: true }
  ],
  title: 'Visite du bien',
  description: 'Appartement 3 pièces - Paris'
} %}

{# YouTube #}
{% include '@ps_theme/ps-video/ps-video.twig' with {
  provider: 'youtube',
  embedId: 'dQw4w9WgXcQ',
  ratio: '16x9',
  title: 'Présentation vidéo'
} %}
```

---

## 📚 Ressources

- HTML Media: <video>, <track>
- YouTube Embed: player parameters
- Vimeo Embed: player parameters
