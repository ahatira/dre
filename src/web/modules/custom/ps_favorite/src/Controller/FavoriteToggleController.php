<?php

declare(strict_types=1);

namespace Drupal\ps_favorite\Controller;

use Drupal\Core\Access\CsrfTokenGenerator;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_favorite\Service\FavoriteManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class FavoriteToggleController implements ContainerInjectionInterface {

  use StringTranslationTrait;

  public function __construct(
    private readonly FavoriteManagerInterface $favoriteManager,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly CsrfTokenGenerator $csrfToken,
  ) {}

  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('ps_favorite.manager'),
      $container->get('entity_type.manager'),
      $container->get('csrf_token'),
    );
  }

  public function toggle(Request $request, string $entity_type_id, int $entity_id): JsonResponse {
    $entity = $this->loadEntity($entity_type_id, $entity_id);
    if (!$entity instanceof EntityInterface || !$entity->access('view') || !$this->favoriteManager->supportsEntity($entity)) {
      return new JsonResponse(['message' => (string) $this->t('Unsupported entity target.')], 404);
    }

    $token = (string) $request->headers->get('X-CSRF-Token', '');
    if (!$this->csrfToken->validate($token, 'ps_favorite.toggle')) {
      return new JsonResponse(['message' => (string) $this->t('Invalid CSRF token.')], 403);
    }

    $isFavorite = $this->favoriteManager->isFavorite($entity);
    if (!$isFavorite) {
      $added = $this->favoriteManager->addFavorite($entity);
      if (!$added) {
        $limit = $this->favoriteManager->getLimitForEntity($entity);
        $message = $limit === NULL
          ? (string) $this->t('Unable to add this favorite right now.')
          : (string) $this->t('Favorite limit reached (@limit).', ['@limit' => $limit]);

        return new JsonResponse([
          'entityId' => (int) $entity->id(),
          'entityTypeId' => $entity->getEntityTypeId(),
          'isFavorite' => FALSE,
          'count' => $this->favoriteManager->getFavoritesCount(),
          'message' => $message,
          'limit' => $limit,
        ], 409);
      }
      $isFavorite = TRUE;
    }
    else {
      $this->favoriteManager->removeFavorite($entity);
      $isFavorite = FALSE;
    }

    return new JsonResponse([
      'entityId' => (int) $entity->id(),
      'entityTypeId' => $entity->getEntityTypeId(),
      'isFavorite' => $isFavorite,
      'count' => $this->favoriteManager->getFavoritesCount(),
      'message' => $isFavorite ? (string) $this->t('Added to favorites.') : (string) $this->t('Removed from favorites.'),
    ]);
  }

  private function loadEntity(string $entityTypeId, int $entityId): ?EntityInterface {
    if (!$this->entityTypeManager->hasDefinition($entityTypeId)) {
      return NULL;
    }

    $entity = $this->entityTypeManager->getStorage($entityTypeId)->load($entityId);
    return $entity instanceof EntityInterface ? $entity : NULL;
  }

}
