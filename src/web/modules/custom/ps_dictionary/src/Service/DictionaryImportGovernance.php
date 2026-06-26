<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Service;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Reads dictionary import governance settings.
 */
class DictionaryImportGovernance {

  public const CONFIG_NAME = 'ps_dictionary.import_governance';

  public const ENTITY_TYPE_ID = 'ps_dictionary_entry';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Whether CSV imports should keep existing entry labels when updating rows.
   */
  public function shouldPreserveExistingLabelsOnCsvImport(): bool {
    return (bool) $this->config()->get('csv_import.preserve_existing_labels');
  }

  /**
   * Whether CSV imports should mark entries as internally locked.
   */
  public function shouldLockOnCsvImport(): bool {
    return (bool) $this->config()->get('csv_import.lock_on_import');
  }

  /**
   * Whether BO-created entries should default to internal lock.
   */
  public function shouldLockOnBoCreate(): bool {
    return (bool) $this->config()->get('bo_create.default_internal_lock');
  }

  /**
   * Whether CRM offer imports should keep existing dictionary labels.
   */
  public function shouldPreserveExistingLabelsFromCrmLookup(): bool {
    return (bool) $this->config()->get('crm_lookup.preserve_existing_labels');
  }

  private function config() {
    return $this->configFactory->get(self::CONFIG_NAME);
  }

}
