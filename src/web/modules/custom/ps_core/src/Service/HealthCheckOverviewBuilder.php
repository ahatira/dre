<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\ps_core\HealthCheck\HealthCheckResult;
use Drupal\ps_core\HealthCheck\HealthCheckStatus;

/**
 * Builds the Platform health admin overview render array.
 */
final class HealthCheckOverviewBuilder {

  use StringTranslationTrait;

  /**
   * Group labels keyed by group id.
   *
   * @var array<string, string>
   */
  private const GROUP_LABELS = [
    'import' => 'CRM import',
    'search' => 'Search & Solr',
    'infra' => 'Drupal infrastructure',
    'cache' => 'Cache',
    'files' => 'Import file paths',
    'governance' => 'Import governance',
  ];

  /**
   * Short descriptions shown under group headings.
   *
   * @var array<string, string>
   */
  private const GROUP_DESCRIPTIONS = [
    'import' => 'Pipeline queue, locks and recent run activity.',
    'files' => 'Configured incoming, processing and archive folders.',
    'search' => 'Solr connectivity and offers index completeness.',
    'infra' => 'Database, PHP runtime and cron freshness.',
    'cache' => 'Default cache backend round-trip probe.',
    'governance' => 'Import protection conflicts and internal locks.',
  ];

  /**
   * Group icon keys for the dashboard template.
   *
   * @var array<string, string>
   */
  private const GROUP_ICONS = [
    'import' => 'group_import',
    'files' => 'group_files',
    'search' => 'group_search',
    'infra' => 'group_infra',
    'cache' => 'group_cache',
    'governance' => 'group_governance',
  ];

  /**
   * Group sort weights.
   *
   * @var array<string, int>
   */
  private const GROUP_WEIGHTS = [
    'import' => 0,
    'files' => 5,
    'search' => 10,
    'infra' => 20,
    'cache' => 25,
    'governance' => 30,
  ];

  public function __construct(
    private readonly HealthCheckCollector $collector,
    private readonly string $sitePath,
  ) {}

  /**
   * Builds the overview page render array.
   *
   * @return array<string, mixed>
   *   Render array for the Platform health dashboard.
   */
  public function buildOverview(): array {
    $grouped = $this->collector->collectGroupedResults();
    $counts = $this->countByStatus($grouped);
    $overallStatus = $this->resolveOverallStatus($counts);

    return [
      '#theme' => 'ps_core_health_admin_overview',
      '#attached' => [
        'library' => ['ps_core/health_admin_overview'],
      ],
      '#cache' => [
        'tags' => ['ps_core:health'],
        'max-age' => 60,
        'contexts' => ['url.site'],
      ],
      '#overall_status' => $overallStatus,
      '#overall_label' => $this->overallLabel($overallStatus, $counts),
      '#lead' => (string) $this->t('Runtime diagnostics for CRM import, search, cache and platform dependencies.'),
      '#refresh_hint' => (string) $this->t('Site @alias · results cached for one minute · built @time', [
        '@alias' => $this->resolveDrushAlias(),
        '@time' => date('H:i:s'),
      ]),
      '#summary_cards' => $this->buildSummaryCards($counts),
      '#groups' => $this->buildGroups($grouped),
    ];
  }

  /**
   * @param array<string, list<array{id: string, label: string, weight: int, result: HealthCheckResult}>> $grouped
   *
   * @return array<string, int>
   */
  private function countByStatus(array $grouped): array {
    $counts = array_fill_keys(HealthCheckStatus::all(), 0);
    foreach ($grouped as $checks) {
      foreach ($checks as $check) {
        $status = $check['result']->status;
        if (isset($counts[$status])) {
          $counts[$status]++;
        }
      }
    }
    return $counts;
  }

  /**
   * @param array<string, int> $counts
   */
  private function resolveOverallStatus(array $counts): string {
    if ($counts[HealthCheckStatus::FAIL] > 0) {
      return 'critical';
    }
    if ($counts[HealthCheckStatus::WARNING] > 0) {
      return 'degraded';
    }
    return 'healthy';
  }

  /**
   * @param array<string, int> $counts
   */
  private function overallLabel(string $overallStatus, array $counts): string {
    $total = array_sum($counts);
    $failCount = $counts[HealthCheckStatus::FAIL];
    $warningCount = $counts[HealthCheckStatus::WARNING];

    return match ($overallStatus) {
      'critical' => (string) $this->formatPlural(
        $failCount,
        '1 critical issue needs attention',
        '@count critical issues need attention',
        ['@count' => $failCount],
      ),
      'degraded' => (string) $this->formatPlural(
        $warningCount,
        '1 warning on @total checks',
        '@count warnings on @total checks',
        ['@count' => $warningCount, '@total' => $total],
      ),
      default => (string) $this->formatPlural(
        $total,
        '1 check passed',
        'All @count checks passed',
        ['@count' => $total],
      ),
    };
  }

