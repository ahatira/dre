<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Ajax placeholder endpoint for offer comparator toggles.
 */
final class OfferCompareController extends ControllerBase {

  /**
   * Returns a lightweight comparator placeholder response.
   */
  public function toggle(NodeInterface $node): JsonResponse {
    if ($node->bundle() !== 'offer') {
      return new JsonResponse([
        'status' => 'error',
        'message' => (string) $this->t('Invalid offer.'),
      ], 400);
    }

    return new JsonResponse([
      'status' => 'ok',
      'nid' => (int) $node->id(),
      'message' => (string) $this->t('Comparator placeholder endpoint.'),
    ]);
  }

}
