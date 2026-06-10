# Module Property Search - Search (`ps_search`)

> Statut : 🟡 En développement

Orchestration Search API + Solr pour la recherche d'offres immobilières : index, processors métier, vue publique, sync dynamique des filtres features, SEO URLs.

## Responsabilité

`ps_search` est le point d'ancrage du domaine recherche (DEC-0017). Il livre :
- le server/index Solr `offers` ;
- la vue publique `ps_search_offers` (route machine `/find-property`, slugs publics i18n) ;
- les processors Search API (surfaces, features) ;
- la synchronisation dynamique des champs/filtres features depuis le catalogue `ps_feature` ;
- le traitement SEO des URLs de recherche (mappings + redirect canonique).

Ce module **ne fait pas** :
- le type de contenu offre (`ps_offer`) ;
- le catalogue features (`ps_feature`, consommé en runtime) ;
- la barre de filtres UI v1 (archivée — voir section « Legacy ») ;
- l'autocomplete géographique standalone (endpoint `/ps-search/location-*` servi par ce module ; module `ps_location_autocomplete` **non présent** — voir Phase 1+ refactor).

## Fonctionnalités

- Server Solr `ps_solr` + index `offers` (datasource `node:offer`)
- Vue `ps_search_offers` avec facettes, Better Exposed Filters, carte Google Maps (`geofield_map`)
- **4 processors Search API custom** : surfaces, features flat (`ps_feature`), champs dérivés More-filters core (`ps_feature_categorizer`), geo lat/lng
- **Feature filter sync** : cron + Drush pour aligner index fields et filtres exposés sur `fb_feature_definition`
- **SEO URL mappings** : form BO + path processors inbound/outbound + redirect canonique
- Routes legacy v1 redirigées (deprecated 2.0.0)

## Architecture

### Services

| Service ID | Classe | Rôle |
|---|---|---|
| `Drupal\ps_search\Service\FeatureFilterSyncManager` | `FeatureFilterSyncManager` | Sync champs index + filtres vue depuis catalogue features |
| `ps_search.map_bounds_resolver` | `MapBoundsResolver` | Bounds actives (URL, défaut, auto-fit résultats) |
| `ps_search.result_geo_bounds_resolver` | `SearchResultGeoBoundsResolver` | Comptages zone/global + bbox résultats (Search API) |
| `ps_search.filter_query_builder` | `SearchFilterQueryBuilder` | Filtres métier URL → requête Search API |
| `ps_search.result_counter` | `SearchResultCounter` | Compteurs zone/global pour liste, markers, drupalSettings |
| `ps_search.markers_builder` | `SearchMarkersBuilder` | Payload JSON `/ps-search/markers` |
| `ps_search.location_centroid` | `LocationCentroidResolver` | Centroïde localité → zone carte par défaut |
| `Drupal\ps_search\Hook\FeatureFilterCronHooks` | `FeatureFilterCronHooks` | Cron hook pour sync automatique |
| `Drupal\ps_search\Hook\MapBoundsQueryHooks` | `MapBoundsQueryHooks` | Filtre map_bounds sur requête Views liste |
| `Drupal\ps_search\Hook\SearchListPagerHooks` | `SearchListPagerHooks` | listLoadAll quand zone ≤ seuil |
| `Drupal\ps_search\Hook\SearchLocationHooks` | `SearchLocationHooks` | drupalSettings carte (bounds, counts, autoFit) |
| `ps_search.seo_path_processor` | `SearchSeoPathProcessor` | Path processor inbound/outbound pour URLs SEO |
| `ps_search.canonical_redirect_subscriber` | `SearchCanonicalRedirectSubscriber` | Redirect vers URL canonique de recherche |

### Entités

Aucune. Configuration exportable uniquement.

### Plugins Search API

| Plugin ID | Classe | Rôle |
|---|---|---|
| `ps_surface_processor` | `SurfaceProcessor` | Indexe surfaces qualifiées depuis `field_surfaces` |
| `ps_feature` | `FeatureProcessor` | Indexe features flat (par definition ID) |
| `ps_feature_categorizer` | `FeatureCategorizerProcessor` | Champs dérivés More-filters core (transport, plafond, visite 360°, vidéo) |

### Commandes Drush

| Commande | Alias | Description |
|---|---|---|
| `ps:search:features:sync` | `ps-sfs` | Sync champs index + filtres exposés (`--prune=0\|1`) |
| `ps:search:features:sync-index` | `ps-sfsi` | Sync + index immédiat offers |

### Hooks OOP

| Classe | Hook | Rôle |
|---|---|---|
| `FeatureFilterCronHooks` | `cron` | Sync features si activé dans config |

