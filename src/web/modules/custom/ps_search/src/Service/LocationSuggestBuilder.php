<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Site\Settings;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\GeoZone\GeoZoneType;

/**
 * Builds offer-derived location autocomplete groups for the search filter bar.
 */
final class LocationSuggestBuilder {

  use StringTranslationTrait;

  private const DEPARTMENT_SUGGEST_LIMIT = 5;

  private const REGION_SUGGEST_LIMIT = 5;

  public function __construct(
    private readonly Connection $database,
    private readonly LocationSearchFilter $locationSearchFilter,
    private readonly GeoZoneRepositoryInterface $geoZoneRepository,
  ) {}

  /**
   * Builds grouped location suggestions from offer address data.
   *
   * @return array{groups: list<array<string, mixed>>, suggestions: list<string>}
   */
  public function build(string $query, int $limit = 8): array {
    $limit = max(1, min($limit, 15));
    $suggestions = [];
    $seen = [];
    $groups = [];

    if ($this->locationSearchFilter->isDepartmentCode($query)) {
      $departmentItem = $this->buildDepartmentItem($query);
      if ($departmentItem !== NULL) {
        $groups[] = [
          'key' => 'department',
          'label' => (string) $this->t('Department'),
          'items' => [$departmentItem],
        ];
        $suggestions[] = $departmentItem['department_code'];
      }

      return [
        'groups' => $groups,
        'suggestions' => $suggestions,
      ];
    }

    $needle = '%' . $this->database->escapeLike($query) . '%';
    $isNumericQuery = preg_match('/^\d+$/', $query) === 1;

    if (!$isNumericQuery) {
      $this->appendRegionGroups($query, $groups, $suggestions, $seen);
      $this->appendDepartmentGroups($query, $groups, $suggestions, $seen);
      $this->appendCityGroups($needle, $limit, $groups, $suggestions, $seen);
    }

    if ($isNumericQuery && strlen($query) >= 3) {
      $this->appendPostalGroups($query, $limit, $groups, $suggestions, $seen);
    }

    $groups = $this->sortGroups($groups);

    return [
      'groups' => $groups,
      'suggestions' => $suggestions,
    ];
  }

  /**
   * @param list<array<string, mixed>> $cityItems
   * @param list<array<string, mixed>> $arrondissementItems
   * @param list<string> $suggestions
   * @param array<string, true> $seen
   */
  private function appendAddressRowsByLocality(
    string $locality,
    int $limit,
    array &$cityItems,
    array &$arrondissementItems,
    array &$suggestions,
    array &$seen,
  ): void {
    $select = $this->database->select('node__field_address', 'a');
    $select->fields('a', ['field_address_locality', 'field_address_administrative_area', 'field_address_postal_code']);
    $select->where('LOWER(a.field_address_locality) = :locality', [
      ':locality' => mb_strtolower($locality),
    ]);
    $select->groupBy('a.field_address_locality');
    $select->groupBy('a.field_address_administrative_area');
    $select->groupBy('a.field_address_postal_code');
    $select->orderBy('a.field_address_postal_code', 'ASC');
    $select->range(0, 5);

    foreach ($select->execute()->fetchAll() as $row) {
      if (count($suggestions) >= $limit) {
        return;
      }
      $this->appendAddressRow($row, $cityItems, $arrondissementItems, $suggestions, $seen);
    }
  }

  /**
   * @param object $row
   *   Address row from node__field_address.
   * @param list<array<string, mixed>> $cityItems
   * @param list<array<string, mixed>> $arrondissementItems
   * @param list<string> $suggestions
   * @param array<string, true> $seen
   */
  private function appendAddressRow(
    object $row,
    array &$cityItems,
    array &$arrondissementItems,
    array &$suggestions,
    array &$seen,
  ): void {
    $locality = $this->sanitizeText($row->field_address_locality);
    $adminArea = $this->sanitizeText($row->field_address_administrative_area);
    $postalCode = $this->sanitizeText($row->field_address_postal_code);

    if ($locality === NULL) {
      return;
    }

    $dedupeKey = mb_strtolower($locality . '|' . ($postalCode ?? ''));
    if (isset($seen[$dedupeKey])) {
      return;
    }
    $seen[$dedupeKey] = TRUE;

    $deptCode = $postalCode ? substr($postalCode, 0, 2) : '';
    $deptName = $adminArea ?: ($deptCode ? ($this->getDepartmentName($deptCode) ?? '') : '');

    $arrondissement = $this->getArrondissement($locality, $postalCode);
    if ($arrondissement !== NULL) {
      $arrondissementItems[] = [
        'label' => $arrondissement['label'],
        'type' => 'arrondissement',
        'locality' => $locality,
        'admin_area' => $deptName,
        'postal_code' => $postalCode ?? '',
        'arrondissement_number' => $arrondissement['number'],
      ];
    }

    $displayLabel = $postalCode ? "$locality ($postalCode)" : $locality;
    $cityItems[] = [
      'label' => $displayLabel,
      'type' => 'city',
      'locality' => $locality,
      'admin_area' => $deptName,
      'postal_code' => $postalCode ?? '',
    ];

    $suggestions[] = $locality;
  }

