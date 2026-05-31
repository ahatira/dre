<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Full formatter.
 */
#[FieldFormatter(
  id: 'diagnostic_item_full',
  label: new TranslatableMarkup('Diagnostic - Full'),
  field_types: ['diagnostic_item'],
)]
final class DiagnosticItemFullFormatter extends DiagnosticItemFormatterBase {

  protected function getThemeHook(): string {
    return 'ps_diagnostic_item_full';
  }

}
