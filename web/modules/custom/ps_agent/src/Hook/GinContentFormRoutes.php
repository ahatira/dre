<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hook implementations for Gin content-form integration.
 */
final class GinContentFormRoutes
{
  /**
   * Implements hook_gin_content_form_routes().
   *
   * Registers Agent add/edit routes as content forms so Gin can apply
   * sticky actions and the right sidebar layout.
   */
    #[Hook('gin_content_form_routes')]
    public function ginContentFormRoutes(): array
    {
        return [
            'entity.agent.add_form',
            'entity.agent.edit_form',
        ];
    }
}
