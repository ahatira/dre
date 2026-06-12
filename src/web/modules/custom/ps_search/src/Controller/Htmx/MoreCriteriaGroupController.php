<?php

declare(strict_types=1);

namespace Drupal\ps_search\Controller\Htmx;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ps_search\Search\Filter\MoreCriteriaBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * HTMX lazy-load fragments for More criteria filter groups (Phase 5A.5).
 */
final class MoreCriteriaGroupController extends ControllerBase {

  public function __construct(
    private readonly MoreCriteriaBuilder $moreCriteriaBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_search.filter_more_criteria_builder'),
    );
  }

  /**
   * Returns rendered HTML for one More criteria group (HTMX fragment).
   */
  public function items(Request $request, string $group_id): array {
    $asset = $request->query->get('asset_type');
    $assetType = is_string($asset) && $asset !== '' ? strtoupper($asset) : NULL;

    $items = $this->moreCriteriaBuilder->getGroupItems($group_id, $assetType);
    if ($items === []) {
      throw new NotFoundHttpException();
    }

    $idPrefix = $request->query->get('id_prefix');
    $idPrefix = is_string($idPrefix) && $idPrefix !== '' ? $idPrefix : 'ps-more';

    return [
      '#theme' => 'ps_search_more_criteria_items',
      '#items' => $items,
      '#id_prefix' => $idPrefix,
      '#cache' => [
        'max-age' => 0,
        'contexts' => ['url.query_args:asset_type', 'url.query_args:id_prefix'],
      ],
    ];
  }

}
