# ps_search Frontend Search Behaviors

## Current Scope

The search behavior loaded by `ps_search/offer_search_tracking` currently handles:

- card viewed-state tracking
- comparator toggle behavior
- card/map hover synchronization
- filter panel interactions and submit flow
- async preview count for `Show X results`
- location multi-value autocomplete UI

Current file split:

- `js/offer-search-tracking.js`: main orchestrator (cards, map, panels, CTA state)
- `js/search-location-autocomplete.js`: dedicated location autocomplete/chips module

## Inter-Module Communication (Current)

The script uses a single Drupal behavior (`psSearchCardSearchTracking`) and communicates through:

- `drupalSettings.psSearch` (runtime endpoints/settings)
- hidden form fields used as transport (`location_multi`)
- shared CTA state on the exposed form (`form.__ps*` runtime fields)

## Proposed Split (Next Increment)

To improve maintainability, split into focused files loaded by one library:

1. `search-state.js`
- shared CTA loading/count state
- preview request orchestration

2. `search-panels.js`
- panel open/close and trigger summaries

3. `search-location-autocomplete.js` (already extracted)
- location chips + autocomplete endpoint integration
- transport field sync (`location_multi`)

4. `search-card-map.js`
- map marker/card hover + cluster styling

5. `search-card-interactions.js`
- viewed cards + comparator actions

## Integration Contract

Each module should expose one initializer under a shared namespace object, for example:

- `window.psSearchUi.state.init(form)`
- `window.psSearchUi.panels.init(form)`
- `window.psSearchUi.location.init(form)`

Guidelines:

- modules must not directly mutate each other's internals
- cross-module data should go through explicit helpers on `state`
- endpoint/config access should only read from `drupalSettings.psSearch`

Current integration call in main orchestrator:

- `window.psSearchUi.location.init({ panel, form, requestResultsCountPreview, updatePanelTriggerLabel })`

## Backend Contract for Location Autocomplete

- endpoint: route `ps_search.location_autocomplete`
- response shape:

```json
{
  "items": [
    { "value": "Madrid", "label": "Madrid" }
  ]
}
```

- search transport for multi-values: query parameter `location_multi` with `||` delimiter
- request-time filtering is applied in `SearchHooks::viewsQueryAlter()`
