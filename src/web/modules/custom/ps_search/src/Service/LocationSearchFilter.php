<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Site\Settings;
use Drupal\ps_dictionary\Service\DictionaryResolver;
use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\GeoZone\GeoZoneType;
use Drupal\search_api\Query\ConditionGroupInterface;
use Drupal\search_api\Query\QueryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Applies unified location token filtering to Search API queries.
 *
 * Each token matches with OR across city, postal code and department fields.
 * Department codes (2 digits) also match postal prefixes.
 */
final class LocationSearchFilter {

  private const DEPARTMENT_DICTIONARY_TYPE = 'department';

  /**
   * Address-related fields indexed for offers.
   */
  private const ADDRESS_FIELDS = [
    'field_address_locality',
    'field_address_postal_code',
    'field_address_admin_area',
  ];

  public function __construct(
    private readonly Connection $database,
    private readonly DictionaryResolver $dictionaryResolver,
    private readonly AdministrativeRegionRegistry $regionRegistry,
    private readonly GeoZoneRepositoryInterface $geoZoneRepository,
  ) {}

  /**
   * Extracts location tokens from a request (comma string or locality[] array).
   *
   * @return list<string>
   */
  public function extractTokensFromRequest(Request $request): array {
    $all = $request->query->all();
    if (array_key_exists('locations', $all)) {
      $locations = $all['locations'];
      if (is_string($locations) && trim($locations) !== '') {
        return $this->extractTokens($locations);
      }
      if (is_array($locations)) {
        return $this->normalizeTokens($locations);
      }
    }

    $raw = $all['locality'] ?? NULL;
    if (is_array($raw)) {
      return $this->normalizeTokens($raw);
    }

    return $this->extractTokens(is_string($raw) ? $raw : NULL);
  }

  /**
   * Parses and deduplicates location tokens from mixed input.
   *
   * @return list<string>
   */
  public function extractTokens(mixed $value): array {
    if (is_array($value)) {
      return $this->normalizeTokens($value);
    }

    if (!is_string($value) || trim($value) === '') {
      return [];
    }

    $parts = preg_split('/[,;]+/', $value) ?: [];
    return $this->normalizeTokens($parts);
  }

  /**
   * Applies location OR conditions to a Search API query.
   *
   * @param list<string> $tokens
   */
  public function applyToQuery(QueryInterface $query, array $tokens): void {
    $tokens = $this->normalizeTokens($tokens);
    if ($tokens === []) {
      return;
    }

    $rootGroup = $query->createConditionGroup('OR');
    foreach ($tokens as $token) {
      $tokenGroup = $this->buildTokenConditionGroup($query, $token);
      if ($tokenGroup !== NULL) {
        $rootGroup->addConditionGroup($tokenGroup);
      }
    }

    $query->addConditionGroup($rootGroup);
  }

  /**
   * Returns published offer count for a single location token (offer-derived).
   */
  public function countOffersForToken(string $token): int {
    $token = $this->sanitizeText($token) ?? $token;
    if ($token === '') {
      return 0;
    }

    if ($this->regionRegistry->isRegionToken($token)) {
      $meta = $this->resolveRegionContext($token) ?? ['label' => $token, 'locality' => '', 'admin_area' => ''];
    }
    elseif (preg_match('/^\d{5}$/', $token) === 1) {
      $meta = ['locality' => '', 'admin_area' => ''];
    }
    elseif ($this->isDepartmentCode($token)) {
      $meta = [
        'locality' => '',
        'admin_area' => $this->getDepartmentName($token),
      ];
    }
    else {
      $meta = [
        'locality' => $token,
        'admin_area' => '',
      ];
    }

    return $this->resolveCentroidForToken($token, $meta)['count'];
  }

  /**
   * Whether a token is a known INSEE department code (2 or 3 digits).
   */
  public function isDepartmentCode(string $token): bool {
    return preg_match('/^\d{2,3}$/', $token) === 1
      && $this->dictionaryResolver->isValid(self::DEPARTMENT_DICTIONARY_TYPE, $token);
  }

  /**
   * Builds OR conditions for one location token.
   */
  private function buildTokenConditionGroup(QueryInterface $query, string $token): ?ConditionGroupInterface {
    if ($this->regionRegistry->isRegionToken($token)) {
      return $this->buildRegionConditionGroup($query, $token);
    }

    if ($this->isDepartmentCode($token)) {
      return $this->buildDepartmentConditionGroup($query, $token);
    }

    $tokenGroup = $query->createConditionGroup('OR');

    if (preg_match('/^\d{5}$/', $token) === 1) {
      $tokenGroup->addCondition('field_address_postal_code', $token);
      return $tokenGroup;
    }

    foreach (self::ADDRESS_FIELDS as $field) {
      $tokenGroup->addCondition($field, $token);
    }

    return $tokenGroup;
  }

