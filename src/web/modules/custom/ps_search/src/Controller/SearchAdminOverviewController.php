<?php

declare(strict_types=1);

namespace Drupal\ps_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Admin landing page for ps_search configuration.
 */
final class SearchAdminOverviewController extends ControllerBase {

  public function __construct(
    ModuleHandlerInterface $moduleHandler,
    LanguageManagerInterface $languageManager,
  ) {
    $this->moduleHandler = $moduleHandler;
    $this->languageManager = $languageManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('module_handler'),
      $container->get('language_manager'),
    );
  }

  /**
   * Redirects the hub path to the overview tab.
   */
  public function redirectToOverview(): RedirectResponse {
    return $this->redirect('ps_search.admin_overview');
  }

  /**
   * Overview of search configuration sections.
   */
  public function overview(): array {
    $build = [
      '#attached' => [
        'library' => ['ps_search/admin_overview'],
      ],
      '#cache' => [
        'contexts' => ['user.permissions', 'languages:language_interface'],
        'max-age' => 0,
      ],
    ];

    $build['intro'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-search-admin-overview__intro']],
      'lead' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Configure property search URLs, map behaviour, feature filters, public API limits and email alerts.'),
      ],
    ];

    $this->addMultilingualHelpMessage();

    foreach ($this->configurationGroups() as $group) {
      $links = $this->buildGroupLinks($group['items']);
      if ($links === []) {
        continue;
      }

      $build['groups'][$group['id']] = [
        '#type' => 'details',
        '#title' => $group['title'],
        '#description' => $group['description'],
        '#open' => TRUE,
        '#attributes' => ['class' => ['ps-search-admin-overview__group']],
        'links' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-search-admin-overview__group-links']],
          '#theme' => 'admin_block_content',
          '#content' => $links,
        ],
      ];
    }

    $build['footer'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-search-admin-overview__footer']],
      'hub' => Link::createFromRoute(
        $this->t('Back to PS configuration hub'),
        'ps_core.config',
      )->toRenderable(),
    ];

    return $build;
  }

  /**
   * Adds an info message when additional content languages are enabled.
   */
  private function addMultilingualHelpMessage(): void {
    if (!$this->moduleHandler->moduleExists('config_translation')) {
      return;
    }

    $default = $this->languageManager->getDefaultLanguage()->getId();
    $targets = array_filter(
      $this->languageManager->getLanguages(),
      static fn(string $langcode): bool => $langcode !== $default,
      ARRAY_FILTER_USE_KEY,
    );
    if ($targets === []) {
      return;
    }

    $this->messenger()->addMessage(
      $this->t('This site is multilingual. Edit SEO URL slugs in the default site language on the SEO URLs tab. Use the Translate tab for other enabled languages. Filter labels (budget, surface, capacity) are managed under Configuration → Context → Labels.'),
      'info',
    );
  }

  /**
   * Returns grouped configuration sections for the overview page.
   *
   * @return list<array{id: string, title: \Drupal\Core\StringTranslation\TranslatableMarkup, description: \Drupal\Core\StringTranslation\TranslatableMarkup, items: list<array{title: \Drupal\Core\StringTranslation\TranslatableMarkup, description: \Drupal\Core\StringTranslation\TranslatableMarkup, route: string}>}>
   *   Configuration groups.
   */
  private function configurationGroups(): array {
    $groups = [
      [
        'id' => 'seo',
        'title' => $this->t('SEO and URLs'),
        'description' => $this->t('Public search page slugs and one-off migration redirects.'),
        'items' => [
          [
            'title' => $this->t('SEO URL mappings'),
            'description' => $this->t('Search page slug, operation and asset type URL segments, and legacy asset aliases.'),
            'route' => 'ps_search.seo_url_mappings_form',
          ],
          [
            'title' => $this->t('SEO migration redirects'),
            'description' => $this->t('301 map for legacy search URLs during platform migration (clean break).'),
            'route' => 'ps_search.seo_redirects_form',
          ],
        ],
      ],
      [
        'id' => 'map',
        'title' => $this->t('Map and search zone'),
        'description' => $this->t('Default geographic zone, map shell and marker limits on the search page.'),
        'items' => [
          [
            'title' => $this->t('Map zone settings'),
            'description' => $this->t('Default center and radius, marker cap, isochrone providers and Google Map shell options.'),
            'route' => 'ps_search.map_zone_settings_form',
          ],
        ],
      ],
      [
        'id' => 'filters',
        'title' => $this->t('Filters and features'),
        'description' => $this->t('Feature-backed filters, catalogue sync and contextual filter wording.'),
        'items' => [
          [
            'title' => $this->t('Feature search filters'),
            'description' => $this->t('Sync exposed feature filters into the search view and Solr index.'),
            'route' => 'ps_search.feature_filter_sync_settings',
          ],
        ],
      ],
      [
        'id' => 'api',
        'title' => $this->t('Public API'),
        'description' => $this->t('Rate limits and server-side cache for /api/ps/* endpoints.'),
        'items' => [
          [
            'title' => $this->t('API limits and cache'),
            'description' => $this->t('IP rate limiting and HTTP cache TTLs for markers, isochrone, location and HTMX fragments.'),
            'route' => 'ps_search.api_settings_form',
          ],
        ],
      ],
      [
        'id' => 'alerts',
        'title' => $this->t('Search alerts'),
        'description' => $this->t('Cron digest emails and retention for property search alert subscriptions.'),
        'items' => [
          [
            'title' => $this->t('Search alert settings'),
            'description' => $this->t('Enable cron notifications, sender details, batch size and anonymous retention.'),
            'route' => 'ps_search.alert_settings_form',
          ],
        ],
      ],
      [
        'id' => 'related',
        'title' => $this->t('Related configuration'),
        'description' => $this->t('Cross-domain settings that affect search filters, alerts and site-wide SEO.'),
        'items' => [],
      ],
    ];

    if ($this->moduleHandler->moduleExists('ps_context')) {
      $groups[2]['items'][] = [
        'title' => $this->t('Context overview'),
        'description' => $this->t('Filter visibility matrix and rules by asset × operation.'),
        'route' => 'ps_context.context_overview',
      ];
      $groups[2]['items'][] = [
        'title' => $this->t('Search filter labels'),
        'description' => $this->t('Budget, surface and capacity wording per asset × operation profile.'),
        'route' => 'entity.ps_context_label_profile.collection',
      ];
    }

    if ($this->moduleHandler->moduleExists('ps_feature')) {
      $groups[2]['items'][] = [
        'title' => $this->t('Feature catalogue'),
        'description' => $this->t('Definitions and groups used when exposing features as search filters.'),
        'route' => 'ps_feature.admin_overview',
      ];
    }

    $groups[4]['items'][] = [
      'title' => $this->t('Stored search alerts'),
      'description' => $this->t('Browse and manage alert subscriptions created from the search page webform.'),
      'route' => 'entity.search_alert.collection',
    ];

    if ($this->moduleHandler->moduleExists('ps_seo')) {
      $groups[5]['items'][] = [
        'title' => $this->t('Site-wide SEO'),
        'description' => $this->t('Metatag defaults, pathauto patterns, redirects and XML sitemap.'),
        'route' => 'ps_seo.admin_overview',
      ];
    }

    if ($this->moduleHandler->moduleExists('ps_core')) {
      $groups[5]['items'][] = [
        'title' => $this->t('Contact forms'),
        'description' => $this->t('Contact hub needs, display mode, and urgency contact block.'),
        'route' => 'ps_form.contact_settings',
      ];
    }

    return $groups;
  }

  /**
   * Builds admin block links for a configuration group.
   *
   * @param list<array{title: \Drupal\Core\StringTranslation\TranslatableMarkup, description: \Drupal\Core\StringTranslation\TranslatableMarkup, route: string}> $items
   *   Group link definitions.
   *
   * @return list<array<string, mixed>>
   *   Items for the admin_block_content theme.
   */
  private function buildGroupLinks(array $items): array {
    $links = [];

    foreach ($items as $item) {
      $url = Url::fromRoute($item['route']);
      if (!$url->access($this->currentUser())) {
        continue;
      }

      $links[] = [
        'title' => $item['title'],
        'url' => $url,
        'description' => $item['description'],
      ];
    }

    return $links;
  }

}
