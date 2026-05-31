# Module PS Feature - Guide Complet

## Vue d'ensemble

Le module **ps_feature** gère les caractéristiques (features) des offres immobilières dans le système Property Search. Il fournit une architecture flexible et extensible pour définir différents types de caractéristiques avec leurs métadonnées, leurs valeurs par défaut, et leur **traduction multilingue avancée** (y compris pour les champs imbriqués).

## Architecture

### Entités de configuration

#### FeatureGroup (Groupe de caractéristiques)
Regroupe logiquement les caractéristiques (ex: "Aménagements", "Caractéristiques techniques").

**Propriétés** :
- `id` : Identifiant machine unique
- `label` : Nom affiché (traduisible)
- `description` : Description (traduisible)
- `asset_types` : Types d'actifs concernés
- `weight` : Ordre d'affichage

#### FeatureDefinition (Définition de caractéristique)
Définit un type de caractéristique avec ses métadonnées et valeurs par défaut.

**Propriétés** :
- `id` : Identifiant machine unique
- `code` : **Code d'import/export unique par groupe**
- `label` : Nom de la caractéristique (traduisible)
- `description` : Description (traduisible)
- `group` : Référence au groupe
- `type_driver` : Type de donnée (numeric, list, etc.)
- `payload_defaults` : Valeurs par défaut (certains champs traduisibles)
- `required_asset_types` : Types d'actifs où cette caractéristique est obligatoire
- `weight` : Ordre d'affichage

### Types de caractéristiques

| Type | Payload | Champs traduisibles | Exemple d'usage |
|------|---------|---------------------|-----------------|
| `numeric` | `{value: float, unit?: string}` | `unit` | Surface en m² |
| `range` | `{min: float, max: float, unit?: string}` | `unit` | Fourchette de prix en € |
| `text` | `{value: string}` | - | Notes additionnelles |
| `flag` | `{present: boolean}` | - | Rénové récemment |
| `yes_no` | `{value: boolean}` | - | Disponible immédiatement |
| `date` | `{value: string}` | - | Date de construction |
| `dictionary` | `{code: string}` | - | Statut (actif, vendu, etc.) |
| `taxonomy` | `{tid: int}` | - | Tags taxonomiques |
| `list` | `{codes: array}` | Options | Équipements (Wi-Fi, Parking, etc.) |

## 🌍 Système de traduction avancée

### Problématique

Le système de traduction standard de Drupal (`config_translation`) gère bien les champs simples (label, description), mais **ne peut pas traduire les champs imbriqués** dans les structures comme `payload_defaults`.

**Exemple** : Pour une caractéristique de type `numeric` :
```yaml
payload_defaults:
  unit: "m²"  # Ce champ doit être traduisible ("sq ft" en anglais)
  min: 0
  max: 1000
```

### Solution implémentée

Le module utilise une **architecture de traduction personnalisée** en trois couches :

#### 1. Schema de configuration

`config/schema/ps_feature.schema.yml` déclare les champs imbriqués comme traduisibles :

```yaml
ps_feature.feature_definition.*:
  mapping:
    label: {type: label, translatable: true}
    description: {type: label, translatable: true}
    payload_defaults:
      type: mapping
      mapping:
        unit: {type: string, translatable: true}  # Pour numeric/range
        options:
          type: mapping
          translatable: true  # Pour list (toutes les options)
```

#### 2. Formulaires de traduction personnalisés

**FeatureDefinitionTranslationForm** étend `ConfigTranslationFormBase` et :

1. Détecte le `type_driver` de la feature
2. Charge les valeurs originales via `config.storage` (sans overrides de langue)
3. Affiche les champs appropriés selon le type :

**Pour type `numeric` ou `range`** :
```php
$form['payload_defaults_container']['unit'] = [
  '#type' => 'textfield',
  '#title' => $this->t('Unit'),
  '#default_value' => $translation_data['payload_defaults']['unit'] ?? '',
  '#description' => $this->t('Original: @value', [
    '@value' => $source_data['payload_defaults']['unit'] ?? '',
  ]),
];
```

**Pour type `list`** :
```php
// Affiche un champ pour chaque option (wifi, parking, etc.)
foreach ($source_data['payload_defaults']['options'] as $key => $original_label) {
  $form['payload_defaults_container']["option_{$key}"] = [
    '#type' => 'textfield',
    '#title' => $this->t('Option: @key', ['@key' => $key]),
    '#default_value' => $translation_options[$key] ?? '',
    '#description' => $this->t('Original: @value', ['@value' => $original_label]),
  ];
}
```

4. Sauvegarde les traductions dans le config override de langue :
```php
$translation_config->set('payload_defaults.unit', $container_values['unit']);
$translation_config->save();
```

