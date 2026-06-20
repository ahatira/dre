<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\State\StateInterface;
use Drupal\ps_migrate\Service\ImportPipeline;

/**
 * Cron hook for CRM import pipeline.
 */
final class ImportPipelineCronHooks {

  public function __construct(
    private readonly ImportPipeline $importPipeline,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly StateInterface $state,
  ) {}

  /**
   * Polls incoming/ when cron is enabled in pipeline settings.
   */
  #[Hook('cron')]
  public function cron(): void {
    $config = $this->configFactory->get('ps_migrate.import_pipeline_settings');
    if (!$config->get('cron_enabled')) {
      return;
    }

    $interval = max(60, (int) $config->get('cron_interval'));
    $last = (int) $this->state->get('ps_migrate.import_pipeline.last_cron', 0);
    if (time() - $last < $interval) {
      return;
    }

    $this->state->set('ps_migrate.import_pipeline.last_cron', time());
    $this->importPipeline->run();
  }

}