## Routes & Accès

| Route | Chemin | Accès |
|---|---|---|
| `ps_search.seo_url_mappings_form` | `/admin/ps/config/search/seo-urls` | `administer site configuration` |
| Vue `ps_search_offers` (page) | `/find-property` (EN) / slugs traduits | Public (`access content`) |
| `ps_search.count` | `/ps-search/count` | Public — compteur temps réel barre filtres |
| `ps_search.location_suggest` | `/ps-search/location-suggest` | Public — autocomplete localisation (SQL offer-driven) |
| `ps_search.isochrone` | `/ps-search/isochrone` | Public — géométrie zone distance + `map_bounds` |
| `ps_search.location_data` | `/ps-search/location-data` | Public — métadonnées tokens localisation |
| `ps_search.markers` | `/ps-search/markers` | Public — markers carte zone |

Menu : **SEO URL Mappings** sous `ps_core.config`.

## Gouvernance BO — configurabilité et traduction

**Principe PS** : toute valeur métier visible ou routable (slug, alias, label, visibilité filtre) doit être **administrable** et **traduisible via le BO**, pas codée en dur en PHP/JS. Le code custom ne fait que **lire** la config et appliquer la logique (path processors, redirects, comptage Solr).

### Où configurer quoi

| Besoin | BO / mécanisme | Config / module |
|--------|----------------|-----------------|
| Slug page recherche flexible (`find-property`, `recherche-immobiliere`…) | `/admin/ps/config/search/seo-urls` + **Configuration translation** | `ps_search.seo_url_mappings` → `search_path` |
| Slugs SEO opération (`for-rent`, `a-louer`…) | Idem | `operation_types` |
| Slugs SEO asset (`office`, `bureaux`…) | Idem | `asset_types` |
| Alias legacy asset (`bureau` → `bureaux`) | Idem — champ **Legacy asset slug aliases** (traduisible par langue) | `asset_slug_aliases` |
| Labels types opération / asset (cartes filtres) | Dictionnaire PS | `ps_dictionary` (`operation_type`, `asset_type`) |
| Noms départements FR (recherche localisation) | Dictionnaire PS | `ps_dictionary` (`department`, import CSV) |
| Visibilité surface / capacité par asset | Matrice contexte | `ps_context` |
| Chaînes UI barre filtres | Traduction interface | `ps_search_filters` `.po` |

### Route machine vs slug public

- **Route Drupal (Views)** : toujours le slug **machine** de la config de base (`find-property`) — jamais traduit.
- **URL publique** : slug par langue via override config (`language.fr:ps_search.seo_url_mappings`, etc.).
- **Liens internes** : `internal:/find-property?…` ou `SearchPathResolver::getPublicPath()` — pas `internal:/recherche` ni slug traduit en dur.

### Workflow développeur

1. Valeur par défaut → `config/install/` (+ `config/install/language/{lang}/` si i18n).
2. Formulaire BO → `SeoUrlMappingsForm`, `MapZoneSettingsForm` (étendre si nouveau champ config).
3. Schema → `config/schema/ps_search.schema.yml`.
4. PHP/JS → lire via `ConfigFactory`, `LanguageConfigFactoryOverride`, ou services dédiés — **pas de tableaux constants métier**.
5. Export après changement UI : `drush cex -y` (aligner `config/install/` pour les installs fraîches).
6. Environnements existants : `drush cim -y` ou `make db-reset` — pas de `hook_update_N()` dans ce module.

### Exceptions acceptées (temporaires)

| Exception | Raison | Action future |
|-----------|--------|---------------|
| Redirect 301 `/recherche` | Bookmarks pré-migration | Retirer quand analytics OK |
| Contenu démo (`ps_demo` menus YAML) | Seed install | Menus éditables en prod via Structure → Menus |

Voir aussi la règle Cursor `.cursor/rules/search-config-bo.mdc`.

## Permissions

Aucune permission propre au module. Configuration SEO : `administer site configuration`.

## Configuration initiale (`config/install/`)

| Fichier | Contenu |
|---|---|
| `search_api.server.ps_solr.yml` | Server Solr |
| `search_api.index.offers.yml` | Index offers (processors, champs) |
| `views.view.ps_search_offers.yml` | Vue recherche publique `/find-property` (liste load more 40/page, carte, block_offer_card) |
| `ps_search.map_zone_settings.yml` | Zone carte (centre, rayon, seuil listLoadAll, markers, isochrone) |
| `ps_search.feature_filter_sync.yml` | Settings sync features (cron, prune) |
| `ps_search.seo_url_mappings.yml` | Mappings URLs SEO (operation, asset, geo) |
| `config_translation.mapper.ps_search_seo_url_mappings.yml` | Mapper traduction SEO URLs |

