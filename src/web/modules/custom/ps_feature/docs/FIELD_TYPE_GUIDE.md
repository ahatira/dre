# Feature Field Type - Guide d'utilisation

## Vue d'ensemble

Le module `ps_feature` fournit maintenant un **Field Type** complet qui peut être ajouté à n'importe quelle entité (Content, Paragraphs, etc.).

Pour la documentation détaillée de l'architecture JS du widget `feature_builder`, voir [FEATURE_BUILDER_JS_GUIDE.md](FEATURE_BUILDER_JS_GUIDE.md).

Ce field type permet de :
- Stocker une référence à une `FeatureDefinition` (config entity)
- Stocker le payload (valeur) de la feature
- Filtrer les features disponibles par groupe ou par feature individuelle
- Afficher les features avec différents formats

## Composants créés

### 1. FieldType : `feature`

**Fichier** : `src/Plugin/Field/FieldType/FeatureItem.php`

**Colonnes de stockage** :
- `feature_definition_id` (varchar 128) : ID de la FeatureDefinition
- `payload` (text) : JSON du payload

**Méthodes utiles** :
```php
$item = $entity->get('field_features')->get(0);

// Obtenir la FeatureDefinition
$definition = $item->getFeatureDefinition();

// Obtenir le payload sous forme de tableau
$payload = $item->getPayloadArray();

// Définir le payload depuis un tableau
$item->setPayloadArray(['value' => 100, 'unit' => 'm²']);
```

**Paramètres du champ** :
- `allowed_groups` : Liste des groupes de features autorisés (checkbox)
- `allowed_features` : Liste des features individuelles autorisées (checkbox)

Si ces paramètres sont vides, toutes les features sont disponibles.

### 2. Widgets

#### `feature_default` (widget Form API classique)

**Fichier** : `src/Plugin/Field/FieldWidget/FeatureWidget.php`

**Fonctionnalités** :
- Sélecteur de feature avec AJAX
- Champs dynamiques selon le type de feature sélectionné
- Formulaires adaptés pour chaque type via `buildPayloadForm()` des plugins (incluant `taxonomy`)

#### `feature_builder` (widget JS recommandé en BO)

**Fichier** : `src/Plugin/Field/FieldWidget/FeatureBuilderWidget.php`

**Fonctionnalités** :
- UI de type catalogue + drag and drop
- Groupement visuel des features par groupe
- Éditeurs JS dédiés pour: `flag`, `yes_no`, `numeric`, `range`, `text`, `list`, `date`, `dictionary`
- Fallback générique pour types sans éditeur JS dédié

Note: le field type `feature` conserve `feature_default` comme widget par défaut technique. Le widget `feature_builder` est utilisé quand configuré sur le form display.

### 3. Formatters

#### `feature_default` (Formatter principal)

**Fichier** : `src/Plugin/Field/FieldFormatter/FeatureFormatter.php`

**Options d'affichage** :
- `show_label` : Afficher le label de la feature (booléen)
- `show_group` : Afficher le groupe de la feature (booléen)
- `format_style` : Style d'affichage
  - **default** : Label + valeur + badge groupe
  - **compact** : Juste la valeur
  - **detailed** : Label, groupe, valeur, description
  - **grouped** : Regroupement des features par groupe
- `hide_disabled_flags` : Masquer les flags désactivés
- `show_flag_text` : Afficher ou masquer le texte `Present/Absent` pour les flags
- `group_order` : Ordre explicite des groupes en mode grouped (config BO via liste draggable)

**Exemples de rendu** :
```
Style default:
  Surface habitable: 120.00 m² (Aménagements)

Style compact:
  120.00 m²

Style detailed:
  Surface habitable (Aménagements): 120.00 m²
  Surface habitable disponible pour l'occupation

Flag avec `show_flag_text = true`:
  QA Live Flag Feature: Present

Flag avec `show_flag_text = false`:
  QA Live Flag Feature
```

#### `feature_label_only`

**Fichier** : `src/Plugin/Field/FieldFormatter/FeatureLabelOnlyFormatter.php`

Affiche uniquement le label de la feature (ex: "Surface habitable").

#### `feature_value_only`

**Fichier** : `src/Plugin/Field/FieldFormatter/FeatureValueOnlyFormatter.php`

