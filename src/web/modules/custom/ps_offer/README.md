# Module Property Search - Offer (`ps_offer`)

> Statut : 🟡 En développement — modèle de données actif, cycle de réinstallation validé

Modèle de données, validations métier et orchestration presave pour les offres immobilières BNPPRE.

## Responsabilité

`ps_offer` définit le bundle node `offer` (~44 champs custom + `body`), ses displays BO/FO, ses règles de validation, la génération de références auto/manuel, la sync diagnostics DPE/GES et l'intégration avec les modules PS satellites (features, surfaces, agents, diagnostics, médias, favoris).

L'édition des contenus reste sous `/node/add/offer` et `/node/{nid}/edit`. La configuration admin est centralisée sous `/admin/ps/config/offer`.

Ce module **ne fait pas** :
- le catalogue features (`ps_feature`) ;
- la matrice visibilité formulaire (`ps_context` — module séparé) ;
- l'import CRM/XML (`ps_migrate`) ;
- l'indexation Solr (`ps_search`).

## Fonctionnalités

- Bundle node `offer` avec révisions, traduction contenu, pathauto, scheduler, metatag
- **44 champs custom** + `body` (103 fichiers `config/install/`)
- Validation progressive : warnings en brouillon, blocage en publication
- Génération références auto/manuel (patterns + alias sets, LOT 1/2/3)
- Sync diagnostics : `field_diagnostics` → `field_diagnostics_dpe` / `field_diagnostics_ges`
- Projection surfaces post-save via `ps_surface` (`SurfaceProjectionManager`)
- Vue admin `ps_offer_by_feature` (itération 1 filtre par feature)
- Cible favoris `ps_favorite.target.node.offer` + view mode `card_favorite`
- Tokens pathauto : operation, asset, country, department, city
- Tests : unit, Behat, scripts E2E bash

## Architecture

### Entités

| Entité Drupal | Bundle / ID | Type | Description |
|---|---|---|---|
| `node` | `offer` | Content | Offre immobilière révisionnable |
| `ps_offer_reference_pattern` | — | Config | Pattern de génération référence auto |
| `ps_offer_reference_alias_set` | — | Config | Alias codes pour segments référence |

### Champs du bundle `offer` (44 custom + body)

#### Identification

| Champ | Type | Obligatoire BO | Description |
|---|---|---|---|
| `field_reference` | `string` | Oui (auto ou manuel) | Référence métier affichée |
| `field_reference_auto` | `boolean` | Non | `1` = génération auto, `0` = saisie manuelle |
| `field_business_id` | `string` | Non | ID métier source (XML) |
| `field_technical_id` | `string` | Non | ID technique intégrations |
| `field_asset_type` | `ps_dictionary` (`asset_type`) | Oui | Type d'actif (BUR, COW, ENT…) |
| `field_operation_type` | `ps_dictionary` (`operation_type`) | Oui | Type d'opération (RENT, SALE) |
| `field_client_type` | `ps_dictionary` (`client_type`) | Oui | Segmentation B2B / B2C |
| `field_mandate_type` | `ps_dictionary` (`mandate_type`) | Non | Type de mandat |
| `field_divisible` | `boolean` | Non | Offre divisible (masqué si COW via matrice) |
| `field_commercial_title` | `string` | Non | Titre commercial |
| `field_asset_type_raw` | `string` | Non | Code XML brut (hidden, import) |
| `field_operation_type_raw` | `string` | Non | Code XML brut (hidden, import) |

#### Localisation

| Champ | Type | Description |
|---|---|---|
| `field_address` | `address` | Adresse structurée (module Address, pays du site via CMI) |
| `field_geo` | `geofield` | Coordonnées WKT (géocodage via Geocoder) |
| `field_show_address` | `boolean` | Afficher l'adresse en front |

#### Surfaces & divisions

| Champ | Type | Description |
|---|---|---|
| `field_surfaces` | `ps_surface_item` (card. -1) | Surfaces qualifiées TOTAL / DISPO / ETREF / MINIM… |
| `field_divisions` | `entity_reference` → `ps_surface_division` | Lots de surface |

#### Capacité (COW)

| Champ | Type | Description |
|---|---|---|
| `field_capacity_total` | `integer` | Nombre de postes total |
| `field_capacity_available` | `integer` | Postes disponibles |
| `field_capacity_unit` | `list_string` | Unité (ex. postes) |
| `field_capacity_mode` | `list_string` | Mode (ex. SEAT_BASED) |
| `field_capacity_notes` | `string_long` | Notes libres |

