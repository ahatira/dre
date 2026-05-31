<?php

declare(strict_types=1);

namespace Drupal\ps_search\Plugin\search_api\processor;

use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;

/**
 * Adds categorized feature fields to the offers index.
 *
 * Extracts features from field_features and categorizes them into:
 * - equipments: parking, air_conditioning, heating, security_system, kitchen
 * - services: reception, maintenance
 * - building_type: new_building, renovated
 * - accessibility: accessibility_pmr
 * - nearby_transport: text field
 * - ceiling_height: numeric field
 * - immersive_tour: boolean
 * - video: boolean
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
   * Feature ID to category mapping.
   */
  private const CATEGORY_MAPPING = [
    // Equipments
    'parking' => 'equipments',
    'air_conditioning' => 'equipments',
    'heating' => 'equipments',
    'security_system' => 'equipments',
    'kitchen' => 'equipments',
    'elevator' => 'equipments',
    'disabled_access' => 'equipments',
    
    // Services
    'reception' => 'services',
    'maintenance' => 'services',
    'cleaning' => 'services',
    'security' => 'services',
    
    // Building type
    'new_building' => 'building_type',
    'renovated' => 'building_type',
    'historic' => 'building_type',
    
    // Accessibility
    'accessibility_pmr' => 'accessibility',
    'wheelchair_access' => 'accessibility',
  ];

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
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL): array {
    if ($datasource !== NULL) {
      return [];
    }

    $properties = [];

    $properties['feature_equipments'] = new ProcessorProperty([
      'label' => $this->t('Equipments'),
      'description' => $this->t('Equipment features (parking, air conditioning, etc.)'),
      'type' => 'string',
      'processor_id' => $this->getPluginId(),
      'is_list' => TRUE,
    ]);

    $properties['feature_services'] = new ProcessorProperty([
      'label' => $this->t('Services'),
      'description' => $this->t('Service features (reception, maintenance, etc.)'),
      'type' => 'string',
      'processor_id' => $this->getPluginId(),
      'is_list' => TRUE,
    ]);

    $properties['feature_building_type'] = new ProcessorProperty([
      'label' => $this->t('Building type'),
      'description' => $this->t('Building type features (new, renovated, etc.)'),
      'type' => 'string',
      'processor_id' => $this->getPluginId(),
      'is_list' => TRUE,
    ]);

    $properties['feature_accessibility'] = new ProcessorProperty([
      'label' => $this->t('Accessibility'),
      'description' => $this->t('Accessibility features (PMR, wheelchair, etc.)'),
      'type' => 'boolean',
      'processor_id' => $this->getPluginId(),
      'is_list' => FALSE,
    ]);

    $properties['nearby_transport'] = new ProcessorProperty([
      'label' => $this->t('Nearby transport'),
      'description' => $this->t('Nearby public transport information'),
      'type' => 'string',
      'processor_id' => $this->getPluginId(),
      'is_list' => FALSE,
    ]);

    $properties['ceiling_height'] = new ProcessorProperty([
      'label' => $this->t('Ceiling height'),
      'description' => $this->t('Ceiling height in meters'),
      'type' => 'decimal',
      'processor_id' => $this->getPluginId(),
      'is_list' => FALSE,
    ]);

    $properties['has_immersive_tour'] = new ProcessorProperty([
      'label' => $this->t('Has immersive tour'),
      'description' => $this->t('Whether the offer has an immersive tour/360° view'),
      'type' => 'boolean',
      'processor_id' => $this->getPluginId(),
      'is_list' => FALSE,
    ]);

    $properties['has_video'] = new ProcessorProperty([
      'label' => $this->t('Has video'),
      'description' => $this->t('Whether the offer has a video'),
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

    // Process field_features
    if ($node->hasField('field_features')) {
      $categorized = $this->categorizeFeatures($node->get('field_features'));

      // Add equipments
      $equipment_fields = $this->getFieldsHelper()->filterForPropertyPath($fields, NULL, 'feature_equipments');
      foreach ($equipment_fields as $field) {
        foreach ($categorized['equipments'] ?? [] as $value) {
          $field->addValue($value);
        }
      }

      // Add services
      $service_fields = $this->getFieldsHelper()->filterForPropertyPath($fields, NULL, 'feature_services');
      foreach ($service_fields as $field) {
        foreach ($categorized['services'] ?? [] as $value) {
          $field->addValue($value);
        }
      }

      // Add building types
      $building_fields = $this->getFieldsHelper()->filterForPropertyPath($fields, NULL, 'feature_building_type');
      foreach ($building_fields as $field) {
        foreach ($categorized['building_type'] ?? [] as $value) {
          $field->addValue($value);
        }
      }

      // Add accessibility (boolean)
      $accessibility_fields = $this->getFieldsHelper()->filterForPropertyPath($fields, NULL, 'feature_accessibility');
      foreach ($accessibility_fields as $field) {
        $has_accessibility = !empty($categorized['accessibility']);
        $field->addValue($has_accessibility);
      }
    }

    // Extract nearby_transport from features or body
    // For now, search for transport-related keywords in body
    if ($node->hasField('body') && !$node->get('body')->isEmpty()) {
      $body_value = $node->get('body')->value ?? '';
      $transport_info = $this->extractTransportInfo($body_value);
      
      $transport_fields = $this->getFieldsHelper()->filterForPropertyPath($fields, NULL, 'nearby_transport');
      foreach ($transport_fields as $field) {
        if ($transport_info) {
          $field->addValue($transport_info);
        }
      }
    }

    // Extract ceiling height from features payload
    $ceiling_height = $this->extractCeilingHeight($node);
    if ($ceiling_height !== NULL) {
      $ceiling_fields = $this->getFieldsHelper()->filterForPropertyPath($fields, NULL, 'ceiling_height');
      foreach ($ceiling_fields as $field) {
        $field->addValue($ceiling_height);
      }
    }

    // Check for immersive tour in media gallery
    $has_tour = $this->checkForImmersiveTour($node);
    $tour_fields = $this->getFieldsHelper()->filterForPropertyPath($fields, NULL, 'has_immersive_tour');
    foreach ($tour_fields as $field) {
      $field->addValue($has_tour);
    }

    // Check for video in media documents
    $has_video = $this->checkForVideo($node);
    $video_fields = $this->getFieldsHelper()->filterForPropertyPath($fields, NULL, 'has_video');
    foreach ($video_fields as $field) {
      $field->addValue($has_video);
    }
  }

  /**
   * Categorize features based on feature_definition_id.
   */
  private function categorizeFeatures($features_field): array {
    $categorized = [];

    foreach ($features_field as $feature_item) {
      $feature_id = $feature_item->feature_definition_id;
      
      if (isset(self::CATEGORY_MAPPING[$feature_id])) {
        $category = self::CATEGORY_MAPPING[$feature_id];
        $categorized[$category][] = $feature_id;
      }
    }

    return $categorized;
  }

  /**
   * Extract transport information from body text.
   */
  private function extractTransportInfo(string $body): ?string {
    $transport_keywords = ['métro', 'metro', 'bus', 'tram', 'train', 'RER', 'station', 'transport'];
    
    foreach ($transport_keywords as $keyword) {
      if (stripos($body, $keyword) !== FALSE) {
        // Extract sentence containing transport info (simplified)
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
      $feature_id = $feature_item->feature_definition_id;
      
      if ($feature_id === 'ceiling_height' || $feature_id === 'hauteur_sous_plafond') {
        $payload = $feature_item->payload;
        if ($payload) {
          $data = json_decode($payload, TRUE);
          if (isset($data['value']) && is_numeric($data['value'])) {
            return (float) $data['value'];
          }
        }
      }
    }

    return NULL;
  }

  /**
   * Check if offer has immersive tour.
   */
  private function checkForImmersiveTour($node): bool {
    // Check in field_features
    if ($node->hasField('field_features')) {
      foreach ($node->get('field_features') as $feature_item) {
        $feature_id = $feature_item->feature_definition_id;
        if (in_array($feature_id, ['immersive_tour', '360_view', 'virtual_tour', 'visite_virtuelle'])) {
          return TRUE;
        }
      }
    }

    // Check in field_media_gallery for 360 images
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
   * Check if offer has video.
   */
  private function checkForVideo($node): bool {
    // Check in field_features
    if ($node->hasField('field_features')) {
      foreach ($node->get('field_features') as $feature_item) {
        $feature_id = $feature_item->feature_definition_id;
        if (in_array($feature_id, ['video', 'has_video'])) {
          return TRUE;
        }
      }
    }

    // Check in field_media_document for videos
    if ($node->hasField('field_media_document') && !$node->get('field_media_document')->isEmpty()) {
      foreach ($node->get('field_media_document')->referencedEntities() as $media) {
        if (in_array($media->bundle(), ['video', 'remote_video'])) {
          return TRUE;
        }
      }
    }

    // Check in field_media_gallery for videos
    if ($node->hasField('field_media_gallery') && !$node->get('field_media_gallery')->isEmpty()) {
      foreach ($node->get('field_media_gallery')->referencedEntities() as $media) {
        if (in_array($media->bundle(), ['video', 'remote_video'])) {
          return TRUE;
        }
      }
    }

    return FALSE;
  }

}