## Tests

- E2E bash : `composer test:search-urls-e2e` (`tests/b2b_search_urls_security.sh`)
- Budget / prix : `composer test:search-budget-e2e` (`tests/b2b_budget_filter.sh`, `tests/b2b_budget_apply.sh`)
- Traductions UI filtres : `web/modules/custom/ps_search_filters/translations/*.po` — import :
  `docker exec ps_php sh -lc 'cd /var/www/html && vendor/bin/drush locale:import fr /var/www/html/web/modules/custom/ps_search_filters/translations/ps_search_filters.fr.po --type=customized --override=all -y'`
- Behat : suite `ps_search` (`tests/behat/features/search_paths.feature`)

## Dépendances

### Modules PS

- `ps_offer:ps_offer` — Datasource index (`node:offer`)

### Contrib

- `search_api:search_api` — Framework indexation
- `search_api_solr:search_api_solr` — Backend Solr
- `facets:facets` — Facettes
- `facets:facets_exposed_filters` — Filtres facettes exposés
- `better_exposed_filters:better_exposed_filters` — UX filtres Views
- `geofield_map:geofield_map` — Carte Google Maps
- `drupal:views` — UI recherche
- `drupal:config_translation` — Traduction config SEO

### Couplage runtime (sans dépendance `.info.yml`)

- `ps_feature` — Catalogue `fb_feature_definition` / `fb_feature_group` pour processors et sync
- `ps_surface` — Indirect via `field_surfaces` + `ps_surface_processor`

## Installation & reset

```bash
# Prérequis : Solr opérationnel, ps_offer activé
drush pm:enable ps_search -y
drush cr
drush search-api:reset-tracker offers
drush search-api:index offers

# Sync filtres features après ajout de définitions
drush ps:search:features:sync
# ou sync + index immédiat
drush ps:search:features:sync-index
```

## Migration v1 → v2

En v2.0.0, la barre de filtres monolithique a été extraite vers le module `ps_search_filters`. Le code v1 archivé a été **supprimé** (Phase 0 refactor, 2026).

Les endpoints JSON (`/ps-search/count`, `/ps-search/location-suggest`, `/ps-search/location-data`) restent dans `ps_search` et sont consommés par `ps_search_filters`.

### Phase 3 — Géographie ✅ (clôturée)

| Sujet | Décision | Détail |
|-------|----------|--------|
| Autocomplete localisation | **SQL** (`LocationSuggestBuilder`) | POC Solr retiré ; pas de `search_api_autocomplete` |
| Zone carte | **`map_bounds`** (bbox) | Filtre Search API lat/lng |
| Zone distance | **`/ps-search/isochrone`** | Provider **Google Routes** (défaut) + fallback **approximation** si Routes API inactive (payant) ; ORS optionnel (`ors_enabled`) |

**Config BO** : `/admin/ps/search/map-zone`

**Prérequis Google isochrone — activer Routes API (GCP)**

