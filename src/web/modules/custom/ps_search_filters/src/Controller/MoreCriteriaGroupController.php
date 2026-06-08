<?php

declare(strict_types=1);

namespace Drupal\ps_search_filters\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\RendererInterface;
use Drupal\ps_search_filters\Service\MoreCriteriaBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Lazy-load endpoints for More criteria filter groups.
 */
final class MoreCriteriaGroupController extends ControllerBase {

  public function __construct(
    private readonly MoreCriteriaBuilder $moreCriteriaBuilder,
    private readonly RendererInterface $renderer,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_search_filters.more_criteria_builder'),
      $container->get('renderer'),
    );
  }

  /**
   * Returns rendered HTML for one More criteria group.
   */
  public function items(Request $request, string $group_id): JsonResponse {
    $asset = $request->query->get('asset_type');
    $assetType = is_string($asset) && $asset !== '' ? strtoupper($asset) : NULL;

    $items = $this->moreCriteriaBuilder->getGroupItems($group_id, $assetType);
    if ($items === []) {
      return new JsonResponse(['html' => ''], 404);
    }

    $build = [
      '#theme' => 'ps_search_more_criteria_items',
      '#items' => $items,
    ];

    $html = (string) $this->renderer->renderRoot($build);
    return new JsonResponse(['html' => $html]);
  }

}
