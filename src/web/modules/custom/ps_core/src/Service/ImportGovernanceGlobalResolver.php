<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\ps_core\ImportGovernance\ImportGovernanceLockStrategy;

/**
 * Resolves global import governance defaults referenced by domain policies.
 */
class ImportGovernanceGlobalResolver {

  public const CONFIG_NAME = 'ps_core.governance';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ModuleHandlerInterface $moduleHandler,
  ) {}

  /**
   * Whether domain settings forms should show global inheritance hints.
   */
  public function shouldShowDomainInheritanceHints(): bool {
    return (bool) $this->governanceConfig()->get('import.show_domain_inheritance_hints');
  }

  /**
   * Returns the effective global CRM lock strategy.
   */
  public function getGlobalLockStrategy(): string {
    $sourceConfig = $this->getGlobalLockStrategySourceConfig();
    if ($sourceConfig === '') {
      return ImportGovernanceLockStrategy::LOG_ONLY;
    }

    $strategy = (string) $this->configFactory
      ->get($sourceConfig)
      ->get($this->getGlobalLockStrategySourceKey());

    return in_array($strategy, ImportGovernanceLockStrategy::allowedStrategies(), TRUE)
      ? $strategy
      : ImportGovernanceLockStrategy::LOG_ONLY;
  }

  /**
   * Returns the config object name holding the global lock strategy.
   */
  public function getGlobalLockStrategySourceConfig(): string {
    $value = trim((string) $this->governanceConfig()->get('import.global_lock_strategy.source_config'));
    return $value !== '' ? $value : 'ps_migrate.import_pipeline_settings';
  }

  /**
   * Returns the config key holding the global lock strategy.
   */
  public function getGlobalLockStrategySourceKey(): string {
    $value = trim((string) $this->governanceConfig()->get('import.global_lock_strategy.source_key'));
    return $value !== '' ? $value : 'lock_strategy_default';
  }

  /**
   * Returns the admin route for editing the global lock strategy, if known.
   */
  public function getGlobalLockStrategySettingsRouteName(): ?string {
    if (!$this->moduleHandler->moduleExists('ps_migrate')) {
      return NULL;
    }

    if ($this->getGlobalLockStrategySourceConfig() === 'ps_migrate.import_pipeline_settings') {
      return 'ps_migrate.import_pipeline_settings';
    }

    return NULL;
  }

  /**
   * Returns the translated label for a lock strategy machine name.
   */
  public function getGlobalLockStrategyLabel(?string $strategy = NULL): string {
    $strategy ??= $this->getGlobalLockStrategy();
    return (string) ($this->getLockStrategyLabels()[$strategy] ?? $strategy);
  }

  /**
   * Returns translated labels keyed by lock strategy machine name.
   *
   * @return array<string, string>
   *   English source labels for extraction and admin display.
   */
  public function getLockStrategyLabels(): array {
    return [
      ImportGovernanceLockStrategy::LOG_ONLY => 'Log only — CRM overwrites protected entities',
      ImportGovernanceLockStrategy::SKIP_ROW => 'Skip row — do not update protected entities',
      ImportGovernanceLockStrategy::SKIP_FIELD => 'Skip field — preserve non-empty internal field values',
    ];
  }

  /**
   * Builds a link to global CRM pipeline settings when the route is available.
   */
  public function buildGlobalPipelineSettingsLinkMarkup(string|\Stringable $label): string {
    $route = $this->getGlobalLockStrategySettingsRouteName();
    if ($route !== NULL && Url::fromRoute($route)->access()) {
      return Link::createFromRoute($label, $route)->toString();
    }

    return (string) $label;
  }

  /**
   * Builds markup explaining the inherited global lock strategy.
   */
  public function buildInheritanceHintMarkup(): string {
    if (!$this->shouldShowDomainInheritanceHints()) {
      return '';
    }

    $strategyLabel = $this->getGlobalLockStrategyLabel();
    $route = $this->getGlobalLockStrategySettingsRouteName();
    if ($route !== NULL && Url::fromRoute($route)->access()) {
      return sprintf(
        'Inherits from global CRM strategy: %s (%s).',
        $strategyLabel,
        Link::createFromRoute('CRM import pipeline settings', $route)->toString(),
      );
    }

    return sprintf(
      'Inherits from global CRM strategy: %s.',
      $strategyLabel,
    );
  }

  /**
   * Returns the editable governance config object.
   */
  private function governanceConfig() {
    return $this->configFactory->get(self::CONFIG_NAME);
  }

}
