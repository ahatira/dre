<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Element;

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
        if (!isset($variables['elements']['#agent'])) {
            return;
        }

        /** @var \Drupal\ps_agent\Entity\AgentInterface $agent */
        $agent = $variables['elements']['#agent'];
        $variables['agent'] = $agent;
        $variables['label'] = $agent->label();
        $variables['view_mode'] = $variables['elements']['#view_mode'] ?? 'full';
        $variables['display_submitted'] = false;

        if ($agent->hasLinkTemplate('canonical')) {
            $variables['url'] = $agent->toUrl('canonical')->toString();
        }

        $variables['content'] = [];
        foreach (Element::children($variables['elements']) as $key) {
            $variables['content'][$key] = $variables['elements'][$key];
        }
    }
}
