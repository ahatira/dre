# Phase 5A — Refactor barre filtres (SDC + HTMX)

> Statut : **5A.9 clôturé** — CSS migré vers SDC `ps_theme` ; Phase 5A barre filtres complète  
> Module : `ps_search_filters` — design BNPPRE conservé  
> Phase audit parente : **Phase 5 UI** (piste A, pas BEF visible)

## Objectif

Réduire le monolithe `search-filter-bar.js` (~2000 lignes) **sans régression UX** :
- morceaux en **SDC** (`ps_theme` / `ui_suite_bnp`) ;
- interactions serveur via **HTMX core 11.3** (`core/drupal.htmx`) ;
- BEF + Search API restent le **moteur** (formulaire exposé masqué).

**Hors scope** : carte (`ps_theme/search-page-*`), SEO paths, index Solr, remplacement par BEF visible.

## État actuel (baseline)

| Fichier | Rôle | Lignes |
|---------|------|--------|
| `js/search-filter-bar.js` | State, count, apply, SEO URL, popins | ~1987 |
| `css/search-filter-bar.css` | Figma Frame 41 desktop | ~1392 |
| `css/search-filters-mobile.css` | Offcanvas mobile | ~185 |
| `FilterBarBuilder.php` | Render barre + drupalSettings | ~240 |
| `SearchResultsHeaderBuilder.php` | H1, tris, counts | ~329 |
| `MoreCriteriaGroupController` | Lazy-load JSON `{ html }` | proto-fragment |

## Lots planifiés

| Lot | Scope | Livrable | Statut |
|-----|--------|----------|--------|
| **5A.0** | Cadrage + POC | Ce doc + count HTMX popin **Type** | ✅ POC |
| **5A.1** | Infra | Service état filtres, lib HTMX partagée, events | ✅ |
| **5A.2** | Type + opération | SDC `search-filter-type`, apply HTMX | ✅ |
| **5A.3** | Localisation | SDC `search-filter-location` (chips + suggest) | ✅ |
| **5A.4** | Surface / budget / capacité | SDC `search-filter-range` | ✅ |
| **5A.5** | More filters | Remplacer JSON lazy-load par routes `_htmx_route` | ✅ |
| **5A.6** | Mobile | Offcanvas = composition SDC desktop | ✅ |
| **5A.7** | Header résultats | Partial HTMX ou PHP statique | ✅ |
| **5A.8** | Nettoyage | Retrait fetch JSON count legacy, E2E | ✅ |
| **5A.9** | CSS SDC | Styles composants → `ps_theme/components/search-filter-*` | ✅ |

Estimation globale : **3–6 semaines** (validation navigateur à chaque lot).

## POC 5A.0 — Count HTMX (popin Type)

### Pattern validé

1. Route `_htmx_route: TRUE` → fragment HTML sans chrome page.
2. Le JS existant construit les query params (`buildCountParams()`).
3. `htmx.ajax('GET', url, { target, swap: 'innerHTML' })` met à jour **un seul** `#ps-filter-type-count-label`.
4. Les autres popins gardent `/ps-search/count` (JSON) jusqu’aux lots suivants.

### Fichiers POC

| Fichier | Rôle |
|---------|------|
| `FilterCountHtmxController.php` | Fragment count depuis `SearchResultCounter` |
| `ps-search-filter-count-label.html.twig` | Markup minimal (nombre) |
| Route `/ps-search-filters/htmx/count-label` | GET, public |
| `search-filter-bar.js` | `refreshTypeCount()` + flag `htmxTypeCountEnabled` |

### Validation POC

```bash
make drush-cr
curl -s -H "HX-Request: true" \
  "http://localhost:8080/ps-search-filters/htmx/count-label?asset_type=BUR&operation_type=RENT"
# → minimal HTML, body = nombre entier (identique à /ps-search/count)

composer test:search-filter-htmx-count-e2e

# Navigateur : /find-property
# 1. Ouvrir popin « Property type »
# 2. Cliquer une carte asset → le count se met à jour (requête HTMX, pas JSON /ps-search/count pour ce label)
```

