<?php

declare(strict_types=1);

namespace Drupal\ps_seo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Admin landing page for ps_seo configuration.
 */
final class SeoAdminOverviewController extends ControllerBase {

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
    return $this->redirect('ps_seo.admin_overview');
  }

  /**
   * Overview of SEO configuration sections.
   */
  public function overview(): array {
    $build = [
      '#attached' => [
        'library' => ['ps_seo/admin_overview'],
      ],
      '#cache' => [
        'contexts' => ['user.permissions', 'languages:language_interface'],
        'max-age' => 0,
      ],
    ];

    $build['intro'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-seo-admin-overview__intro']],
      'lead' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Site-wide SEO shipped by ps_seo: Metatag defaults, Schema.org tokens, XML sitemap variants and search listing head tags. Search page URL slugs remain under Configuration → Search.'),
      ],
    ];

    if ($multilingualHelp = $this->buildMultilingualHelpElement()) {
      $build['multilingual_help'] = $multilingualHelp;
    }

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
        '#attributes' => ['class' => ['ps-seo-admin-overview__group']],
        'links' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-seo-admin-overview__group-links']],
          '#theme' => 'admin_block_content',
          '#content' => $links,
        ],
      ];
    }

    $build['footer'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-seo-admin-overview__footer']],
      'hub' => Link::createFromRoute(
        $this->t('Back to PS configuration hub'),
        'ps_core.config',
      )->toRenderable(),
    ];

    return $build;
  }

  /**
   * Builds a persistent multilingual help notice for the overview page.
   *
   * @return array<string, mixed>|null
   *   Render array, or NULL when not applicable.
   */
  private function buildMultilingualHelpElement(): ?array {
    if (!$this->moduleHandler->moduleExists('config_translation')) {
      return NULL;
    }

    $default = $this->languageManager->getDefaultLanguage()->getId();
    $targets = array_filter(
      $this->languageManager->getLanguages(),
      static fn(string $langcode): bool => $langcode !== $default,
      ARRAY_FILTER_USE_KEY,
    );
    if ($targets === []) {
      return NULL;
    }

    $content = [
      '#type' => 'container',
      '#attributes' => ['class' => ['messages', 'messages--info', 'ps-seo-admin-overview__multilingual-help']],
    ];

    if ($this->moduleHandler->moduleExists('ps_search') && Url::fromRoute('ps_search.seo_url_mappings_form')->access()) {
      $content['message'] = [
        '#type' => 'inline_template',
        '#template' => '{{ text }} {{ link }}.',
        '#context' => [
          'text' => $this->t('This site is multilingual. Edit Metatag defaults in the default site language on the Metatags tab. Per-language overrides are available on each Metatag default edit form when Configuration translation is enabled. Search page URL slugs are managed under'),
          'link' => Link::createFromRoute(
            $this->t('Configuration → Search → SEO URLs'),
            'ps_search.seo_url_mappings_form',
          )->toRenderable(),
        ],
      ];
    }
    else {
      $content['message'] = [
        '#markup' => $this->t('This site is multilingual. Edit Metatag defaults in the default site language on the Metatags tab. Per-language overrides are available on each Metatag default edit form when Configuration translation is enabled.'),
      ];
    }

    return $content;
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
        'id' => 'metatags',
        'title' => $this->t('Metatags and Schema.org'),
        'description' => $this->t('Default title, description, Open Graph, Twitter Cards and Schema.org tokens for global, front and offer pages.'),
        'items' => [
          [
            'title' => $this->t('Metatag defaults'),
            'description' => $this->t('Global, front page and offer defaults shipped by ps_seo (WebPage, Organization, Place). Per-offer overrides use field_metatag on offers.'),
            'route' => 'entity.metatag_defaults.collection',
          ],
        ],
      ],
      [
        'id' => 'urls',
        'title' => $this->t('URLs and redirects'),
        'description' => $this->t('Canonical URL patterns and site-wide redirect entities.'),
        'items' => [
          [
            'title' => $this->t('Pathauto patterns'),
            'description' => $this->t('Automatic URL aliases for offers and other content types.'),
            'route' => 'entity.pathauto_pattern.collection',
          ],
          [
            'title' => $this->t('URL redirects'),
            'description' => $this->t('301/302 redirect entities managed by the Redirect module.'),
            'route' => 'redirect.list',
          ],
        ],
      ],
      [
        'id' => 'sitemap',
        'title' => $this->t('XML sitemap'),
        'description' => $this->t('Published offer URLs grouped by asset type for search engines.'),
        'items' => [
          [
            'title' => $this->t('Simple XML sitemap'),
            'description' => $this->t('Index and per-asset-type sitemap variants (offer_bur, offer_com, …). Regenerate with drush simple-sitemap:generate.'),
            'route' => 'entity.simple_sitemap.collection',
          ],
        ],
      ],
      [
        'id' => 'related',
        'title' => $this->t('Related configuration'),
        'description' => $this->t('Search-specific SEO settings owned by ps_search.'),
        'items' => [],
      ],
    ];

    if ($this->moduleHandler->moduleExists('ps_search')) {
      $groups[3]['items'][] = [
        'title' => $this->t('Search configuration hub'),
        'description' => $this->t('Search page slugs, migration redirects, map zone and public API limits.'),
        'route' => 'ps_search.admin_overview',
      ];
      $groups[3]['items'][] = [
        'title' => $this->t('Search SEO URL mappings'),
        'description' => $this->t('Search page base slug and operation / asset URL segments (translatable per language).'),
        'route' => 'ps_search.seo_url_mappings_form',
      ];
      $groups[3]['items'][] = [
        'title' => $this->t('Search SEO migration redirects'),
        'description' => $this->t('One-off 301 map for legacy search URLs during platform migration.'),
        'route' => 'ps_search.seo_redirects_form',
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
