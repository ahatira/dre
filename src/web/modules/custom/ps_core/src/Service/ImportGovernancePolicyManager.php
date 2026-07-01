<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\ps_core\Annotation\ImportGovernancePolicy;
use Drupal\ps_core\Attribute\ImportGovernancePolicy as ImportGovernancePolicyAttribute;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernancePolicyInterface;

/**
 * Discovers import governance policy plugins from enabled modules.
 */
class ImportGovernancePolicyManager extends DefaultPluginManager {

  public function __construct(
    \Traversable $namespaces,
    CacheBackendInterface $cache_backend,
    ModuleHandlerInterface $module_handler,
  ) {
    parent::__construct(
      'Plugin/ImportGovernance',
      $namespaces,
      $module_handler,
      ImportGovernancePolicyInterface::class,
      ImportGovernancePolicyAttribute::class,
      ImportGovernancePolicy::class,
    );

    $this->alterInfo('ps_core_import_governance_policy_info');
    $this->setCacheBackend($cache_backend, 'ps_core_import_governance_policy_plugins');
  }

}
