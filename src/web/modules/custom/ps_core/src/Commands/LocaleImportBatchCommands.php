<?php

declare(strict_types=1);

namespace Drupal\ps_core\Commands;

use Drupal\ps_core\Service\LocaleImportBatchService;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;

/**
 * Batch locale import for Property Search install scripts.
 */
final class LocaleImportBatchCommands extends DrushCommands {

  public function __construct(
    private readonly LocaleImportBatchService $importBatch,
  ) {
    parent::__construct();
  }

  /**
   * Imports contrib PO, custom PO, and language config overrides in one pass.
   */
  #[CLI\Command(name: 'ps:locale:import-batch', aliases: ['ps-lib'])]
  #[CLI\Option(name: 'country', description: 'Country code (log context only).')]
  #[CLI\Usage(name: 'drush ps:locale:import-batch --country=com', description: 'Import all translations for active site languages.')]
  public function importBatch(array $options = ['country' => 'com']): void {
    $country = strtoupper((string) ($options['country'] ?? 'com'));
    $this->io()->title(sprintf('Importing translations (%s)', $country));

    $stats = $this->importBatch->import(function (string $phase, array $context): void {
      $langcode = (string) ($context['langcode'] ?? '');
      $file = (string) ($context['file'] ?? '');
      $current = (int) ($context['current'] ?? 0);
      $total = (int) ($context['total'] ?? 0);
      $phaseLabel = match ($phase) {
        'contrib' => 'Contrib',
        'custom' => 'Custom',
        default => 'Overrides',
      };
      $this->io()->text(sprintf(
        '[INFO] %s %s: %s (%d/%d)',
        $phaseLabel,
        $langcode,
        $file,
        $current,
        $total,
      ));
    });

    foreach (['contrib', 'custom', 'overrides'] as $phase) {
      $phaseStats = $stats[$phase];
      $this->io()->success(sprintf(
        '%s: imported=%d, skipped=%d, failed=%d',
        ucfirst($phase),
        $phaseStats['imported'],
        $phaseStats['skipped'],
        $phaseStats['failed'],
      ));
    }
  }

}
