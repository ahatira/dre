<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Service;

/**
 * Interface for diagnostic normalizer service.
 */
interface DiagnosticNormalizerInterface {

  /**
   * Normalizes and validates diagnostic data.
   *
   * @param array<string, mixed> $data
   *   Raw diagnostic data.
   *
   * @return array<string, mixed>
   *   Normalized diagnostic data.
   */
  public function normalize(array $data): array;

}
