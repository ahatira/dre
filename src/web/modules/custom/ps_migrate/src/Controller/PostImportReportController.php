<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class PostImportReportController extends ControllerBase {

  public function __construct(
    private readonly StateInterface $state,
    private readonly DateFormatterInterface $dateFormatter,
  ) {}

  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('state'),
      $container->get('date.formatter'),
    );
  }

  public function __invoke(): array {
    $report = $this->state->get('ps_migrate.last_import_publication_report');
    if (!is_array($report) || empty($report)) {
      return [
        '#type' => 'container',
        'empty' => [
          '#markup' => $this->t('No post-import publication report is available yet. Run CRM import first.'),
        ],
      ];
    }

    $generated = (int) ($report['generated_at'] ?? 0);
    $summary_items = [
      $this->t('Generated at: @date', ['@date' => $generated > 0 ? $this->dateFormatter->format($generated, 'short') : 'n/a']),
      $this->t('Total offers scanned: @count', ['@count' => (int) ($report['total'] ?? 0)]),
      $this->t('Already published: @count', ['@count' => (int) ($report['already_published'] ?? 0)]),
      $this->t('Published during post-import: @count', ['@count' => (int) ($report['published'] ?? 0)]),
      $this->t('Rejected during publication: @count', ['@count' => (int) ($report['skipped'] ?? 0)]),
    ];

    $build = [
      'summary' => [
        '#theme' => 'item_list',
        '#title' => $this->t('Last Post-Import Publication Report'),
        '#items' => $summary_items,
      ],
    ];

    $rows = [];
    $skipped_items = $report['skipped_items'] ?? [];
    if (is_array($skipped_items)) {
      foreach ($skipped_items as $item) {
        if (!is_array($item)) {
          continue;
        }

        $rows[] = [
          (string) ($item['nid'] ?? ''),
          (string) ($item['business_id'] ?? ''),
          (string) ($item['reference'] ?? ''),
          (string) ($item['reason'] ?? ''),
        ];
      }
    }

    $build['rejected_table'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Node ID'),
        $this->t('Business ID (source XML)'),
        $this->t('Reference'),
        $this->t('Rejection reason'),
      ],
      '#rows' => $rows,
      '#empty' => $this->t('No rejected offers in the latest report.'),
      '#attributes' => ['class' => ['responsive-enabled']],
    ];

    return $build;
  }

}
