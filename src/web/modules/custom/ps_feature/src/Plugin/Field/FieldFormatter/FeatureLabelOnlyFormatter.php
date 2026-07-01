<?php

namespace Drupal\ps_feature\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 'feature_label_only' formatter.
 */
#[FieldFormatter(
  id: 'feature_label_only',
  label: new TranslatableMarkup('Feature label only'),
  description: new TranslatableMarkup('Display only the feature label.'),
  field_types: ['feature'],
)]
class FeatureLabelOnlyFormatter extends FormatterBase {

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

      $elements[$delta] = [
        '#markup' => $feature_definition->label(),
      ];
    }

    return $elements;
  }

}
