<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Vertical formatter.
 */
#[FieldFormatter(
  id: 'diagnostic_item_vertical',
  label: new TranslatableMarkup('Diagnostic - Vertical'),
  field_types: ['diagnostic_item'],
)]
final class DiagnosticItemVerticalFormatter extends DiagnosticItemFormatterBase {

  protected function getThemeHook(): string {
    return 'ps_diagnostic_item_vertical';
  }

}