1. [Google Cloud Console](https://console.cloud.google.com/) → projet de la clé Geofield Map
2. **APIs & Services → Library** → chercher **Routes API** → **Enable**
3. **APIs & Services → Enabled APIs** : vérifier **Maps JavaScript API** + **Routes API**
4. **APIs & Services → Credentials** → clé utilisée par `geofield_map.settings:gmap_api_key` :
   - Restrictions API : inclure **Routes API** (ou pas de restriction API en dev)
5. Attendre ~1 min, puis vérifier :
   ```bash
   curl -s "http://localhost:8080/ps-search/isochrone?lat=48.8566&lng=2.3522&transport=walking&minutes=5" \
     | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('provider'), d.get('fallback'))"
   ```
   Attendu : `google_routes` sans `fallback: true`. Tant que Routes API est bloquée (HTTP 403), le fallback approximation reste actif.

**Tests** : `composer test:search-isochrone-e2e`, `composer test:search-zone-e2e`

### Phase 4 — Markers

#### 4.1 Audit ✅ (clôturée)

**Décisions**

| Action | Détail |
|--------|--------|
| Source markers | **Uniquement** `/ps-search/markers` + `search-page-map-markers.js` |
| Coque carte | Views `map_attachment` + geofield_map (`empty_behaviour: 1`) |
| `GeofieldMapHooks` | Réduit au **cluster/center** — carte vide via config Views (`empty_behaviour`) |
| Display `block_map` | **Absent** de `config/install` (POC AJAX abandonné) |

**Flux JS (`.ps-search-view`)**

```
1. geofield_map rend coque Google (sans features)
2. search-page-map-markers.js → GET /ps-search/markers?{filtres URL}
3. search-page-map-utils.js    → icônes prix, index markersByNid, cluster
4. search-page-map-sync.js     → hover/click carte ↔ liste
5. search-page-map-bounds.js   → pan/zoom → map_bounds URL (optionnel)
6. search-page-map-zone.js     → isochrone → map_bounds zone distance
7. search-page-zone-reload.js  → reloadZoneSearch(liste AJAX + markers)
```

**Événements custom** : `ps-search-map-markers-loaded`, `ps-search-map-marker-select`, `ps-search-distance-zone-updated`

#### 4.2 Perf markers ✅ (clôturée)

| Changement | Détail |
|------------|--------|
| Labels depuis index | `field_budget_value` + `field_budget_currency` (Solr) — plus de `loadMultiple()` |
| Cache interne | Bin `cache.default`, clé = empreinte query + `markers_max`, TTL 60 s |
| Invalidation | Tags `search_api_list:offers`, `config:ps_search.map_zone_settings` |
| HTTP | `CacheableJsonResponse` + contexte `url.query_args` |
| Index | Champ `field_budget_currency` dans `search_api.index.offers` (labels markers Solr) |

#### 4.3 Sync carte / isochrone ✅ (clôturée)

| Changement | Détail |
|------------|--------|
| Centre isochrone JS | Fallback : marker sélectionné → premier marker → centre carte |
| E2E sync | `composer test:search-map-isochrone-sync-e2e` |
| Behat | Suite `ps_search` — `map_isochrone.feature` |
| Test zone | `b2b_map_zone.sh` vérifie `zoneCount` drupalSettings pour grandes zones |

**Tests** : `composer test:search-map-isochrone-sync-e2e`, `composer test:behat -- --suite=ps_search`

#### 4.4 Cluster grille server-side ✅ (clôturée)

Quand `zone_count > markers_max` (500 par défaut), l’API bascule en `display_mode: clusters` au lieu de tronquer silencieusement à 500 markers individuels.

| Changement | Détail |
|------------|--------|
| Agrégation PHP | `SearchMarkersGridClusterBuilder` — grille lat/lng (~64 cellules) |
| API | Payload `{ display_mode, markers, clusters, capped, zone_count }` |
| Config | `markers_cluster_enabled`, `markers_cluster_cells` dans `ps_search.map_zone_settings` |
| JS | Icônes count (`buildClusterMarkerIcon`) — clic cluster → `reloadZoneSearch(map_bounds)` |
| Client cluster | MarkerClusterer reste pour markers individuels ; clusters serveur sans double agrégation |

**Tests** : `composer test:search-markers-cluster-e2e`, Behat `map_markers_cluster.feature`

#### 4.5 Auto-fit carte / liste ✅

Au premier chargement sans `map_bounds` explicite, si la zone par défaut ne couvre pas tous les résultats filtrés, `MapBoundsResolver` élargit la bbox pour englober les offres géolocalisées (`SearchResultGeoBoundsResolver`).

| Élément | Détail |
|---------|--------|
| Serveur | `autoFitToResults`, `mapBounds.autoFit` dans `drupalSettings` (`SearchLocationHooks`) |
| Client | `fitMapToZone()` dans `search-page-map-markers.js` |
| Liste | `list_pager_threshold` (100) — au-delà : load more **40/page** (`views_load_more`) ; en dessous : `listLoadAll` |

**Tests** : `composer test:search-sync-e2e`, `composer test:search-zone-e2e`

#### Fichiers clés

| Fichier | Rôle |
|---------|------|
| `SearchMarkersBuilder.php` | Requête Search API + payload JSON |
| `SearchMarkersGridClusterBuilder.php` | Grille server-side pour zones denses |
| `SearchMapMarkerBuilder.php` | Labels / SVG prix (API markers) |
| `GeofieldMapHooks.php` | Cluster geofield sur coque carte |
| `search-page-map-markers.js` | Rendu client markers + cluster |
| `views.view.ps_search_offers` → `map_attachment` | Style geofield_map, carte vide |

- Version module : **2.0.0**
- Itération 1 recherche BO (fallback) : vue `ps_offer_by_feature` dans `ps_offer` (DEC-0016)
- Décisions : DEC-0016, DEC-0017 dans `.ai/PROJECT_DECISIONS.md`
- Inventaire modules : `.ai/PROJECT_MODULES.md` §3.9

## À venir

- Tests kernel/functional sur processors et sync features
- Gouvernance schema Solr (versioning, rollback) — voir `.ai/PROJECT_DECISIONS.md`
