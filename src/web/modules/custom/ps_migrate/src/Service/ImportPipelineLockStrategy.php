<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Resolves CRM import lock strategies from pipeline settings.
 */
final class ImportPipelineLockStrategy {

  public const STRATEGY_LOG_ONLY = 'log_only';

  public const STRATEGY_SKIP_ROW = 'skip_row';

  public const STRATEGY_SKIP_FIELD = 'skip_field';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Returns the default lock strategy for protected entities.
   */
  public function getDefaultStrategy(): string {
    $strategy = (string) $this->configFactory
      ->get('ps_migrate.import_pipeline_settings')
      ->get('lock_strategy_default');
    return $this->normalizeStrategy($strategy);
  }

  /**
   * Returns the lock strategy for a destination field, if overridden.
   */
  public function getFieldStrategy(string $fieldName): string {
    $mapping = $this->configFactory
      ->get('ps_migrate.import_pipeline_settings')
      ->get('lock_field_strategies');
    if (!is_array($mapping) || !isset($mapping[$fieldName])) {
      return $this->getDefaultStrategy();
    }
    return $this->normalizeStrategy((string) $mapping[$fieldName]);
  }

  /**
   * Whether a protected entity row should be skipped entirely.
   */
  public function shouldSkipRow(string $fieldName = ''): bool {
    $strategy = $fieldName !== ''
      ? $this->getFieldStrategy($fieldName)
      : $this->getDefaultStrategy();
    return $strategy === self::STRATEGY_SKIP_ROW;
  }

  /**
   * Whether protected entities should preserve internal field values.
   */
  public function shouldPreserveInternalFields(string $fieldName = ''): bool {
    $strategy = $fieldName !== ''
      ? $this->getFieldStrategy($fieldName)
      : $this->getDefaultStrategy();
    return $strategy === self::STRATEGY_SKIP_FIELD;
  }

  /**
   * @return array<string, string>
   */
  public function getAllowedStrategies(): array {
    return [
      self::STRATEGY_LOG_ONLY => 'log_only',
      self::STRATEGY_SKIP_ROW => 'skip_row',
      self::STRATEGY_SKIP_FIELD => 'skip_field',
    ];
  }

  private function normalizeStrategy(string $strategy): string {
    return in_array($strategy, $this->getAllowedStrategies(), TRUE)
      ? $strategy
      : self::STRATEGY_LOG_ONLY;
  }

}
