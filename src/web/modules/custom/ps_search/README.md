# ps_search 1.0.0

Module Drupal unique pour le domaine **Property Search** (BNPPRE) : moteur Solr, barre de filtres BNPPRE, carte, URLs SEO, API publique `/api/ps/*`, header/hero.

## Prérequis

- Drupal **11.3+**, PHP **8.3**, PostgreSQL, Solr 9 (stack Docker PS)
- Modules : `search_api`, `search_api_solr`, `facets`, `better_exposed_filters`, `views_load_more`, `ps_dictionary`, `ps_feature`, `ps_context`, `ps_offer`, …
- Clé Google Maps (carte recherche) : lue depuis `geofield_map.settings` (BO Geofield Map — module via `ps_offer`)
- Site local : `make up` → http://com.localhost:8080 (multisite `com`)

## Installation

Sur site neuf (config `config/install/` du module) :

```bash
make up
make drush-cr
make index-solr
```

Sur site existant (config runtime non présente dans CMI sync) :

```bash
# Feature sync settings, map zone, API limits — une fois si absent
make drush com php:eval "
\$source = new \Drupal\Core\Config\FileStorage('modules/custom/ps_search/config/install');
foreach (['ps_search.feature_filter_sync', 'ps_search.map_zone_settings', 'ps_search.api_rate_limit_settings', 'ps_search.api_cache_settings'] as \$name) {
  if (\Drupal::configFactory()->get(\$name)->isNew()) {
    \Drupal::configFactory()->getEditable(\$name)->setData(\$source->read(\$name))->save(TRUE);
    echo \"imported \$name\n\";
  }
}
"
make drush-cr
make drush com ps:search:features:sync -- --prune=0
make index-solr
```

## Administration

Hub : **`/admin/ps/config/search`** (permission `access ps_core config section`)

| Écran | Route |
|-------|--------|
| SEO URL mappings | `/admin/ps/config/search/seo-urls` |
| Map zone & markers | `/admin/ps/config/search/map-zone` |
| API rate limits & cache | `/admin/ps/config/search/api-limits` |
| Feature filter sync | `/admin/ps/config/search/feature-filters` |

## API publique (`/api/ps/*`)

| Endpoint | Rôle |
|----------|------|
| `GET /api/ps/markers` | Markers / clusters JSON (filtres URL + `map_bounds`) |
| `GET /api/ps/isochrone` | Géométrie zone distance |

### Geo zones (search v2 — M0)

| Commande | Description |
|----------|-------------|
| `drush ps:search:geo-zones:build {country}` | Régénère `data/geo_zones/{country}.yml` (fr, com, be, nl, …) |
| `drush ps:search:geo-zones:import {country}` | Importe un pays depuis YAML → config active |
| `drush ps:search:geo-zones:validate` | Valide toutes les configs `ps_search.geo_zones.*` |

Scripts CLI (sans site bootstrappé) :

```bash
php scripts/geo_zones/generate_fr_centroids.php   # centroids depuis geo.api.gouv.fr
php scripts/geo_zones/build_countries.php         # build tous les pays (ou: fr com be nl …)
php scripts/geo_zones/validate_all.php            # validate 9 pays YAML sources
```

Sources YAML (unique source de vérité, déployable avec `src/`) :
`src/web/modules/custom/ps_search/data/geo_zones/{country}.yml`

Les scripts sous `scripts/geo_zones/` ne génèrent que vers ce dossier — pas de copie parallèle.

Inventaire `data/geo_zones/` (9 pays multisite) :

| Fichier | Zones | Granularité |
|---------|------:|-------------|
| `fr.yml` | 96 | Départements FR |
| `com.yml` | 96 | Clone FR (`department.com.*`) — site international |
| `be.yml` | 11 | Provinces belges |
| `nl.yml` | 12 | Provinces néerlandaises |
| `es.yml` | 17 | Communautés autonomes |
| `it.yml` | 20 | Régions italiennes |
| `pl.yml` | 16 | Voïvodies |
| `ie.yml` | 26 | Comtés irlandais |
| `lu.yml` | 12 | Cantons luxembourgeois |
| `centroids/fr.departments.yml` | 96 | Centroids API geo.gouv.fr (build FR) |

Source métier :
- **FR** : dictionnaire `ps_dictionary` + centroids `centroids/fr.departments.yml` (via `GeoZoneDefinitionProvider`)
- **COM** : clone de `fr.yml` (`GeoZoneComBuilder`)
- **Autres pays** : `data/geo_zones/country_definitions.php` → `GeoZoneBuilder`

### SearchContext (search v2 — M1)

Pipeline : `SearchContextRequestSubscriber` (priority 34) → `SearchContextResolver` → request attribute `_ps_search_context`.

Feature flag : `ps_search.engine_settings.features.use_search_context` (défaut `false` — activer après validation M4).

### SearchQueryExecutor (search v2 — M2)

`SearchSpatialApplier` (bbox+postal, geofilt, viewport) + `SearchQueryExecutor` branchés via `SearchContextQueryHooks` sur la vue liste.

Legacy : `SearchQueryFactory` délègue à l'executor quand le feature flag est actif.

### SearchContextSerializer (search v2 — M3)

`SearchContextSerializer` : `buildSeoPath()`, `buildQueryParams()`, `toUrl()`, `toArray()`, `fromRequest()`.

Intégration SEO v2 (flag actif) :
- `SearchSeoPathProcessor` — inbound/outbound via GeoZone + serializer
- `SearchCanonicalRedirectSubscriber` — redirect flexible → SEO canonique
- `SearchSeoCanonicalUrlBuilder` — canonical href via serializer

Debug admin : `GET /api/ps/search-context` (permission `access ps_core config section`).

### search-context.js (search v2 — M4)

