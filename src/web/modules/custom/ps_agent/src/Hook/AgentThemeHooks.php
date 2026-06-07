<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Element;

/**
 * Theme hooks for PS Agent entities.
 */
final class AgentThemeHooks {

  /**
   * Registers the ps_agent theme hook.
   */
  #[Hook('theme')]
  public function theme(): array {
    return [
      'ps_agent' => [
        'render element' => 'elements',
        'initial preprocess' => static::class . ':preprocessPsAgent',
      ],
    ];
  }

  /**
   * Prepares variables for ps_agent templates.
   */
  public function preprocessPsAgent(array &$variables): void {
    $variables['ps_agent'] = $variables['elements']['#ps_agent'];
    $variables['view_mode'] = $variables['elements']['#view_mode'];

    if (isset($variables['elements']['#consultant_label'])) {
      $variables['consultant_label'] = $variables['elements']['#consultant_label'];
    }

    foreach (Element::children($variables['elements']) as $key) {
      $variables['content'][$key] = $variables['elements'][$key];
    }
  }

  /**
   * Adds template suggestions for ps_agent view modes.
   */
  #[Hook('theme_suggestions_ps_agent')]
  public function themeSuggestionsPsAgent(array $variables): array {
    $entity = $variables['elements']['#ps_agent'];
    $view_mode = strtr((string) $variables['elements']['#view_mode'], '.', '_');
    $bundle = $entity->bundle();

    return [
      'ps_agent__' . $view_mode,
      'ps_agent__' . $bundle,
      'ps_agent__' . $bundle . '__' . $view_mode,
    ];
  }

}
