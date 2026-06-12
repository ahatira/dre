<?php

declare(strict_types=1);

namespace Drupal\ps_favorite\Service;

use Drupal\Core\Access\CsrfTokenGenerator;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\Url;

final class FavoriteLazyBuilder implements TrustedCallbackInterface {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly FavoriteManagerInterface $favoriteManager,
    private readonly CsrfTokenGenerator $csrfToken,
  ) {}

  public static function trustedCallbacks(): array {
    return ['buildButton', 'buildHeaderCount'];
  }

  public function buildButton(string $entityTypeId, int $entityId, string $context = 'inline'): array {
    $entity = $this->entityTypeManager->getStorage($entityTypeId)->load($entityId);
    if (!$entity instanceof EntityInterface || !$entity->access('view') || !$this->favoriteManager->supportsEntity($entity)) {
      return [];
    }

    return $this->buildButtonRenderable($entity, $context);
  }

  public function buildButtonRenderable(EntityInterface $entity, string $context = 'inline'): array {
    if (!$this->favoriteManager->supportsEntity($entity)) {
      return [];
    }

    $isFavorite = $this->favoriteManager->isFavorite($entity);
    $entityId = (int) $entity->id();

    return [
      '#theme' => 'ps_favorite_button',
      '#entity_type_id' => $entity->getEntityTypeId(),
      '#entity_id' => $entityId,
      '#toggle_url' => Url::fromRoute('ps_favorite.toggle', ['entity_type_id' => $entity->getEntityTypeId(), 'entity_id' => $entityId])->toString(),
      '#is_favorite' => $isFavorite,
      '#context' => $context,
      '#label_add' => t('Add to favorites'),
      '#label_remove' => t('Remove from favorites'),
      '#text_add' => t('Save'),
      '#text_remove' => t('Saved'),
      '#attached' => [
        'library' => ['ps_favorite/favorites'],
        'drupalSettings' => [
          'psFavorite' => [
            'csrfToken' => $this->csrfToken->get('ps_favorite.toggle'),
            'countEndpoint' => Url::fromRoute('ps_favorite.count')->toString(),
            'countRefreshMs' => 0,
          ],
        ],
      ],
      '#cache' => [
        'tags' => array_merge($entity->getCacheTags(), [
          'ps_favorite:list',
          'ps_favorite:count',
          sprintf('ps_favorite:%s:%d', $entity->getEntityTypeId(), $entityId),
        ]),
        'contexts' => ['session', 'user'],
        'max-age' => 0,
      ],
    ];
  }

  public function buildHeaderCount(): array {
    $count = $this->favoriteManager->getFavoritesCount();

    return [
      '#type' => 'html_tag',
      '#tag' => 'span',
      '#value' => (string) $count,
      '#attributes' => [
        'class' => ['ps-favorite-header__count'],
        'data-ps-favorite-count' => '',
        'hidden' => $count > 0 ? NULL : 'hidden',
        'aria-live' => 'polite',
      ],
      '#cache' => [
        'tags' => ['ps_favorite:count'],
        'contexts' => ['session', 'user'],
        'max-age' => 0,
      ],
    ];
  }

}