Store client `Drupal.psSearchContext` : `getState()`, `setGeo()`, `setFilter()`, `buildUrl()`, `buildApiParams()`, `apply()`, `syncFromUrl()`.

`drupalSettings.psSearch.searchContext` injecté serveur quand le flag est actif.  
`search-filter-bar.js` et `buildSearchParams()` délèguent au store ; legacy inchangé si flag off.

### SearchContextViewsQuery (search v2 — M5)

Plugin Views `ps_search_context` : applique `SearchQueryExecutor` directement (filtres BEF ignorés quand flag actif).  
Hooks `search_api_query_alter` court-circuités via `SearchViewsQueryGate`.

Config vue : `views.view.ps_search_offers` → `display.default.display_options.query.type: ps_search_context`.

### LocationResolver (search v2 — L3)

`LocationResolver` + providers `geo_zone`, `offers` (+ stubs `google`, `osm`).  
`GET /api/ps/location/resolve?q=…` — résolution GeoContext JSON.  
Suggest enrichi : `slug` + `id` GeoZone sur départements.

### SEO migration redirects (search v2 — M6)

Config `ps_search.seo_redirects` — map explicite de 301 (clean break v2).  
`SearchSeoRedirectsSubscriber` (priority 30) — après canonical (31), avant render.  
BO : `/admin/ps/config/search/seo-redirects`.

### Search alerts + presets (search v2 — M7)

Quand `use_search_context` est actif :

- `SearchAlertCriteriaSerializer` stocke un blob `schema_version: 2` + `context` (SearchContext complet via `SearchContextStorageMapper`)
- `SearchAlertMatcher` rejoue les critères v2 via request attribute `_ps_search_context`
- `SearchPresetQueryBuilder` construit les URLs preset via `SearchContextSerializer` (GeoZone slug)

Legacy (`schema_version: 1`) inchangé si flag off.

| `GET /api/ps/count` | Compteur résultats (filtres) |
| `GET /api/ps/location-suggest` | Autocomplete localisation |
| `GET /api/ps/location-data` | Métadonnées localités |
| `GET /api/ps/htmx/*` | Fragments HTMX barre filtres |

Rate limiting IP + validation params : voir config BO **API limits and cache**.  
Legacy `/ps-search*` supprimé (L10).

## Drush

```bash
make drush com ps:search:features:sync
make drush com ps:search:features:sync-index
```

Alias : `ps-sfs`, `ps-sfs-index`.

## Frontend

- **`drupalSettings.psSearch`** — URLs API, slugs SEO, compteurs zone/global, carte
- Libraries : `ps_search/filter.bar`, `ps_search/search-page*` (JS page recherche rapatrié depuis le thème L10)
- Thème : rendu Views (`ps_theme`), styles `_search-page.scss`, SDC header/hero

## Tests

```bash
# Depuis la racine du repo
make search-locality-seo-b2b   # Région / dept / ville — URLs SEO + APIs + chips
make search-b2b              # Suite B2B recherche complète

# Ou directement
bash web/modules/custom/ps_search/tests/b2b_locality_seo.sh
bash web/modules/custom/ps_search/tests/b2b_search_full.sh
bash web/modules/custom/ps_search/tests/e2e_seo_urls.sh
```

## Translations

Interface strings live in `translations/ps_search.{langcode}.po` (fr, de, es, it, nl, pl, lb).

Import after editing a `.po` file:

```bash
make drush com locale:import fr web/modules/custom/ps_search/translations/ps_search.fr.po -- --type=customized --override=all -y
make drush-cr
```

On fresh install, `src/scripts/drupal/install.sh` imports all `ps_*.*.po` files for active languages.

Validation manuelle navigateur : checklist §12 dans [`docs/SEARCH_L11_VALIDATION.md`](../../../../docs/SEARCH_L11_VALIDATION.md).

## Filtres et matrix Context

La visibilité des filtres **surface / capacité / budget** (barre recherche, hero homepage) est dérivée des règles actives `ps_context` via le service `ps_context.search_filter_visibility` (`SearchFilterVisibilityResolver`).

- Sans actif sélectionné : surface visible, capacité masquée
- Actif COW : capacité visible, surface masquée
- Libellés budget (loyer, €/m²/an, €/poste) : combinaison opération × actif

Scénarios recette : `ps_context/docs/RECETTE.md` (CTX-SEARCH-01 à 08).

## Architecture (résumé)

```
ps_search/
├── config/install/          # Solr, Views, SEO, map zone, …
├── src/
│   ├── Api/                 # ApiRoutePaths, RequestValidator, rate limit
│   ├── Search/Query/        # SearchQueryFactory (filtres métier)
│   ├── Search/Filter/       # Barre BNPPRE + HTMX
│   ├── Search/Header|Hero/  # Entrées header / homepage
│   ├── Controller/          # JSON + HTMX
│   ├── Hook/                # Views, map bounds, features, …
│   └── Service/             # Markers, isochrone, location, sync
├── js/                      # filter + page map stack
└── templates/               # Twig barre filtres
```

Décisions et dette : [`docs/SEARCH_SYSTEM_AUDIT.md`](../../../../docs/SEARCH_SYSTEM_AUDIT.md)  
Conception lots L0–L11 : [`docs/SEARCH_CONCEPTION.md`](../../../../docs/SEARCH_CONCEPTION.md)

## Lots de validation

| Lot | Doc |
|-----|-----|
| L0–L2 | `docs/SEARCH_L0_VALIDATION.md`, L2 |
| L3–L10 | `docs/SEARCH_L3_VALIDATION.md` … `SEARCH_L10_VALIDATION.md` |
| L11 | `docs/SEARCH_L11_VALIDATION.md` (checklist livraison v1) |
