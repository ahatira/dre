<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Admin landing page for ps_offer configuration.
 */
final class OfferAdminOverviewController extends ControllerBase {

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
   * Redirects the legacy hub path to the overview tab.
   */
  public function redirectToOverview(): RedirectResponse {
    return $this->redirect('ps_offer.admin_overview');
  }

  /**
   * Overview of offer configuration sections.
   */
  public function overview(): array {
    $build = [
      '#attached' => [
        'library' => ['ps_offer/admin_overview'],
      ],
      '#cache' => [
        'contexts' => ['user.permissions', 'languages:language_interface'],
        'max-age' => 0,
      ],
    ];

    $build['intro'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-admin-overview__intro']],
      'lead' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Configure offer display on cards, search results and detail pages.'),
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
        '#attributes' => ['class' => ['ps-offer-admin-overview__group']],
        'links' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-offer-admin-overview__group-links']],
          '#theme' => 'admin_block_content',
          '#content' => $links,
        ],
      ];
    }

    $build['footer'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-admin-overview__footer']],
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
      $this->t('This site is multilingual. Edit wording in the default site language in each settings tab. Use the Translate tab for other enabled languages. Budget, Popover, Surface and Media share one display-labels form.'),
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
    return [
      [
        'id' => 'display',
        'title' => $this->t('Display labels'),
        'description' => $this->t('Wording shown with prices and surfaces.'),
        'items' => [
          [
            'title' => $this->t('Budget display labels'),
            'description' => $this->t('On request, TTC, “from” prefix and price info aria-label.'),
            'route' => 'ps_offer.budget_display_settings',
          ],
          [
            'title' => $this->t('Budget info popover'),
            'description' => $this->t('HT/HC/CC/HD definitions and default agency fees text.'),
            'route' => 'ps_offer.budget_popover_settings',
          ],
          [
            'title' => $this->t('Surface display labels'),
            'description' => $this->t('Surface KPI and divisibility labels on cards and detail blocks.'),
            'route' => 'ps_offer.surface_display_settings',
          ],
        ],
      ],
      [
        'id' => 'detail',
        'title' => $this->t('Offer detail page'),
        'description' => $this->t('Structure and content blocks on the full offer page.'),
        'items' => [
          [
            'title' => $this->t('Section headings'),
            'description' => $this->t('Titles, icons and transport feature group for detail sections.'),
            'route' => 'ps_offer.section_settings',
          ],
          [
            'title' => $this->t('Interactive map'),
            'description' => $this->t('Map behaviour, markers and surrounding area on the location section.'),
            'route' => 'ps_offer.map_settings',
          ],
          [
            'title' => $this->t('Contact'),
            'description' => $this->t('Default consultant when an offer has no primary or secondary agent.'),
            'route' => 'ps_offer.contact_settings',
          ],
        ],
      ],
      [
        'id' => 'media',
        'title' => $this->t('Media and gallery'),
        'description' => $this->t('Fallback image and gallery badge icons.'),
        'items' => [
          [
            'title' => $this->t('Media and gallery'),
            'description' => $this->t('Default offer image, alt text and photo/video/3D/plan badge icons.'),
            'route' => 'ps_offer.media_settings',
          ],
        ],
      ],
      [
        'id' => 'reference',
        'title' => $this->t('Reference generation'),
        'description' => $this->t('Patterns and alias sets for CRM offer references.'),
        'items' => [
          [
            'title' => $this->t('Reference generation'),
            'description' => $this->t('Manage patterns and alias sets used when generating offer references.'),
            'route' => 'ps_offer.reference_config',
          ],
        ],
      ],
    ];
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
