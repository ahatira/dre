<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hook implementations for theme declarations.
 *
 * @see hook_theme()
 */
final class Theme
{
  /**
   * Implements hook_theme().
   *
   * Declares theme hooks for agent templates.
   */
    #[Hook('theme')]
    public function theme(): array
    {
        return [
        'agent' => [
        'render element' => 'elements',
        'template' => 'agent',
        ],
        ];
    }
}
