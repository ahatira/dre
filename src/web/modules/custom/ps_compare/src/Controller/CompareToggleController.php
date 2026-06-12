<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Controller;

use Drupal\Core\Access\CsrfTokenGenerator;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_compare\Service\CompareManagerInterface;
use Drupal\ps_compare\Service\ComparePathResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */
final class CompareToggleController implements ContainerInjectionInterface {

  use StringTranslationTrait;

  public function __construct(
    private readonly CompareManagerInterface $compareManager,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly CsrfTokenGenerator $csrfToken,
    private readonly ComparePathResolver $comparePathResolver,
  ) {}

  /**
   *
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('ps_compare.manager'),
      $container->get('entity_type.manager'),
      $container->get('csrf_token'),
      $container->get('ps_compare.path_resolver'),
    );
  }

  /**
   *
   */
  public function toggle(Request $request, string $entity_type_id, int $entity_id): JsonResponse {
    $entity = $this->loadEntity($entity_type_id, $entity_id);
    if (!$entity instanceof EntityInterface || !$entity->access('view') || !$this->compareManager->supportsEntity($entity)) {
      return new JsonResponse(['message' => (string) $this->t('Unsupported entity target.')], 404);
    }

    $token = (string) $request->headers->get('X-CSRF-Token', '');
    if (!$this->csrfToken->validate($token, 'ps_compare.toggle')) {
      return new JsonResponse(['message' => (string) $this->t('Invalid CSRF token.')], 403);
    }

    $isCompared = $this->compareManager->isCompared($entity);
    if (!$isCompared) {
      $added = $this->compareManager->addCompare($entity);
      if (!$added) {
        $max = $this->compareManager->getMaxItems();
        $atLimit = $this->compareManager->getCompareCount($entity->getEntityTypeId()) >= $max;
        $message = $atLimit
          ? (string) $this->t("You can't compare more than @max ads at the same time. Delete one to add this one.", ['@max' => $max])
          : (string) $this->t('Unable to add this property to the comparison right now.');

        return new JsonResponse([
          'entityId' => (int) $entity->id(),
          'entityTypeId' => $entity->getEntityTypeId(),
          'isCompared' => FALSE,
          'count' => $this->compareManager->getCompareCount(),
          'canCompare' => $this->compareManager->canOpenComparisonPage(),
          'compareUrl' => $this->comparePathResolver->getPublicPath(),
          'message' => $message,
          'limit' => $atLimit ? $max : NULL,
        ], 409);
      }
      $isCompared = TRUE;
    }
    else {
      $this->compareManager->removeCompare($entity);
      $isCompared = FALSE;
    }

    return new JsonResponse([
      'entityId' => (int) $entity->id(),
      'entityTypeId' => $entity->getEntityTypeId(),
      'isCompared' => $isCompared,
      'count' => $this->compareManager->getCompareCount(),
      'canCompare' => $this->compareManager->canOpenComparisonPage(),
      'compareUrl' => $this->comparePathResolver->getPublicPath(),
      'message' => $isCompared
        ? (string) $this->t('Added to comparison.')
        : (string) $this->t('Removed from comparison.'),
    ]);
  }

  /**
   *
   */
  private function loadEntity(string $entityTypeId, int $entityId): ?EntityInterface {
    if (!$this->entityTypeManager->hasDefinition($entityTypeId)) {
      return NULL;
    }

    $entity = $this->entityTypeManager->getStorage($entityTypeId)->load($entityId);
    return $entity instanceof EntityInterface ? $entity : NULL;
  }

}