  /**
   * @param list<array<string, mixed>> $groups
   * @param list<string> $suggestions
   * @param array<string, true> $seen
   */
  private function appendCityGroups(
    string $needle,
    int $limit,
    array &$groups,
    array &$suggestions,
    array &$seen,
  ): void {
    if (count($suggestions) >= $limit) {
      return;
    }

    $select = $this->database->select('node__field_address', 'a');
    $select->fields('a', ['field_address_locality', 'field_address_administrative_area', 'field_address_postal_code']);
    $select->condition('a.field_address_locality', '', '<>');
    $select->condition('a.field_address_locality', $needle, 'LIKE');
    $select->groupBy('a.field_address_locality');
    $select->groupBy('a.field_address_administrative_area');
    $select->groupBy('a.field_address_postal_code');
    $select->orderBy('a.field_address_locality', 'ASC');
    $select->range(0, $limit);

    $cityItems = [];
    $arrondissementItems = [];

    foreach ($select->execute()->fetchAll() as $row) {
      $locality = $this->sanitizeText($row->field_address_locality);
      $adminArea = $this->sanitizeText($row->field_address_administrative_area);
      $postalCode = $this->sanitizeText($row->field_address_postal_code);

      if ($locality === NULL) {
        continue;
      }

      $dedupeKey = mb_strtolower($locality . '|' . ($postalCode ?? ''));
      if (isset($seen[$dedupeKey])) {
        continue;
      }
      $seen[$dedupeKey] = TRUE;

      $deptCode = $postalCode ? substr($postalCode, 0, 2) : '';
      $deptName = $adminArea ?: ($deptCode ? ($this->getDepartmentName($deptCode) ?? '') : '');

      $arrondissement = $this->getArrondissement($locality, $postalCode);
      if ($arrondissement !== NULL) {
        $arrondissementItems[] = [
          'label' => $arrondissement['label'],
          'type' => 'arrondissement',
          'locality' => $locality,
          'admin_area' => $deptName,
          'postal_code' => $postalCode ?? '',
          'arrondissement_number' => $arrondissement['number'],
        ];
      }

      $displayLabel = $postalCode ? "$locality ($postalCode)" : $locality;
      $cityItems[] = [
        'label' => $displayLabel,
        'type' => 'city',
        'locality' => $locality,
        'admin_area' => $deptName,
        'postal_code' => $postalCode ?? '',
      ];

      $suggestions[] = $locality;
      if (count($suggestions) >= $limit) {
        break;
      }
    }

    if ($arrondissementItems !== []) {
      $groups[] = [
        'key' => 'arrondissement',
        'label' => (string) $this->t('District'),
        'items' => $this->sortItemsAlphabetically($arrondissementItems),
      ];
    }

    if ($cityItems !== []) {
      $groups[] = [
        'key' => 'city',
        'label' => (string) $this->t('City'),
        'items' => $this->sortItemsAlphabetically($cityItems),
      ];
    }
  }

  /**
   * @param list<array<string, mixed>> $groups
   * @param list<string> $suggestions
   * @param array<string, true> $seen
   */
  private function appendRegionGroups(
    string $query,
    array &$groups,
    array &$suggestions,
    array &$seen,
  ): void {
    $regionItems = [];

    foreach ($this->geoZoneRepository->searchByLabelPrefix(
      $query,
      $this->resolveCountryCode(),
      GeoZoneType::Region,
      self::REGION_SUGGEST_LIMIT,
    ) as $zone) {
      $token = $this->geoZoneRepository->buildRegionToken($zone->slug);
      $key = mb_strtolower($token);
      if (isset($seen[$key])) {
        continue;
      }

      if ($this->locationSearchFilter->countOffersForToken($token) <= 0) {
        continue;
      }

      $seen[$key] = TRUE;
      $regionItems[] = [
        'label' => $zone->label,
        'type' => 'region',
        'region_slug' => $zone->slug,
        'region_token' => $token,
        'slug' => $zone->slug,
        'id' => $zone->id,
        'locality' => '',
        'admin_area' => '',
        'postal_code' => '',
      ];
      $suggestions[] = $zone->label;
    }

    if ($regionItems !== []) {
      $groups[] = [
        'key' => 'region',
        'label' => (string) $this->t('Region'),
        'items' => $this->sortItemsAlphabetically($regionItems),
      ];
    }
  }

