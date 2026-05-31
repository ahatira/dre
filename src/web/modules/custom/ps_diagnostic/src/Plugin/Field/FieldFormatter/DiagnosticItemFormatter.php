<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Horizontal formatter (default).
 */
#[FieldFormatter(
  id: 'diagnostic_item_default',
  label: new TranslatableMarkup('Diagnostic - Horizontal (default)'),
  field_types: ['diagnostic_item'],
)]
final class DiagnosticItemFormatter extends DiagnosticItemFormatterBase {

  protected function getThemeHook(): string {
    return 'ps_diagnostic_item_horizontal';
  }

}
