<?php

declare(strict_types=1);

namespace Drupal\ps_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\search_api\Entity\Index;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns the number of search results matching the given filter parameters.
 *
 * Used by the Search Filter Bar JS to update the "Afficher X résultats" button
 * in real time without navigating away from the current page.
 *
 * GET /ps-search/count
 *   ?operation_type=LOC      (optional)
 *   &asset_type=BUR          (optional)
 *   &locality=Paris          (optional, free text — approximate)
 *   &surface_min=100         (optional, positive number in m²)
 *   &surface_max=500         (optional, positive number in m²)
 *   &budget_min=100          (optional, positive number in €/m²/year)
 *   &budget_max=5000         (optional, positive number in €/m²/year)
 */
final class SearchCountController extends ControllerBase {

  /**
   * Max accepted server-side bound for surface filters (m²).
   */
  private const MAX_SURFACE = 200000.0;

  /**
   * Max accepted server-side bound for budget filters (€/m²/year).
   */
  private const MAX_BUDGET = 100000.0;

  public function __construct(
    private readonly Connection $database,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('database'),
    );
  }

  /**
   * Returns result count as JSON.
   */
  public function count(Request $request): JsonResponse {
    $operationType = $request->query->get('operation_type');
    $assetType = $request->query->get('asset_type');
    $localityRaw = $request->query->get('locality');
    $surfaceMin = $request->query->get('surface_min');
    $surfaceMax = $request->query->get('surface_max');
    $budgetMin = $request->query->get('budget_min');
    $budgetMax = $request->query->get('budget_max');
    $capacityMin = $request->query->get('capacity_min');
    $capacityMax = $request->query->get('capacity_max');

    // Sanitize all inputs.
    $operationType = $this->sanitizeCode($operationType);
    $assetType = $this->sanitizeCode($assetType);
    $localityTokens = $this->extractLocationTokens($localityRaw);
    $surfaceMin = $this->sanitizePositiveNumber($surfaceMin, self::MAX_SURFACE);
    $surfaceMax = $this->sanitizePositiveNumber($surfaceMax, self::MAX_SURFACE);
    $budgetMin = $this->sanitizePositiveNumber($budgetMin, self::MAX_BUDGET);
    $budgetMax = $this->sanitizePositiveNumber($budgetMax, self::MAX_BUDGET);
    $capacityMin = $this->sanitizePositiveNumber($capacityMin, 500.0);
    $capacityMax = $this->sanitizePositiveNumber($capacityMax, 500.0);

    $index = Index::load('offers');
    if (!$index) {
      return new JsonResponse(['count' => 0, 'error' => 'index_unavailable'], 503);
    }

    $query = $index->query();
    $query->range(0, 0);

    if ($operationType !== NULL) {
      $query->addCondition('field_operation_type', $operationType);
    }
    if ($assetType !== NULL) {
      $query->addCondition('field_asset_type', $assetType);
    }
    // Location tokens: OR match across city/postal code/department.
    if ($localityTokens !== []) {
      $locationGroup = $query->createConditionGroup('OR');
      foreach ($localityTokens as $token) {
        $locationGroup->addCondition('field_address_locality', $token);
        $locationGroup->addCondition('field_address_postal_code', $token);
        $locationGroup->addCondition('field_address_admin_area', $token);
      }
      $query->addConditionGroup($locationGroup);
    }
    if ($surfaceMin !== NULL) {
      $query->addCondition('surface_total', $surfaceMin, '>=');
    }
    if ($surfaceMax !== NULL) {
      $query->addCondition('surface_total', $surfaceMax, '<=');
    }
    if ($budgetMin !== NULL) {
      $query->addCondition('field_budget_value', $budgetMin, '>=');
    }
    if ($budgetMax !== NULL) {
      $query->addCondition('field_budget_value', $budgetMax, '<=');
    }
    if ($capacityMin !== NULL) {
      $query->addCondition('field_capacity_total', $capacityMin, '>=');
    }
    if ($capacityMax !== NULL) {
      $query->addCondition('field_capacity_total', $capacityMax, '<=');
    }

    $this->applyMoreCriteriaConditions($query, $request);

    try {
      $results = $query->execute();
      $count = (int) $results->getResultCount();
    }
    catch (\Exception) {
      return new JsonResponse(['count' => 0, 'error' => 'query_failed'], 200);
    }

    $response = new JsonResponse(['count' => $count]);
    // Short cache: count changes when offers are added/removed.
    $response->setMaxAge(60);
    $response->setPublic();

    return $response;
  }

  /**
   * Returns location suggestions for autocomplete.
   */
  public function suggest(Request $request): JsonResponse {
    $queryRaw = $request->query->get('q');
    $query = $this->sanitizeText($queryRaw);
    if ($query === NULL || mb_strlen($query) < 2) {
      return new JsonResponse(['groups' => [], 'suggestions' => []]);
    }

    $limitRaw = $request->query->get('limit');
    $limit = is_numeric($limitRaw) ? (int) $limitRaw : 8;
    $limit = max(1, min($limit, 15));

    $suggestions = [];
    $seen = [];
    $needle = '%' . $this->database->escapeLike($query) . '%';

    $groups = [];

    // --- Cities with structured data (locality, admin_area, postal_code) ---
    if (count($suggestions) < $limit) {
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
        $key = mb_strtolower($locality);
        if (isset($seen[$key])) {
          continue;
        }
        $seen[$key] = TRUE;

        // Derive department name from postal code if admin_area is empty.
        $deptCode = $postalCode ? substr($postalCode, 0, 2) : '';
        $deptName = $adminArea ?: ($deptCode ? $this->getDepartmentName($deptCode) : '');

        // Check if this is an arrondissement (Paris, Lyon, Marseille).
        $arrondissement = $this->getArrondissement($locality, $postalCode);
        
        if ($arrondissement !== NULL) {
          // Format: "Paris, 8ème arrondissement (75008)"
          $arrondissementItems[] = [
            'label' => $arrondissement['label'],
            'type' => 'arrondissement',
            'locality' => $locality,
            'admin_area' => $deptName ?? '',
            'postal_code' => $postalCode ?? '',
            'arrondissement_number' => $arrondissement['number'],
          ];
        }
        
        // City format: "Locality (PostalCode)"
        $displayLabel = $postalCode ? "$locality ($postalCode)" : $locality;
        
        $item = [
          'label' => $displayLabel,
          'type' => 'city',
          'locality' => $locality,
          'admin_area' => $deptName ?? '',
          'postal_code' => $postalCode ?? '',
        ];

        $cityItems[] = $item;
        $suggestions[] = $locality;
        if (count($suggestions) >= $limit) {
          break;
        }
      }

      // Arrondissement group (appears first, like BNPPRE)
      if ($arrondissementItems !== []) {
        $groups[] = [
          'key' => 'arrondissement',
          'label' => (string) $this->t('Arrondissement'),
          'items' => $arrondissementItems,
        ];
      }
      
      // City group
      if ($cityItems !== []) {
        $groups[] = [
          'key' => 'city',
          'label' => (string) $this->t('Ville'),
          'items' => $cityItems,
        ];
      }
    }

    // --- Departments (if search matches department name) ---
    if (count($suggestions) < $limit) {
      $deptMatches = $this->searchDepartments($query);
      if (!empty($deptMatches)) {
        $deptItems = [];
        foreach ($deptMatches as $match) {
          $key = mb_strtolower($match['name']);
          if (isset($seen[$key])) {
            continue;
          }
          $seen[$key] = TRUE;
          
          // Format: "Department Name (Code)"
          $deptItems[] = [
            'label' => "{$match['name']} ({$match['code']})",
            'type' => 'department',
            'locality' => '',
            'admin_area' => $match['name'],
            'postal_code' => '',
            'department_code' => $match['code'],
          ];
          $suggestions[] = $match['name'];
          if (count($suggestions) >= $limit) {
            break;
          }
        }
        
        if ($deptItems !== []) {
          $groups[] = [
            'key' => 'department',
            'label' => (string) $this->t('Département'),
            'items' => $deptItems,
          ];
        }
      }
    }

    // --- Postal codes (fallback for direct postal code search) ---
    if (count($suggestions) < $limit) {
      $select = $this->database->select('node__field_address', 'a');
      $select->fields('a', ['field_address_postal_code', 'field_address_locality', 'field_address_administrative_area']);
      $select->condition('a.field_address_postal_code', '', '<>');
      $select->condition('a.field_address_postal_code', $needle, 'LIKE');
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

        // Derive department if admin_area is empty
        $deptCode = substr($postalCode, 0, 2);
        $deptName = $adminArea ?: ($deptCode ? $this->getDepartmentName($deptCode) : '');

        // Check if this postal code is an arrondissement
        $arrondissement = $locality ? $this->getArrondissement($locality, $postalCode) : NULL;
        
        if ($arrondissement !== NULL) {
          // Add as formatted arrondissement
          $arrondissementItems[] = [
            'label' => $arrondissement['label'],
            'type' => 'arrondissement',
            'locality' => $locality,
            'admin_area' => $deptName,
            'postal_code' => $postalCode,
            'arrondissement_number' => $arrondissement['number'],
          ];
        } else {
          // Add as regular postal code
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

      // Arrondissements detected from postal code search appear first
      if ($arrondissementItems !== []) {
        // Insert at beginning or merge with existing arrondissement group
        $foundArrGroup = FALSE;
        foreach ($groups as &$group) {
          if ($group['key'] === 'arrondissement') {
            $group['items'] = array_merge($arrondissementItems, $group['items']);
            $foundArrGroup = TRUE;
            break;
          }
        }
        if (!$foundArrGroup) {
          array_unshift($groups, [
            'key' => 'arrondissement',
            'label' => (string) $this->t('Arrondissement'),
            'items' => $arrondissementItems,
          ]);
        }
      }

      if ($postalItems !== []) {
        $groups[] = [
          'key' => 'postal_code',
          'label' => (string) $this->t('Code postal'),
          'items' => $postalItems,
        ];
      }
    }

    $response = new JsonResponse([
      'groups' => $groups,
      // Flat list kept for backward compatibility.
      'suggestions' => $suggestions,
    ]);
    $response->setPrivate();
    $response->setMaxAge(60);
    return $response;
  }

  /**
   * Fetches structured location data for multiple cities.
   *
   * Endpoint: /ps-search/location-data?localities[]=Paris&localities[]=Nancy
   */
  public function locationData(Request $request): JsonResponse {
    $localitiesRaw = $request->query->all('localities');
    if (!is_array($localitiesRaw) || empty($localitiesRaw)) {
      return new JsonResponse(['data' => []]);
    }

    $localities = array_slice(array_map(fn($l) => $this->sanitizeText($l), $localitiesRaw), 0, 10);
    $localities = array_filter($localities, fn($l) => $l !== NULL);

    if (empty($localities)) {
      return new JsonResponse(['data' => []]);
    }

    $data = [];
    foreach ($localities as $locality) {
      $select = $this->database->select('node__field_address', 'a');
      $select->fields('a', ['field_address_locality', 'field_address_administrative_area', 'field_address_postal_code']);
      $select->condition('a.field_address_locality', $locality, '=');
      $select->range(0, 1);

      $row = $select->execute()->fetchAssoc();
      if ($row !== FALSE) {
        $postalCode = $this->sanitizeText($row['field_address_postal_code']) ?? '';
        $adminArea = $this->sanitizeText($row['field_address_administrative_area']) ?? '';
        $deptCode = $postalCode ? substr($postalCode, 0, 2) : '';
        $deptName = $adminArea ?: ($deptCode ? $this->getDepartmentName($deptCode) : '');

        $data[] = [
          'label' => $locality,
          'type' => 'city',
          'locality' => $locality,
          'admin_area' => $deptName,
          'postal_code' => $postalCode,
        ];
      } else {
        // Fallback for unknown locality.
        $data[] = [
          'label' => $locality,
          'type' => 'city',
          'locality' => $locality,
          'admin_area' => '',
          'postal_code' => '',
        ];
      }
    }

    $response = new JsonResponse(['data' => $data]);
    $response->setPrivate();
    $response->setMaxAge(60);
    return $response;
  }

  /**
   * Sanitizes a filter code: only A–Z letters, max 10 chars.
   */
  private function sanitizeCode(mixed $value): ?string {
    if (!is_string($value) || $value === '') {
      return NULL;
    }
    $cleaned = preg_replace('/[^A-Z]/i', '', strtoupper(substr($value, 0, 10)));
    return $cleaned !== '' ? $cleaned : NULL;
  }

  /**
   * Sanitizes free text: letters, digits, spaces, hyphens, apostrophes — max 100 chars.
   */
  private function sanitizeText(mixed $value): ?string {
    if (!is_string($value) || trim($value) === '') {
      return NULL;
    }
    $cleaned = preg_replace('/[^\p{L}\p{N}\s\-\']/u', '', substr(trim($value), 0, 100));
    return $cleaned !== '' ? $cleaned : NULL;
  }

  /**
   * Sanitizes a positive numeric value (surface in m², budget in €).
   */
  private function sanitizePositiveNumber(mixed $value, float $max): ?float {
    if ($value === NULL || $value === '') {
      return NULL;
    }
    $num = filter_var($value, FILTER_VALIDATE_FLOAT);
    if ($num === FALSE || $num < 0) {
      return NULL;
    }
    return min((float) $num, $max);
  }

  /**
   * Returns department name from 2-digit code.
   */
  private function getDepartmentName(string $code): ?string {
    $departments = [
      '01' => 'Ain', '02' => 'Aisne', '03' => 'Allier', '04' => 'Alpes-de-Haute-Provence',
      '05' => 'Hautes-Alpes', '06' => 'Alpes-Maritimes', '07' => 'Ardeche', '08' => 'Ardennes',
      '09' => 'Ariege', '10' => 'Aube', '11' => 'Aude', '12' => 'Aveyron',
      '13' => 'Bouches-du-Rhone', '14' => 'Calvados', '15' => 'Cantal', '16' => 'Charente',
      '17' => 'Charente-Maritime', '18' => 'Cher', '19' => 'Correze', '21' => 'Cote-d\'Or',
      '22' => 'Cotes-d\'Armor', '23' => 'Creuse', '24' => 'Dordogne', '25' => 'Doubs',
      '26' => 'Drome', '27' => 'Eure', '28' => 'Eure-et-Loir', '29' => 'Finistere',
      '2A' => 'Corse-du-Sud', '2B' => 'Haute-Corse', '30' => 'Gard', '31' => 'Haute-Garonne',
      '32' => 'Gers', '33' => 'Gironde', '34' => 'Herault', '35' => 'Ille-et-Vilaine',
      '36' => 'Indre', '37' => 'Indre-et-Loire', '38' => 'Isere', '39' => 'Jura',
      '40' => 'Landes', '41' => 'Loir-et-Cher', '42' => 'Loire', '43' => 'Haute-Loire',
      '44' => 'Loire-Atlantique', '45' => 'Loiret', '46' => 'Lot', '47' => 'Lot-et-Garonne',
      '48' => 'Lozere', '49' => 'Maine-et-Loire', '50' => 'Manche', '51' => 'Marne',
      '52' => 'Haute-Marne', '53' => 'Mayenne', '54' => 'Meurthe-et-Moselle', '55' => 'Meuse',
      '56' => 'Morbihan', '57' => 'Moselle', '58' => 'Nievre', '59' => 'Nord',
      '60' => 'Oise', '61' => 'Orne', '62' => 'Pas-de-Calais', '63' => 'Puy-de-Dome',
      '64' => 'Pyrenees-Atlantiques', '65' => 'Hautes-Pyrenees', '66' => 'Pyrenees-Orientales',
      '67' => 'Bas-Rhin', '68' => 'Haut-Rhin', '69' => 'Rhone', '70' => 'Haute-Saone',
      '71' => 'Saone-et-Loire', '72' => 'Sarthe', '73' => 'Savoie', '74' => 'Haute-Savoie',
      '75' => 'Paris', '76' => 'Seine-Maritime', '77' => 'Seine-et-Marne', '78' => 'Yvelines',
      '79' => 'Deux-Sevres', '80' => 'Somme', '81' => 'Tarn', '82' => 'Tarn-et-Garonne',
      '83' => 'Var', '84' => 'Vaucluse', '85' => 'Vendee', '86' => 'Vienne',
      '87' => 'Haute-Vienne', '88' => 'Vosges', '89' => 'Yonne', '90' => 'Territoire-de-Belfort',
      '91' => 'Essonne', '92' => 'Hauts-de-Seine', '93' => 'Seine-Saint-Denis', '94' => 'Val-de-Marne',
      '95' => 'Val-d\'Oise',
    ];
    return $departments[$code] ?? NULL;
  }

  /**
   * Detects and formats arrondissement for Paris, Lyon, Marseille.
   *
   * @return array{label: string, number: int}|null
   */
  private function getArrondissement(string $locality, ?string $postalCode): ?array {
    if ($postalCode === NULL || strlen($postalCode) < 5) {
      return NULL;
    }

    $localityLower = mb_strtolower($locality);
    $prefix = substr($postalCode, 0, 2);
    $suffix = (int) substr($postalCode, 3, 2);

    // Paris: 75001-75020
    if ($localityLower === 'paris' && $prefix === '75' && $suffix >= 1 && $suffix <= 20) {
      $ordinal = $this->formatOrdinal($suffix);
      return [
        'label' => "Paris, {$ordinal} arrondissement ($postalCode)",
        'number' => $suffix,
      ];
    }

    // Lyon: 69001-69009
    if ($localityLower === 'lyon' && $prefix === '69' && $suffix >= 1 && $suffix <= 9) {
      $ordinal = $this->formatOrdinal($suffix);
      return [
        'label' => "Lyon, {$ordinal} arrondissement ($postalCode)",
        'number' => $suffix,
      ];
    }

    // Marseille: 13001-13016
    if ($localityLower === 'marseille' && $prefix === '13' && $suffix >= 1 && $suffix <= 16) {
      $ordinal = $this->formatOrdinal($suffix);
      return [
        'label' => "Marseille, {$ordinal} arrondissement ($postalCode)",
        'number' => $suffix,
      ];
    }

    return NULL;
  }

  /**
   * Formats ordinal numbers (1 -> "1er", 2 -> "2ème").
   */
  private function formatOrdinal(int $number): string {
    return $number === 1 ? '1er' : $number . 'ème';
  }

  /**
   * Searches departments by name prefix.
   *
   * @return array<array{name: string, code: string}>
   */
  private function searchDepartments(string $query): array {
    $departments = [
      '01' => 'Ain', '02' => 'Aisne', '03' => 'Allier', '04' => 'Alpes-de-Haute-Provence',
      '05' => 'Hautes-Alpes', '06' => 'Alpes-Maritimes', '07' => 'Ardeche', '08' => 'Ardennes',
      '09' => 'Ariege', '10' => 'Aube', '11' => 'Aude', '12' => 'Aveyron',
      '13' => 'Bouches-du-Rhone', '14' => 'Calvados', '15' => 'Cantal', '16' => 'Charente',
      '17' => 'Charente-Maritime', '18' => 'Cher', '19' => 'Correze', '21' => 'Cote-d\'Or',
      '22' => 'Cotes-d\'Armor', '23' => 'Creuse', '24' => 'Dordogne', '25' => 'Doubs',
      '26' => 'Drome', '27' => 'Eure', '28' => 'Eure-et-Loir', '29' => 'Finistere',
      '2A' => 'Corse-du-Sud', '2B' => 'Haute-Corse', '30' => 'Gard', '31' => 'Haute-Garonne',
      '32' => 'Gers', '33' => 'Gironde', '34' => 'Herault', '35' => 'Ille-et-Vilaine',
      '36' => 'Indre', '37' => 'Indre-et-Loire', '38' => 'Isere', '39' => 'Jura',
      '40' => 'Landes', '41' => 'Loir-et-Cher', '42' => 'Loire', '43' => 'Haute-Loire',
      '44' => 'Loire-Atlantique', '45' => 'Loiret', '46' => 'Lot', '47' => 'Lot-et-Garonne',
      '48' => 'Lozere', '49' => 'Maine-et-Loire', '50' => 'Manche', '51' => 'Marne',
      '52' => 'Haute-Marne', '53' => 'Mayenne', '54' => 'Meurthe-et-Moselle', '55' => 'Meuse',
      '56' => 'Morbihan', '57' => 'Moselle', '58' => 'Nievre', '59' => 'Nord',
      '60' => 'Oise', '61' => 'Orne', '62' => 'Pas-de-Calais', '63' => 'Puy-de-Dome',
      '64' => 'Pyrenees-Atlantiques', '65' => 'Hautes-Pyrenees', '66' => 'Pyrenees-Orientales',
      '67' => 'Bas-Rhin', '68' => 'Haut-Rhin', '69' => 'Rhone', '70' => 'Haute-Saone',
      '71' => 'Saone-et-Loire', '72' => 'Sarthe', '73' => 'Savoie', '74' => 'Haute-Savoie',
      '75' => 'Paris', '76' => 'Seine-Maritime', '77' => 'Seine-et-Marne', '78' => 'Yvelines',
      '79' => 'Deux-Sevres', '80' => 'Somme', '81' => 'Tarn', '82' => 'Tarn-et-Garonne',
      '83' => 'Var', '84' => 'Vaucluse', '85' => 'Vendee', '86' => 'Vienne',
      '87' => 'Haute-Vienne', '88' => 'Vosges', '89' => 'Yonne', '90' => 'Territoire-de-Belfort',
      '91' => 'Essonne', '92' => 'Hauts-de-Seine', '93' => 'Seine-Saint-Denis', '94' => 'Val-de-Marne',
      '95' => 'Val-d\'Oise',
    ];

    $results = [];
    $queryLower = mb_strtolower($query);
    
    foreach ($departments as $code => $name) {
      if (stripos($name, $query) === 0) {
        $results[] = ['name' => $name, 'code' => $code];
      }
    }
    
    return array_slice($results, 0, 5);
  }

  /**
   * Parses and sanitizes a comma-separated location list.
   *
   * Example: "Paris, Lyon, 75015" => ["Paris", "Lyon", "75015"].
   */
  private function extractLocationTokens(mixed $value): array {
    if (!is_string($value) || trim($value) === '') {
      return [];
    }

    $parts = preg_split('/[,;]+/', $value) ?: [];
    $tokens = [];
    foreach ($parts as $part) {
      $cleaned = $this->sanitizeText($part);
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
   * Applies More criteria filters from the filter bar to a Search API query.
   */
  private function applyMoreCriteriaConditions($query, Request $request): void {
    foreach (['feature_accessibility', 'has_immersive_tour', 'has_video'] as $field) {
      if ($request->query->get($field) === '1') {
        $query->addCondition($field, TRUE);
      }
    }

    foreach (['feature_equipments', 'feature_services', 'feature_building_type'] as $field) {
      $values = array_values(array_filter(array_map(
        fn(mixed $value): ?string => $this->sanitizeFeatureId($value),
        $request->query->all($field),
      )));
      if ($values === []) {
        continue;
      }
      $group = $query->createConditionGroup('OR');
      foreach ($values as $value) {
        $group->addCondition($field, $value);
      }
      $query->addConditionGroup($group);
    }

    $reference = $this->sanitizeText($request->query->get('reference'));
    if ($reference !== NULL) {
      $query->addCondition('field_reference', $reference);
    }

    $transport = $this->sanitizeText($request->query->get('nearby_transport'));
    if ($transport !== NULL) {
      $query->addCondition('nearby_transport', $transport);
    }

    $ceilingMin = $this->sanitizePositiveNumber($request->query->get('ceiling_height_min'), 50.0);
    $ceilingMax = $this->sanitizePositiveNumber($request->query->get('ceiling_height_max'), 50.0);
    if ($ceilingMin !== NULL) {
      $query->addCondition('ceiling_height', $ceilingMin, '>=');
    }
    if ($ceilingMax !== NULL) {
      $query->addCondition('ceiling_height', $ceilingMax, '<=');
    }
  }

  /**
   * Sanitizes a feature definition ID for Search API conditions.
   */
  private function sanitizeFeatureId(mixed $value): ?string {
    if (!is_string($value) || $value === '') {
      return NULL;
    }
    $cleaned = preg_replace('/[^a-z0-9_]+/i', '', $value);
    return $cleaned !== '' ? $cleaned : NULL;
  }

  /**
   * Redirect count to new ps_search_filters endpoint.
   *
   * @deprecated in ps_search:2.0.0 and will be removed in ps_search:3.0.0.
   *   Use ps_search_filters.count route instead.
   */
  public function countRedirect(Request $request): JsonResponse {
    // Forward to new endpoint
    $queryParams = $request->query->all();
    $newUrl = '/ps-search-filters/count?' . http_build_query($queryParams);
    
    // Create subrequest to new endpoint
    $subRequest = Request::create($newUrl, 'GET');
    $response = \Drupal::service('http_kernel')->handle($subRequest, \Symfony\Component\HttpKernel\HttpKernelInterface::SUB_REQUEST);
    
    return JsonResponse::fromJsonString($response->getContent());
  }

  /**
   * Redirect suggest to new ps_location_autocomplete endpoint.
   *
   * @deprecated in ps_search:2.0.0 and will be removed in ps_search:3.0.0.
   *   Use ps_location_autocomplete.suggest route instead.
   */
  public function suggestRedirect(Request $request): JsonResponse {
    // Forward to new endpoint
    $queryParams = $request->query->all();
    $newUrl = '/ps-location/suggest?' . http_build_query($queryParams);
    
    // Create subrequest to new endpoint
    $subRequest = Request::create($newUrl, 'GET');
    $response = \Drupal::service('http_kernel')->handle($subRequest, \Symfony\Component\HttpKernel\HttpKernelInterface::SUB_REQUEST);
    
    return JsonResponse::fromJsonString($response->getContent());
  }

  /**
   * Redirect locationData to new ps_location_autocomplete endpoint.
   *
   * @deprecated in ps_search:2.0.0 and will be removed in ps_search:3.0.0.
   *   Use ps_location_autocomplete.data route instead.
   */
  public function locationDataRedirect(Request $request): JsonResponse {
    // Forward to new endpoint
    $queryParams = $request->query->all();
    $newUrl = '/ps-location/data?' . http_build_query($queryParams);
    
    // Create subrequest to new endpoint
    $subRequest = Request::create($newUrl, 'GET');
    $response = \Drupal::service('http_kernel')->handle($subRequest, \Symfony\Component\HttpKernel\HttpKernelInterface::SUB_REQUEST);
    
    return JsonResponse::fromJsonString($response->getContent());
  }

}