  /**
   * @return array<array{name: string, code: string}>
   */
  private function searchDepartments(string $query): array {
    $results = [];
    foreach ($this->geoZoneRepository->searchByLabelPrefix(
      $query,
      $this->resolveCountryCode(),
      GeoZoneType::Department,
      self::DEPARTMENT_SUGGEST_LIMIT,
    ) as $zone) {
      $results[] = [
        'name' => $zone->label,
        'code' => $zone->code,
      ];
    }

    return $results;
  }

  private function getDepartmentName(string $code): ?string {
    $zone = $this->geoZoneRepository->findDepartmentByCode($code, $this->resolveCountryCode());

    return $zone?->label;
  }

  /**
   * @param list<array<string, mixed>> $groups
   * @param list<string> $suggestions
   * @param array<string, true> $seen
   */
  private function appendDepartmentGroups(
    string $query,
    array &$groups,
    array &$suggestions,
    array &$seen,
  ): void {
    $deptItems = [];
    foreach ($this->searchDepartments($query) as $match) {
      if (count($deptItems) >= self::DEPARTMENT_SUGGEST_LIMIT) {
        break;
      }

      $key = mb_strtolower($match['name']);
      if (isset($seen[$key])) {
        continue;
      }
      $seen[$key] = TRUE;

      if ($this->locationSearchFilter->countOffersForToken($match['code']) <= 0) {
        continue;
      }

      $deptItems[] = $this->enrichWithGeoZone([
        'label' => "{$match['name']} ({$match['code']})",
        'type' => 'department',
        'locality' => '',
        'admin_area' => $match['name'],
        'postal_code' => '',
        'department_code' => $match['code'],
      ]);
      $suggestions[] = $match['name'];
    }

    if ($deptItems !== []) {
      $groups[] = [
        'key' => 'department',
        'label' => (string) $this->t('Department'),
        'items' => $this->sortItemsAlphabetically($deptItems),
      ];
    }
  }

  /**
   * @param list<array<string, mixed>> $groups
   * @param list<string> $suggestions
   * @param array<string, true> $seen
   */
  private function appendPostalGroups(
    string $query,
    int $limit,
    array &$groups,
    array &$suggestions,
    array &$seen,
  ): void {
    if (count($suggestions) >= $limit) {
      return;
    }

    $select = $this->database->select('node__field_address', 'a');
    $select->fields('a', ['field_address_postal_code', 'field_address_locality', 'field_address_administrative_area']);
    $select->condition('a.field_address_postal_code', '', '<>');
    if (strlen($query) === 5) {
      $select->condition('a.field_address_postal_code', $query);
    }
    else {
      $select->condition('a.field_address_postal_code', $query . '%', 'LIKE');
    }
    $select->groupBy('a.field_address_postal_code');
    $select->groupBy('a.field_address_locality');
    $select->groupBy('a.field_address_administrative_area');
    $select->orderBy('a.field_address_postal_code', 'ASC');
    $select->range(0, $limit - count($suggestions));

    $postalItems = [];
    $arrondissementItems = [];

    foreach ($select->execute()->fetchAll() as $row) {
      $postalCode = $this->sanitizeText($row->field_address_postal_code);
      $locality = $this->sanitizeText($row->field_address_locality);
      $adminArea = $this->sanitizeText($row->field_address_administrative_area);

      if ($postalCode === NULL) {
        continue;
      }
      $key = mb_strtolower($postalCode);
      if (isset($seen[$key])) {
        continue;
      }
      $seen[$key] = TRUE;

      $deptCode = substr($postalCode, 0, 2);
      $deptName = $adminArea ?: ($deptCode ? ($this->getDepartmentName($deptCode) ?? '') : '');

      $arrondissement = $locality ? $this->getArrondissement($locality, $postalCode) : NULL;
      if ($arrondissement !== NULL) {
        $arrondissementItems[] = [
          'label' => $arrondissement['label'],
          'type' => 'arrondissement',
          'locality' => $locality,
          'admin_area' => $deptName,
          'postal_code' => $postalCode,
          'arrondissement_number' => $arrondissement['number'],
        ];
      }
      else {
        $postalItems[] = [
          'label' => $postalCode,
          'type' => 'postal_code',
          'postal_code' => $postalCode,
          'locality' => $locality ?: '',
          'admin_area' => $deptName,
        ];
      }

      $suggestions[] = $postalCode;
      if (count($suggestions) >= $limit) {
        break;
      }
    }

    if ($arrondissementItems !== []) {
      $foundArrGroup = FALSE;
      foreach ($groups as &$group) {
        if ($group['key'] === 'arrondissement') {
          $group['items'] = $this->sortItemsAlphabetically(
            array_merge($arrondissementItems, $group['items']),
          );
          $foundArrGroup = TRUE;
          break;
        }
      }
      unset($group);
      if (!$foundArrGroup) {
        array_unshift($groups, [
          'key' => 'arrondissement',
          'label' => (string) $this->t('District'),
          'items' => $this->sortItemsAlphabetically($arrondissementItems),
        ]);
      }
    }

    if ($postalItems !== []) {
      $groups[] = [
        'key' => 'postal_code',
        'label' => (string) $this->t('Postal code'),
        'items' => $this->sortItemsAlphabetically($postalItems),
      ];
    }
  }

