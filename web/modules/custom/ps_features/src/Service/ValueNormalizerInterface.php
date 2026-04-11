<?php

declare(strict_types=1);

namespace Drupal\ps_features\Service;

/**
 * Provides normalization and validation for raw feature values.
 *
 * Converts raw import / form values into typed canonical data based on feature
 * definitions. Validates dictionary codes, numeric ranges and enforces value
 * type constraints.
 *
 * @see docs/specs/04-ps-features.md#value-normalizer
 */
interface ValueNormalizerInterface {

  /**
   * Normalizes raw feature values.
   *
   * @param array<string,mixed> $raw
   *   Raw input values keyed by feature code.
   * @param array<string,\Drupal\ps_features\Entity\FeatureInterface> $definitions
   *   Definitions keyed by feature code.
   * @param bool $allowUnknown
   *   Whether unknown codes are skipped instead of reported.
   *
   * @return array{values: array<string,mixed>, errors: array<string,string>}
   *   Normalized values plus error messages keyed by feature code.
   */
  public function normalize(array $raw, array $definitions, bool $allowUnknown = FALSE): array;

}
