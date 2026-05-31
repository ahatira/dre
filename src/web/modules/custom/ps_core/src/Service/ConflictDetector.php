<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

final class ConflictDetector {

  public function __construct(
    private readonly AuditLogger $auditLogger,
  ) {}

  public function hasConflict(array $internal, array $external): bool {
    $internalChecksum = (string) ($internal['checksum'] ?? '');
    $externalChecksum = (string) ($external['checksum'] ?? '');

    if ($internalChecksum === '' || $externalChecksum === '') {
      return FALSE;
    }

    $conflict = $internalChecksum !== $externalChecksum;
    if ($conflict) {
      $this->auditLogger->log('conflict_detected', [
        'internal_checksum' => $internalChecksum,
        'external_checksum' => $externalChecksum,
      ]);
    }

    return $conflict;
  }

}
