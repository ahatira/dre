# Module `ps_offer`

> Statut : 🟡 En développement — modèle de données actif, cycle de réinstallation validé

Modèle de données et validations métier pour les offres immobilières BNPPRE.

## Responsabilité

`ps_offer` définit le type de contenu `offer` (node), ses champs, ses règles de validation et son hook `node_presave` en OOP. Il constitue le cœur du modèle de données offre et s'appuie sur `ps_dictionary` pour les champs de codes métier.

Ce module conserve l'édition des contenus offre sous `/admin/content` et expose aussi une configuration dédiée des références sous `/admin/ps/config/offer-reference`.

## Fonctionnalités

- Type de contenu `offer` (node bundle) avec révisions activées
- 25 champs custom configurés (form display + view display)
- SEO handled via Metatag defaults for bundle `offer` (no custom `field_seo_*`)
- Bundle-level integration for translation, metatag defaults, pathauto pattern, and scheduler settings
- Validation métier à la pré-sauvegarde via hook OOP `node_presave`
- 2 règles de validation : budget, agent principal
- Dépublication automatique si aucun agent principal n'est défini
- Vue admin installée `ps_offer_by_feature` pour filtrer les offres par feature ID
- Configurable offer reference pattern baseline (LOT 1) with BO-managed pattern entities and generator service
- Reference alias sets (LOT 2) to decouple reference codes from canonical dictionary values
- LOT 3 delivered: boolean auto/manual mode (`field_reference_auto`), widget toggle and generated preview on `field_reference`
- Automated regression script for reference mode: `tests/e2e_offer_reference.sh`
- Behat smoke scaffold for offer reference regression: `tests/behat/features/offer_reference.feature`
- Behat matrix baseline for reference generation aliases: `LOC/BUR`, `VEN/COM`, `LOC/ACT`, `LOC/ENT`, `VEN/BUR`, `VEN/COW`

## Architecture

### Entités

| Entité Drupal | Bundle | Type | Description |
|---|---|---|---|
| `node` | `offer` | Content | Offre immobilière avec révisions |

### Champs du bundle `offer`

| Champ | Type Drupal | Obligatoire | Description |
|---|---|---|---|
| `field_asset_type` | `ps_dictionary` (type: `asset_type`) | Oui | Type d'actif (BUR, COW, LOG…) |
| `field_reference` | `string` | Non | Business-facing offer reference |
| `field_reference_auto` | `boolean` | Non | Enables automatic generation when checked, manual edition when unchecked |
| `field_business_id` | `string` | Non | Business identifier from source systems |
| `field_technical_id` | `string` | Non | Technical identifier used by integrations |
| `field_operation_type` | `ps_dictionary` (type: `operation_type`) | Non | Operation type (LOC, VEN) |
| `field_budget_value` | `decimal` (10,2) | Non | Valeur budgétaire |
| `field_budget_period` | `list_string` | Non | Période budgétaire (YEAR, MONTH, ONE_SHOT) |
| `field_budget_unit` | `ps_dictionary` (type: `budget_unit`) | Non | Budget unit (PER_M2, PER_POSTE, GLOBAL) |
| `field_budget_currency` | `ps_dictionary` (type: `currency`) | Non | Budget currency (EUR, ...) |
| `field_budget_ht_hc` | `list_string` | Non | Budget basis (HT / HC) |
| `field_address` | `string_long` | Non | Full address |
| `field_city` | `string` | Non | City |
| `field_department` | `string` | Non | Department |
| `field_region` | `string` | Non | Region |
| `field_media_photos` | `entity_reference` (media, multiple) | Non | Photos attached to the offer |
| `field_media_videos` | `entity_reference` (media, multiple) | Non | Videos attached to the offer |
| `field_virtual_tour_url` | `link` | Non | External virtual tour URL |
| `field_geo` | `geofield` | Non | Géolocalisation (WKT) |
| `field_primary_agent` | `entity_reference` (user) | Non | Agent principal (auto-dépublication si absent) |
| `field_secondary_agents` | `entity_reference` (user, multiple) | Non | Secondary agents linked to the offer |
| `field_divisible` | `boolean` | Non | Offre divisible |
| `field_source_checksum` | `string` | Non | Checksum source (déduplication import) |
| `field_source_system` | `string` | Non | Name of source system that provided the offer |
| `field_last_imported_at` | `datetime` | Non | Timestamp of latest source import |
| `field_import_id` | `string` | Non | Source import execution identifier |

