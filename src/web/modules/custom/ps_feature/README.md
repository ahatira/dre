# Module PS Feature (ps_feature)

> Statut : 🟢 Stable

Moteur de gestion des caractéristiques métier (features) avec catalogue configurable, widgets BO et formatters de rendu.

## Responsabilité

Le module `ps_feature` fournit un champ `feature`, un catalogue de définitions/groupes configurables, et une chaîne complète de validation/normalisation/formatage des payloads.

Frontières explicites:
- le module ne gère pas l'import de données métier externes;
- le module n'impose pas le mapping business des offres (porté par les modules consommateurs).

## Fonctionnalités

- Field type `feature` (référence définition + payload JSON).
- Deux widgets de saisie BO:
	- `feature_default` (sélecteur + formulaire payload classique);
	- `feature_builder` (widget JS Feature Builder).
- Trois formatters:
	- `feature_default` (styles `default`, `compact`, `detailed`, `grouped`),
	- `feature_label_only`,
	- `feature_value_only`.
- 9 type drivers: `flag`, `yes_no`, `numeric`, `range`, `text`, `dictionary`, `list`, `date`, `taxonomy`.
- Ordonnancement grouped configurable (`group_order`) avec UI draggable en BO.
- Option de rendu flags:
	- `hide_disabled_flags`,
	- `show_flag_text` (afficher/masquer `Present/Absent`).

## Architecture

### Services

| Service ID | Classe | Rôle |
|---|---|---|
| `ps_feature.type_manager` | `Drupal\ps_feature\Service\FeatureTypeManager` | Plugin manager des type drivers |
| `plugin.manager.feature_type` | Alias | Alias Drupal pour injection dans widgets/formatters |
| `ps_feature.presave_subscriber` | `Drupal\ps_feature\EventSubscriber\OfferFeaturePresaveSubscriber` | Validation/normalisation en presave |
| `ps_feature.translation_route_subscriber` | `Drupal\ps_feature\Routing\TranslationRouteSubscriber` | Override des routes de traduction |
| `ps_feature.state_builder` | `Drupal\ps_feature\Service\FeatureBuilderStateBuilder` | Build du state initial du widget JS |
| `ps_feature.catalogue_builder` | `Drupal\ps_feature\Service\FeatureCatalogueBuilder` | Build du catalogue groupes/définitions |

### Entités

| Type | Machine name | Classe | Description |
|---|---|---|---|
| Content entity | `entity_offer_feature` | `Drupal\ps_feature\Entity\OfferFeature` | Feature autonome avec révisions |
| Config entity | `fb_feature_group` | `Drupal\ps_feature\Entity\FeatureGroup` | Groupe de features |
| Config entity | `fb_feature_definition` | `Drupal\ps_feature\Entity\FeatureDefinition` | Définition d'une feature |

### Plugins

| Plugin type | Plugin ID | Classe | Description |
|---|---|---|---|
| FieldType | `feature` | `Drupal\ps_feature\Plugin\Field\FieldType\FeatureItem` | Stockage d'une feature + payload |
| FieldWidget | `feature_default` | `Drupal\ps_feature\Plugin\Field\FieldWidget\FeatureWidget` | Widget Form API classique |
| FieldWidget | `feature_builder` | `Drupal\ps_feature\Plugin\Field\FieldWidget\FeatureBuilderWidget` | Widget JS Feature Builder |
| FieldFormatter | `feature_default` | `Drupal\ps_feature\Plugin\Field\FieldFormatter\FeatureFormatter` | Formatter principal |
| FieldFormatter | `feature_label_only` | `Drupal\ps_feature\Plugin\Field\FieldFormatter\FeatureLabelOnlyFormatter` | Label only |
| FieldFormatter | `feature_value_only` | `Drupal\ps_feature\Plugin\Field\FieldFormatter\FeatureValueOnlyFormatter` | Value only |

### Hooks OOP

| Classe | Hook | Méthode | Rôle |
|---|---|---|---|
| `Drupal\ps_feature\Hook\LocalTaskAlter` | `menu_local_tasks_alter` | `menuLocalTasksAlter()` | Ajuste les intitulés des onglets de traduction |

