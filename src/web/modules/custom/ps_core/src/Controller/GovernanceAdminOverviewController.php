<?php

declare(strict_types=1);

namespace Drupal\ps_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\ps_core\Service\ImportGovernanceRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Central admin hub for import and catalogue governance settings.
 */
final class GovernanceAdminOverviewController extends ControllerBase {

  public function __construct(
    private readonly ImportGovernanceRegistry $governanceRegistry,
    ModuleHandlerInterface $moduleHandler,
  ) {
    $this->moduleHandler = $moduleHandler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_core.import_governance_registry'),
      $container->get('module_handler'),
    );
  }

  /**
   * Overview of governance settings across PS domains.
   */
  public function overview(): array {
    $build = [
      '#attached' => [
        'library' => ['ps_core/governance_admin_overview'],
      ],
      '#cache' => [
        'contexts' => ['user.permissions', 'languages:language_interface'],
        'max-age' => 0,
      ],
    ];

    $build['intro'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-governance-admin-overview__intro']],
      'lead' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Central entry point for import protection and domain-specific governance rules. Global CRM defaults apply unless a domain overrides them.'),
      ],
    ];

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
        '#attributes' => ['class' => ['ps-governance-admin-overview__group']],
        'links' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-governance-admin-overview__group-links']],
          '#theme' => 'admin_block_content',
          '#content' => $links,
        ],
      ];
    }

    $build['footer'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-governance-admin-overview__footer']],
      'hub' => Link::createFromRoute(
        $this->t('Back to PS configuration hub'),
        'ps_core.config',
      )->toRenderable(),
    ];

    return $build;
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
        'id' => 'global',
        'title' => $this->t('Global import pipeline'),
        'description' => $this->t('Cross-domain CRM import folders, batch limits and default lock strategy.'),
        'items' => [],
      ],
      [
        'id' => 'domains',
        'title' => $this->t('Domain governance'),
        'description' => $this->t('Business rules applied when imports touch a specific entity catalogue.'),
        'items' => [],
      ],
    ];

    if ($this->moduleHandler->moduleExists('ps_migrate')) {
      $groups[0]['items'][] = [
        'title' => $this->t('CRM import overview'),
        'description' => $this->t('Pipeline status, queue depth and quick upload.'),
        'route' => 'ps_migrate.admin_overview',
      ];
      $groups[0]['items'][] = [
        'title' => $this->t('Global governance defaults'),
        'description' => $this->t('Inheritance hints and overview of the global CRM lock strategy source.'),
        'route' => 'ps_core.governance_settings',
      ];
      $groups[0]['items'][] = [
        'title' => $this->t('CRM import pipeline settings'),
        'description' => $this->t('Default lock strategy, pipeline folders and upload limits.'),
        'route' => 'ps_migrate.import_pipeline_settings',
      ];
    }

    foreach ($this->governanceRegistry->getPolicies() as $policy) {
      $route = $policy->getSettingsRouteName();
      if ($route === NULL) {
        continue;
      }

      $groups[1]['items'][] = [
        'title' => $policy->getAdminLabel(),
        'description' => $policy->getAdminDescription(),
        'route' => $route,
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