### Services

| Service ID | Classe | Interface | Rôle |
|---|---|---|---|
| `Drupal\ps_offer\Service\OfferValidationManager` | `OfferValidationManager` | `OfferValidationManagerInterface` | Orchestrateur des règles de validation |
| `Drupal\ps_offer\Service\OfferReferenceGenerator` | `OfferReferenceGenerator` | n/a | Builds references from configurable pattern segments |
| `Drupal\ps_offer\Service\OfferReferencePatternResolver` | `OfferReferencePatternResolver` | n/a | Resolves the active reference pattern for a bundle |
| `Drupal\ps_offer\Service\OfferReferenceAliasResolver` | `OfferReferenceAliasResolver` | `OfferReferenceAliasResolverInterface` | Resolves BO-managed reference aliases before fallback to canonical codes |
| `Drupal\ps_offer\Service\OfferReferenceManager` | `OfferReferenceManager` | `OfferReferenceManagerInterface` | Orchestrates auto/manual mode and pre-save reference generation |

### Hooks OOP

| Classe | Hook | Méthode | Rôle |
|---|---|---|---|
| `OfferHooks` | `node_presave` | `nodePresave(NodeInterface)` | Applies reference mode generation then triggers `OfferValidationManager::apply()` |

### Règles de validation (`OfferValidationManager`)

| Méthode privée | Règle |
|---|---|
| `validateBudget()` | Si `field_budget_period` est défini et `field_budget_value` absent/≤ 0 : blocant en publication, warning en brouillon |
| `validatePrimaryAgent()` | Si aucun agent principal : dépublication du node + message d'avertissement |

## Configuration initiale (`config/install/`)

| Fichier | Contenu |
|---|---|
| `node.type.offer.yml` | Définition du bundle offer |
| `field.storage.node.field_*.yml` | Définitions de stockage de champs (inclut `field_reference_auto`) |
| `field.field.node.offer.field_*.yml` | Configurations d'instance de champs (inclut `field_reference_auto`) |
| `core.entity_form_display.node.offer.default.yml` | Form display (widgets par champ) |
| `core.entity_view_display.node.offer.default.yml` | Main view display (formatters par champ) |
| `core.entity_view_display.node.offer.teaser.yml` | Compact card preview used by favorites fallback and teaser-like renders |
| `core.entity_view_mode.node.card_favorite.yml` | Dedicated view mode for favorites cards |
| `core.entity_view_display.node.offer.card_favorite.yml` | Favorites card display: first gallery image + city/address + surface + budget |
| `ps_favorite.target.node.offer.yml` | Favorite target config entity enabling favorites on offer content |
| `metatag.metatag_defaults.node__offer.yml` | Bundle-specific Metatag defaults for offer nodes |
| `language.content_settings.node.offer.yml` | Content translation settings for `offer` |
| `pathauto.pattern.offer.yml` | Dedicated URL alias pattern for `offer` |
| `views.view.ps_offer_by_feature.yml` | Admin view to filter offers by feature ID |
| `ps_offer.reference_pattern.default.yml` | Default active offer reference pattern installed with the module |

**Total** : 86 fichiers de configuration.

## Tests

| Classe | Type | Scénarios couverts |
|---|---|---|
| `OfferHooksTest` | Unit | `apply()` appelé exactement 1 fois sur `node_presave` |
| `OfferValidationManagerTest` | Unit | Budget invalide (publication vs brouillon), dépublication sans agent, no-op hors bundle `offer` |
| `OfferReferenceGeneratorTest` | Unit | Segment-based reference generation, fallback mapping, length validation |
| `OfferReferenceManagerTest` | Unit | Boolean auto/manual mode behavior (`field_reference_auto`) and generation application |

**Résultat** : `OK (15 tests, 43 assertions)` sur `tests/src/Unit`.

## Dépendances

