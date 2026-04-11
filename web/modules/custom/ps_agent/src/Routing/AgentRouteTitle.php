<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Routing;

use Drupal\ps_agent\Entity\AgentInterface;

/**
 * Route title callback for agent routes.
 */
final class AgentRouteTitle {

  /**
   * Gets the title for the canonical route.
   *
   * @param \Drupal\ps_agent\Entity\AgentInterface $agent
   *   The agent entity.
   *
   * @return string
   *   The page title.
   */
  public static function canonical(AgentInterface $agent): string {
    return $agent->label() ?? 'Agent';
  }

  /**
   * Gets the title for the edit route.
   *
   * @param \Drupal\ps_agent\Entity\AgentInterface $agent
   *   The agent entity.
   *
   * @return string
   *   The page title.
   */
  public static function edit(AgentInterface $agent): string {
    return t('Edit %label', ['%label' => $agent->label()]);
  }

}
