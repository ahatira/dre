<?php

declare(strict_types=1);

namespace Drupal\ps_favorite\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\ps_favorite\Service\FavoritePageBuilder;
use Drupal\ps_favorite\Service\FavoriteManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final class FavoriteController implements ContainerInjectionInterface {

  public function __construct(
    private readonly FavoritePageBuilder $pageBuilder,
    private readonly FavoriteManagerInterface $favoriteManager,
  ) {}

  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('ps_favorite.page_builder'),
      $container->get('ps_favorite.manager'),
    );
  }

  public function page(): array {
    return $this->pageBuilder->buildPage();
  }

  public function offcanvas(): array {
    return $this->pageBuilder->buildOffcanvas();
  }

  public function count(): JsonResponse {
    return new JsonResponse([
      'count' => $this->favoriteManager->getFavoritesCount(),
    ]);
  }

}
