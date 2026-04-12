<?php

declare(strict_types=1);

namespace Drupal\ps_favorites\Hook;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Hooks for favorites display integration.
 */
final class FavoritesHooks
{
    public function __construct(
        private readonly AccountProxyInterface $currentUser,
    ) {
    }

  /**
   * Implements hook_entity_view().
   */
    #[Hook('entity_view')]
    public function entityView(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display, string $view_mode): void
    {
        if ($entity->getEntityTypeId() !== 'node') {
            return;
        }

        if (!method_exists($entity, 'bundle') || $entity->bundle() !== 'offer') {
            return;
        }

        if ($view_mode === 'full') {
            return;
        }

        if ($this->currentUser->isAuthenticated()) {
            $build['ps_favorites_toggle'] = [
            '#type' => 'container',
            '#attributes' => [
            'class' => ['ps-favorites-toggle', 'ps-favorites-toggle--logged'],
            ],
            'link' => [
            '#lazy_builder' => ['flag.link_builder:build', ['node', (string) $entity->id(), 'offer_favorite', $view_mode]],
            '#create_placeholder' => true,
            ],
            '#weight' => -100,
            '#attached' => [
            'library' => ['ps_favorites/favorites'],
            ],
            ];

            return;
        }

        $build['ps_favorites_toggle'] = [
        '#theme' => 'ps_favorites_offer_toggle',
        '#nid' => (int) $entity->id(),
        '#weight' => -100,
        '#attached' => [
        'library' => ['ps_favorites/favorites'],
        ],
        '#cache' => [
        'contexts' => ['user.roles:anonymous'],
        'tags' => ['node:' . $entity->id()],
        ],
        ];
    }
}
