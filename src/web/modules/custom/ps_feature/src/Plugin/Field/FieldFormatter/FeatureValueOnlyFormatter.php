<?php

namespace Drupal\ps_feature\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\ps_feature\Service\FeatureTypeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'feature_value_only' formatter.
 *
 * @FieldFormatter(
 *   id = "feature_value_only",
 *   label = @Translation("Feature value only"),
 *   description = @Translation("Display only the formatted feature value."),
 *   field_types = {
 *     "feature"
 *   }
 * )
 */
class FeatureValueOnlyFormatter extends FormatterBase {

  /**
   * The feature type plugin manager.
   *
   * @var \Drupal\ps_feature\Service\FeatureTypeManager
   */
  protected FeatureTypeManager $featureTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->featureTypeManager = $container->get('plugin.manager.feature_type');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];

    foreach ($items as $delta => $item) {
      $feature_definition = $item->getFeatureDefinition();
      
      if (!$feature_definition) {
        continue;
      }

      $payload = $item->getPayloadArray();
      $type = $feature_definition->getTypeDriver();
      
      try {
        $plugin = $this->featureTypeManager->createInstance($type);
        $formatted_value = $plugin->format($payload);
      }
      catch (\Exception $e) {
        $formatted_value = $this->t('Error formatting feature: @error', ['@error' => $e->getMessage()]);
      }

      $elements[$delta] = [
        '#markup' => $formatted_value,
      ];
    }

    return $elements;
  }

}
