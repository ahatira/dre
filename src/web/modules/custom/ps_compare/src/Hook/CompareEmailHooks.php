<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Compare share email shell overrides (full-width layout, short H1, sign-off).
 */
final class CompareEmailHooks {

  use StringTranslationTrait;

  /**
   * Applies compare-specific email-wrap variables.
   */
  #[Hook('preprocess_email_wrap')]
  public function preprocessEmailWrap(array &$variables): void {
    if (($variables['type'] ?? '') !== 'ps_compare') {
      return;
    }

    $variables['ps_email_full_width'] = TRUE;
    $variables['ps_email_keep_signoff'] = TRUE;
    $variables['email_hide_default_signoff'] = FALSE;
    $variables['email_display_title'] = (string) $this->t('Property comparison');
  }

}
