# Import CSV du catalogue Features — Documentation technique

## Vue d'ensemble

L'import CSV permet d'initialiser ou de mettre à jour le catalogue de définitions Features (`fb_feature_definition`) à partir d'un fichier **métier** préparé par les équipes contenu.

```
FeatureCatalogueCsvImporterInterface
    ↑ implémentée par
FeatureCatalogueCsvImporter (service)
    ↑ appelé par
FeatureCatalogueImportForm (BO) + FeatureCatalogueImportCommands (Drush)
```

Le mapping libellés métier → champs techniques est centralisé dans `FeatureCatalogueCsvMapper`.

---

## Fichiers de référence

| Fichier | Usage |
|---|---|
| `data/feature_catalogue_import.template.csv` | Exemples métier + traductions |
| `data/feature_catalogue_import.csv` | Grille vide à remplir |

---

## Format CSV métier

### Colonnes obligatoires

```csv
code,categorie,libelle,type_valeur
TEC_SURFACE_TOTALE,Équipements,Total surface,Nombre
```

| Colonne | Description |
|---|---|
| `code` | Référence stable alignée sur le CRM (`CODE_ELEMENT`) |
| `categorie` | Libellé métier (4 valeurs autorisées) |
| `libelle` | Libellé par défaut (anglais recommandé) |
| `type_valeur` | Type de donnée en langage métier |

### Colonnes optionnelles

| Colonne | Description | Défaut |
|---|---|---|
| `description` | Description par défaut | vide |
| `unite` | Unité pour les types « Nombre » | vide |
| `ordre` | Ordre d'affichage | `0` |
| `filtre_recherche` | `Oui` / `Non` | `Non` |

### Traductions (même ligne)

Colonnes optionnelles :

- `libelle_{langcode}` — ex. `libelle_fr`, `libelle_de`
- `description_{langcode}` — ex. `description_fr`, `description_de`

Langues projet : `fr`, `de`, `es`, `it`, `lb`, `nl`, `pl`.

Exemple :

```csv
code,categorie,libelle,description,type_valeur,unite,ordre,filtre_recherche,libelle_fr,description_fr
TEC_SURFACE_TOTALE,Équipements,Total surface,Total lettable surface,Nombre,m²,5,Oui,Surface totale,Surface totale exploitable
```

---

## Valeurs autorisées

### Catégories (`categorie`)

| Valeur CSV | Groupe technique |
|---|---|
| `Équipements` | `equipements` |
| `Services` | `prestations_de_service` |
| `État du bâtiment` | `type_etat_du_batiment` |
| `Informations complémentaires` | `informations_complementaires` |

### Types de valeur (`type_valeur`)

| Valeur CSV | Type technique |
|---|---|
| `Indicateur` | `flag` |
| `Oui/Non` | `yes_no` |
| `Nombre` | `numeric` |
| `Texte` | `text` |
| `Date` | `date` |

### Filtre recherche (`filtre_recherche`)

Valeurs reconnues pour **Oui** : `Oui`, `Yes`, `1`, `true`, `vrai`. Toute autre valeur = Non.

---

## Comportement de l'import

| Élément | Règle |
|---|---|
| ID définition | `normalize(code)` — ex. `TEC_SURFACE_TOTALE` → `tec_surface_totale` |
| Code stocké | `CODE` en majuscules |
| Clé unique | Le `code` (une définition par code CRM) |
| Création | Si la définition n'existe pas |
| Mise à jour | Si la définition existe déjà (upsert) |
| Groupe inexistant | Ligne ignorée + message d'erreur |
| Catégorie inconnue | Ligne ignorée + message d'erreur |
| Traduction langue absente | Warning, import de la ligne maintenu |
| Dry run | Validation sans `save()` |

**Retour** : `array{imported: int, skipped: int, errors: string[], dry_run: bool}`.

---

## Back-office

| Route | Chemin | Permission |
|---|---|---|
| `ps_feature.catalogue_import_form` | `/admin/ps/content/features/import` | `administer ps features` |
| `ps_feature.catalogue_import_template` | `/admin/ps/content/features/import/template` | `administer ps features` |

Accès également via le bouton **Import CSV** sur la liste des Features (`/admin/ps/content/features`).

Options du formulaire :

1. Télécharger le template CSV
2. Remplir une ligne par caractéristique
3. Uploader le fichier préparé
4. Dry run optionnel (validation seule)
5. Importer le CSV

---

## Commande Drush

```bash
# Import depuis le template embarqué
drush ps:features:import-catalogue

# Import depuis un fichier externe
drush ps:features:import-catalogue /chemin/vers/catalogue.csv

# Validation sans persistance
drush ps:features:import-catalogue /chemin/vers/catalogue.csv --dry-run
```

Alias : `ps-fci`

---

## Pourquoi un import custom (et pas Migrate) ?

| Critère | Migrate | Import custom |
|---|---|---|
| Public cible | Ops techniques | **Métier + ops** |
| Format source | Technique | **CSV métier** |
| Traductions config | Lourd | **Pattern dictionnaire** |
| BO upload + dry-run | Difficile | **Natif** |
| Cycle de vie | Pipeline CRM récurrent | **Initialisation catalogue** |

Référence : `ps_dictionary` — `DictionaryCsvImporter`.

---

## Tests

```bash
cd src && vendor/bin/phpunit -c web/core/phpunit.xml.dist \
  web/modules/custom/ps_feature/tests/src/Unit/Service/FeatureCatalogueCsvMapperTest.php \
  web/modules/custom/ps_feature/tests/src/Unit/Service/FeatureCatalogueCsvImporterTest.php
```

---

## Workflow recommandé

1. Install Drupal + module `ps_feature` (4 groupes canoniques : équipements, services, état du bâtiment, informations complémentaires)
2. Remplir `feature_catalogue_import.csv` ou copier le template
3. Dry run BO ou Drush
4. Import définitif
5. Configurer les mappings `CODE_GROUP` CRM (évolution `ps_migrate`)
6. Lancer l'import XML offres (valeurs uniquement)
