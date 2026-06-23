# Plan de tests — Apply filtres & URLs (SEO + query)

> Cible principale : COM `http://com.localhost:8080` (`@ps.com`).
> Compléments : FR `http://fr.localhost:8083` pour langue / slugs FR.
> Prérequis : `make up`, `make drush-cr`, hard refresh si JS modifié (`filter.bar` 1.0.10+).

## Règles d’or (à vérifier sur chaque scénario « apply »)

| Règle | Exemple valide | Exemple invalide |
|-------|----------------|------------------|
| Op + asset dans le **path SEO** → pas dans la query navigateur | `/for-rent/office/?nearby_transport=bus` | `…?operation_type[LOC]=LOC&asset_type[BUR]=BUR` |
| Localité en **segment SEO** → pas `locality=` en query | `/for-rent/office/ile-de-france/?reference=ABC` | `…?locality=region:ile-de-france` sur page région |
| Filtres « More » / surface / budget → **query seule** | `surface[min]=100` | facet bracket dupliqué |
| `pushState` = même URL que navigation directe | curl 200 + count cohérent | URL bar ≠ curl |
| Count live (HTMX) = count après apply = API `/api/ps/count` | 3 partout | 3 → 20 après apply |
| Canonical ne duplique pas les facets du path | canonical `/for-rent/office/` | canonical avec `operation_type` |

## Matrice des bases URL

| ID | Base path | Op | Asset | Flexible |
|----|-----------|-----|-------|----------|
| A1 | `/find-property` | — | — | oui |
| A2 | `/for-rent/` | LOC | — | non |
| A3 | `/for-rent/office/` | LOC | BUR | non |
| A4 | `/for-sale/office/` | VEN | BUR | non |
| A5 | `/office/` | — | BUR | asset-only |
| A6 | `/for-rent/office/ile-de-france/` | LOC | BUR | + région |
| A7 | `/for-rent/office/paris-75/` | LOC | BUR | + dept |
| A8 | `/for-rent/office/paris-75/paris-9-75009/` | LOC | BUR | + ville |
| A9 | `/fr/a-louer/bureaux/` | LOC | BUR | FR |
| A10 | `/fr/recherche-immobiliere` | — | — | FR flexible |

---

## 1. Scénarios réalistes — un filtre à la fois

### 1.1 Type / opération (popin desktop HTMX)

| # | Contexte | Action | URL attendue (browser) | Résultats |
|---|----------|--------|------------------------|-----------|
| R1 | `/find-property` | LOC + BUR → Apply | Redirect `/for-rent/office/` | ≤ base |
| R2 | `/for-rent/office/` | Changer asset → ENT → Apply | `/for-rent/warehouse/` | ≤ bureaux |
| R3 | `/for-rent/office/` | VEN → Apply | `/for-sale/office/` (full nav) | change |
| R4 | `/office/` (Indifférent) | LOC → Apply | `/for-rent/office/` | filtré LOC |

### 1.2 Localisation (popin + chips)

| # | Contexte | Action | URL attendue | Notes |
|---|----------|--------|--------------|-------|
| R5 | `/for-rent/office/` | Chip Paris (75) → Apply | `/for-rent/office/paris-75/` | pas `locality=` |
| R6 | `/for-rent/office/` | Suggest Île-de-France → Apply | `/for-rent/office/ile-de-france/` | chip lisible |
| R7 | `/for-rent/office/paris-75/` | Supprimer chip → Apply | `/for-rent/office/` | path raccourci |
| R8 | `/find-property` | Paris query → Apply | redirect SEO + locality path ou query | selon règle hero |

### 1.3 Surface / capacité / budget (dropdown desktop)

| # | Contexte | Action | Query attendue | Path inchangé |
|---|----------|--------|----------------|---------------|
| R9 | `/for-rent/office/` | surface 100–500 → Apply | `surface[min]=100&surface[max]=500` | oui |
| R10 | `/for-rent/office/` | budget max → Apply | `budget[max]=…` | oui |
| R11 | `/for-rent/office/` | capacity (coworking) | sur `/for-rent/coworking/` si visible | — |

### 1.4 More filters (offcanvas desktop + mobile)

| # | Contexte | Action | Query | Count |
|---|----------|--------|-------|-------|
| R12 | `/for-rent/office/` | `nearby_transport=bus` → Apply | `?nearby_transport=bus` seul | 3 |
| R13 | `/for-rent/office/` | `reference` texte → Apply | `?reference=…` | ≤ base |
| R14 | `/for-rent/office/` | checkbox immersive tour | `?has_immersive_tour=1` | ≤ base |
| R15 | `/for-rent/office/` | lazy group équipements (1 feature) | `?feature_…=1` | diminue |
| R16 | Mobile `/for-rent/office/` | même filtre via `#ps-mobile-filters` | identique desktop | pas wipe |

### 1.5 Apply HTMX par popin

| Popin | Route apply | Vérifie |
|-------|-------------|---------|
| type | `/api/ps/htmx/apply-type` | trigger + URL |
| location | apply-location | path locality |
| surface | apply-surface | bracket query |
| capacity | apply-capacity | idem |
| budget | apply-budget | idem |
| more | apply-more | more query clean |
| mobile | apply-mobile | agrégat mobile |