### Critères de succès 5A.0

- [x] Popin Type : count live via route HTMX (validé navigateur : asset click → `100`)
- [x] Bouton « Show X results » reste cliquable (apply JS inchangé — swap cible = span)
- [x] Pas de régression autres popins (JSON `/ps-search/count` conservé)
- [x] E2E : `composer test:search-filter-htmx-count-e2e` (HTMX = JSON)

## 5A.1 — Infra HTMX partagée

### Livrables

| Fichier | Rôle |
|---------|------|
| `FilterBarHtmxSettings.php` | Registry popins + `drupalSettings.psSearchFilterHtmx` |
| `search-filter-htmx.js` | Namespace `Drupal.psSearchFilterHtmx` (init, refreshCount, events) |
| `filter_bar_htmx` library | Dépendance `core/drupal.htmx`, chargée avant `filter_bar` |
| `search-filter-bar.js` | Branché sur l’API partagée (plus de HTMX inline) |

### Event custom

`ps-search-filter-htmx-count-updated` — detail : `{ popinKey, targetId, count }`.

Callbacks optionnels sur `Drupal.psSearchFilterHtmx.callbacks` :
- `setLoading(boolean)` — état boutons « Afficher X »
- `onCountUpdated(count, popinKey, targetId)` — sync `currentCount` barre

### Validation 5A.1

```bash
make drush-cr
cd src && vendor/bin/phpunit web/modules/custom/ps_search_filters/tests/src/Unit/Service/FilterBarHtmxSettingsTest.php
composer test:search-filter-htmx-count-e2e
composer test:search-more-filters-e2e

# Navigateur : /find-property — popin Type, clic asset → count HTMX
```

### Critères de succès 5A.1

- [x] `psSearchFilterHtmx` dans drupalSettings (plus de `htmxCountUrl` / `htmxTypeCountEnabled` dans `psSearch`)
- [x] Lib `search-filter-htmx.js` + behavior `psSearchFilterHtmx`
- [x] `search-filter-bar.js` délègue à `Drupal.psSearchFilterHtmx`
- [x] Unit test `FilterBarHtmxSettingsTest`
- [x] E2E count HTMX inchangé
- [x] Validation navigateur (popin Type : Office → « Show 100 results »)

## 5A.2 — SDC Type + apply HTMX

### Livrables

| Fichier | Rôle |
|---------|------|
| `ps_theme/components/search-filter-type/` | SDC grille assets + boutons opération |
| `ps-search-filter-section-type-fields.twig` | Wrapper `{% include 'ps_theme:search-filter-type' %}` |
| `FilterTypeApplyHtmxController.php` | Route apply → `HX-Trigger-After-Settle: ps-search-filter-htmx-apply` |
| `/ps-search-filters/htmx/apply-type` | Validation serveur + déclenchement apply client |
| `search-filter-htmx.js` | `applyPopin()`, `closePopinDropdown()`, event apply |
| `search-filter-bar.js` | Apply popin Type via HTMX puis `applyFilters()` |

Styles asset/op → SDC `search-filter-type/styles/` (migration CSS 5A.9).

### Parcours apply Type

1. Clic « Show X results » dans popin Type (desktop).
2. `htmx.ajax` GET `/ps-search-filters/htmx/apply-type?…` (params AJAX complets).
3. Réponse serveur : trigger `ps-search-filter-htmx-apply` `{ popinKey: type }`.
4. JS ferme le dropdown, appelle `Drupal.psSearchPage.reloadSearch()` (inchangé).

### Validation 5A.2

```bash
make drush-cr
composer test:search-filter-htmx-count-e2e   # inclut apply-type trigger
composer test:search-more-filters-e2e

# Navigateur : /find-property
# Popin Type → Office → Show X results → liste + URL SEO mise à jour
```

### Critères de succès 5A.2

