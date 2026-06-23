<?php

declare(strict_types=1);

namespace Drupal\ps_search\GeoZone;

use Drupal\ps_search\ValueObject\GeoZone;

/**
 * Validates geo zone config payloads before import or persistence.
 */
final class GeoZoneValidator {

  /**
   * @param array<string, array<string, mixed>> $zones
   *   Raw zone config keyed by zone id.
   *
   * @return list<string>
   *   Human-readable error messages.
   */
  public function validateCountryPayload(string $countryCode, array $zones, ?string $defaultZoneId = NULL): array {
    $errors = [];
    $countryCode = strtolower($countryCode);
    $slugs = [];
    $ids = array_keys($zones);

    if ($zones === []) {
      $errors[] = sprintf('Country "%s" has no zones.', $countryCode);
      return $errors;
    }

    if ($defaultZoneId !== NULL && $defaultZoneId !== '' && !isset($zones[$defaultZoneId])) {
      $errors[] = sprintf('Default zone "%s" is not defined for country "%s".', $defaultZoneId, $countryCode);
    }

    foreach ($zones as $id => $data) {
      if (!is_array($data)) {
        $errors[] = sprintf('Zone "%s" must be a mapping.', $id);
        continue;
      }

      $zoneErrors = $this->validateZone($id, $data, $countryCode, $ids);
      $errors = array_merge($errors, $zoneErrors);

      $slug = strtolower(trim((string) ($data['slug'] ?? '')));
      if ($slug !== '') {
        $slugKey = $countryCode . ':' . $slug;
        if (isset($slugs[$slugKey])) {
          $errors[] = sprintf('Duplicate slug "%s" for country "%s" (zones %s and %s).', $slug, $countryCode, $slugs[$slugKey], $id);
        }
        else {
          $slugs[$slugKey] = $id;
        }
      }
    }

    return $errors;
  }

  /**
   * @param array<string, mixed> $data
   * @param list<string> $allIds
   *
   * @return list<string>
   */
  public function validateZone(string $id, array $data, string $countryCode, array $allIds): array {
    $errors = [];
    $type = GeoZoneType::fromConfig((string) ($data['type'] ?? ''));

    if ($type === NULL) {
      $errors[] = sprintf('Zone "%s" has an invalid type.', $id);
      return $errors;
    }

    foreach (['code', 'label', 'slug'] as $requiredScalar) {
      if (!isset($data[$requiredScalar]) || !is_string($data[$requiredScalar]) || trim($data[$requiredScalar]) === '') {
        $errors[] = sprintf('Zone "%s" is missing "%s".', $id, $requiredScalar);
      }
    }

    if (!isset($data['lat'], $data['lng']) || !is_numeric($data['lat']) || !is_numeric($data['lng'])) {
      $errors[] = sprintf('Zone "%s" requires numeric lat/lng.', $id);
    }

    $bbox = is_array($data['bbox'] ?? NULL) ? $data['bbox'] : [];
    foreach (['sw_lat', 'sw_lng', 'ne_lat', 'ne_lng'] as $corner) {
      if (!isset($bbox[$corner]) || !is_numeric($bbox[$corner])) {
        $errors[] = sprintf('Zone "%s" bbox.%s is missing or invalid.', $id, $corner);
      }
    }

    if (isset($bbox['sw_lat'], $bbox['ne_lat'], $bbox['sw_lng'], $bbox['ne_lng'])
      && is_numeric($bbox['sw_lat']) && is_numeric($bbox['ne_lat'])
      && is_numeric($bbox['sw_lng']) && is_numeric($bbox['ne_lng'])
      && (float) $bbox['sw_lat'] >= (float) $bbox['ne_lat']) {
      $errors[] = sprintf('Zone "%s" bbox sw_lat must be less than ne_lat.', $id);
    }

    if (isset($bbox['sw_lng'], $bbox['ne_lng'])
      && is_numeric($bbox['sw_lng']) && is_numeric($bbox['ne_lng'])
      && (float) $bbox['sw_lng'] >= (float) $bbox['ne_lng']) {
      $errors[] = sprintf('Zone "%s" bbox sw_lng must be less than ne_lng.', $id);
    }

    $prefixes = $data['postal_prefixes'] ?? [];
    if ($type->requiresPostalPrefixes()) {
      if (!is_array($prefixes) || $prefixes === []) {
        $errors[] = sprintf('Zone "%s" (%s) requires postal_prefixes.', $id, $type->value);
      }
    }

    if (isset($data['parent']) && is_string($data['parent']) && $data['parent'] !== '') {
      if (!in_array($data['parent'], $allIds, TRUE)) {
        $errors[] = sprintf('Zone "%s" parent "%s" is not defined.', $id, $data['parent']);
      }
      if ($data['parent'] === $id) {
        $errors[] = sprintf('Zone "%s" cannot be its own parent.', $id);
      }
    }

    if (!str_starts_with(strtolower($id), strtolower($type->value) . '.' . strtolower($countryCode) . '.')) {
      $errors[] = sprintf('Zone id "%s" should start with "%s.%s.".', $id, $type->value, strtolower($countryCode));
    }

    $parsed = GeoZone::fromConfigArray($id, $data, $countryCode);
    if ($parsed instanceof GeoZone && !$parsed->bbox->isValid()) {
      $errors[] = sprintf('Zone "%s" has an invalid bbox.', $id);
    }

    return $errors;
  }

}