  /**
   * @param array<string, int> $counts
   *
   * @return list<array{status: string, count: int, label: string}>
   */
  private function buildSummaryCards(array $counts): array {
    return [
      [
        'status' => HealthCheckStatus::OK,
        'count' => $counts[HealthCheckStatus::OK],
        'label' => (string) $this->t('Healthy'),
      ],
      [
        'status' => HealthCheckStatus::WARNING,
        'count' => $counts[HealthCheckStatus::WARNING],
        'label' => (string) $this->t('Warnings'),
      ],
      [
        'status' => HealthCheckStatus::FAIL,
        'count' => $counts[HealthCheckStatus::FAIL],
        'label' => (string) $this->t('Failures'),
      ],
      [
        'status' => HealthCheckStatus::INFO,
        'count' => $counts[HealthCheckStatus::INFO],
        'label' => (string) $this->t('Info'),
      ],
    ];
  }

  /**
   * @param array<string, list<array{id: string, label: string, weight: int, result: HealthCheckResult}>> $grouped
   *
   * @return list<array<string, mixed>>
   */
  private function buildGroups(array $grouped): array {
    uksort(
      $grouped,
      static fn(string $a, string $b): int => (self::GROUP_WEIGHTS[$a] ?? 100) <=> (self::GROUP_WEIGHTS[$b] ?? 100),
    );

    $groups = [];
    foreach ($grouped as $groupKey => $checks) {
      if ($checks === []) {
        continue;
      }

      $groupLabel = (string) $this->t(self::GROUP_LABELS[$groupKey] ?? $groupKey);
      $groupChecks = [];
      foreach ($checks as $check) {
        $groupChecks[] = $this->buildCheckVariables(
          $check['id'],
          $check['label'],
          $check['result'],
          $groupLabel,
        );
      }

      $groups[] = [
        'key' => $groupKey,
        'label' => $groupLabel,
        'description' => isset(self::GROUP_DESCRIPTIONS[$groupKey])
          ? (string) $this->t(self::GROUP_DESCRIPTIONS[$groupKey])
          : '',
        'icon' => self::GROUP_ICONS[$groupKey] ?? 'info',
        'check_count_label' => (string) $this->formatPlural(
          count($groupChecks),
          '1 check',
          '@count checks',
          ['@count' => count($groupChecks)],
        ),
        'checks' => $groupChecks,
      ];
    }

    return $groups;
  }

  /**
   * @return array<string, mixed>
   */
  private function buildCheckVariables(string $id, string $label, HealthCheckResult $result, string $groupLabel = ''): array {
    $links = [];
    foreach ($result->links as $linkDef) {
      $url = Url::fromRoute($linkDef['route'], $linkDef['params'] ?? []);
      if (!$url->access()) {
        continue;
      }
      $links[] = [
        'title' => $linkDef['title'],
        'url' => $url->toString(),
      ];
    }

    $normalizedLabel = mb_strtolower(trim($label));
    $normalizedGroup = mb_strtolower(trim($groupLabel));

    return [
      'id' => $id,
      'icon' => $id,
      'label' => $label,
      'show_title' => $normalizedGroup === '' || $normalizedLabel !== $normalizedGroup,
      'status' => $result->status,
      'status_label' => $this->statusLabel($result->status),
      'message' => $result->message,
      'detail' => $result->detail ?? '',
      'links' => $links,
      'commands' => array_map(
        fn(string $command): string => $this->localizeCommand($command),
        $result->commands,
      ),
    ];
  }

  /**
   * Resolves the Drush alias for the active multisite (e.g. @ps.com).
   */
  private function resolveDrushAlias(): string {
    if (!function_exists('ps_load_countries_manifest')) {
      $manifestLoader = DRUPAL_ROOT . '/sites/countries.php';
      if (is_readable($manifestLoader)) {
        require_once $manifestLoader;
      }
    }

    if (function_exists('ps_load_countries_manifest')) {
      $siteDir = basename($this->sitePath);
      foreach (ps_load_countries_manifest()['countries'] as $code => $config) {
        if (is_array($config) && ($config['site_dir'] ?? '') === $siteDir) {
          return '@ps.' . $code;
        }
      }
    }

    return '@self';
  }

  /**
   * Rewrites example commands for the active country site.
   */
  private function localizeCommand(string $command): string {
    $alias = $this->resolveDrushAlias();
    $countryCode = substr($alias, strrpos($alias, '.') + 1);
    if ($countryCode === '' || $countryCode === 'self') {
      return $command;
    }

    return str_replace(
      ['@ps.fr', 'make drush fr '],
      [$alias, 'make drush ' . $countryCode . ' '],
      $command,
    );
  }

  private function statusLabel(string $status): string {
    return match ($status) {
      HealthCheckStatus::OK => (string) $this->t('OK'),
      HealthCheckStatus::WARNING => (string) $this->t('Warning'),
      HealthCheckStatus::FAIL => (string) $this->t('Fail'),
      HealthCheckStatus::INFO => (string) $this->t('Info'),
      default => $status,
    };
  }

}