- [x] SDC `ps_theme:search-filter-type` (desktop + mobile via include existant)
- [x] Route apply HTMX + trigger custom
- [x] Apply popin Type branché sur infra 5A.1
- [x] E2E apply trigger
- [x] Validation navigateur apply Type (`/for-rent/office/` après Office + To rent + Apply)

## 5A.3 — SDC Localisation + count/apply HTMX

### Livrables

| Fichier | Rôle |
|---------|------|
| `ps_theme/components/search-filter-location/` | SDC éditeur chips + input autocomplete |
| `ps-search-filter-section-location-fields.twig` | Wrapper SDC (desktop + mobile `-m`) |
| `FilterBarHtmxSettings` | Popin `location` (count + apply URLs, toggleSelector) |
| `FilterLocationApplyHtmxController.php` | Route `/ps-search-filters/htmx/apply-location` |
| `search-filter-bar.js` | Count live HTMX si popin location ouverte ; apply HTMX |
| `#ps-filter-location-count-label` | Cible swap count HTMX (footer popin location) |

Suggest autocomplete reste sur `/ps-search/location-suggest` (JSON) — hors scope HTMX ce lot.

### Validation 5A.3

```bash
make drush-cr
composer test:search-filter-htmx-count-e2e
composer test:search-more-filters-e2e

# Navigateur : /find-property
# Popin Location → saisir ville → count HTMX → Apply → URL avec locality
```

### Critères de succès 5A.3

- [x] SDC `ps_theme:search-filter-location`
- [x] Count popin Location via HTMX (`#ps-filter-location-count-label`)
- [x] Apply popin Location via HTMX + trigger
- [x] E2E count locality + apply-location
- [x] Validation navigateur Location (`Paris (75002)` → count **27** → Apply → `?locality=Paris`)

### Correctif count stale (5A.3)

Les réponses HTMX count arrivant hors ordre ne doivent pas écraser le DOM : séquence `_ps_count_seq` en query string + `htmx:beforeSwap` → `preventDefault()` si stale (`search-filter-htmx.js` v1.0.6).

### Correctif hauteur champ Location (5A.3)

Retrait du `min-height: 44px` sur le toggle ouvert — aligné sur `--ps-filter-field-height` (36px) comme les autres champs.

## 5A.4 — SDC Range + count/apply HTMX

### Livrables

| Fichier | Rôle |
|---------|------|
| `ps_theme/components/search-filter-range/` | SDC min/max surface, capacity, budget |
| `ps-search-filter-section-range-fields.twig` | Wrapper SDC |
| `FilterBarHtmxSettings` | Popins `surface`, `capacity`, `budget` |
| `FilterRangeApplyHtmxController.php` | Route `/ps-search-filters/htmx/apply-range/{section}` |
| `ps-search-filter-bar.html.twig` | `#ps-filter-*-count-label`, `data-ps-htmx-apply` |
| `search-filter-bar.js` | `applyPopinViaHtmx()` générique pour tous les popins HTMX |

### Validation 5A.4

```bash
make drush-cr
composer test:search-filter-htmx-count-e2e
composer test:search-more-filters-e2e

# Navigateur : /find-property
# Popin Surface → min 100 → count HTMX → Apply
```

### Critères de succès 5A.4

- [x] SDC `ps_theme:search-filter-range`
- [x] Count popins surface/capacity/budget via HTMX
- [x] Apply via HTMX + trigger (route paramétrée `{section}`)
- [x] E2E count surface + apply-range/surface
- [x] Validation navigateur Surface (min 100 m² → count **424** → Apply → `?surface[min]=100`)

## 5A.5 — More filters lazy-load HTMX

### Livrables

| Fichier | Rôle |
|---------|------|
| `MoreCriteriaGroupController.php` | Fragment HTML (plus de JSON `{ html }`) |
| `/ps-search-filters/htmx/more-criteria/{group_id}` | Route `_htmx_route` |
| `FilterBarHtmxSettings` | `moreCriteriaGroupUrl` dans `psSearchFilterHtmx` |
| `search-filter-htmx.js` | `loadMoreCriteriaGroup()` via `htmx.ajax` |
| `search-filter-bar.js` | Lazy-load accordion/mobile via API HTMX partagée |

