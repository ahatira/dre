<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_search\Service\SearchResultCounter;
use Drupal\views\ViewExecutable;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Adjusts list pager size for small map zones.
 *
 * When zone_count is within list_pager_threshold (map zone settings), the
 * page_list display loads every offer in one response (listLoadAll). Larger
 * zones keep Views Load More (40 items per page in config/install).
 */
final class SearchListPagerHooks {

  private const VIEW_ID = 'ps_search_offers';

  private const DISPLAY_ID = 'page_list';

  public function __construct(
    private readonly SearchResultCounter $resultCounter,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly RequestStack $requestStack,
  ) {}

  /**
   * Implements hook_views_pre_view().
   */
  #[Hook('views_pre_view')]
  public function viewsPreView(ViewExecutable $view, string $display_id, array &$args): void {
    if ($view->id() !== self::VIEW_ID || $display_id !== self::DISPLAY_ID) {
      return;
    }

    $request = $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return;
    }

    $threshold = max(1, (int) ($this->configFactory->get('ps_search.map_zone_settings')->get('list_pager_threshold') ?? 100));
    $zoneCount = $this->resultCounter->countInBounds($request);
    if ($zoneCount > 0 && $zoneCount <= $threshold) {
      $view->setItemsPerPage($zoneCount);
    }
  }

}
