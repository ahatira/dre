<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Attaches page-level assets and settings.
 */
class PageAttachments
{
    /**
     * Implements hook_page_attachments().
     */
    #[Hook('page_attachments')]
    public function attach(array &$attachments): void
    {
        $labelMode = theme_get_setting('header_language_label_mode') ?: 'code_capitalized';
        $attachments['#attached']['drupalSettings']['uiSuiteBnppre']['header']['languageLabelMode'] = $labelMode;
    }
}