---

## 2. Scénarios réalistes — combinaisons (mélanges)

| # | Path initial | Filtres combinés | URL browser attendue |
|---|--------------|-----------------|----------------------|
| M1 | `/for-rent/office/` | transport bus + surface min | `?nearby_transport=bus&surface[min]=100` |
| M2 | `/for-rent/office/ile-de-france/` | transport + ref | path région + query only |
| M3 | `/for-rent/office/paris-75/` | surface + budget | dept path + brackets |
| M4 | `/for-rent/office/` | transport puis add Paris → Apply | `/for-rent/office/paris-75/?nearby_transport=bus` ou query locality selon règle |
| M5 | `/for-rent/office/?nearby_transport=bus` | change surface → Apply | transport conservé |
| M6 | `/for-rent/office/` | feature checkbox + transport | deux params query |
| M7 | `/office/` asset-only | LOC + transport | `/for-rent/office/?nearby_transport=bus` |
| M8 | `/find-property` | LOC+BUR+Paris+surface | redirect SEO complet |
| M9 | map_bounds actif | + transport (localité inchangée) | `map_bounds` conservé |
| M10 | map_bounds actif | change localité | `map_bounds` **supprimé** |

---

## 3. Scénarios irréalistes / edge cases

| # | Scénario | Entrée | Comportement attendu | Risque |
|---|----------|--------|----------------------|--------|
| E1 | Double facet path + query | `/for-rent/office/?operation_type[LOC]=LOC` | Filtre OK via path ; query bracket ignorée ou redirigée ; **URL apply ne rajoute pas brackets** | double filtre |
| E2 | Asset conflict | `/for-rent/office/?asset_type[ENT]=ENT` | Path BUR prime ou intersection vide | 0 résultats |
| E3 | Op conflict | `/for-sale/office/?operation_type[LOC]=LOC` | incohérent VEN vs LOC | 0 ou ignore |
| E4 | `nearby_transport=` vide | Apply avec champ vide | pas de clé dans URL | delete stale |
| E5 | Transport mobile vide + desktop rempli | Apply desktop | desktop valeur conservée (offcanvas hidden skip) | **bug historique** |
| E6 | Caractères spéciaux transport | `Métro & RER` | encodage URL OK, count stable | Solr contains |
| E7 | `reference` SQL-ish | `' OR 1=1` | pas d’erreur, 0 ou safe | sécurité |
| E8 | Surface min > max | 500–100 | 0 résultats ou swap | UX |
| E9 | Page 2 + apply | `?page=1` + apply | `page` supprimé | pagination reset |
| E10 | Sort actif + apply | `sort_by` + transport | sort conservé | |
| E11 | URL 2× même more filter | duplicate keys | stable | |
| E12 | `/for-rent/invalid-slug/` | 404 ou redirect | pas 500 | |
| E13 | `lang=fr` sur EN path | query lang | contenu cohérent | |
| E14 | HTMX apply-more sans JS | curl HX-Request | trigger header seul | |
| E15 | Hard refresh mid-offcanvas | état UI vs URL | URL = vérité | |
| E16 | popstate arrière | navigateur back | sync UI depuis URL | |
| E17 | Canonical vs URL bar après pushState | | canonical inchangé jusqu’à reload | SEO |
| E18 | API count vs page | chaque combinaison M* | counts égaux | régression |

---

## 4. Exécution — navigateur (B2B manuel / Cursor browser)

Checklist par scénario :

1. Ouvrir path initial (hard refresh).
2. Ouvrir filtre, saisir valeur, vérifier count HTMX.
3. Apply (bouton Show X results).
4. **URL bar** : path + query conformes au tableau.
5. **Résultats** : nombre = count bouton.
6. **Pas de** `operation_type[` / `asset_type[` si déjà dans path.
7. Re-ouvrir More : champs pré-remplis depuis URL/état.
8. DevTools : pas d’erreur JS ; une requête Views AJAX.

Priorité navigateur (smoke) : R12, R16, M1, M4, M9, M10, E1, E5.

---

## 5. Exécution — scripts E2E (régression automatisée)

| Script | Couverture |
|--------|------------|
| `b2b_filter_apply_urls.sh` | Matrice URL directe + propreté query + count API/HTML |
| `b2b_more_filters.sh` | More filters, transport, lazy-load |
| `b2b_locality_seo.sh` | Paths locality |
| `b2b_filter_count_htmx.sh` | HTMX count/apply headers |
| `e2e_seo_urls.sh` | Redirects, canonical, slugs |
| `b2b_search_full.sh` | Agrégat |

```bash
# Suite apply URLs
bash src/web/modules/custom/ps_search/tests/b2b_filter_apply_urls.sh

# Suite complète
bash src/web/modules/custom/ps_search/tests/b2b_search_full.sh
```

---

## 6. Critères de sortie

- [ ] Tous les scripts E2E PASS
- [ ] Smoke navigateur R12 + M1 + E5 validés
- [ ] Aucune URL SEO avec facet brackets redondants après apply AJAX
- [ ] Count API = count page pour ≥ 10 combinaisons de la matrice M*
- [ ] FR A9 : au moins smoke `/fr/a-louer/bureaux/?nearby_transport=bus`