Affiche uniquement la valeur formatée (ex: "120.00 m²").

## Utilisation pratique

### 1. Ajouter le champ à une entité

**Via l'interface** :
1. Aller dans la gestion des champs de l'entité
2. Cliquer "Add field"
3. Sélectionner "Feature" dans la liste
4. Configurer les paramètres :
   - Cardinalité (nombre de valeurs)
   - Groupes autorisés
   - Features autorisées

**Via code** (dans un hook d'installation ou update) :

```php
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;

// Créer le storage.
$field_storage = FieldStorageConfig::create([
  'field_name' => 'field_features',
  'entity_type' => 'node',
  'type' => 'feature',
  'cardinality' => -1, // Illimité
]);
$field_storage->save();

// Créer l'instance du champ pour un bundle.
$field = FieldConfig::create([
  'field_storage' => $field_storage,
  'bundle' => 'offer', // Nom du bundle
  'label' => 'Caractéristiques',
  'description' => 'Caractéristiques techniques de l\'offre.',
  'settings' => [
    'allowed_groups' => ['amenagements', 'technique'], // Vide = tous
    'allowed_features' => [], // Vide = toutes
  ],
]);
$field->save();

// Configurer l'affichage du formulaire.
\Drupal::service('entity_display.repository')
  ->getFormDisplay('node', 'offer', 'default')
  ->setComponent('field_features', [
    'type' => 'feature_builder',
    'weight' => 10,
  ])
  ->save();

// Configurer l'affichage.
\Drupal::service('entity_display.repository')
  ->getViewDisplay('node', 'offer', 'default')
  ->setComponent('field_features', [
    'type' => 'feature_default',
    'settings' => [
      'show_label' => TRUE,
      'show_group' => TRUE,
      'format_style' => 'grouped',
      'hide_disabled_flags' => TRUE,
      'show_flag_text' => TRUE,
      'group_order' => "amenagements\ntechnique",
    ],
    'weight' => 10,
  ])
  ->save();
```

### 2. Manipuler programmatiquement

#### Créer une entité avec des features

```php
use Drupal\node\Entity\Node;

$node = Node::create([
  'type' => 'offer',
  'title' => 'Appartement T3',
  'field_features' => [
    [
      'feature_definition_id' => 'surface_habitable',
      'payload' => json_encode(['value' => 65.5, 'unit' => 'm²']),
    ],
    [
      'feature_definition_id' => 'nb_chambres',
      'payload' => json_encode(['value' => 2, 'unit' => 'chambres']),
    ],
  ],
]);
$node->save();
```

#### Lire les features d'une entité

```php
$node = Node::load(123);

foreach ($node->get('field_features') as $item) {
  $definition = $item->getFeatureDefinition();
  $payload = $item->getPayloadArray();
  
  echo sprintf(
    "%s: %s\n",
    $definition->label(),
    json_encode($payload)
  );
}
```

#### Ajouter une feature à une entité existante

```php
$node = Node::load(123);

$node->get('field_features')->appendItem([
  'feature_definition_id' => 'balcon',
  'payload' => json_encode(['present' => TRUE]),
]);

$node->save();
```

#### Filtrer par feature

```php
$query = \Drupal::entityQuery('node')
  ->condition('type', 'offer')
  ->condition('field_features.feature_definition_id', 'surface_habitable')
  ->accessCheck(FALSE);

$nids = $query->execute();
```

### 3. Themer l'affichage

#### Via un template Twig

Les champs features peuvent être affichés dans les templates Twig :

```twig
{# Affichage par défaut #}
{{ content.field_features }}

{# Boucle sur chaque feature #}
{% for item in node.field_features %}
  <div class="feature">
    <strong>{{ item.entity.label }}</strong>:
    {{ item.formatted_value }}
  </div>
{% endfor %}
```

#### Via un hook de preprocess

```php
/**
 * Implements hook_preprocess_field().
 */
function mymodule_preprocess_field(&$variables) {
  if ($variables['field_name'] === 'field_features') {
    foreach ($variables['items'] as $delta => $item) {
      $field_item = $variables['element']['#items'][$delta];
      $definition = $field_item->getFeatureDefinition();
      $payload = $field_item->getPayloadArray();
      
      // Ajouter des variables personnalisées.
      $variables['items'][$delta]['feature_type'] = $definition->getType();
      $variables['items'][$delta]['feature_group'] = $definition->getGroup();
    }
  }
}
```

## Interface FeatureTypeInterface - Méthodes ajoutées

Pour supporter le Widget et les Formatters, deux nouvelles méthodes ont été ajoutées à tous les plugins FeatureType :

### `buildPayloadForm(array $current_payload = []): array`

Construit les éléments de formulaire pour éditer le payload dans le widget.

**Retourne** : Tableau d'éléments Form API.

**Exemple** (NumericFeatureType) :
```php
public function buildPayloadForm(array $current_payload = []): array {
  return [
    'value' => [
      '#type' => 'number',
      '#title' => t('Value'),
      '#default_value' => $current_payload['value'] ?? '',
      '#step' => 'any',
      '#required' => TRUE,
    ],
    'unit' => [
      '#type' => 'textfield',
      '#title' => t('Unit'),
      '#default_value' => $current_payload['unit'] ?? '',
      '#required' => TRUE,
    ],
  ];
}
```

### `format(array $payload): string`

Formate le payload pour l'affichage lisible.

**Retourne** : Chaîne formatée prête pour l'affichage.

**Exemples** :
- **Numeric** : `120.00 m²`
- **Range** : `50.00 - 100.00 €`
- **List** : `Cuisine équipée, Balcon, Cave`
- **Date** : `December 15, 2024` ou `Q4 2024`
- **YesNo** : `Yes` ou `No`
- **Dictionary** : `Étiquette du code dictionnaire`
- **Taxonomy** : `Nom du terme`

## Avantages de cette approche

1. **Réutilisabilité** : Le champ peut être ajouté à n'importe quelle entité
2. **Filtrage** : Possibilité de restreindre les features par groupe/feature
3. **Cohérence** : Tous les types de features utilisent le même champ
4. **Formatage** : Plusieurs formatters pour différents cas d'usage
5. **Validation** : Les plugins FeatureType valident automatiquement le payload
6. **Traduction** : Compatible avec le système de traduction Drupal
7. **Typage fort** : Structure de données stricte (feature_definition_id + payload)

## Différence avec OfferFeature

**OfferFeature** (entité content) :
- Entité standalone avec révisions
- Utilisé pour gérer les features comme des contenus autonomes
- Plus lourd, mais plus flexible

**Feature Field** :
- Champ attaché à une entité parent
- Plus léger et plus rapide
- Mieux intégré à l'API Field de Drupal

**Usage recommandé** : Utiliser le Feature Field sauf si vous avez besoin de révisions spécifiques ou de workflows complexes sur les features elles-mêmes.

## Dépannage

### Le champ ne s'affiche pas dans la liste

Vider le cache :
```bash
drush cr
```

### AJAX ne fonctionne pas dans le widget

Vérifier que jQuery est bien chargé et qu'il n'y a pas d'erreurs JavaScript dans la console.

### Les valeurs ne se sauvegardent pas

Vérifier que `massageFormValues()` dans le widget traite correctement les données. Pour le type `list`, vérifier que `codes_text` est bien converti en `codes`.

### Erreur de validation

Chaque plugin FeatureType implémente `validate()`. Vérifier que le payload respecte la structure attendue.

## Extensions possibles

1. **Widget inline** : Widget plus compact pour les cas simples
2. **Formatter table** : Affichage en tableau pour plusieurs features
3. **Filtres Views** : Exposer les features comme filtres dans Views
4. **Bulk operations** : Actions en masse sur les features
5. **Import/Export** : Via le champ `code` des FeatureDefinition

## Fichiers créés

```
src/Plugin/Field/
├── FieldType/
│   └── FeatureItem.php
├── FieldWidget/
│   ├── FeatureWidget.php
│   └── FeatureBuilderWidget.php
└── FieldFormatter/
    ├── FeatureFormatter.php
    ├── FeatureLabelOnlyFormatter.php
    └── FeatureValueOnlyFormatter.php
```

## Prochaines étapes

1. Tester l'ajout du champ à une entité (ex: `node` type `offer`)
2. Créer quelques features via le formulaire
3. Vérifier les différents formatters
4. Adapter les templates Twig si nécessaire
5. Créer des Views exposant les features
