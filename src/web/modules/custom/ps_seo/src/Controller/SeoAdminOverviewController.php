<?php

declare(strict_types=1);

namespace Drupal\ps_seo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Admin landing page for ps_seo configuration.
 */
final class SeoAdminOverviewController extends ControllerBase {

  /**
   * Overview of SEO configuration sections.
   */
  public function overview(): array {
    $items = [
      [
        '#type' => 'link',
        '#title' => $this->t('Metatag defaults'),
        '#url' => Url::fromRoute('entity.metatag_defaults.collection'),
        '#attributes' => ['class' => ['admin-item__link']],
      ],
      [
        '#type' => 'link',
        '#title' => $this->t('Pathauto patterns'),
        '#url' => Url::fromRoute('entity.pathauto_pattern.collection'),
        '#attributes' => ['class' => ['admin-item__link']],
      ],
      [
        '#type' => 'link',
        '#title' => $this->t('URL redirects'),
        '#url' => Url::fromRoute('entity.redirect.collection'),
        '#attributes' => ['class' => ['admin-item__link']],
      ],
      [
        '#type' => 'link',
        '#title' => $this->t('Simple XML sitemap'),
        '#url' => Url::fromRoute('simple_sitemap.sitemaps'),
        '#attributes' => ['class' => ['admin-item__link']],
      ],
    ];

    if ($this->moduleHandler()->moduleExists('ps_search')) {
      $items[] = [
        '#type' => 'link',
        '#title' => $this->t('Search SEO URL mappings'),
        '#url' => Url::fromRoute('ps_search.seo_url_mappings_form'),
        '#attributes' => ['class' => ['admin-item__link']],
      ];
    }

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-seo-admin-overview']],
      'intro' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Module ps_seo — Metatag defaults, Schema.org, sitemap and search listing head tags.'),
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