- `ps_core:ps_core` — Hub admin, permissions
- `ps_dictionary:ps_dictionary` — Type de champ `ps_dictionary` pour `field_asset_type`
- `drupal:node` — Content entity node
- `drupal:field` — API de champs
- `drupal:options` — List string pour `field_budget_period`
- `drupal:user` — Entity reference user pour `field_primary_agent`
- `drupal:address` — Champs d'adresse
- `drupal:geofield` — Géolocalisation
- `drupal:media` — Champs médias photo/vidéo
- `drupal:datetime` — Tracking import timestamps
- `drupal:link` — URL visite virtuelle
- `drupal:content_translation` — Translatable offer content settings
- `drupal:pathauto` — URL alias automation for offer nodes
- `drupal:scheduler` — Publication/unpublication scheduling for offer nodes
- `drupal:metatag` — Default SEO metadata management
- `drupal:schema_metatag` — Schema.org metatag extensions

## Installation & reset

```bash
# Désinstallation propre (supprime les configs et données)
drush pmu ps_offer -y

# Réinstallation fraîche (import config/install/)
drush pm:enable ps_offer -y
```

Cycle de réinstallation validé sur environnement Docker:

- `drush pmu ps_offer -y`
- purge des objets `config/install` restés actifs
- `drush pm:enable ps_offer -y`
- `drush cr`

Point d'attention: si `pm:enable` remonte une `PreExistingConfigException`, supprimer les objets de configuration actifs fournis par `ps_offer/config/install` puis relancer l'activation.

## Exécution des tests de régression référence

```bash
# E2E script (manual + auto + DB assertions)
composer run test:offer-ref-e2e

# Cross-module orchestration (Behat + bash/Drush: ps_dictionary, ps_feature, ps_offer)
composer run test:regression:modules

# E2E script with explicit operation/asset matrix case
bash web/modules/custom/ps_offer/tests/e2e_offer_reference.sh 1 VEN COM

# Advanced uniqueness/collision scenario (logical concurrency)
bash web/modules/custom/ps_offer/tests/e2e_offer_reference_uniqueness.sh LOC BUR

# Controlled-failure business scenario: manual duplicate reference must be rejected
bash web/modules/custom/ps_offer/tests/e2e_offer_validation.sh 1 100 draft offer manual-duplicate

# Manual self-edit scenario: same offer keeps same manual reference (no false positive)
bash web/modules/custom/ps_offer/tests/e2e_offer_validation.sh 1 100 draft offer manual-self-edit

# Controlled-failure business scenario on publication: manual duplicate reference must be rejected
bash web/modules/custom/ps_offer/tests/e2e_offer_validation.sh 1 100 published offer manual-duplicate-published

# Logical parallel roundtrip on two existing offers (save back-and-forth)
bash web/modules/custom/ps_offer/tests/e2e_offer_reference_uniqueness.sh LOC BUR parallel-roundtrip-two

# Behat smoke (wrap du script E2E)
composer run test:behat -- --suite=ps_offer_reference --no-interaction
```


### Approche recommandée pour enrichir la couverture Behat (Matrice)

La matrice de scénarios Behat couvre actuellement :

| operation | asset |
|-----------|-------|
| LOC       | BUR   |
| VEN       | COM   |
| LOC       | ACT   |
| LOC       | ENT   |
| VEN       | BUR   |
| VEN       | COW   |

Pour ajouter un nouveau cas :
1. Ajouter une ligne dans les `Examples` de `offer_reference.feature`.
2. Vérifier que la paire operation/asset existe dans les dictionnaires actifs.
3. Exécuter la suite Behat :
	```bash
	composer run test:behat -- --suite=ps_offer_reference --no-interaction
	```

## Documentation technique

Voir [`docs/`](docs/) pour :

- [VALIDATION.md](docs/VALIDATION.md) — Architecture du hook OOP et des règles de validation
- [FEATURE_VIEWS.md](docs/FEATURE_VIEWS.md) — Itération 1 (Views) pour filtrer les offres par feature ID
- [OFFER_REFERENCE_ARCHITECTURE.md](docs/OFFER_REFERENCE_ARCHITECTURE.md) — Architecture de référence et état LOT 1/2/3

## Champs à venir (lots suivants)

Aucun lot prioritaire restant dans la phase en cours.
