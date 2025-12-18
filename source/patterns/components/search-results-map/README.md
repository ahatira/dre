# Search Results Map Component

**Drupal-native map component** for property search results with price markers, clustering, and radius visualization.

## Architecture

- **Drupal.behaviors** pattern with lifecycle (attach/detach)
- **drupalSettings** configuration (priority) with data-attributes fallback
- **Theming in JavaScript** (`Drupal.theme.mapMarker`) for overridable marker rendering
- **Views integration** ready (viewId, displayId, contextualFilters hooks)
- **Google Maps API** v3 with AdvancedMarkerElement (HTML markers)

##Files
- `search-results-map.twig` - Drupal template with data-attributes
- `search-results-map.js` - Main behavior with drupalSettings support
- `map-marker.js` - Theming layer for marker generation (`Drupal.theme`)
- `search-results-map.css` - BEM styling with 3-layer CSS variables
- `search-results-map.yml` - Storybook mock data

---

## Usage: Drupal (via drupalSettings)

### 1. Preprocess function

```php
<?php
/**
 * Implements hook_preprocess_HOOK() for search-results-map.html.twig.
 */
function mytheme_preprocess_search_results_map(&$variables) {
  // Attach library
  $variables['#attached']['library'][] = 'ps_theme/search-results-map';

  // Pass configuration via drupalSettings
  $map_id = 'search_page'; // Unique map identifier
  $variables['#attached']['drupalSettings']['psSearchResultsMap'][$map_id] = [
    'lat' => 48.862,
    'lng' => 2.315,
    'zoom' => 13,
    'provider' => 'google',
    'mapId' => 'YOUR_GOOGLE_MAP_ID', // Required for AdvancedMarkerElement
    'results' => $variables['results'], // From View or custom query
    'showRadius' => TRUE,
    'radiusMeters' => 1200,
    'cluster' => TRUE,
    'autoFit' => TRUE, // Auto-fit bounds to include all markers
    'selectedId' => $variables['selected_id'] ?? NULL,
    // Views integration
    'viewId' => 'property_search',
    'displayId' => 'map_display',
    'contextualFilters' => [$variables['location_filter']],
  ];

  // Set map ID attribute for JavaScript to read drupalSettings
  $variables['attributes']['data-map-id'] = $map_id;
}
```

### 2. Template usage

```twig
{# In your custom template #}
{% include '@components/search-results-map/search-results-map.twig' with {
  attributes: attributes,
  map: { lat: 48.862, lng: 2.315, zoom: 13 },
  results: [], {# Empty, will be populated from drupalSettings #}
} only %}
```

### 3. Results data structure

```php
$results = [
  [
    'id' => 'property_1',
    'lat' => 48.8678,
    'lng' => 2.3216,
    'price' => 340,
    'currency' => 'EUR',
    'selected' => FALSE,
  ],
  [
    'id' => 'property_2',
    'lat' => 48.8592,
    'lng' => 2.3267,
    'price' => NULL, // NC marker (gray)
    'currency' => 'EUR',
  ],
  // ...
];
```

---

## Usage: Views Integration

### 1. Create custom Views field plugin

```php
<?php
namespace Drupal\mymodule\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * @ViewsField("property_map_marker")
 */
class PropertyMapMarker extends FieldPluginBase {
  public function render(ResultRow $values) {
    /** @var \Drupal\node\Entity\Node $node */
    $node = $this->getEntity($values);

    return [
      'id' => 'node_' . $node->id(),
      'lat' => (float) $node->get('field_latitude')->value,
      'lng' => (float) $node->get('field_longitude')->value,
      'price' => $node->get('field_price')->value,
      'currency' => $node->get('field_currency')->value ?? 'EUR',
      'selected' => FALSE,
    ];
  }
}
```

### 2. Views attachment in preprocess

```php
function mytheme_preprocess_views_view__property_search__map(&$variables) {
  $view = $variables['view'];
  $results = [];

  foreach ($view->result as $row) {
    $results[] = $view->field['property_map_marker']->render($row);
  }

  $variables['#attached']['drupalSettings']['psSearchResultsMap']['view_map'] = [
    'results' => $results,
    'viewId' => $view->id(),
    'displayId' => $view->current_display,
    'autoFit' => TRUE,
    'cluster' => TRUE,
    'showRadius' => FALSE,
  ];
}
```

---

## Theming: Override Marker Rendering

### Custom marker theme

