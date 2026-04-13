<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hook implementations for template preprocessing.
 *
 * @see hook_preprocess_HOOK()
 */
final class PreprocessAgent
{
  /**
   * Prepares variables for agent templates.
   *
   * Implements hook_preprocess_agent().
   *
   * @param array $variables
   *   An associative array of variables to be passed to the template.
   *   Variables from template_preprocess_agent() are included.
   */
    #[Hook('preprocess_agent')]
    public function preprocessAgent(array &$variables): void
    {
        if (isset($variables['elements']['#agent'])) {
            $variables['agent'] = $variables['elements']['#agent'];
        }
    }
}
