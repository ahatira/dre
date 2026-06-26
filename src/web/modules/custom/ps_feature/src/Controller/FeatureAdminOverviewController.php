<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Admin landing page for ps_feature configuration.
 */
final class FeatureAdminOverviewController extends ControllerBase {

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
   * Redirects the config hub path to the overview tab.
   */
  public function redirectToOverview(): RedirectResponse {
    return $this->redirect('ps_feature.admin_overview');
  }

  /**
   * Redirects legacy structure display settings to the config hub tab.
   */
  public function redirectToDisplaySettings(): RedirectResponse {
    return $this->redirect('ps_feature.config_display_settings');
  }

  /**
   * Redirects the legacy feature governance path to the central hub route.
   */
  public function redirectToGovernanceSettings(): RedirectResponse {
    return $this->redirect('ps_feature.governance_domain_settings', [], [], 301);
  }

  /**
   * Overview of feature configuration sections.
   */
  public function overview(): array {
    $build = [
      '#attached' => [
        'library' => ['ps_feature/admin_overview'],
      ],
      '#cache' => [
        'contexts' => ['user.permissions', 'languages:language_interface'],
        'max-age' => 0,
      ],
    ];

    $build['intro'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-feature-admin-overview__intro']],
      'lead' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Configure the feature catalogue, import behaviour and display defaults used on offers.'),
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
        '#attributes' => ['class' => ['ps-feature-admin-overview__group']],
        'links' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-feature-admin-overview__group-links']],
          '#theme' => 'admin_block_content',
          '#content' => $links,
        ],
      ];
    }

    $build['footer'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-feature-admin-overview__footer']],
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
      $this->t('This site is multilingual. Edit wording in the default site language in each settings tab. Use the Translate tab for other enabled languages.'),
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
        'id' => 'catalogue',
        'title' => $this->t('Catalogue management'),
        'description' => $this->t('Browse, import and organise the feature catalogue used on offers.'),
        'items' => [
          [
            'title' => $this->t('Feature definitions'),
            'description' => $this->t('Browse, create and edit catalogue entries.'),
            'route' => 'view.ps_feature_definitions_admin.page_1',
          ],
          [
            'title' => $this->t('Import catalogue'),
            'description' => $this->t('Upload a business CSV to create or update definitions.'),
            'route' => 'ps_feature.catalogue_import_form',
          ],
          [
            'title' => $this->t('Feature groups'),
            'description' => $this->t('Organise definitions into business groups.'),
            'route' => 'entity.fb_feature_group.collection',
          ],
        ],
      ],
      [
        'id' => 'import',
        'title' => $this->t('Import behaviour'),
        'description' => $this->t('CRM/XML rules, CSV defaults and offer import stubs.'),
        'items' => [
          [
            'title' => $this->t('Feature catalogue governance'),
            'description' => $this->t('Protected catalogue rules, missing XML behaviour and default import group.'),
            'route' => 'ps_feature.governance_domain_settings',
          ],
          [
            'title' => $this->t('Governance hub'),
            'description' => $this->t('Central entry point for all import governance domains.'),
            'route' => 'ps_core.governance',
          ],
        ],
      ],
      [
        'id' => 'display',
        'title' => $this->t('Display and reference'),
        'description' => $this->t('Front icons and read-only developer data types.'),
        'items' => [
          [
            'title' => $this->t('Feature display settings'),
            'description' => $this->t('Default icon for groups without a custom UI icon.'),
            'route' => 'ps_feature.config_display_settings',
          ],
          [
            'title' => $this->t('Feature types'),
            'description' => $this->t('Read-only list of data formats available to definitions.'),
            'route' => 'ps_feature.feature_types',
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