```javascript
// In your custom theme's JS file
((Drupal) => {
  // Override default marker theme
  const originalMarker = Drupal.theme.mapMarker;

  Drupal.theme.mapMarker = function (data) {
    // Add custom logic for specific marker types
    if (data.type === 'premium') {
      const wrapper = document.createElement('div');
      wrapper.className = 'custom-premium-marker';
      wrapper.innerHTML = `
        <div class="premium-badge">
          <span class="premium-icon">⭐</span>
          <span>${data.price} ${Drupal.theme.getCurrencySymbol(data.currency)}</span>
        </div>
      `;
      return wrapper;
    }

    // Fallback to original theme
    return originalMarker.call(this, data);
  };
})(Drupal);
```

---

## Configuration Options

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `lat` | number | 48.8723 | Map center latitude |
| `lng` | number | 2.3035 | Map center longitude |
| `zoom` | number | 13 | Initial zoom level |
| `provider` | string | `'google'` | Map provider (currently Google only) |
| `mapId` | string | `'DEMO_MAP_ID'` | Google Maps ID (required for AdvancedMarkerElement) |
| `results` | array | `[]` | Array of search results with coordinates |
| `showRadius` | boolean | `false` | Display distance circle |
| `radiusMeters` | number | 1200 | Radius of distance circle in meters |
| `cluster` | boolean | `true` | Enable marker clustering |
| `autoFit` | boolean | `true` | Auto-fit map bounds to include all markers |
| `selectedId` | string\|null | `null` | ID of selected result (highlighted) |
| `viewId` | string\|null | `null` | Views ID for integration |
| `displayId` | string\|null | `null` | Views display ID |
| `contextualFilters` | array | `[]` | Contextual filter values |

---

## Events

The component emits custom events for integration:

### `map:initialized`

```javascript
document.addEventListener('map:initialized', (e) => {
  console.log('Map initialized:', e.detail);
  // e.detail = { map, results, config }
});
```

### `result:click`

```javascript
canvas.addEventListener('result:click', (e) => {
  console.log('Marker clicked:', e.detail);
  // e.detail = { id, result }
  // Redirect to property page or open modal
});
```

### `bounds:changed`

```javascript
canvas.addEventListener('bounds:changed', (e) => {
  console.log('Map bounds changed:', e.detail);
  // e.detail = { bounds: { north, east, south, west }, zoom, center }
  // Update Views contextual filters or reload results
});
```

---

## Fallback: Data-Attributes Only

If drupalSettings is not available, the component reads from data-attributes:

```twig
<div class="ps-map">
  <div
    class="ps-map__canvas"
    data-map-id="fallback_map"
    data-lat="48.862"
    data-lng="2.315"
    data-zoom="13"
    data-provider="google"
    data-results='[{"id":"1","lat":48.8678,"lng":2.3216,"price":340}]'
    data-show-radius="true"
    data-radius-meters="1200"
    data-cluster="true"
    data-selected-id="1"
  ></div>
</div>
```

---

## Accessibility

- `role="region"` with `aria-label="Search results map"`
- Keyboard-accessible controls (fullscreen, zoom)
- ARIA labels on markers via `title` attribute
- Screen reader announcement on bounds change (via bounds:changed event)

---

## Dependencies

- **Drupal core**: `core/drupal`, `core/drupalSettings`, `core/once`
- **Google Maps API**: Must be loaded externally (not bundled)
- **Marker Clusterer**: Optional for clustering (`@googlemaps/markerclusterer`)

See `ps_theme.libraries.yml` for complete dependency list.

---

## Performance

- **Lazy loading**: Only initializes when element enters viewport (via `once()`)
- **Cleanup**: Proper detach() lifecycle removes listeners on view unload
- **Clustering**: Reduces marker count for large datasets
- **Auto-fit**: Optional (disable via `autoFit: false` for static zoom)

---

## Troubleshooting

### Markers not appearing

1. Check browser console for JavaScript errors
2. Verify Google Maps API is loaded: `typeof google !== 'undefined'`
3. Confirm `mapId` is valid (required for AdvancedMarkerElement)
4. Check results data structure (lat/lng must be numbers, not strings)

### drupalSettings not working

1. Verify library attachment: `$variables['#attached']['library'][] = 'ps_theme/search-results-map';`
2. Check drupalSettings structure: `drupalSettings.psSearchResultsMap.{mapId}`
3. Ensure `data-map-id` attribute matches settings key

### Views integration issues

1. Confirm Views field plugin returns correct data structure
2. Verify preprocess function attaches settings before render
3. Check contextual filters are passed correctly

---

## Examples

See `search-results-map.stories.jsx` for interactive examples in Storybook:
- Default (data-attributes)
- With Radius
- With Clustering
- Dense Results (80+ markers)
- Mockup: Default (mirrors BNP Paribas mockup)