#### Budget

| Champ | Type | Description |
|---|---|---|
| `field_budget_value` | `decimal` | Montant |
| `field_budget_currency` | `ps_dictionary` (`currency`) | Devise (EUR par défaut) |
| `field_budget_unit` | `ps_dictionary` (`budget_unit`) | Unité (PER_M2, PER_POSTE, GLOBAL) |
| `field_budget_period` | `list_string` | Période (YEAR — location uniquement) |
| `field_budget_ht` | `boolean` | Prix HT |
| `field_budget_cc` | `boolean` | Charges comprises |
| `field_budget_fees` | `string_long` | Honoraires (texte libre, popover budget) |

Configuration site : `/admin/ps/config/offer` — hub avec onglets (budget, surface, médias, sections, carte, références). Objet config `ps_offer.settings` (libellés budget/surface, image par défaut).

#### Features & diagnostics

| Champ | Type | Description |
|---|---|---|
| `field_features` | `feature` | Caractéristiques métier (widget `feature_builder`) |
| `field_diagnostics` | `diagnostic_item` (card. -1) | Source import XML (multi-valeur) |
| `field_diagnostics_dpe` | `diagnostic_item` | Diagnostic DPE dédié (sync presave) |
| `field_diagnostics_ges` | `diagnostic_item` | Diagnostic GES dédié (sync presave) |

#### Médias & agents

| Champ | Type | Description |
|---|---|---|
| `field_media_gallery` | `entity_reference` → media | Galerie (images, vidéos, visites 3D…) |
| `field_media_document` | `entity_reference` → media | Brochure unique téléchargeable |
| `field_primary_agent` | `entity_reference` → `ps_agent` | Agent principal |
| `field_secondary_agents` | `entity_reference` → `ps_agent` | Agents secondaires |

#### Contenu & SEO

| Champ | Type | Description |
|---|---|---|
| `body` | `text_with_summary` | Description (summary + value) |
| `field_availability` | `string_long` | Disponibilité (texte libre) |
| `field_metatag` | `metatag` | SEO (Metatag + Schema.org) |

#### Import & protection

| Champ | Type | Description |
|---|---|---|
| `field_source_checksum` | `string` | Hash source (déduplication) |
| `field_source_system` | `string` | Système source |
| `field_import_id` | `string` | ID batch import |
| `field_last_imported_at` | `datetime` | Timestamp dernier import |
| `field_internal_lock` | `boolean` | Protection écrasement import |
| `field_source_tracking` | `string_long` | Métadonnées source JSON |

### Services

| Service ID | Classe | Rôle |
|---|---|---|
| `Drupal\ps_offer\Service\OfferValidationManager` | `OfferValidationManager` | Règles validation presave (matrix-aware via `OfferContextResolverInterface`) |
| `ps_offer.surface_kpi_builder` | `OfferSurfaceKpiBuilder` | KPI surface/capacité FO (matrix-driven) |
| `Drupal\ps_offer\OfferContextResolverInterface` | *(interface)* | Pont optionnel vers `ps_context` |
| `Drupal\ps_offer\Service\OfferReferenceManager` | `OfferReferenceManager` | Mode auto/manuel + génération |
| `Drupal\ps_offer\Service\OfferReferenceGenerator` | `OfferReferenceGenerator` | Construction référence par segments |
| `Drupal\ps_offer\Service\OfferReferencePatternResolver` | `OfferReferencePatternResolver` | Pattern actif par bundle |
| `Drupal\ps_offer\Service\OfferReferenceAliasResolver` | `OfferReferenceAliasResolver` | Alias codes dictionnaire |
| `ps_offer.path_token_provider` | `OfferPathTokenProvider` | Tokens pathauto custom |
| `Drupal\ps_offer\Hook\OfferHooks` | `OfferHooks` | Presave, form alters, validation galerie |

### Hooks OOP

| Classe | Hook | Méthode | Rôle |
|---|---|---|---|
| `OfferHooks` | `node_presave` | `nodePresave()` | Sync diagnostics, médias traduction, refs, validation |
| `OfferHooks` | `form_node_offer_form_alter` | — | Warnings dictionnaire, library diagnostic admin |
| `OfferHooks` | `form_node_offer_edit_form_alter` | — | Idem édition |
| `OfferHooks` | (validate) | `validateGallery()` | Galerie obligatoire à la publication |

