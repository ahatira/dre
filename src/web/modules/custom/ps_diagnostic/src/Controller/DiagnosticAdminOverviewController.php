<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Admin landing page for ps_diagnostic configuration.
 */
final class DiagnosticAdminOverviewController extends ControllerBase {

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
    return $this->redirect('ps_diagnostic.admin_overview');
  }

  /**
   * Redirects to the certification label taxonomy overview.
   */
  public function redirectToCertificationLabels(): RedirectResponse {
    return new RedirectResponse(
      Url::fromUri('internal:/admin/structure/taxonomy/manage/certification_label/overview')->toString(),
    );
  }

  /**
   * Overview of diagnostic configuration sections.
   */
  public function overview(): array {
    $build = [
      '#attached' => [
        'library' => ['ps_diagnostic/admin_overview'],
      ],
      '#cache' => [
        'contexts' => ['user.permissions', 'languages:language_interface'],
        'max-age' => 0,
      ],
    ];

    $build['intro'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-diagnostic-admin-overview__intro']],
      'lead' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Configure energy diagnostics (DPE, GES), certification labels on offers and fallback messages when a diagnostic cannot be displayed.'),
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
        '#attributes' => ['class' => ['ps-diagnostic-admin-overview__group']],
        'links' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-diagnostic-admin-overview__group-links']],
          '#theme' => 'admin_block_content',
          '#content' => $links,
        ],
      ];
    }

    $build['footer'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-diagnostic-admin-overview__footer']],
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
      $this->t('This site is multilingual. Edit diagnostic settings in the default site language on the Settings tab. Per-language overrides for diagnostic type labels are available on each type edit form when Configuration translation is enabled.'),
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
        'id' => 'settings',
        'title' => $this->t('General settings'),
        'description' => $this->t('Validity duration, manual entry rules and fallback messages on offer detail pages.'),
        'items' => [
          [
            'title' => $this->t('Diagnostic settings'),
            'description' => $this->t('Default validity, manual class entry, empty values and disabled diagnostic messages.'),
            'route' => 'ps_diagnostic.settings',
          ],
        ],
      ],
      [
        'id' => 'types',
        'title' => $this->t('Diagnostic types'),
        'description' => $this->t('DPE/GES scales, units, icons and per-type behaviour (structure hub).'),
        'items' => [
          [
            'title' => $this->t('Diagnostic types'),
            'description' => $this->t('Manage diagnostic type entities (classes, colours, validity rules).'),
            'route' => 'entity.ps_diagnostic_type.collection',
          ],
          [
            'title' => $this->t('Diagnostics structure hub'),
            'description' => $this->t('Structure overview with shortcuts to types and related tools.'),
            'route' => 'ps_diagnostic.admin_structure',
          ],
        ],
      ],
      [
        'id' => 'labels',
        'title' => $this->t('Certification labels'),
        'description' => $this->t('Quality and certification badges shown on offers (LEED, BREEAM, HQE, …).'),
        'items' => [
          [
            'title' => $this->t('Certification label terms'),
            'description' => $this->t('Taxonomy terms with badge images used on the offer certification field.'),
            'route' => 'ps_diagnostic.certification_labels',
          ],
        ],
      ],
      [
        'id' => 'related',
        'title' => $this->t('Related configuration'),
        'description' => $this->t('Offer detail sections owned by other PS modules.'),
        'items' => [],
      ],
    ];

    if ($this->moduleHandler->moduleExists('ps_offer')) {
      $groups[3]['items'][] = [
        'title' => $this->t('Offer section headings'),
        'description' => $this->t('Title and icon for the Energy & diagnostics block on offer detail pages.'),
        'route' => 'ps_offer.section_settings',
      ];
      $groups[3]['items'][] = [
        'title' => $this->t('Offer configuration hub'),
        'description' => $this->t('Cards, search results, detail page sections and reference patterns.'),
        'route' => 'ps_offer.admin_overview',
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
