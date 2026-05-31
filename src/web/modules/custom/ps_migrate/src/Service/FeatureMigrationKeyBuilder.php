<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

/**
 * Builds stable machine names for feature migration records.
 */
final class FeatureMigrationKeyBuilder {

  /**
   * Builds a normalized group identifier.
   */
  public function buildGroupId(string $groupCode): string {
    return $this->normalize($groupCode);
  }

  /**
   * Builds a normalized definition identifier.
   */
  public function buildDefinitionId(string $groupCode, string $featureCode): string {
    return $this->normalize($groupCode) . '__' . $this->normalize($featureCode);
  }

  /**
   * Normalizes a CRM code into a Drupal-safe machine name.
   */
  public function normalize(string $code): string {
    $code = trim($code);
    if ($code === '') {
      return '';
    }

    $code = strtolower($code);
    $code = preg_replace('/[^a-z0-9]+/u', '_', $code) ?? $code;
    $code = trim($code, '_');

    return $code;
  }

}