Fichier tokens : `ps_offer.tokens.inc` (`ps-offer-operation`, `ps-offer-asset`, `ps-offer-country`, `ps-offer-department`, `ps-offer-city`).

### Plugins

| Plugin type | Plugin ID | Classe | Description |
|---|---|---|---|
| FieldWidget | `ps_offer_reference_widget` | `OfferReferenceWidget` | Toggle auto/manuel + preview référence |

### Règles de validation (`OfferValidationManager`)

| Méthode | Règle | Brouillon | Publication |
|---|---|---|---|
| `validateBudget()` | Normalise budget : valeur ≤ 0 ou vide → NULL + reset period/unit | Silencieux | Silencieux |
| `validateCapacity()` | SEAT_BASED / PER_POSTE ; requis si onglet Capacity visible (matrix) | Warning | **Blocant** |
| `validateSurface()` | Au moins une surface TOTAL > 0 ; **skip si matrix masque Surface** | Warning | **Blocant** |
| `validateDivisibility()` | Non divisible + DISPO < TOTAL → warning UX ; skip si Surface masquée | Warning | Warning |
| `validatePrimaryAgent()` | Pas d'agent principal → dépublication | N/A | Dépublication auto |
| `validateManualReferenceUniqueness()` | Mode manuel + doublon `field_reference` | **Blocant** | **Blocant** |

**Form validate (hors manager) :** `validateGallery()` — galerie non vide requise à la publication.

**Skip validation :** sauvegardes en contexte traduction (langue ≠ langue par défaut).

## Routes & Accès

Hub : **`/admin/ps/config/offer`** (`ps_offer.admin_overview`) — permission `manage ps_offer` ou `administer ps offer reference patterns`.

| Route | Chemin | Permission |
|---|---|---|
| `ps_offer.admin_overview` | `/admin/ps/config/offer` | `manage ps_offer` ou `administer ps offer reference patterns` |
| `ps_offer.budget_display_settings` | `/admin/ps/config/offer/budget` | `manage ps_offer` |
| `ps_offer.budget_popover_settings` | `/admin/ps/config/offer/budget/popover` | `manage ps_offer` |
| `ps_offer.surface_display_settings` | `/admin/ps/config/offer/surface` | `manage ps_offer` |
| `ps_offer.media_settings` | `/admin/ps/config/offer/media` | `manage ps_offer` |
| `ps_offer.section_settings` | `/admin/ps/config/offer/sections` | `manage ps_offer` |
| `ps_offer.map_settings` | `/admin/ps/config/offer/map` | `manage ps_offer` |
| `ps_offer.reference_config` | `/admin/ps/config/offer/reference` | `administer ps offer reference patterns` |
| `entity.ps_offer_reference_pattern.collection` | `/admin/ps/config/offer/reference/patterns` | `administer ps offer reference patterns` |
| `entity.ps_offer_reference_alias_set.collection` | `/admin/ps/config/offer/reference/aliases` | `administer ps offer reference patterns` |
| Node add/edit | `/node/add/offer`, `/node/{nid}/edit` | Permissions node standard |
| Vue admin features | `/admin/ps/content/offers-by-feature` | `manage ps_offer` |

## Permissions

| Permission | Description |
|---|---|
| `administer ps offer reference patterns` | Gérer patterns et alias sets de référence |
| `manage ps_offer` | Accès section hub (via `ps_core`) |

## Configuration initiale (`config/install/`)

**103 fichiers** dont :

| Catégorie | Exemples |
|---|---|
| Type & champs | `node.type.offer.yml`, `field.storage.*`, `field.field.node.offer.*` |
| Displays | form/view default, teaser, card_favorite |
| Intégrations | pathauto, metatag, scheduler, content_translation, geocoder |
| Références | `ps_offer.reference_pattern.default.yml` |
| Admin | `views.view.ps_offer_by_feature.yml` |
| Favoris | `ps_favorite.target.node.offer.yml`, `core.entity_view_mode.node.card_favorite.yml` |

## Tests