Route JSON `/ps-search-filters/more-criteria/*` supprimée.

### Validation 5A.5

```bash
make drush-cr
composer test:search-more-filters-e2e
composer test:search-filter-htmx-count-e2e

# Navigateur : /find-property
# More filters → ouvrir groupe Equipments → filtres chargés (HTMX)
```

### Critères de succès 5A.5

- [x] Lazy-load groupes via route HTMX (plus de `fetch` JSON)
- [x] `moreCriteriaGroupUrl` dans `psSearchFilterHtmx`
- [x] E2E more-filters (endpoint HTMX + structure page)
- [x] Validation navigateur More filters (Equipments → **45** filtres HTMX, `data-loaded=1`)

## 5A.6 — Mobile offcanvas = composition SDC

### Livrables

| Fichier | Rôle |
|---------|------|
| `ps_theme/components/search-filter-mobile-section/` | SDC coquille section mobile (titre + slot) |
| `ps-search-filter-mobile-offcanvas-body.twig` | Composition unifiée (type, location, ranges, more) |
| `ps-search-filter-section-more-stacked.twig` | Refactor via SDC mobile-section |
| `FilterBarHtmxSettings` | Popin `mobile` (count + apply, offcanvas) |
| `FilterMobileApplyHtmxController.php` | Route `/ps-search-filters/htmx/apply-mobile` |
| `#ps-filter-mobile-count-label` | Count HTMX footer offcanvas mobile |
| `search-filter-htmx.js` | `closePopinDropdown()` supporte `offcanvasId` |

Chaque section mobile réutilise les mêmes wrappers SDC desktop (`search-filter-type`, `search-filter-location`, `search-filter-range`).

### Validation 5A.6

```bash
make drush-cr
composer test:search-more-filters-e2e
composer test:search-filter-htmx-count-e2e

# Navigateur mobile (< lg) : /find-property
# See all filters → count HTMX → Apply → offcanvas se ferme + résultats
```

### Critères de succès 5A.6

- [x] SDC `ps_theme:search-filter-mobile-section`
- [x] Offcanvas body = composition SDC desktop (template dédié)
- [x] Count + apply mobile via HTMX
- [x] E2E structure + apply-mobile trigger
- [x] Validation navigateur mobile : surface min 100 → count **424** → Apply → `?surface[min]=100`, offcanvas fermé

### Correctifs livrés avec 5A.6

| Problème | Fix |
|----------|-----|
| Slots SDC vides avec `{% embed %}` | Passer le contenu via `{% include %}` + variable `content` |
| Count HTMX écrasé après swap | Init barre filtrée dans `once('ps-filter-bar-init', '.ps-search-view')` |

## 5A.7 — Header résultats (template + HTMX)

### Livrables

| Fichier | Rôle |
|---------|------|
| `ps-search-results-header.html.twig` | Markup header (titre, count, zone hint, tri) |
| `SearchResultsHeaderBuilder::buildRenderArray()` | Render array thème partagé |
| `SearchResultsHeaderHtmxController.php` | Route `/ps-search-filters/htmx/results-header` |
| `FilterBarHtmxSettings` | `resultsHeaderUrl`, `resultsHeaderTargetId` |
| `search-filter-htmx.js` | `refreshResultsHeader()` |
| `search-page-zone-reload.js` | Header via HTMX après reload liste (plus d’extract Views AJAX) |
| `search-page-layout.js` | Tri en délégation d’événements (survit au swap header) |

Le markup header est sorti du template Views ; `#ps-search-results-header` sert de cible HTMX.

### Validation 5A.7

```bash
make drush-cr
composer test:search-filter-htmx-count-e2e
cd src && vendor/bin/phpunit web/modules/custom/ps_search_filters/tests/src/Unit/Service/FilterBarHtmxSettingsTest.php

# Navigateur : /find-property
# Popin Surface → min 100 → Apply → header « 424 results »
```

### Critères de succès 5A.7

