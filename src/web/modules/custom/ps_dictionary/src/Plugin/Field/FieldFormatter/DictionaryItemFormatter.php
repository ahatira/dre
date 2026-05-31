<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'ps_dictionary_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "ps_dictionary_formatter",
 *   label = @Translation("Dictionary label"),
 *   field_types = {
 *     "ps_dictionary"
 *   }
 * )
 */
class DictionaryItemFormatter extends FormatterBase {

  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $resolver = \Drupal::service('ps_dictionary.resolver');
    $type = $this->getFieldSetting('dictionary_type') ?? '';
    foreach ($items as $delta => $item) {
      $label = $type ? $resolver->resolveLabel($type, $item->value) : $item->value;
      $elements[$delta] = ['#markup' => $label ?: $item->value];
    }
    return $elements;
  }
}
