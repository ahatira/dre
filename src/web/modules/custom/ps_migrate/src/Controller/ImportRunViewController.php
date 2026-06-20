<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\ps_migrate\Entity\ImportRunInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Displays a single CRM import run.
 */
final class ImportRunViewController extends ControllerBase {

  public function __construct(
    private readonly DateFormatterInterface $dateFormatter,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('date.formatter'),
    );
  }

  /**
   * Builds the import run detail page title.
   */
  public function title(ImportRunInterface $import_run): string {
    return $import_run->getFilename();
  }

  /**
   * Builds the import run detail page.
   */
  public function view(ImportRunInterface $import_run): array {
    $started = (int) $import_run->get('started')->value;
    $finished = (int) $import_run->get('finished')->value;
    $stats = $import_run->getStats();

    $build = [
      '#attached' => [
        'library' => ['ps_migrate/import_run_view'],
      ],
      '#cache' => [
        'tags' => $import_run->getCacheTags(),
        'contexts' => ['user.permissions'],
      ],
    ];

    $build['summary'] = [
      '#type' => 'table',
      '#header' => [$this->t('Property'), $this->t('Value')],
      '#rows' => $this->buildSummaryRows($import_run, $started, $finished),
      '#attributes' => ['class' => ['ps-migrate-import-run__summary']],
      '#weight' => 0,
    ];

    if ($stats !== []) {
      $build['migrations'] = $this->buildMigrationStats($stats);
      $build['migrations']['#weight'] = 10;
    }

    if ($import_run->getMessages() !== '') {
      $build['messages'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['messages', 'messages--warning', 'ps-migrate-import-run__messages']],
        'content' => [
          '#markup' => nl2br(htmlspecialchars($import_run->getMessages(), ENT_QUOTES, 'UTF-8')),
        ],
        '#weight' => 20,
      ];
    }

    if ($stats !== []) {
      $build['raw_stats'] = [
        '#type' => 'details',
        '#title' => $this->t('Raw statistics (JSON)'),
        '#open' => FALSE,
        '#attributes' => ['class' => ['ps-migrate-import-run__raw-stats']],
        'content' => [
          '#type' => 'html_tag',
          '#tag' => 'pre',
          '#value' => json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
          '#attributes' => ['class' => ['ps-migrate-import-run__json']],
        ],
        '#weight' => 30,
      ];
    }

    $build['actions'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-migrate-import-run__actions']],
      'back' => Link::fromTextAndUrl(
        $this->t('Back to import runs'),
        Url::fromRoute('entity.import_run.collection'),
      )->toRenderable(),
      '#weight' => 40,
    ];

    return $build;
  }

  /**
   * Builds summary table rows for an import run.
   */
  private function buildSummaryRows(ImportRunInterface $import_run, int $started, int $finished): array {
    $rows = [
      [$this->t('Filename'), $import_run->getFilename()],
      [
        $this->t('Status'),
        ['data' => $this->buildStatusBadge($import_run->getPipelineStatus())],
      ],
      [$this->t('Mode'), $this->formatMode($import_run->getImportMode())],
      [$this->t('Started'), $this->formatTimestamp($started)],
      [$this->t('Finished'), $this->formatTimestamp($finished)],
      [$this->t('Duration'), $this->formatDuration($started, $finished)],
      [$this->t('Source URI'), $this->formatUri($import_run->get('source_uri')->value)],
      [$this->t('Final URI'), $this->formatUri($import_run->get('file_uri')->value)],
    ];

    $owner = $import_run->getOwner();
    if ($owner !== NULL && !$owner->isAnonymous()) {
      $rows[] = [
        $this->t('Triggered by'),
        Link::fromTextAndUrl($owner->getDisplayName(), $owner->toUrl())->toString(),
      ];
    }

    return $rows;
  }

  /**
   * Builds the per-migration statistics table.
   *
   * @param array<string, mixed> $stats
   *   Decoded stats from the import run.
   */
  private function buildMigrationStats(array $stats): array {
    $header = [
      $this->t('Migration'),
      $this->t('Result'),
      $this->t('Imported'),
      $this->t('Failed'),
    ];
    $rows = [];

    $migrations = $stats['migrations'] ?? [];
    if (is_array($migrations)) {
      foreach ($migrations as $migrationId => $migrationStats) {
        if (!is_array($migrationStats)) {
          continue;
        }
        $failed = (int) ($migrationStats['failed'] ?? 0);
        $rows[] = [
          $migrationId,
          ['data' => $this->buildMigrationResultBadge((int) ($migrationStats['result'] ?? 0), $failed)],
          (string) (int) ($migrationStats['imported'] ?? 0),
          (string) $failed,
        ];
      }
    }

    $panel = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-migrate-import-run__panel']],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('Migration statistics'),
        '#attributes' => ['class' => ['ps-migrate-import-run__panel-title']],
      ],
    ];

    if (!empty($stats['error'])) {
      $panel['error'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['messages', 'messages--error', 'ps-migrate-import-run__pipeline-error']],
        'content' => [
          '#markup' => htmlspecialchars((string) $stats['error'], ENT_QUOTES, 'UTF-8'),
        ],
      ];
    }

    $panel['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No migration counters recorded.'),
      '#attributes' => ['class' => ['ps-migrate-import-run__migrations']],
    ];

    return $panel;
  }

  /**
   * Builds a pipeline status badge render array.
   */
  private function buildStatusBadge(string $status): array {
    $label = match ($status) {
      ImportRunInterface::STATUS_SUCCESS => $this->t('Success'),
      ImportRunInterface::STATUS_FAILED => $this->t('Failed'),
      ImportRunInterface::STATUS_PROCESSING => $this->t('Processing'),
      ImportRunInterface::STATUS_PENDING => $this->t('Pending'),
      default => $status,
    };

    return [
      '#type' => 'html_tag',
      '#tag' => 'span',
      '#value' => $label,
      '#attributes' => [
        'class' => [
          'ps-migrate-import-run__badge',
          'ps-migrate-import-run__badge--' . preg_replace('/[^a-z0-9_-]+/', '-', strtolower($status)),
        ],
      ],
    ];
  }

  /**
   * Builds a migration result badge render array.
   */
  private function buildMigrationResultBadge(int $result, int $failed): array {
    $statusClass = 'neutral';
    $label = $this->t('Unknown');

    if ($result === MigrationInterface::RESULT_COMPLETED && $failed === 0) {
      $statusClass = 'success';
      $label = $this->t('Completed');
    }
    elseif ($result === MigrationInterface::RESULT_COMPLETED && $failed > 0) {
      $statusClass = 'warning';
      $label = $this->t('Completed with errors');
    }
    elseif ($result === MigrationInterface::RESULT_FAILED) {
      $statusClass = 'failed';
      $label = $this->t('Failed');
    }
    elseif ($result === MigrationInterface::RESULT_STOPPED) {
      $statusClass = 'warning';
      $label = $this->t('Stopped');
    }

    return [
      '#type' => 'html_tag',
      '#tag' => 'span',
      '#value' => $label,
      '#attributes' => [
        'class' => [
          'ps-migrate-import-run__badge',
          'ps-migrate-import-run__badge--' . $statusClass,
        ],
      ],
    ];
  }

  /**
   * Formats import mode for display.
   */
  private function formatMode(string $mode): string {
    return match ($mode) {
      ImportRunInterface::MODE_FULL => (string) $this->t('Full'),
      ImportRunInterface::MODE_DELTA => (string) $this->t('Delta'),
      default => $mode,
    };
  }

  /**
   * Formats a URI for display.
   */
  private function formatUri(?string $uri): string {
    $uri = trim((string) $uri);
    if ($uri === '') {
      return (string) $this->t('N/A');
    }
    return $uri;
  }

  /**
   * Formats a timestamp for display.
   */
  private function formatTimestamp(int $timestamp): string {
    if ($timestamp <= 0) {
      return (string) $this->t('N/A');
    }
    return $this->dateFormatter->format($timestamp, 'long');
  }

  /**
   * Formats elapsed time between two timestamps.
   */
  private function formatDuration(int $started, int $finished): string {
    if ($started <= 0 || $finished <= 0 || $finished < $started) {
      return (string) $this->t('N/A');
    }
    return (string) $this->dateFormatter->formatInterval($finished - $started);
  }

}