- [x] Template dédié `ps_search_results_header`
- [x] Route HTMX fragment header
- [x] Reload liste met à jour header via `refreshResultsHeader()`
- [x] E2E HTMX results-header + target id page
- [x] Validation navigateur apply → header count/titre (`424 results` après Surface min 100)

## 5A.8 — Nettoyage count JSON legacy

### Livrables

| Fichier | Rôle |
|---------|------|
| `search-filter-bar.js` | Suppression `fetchCount()` / `countUrl` JSON ; tout passe par HTMX |
| `search-filter-htmx.js` | `refreshGlobalCount()` ; fix `resolvePopinKeyFromDropdown` |
| `FilterBarHtmxSettings` | Popin `more` (`#ps-filter-more-count-label`) |
| `FilterBarBuilder` | Retrait `psSearch.countUrl` |
| `ps_search_filters.routing.yml` | `_title: ''` sur route count-label (plus de pollution titre page) |

### Validation 5A.8

```bash
make drush-cr
composer test:search-filter-htmx-count-e2e
cd src && vendor/bin/phpunit web/modules/custom/ps_search_filters/tests/src/Unit/Service/FilterBarHtmxSettingsTest.php

# Navigateur : /find-property
# 1. Popin Type → count HTMX (pas de requête JSON /ps-search/count)
# 2. More filters offcanvas → count HTMX sur footer
# 3. Titre page reste « Search offers » (pas « Filter count label »)
```

### Critères de succès 5A.8

- [x] Plus de `fetch()` JSON `/ps-search/count` dans `search-filter-bar.js`
- [x] Popin More filters : count via HTMX (`#ps-filter-more-count-label`)
- [x] Route count-label : titre route vide
- [x] E2E inchangé (13/13)
- [x] Validation navigateur : Type popin + More offcanvas → HTMX `count-label`, titre page inchangé, zéro requête `/ps-search/count`

## 5A.9 — Migration CSS vers SDC

### Livrables

| SDC | Fichier CSS | Contenu migré |
|-----|-------------|---------------|
| `search-filter-type` | `styles/search-filter-type.scss` | Grille assets, boutons op, popin type, responsive |
| `search-filter-location` | `styles/search-filter-location.scss` | Éditeur, chips, suggest, popin location |
| `search-filter-range` | `styles/search-filter-range.scss` | Inputs min/max surface, capacity, budget |
| `search-filter-mobile-section` | `styles/search-filter-mobile-section.scss` | Coquille sections offcanvas mobile |

Chaque `*.component.yml` : `libraryOverrides.css.component` → chargement auto à l’include SDC.

`search-filter-bar.css` conserve le **shell** barre (grid, toggles, popins Bootstrap, footer apply, More offcanvas, backdrop).

### Validation 5A.9

```bash
make drush-cr
docker exec ps_npm sh -lc 'cd /workspace/web/themes/custom/ps_theme && npm run gulp-prod'
composer test:search-filter-htmx-count-e2e

# Navigateur : /find-property (desktop 1280px)
# Popin Type → grille assets + op buttons stylés
# Popin Surface → inputs range bordure verte au focus
# Popin Location → icône pin 20px
```

### Critères de succès 5A.9

- [x] CSS composants dans `ps_theme/components/search-filter-*/styles/`
- [x] `libraryOverrides` sur les 4 SDC filtres
- [x] `search-filter-bar.css` allégé (~580 lignes, shell only)
- [x] E2E inchangé (13/13)
- [x] Validation navigateur : styles Type / Surface / Location OK (agrégés Drupal)

## Décision produit requise avant 5A.8+

La Phase 5A **ne remplace pas** la barre custom par BEF visible.  
Si le design BNPPRE reste obligatoire, poursuivre 5A ; sinon Phase 5B (BEF) = autre UX.

## Références

- Audit refactoring : Phase 5 UI (plan conversation / audit Drupal First)
- Skill : `.claude/skills/drupal-htmx/SKILL.md`
- Module carte clôturé : `ps_search/README.md` Phase 4
