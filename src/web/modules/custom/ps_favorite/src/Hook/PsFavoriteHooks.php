<?php

declare(strict_types=1);

namespace Drupal\ps_favorite\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_favorite\Repository\FavoriteRepositoryInterface;
use Drupal\ps_favorite\Service\FavoriteManagerInterface;
use Drupal\user\UserInterface;

final class PsFavoriteHooks {

  public function __construct(
    private readonly FavoriteManagerInterface $favoriteManager,
    private readonly FavoriteRepositoryInterface $favoriteRepository,
  ) {}

  #[Hook('theme')]
  public function theme(array $existing, string $type, string $theme, string $path): array {
    return [
      'ps_favorite_button' => [
        'variables' => [
          'entity_type_id' => NULL,
          'entity_id' => NULL,
          'toggle_url' => NULL,
          'is_favorite' => FALSE,
          'context' => 'inline',
          'label_add' => NULL,
          'label_remove' => NULL,
          'text_add' => NULL,
          'text_remove' => NULL,
        ],
        'template' => 'ps-favorite-button',
      ],
      'ps_favorite_card' => [
        'variables' => [
          'entity_type_id' => NULL,
          'entity_id' => NULL,
          'title' => NULL,
          'url' => NULL,
          'entity_view' => NULL,
          'toggle_button' => NULL,
        ],
        'template' => 'ps-favorite-card',
      ],
      'ps_favorite_list' => [
        'variables' => [
          'context' => 'page',
          'title' => NULL,
          'items' => [],
          'empty_title' => NULL,
          'empty_text' => NULL,
          'show_view_all' => FALSE,
          'page_url' => NULL,
          'pager' => [],
        ],
        'template' => 'ps-favorite-list',
      ],
      'ps_favorite_header_block' => [
        'variables' => [
          'offcanvas_url' => NULL,
          'dialog_options' => NULL,
          'count' => NULL,
        ],
        'template' => 'ps-favorite-header-block',
      ],
    ];
  }

  #[Hook('entity_predelete')]
  public function entityPredelete(\Drupal\Core\Entity\EntityInterface $entity): void {
    $this->favoriteRepository->removeByEntity($entity->getEntityTypeId(), (int) $entity->id());
  }

  #[Hook('user_login')]
  public function userLogin(UserInterface $account): void {
    $this->favoriteManager->mergeAnonymousFavorites();
  }

}