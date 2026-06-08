<?php

declare(strict_types=1);

namespace Drupal\ps_search\Plugin\search_api\processor;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Adds categorized feature fields to the offers index.
 *
 * Maps CRM feature definitions (fb_feature_group) to search filter groups:
 * - feature_equipments: equipements group
 * - feature_services: prestations_de_service group
 * - feature_building_type: building type flags
 * - feature_accessibility: PMR accessibility flags
 * - nearby_transport: extracted from body text
 * - ceiling_height: hauteurs features
 * - has_immersive_tour / has_video: media and feature flags.
 *
 * @SearchApiProcessor(
 *   id = "ps_feature_categorizer",
 *   label = @Translation("Feature categorizer"),
 *   description = @Translation("Exposes categorized feature fields for filtering."),
 *   stages = {
 *     "add_properties" = 0,
 *   }
 * )
 */
final class FeatureCategorizerProcessor extends ProcessorPluginBase {

  /**
   * Feature groups indexed as equipments.
   */
  private const EQUIPMENT_GROUPS = [
    'equipements',
  ];

  /**
   * Feature groups indexed as services.
   */
  private const SERVICE_GROUPS = [
    'prestations_de_service',
  ];

  /**
   * Building type feature definition IDs.
   */
  private const BUILDING_TYPE_FEATURES = [
    'type_etat_du_batiment__tec_immeuble_coproprit',
    'type_etat_du_batiment__tec_immeuble_indpendant',
  ];

  /**
   * Accessibility feature definition IDs.
   */
  private const ACCESSIBILITY_FEATURES = [
    'amenagements__tec_accs_pers_mobilit_rduite',
  ];

  /**
   * Ceiling height feature definition IDs (meters).
   */
  private const CEILING_HEIGHT_FEATURES = [
    'hauteurs__tec_hauteur_sous_plafond',
    'hauteurs__tec_hauteur_libre',
    'hauteurs__tec_hauteur_sous_poutre',
  ];

  /**
   * Immersive tour feature definition IDs.
   */
  private const IMMERSIVE_TOUR_FEATURES = [
    'immersive_tour',
    '360_view',
    'virtual_tour',
    'visite_virtuelle',
  ];

  /**
   * Video feature definition IDs.
   */
  private const VIDEO_FEATURES = [
    'video',
    'has_video',
  ];

