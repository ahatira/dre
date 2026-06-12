<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Compact diagnostic formatter for comparison tables.
 */
#[FieldFormatter(
  id: 'diagnostic_item_compare',
  label: new TranslatableMarkup('Diagnostic - Compare table (badge)'),
  field_types: ['diagnostic_item'],
)]
final class DiagnosticItemCompareFormatter extends DiagnosticItemFormatterBase {

  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = parent::viewElements($items, $langcode);
    foreach ($elements as &$element) {
      unset($element['#attached']);
    }
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  protected function getThemeHook(): string {
    return 'ps_diagnostic_item_compare';
  }

}