#### 3. RouteSubscriber

`TranslationRouteSubscriber` (priorité `-300`) intercepte et remplace les routes générées par `config_translation` :

```php
protected function alterRoutes(RouteCollection $collection): void {
  if ($route = $collection->get('config_translation.item.add.entity.fb_feature_definition.edit_form')) {
    $route->setDefault('_form', '\Drupal\ps_feature\Form\FeatureDefinitionTranslationForm');
  }
  // Idem pour edit, et pour FeatureGroup
}
```

La priorité `-300` est **critique** : elle doit être plus basse que celle de `config_translation` (`-217`) pour que l'override fonctionne.

### Types avec traduction de champs imbriqués

#### Numeric et Range : Le champ `unit`

```yaml
# Original (français)
payload_defaults:
  unit: "m²"

# Traduction anglaise
payload_defaults:
  unit: "sq ft"
```

#### List : Toutes les options

```yaml
# Original (français)
payload_defaults:
  options:
    wifi: "Wi-Fi"
    parking: "Parking"
    climatisation: "Climatisation"

# Traduction anglaise
payload_defaults:
  options:
    wifi: "Wi-Fi"
    parking: "Parking"
    climatisation: "Air Conditioning"
```

### Navigation et interface

Chaque entité dispose de **3 onglets automatiques** :

1. **Modifier** : Formulaire d'édition principal
2. **Traduire** : Interface de traduction (générée ou custom)
3. **Devel** : Outils de développement (si module activé)

Les titres d'onglets sont simplifiés via `LocalTaskAlter` :
```php
#[Hook('menu_local_tasks_alter')]
public function menuLocalTasksAlter(array &$data, string $route_name): void {
  if (isset($data['tabs'][0]['config_translation.local_tasks:entity.fb_feature_definition...'])) {
    $data['tabs'][0][...]['#link']['title'] = new TranslatableMarkup('Translate');
  }
}
```

## Validation des données

### Validation d'unicité du code

Le champ `code` doit être **unique par groupe** :

```php
// Dans FeatureDefinitionForm::validateForm()
$query = $storage->getQuery()
  ->condition('group', $group)
  ->condition('code', $code)
  ->accessCheck(FALSE);

if (!$entity->isNew()) {
  $query->condition('id', $entity->id(), '<>');
}

if (!empty($query->execute())) {
  $form_state->setErrorByName('code', 
    'Le code "@code" est déjà utilisé par une autre caractéristique dans ce groupe.');
}
```

### Validation par type de feature

#### Dictionary et List

Vérifie que les codes existent dans `ps_dictionary` :
```php
$entry_storage = \Drupal::entityTypeManager()->getStorage('ps_dictionary_entry');
$query = $entry_storage->getQuery()
  ->condition('code', $code)
  ->accessCheck(FALSE);

if (empty($query->execute())) {
  $errors[] = "Dictionary code '{$code}' does not exist.";
}
```

#### Taxonomy

Vérifie que le terme existe :
```php
$term = $term_storage->load($tid);
if (!$term) {
  $errors[] = "Taxonomy term ID '{$tid}' does not exist.";
}
```

## Ajouter un nouveau type de caractéristique

### 1. Créer le plugin FeatureType

Créer `src/Plugin/FeatureType/CustomFeatureType.php` :

```php
<?php

namespace Drupal\ps_feature\Plugin\FeatureType;

use Drupal\ps_feature\Plugin\FeatureTypeBase;

/**
 * @FeatureType(
 *   id = "custom",
 *   label = @Translation("Custom Type"),
 *   description = @Translation("Custom payload structure")
 * )
 */
class CustomFeatureType extends FeatureTypeBase {

  public function validate(array $payload): array {
    $errors = [];
    
    if (!isset($payload['my_field'])) {
      $errors[] = "Missing 'my_field' in payload.";
    }
    
    return $errors;
  }

  public function normalize(array $payload): array {
    return [
      'my_field' => trim($payload['my_field'] ?? ''),
    ];
  }
}
```

### 2. Ajouter des champs traduisibles (optionnel)

#### a. Déclarer dans le schema

`config/schema/ps_feature.schema.yml` :

```yaml
ps_feature.feature_definition.*:
  mapping:
    payload_defaults:
      mapping:
        my_custom_field:
          type: string
          translatable: true
```

#### b. Afficher dans le formulaire de traduction

`FeatureDefinitionTranslationForm::buildForm()` :

```php
case 'custom':
  $form['payload_defaults_container']['my_custom_field'] = [
    '#type' => 'textfield',
    '#title' => $this->t('My Custom Field'),
    '#default_value' => $translation_data['payload_defaults']['my_custom_field'] ?? '',
    '#description' => [
      '#markup' => $this->t('Original: @value', [
        '@value' => $source_data['payload_defaults']['my_custom_field'] ?? '',
      ]),
    ],
  ];
  break;
```

