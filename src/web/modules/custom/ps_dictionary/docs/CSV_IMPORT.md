# Import CSV des dictionnaires — Documentation technique

## Vue d'ensemble

Le système d'import CSV permet d'initialiser ou de mettre à jour les entrées de dictionnaire en masse. Il est composé d'une interface de service, d'une implémentation et d'une commande Drush.

```
DictionaryCsvImporterInterface
    ↑ implémentée par
DictionaryCsvImporter (service)
    ↑ appelé par
DictionaryImportCommands (Drush)
```

---

## Format CSV

Le fichier CSV doit comporter une ligne d'en-tête avec ces colonnes obligatoires (dans n'importe quel ordre) :

```csv
type,code,label,weight
asset_type,BUR,Bureau,1
asset_type,COW,Coworking,2
budget_period,YEAR,Annuel,1
```

| Colonne | Type | Règle |
|---|---|---|
| `type` | string | Machine name d'un `ps_dictionary_type` existant |
| `code` | string | Code métier — normalisé en MAJUSCULES automatiquement |
| `label` | string | Libellé humain |
| `weight` | integer | Ordre d'affichage |

Colonnes optionnelles pour traductions : `label_{langcode}` (ex: `label_fr`, `label_en`).

Exemple :

```csv
type,code,label,weight,label_fr
asset_type,BUR,Office,1,Bureau
asset_type,SHOP,Retail unit,2,Commerce
```

Le `label` reste la valeur source (anglais), et chaque colonne `label_{langcode}` alimente les overrides de config Drupal pour la langue ciblée.

**Contraintes** : le type doit exister en base. Les colonnes manquantes ou vides (type, code, label) provoquent un skip avec message d'erreur.

---

## Comportement de l'import

L'identifiant unique d'une entrée est `{type}.{code_lower}` (ex : `asset_type.bur`).

| Cas | Comportement |
|---|---|
| Entrée inexistante | Création via `EntityStorage::create()` + `save()` |
| Entrée existante | Mise à jour de `label` et `weight` via `set()` + `save()` |
| Type inexistant | Skip + message d'erreur dans `errors[]` |
| Ligne incomplète | Skip + message d'erreur dans `errors[]` |
| Colonne `label_{langcode}` avec langue absente | Traduction ignorée + warning dans `errors[]` |

**Retour** : `array{imported: int, skipped: int, errors: string[]}`.

---

## Commande Drush

```bash
# Import depuis le CSV embarqué dans le module
drush ps:dictionary:import

# Import depuis un fichier externe
drush ps:dictionary:import /chemin/vers/fichier.csv

# Import filtré par type (autres types skippés)
drush ps:dictionary:import --type=asset_type

# Combiné
drush ps:dictionary:import /tmp/import.csv --type=mandate_type
```

Le fichier embarqué est résolu automatiquement depuis `DictionaryImportCommands::defaultFixturePath()` :

```php
dirname(__DIR__, 2) . '/data/dictionary_entries.csv'
// → web/modules/custom/ps_dictionary/data/dictionary_entries.csv
```

---

## Fixture embarquée

`data/dictionary_entries.csv` contient les 38 entrées de référence pour les 9 types livrés avec le module. Les types sont installés par défaut, puis les entrées sont importées via CSV (BO ou Drush).

Pour ajouter un type :
1. Créer `config/install/ps_dictionary.type.{machine_name}.yml`
2. Créer les `config/install/ps_dictionary.entry.{type}.{code_lower}.yml` correspondants
3. Ajouter les lignes dans `data/dictionary_entries.csv`
4. Réinstaller le module **ou** lancer `drush ps:dictionary:import`

---

## Extension du service

Pour personnaliser l'import (ex: import depuis une API, un XML), créer une implémentation alternative de `DictionaryCsvImporterInterface` et re-déclarer l'alias dans `*.services.yml` :

```yaml
Drupal\mon_module\Service\MonImporter:
  class: Drupal\mon_module\Service\MonImporter
  arguments: ['@entity_type.manager']

Drupal\ps_dictionary\Service\DictionaryCsvImporterInterface:
  alias: Drupal\mon_module\Service\MonImporter
```

La commande Drush utilisera automatiquement la nouvelle implémentation par injection de dépendance.

---

## Tests

Les 10 cas de test unitaires couvrent :
- Création d'entrée inexistante
- Mise à jour d'entrée existante (label + weight)
- Skip si type inconnu
- Erreur si fichier manquant
- Erreur si fichier vide
- Erreur si colonnes manquantes
- Filtre par `--type`
- Skip si code ou label vide
- Import des traductions via `label_{langcode}`
- Warning quand une langue de traduction n'est pas disponible

Exécution recommandée en Docker (conteneur `php`) :

```bash
docker compose -f docker/docker-compose.yml -f docker/docker-compose.wsl.yml exec -T php \
  sh -lc 'cd /var/www/html && php ./vendor/bin/phpunit -c web/core/phpunit.xml.dist web/modules/custom/ps_dictionary/tests/src/Unit/DictionaryCsvImporterTest.php'
```

Voir [`tests/src/Unit/DictionaryCsvImporterTest.php`](../tests/src/Unit/DictionaryCsvImporterTest.php).
