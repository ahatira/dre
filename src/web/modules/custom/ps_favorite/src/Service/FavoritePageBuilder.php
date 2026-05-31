<?php

declare(strict_types=1);

namespace Drupal\ps_favorite\Service;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

final class FavoritePageBuilder {

  use StringTranslationTrait;

  private const PAGE_SIZE = 12;

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly EntityDisplayRepositoryInterface $entityDisplayRepository,
    private readonly FavoriteManagerInterface $favoriteManager,
    private readonly PagerManagerInterface $pagerManager,
    private readonly FavoriteLazyBuilder $favoriteLazyBuilder,
  ) {}

  public function buildPage(): array {
    $total = $this->favoriteManager->getFavoritesCount();
    $pager = $this->pagerManager->createPager($total, self::PAGE_SIZE);
    $offset = $pager->getCurrentPage() * self::PAGE_SIZE;

    return $this->buildList(
      $this->favoriteManager->getFavorites(NULL, self::PAGE_SIZE, $offset),
      'page',
      $total,
      $total > self::PAGE_SIZE ? ['#type' => 'pager'] : [],
    );
  }

  public function buildOffcanvas(): array {
    $total = $this->favoriteManager->getFavoritesCount();

    return $this->buildList(
      $this->favoriteManager->getFavorites(NULL, 20, 0),
      'offcanvas',
      $total,
      [],
    );
  }

  /**
   * @param \Drupal\Core\Entity\EntityInterface[] $entities
   *   Favorite entities.
   */
  private function buildList(array $entities, string $context, int $total, array $pager): array {
    $items = [];
    foreach ($entities as $entity) {
      if ($entity instanceof EntityInterface && $this->favoriteManager->supportsEntity($entity) && $entity->access('view')) {
        $items[] = $this->buildCard($entity);
      }
    }

    return [
      '#theme' => 'ps_favorite_list',
      '#context' => $context,
      '#title' => NULL,
      '#items' => $items,
      '#empty_title' => $this->t('No favorites yet'),
      '#empty_text' => $this->t('Use the favorite button on a supported entity to keep it close at hand.'),
      '#show_view_all' => $context === 'offcanvas' && $total > count($items),
      '#page_url' => Url::fromRoute('ps_favorite.page')->toString(),
      '#pager' => $pager,
      '#attached' => [
        'library' => ['ps_favorite/favorites'],
      ],
      '#cache' => [
        'tags' => ['ps_favorite:list', 'ps_favorite:count'],
        'max-age' => 0,
      ],
    ];
  }

  private function buildCard(EntityInterface $entity): array {
    return [
      '#theme' => 'ps_favorite_card',
      '#entity_type_id' => $entity->getEntityTypeId(),
      '#entity_id' => (int) $entity->id(),
      '#title' => $entity->label(),
      '#url' => $this->buildCanonicalUrl($entity),
      '#entity_view' => $this->buildEntityPreview($entity),
      '#toggle_button' => $this->favoriteLazyBuilder->buildButtonRenderable($entity, 'card'),
    ];
  }

  private function buildEntityPreview(EntityInterface $entity): ?array {
    $configuredViewMode = $this->favoriteManager->getPreferredViewMode($entity);
    if ($configuredViewMode !== NULL && ($display = $this->loadViewDisplay($entity, $configuredViewMode)) !== NULL) {
      return $this->sanitizePreviewRenderArray($display->build($entity));
    }

    if (($display = $this->loadViewDisplay($entity, 'card_favorite')) !== NULL) {
      return $this->sanitizePreviewRenderArray($display->build($entity));
    }

    if (($display = $this->loadViewDisplay($entity, 'teaser')) !== NULL) {
      return $this->sanitizePreviewRenderArray($display->build($entity));
    }

    return NULL;
  }

  /**
   * Removes noisy metadata fields from compact favorite card previews.
   *
   * @param array<string, mixed> $build
   *   Display render array.
   *
   * @return array<string, mixed>
   *   Sanitized render array.
   */
  private function sanitizePreviewRenderArray(array $build): array {
    unset($build['uid'], $build['created'], $build['title'], $build['links']);
    return $build;
  }

  private function loadViewDisplay(EntityInterface $entity, string $viewMode): ?EntityViewDisplayInterface {
    $entityTypeId = $entity->getEntityTypeId();
    $bundle = $entity->bundle();
    $displayId = $entityTypeId . '.' . $bundle . '.' . $viewMode;
    $display = $this->entityTypeManager->getStorage('entity_view_display')->load($displayId);
    if ($display instanceof EntityViewDisplayInterface) {
      return $display;
    }

    return NULL;
  }

  private function buildCanonicalUrl(EntityInterface $entity): ?string {
    if (!$entity->hasLinkTemplate('canonical')) {
      return NULL;
    }

    return $entity->toUrl()->toString();
  }

}
