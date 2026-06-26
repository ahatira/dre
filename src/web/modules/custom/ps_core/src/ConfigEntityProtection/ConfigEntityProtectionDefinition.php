<?php

declare(strict_types=1);

namespace Drupal\ps_core\ConfigEntityProtection;

/**
 * Declares protection property mapping for a config entity type.
 */
final class ConfigEntityProtectionDefinition {

  /**
   * Constructs a config entity protection definition.
   *
   * @param string $entityTypeId
   *   Config entity type ID.
   * @param string $lockProperty
   *   Boolean property protecting the whole entity from external overwrites.
   * @param string $trackingProperty
   *   JSON string property for source traceability metadata.
   * @param string|null $checksumProperty
   *   Optional checksum property for idempotence checks.
   * @param string $fieldLocksProperty
   *   Map property listing field names locked against external updates.
   */
  public function __construct(
    private readonly string $entityTypeId,
    private readonly string $lockProperty = 'internal_lock',
    private readonly string $trackingProperty = 'source_tracking',
    private readonly ?string $checksumProperty = 'checksum',
    private readonly string $fieldLocksProperty = 'field_locks',
  ) {}

  /**
   * Returns the config entity type ID.
   */
  public function getEntityTypeId(): string {
    return $this->entityTypeId;
  }

  /**
   * Returns the entity-level lock property name.
   */
  public function getLockProperty(): string {
    return $this->lockProperty;
  }

  /**
   * Returns the source tracking property name.
   */
  public function getTrackingProperty(): string {
    return $this->trackingProperty;
  }

  /**
   * Returns the checksum property name, if configured.
   */
  public function getChecksumProperty(): ?string {
    return $this->checksumProperty;
  }

  /**
   * Returns the field locks map property name.
   */
  public function getFieldLocksProperty(): string {
    return $this->fieldLocksProperty;
  }

}
