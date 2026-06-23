# Module `ps_dictionary`

> Statut : 🟢 Stable

Dictionnaires métier configurables pour l'écosystème Property Search : types, entrées CRUD, type de champ Drupal natif, résolution de codes, et import CSV.

## Responsabilité

`ps_dictionary` fournit un système de référentiels métier basé sur des config entities Drupal. Il expose un type de champ `ps_dictionary` réutilisable dans tous les modules PS, un service de résolution `DictionaryResolver`, et une commande Drush d'import CSV pour initialiser les données.

Ce module **ne gère pas** de contenu, de workflows ou de logique métier spécifique à un domaine. Il est un fournisseur de données de référence.

## Fonctionnalités

- 2 config entity types : `ps_dictionary_type` (types de dictionnaires) et `ps_dictionary_entry` (entrées)
- BO CRUD complet sous `/admin/ps/structure/dictionary` (liste, add, edit, delete)
- Gestion du tri par `weight` et suppression par lot dans un formulaire unifié (`DictionaryEntryListBuilder`)
- Autocomplete JSON pour les entrées (`/ps-dictionary/autocomplete/{type}`)
- Type de champ Drupal `ps_dictionary` avec 3 widgets et 1 formatter
- Service `DictionaryResolver` : résolution code ↔ label, validation, liste triée
- Service `DictionaryCsvImporter` : import/mise à jour depuis fichier CSV, avec traductions optionnelles `label_{langcode}`
- Interface BO d'import CSV (`/admin/ps/structure/dictionary/import`) avec source fixture ou upload
- Commande Drush `ps:dictionary:import` avec support du filtre par type
- 9 types livrés en `config/install/` (les entrées sont importées via CSV)

## Architecture

### Entités

| Type Drupal | Machine name | Type | Description |
|---|---|---|---|
| Config | `ps_dictionary_type` | ConfigEntityType | Type de dictionnaire (ex : `asset_type`) |
| Config | `ps_dictionary_entry` | ConfigEntityType | Entrée de dictionnaire (ex : BUR → Bureau) |

**Structure d'une entrée** : `id` (`type.code_lower`), `type`, `code` (uppercase), `label`, `weight`.

### Services

| Service ID | Classe | Rôle |
|---|---|---|
| `ps_dictionary.resolver` | `DictionaryResolver` | Résolution code/label, validation, liste triée |
| `Drupal\ps_dictionary\Service\DictionaryCsvImporter` | `DictionaryCsvImporter` | Import CSV create/update |
| `Drupal\ps_dictionary\Service\DictionaryCsvImporterInterface` | *(alias)* | Interface pour injection |

### Plugins

| Type | Plugin ID | Classe | Description |
|---|---|---|---|
| FieldType | `ps_dictionary` | `DictionaryItemFieldType` | Champ de code dictionnaire (varchar 16), settings: `dictionary_type` |
| FieldWidget | `ps_dictionary_options_select` | `DictionaryOptionsSelectWidget` | Select list d'options |
| FieldWidget | `ps_dictionary_options_buttons` | `DictionaryOptionsButtonsWidget` | Groupe de boutons |
| FieldWidget | `ps_dictionary_autocomplete` | `DictionaryItemWidget` | Textfield avec autocomplete |
| FieldFormatter | `ps_dictionary_formatter` | `DictionaryItemFormatter` | Affiche le label résolu via `DictionaryResolver` |

### Controllers

| Classe | Route | Rôle |
|---|---|---|
| `DictionaryEntryListController` | `ps_dictionary.entry_collection` | Liste des entrées filtrées par type + formulaire batch |
| `DictionaryAutocompleteController` | `ps_dictionary.autocomplete` | JSON autocomplete par code ou label |

### Forms

| Classe | Rôle |
|---|---|
| `DictionaryTypeForm` | CRUD type (label + machine name) |
| `DictionaryEntryForm` | CRUD entrée (type, code uppercase, label, weight) avec vérification de doublon |
| `DictionaryEntryDeleteForm` | Confirmation de suppression |
| `DictionaryImportForm` | Import CSV en BO (fixture par défaut ou fichier uploadé) |

### List builders

| Classe | Rôle |
|---|---|
| `DictionaryTypeListBuilder` | Liste des types + opérations de navigation |
| `DictionaryEntryListBuilder` | Liste filtrée des entrées avec `weight` draggable + suppression batch dans le meme formulaire |

### Commandes Drush

| Commande | Alias | Options | Description |
|---|---|---|---|
| `ps:dictionary:import [file]` | `ps-di` | `--type=TYPE` | Import CSV depuis le fixture embarqué ou un fichier custom |

**Format CSV requis** (avec header) :

