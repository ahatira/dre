<?php

declare(strict_types=1);

namespace Drupal\ps_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Admin landing page for ps_search configuration.
 */
final class SearchAdminOverviewController extends ControllerBase {

  /**
   * Overview of search configuration sections.
   */
  public function overview(): array {
    $items = [
      [
        '#type' => 'link',
        '#title' => $this->t('SEO URL mappings'),
        '#url' => Url::fromRoute('ps_search.seo_url_mappings_form'),
        '#attributes' => ['class' => ['admin-item__link']],
      ],
      [
        '#type' => 'link',
        '#title' => $this->t('Map zone settings'),
        '#url' => Url::fromRoute('ps_search.map_zone_settings_form'),
        '#attributes' => ['class' => ['admin-item__link']],
      ],
      [
        '#type' => 'link',
        '#title' => $this->t('API limits and cache'),
        '#url' => Url::fromRoute('ps_search.api_settings_form'),
        '#attributes' => ['class' => ['admin-item__link']],
      ],
      [
        '#type' => 'link',
        '#title' => $this->t('Feature search filters'),
        '#url' => Url::fromRoute('ps_search.feature_filter_sync_settings'),
        '#attributes' => ['class' => ['admin-item__link']],
      ],
      [
        '#type' => 'link',
        '#title' => $this->t('Card Push'),
        '#url' => Url::fromRoute('ps_search.card_push_admin_overview'),
        '#attributes' => ['class' => ['admin-item__link']],
      ],
      [
        '#type' => 'link',
        '#title' => $this->t('Search alerts'),
        '#url' => Url::fromRoute('ps_search.alert_settings_form'),
        '#attributes' => ['class' => ['admin-item__link']],
      ],
    ];

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-search-admin-overview']],
      'intro' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Module ps_search 1.0.0 — search engine, filters, map and public API under /api/ps/*.'),
      ],
      'sections' => [
        '#type' => 'item_list',
        '#title' => $this->t('Configuration'),
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

}
