<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Trait;

/**
 * Shared governance properties for feature catalogue config entities.
 *
 * Property names mirror config export keys (snake_case).
 */
trait FeatureConfigEntityGovernanceTrait {

  /**
   * Whether manual catalogue curation is protected from external imports.
   *
   * @var bool
   */
  // phpcs:ignore Drupal.NamingConventions.ValidVariableName.LowerCamelName
  protected $internal_lock = FALSE;

  /**
   * JSON metadata describing how the entity entered the catalogue.
   *
   * @var string
   */
  // phpcs:ignore Drupal.NamingConventions.ValidVariableName.LowerCamelName
  protected $source_tracking = '';

  /**
   * SHA256 checksum of the last imported external snapshot.
   *
   * @var string
   */
  protected $checksum = '';

  /**
   * Field names locked against external updates.
   *
   * @var array<string, bool>
   */
  // phpcs:ignore Drupal.NamingConventions.ValidVariableName.LowerCamelName
  protected $field_locks = [];

  /**
   * Whether the entity is protected against external catalogue updates.
   */
  public function isInternallyLocked(): bool {
    return (bool) ($this->internal_lock ?? FALSE);
  }

  /**
   * Sets whether the entity is protected against external catalogue updates.
   */
  public function setInternallyLocked(bool $locked): static {
    $this->internal_lock = $locked;
    return $this;
  }

  /**
   * Returns source tracking metadata as a JSON string.
   */
  public function getSourceTracking(): string {
    return (string) ($this->source_tracking ?? '');
  }

  /**
   * Sets source tracking metadata.
   */
  public function setSourceTracking(string $sourceTracking): static {
    $this->source_tracking = $sourceTracking;
    return $this;
  }

  /**
   * Returns the stored external snapshot checksum.
   */
  public function getChecksum(): string {
    return (string) ($this->checksum ?? '');
  }

  /**
   * Sets the stored external snapshot checksum.
   */
  public function setChecksum(string $checksum): static {
    $this->checksum = $checksum;
    return $this;
  }

  /**
   * Returns locked field names.
   *
   * @return array<string, bool>
   *   Locked field map keyed by field name.
   */
  public function getFieldLocks(): array {
    if (!is_array($this->field_locks ?? NULL)) {
      return [];
    }

    $locks = [];
    foreach ($this->field_locks as $fieldName => $locked) {
      if (is_string($fieldName) && $fieldName !== '' && $locked) {
        $locks[$fieldName] = TRUE;
      }
    }

    return $locks;
  }

  /**
   * Sets the field lock map.
   *
   * @param array<string, bool> $fieldLocks
   *   Locked field map keyed by field name.
   */
  public function setFieldLocks(array $fieldLocks): static {
    $this->field_locks = $fieldLocks;
    return $this;
  }

  /**
   * Checks whether a specific property is locked against external updates.
   */
  public function isFieldLocked(string $fieldName): bool {
    if ($fieldName === '') {
      return FALSE;
    }

    return !empty($this->getFieldLocks()[$fieldName]);
  }

  /**
   * Sets or clears a field lock entry.
   */
  public function setFieldLocked(string $fieldName, bool $locked = TRUE): static {
    if ($fieldName === '') {
      return $this;
    }

    $fieldLocks = $this->getFieldLocks();
    if ($locked) {
      $fieldLocks[$fieldName] = TRUE;
    }
    else {
      unset($fieldLocks[$fieldName]);
    }

    $this->field_locks = $fieldLocks;
    return $this;
  }

}
