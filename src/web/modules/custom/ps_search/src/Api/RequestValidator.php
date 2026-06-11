<?php

declare(strict_types=1);

namespace Drupal\ps_search\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Validates query parameters for ps_search public API endpoints.
 */
final class RequestValidator {

  private const OPERATION_TYPES = ['LOC', 'VEN'];

  private const ASSET_TYPES = ['BUR', 'ENT', 'ACT', 'COM', 'TER', 'LOG', 'COW'];

  private const TRANSPORTS = ['walking', 'transports', 'bike', 'car'];

  private const ISOCHRONE_MIN_MINUTES = 1;

  private const ISOCHRONE_MAX_MINUTES = 120;

  private const TEXT_MAX_LENGTH = 100;

  private const SUGGEST_MIN_LENGTH = 2;

  private const SUGGEST_MAX_LIMIT = 20;

  private const LOCATION_DATA_MAX_ITEMS = 10;

  /**
   * Validates isochrone query parameters.
   */
  public function validateIsochrone(Request $request): ?JsonResponse {
    $latRaw = $request->query->get('lat');
    $lngRaw = $request->query->get('lng');
    if (!is_numeric($latRaw) || !is_numeric($lngRaw)) {
      return $this->error('invalid_center', 400);
    }

    $lat = (float) $latRaw;
    $lng = (float) $lngRaw;
    if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
      return $this->error('invalid_center', 400);
    }

    $transport = strtolower(trim((string) $request->query->get('transport', 'walking')));
    if (!in_array($transport, self::TRANSPORTS, TRUE)) {
      return $this->error('invalid_transport', 400);
    }

    $minutesRaw = $request->query->get('minutes');
    if ($minutesRaw !== NULL && $minutesRaw !== '' && !is_numeric($minutesRaw)) {
      return $this->error('invalid_minutes', 400);
    }

    $minutes = is_numeric($minutesRaw) ? (int) $minutesRaw : 5;
    if ($minutes < self::ISOCHRONE_MIN_MINUTES || $minutes > self::ISOCHRONE_MAX_MINUTES) {
      return $this->error('invalid_minutes', 400);
    }

    return NULL;
  }

  /**
   * Validates location suggest query parameters.
   */
  public function validateLocationSuggest(Request $request): ?JsonResponse {
    $query = $this->sanitizeText($request->query->get('q'));
    if ($query === NULL || mb_strlen($query) < self::SUGGEST_MIN_LENGTH) {
      return NULL;
    }

    $limitRaw = $request->query->get('limit');
    if ($limitRaw !== NULL && $limitRaw !== '' && !is_numeric($limitRaw)) {
      return $this->error('invalid_limit', 400);
    }

    $limit = is_numeric($limitRaw) ? (int) $limitRaw : 8;
    if ($limit < 1 || $limit > self::SUGGEST_MAX_LIMIT) {
      return $this->error('invalid_limit', 400);
    }

    return NULL;
  }

  /**
   * Validates location-data query parameters.
   */
  public function validateLocationData(Request $request): ?JsonResponse {
    $localitiesRaw = $request->query->all('localities');
    if (!is_array($localitiesRaw) || $localitiesRaw === []) {
      return NULL;
    }

    if (count($localitiesRaw) > self::LOCATION_DATA_MAX_ITEMS) {
      return $this->error('too_many_localities', 400);
    }

    foreach ($localitiesRaw as $locality) {
      if (!is_string($locality) || $this->sanitizeText($locality) === NULL) {
        return $this->error('invalid_locality', 400);
      }
    }

    return NULL;
  }

  /**
   * Validates optional business filter query parameters.
   */
  public function validateBusinessFilters(Request $request): ?JsonResponse {
    $operation = strtoupper(trim((string) $request->query->get('operation_type', '')));
    if ($operation !== '' && !in_array($operation, self::OPERATION_TYPES, TRUE)) {
      return $this->error('invalid_operation_type', 400);
    }

    $asset = strtoupper(trim((string) $request->query->get('asset_type', '')));
    if ($asset !== '' && !in_array($asset, self::ASSET_TYPES, TRUE)) {
      return $this->error('invalid_asset_type', 400);
    }

    foreach (['surface_min', 'surface_max', 'capacity_min', 'capacity_max', 'budget_min', 'budget_max'] as $key) {
      $raw = $request->query->get($key);
      if ($raw === NULL || $raw === '') {
        continue;
      }
      if (!is_numeric($raw) || (float) $raw < 0) {
        return $this->error('invalid_range', 400);
      }
    }

    $bounds = $request->query->get('map_bounds');
    if ($bounds !== NULL && $bounds !== '' && !$this->isValidMapBounds((string) $bounds)) {
      return $this->error('invalid_map_bounds', 400);
    }

    return NULL;
  }

  /**
   * Sanitizes free text for location tokens.
   */
  public function sanitizeText(mixed $value): ?string {
    if (!is_string($value) || trim($value) === '') {
      return NULL;
    }
    $cleaned = preg_replace('/[^\p{L}\p{N}\s\-\']/u', '', substr(trim($value), 0, self::TEXT_MAX_LENGTH));
    return $cleaned !== '' ? $cleaned : NULL;
  }

  /**
   * Parses validated isochrone minutes from the request.
   */
  public function parseIsochroneMinutes(Request $request): int {
    $minutesRaw = $request->query->get('minutes');
    $minutes = is_numeric($minutesRaw) ? (int) $minutesRaw : 5;
    return max(self::ISOCHRONE_MIN_MINUTES, min(self::ISOCHRONE_MAX_MINUTES, $minutes));
  }

  /**
   * Parses validated isochrone transport from the request.
   */
  public function parseIsochroneTransport(Request $request): string {
    $transport = strtolower(trim((string) $request->query->get('transport', 'walking')));
    return in_array($transport, self::TRANSPORTS, TRUE) ? $transport : 'walking';
  }

  private function isValidMapBounds(string $bounds): bool {
    $parts = array_map('trim', explode(',', $bounds));
    if (count($parts) !== 4) {
      return FALSE;
    }
    foreach ($parts as $part) {
      if (!is_numeric($part)) {
        return FALSE;
      }
    }
    [$swLat, $swLng, $neLat, $neLng] = array_map('floatval', $parts);
    return $swLat >= -90 && $swLat <= 90
      && $neLat >= -90 && $neLat <= 90
      && $swLng >= -180 && $swLng <= 180
      && $neLng >= -180 && $neLng <= 180
      && $swLat <= $neLat;
  }

  private function error(string $code, int $status): JsonResponse {
    $response = new JsonResponse(['error' => $code], $status);
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    return $response;
  }

}
