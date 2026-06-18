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
cd src
composer test:search-filter-htmx-count-e2e
composer test:search-more-filters-e2e
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