  /**
   * @return array<string, mixed>|null
   */
  private function buildDepartmentItem(string $code): ?array {
    $name = $this->getDepartmentName($code);
    if ($name === NULL) {
      return NULL;
    }

    if ($this->locationSearchFilter->countOffersForToken($code) <= 0) {
      return NULL;
    }

    return $this->enrichWithGeoZone([
      'label' => $this->locationSearchFilter->buildDepartmentLabel($code),
      'type' => 'department',
      'locality' => '',
      'admin_area' => $name,
      'postal_code' => '',
      'department_code' => $code,
    ]);
  }

  /**
   * Adds GeoZone slug/id when a referential match exists (L3 suggest).
   *
   * @param array<string, mixed> $item
   *
   * @return array<string, mixed>
   */
  private function enrichWithGeoZone(array $item): array {
    $countryCode = $this->resolveCountryCode();
    $deptCode = is_string($item['department_code'] ?? NULL) ? $item['department_code'] : '';
    if ($deptCode === '' && is_string($item['postal_code'] ?? NULL) && strlen($item['postal_code']) >= 2) {
      $deptCode = substr($item['postal_code'], 0, 2);
    }

    if ($deptCode !== '') {
      $zone = $this->geoZoneRepository->findByPostalPrefix($deptCode, $countryCode);
      if ($zone !== NULL) {
        $item['slug'] = $zone->slug;
        $item['id'] = $zone->id;
      }
    }

    return $item;
  }

  private function resolveCountryCode(): string {
    $code = Settings::get('ps_country_code');
    return is_string($code) && $code !== '' ? strtolower($code) : 'com';
  }

  /**
   * @param list<array<string, mixed>> $items
   *
   * @return list<array<string, mixed>>
   */
  private function sortItemsAlphabetically(array $items): array {
    usort($items, static function (array $a, array $b): int {
      return strcasecmp((string) ($a['label'] ?? ''), (string) ($b['label'] ?? ''));
    });
    return $items;
  }

  /**
   * @param list<array<string, mixed>> $groups
   *
   * @return list<array<string, mixed>>
   */
  private function sortGroups(array $groups): array {
    $order = [
      'region' => 0,
      'arrondissement' => 1,
      'department' => 2,
      'city' => 3,
      'postal_code' => 4,
    ];
    usort($groups, static function (array $a, array $b) use ($order): int {
      return ($order[$a['key'] ?? ''] ?? 99) <=> ($order[$b['key'] ?? ''] ?? 99);
    });
    return $groups;
  }

  /**
   * @return array{label: string, number: int}|null
   */
  private function getArrondissement(string $locality, ?string $postalCode): ?array {
    if ($postalCode === NULL || strlen($postalCode) < 5) {
      return NULL;
    }

    $localityLower = mb_strtolower($locality);
    $prefix = substr($postalCode, 0, 2);
    $suffix = (int) substr($postalCode, 3, 2);

    if ($localityLower === 'paris' && $prefix === '75' && $suffix >= 1 && $suffix <= 20) {
      $ordinal = $this->formatOrdinal($suffix);
      return [
        'label' => "Paris, {$ordinal} arrondissement ($postalCode)",
        'number' => $suffix,
      ];
    }

    if ($localityLower === 'lyon' && $prefix === '69' && $suffix >= 1 && $suffix <= 9) {
      $ordinal = $this->formatOrdinal($suffix);
      return [
        'label' => "Lyon, {$ordinal} arrondissement ($postalCode)",
        'number' => $suffix,
      ];
    }

    if ($localityLower === 'marseille' && $prefix === '13' && $suffix >= 1 && $suffix <= 16) {
      $ordinal = $this->formatOrdinal($suffix);
      return [
        'label' => "Marseille, {$ordinal} arrondissement ($postalCode)",
        'number' => $suffix,
      ];
    }

    return NULL;
  }

  private function formatOrdinal(int $number): string {
    return $number === 1 ? '1er' : $number . 'ème';
  }

  private function sanitizeText(mixed $value): ?string {
    if (!is_string($value) || trim($value) === '') {
      return NULL;
    }
    $cleaned = preg_replace('/[^\p{L}\p{N}\s\-\']/u', '', substr(trim($value), 0, 100));
    return $cleaned !== '' ? $cleaned : NULL;
  }

}
