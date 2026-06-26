<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\ps_migrate\Entity\ImportRunInterface;
use Drupal\ps_migrate\Service\ImportPipelineAdminSummary;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * CRM import hub overview with pipeline status and quick actions.
 */
final class ImportAdminOverviewController extends ControllerBase {

  public function __construct(
    private readonly ImportPipelineAdminSummary $adminSummary,
    ModuleHandlerInterface $moduleHandler,
  ) {
    $this->moduleHandler = $moduleHandler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_migrate.import_pipeline_admin_summary'),
      $container->get('module_handler'),
    );
  }

  /**
   * Overview of CRM import pipeline status and navigation.
   */
  public function overview(): array {
    $config = $this->adminSummary->getPipelineConfig();
    $lastRun = $this->adminSummary->loadLastRun();

    $build = [
      '#attached' => [
        'library' => ['ps_migrate/import_admin_overview'],
      ],
      '#cache' => [
        'tags' => $config->getCacheTags(),
        'contexts' => ['user.permissions'],
        'max-age' => 0,
      ],
    ];

    $build['intro'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-migrate-import-admin__intro']],
      'lead' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Monitor CRM XML imports, queue health and recent runs. Deposit files via upload or the incoming folder, then process through Drush, cron or the async queue.'),
      ],
    ];

    $build['stats'] = $this->adminSummary->buildStatsRenderArray();

    $actionCandidates = [
      'upload' => $this->buildActionLink(
        'ps_migrate.import_upload',
        $this->t('Upload CRM XML'),
        TRUE,
      ),
      'runs' => $this->buildActionLink(
        'entity.import_run.collection',
        $this->t('View import runs'),
      ),
      'settings' => $this->buildActionLink(
        'ps_migrate.import_pipeline_settings',
        $this->t('Pipeline settings'),
      ),
      'rejections' => $this->buildActionLink(
        'ps_migrate.post_import_report',
        $this->t('Import rejections'),
      ),
    ];
    if ($lastRun instanceof ImportRunInterface) {
      $actionCandidates['last_run'] = $this->buildActionLink(
        'entity.import_run.canonical',
        $this->t('Open last run'),
        FALSE,
        ['import_run' => $lastRun->id()],
      );
    }
    $actionLinks = array_filter($actionCandidates);

    if ($actionLinks !== []) {
      $build['actions'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-migrate-import-admin__actions']],
      ] + $actionLinks;
    }

    $navigationLinks = $this->buildNavigationLinks();
    if ($navigationLinks !== []) {
      $build['navigation'] = [
        '#type' => 'details',
        '#title' => $this->t('CRM import sections'),
        '#open' => TRUE,
        '#attributes' => ['class' => ['ps-migrate-import-admin__navigation']],
        'links' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-migrate-import-admin__navigation-links']],
          '#theme' => 'admin_block_content',
          '#content' => $navigationLinks,
        ],
      ];
    }

    if ($this->moduleHandler->moduleExists('ps_core')) {
      $governanceUrl = Url::fromRoute('ps_core.governance');
      if ($governanceUrl->access($this->currentUser())) {
        $build['governance'] = [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-migrate-import-admin__footer']],
          'link' => Link::createFromRoute(
            $this->t('Import governance settings'),
            'ps_core.governance',
          )->toRenderable(),
          'hint' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $this->t('Domain-specific lock rules and global CRM defaults live under Configuration → Governance.'),
          ],
        ];
      }
    }

    return $build;
  }

  /**
   * Builds admin block navigation links for CRM import child routes.
   *
   * @return list<array<string, mixed>>
   *   Items for the admin_block_content theme.
   */
  private function buildNavigationLinks(): array {
    $items = [
      [
        'title' => $this->t('Import runs'),
        'description' => $this->t('History of CRM XML import pipeline runs.'),
        'route' => 'entity.import_run.collection',
      ],
      [
        'title' => $this->t('Upload CRM XML'),
        'description' => $this->t('Simulate external CRM XML deposit.'),
        'route' => 'ps_migrate.import_upload',
      ],
      [
        'title' => $this->t('Pipeline settings'),
        'description' => $this->t('Configure import folders and pipeline behaviour.'),
        'route' => 'ps_migrate.import_pipeline_settings',
      ],
      [
        'title' => $this->t('Import rejections'),
        'description' => $this->t('View offers rejected during CRM import.'),
        'route' => 'ps_migrate.post_import_report',
      ],
    ];

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

  /**
   * Builds a quick action link when the route is accessible.
   *
   * @param string $route
   *   Route name.
   * @param \Stringable|string $title
   *   Link title.
   * @param bool $primary
   *   Whether to style the link as the primary action.
   * @param array<string, scalar> $parameters
   *   Route parameters.
   *
   * @return array<string, mixed>|null
   *   Render array or NULL when access is denied.
   */
  private function buildActionLink(
    string $route,
    \Stringable|string $title,
    bool $primary = FALSE,
    array $parameters = [],
  ): ?array {
    $url = Url::fromRoute($route, $parameters);
    if (!$url->access($this->currentUser())) {
      return NULL;
    }

    $classes = ['ps-migrate-import-admin__action'];
    if ($primary) {
      $classes[] = 'ps-migrate-import-admin__action--primary';
    }

    return Link::fromTextAndUrl($title, $url)->toRenderable() + [
      '#attributes' => ['class' => $classes],
    ];
  }

}