  /**
   * Resolves structured metadata for one location token.
   *
   * @return array{
   *   label: string,
   *   type: string,
   *   locality: string,
   *   admin_area: string,
   *   postal_code: string,
   *   lat: float|null,
   *   lng: float|null,
   *   offer_count: int
   *   }
   */
  public function resolveTokenMetadata(string $token): array {
    $token = $this->sanitizeText($token) ?? $token;
    $meta = [
      'label' => $token,
      'type' => 'city',
      'locality' => $token,
      'admin_area' => '',
      'postal_code' => '',
      'lat' => NULL,
      'lng' => NULL,
      'offer_count' => 0,
    ];

    if ($this->regionRegistry->isRegionToken($token)) {
      $meta = $this->resolveRegionToken($token, $meta);
    }
    elseif (preg_match('/^\d{5}$/', $token) === 1) {
      $meta = $this->resolvePostalToken($token, $meta);
    }
    elseif ($this->isDepartmentCode($token)) {
      $meta = $this->resolveDepartmentToken($token, $meta);
    }
    else {
      $meta = $this->resolveLocalityToken($token, $meta);
    }

    $centroid = $this->resolveCentroidForToken($token, $meta);
    $meta['lat'] = $centroid['lat'];
    $meta['lng'] = $centroid['lng'];
    $meta['offer_count'] = $centroid['count'];

    return $meta;
  }

  /**
   * @param list<string> $values
   *
   * @return list<string>
   */
  private function normalizeTokens(array $values): array {
    $tokens = [];
    foreach ($values as $value) {
      $cleaned = $this->sanitizeText($value);
      if ($cleaned === NULL) {
        continue;
      }
      $key = mb_strtolower($cleaned);
      $tokens[$key] = $cleaned;
      if (count($tokens) >= 10) {
        break;
      }
    }

    return array_values($tokens);
  }

  /**
   * @param array<string, mixed> $meta
   *
   * @return array<string, mixed>
   */
  private function resolvePostalToken(string $token, array $meta): array {
    $select = $this->database->select('node__field_address', 'a');
    $select->fields('a', ['field_address_locality', 'field_address_administrative_area']);
    $select->condition('a.field_address_postal_code', $token);
    $select->range(0, 1);
    $row = $select->execute()->fetchAssoc();

    if ($row !== FALSE) {
      $locality = $this->sanitizeText($row['field_address_locality']) ?? '';
      $adminArea = $this->sanitizeText($row['field_address_administrative_area']) ?? '';
      if ($adminArea === '') {
        $adminArea = $this->getDepartmentName(substr($token, 0, 2));
      }
      $arrondissement = $locality !== '' ? $this->formatArrondissementLabel($locality, $token) : NULL;

      $meta['type'] = $arrondissement !== NULL ? 'arrondissement' : 'postal_code';
      $meta['locality'] = $locality;
      $meta['admin_area'] = $adminArea;
      $meta['postal_code'] = $token;
      $meta['label'] = $arrondissement ?? ($locality !== '' ? "$locality ($token)" : $token);
    }

    return $meta;
  }

  /**
   * @param array<string, mixed> $meta
   *
   * @return array<string, mixed>
   */
  private function resolveDepartmentToken(string $token, array $meta): array {
    $deptName = $this->getDepartmentName($token);
    $meta['type'] = 'department';
    $meta['locality'] = '';
    $meta['admin_area'] = $deptName;
    $meta['postal_code'] = '';
    $meta['label'] = $this->buildDepartmentLabel($token, $deptName);
    $region = $this->regionRegistry->getRegionLabelForDepartment($token);
    if ($region !== NULL) {
      $meta['region_name'] = $region;
    }

    return $meta;
  }

  /**
   * Builds a human label for a department filter token.
   */
  public function buildDepartmentLabel(string $token, ?string $deptName = NULL): string {
    $deptName ??= $this->getDepartmentName($token);
    if ($deptName !== '') {
      return "$deptName ($token)";
    }

    return $token;
  }

  /**
   * @param array<string, mixed> $meta
   *
   * @return array<string, mixed>
   */
  private function resolveRegionToken(string $token, array $meta): array {
    $context = $this->resolveRegionContext($token);
    if ($context === NULL) {
      return $meta;
    }

    $meta['type'] = 'region';
    $meta['locality'] = '';
    $meta['admin_area'] = '';
    $meta['postal_code'] = '';
    $meta['label'] = $context['label'];
    $meta['slug'] = $context['slug'];
    $meta['region_slug'] = $context['slug'];

    return $meta;
  }

