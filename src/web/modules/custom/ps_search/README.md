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
- l'autocomplete géographique standalone (modules `ps_search_filters` / `ps_location_autocomplete` **absents de ce repo**).

## Fonctionnalités

- Server Solr `ps_solr` + index `offers` (datasource `node:offer`)
- Vue `ps_search_offers` avec facettes, Better Exposed Filters, carte Google Maps (`geofield_map`)
- **4 processors Search API custom** : surfaces, features flat, features par groupe, catégoriseur features
- **Feature filter sync** : cron + Drush pour aligner index fields et filtres exposés sur `fb_feature_definition`
- **SEO URL mappings** : form BO + path processors inbound/outbound + redirect canonique
- Routes legacy v1 redirigées (deprecated 2.0.0)

## Architecture

### Services

| Service ID | Classe | Rôle |
|---|---|---|
| `Drupal\ps_search\Service\FeatureFilterSyncManager` | `FeatureFilterSyncManager` | Sync champs index + filtres vue depuis catalogue features |
| `Drupal\ps_search\Hook\FeatureFilterCronHooks` | `FeatureFilterCronHooks` | Cron hook pour sync automatique |
| `ps_search.seo_path_processor` | `SearchSeoPathProcessor` | Path processor inbound/outbound pour URLs SEO |
| `ps_search.canonical_redirect_subscriber` | `SearchCanonicalRedirectSubscriber` | Redirect vers URL canonique de recherche |

### Entités

Aucune. Configuration exportable uniquement.

### Plugins Search API

| Plugin ID | Classe | Rôle |
|---|---|---|
| `ps_surface_processor` | `SurfaceProcessor` | Indexe surfaces qualifiées depuis `field_surfaces` |
| `ps_feature` | `FeatureProcessor` | Indexe features flat (par definition ID) |
| `ps_feature_by_group` | `FeatureByGroupProcessor` | Indexe features groupées par `fb_feature_group` |
| `ps_feature_categorizer` | `FeatureCategorizerProcessor` | Catégorise features pour facettes avancées |

### Commandes Drush

| Commande | Alias | Description |
|---|---|---|
| `ps:search:features:sync` | `ps-sfs` | Sync champs index + filtres exposés (`--prune=0\|1`) |
| `ps:search:features:sync-index` | `ps-sfsi` | Sync + indexation immédiate offers (`--rebuild-tracker=0\|1`) |

### Hooks OOP

| Classe | Hook | Rôle |
|---|---|---|
| `FeatureFilterCronHooks` | `cron` | Sync features si activé dans config |

## Routes & Accès

| Route | Chemin | Accès |
|---|---|---|
| `ps_search.seo_url_mappings_form` | `/admin/ps/config/search/seo-urls` | `administer site configuration` |
| Vue `ps_search_offers` (page) | `/find-property` (EN) / slugs traduits | Public (`access content`) |
| `ps_search.count` | `/ps-search/count` | Public — **deprecated**, redirect |
| `ps_search.location_suggest` | `/ps-search/location-suggest` | Public — **deprecated**, redirect |
| `ps_search.location_data` | `/ps-search/location-data` | Public — **deprecated**, redirect |

Menu : **SEO URL Mappings** sous `ps_core.config`.

## Permissions

Aucune permission propre au module. Configuration SEO : `administer site configuration`.

## Configuration initiale (`config/install/`)

| Fichier | Contenu |
|---|---|
| `search_api.server.ps_solr.yml` | Server Solr |
| `search_api.index.offers.yml` | Index offers (processors, champs) |
| `views.view.ps_search_offers.yml` | Vue recherche publique `/find-property` |
| `ps_search.feature_filter_sync.yml` | Settings sync features (cron, prune) |
| `ps_search.seo_url_mappings.yml` | Mappings URLs SEO (operation, asset, geo) |
| `config_translation.mapper.ps_search_seo_url_mappings.yml` | Mapper traduction SEO URLs |

## Tests

- E2E bash : `composer test:search-urls-e2e` (`tests/b2b_search_urls_security.sh`)
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

## Legacy v1 (archivé)

Le dossier `archived/` contient l'implémentation v1.x (barre de filtres monolithique). En v2.0.0, le code a été scindé vers :
- `ps_search_filters` — système de filtres modulaire
- `ps_location_autocomplete` — autocomplete géographique

**État repo :** ces deux modules ne sont **pas présents** dans `src/web/modules/custom/`. Les routes legacy redirigent vers leurs paths (`/ps-search-filters/*`, `/ps-location/*`) qui ne sont pas servis.

Voir `archived/README.md` pour le détail de la migration.

## Notes techniques

- Version module : **2.0.0**
- Itération 1 recherche BO (fallback) : vue `ps_offer_by_feature` dans `ps_offer` (DEC-0016)
- Décisions : DEC-0016, DEC-0017 dans `.ai/PROJECT_DECISIONS.md`
- Inventaire modules : `.ai/PROJECT_MODULES.md` §3.9

## À venir

- Implémenter ou retirer les routes legacy (modules filtres/autocomplete absents)
- Tests kernel/functional sur processors et sync features
- Gouvernance schema Solr (versioning, rollback) — voir `.ai/PROJECT_DECISIONS.md`