```csv
type,code,label,weight
asset_type,BUR,Bureau,1
```

Colonnes de traduction optionnelles : `label_{langcode}` (ex : `label_fr`, `label_en`).

## Routes & Accès

| Route | Chemin | Permission |
|---|---|---|
| `ps_dictionary.type_collection` | `/admin/ps/structure/dictionary` | `manage ps_dictionary` |
| `ps_dictionary.import_form` | `/admin/ps/structure/dictionary/import` | `manage ps_dictionary` |
| `ps_dictionary.entry_collection` | `/admin/ps/structure/dictionary/manage/{type}` | `manage ps_dictionary` |
| `ps_dictionary.autocomplete` | `/ps-dictionary/autocomplete/{type}` | publique (lecture) |

## Dictionnaires livrés (`config/install/`)

| Type | Code machine | Entrées |
|---|---|---|
| Types d'actifs | `asset_type` | BUR, COW, ENT, ACT, COM, TER |
| Types d'opérations | `operation_type` | RENT, SALE |
| Unités de surface | `surface_unit` | M2, HA |
| Devises | `currency` | EUR |
| Périodes budgétaires | `budget_period` | YEAR, MONTH, ONE_SHOT |
| Unités budgétaires | `budget_unit` | PER_M2, PER_POSTE, GLOBAL |
| Types de mandats | `mandate_type` | SIM, EX, CEX, CPR, PRE, TRI, DEL |
| Classes DPE | `dpe_class` | A, B, C, D, E, F, G |
| Classes GES | `ges_class` | A, B, C, D, E, F, G |

**Total installé par défaut** : 9 types, 0 entrée.

Les entrées peuvent ensuite être chargées:
- via l'interface BO d'import CSV (`Import CSV` sur la page des types)
- ou via la commande Drush `ps:dictionary:import`

**Note** : les départements français ne sont plus un type de dictionnaire — le référentiel géo est `ps_search` (`data/geo_zones/fr.yml`). Sur sites existants, `ps_dictionary_update_10001()` supprime le type legacy `department`.

## Tests

| Classe | Type | Scénarios couverts |
|---|---|---|
| `DictionaryResolverTest` | Unit | resolveLabel, resolveCode, all (tri weight), isValid (4 tests, 14 assertions) |
| `DictionaryCsvImporterTest` | Unit | create, update, filtre type, fichier manquant, header manquant, colonnes manquantes, code/label vides, import traductions `label_{langcode}`, langue absente (10 tests, 45 assertions) |

**Résultat** : 14 tests, 59 assertions, 0 dépréciations PHPUnit.

### Régression E2E (Behat + moteur bash/Drush)

- Behat suite: `ps_dictionary`
- Feature file: `tests/behat/features/dictionary_crud.feature`
- Script moteur: `tests/e2e_dictionary.sh`

Commandes:

```bash
# Suite Behat dédiée ps_dictionary
./vendor/bin/behat --config behat.yml.dist --suite=ps_dictionary --strict --colors

# Exemple direct via moteur bash/Drush
bash web/modules/custom/ps_dictionary/tests/e2e_dictionary.sh ensure_type test_type "" "Test Type" 0
bash web/modules/custom/ps_dictionary/tests/e2e_dictionary.sh create_entry test_type FOO "Foo Label" 0
bash web/modules/custom/ps_dictionary/tests/e2e_dictionary.sh resolve_label test_type FOO
```

Couverture E2E actuelle:

- CRUD type/entry (create, update, delete)
- Contrôle de doublon code entrée
- Résolution service `DictionaryResolver` (code -> label, code inconnu -> `NULL`)
- Endpoint API autocomplete `/ps-dictionary/autocomplete/{type}`

## Dépendances

- `drupal:system` — API système
- `drupal:field` — API de champs (FieldType plugin)
- `drupal:options` — OptionsProviderInterface
- `ps_core:ps_core` — Hub admin, permissions sectorielles

## Installation & initialisation

```bash
# Installation du module (charge les 9 types)
drush pm:enable ps_dictionary -y

# Import des entrées depuis le CSV embarqué
drush ps:dictionary:import

# Import filtré par type
drush ps:dictionary:import --type=asset_type

# Import depuis un fichier custom
drush ps:dictionary:import /chemin/vers/mon-fichier.csv
```

## Documentation technique

Voir [`docs/`](docs/) pour :

- [FIELD_TYPE_PLUGIN.md](docs/FIELD_TYPE_PLUGIN.md) — Architecture du plugin de champ `ps_dictionary`
- [CSV_IMPORT.md](docs/CSV_IMPORT.md) — Workflow d'import CSV et extension du service