| Classe / asset | Type | Scénarios |
|---|---|---|
| `OfferHooksTest` | Unit | Presave appelle validation |
| `OfferValidationManagerTest` | Unit | Budget, capacité, surface, agent, unicité référence |
| `OfferReferenceGeneratorTest` | Unit | Segments, alias, longueur |
| `OfferReferenceManagerTest` | Unit | Mode auto/manuel |
| `offer_reference.feature` | Behat | Matrice génération références |
| `offer_validation.feature` | Behat | Validations métier |
| `e2e_offer_reference.sh` | E2E bash | Régression références |
| `e2e_offer_reference_uniqueness.sh` | E2E bash | Unicité / collision |
| `e2e_offer_validation.sh` | E2E bash | Scénarios validation contrôlés |

Scripts recette QA (hébergés dans `bnp_admin`) :

```bash
cd src && composer test:manual-offer-val    # OFF-01→18, VAL-01→10
cd src && composer test:manual-offer-full   # Publication matrix §5.3
```

```bash
# Unit tests
cd src && vendor/bin/phpunit web/modules/custom/ps_offer/tests/src/Unit

# E2E référence
composer run test:offer-ref-e2e

# Behat
composer run test:behat -- --suite=ps_offer_reference --no-interaction
```

## Dépendances

### Modules PS

| Module | Rôle |
|---|---|
| `ps_core` | Hub admin, permissions |
| `ps_dictionary` | Champs `ps_dictionary` (asset, operation, client, budget…) |
| `ps_feature` | `field_features` (type `feature`) |
| `ps_agent` | `field_primary_agent`, `field_secondary_agents` |
| `ps_surface` | `field_surfaces`, `field_divisions`, projection post-save |
| `ps_diagnostic` | `field_diagnostics*`, widget/formatters |
| `ps_favorite` | Cible favoris offer |
| `ps_media` | Formatters galerie/documents en view display |
| `bnp_editor` | Éditeur riche body |

### Drupal & contrib

`node`, `field`, `options`, `user`, `address`, `geofield`, `entity_reference_revisions`, `geocoder` (+ field/address/geofield), `media`, `datetime`, `link`, `text`, `views`, `content_translation`, `config_translation`, `pathauto`, `token`, `scheduler`, `metatag`, `schema_metatag`, `inline_entity_form`, `field_group`

**Couplage runtime sans `.info.yml` :** `ps_context` (matrice formulaire), `ps_migrate` (import XML), `ps_search` (indexation).

## Installation & reset

```bash
drush pmu ps_offer -y
drush pm:enable ps_offer -y
drush cr
```

En cas de `PreExistingConfigException` après `pmu` : supprimer les objets actifs de `ps_offer/config/install` puis relancer `pm:enable`.

Activer aussi (selon besoin) : `ps_context`, `ps_migrate`, `ps_search`.

## Documentation technique

Voir [`docs/`](docs/) :

- [VALIDATION.md](docs/VALIDATION.md) — Hook OOP et règles de validation (matrix-aware)
- [CONTEXT_INTEGRATION.md](docs/CONTEXT_INTEGRATION.md) — Pont `ps_context`, publication UI
- [RECETTE.md](docs/RECETTE.md) — Scénarios OFF-* / VAL-* et scripts
- [FEATURE_VIEWS.md](docs/FEATURE_VIEWS.md) — Vue admin filtre par feature (itération 1)
- [OFFER_REFERENCE_ARCHITECTURE.md](docs/OFFER_REFERENCE_ARCHITECTURE.md) — Architecture références LOT 1/2/3

Références projet : `.ai/PROJECT_MATRIX.md`, `.ai/PROJECT_MODULES.md` §3.6.

## Offer full — Agent card labels (i18n)

The offer full Layout Builder uses formatter `ps_offer_agent_card` on `field_primary_agent`. UI strings default to English in `core.entity_view_display.node.offer.full` formatter settings:

- `consultant_label`, `contact_label`, `visit_title`, `visit_label`, `contact_dialog_options`

Edit defaults: **Structure → Content types → Offer → Manage display → Full → Layout → Primary agent → Formatter settings**.

Translate via **Configuration → Regional and language → Configuration translation → Content language configuration → Node type offer → Full** (requires `config_translation`).

Agent data (name, phone, avatar) is managed per agent at `/admin/ps/content/agent`. Avatar fallbacks: `/admin/ps/config/agent/fallback`.

## À venir

- Alignement codes dictionnaire CRM (MAT-01 LOG→ENT, MAT-02 LOC/VEN vs RENT/SALE)
- Extension `EntityProtectionTrait` sur entité offer (DEC-0027)
- Tests kernel sur sync diagnostics et projection surfaces