#### c. Sauvegarder les traductions

`FeatureDefinitionTranslationForm::submitForm()` :

```php
case 'custom':
  if (isset($container_values['my_custom_field'])) {
    $payload_defaults_translation['my_custom_field'] = $container_values['my_custom_field'];
  }
  break;
```

### 3. Ajouter les champs dans le formulaire d'édition

`FeatureDefinitionForm::buildPayloadDefaultsFields()` :

```php
case 'custom':
  $form['my_custom_field'] = [
    '#type' => 'textfield',
    '#title' => $this->t('My Custom Field'),
    '#default_value' => $defaults['my_custom_field'] ?? '',
  ];
  break;
```

## Utilisation

### Via l'interface

1. **Créer un groupe** : `/admin/ps/structure/features`
2. **Créer une définition** : `/admin/ps/content/features/add`
3. **Traduire** : `/admin/ps/content/features/{id}/edit/translate`

### Par code

```php
// Créer un groupe
$group = \Drupal::entityTypeManager()
  ->getStorage('fb_feature_group')
  ->create([
    'id' => 'technical',
    'label' => 'Technical Features',
    'description' => 'Technical characteristics',
    'asset_types' => ['bureau', 'logement'],
  ]);
$group->save();

// Créer une définition avec champs traduisibles
$feature = \Drupal::entityTypeManager()
  ->getStorage('fb_feature_definition')
  ->create([
    'id' => 'surface',
    'label' => 'Surface totale',
    'description' => 'La surface totale du bien',
    'code' => 'total_area',
    'group' => 'technical',
    'type_driver' => 'numeric',
    'payload_defaults' => [
      'unit' => 'm²',
      'min' => 0,
      'max' => 10000,
    ],
  ]);
$feature->save();

// Traduire
$config = \Drupal::service('language.config_factory_override')
  ->getOverride('en', 'ps_feature.feature_definition.surface');

$config->set('label', 'Total Area');
$config->set('description', 'Total property area');
$config->set('payload_defaults.unit', 'sq ft');  // Champ imbriqué !
$config->save();
```

## Fichiers clés

```
ps_feature/
├── config/schema/
│   └── ps_feature.schema.yml          # ⭐ Définit champs traduisibles
├── src/
│   ├── Entity/
│   │   ├── FeatureGroup.php
│   │   ├── FeatureDefinition.php
│   │   └── OfferFeature.php
│   ├── Form/
│   │   ├── FeatureGroupTranslationForm.php      # ⭐ Traduction groupes
│   │   ├── FeatureDefinitionTranslationForm.php # ⭐ Traduction définitions
│   │   ├── FeatureGroupForm.php
│   │   └── FeatureDefinitionForm.php
│   ├── Hook/
│   │   └── LocalTaskAlter.php          # Simplifie titres d'onglets
│   ├── Plugin/
│   │   └── FeatureType/
│   │       ├── NumericFeatureType.php
│   │       ├── RangeFeatureType.php
│   │       ├── ListFeatureType.php
│   │       ├── DictionaryFeatureType.php
│   │       ├── TaxonomyFeatureType.php
│   │       └── ...
│   ├── Routing/
│   │   └── TranslationRouteSubscriber.php  # ⭐ Override routes traduction
│   └── Service/
│       └── FeatureTypeManager.php
├── ps_feature.links.task.yml          # Définit onglets
├── ps_feature.routing.yml
└── README.md
```

## Debugging

### Vérifier la configuration traduite

```bash
# Configuration originale
drush config:get ps_feature.feature_definition.surface_totale

# Avec overrides de langue
drush config:get ps_feature.feature_definition.surface_totale --include-overridden

# Override français
drush config:get language.fr.ps_feature.feature_definition.surface_totale
```

### Vérifier le schema

```bash
drush php:eval "
\$config = \Drupal::service('config.typed')->get('ps_feature.feature_definition.surface_totale');
print_r(\$config->getElements());
"
```

### Vérifier les routes

```bash
drush php:eval "
\$routes = \Drupal::service('router.route_provider')->getAllRoutes();
foreach (\$routes as \$name => \$route) {
  if (strpos(\$name, 'config_translation.item') === 0 && strpos(\$name, 'fb_feature') !== false) {
    echo \$name . ' -> ' . \$route->getDefault('_form') . PHP_EOL;
  }
}
"
```

## Dépendances

- Drupal Core 11.x
- `config_translation` : Système de traduction de configuration
- `ps_dictionary` : Pour validation codes dictionary (optionnel)

## Support

Consulter la documentation complète dans `docs/modules/PS_FEATURE/`.
