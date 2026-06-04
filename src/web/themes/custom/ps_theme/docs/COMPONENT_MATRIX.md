# Matrice composants — Figma Stellar → Drupal

> Prototype : [BNP PRE Stellar](https://www.figma.com/proto/rrA1dlYnJMzcXlwOZ5iuuw/BNP-PRE-Stellar---Livrable-client?node-id=48-7862) (~56 écrans)

Légende : **G** = `ui_suite_bnp` · **PS** = `ps_theme` · **M** = module Drupal · **LB** = Layout Builder

## Global / chrome

| Écran Figma | Composant | Couche | Notes |
|---|---|---|---|
| Header (logo, nav, langue, favoris, compte) | `navbar` + region `navigation` | G + PS template | Favoris : `ps_favorite` block ; icônes RE dans parent |
| Fil d'Ariane | `breadcrumb` | G | Override Sass Stellar dans ps_theme |
| Footer corporate | `footer` section LB | G + LB | Liens légaux, réseaux — contenu éditorial |
| Boutons CTA | `button` | G | Variante primary RE (#00915a) |
| Modales (contact, brochure) | `modal` | G | Contenu formulaire Webform / custom |
| Toast / alertes | `alert`, `toast` | G | Messages Drupal |

## Homepage

| Écran Figma | Composant | Couche | Notes |
|---|---|---|---|
| Hero photo + titre serif | `search-hero` | **PS** | SDC + LB section ; overlay `--re-color-overlay-60` |
| Toggle Louer / Acheter / Investir | `transaction-toggle` | **PS** | Liens SEO `ps_search` ; Investir = TBD produit |
| Champ localisation | autocomplete location | **M** | Module `ps_location_autocomplete` absent |
| Filtres rapides (type bien) | `asset-type-chips` | **PS** | Dictionary `asset_type` |
| Tuiles univers (bureaux, logistique…) | `card` + LB | G + LB | Image + titre + lien |
| Bandeau confiance / chiffres | `stats-bar` | **PS** ou G | À trancher réutilisabilité |
| Carrousel offres | `offer-card` × N | **PS** | View display teaser |

## Recherche (`/recherche`)

| Écran Figma | Composant | Couche | Notes |
|---|---|---|---|
| Barre filtres (sticky) | `search-filter-bar` | **PS** + **M** | `ps_search_filters` absent |
| Facettes Solr | `facet-checkbox`, `facet-range` | **PS** | Facets module + ps_search index |
| Compteur résultats | texte View | M | `views.view.ps_search_offers` |
| Toggle liste / carte | `view-mode-toggle` | **PS** | JS behavior |
| Carte interactive | map container | **PS** + **M** | Leaflet/Mapbox — non implémenté |
| Grille résultats | `offer-card` | **PS** | View mode `search_card` à créer |
| Pagination | `pagination` | G | Override Sass |
| Empty state | `alert` + illustration | G + PS | |

## Fiche offre

| Écran Figma | Composant | Couche | Notes |
|---|---|---|---|
| Galerie média | `media-gallery` | **PS** | `field_media_gallery`, carousel G |
| Titre + référence + badges | `offer-header` | **PS** | `field_commercial_title`, `field_reference` |
| KPI bar (surface, prix, dispo) | `offer-kpi-bar` | **PS** | `field_surfaces`, `field_budget_*` |
| Description | body field | Drupal | View mode full |
| Caractéristiques | `feature-list` | **PS** | `ps_feature` field formatter |
| Diagnostics DPE/GES | `diagnostic-badge` | **PS** | `ps_diagnostic` |
| Plan / documents | `document-list` | **PS** | `field_media_document` |
| Carte localisation | map pin | **PS** | `field_geo`, `field_address` |
| Contact agent | `agent-contact-card` | **PS** | `ps_agent` entity |
| Favoris / partage / brochure | actions bar | **PS** | `ps_favorite` + download |
| Offres similaires | `offer-card` carousel | **PS** | View relationnelle |

## Composants génériques existants (`ui_suite_bnp`)

Déjà disponibles : accordion, alert, badge, breadcrumb, button, button_group, card, carousel, close_button, dropdown, list_group, modal, nav, navbar, offcanvas, pagination, progress, spinner, table, toast.

## Priorité implémentation (vertical slices)

1. ✅ `search-hero` + tokens Stellar
2. ✅ `offer-card` (props statiques → puis branchement node teaser)
3. `transaction-toggle` + wiring SEO URLs
4. `node--offer--teaser.html.twig` + view mode `search_card`
5. `views-view--ps-search-offers.html.twig` (layout split liste/carte)
6. Header favoris + `node--offer--full.html.twig`

## Questions produit ouvertes

| Sujet | Impact composants |
|---|---|
| Investir / flexible | `transaction-toggle`, filtres |
| Hôtellerie | Nouveau asset type + tuiles homepage |
| LOG vs ENT URLs SEO | `ps_search` mappings, pas le thème |
| Alertes recherche | Nouveau module + modal |