  /**
   * @return array{label: string, slug: string, departments: list<string>, postal_prefixes: list<string>}|null
   */
  private function resolveRegionContext(string $token): ?array {
    if (!$this->regionRegistry->isRegionToken($token)) {
      return NULL;
    }

    $slug = $this->regionRegistry->parseRegionToken($token);
    if ($slug === NULL) {
      return NULL;
    }

    $frenchRegion = $this->regionRegistry->findBySlug($slug);
    if ($frenchRegion !== NULL) {
      return [
        'label' => $frenchRegion['label'],
        'slug' => $frenchRegion['slug'],
        'departments' => $frenchRegion['departments'],
        'postal_prefixes' => [],
      ];
    }

    $zone = $this->geoZoneRepository->findBySlug($slug, $this->resolveCountryCode());
    if ($zone !== NULL && $zone->type === GeoZoneType::Region) {
      return [
        'label' => $zone->label,
        'slug' => $zone->slug,
        'departments' => [],
        'postal_prefixes' => $zone->postalPrefixes,
      ];
    }

    return NULL;
  }

  private function buildRegionConditionGroup(QueryInterface $query, string $token): ?ConditionGroupInterface {
    $context = $this->resolveRegionContext($token);
    if ($context === NULL) {
      return NULL;
    }

    $regionGroup = $query->createConditionGroup('OR');
    $hasConditions = FALSE;
    if ($context['departments'] !== []) {
      foreach ($context['departments'] as $departmentCode) {
        $departmentGroup = $this->buildDepartmentConditionGroup($query, $departmentCode);
        if ($departmentGroup !== NULL) {
          $regionGroup->addConditionGroup($departmentGroup);
          $hasConditions = TRUE;
        }
      }
      return $hasConditions ? $regionGroup : NULL;
    }

    foreach ($context['postal_prefixes'] as $prefix) {
      $prefixGroup = $query->createConditionGroup('OR');
      $prefixGroup->addCondition(
        'field_address_postal_code',
        [$prefix . '000', $prefix . '999'],
        'BETWEEN',
      );
      $regionGroup->addConditionGroup($prefixGroup);
      $hasConditions = TRUE;
    }

    return $hasConditions ? $regionGroup : NULL;
  }

  private function buildDepartmentConditionGroup(QueryInterface $query, string $token): ?ConditionGroupInterface {
    $tokenGroup = $query->createConditionGroup('OR');

    if (!$this->isDepartmentCode($token)) {
      return NULL;
    }

    $deptName = $this->getDepartmentName($token);
    $tokenGroup->addCondition(
      'field_address_postal_code',
      [$token . '000', $token . '999'],
      'BETWEEN',
    );
    if ($deptName !== '') {
      $tokenGroup->addCondition('field_address_admin_area', $deptName);
      $tokenGroup->addCondition('field_address_locality', $deptName);
    }

    return $tokenGroup;
  }

  /**
   * @param array<string, mixed> $meta
   *
   * @return array<string, mixed>
   */
  private function resolveLocalityToken(string $token, array $meta): array {
    $select = $this->database->select('node__field_address', 'a');
    $select->fields('a', [
      'field_address_locality',
      'field_address_administrative_area',
      'field_address_postal_code',
    ]);
    $or = $select->orConditionGroup()
      ->condition('a.field_address_locality', $token)
      ->condition('a.field_address_administrative_area', $token);
    $select->condition($or);
    $select->range(0, 1);
    $row = $select->execute()->fetchAssoc();

    if ($row !== FALSE) {
      $locality = $this->sanitizeText($row['field_address_locality']) ?? $token;
      $adminArea = $this->sanitizeText($row['field_address_administrative_area']) ?? '';
      $postalCode = $this->sanitizeText($row['field_address_postal_code']) ?? '';
      if ($adminArea === '' && $postalCode !== '') {
        $adminArea = $this->getDepartmentName(substr($postalCode, 0, 2));
      }
      $meta['locality'] = $locality;
      $meta['admin_area'] = $adminArea;
      $meta['postal_code'] = $postalCode;
      $meta['label'] = $postalCode !== '' ? "$locality ($postalCode)" : $locality;
      $meta['type'] = 'city';
    }

    return $meta;
  }