## Routes et accès

| Route | Chemin | Permission requise |
|---|---|---|
| `ps_feature.admin` | `/admin/ps/config/features` | `administer ps features` |
| `ps_feature.admin_overview` | `/admin/ps/config/features/overview` | `administer ps features` |
| `ps_feature.feature_types` | `/admin/ps/config/features/types` | `administer ps features` |
| `ps_feature.config_display_settings` | `/admin/ps/config/features/display` | `administer ps features` |
| `entity.fb_feature_definition.collection_by_group` | `/admin/ps/structure/features/{feature_group}/definitions` | `administer ps features` |

## Permissions

| Permission | Description |
|---|---|
| `administer ps features` | Manage feature groups and definitions |

## Configuration initiale (config/install)

Le dossier `config/install/` du module est actuellement vide.

## Tests

| Classe | Type | Scénarios couverts |
|---|---|---|
| `FeatureBuilderWidgetExtractionTest` | Kernel | Extraction/sanitation des valeurs depuis le state JSON |
| `FeatureBuilderWidgetSecurityTest` | Unit | Garde-fous sécurité (payload size, IDs invalides, JSON malformé) |
| `FeatureBuilderStateBuilderTest` | Unit | Construction du state initial depuis les items de champ |

Statut d'exécution dans cette passe: non exécutés.

### Régression E2E (Behat + moteur bash/Drush)

- Behat suite: `ps_feature`
- Feature file: `tests/behat/features/feature_crud.feature`
- Script moteur: `tests/e2e_feature.sh`

Commandes:

```bash
# Suite Behat dédiée ps_feature
./vendor/bin/behat --config behat.yml.dist --suite=ps_feature --strict --colors

# Exemple direct via moteur bash/Drush
bash web/modules/custom/ps_feature/tests/e2e_feature.sh ensure_group test_feature_group "" "Test Feature Group"
bash web/modules/custom/ps_feature/tests/e2e_feature.sh create_definition test_feature_group test_feature_total "Total Area" numeric total_area
bash web/modules/custom/ps_feature/tests/e2e_feature.sh feature_type_exists "" "" "" flag
```

Couverture E2E actuelle:

- Navigation UI vers `/admin/ps/config/features/overview`
- Vérification API service des type drivers (`plugin.manager.feature_type`)
- CRUD config entities `fb_feature_group` / `fb_feature_definition`
- Contrôle doublon de code par groupe
- Persistance des payload defaults pour drivers `dictionary`, `list`, `taxonomy`
- Vérification des valeurs scalaires (`dictionary_id`, `allow_custom`, `multiple`, `vocabulary_id`)
- Vérification des listes de payload (`options`, présence + cardinalité)
- Vérification catalogue builder: résolution `dictionary_id` (fallback `dictionary_type`) pour charger les options dictionary
- Vérification catalogue builder: options inline pour driver `list` (sans dictionnaire externe)
- Vérification filtrage catalogue par `required_asset_types` (définition visible pour asset autorisé, masquée sinon)
- Validation taxonomy: existence réelle du vocabulaire (`vocabulary_id`) lors de la création/édition de définition
- Test d'échec contrôlé Behat: rejet d'une définition taxonomy avec vocabulaire inexistant
- Test de non-régression round-trip `feature_builder`: build initial -> édition -> save -> reload (intégrité du state JSON)

## Dépendances

- `drupal:ps_core` — socle projet.
- `drupal:ps_dictionary` — options dictionary/list.

## Installation et reset

```bash
drush pm:enable ps_feature -y
drush cr
```

## Notes techniques

- [docs/FIELD_TYPE_GUIDE.md](docs/FIELD_TYPE_GUIDE.md) — Guide Field Type, widgets et formatters.
- [docs/TRANSLATION_GUIDE.md](docs/TRANSLATION_GUIDE.md) — Guide i18n et traduction.
- [docs/FEATURE_BUILDER_JS_GUIDE.md](docs/FEATURE_BUILDER_JS_GUIDE.md) — Documentation complète de la couche JavaScript Feature Builder.
