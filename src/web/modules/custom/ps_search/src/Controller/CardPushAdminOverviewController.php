<?php

declare(strict_types=1);

namespace Drupal\ps_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Admin landing page for Card Push configuration.
 */
final class CardPushAdminOverviewController extends ControllerBase {

  /**
   * Overview of configured Card Push placements and types.
   */
  public function overview(): array {
    $items = [
      [
        '#type' => 'link',
        '#title' => $this->t('Search results'),
        '#url' => Url::fromRoute('ps_search.card_push_search_results_form'),
        '#attributes' => ['class' => ['admin-item__link']],
      ],
    ];

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-search-card-push-admin-overview']],
      'intro' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Card Push inserts promotional interstitial cards into listing pages. Each placement can use a dedicated SDC (calculator, image, image + link, content block, etc.).'),
      ],
      'sections' => [
        '#type' => 'item_list',
        '#title' => $this->t('Placements'),
        '#items' => $items,
      ],
      'hub' => [
        '#type' => 'link',
        '#title' => $this->t('Back to PS configuration hub'),
        '#url' => Url::fromRoute('ps_core.config'),
      ],
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  /**
   * Redirects legacy push settings URL to the Card Push search results form.
   */
  public function legacyPushRedirect(): RedirectResponse {
    return new RedirectResponse(
      Url::fromRoute('ps_search.card_push_search_results_form')->toString(),
      301,
    );
  }

}
