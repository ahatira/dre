# Map View (Organism)

**Niveau Atomic Design** : Organism / Map  
**Catégorie** : Geospatial  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Wrapper carte pour intégrations Leaflet ou Google Maps. Affiche marqueurs de propriétés, contrôle de zoom, cluster optionnel, et panneau d'information. A11y: fournir alternatives (liste), gérer focus et annonces pour mises à jour.

---

## 🏗️ Structure BEM

```html
<section class="ps-map-view" aria-label="Carte des propriétés">
  <div class="ps-map-view__map" id="ps-map" role="application" aria-roledescription="map"></div>
  <div class="ps-map-view__panel" aria-live="polite" aria-atomic="true"></div>
</section>
```

### Classes BEM

```
ps-map-view                                // Block
  ps-map-view__map                         // Map container
  ps-map-view__panel                       // Info panel/live region

Modificateurs :
  ps-map-view--leaflet                     // Leaflet provider
  ps-map-view--google                      // Google Maps provider
  ps-map-view--cluster                     // Marker clustering
```

---

## 📐 Props (Component API)

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Map View'
status: stable
group: organisms
description: 'Map wrapper for Leaflet/Google Maps with markers and panel.'

props:
  type: object
  properties:
    provider: { type: string, enum: ['leaflet','google'], default: 'leaflet' }
    center:
      type: object
      properties: { lat: { type: number }, lng: { type: number } }
    zoom: { type: number, default: 12 }
    markers:
      type: array
      items:
        type: object
        properties:
          id: { type: string }
          lat: { type: number }
          lng: { type: number }
          title: { type: string }
          content: { type: string }
        required: ['id','lat','lng']
    cluster: { type: boolean, default: false }
    attributes:
      type: Drupal\\Core\\Template\\Attribute
```

---

## 🔧 Template Twig

```twig
{% set provider = provider|default('leaflet') %}
{% set classes = ['ps-map-view', 'ps-map-view--' ~ provider, cluster ? 'ps-map-view--cluster'] %}

<section {{ attributes.addClass(classes) }} aria-label="Carte des propriétés">
  <div class="ps-map-view__map" id="ps-map" role="application" aria-roledescription="map"></div>
  <div class="ps-map-view__panel" aria-live="polite" aria-atomic="true"></div>
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-map-view {
  &__map { width: 100%; height: clamp(var(--size-80), 50vh, var(--size-96)); border-radius: var(--radius-5); overflow: hidden; }
  &__panel { margin-top: var(--size-3); font-size: var(--font-size-0); color: var(--text-secondary); }
  @media (max-width: var(--size-tablet)) { &__map { height: var(--size-80); } }
}
```

---

## ♿ Accessibilité

- Fournir liste des propriétés comme alternative.
- Live region pour annonces (ex: "3 propriétés dans la zone").
- Gérer focus vers panel lors de mises à jour.

---

## 🔌 JavaScript (exemple Leaflet)

```js
// Requires Leaflet loaded
(function(){
  const el = document.getElementById('ps-map');
  if(!el || typeof L === 'undefined') return;
  const map = L.map(el).setView([{{ center.lat|default(48.8566) }}, {{ center.lng|default(2.3522) }}], {{ zoom|default(12) }});
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
  const group = L.layerGroup().addTo(map);
  let clusterLayer = null;
  const panel = document.querySelector('.ps-map-view__panel');
  let count = 0;
  {% for m in markers %}
    L.marker([{{ m.lat }}, {{ m.lng }}]).addTo(group).bindPopup(`{{ m.title|e }}\n{{ m.content|e }}`);
    count++;
  {% endfor %}
  panel.textContent = `${count} propriétés dans la zone`;
  panel.focus && panel.focus();

  // Optional clustering if plugin available and prop enabled
  try {
    if ({{ cluster ? 'true' : 'false' }} && L.markerClusterGroup) {
      clusterLayer = L.markerClusterGroup();
      group.eachLayer((mk)=> clusterLayer.addLayer(mk));
      group.clearLayers();
      map.addLayer(clusterLayer);
      panel.textContent = `${count} propriétés (regroupées) dans la zone`;
    }
  } catch(e) { /* silently ignore if plugin missing */ }
})();
```

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-map-view/ps-map-view.twig' with {
  provider: 'leaflet',
  center: { lat: 48.8566, lng: 2.3522 },
  zoom: 12,
  markers: [
    { id: 'p1', lat: 48.86, lng: 2.35, title: 'Appartement', content: '2 pièces' },
    { id: 'p2', lat: 48.85, lng: 2.34, title: 'Maison', content: '4 pièces' }
  ]
} %}
```

---

## 📚 Ressources

- Leaflet docs, Google Maps docs
- A11y for dynamic content/live regions