  /**
   * Cached feature definition group map.
   *
   * @var array<string, string>|null
   */
  private ?array $featureGroups = NULL;

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function supportsIndex(IndexInterface $index): bool {
    foreach ($index->getDatasources() as $datasource) {
      if ($datasource->getEntityTypeId() === 'node') {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(?DatasourceInterface $datasource = NULL): array {
    if ($datasource !== NULL) {
      return [];
    }

    $properties = [];

    $properties['feature_equipments'] = new ProcessorProperty([
      'label' => $this->t('Equipments'),
      'description' => $this->t('Equipment features from the equipements group.'),
      'type' => 'string',
      'processor_id' => $this->getPluginId(),
      'is_list' => TRUE,
    ]);

    $properties['feature_services'] = new ProcessorProperty([
      'label' => $this->t('Services'),
      'description' => $this->t('Service features from prestations_de_service.'),
      'type' => 'string',
      'processor_id' => $this->getPluginId(),
      'is_list' => TRUE,
    ]);

    $properties['feature_building_type'] = new ProcessorProperty([
      'label' => $this->t('Building type'),
      'description' => $this->t('Building type features (copropriété, indépendant).'),
      'type' => 'string',
      'processor_id' => $this->getPluginId(),
      'is_list' => TRUE,
    ]);

    $properties['feature_accessibility'] = new ProcessorProperty([
      'label' => $this->t('Accessibility'),
      'description' => $this->t('Whether the offer has PMR accessibility features.'),
      'type' => 'boolean',
      'processor_id' => $this->getPluginId(),
      'is_list' => FALSE,
    ]);

    $properties['nearby_transport'] = new ProcessorProperty([
      'label' => $this->t('Nearby transport'),
      'description' => $this->t('Nearby public transport information.'),
      'type' => 'string',
      'processor_id' => $this->getPluginId(),
      'is_list' => FALSE,
    ]);

    $properties['ceiling_height'] = new ProcessorProperty([
      'label' => $this->t('Ceiling height'),
      'description' => $this->t('Ceiling height in meters.'),
      'type' => 'decimal',
      'processor_id' => $this->getPluginId(),
      'is_list' => FALSE,
    ]);

    $properties['has_immersive_tour'] = new ProcessorProperty([
      'label' => $this->t('Has immersive tour'),
      'description' => $this->t('Whether the offer has an immersive tour/360° view.'),
      'type' => 'boolean',
      'processor_id' => $this->getPluginId(),
      'is_list' => FALSE,
    ]);

    $properties['has_video'] = new ProcessorProperty([
      'label' => $this->t('Has video'),
      'description' => $this->t('Whether the offer has a video.'),
      'type' => 'boolean',
      'processor_id' => $this->getPluginId(),
      'is_list' => FALSE,
    ]);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item): void {
    $node = $item->getOriginalObject()->getValue();

    if (!$node) {
      return;
    }

    $fields = $item->getFields(FALSE);
    $categorized = $node->hasField('field_features')
      ? $this->categorizeFeatures($node->get('field_features'))
      : [
        'equipments' => [],
        'services' => [],
        'building_type' => [],
        'accessibility' => [],
      ];

    $this->addListValues($fields, 'feature_equipments', $categorized['equipments']);
    $this->addListValues($fields, 'feature_services', $categorized['services']);
    $this->addListValues($fields, 'feature_building_type', $categorized['building_type']);
    $this->addBooleanValue($fields, 'feature_accessibility', !empty($categorized['accessibility']));

    if ($node->hasField('body') && !$node->get('body')->isEmpty()) {
      $body_value = $node->get('body')->value ?? '';
      $transport_info = $this->extractTransportInfo($body_value);
      if ($transport_info) {
        $this->addScalarValue($fields, 'nearby_transport', $transport_info);
      }
    }

    $ceiling_height = $this->extractCeilingHeight($node);
    if ($ceiling_height !== NULL) {
      $this->addScalarValue($fields, 'ceiling_height', $ceiling_height);
    }

    $this->addBooleanValue($fields, 'has_immersive_tour', $this->checkForImmersiveTour($node));
    $this->addBooleanValue($fields, 'has_video', $this->checkForVideo($node));
  }

  /**
   * Categorizes features based on fb_feature_group and known IDs.
   */
  private function categorizeFeatures($features_field): array {
    $categorized = [
      'equipments' => [],
      'services' => [],
      'building_type' => [],
      'accessibility' => [],
    ];

    foreach ($features_field as $feature_item) {
      $feature_id = (string) $feature_item->feature_definition_id;
      $group = $this->getFeatureGroup($feature_id);

      if (in_array($group, self::EQUIPMENT_GROUPS, TRUE)) {
        $categorized['equipments'][] = $feature_id;
      }

      if (in_array($group, self::SERVICE_GROUPS, TRUE)) {
        $categorized['services'][] = $feature_id;
      }

      if (in_array($feature_id, self::BUILDING_TYPE_FEATURES, TRUE)) {
        $categorized['building_type'][] = $feature_id;
      }

      if (in_array($feature_id, self::ACCESSIBILITY_FEATURES, TRUE)) {
        $categorized['accessibility'][] = $feature_id;
      }
    }

    return $categorized;
  }

  /**
   * Returns the feature group for a definition ID.
   */
  private function getFeatureGroup(string $feature_id): string {
    if ($this->featureGroups === NULL) {
      $this->featureGroups = [];
      $definitions = $this->entityTypeManager
        ->getStorage('fb_feature_definition')
        ->loadMultiple();

      foreach ($definitions as $definition) {
        $this->featureGroups[(string) $definition->id()] = (string) $definition->getGroup();
      }
    }

    return $this->featureGroups[$feature_id] ?? '';
  }

  /**
   * Adds multi-value processor field values.
   *
   * @param array $fields
   *   Item fields keyed by field identifier.
   * @param string $property_path
   *   Processor property path.
   * @param array $values
   *   Values to append.
   */
  private function addListValues(array $fields, string $property_path, array $values): void {
    $matching_fields = $this->getFieldsHelper()->filterForPropertyPath($fields, NULL, $property_path);
    foreach ($matching_fields as $field) {
      foreach ($values as $value) {
        $field->addValue($value);
      }
    }
  }

  /**
   * Adds a boolean processor field value.
   */
  private function addBooleanValue(array $fields, string $property_path, bool $value): void {
    $matching_fields = $this->getFieldsHelper()->filterForPropertyPath($fields, NULL, $property_path);
    foreach ($matching_fields as $field) {
      $field->addValue($value);
    }
  }

  /**
   * Adds a scalar processor field value.
   *
   * @param string|float $value
   *   Indexed value.
   */
  private function addScalarValue(array $fields, string $property_path, string|float $value): void {
    $matching_fields = $this->getFieldsHelper()->filterForPropertyPath($fields, NULL, $property_path);
    foreach ($matching_fields as $field) {
      $field->addValue($value);
    }
  }

  /**
   * Extract transport information from body text.
   */
  private function extractTransportInfo(string $body): ?string {
    $transport_keywords = ['métro', 'metro', 'bus', 'tram', 'train', 'RER', 'station', 'transport'];

    foreach ($transport_keywords as $keyword) {
      if (stripos($body, $keyword) !== FALSE) {
        $sentences = preg_split('/[.!?]/', $body);
        foreach ($sentences as $sentence) {
          if (stripos($sentence, $keyword) !== FALSE) {
            return trim($sentence);
          }
        }
      }
    }

    return NULL;
  }

  /**
   * Extract ceiling height from features payload.
   */
  private function extractCeilingHeight($node): ?float {
    if (!$node->hasField('field_features')) {
      return NULL;
    }

    foreach ($node->get('field_features') as $feature_item) {
      $feature_id = (string) $feature_item->feature_definition_id;

      if (!in_array($feature_id, self::CEILING_HEIGHT_FEATURES, TRUE)) {
        continue;
      }

      $payload = $feature_item->payload;
      if (!$payload) {
        continue;
      }

      $data = json_decode($payload, TRUE);
      if (isset($data['value']) && is_numeric($data['value'])) {
        return (float) $data['value'];
      }
    }

    return NULL;
  }

  /**
   * Checks if offer has immersive tour.
   */
  private function checkForImmersiveTour($node): bool {
    if ($node->hasField('field_features')) {
      foreach ($node->get('field_features') as $feature_item) {
        $feature_id = (string) $feature_item->feature_definition_id;
        if (in_array($feature_id, self::IMMERSIVE_TOUR_FEATURES, TRUE)) {
          return TRUE;
        }
      }
    }

    if ($node->hasField('field_media_gallery') && !$node->get('field_media_gallery')->isEmpty()) {
      foreach ($node->get('field_media_gallery')->referencedEntities() as $media) {
        if ($media->bundle() === 'image') {
          $name = $media->label();
          if (stripos($name, '360') !== FALSE || stripos($name, 'virtual') !== FALSE) {
            return TRUE;
          }
        }
      }
    }

    return FALSE;
  }

  /**
   * Checks if offer has video.
   */
  private function checkForVideo($node): bool {
    if ($node->hasField('field_features')) {
      foreach ($node->get('field_features') as $feature_item) {
        $feature_id = (string) $feature_item->feature_definition_id;
        if (in_array($feature_id, self::VIDEO_FEATURES, TRUE)) {
          return TRUE;
        }
      }
    }

    if ($node->hasField('field_media_document') && !$node->get('field_media_document')->isEmpty()) {
      foreach ($node->get('field_media_document')->referencedEntities() as $media) {
        if (in_array($media->bundle(), ['video', 'remote_video'], TRUE)) {
          return TRUE;
        }
      }
    }

    if ($node->hasField('field_media_gallery') && !$node->get('field_media_gallery')->isEmpty()) {
      foreach ($node->get('field_media_gallery')->referencedEntities() as $media) {
        if (in_array($media->bundle(), ['video', 'remote_video'], TRUE)) {
          return TRUE;
        }
      }
    }

    return FALSE;
  }

}
