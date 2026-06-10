<?php

declare(strict_types=1);

namespace Drupal\ps_search\Plugin\search_api\processor;

use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;

/**
 * Adds derived offer fields used by More-filters (core criteria group).
 *
 * Per-feature filters use FeatureProcessor (feature_* fields). This processor
 * only indexes cross-cutting fields not tied to a single definition:
 * - nearby_transport: extracted from body text
 * - ceiling_height: hauteurs features
 * - has_immersive_tour / has_video: media and feature flags.
 *
 * @SearchApiProcessor(
 *   id = "ps_feature_categorizer",
 *   label = @Translation("Offer derived search fields"),
 *   description = @Translation("Exposes derived fields for More-filters core criteria."),
 *   stages = {
 *     "add_properties" = 0,
 *   }
 * )
 */
final class FeatureCategorizerProcessor extends ProcessorPluginBase {

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

    return [
      'nearby_transport' => new ProcessorProperty([
        'label' => $this->t('Nearby transport'),
        'description' => $this->t('Nearby public transport information.'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
        'is_list' => FALSE,
      ]),
      'ceiling_height' => new ProcessorProperty([
        'label' => $this->t('Ceiling height'),
        'description' => $this->t('Ceiling height in meters.'),
        'type' => 'decimal',
        'processor_id' => $this->getPluginId(),
        'is_list' => FALSE,
      ]),
      'has_immersive_tour' => new ProcessorProperty([
        'label' => $this->t('Has immersive tour'),
        'description' => $this->t('Whether the offer has an immersive tour/360° view.'),
        'type' => 'boolean',
        'processor_id' => $this->getPluginId(),
        'is_list' => FALSE,
      ]),
      'has_video' => new ProcessorProperty([
        'label' => $this->t('Has video'),
        'description' => $this->t('Whether the offer has a video.'),
        'type' => 'boolean',
        'processor_id' => $this->getPluginId(),
        'is_list' => FALSE,
      ]),
    ];
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
