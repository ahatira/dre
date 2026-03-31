<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Handle CSS classes.
 */
class PreprocessPage
{
    public function __construct(
        protected ThemeSettingsProvider $themeSettings,
    ) {
    }

  /**
   * Implements hook_preprocess_HOOK().
   */
    #[Hook('preprocess_maintenance_page')]
    #[Hook('preprocess_page')]
    public function preprocess(array &$variables): void
    {
        $variables['container'] = $this->themeSettings->getSetting('container') ?? 'container';
        $variables['header_container_class'] = $this->themeSettings->getSetting('header_container') ?? 'container-fluid';
    }
}