  /**
   * @param array<string, mixed> $meta
   *
   * @return array{lat: float|null, lng: float|null, count: int}
   */
  private function resolveCentroidForToken(string $token, array $meta): array {
    $select = $this->database->select('node__field_geo', 'g');
    $select->addExpression('AVG(g.field_geo_lat)', 'lat');
    $select->addExpression('AVG(g.field_geo_lon)', 'lng');
    $select->addExpression('COUNT(*)', 'offer_count');
    $select->innerJoin('node_field_data', 'n', 'n.nid = g.entity_id AND n.status = 1');
    $select->innerJoin('node__field_address', 'a', 'a.entity_id = g.entity_id');

    if ($this->regionRegistry->isRegionToken($token)) {
      $context = $this->resolveRegionContext($token);
      if ($context !== NULL && $context['departments'] !== []) {
        $or = $select->orConditionGroup();
        foreach ($context['departments'] as $departmentCode) {
          $deptName = $this->getDepartmentName($departmentCode);
          $deptOr = $select->orConditionGroup()
            ->condition('a.field_address_postal_code', $departmentCode . '%', 'LIKE');
          if ($deptName !== '') {
            $deptOr->condition('a.field_address_administrative_area', $deptName);
            $deptOr->condition('a.field_address_locality', $deptName);
          }
          $or->condition($deptOr);
        }
        $select->condition($or);
      }
      elseif ($context !== NULL && $context['postal_prefixes'] !== []) {
        $or = $select->orConditionGroup();
        foreach ($context['postal_prefixes'] as $prefix) {
          $or->condition('a.field_address_postal_code', $prefix . '%', 'LIKE');
        }
        $select->condition($or);
      }
    }
    elseif (preg_match('/^\d{5}$/', $token) === 1) {
      $select->condition('a.field_address_postal_code', $token);
    }
    elseif ($this->isDepartmentCode($token)) {
      $deptName = $this->getDepartmentName($token);
      $or = $select->orConditionGroup()
        ->condition('a.field_address_postal_code', $token . '%', 'LIKE');
      if ($deptName !== '') {
        $or->condition('a.field_address_administrative_area', $deptName);
        $or->condition('a.field_address_locality', $deptName);
      }
      $select->condition($or);
    }
    elseif (($meta['admin_area'] ?? '') !== '' && ($meta['locality'] ?? '') === '') {
      $select->condition('a.field_address_administrative_area', $meta['admin_area']);
    }
    elseif (($meta['locality'] ?? '') !== '') {
      $select->condition('a.field_address_locality', $meta['locality']);
    }
    else {
      $or = $select->orConditionGroup()
        ->condition('a.field_address_locality', $token)
        ->condition('a.field_address_postal_code', $token)
        ->condition('a.field_address_administrative_area', $token);
      $select->condition($or);
    }

    $row = $select->execute()->fetchAssoc();
    if ($row === FALSE || $row['lat'] === NULL) {
      return ['lat' => NULL, 'lng' => NULL, 'count' => 0];
    }

    return [
      'lat' => round((float) $row['lat'], 6),
      'lng' => round((float) $row['lng'], 6),
      'count' => (int) $row['offer_count'],
    ];
  }

  /**
   * Returns a formatted arrondissement label when applicable.
   */
  private function formatArrondissementLabel(string $locality, string $postalCode): ?string {
    if (strlen($postalCode) !== 5) {
      return NULL;
    }

    $suffix = (int) substr($postalCode, -2);
    $prefix = substr($postalCode, 0, 2);
    $localityLower = mb_strtolower($locality);

    if ($localityLower === 'paris' && $prefix === '75' && $suffix >= 1 && $suffix <= 20) {
      return "Paris, {$suffix}ème arrondissement ($postalCode)";
    }
    if ($localityLower === 'lyon' && $prefix === '69' && $suffix >= 1 && $suffix <= 9) {
      return "Lyon, {$suffix}ème arrondissement ($postalCode)";
    }
    if ($localityLower === 'marseille' && $prefix === '13' && $suffix >= 1 && $suffix <= 16) {
      return "Marseille, {$suffix}ème arrondissement ($postalCode)";
    }

    return NULL;
  }

  /**
   * Returns department name from a 2-digit INSEE code.
   */
  private function getDepartmentName(string $code): string {
    return $this->dictionaryResolver->resolveLabel(self::DEPARTMENT_DICTIONARY_TYPE, $code) ?? '';
  }

  private function resolveCountryCode(): string {
    $code = Settings::get('ps_country_code');
    return is_string($code) && $code !== '' ? strtolower($code) : 'com';
  }

  /**
   * Sanitizes free text input.
   */
  private function sanitizeText(mixed $value): ?string {
    if (!is_string($value) || trim($value) === '') {
      return NULL;
    }
    $trimmed = trim($value);
    if (str_starts_with($trimmed, AdministrativeRegionRegistry::TOKEN_PREFIX)) {
      $slug = substr($trimmed, strlen(AdministrativeRegionRegistry::TOKEN_PREFIX));
      $slug = preg_replace('/[^a-z0-9\-]/', '', strtolower($slug));
      return $slug !== '' ? AdministrativeRegionRegistry::TOKEN_PREFIX . $slug : NULL;
    }
    $cleaned = preg_replace('/[^\p{L}\p{N}\s\-\']/u', '', substr($trimmed, 0, 100));
    return $cleaned !== '' ? $cleaned : NULL;
  }

}
