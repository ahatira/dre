<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\ps_core\Service\ConflictWindowProviderInterface;

/**
 * Reads conflict window from CRM import pipeline settings.
 */
final class ImportPipelineConflictWindowProvider implements ConflictWindowProviderInterface {

  private const CONFIG_NAME = 'ps_migrate.import_pipeline_settings';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getConflictWindowSeconds(): int {
    return max(0, (int) ($this->configFactory->get(self::CONFIG_NAME)->get('conflict_window_seconds') ?? 300));
  }

}
