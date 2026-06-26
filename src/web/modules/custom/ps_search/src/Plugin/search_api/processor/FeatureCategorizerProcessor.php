<?php

declare(strict_types=1);

namespace Drupal\ps_search\Plugin\search_api\processor;

use Drupal\ps_search\Service\TransportFeatureSearchTextBuilder;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Adds derived offer fields used by More-filters (core criteria group).
 *
 * Per-feature filters use FeatureProcessor (feature_* fields). This processor
 * only indexes cross-cutting fields not tied to a single definition:
 * - nearby_transport: searchable text from transport-group features (BO config)
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

  private TransportFeatureSearchTextBuilder $transportTextBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->transportTextBuilder = $container->get('ps_search.transport_feature_search_text_builder');
    return $instance;
  }

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
        'type' => 'text',
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

    $featureNode = $this->transportTextBuilder->resolveNodeForFeatureIndexing($node);
    $fields = $item->getFields(FALSE);

    $transportText = $this->transportTextBuilder->buildFromNode($featureNode);
    if ($transportText !== '') {
      $this->addScalarValue($fields, 'nearby_transport', $transportText);
    }

    $this->addBooleanValue($fields, 'has_immersive_tour', $this->checkForImmersiveTour($featureNode));
    $this->addBooleanValue($fields, 'has_video', $this->checkForVideo($featureNode));
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
        if ($media->bundle() === 'visite_guided') {
          return TRUE;
        }
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
