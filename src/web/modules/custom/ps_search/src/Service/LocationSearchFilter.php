<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Database\Connection;
use Drupal\search_api\Query\QueryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Applies unified location token filtering to Search API queries.
 *
 * Each token matches with OR across city, postal code and department fields.
 * Department codes (2 digits) also match postal prefixes.
 */
final class LocationSearchFilter {

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
  ) {}

  /**
   * Extracts location tokens from a request (comma string or locality[] array).
   *
   * @return list<string>
   */
  public function extractTokensFromRequest(Request $request): array {
    $raw = $request->query->all()['locality'] ?? NULL;
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
      $tokenGroup = $query->createConditionGroup('OR');
      foreach (self::ADDRESS_FIELDS as $field) {
        $tokenGroup->addCondition($field, $token);
      }

      if (preg_match('/^\d{2}$/', $token) === 1) {
        $deptName = $this->getDepartmentName($token);
        if ($deptName !== '') {
          $tokenGroup->addCondition('field_address_admin_area', $deptName);
        }
      }

      $rootGroup->addConditionGroup($tokenGroup);
    }

    $query->addConditionGroup($rootGroup);
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

    if (preg_match('/^\d{5}$/', $token) === 1) {
      $meta = $this->resolvePostalToken($token, $meta);
    }
    elseif (preg_match('/^\d{2}$/', $token) === 1) {
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
    $meta['label'] = $deptName !== '' ? "$deptName ($token)" : $token;

    return $meta;
  }

  /**
   * @param array<string, mixed> $meta
   *
   * @return array<string, mixed>
   */
  private function resolveLocalityToken(string $token, array $meta): array {
    $select = $this->database->select('node__field_address', 'a');
    $select->fields('a', ['field_address_locality', 'field_address_administrative_area']);
    $or = $select->orConditionGroup()
      ->condition('a.field_address_locality', $token)
      ->condition('a.field_address_administrative_area', $token);
    $select->condition($or);
    $select->range(0, 1);
    $row = $select->execute()->fetchAssoc();

    if ($row !== FALSE) {
      $locality = $this->sanitizeText($row['field_address_locality']) ?? $token;
      $adminArea = $this->sanitizeText($row['field_address_administrative_area']) ?? '';
      $meta['locality'] = $locality;
      $meta['admin_area'] = $adminArea;
      $meta['label'] = $locality;
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

    if (preg_match('/^\d{5}$/', $token) === 1) {
      $select->condition('a.field_address_postal_code', $token);
    }
    elseif (preg_match('/^\d{2}$/', $token) === 1) {
      $select->condition('a.field_address_postal_code', $token . '%', 'LIKE');
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
   * Returns department name from a 2-digit code.
   */
  private function getDepartmentName(string $code): string {
    $departments = [
      '01' => 'Ain',
      '02' => 'Aisne',
      '03' => 'Allier',
      '04' => 'Alpes-de-Haute-Provence',
      '05' => 'Hautes-Alpes',
      '06' => 'Alpes-Maritimes',
      '07' => 'Ardèche',
      '08' => 'Ardennes',
      '09' => 'Ariège',
      '10' => 'Aube',
      '11' => 'Aude',
      '12' => 'Aveyron',
      '13' => 'Bouches-du-Rhône',
      '14' => 'Calvados',
      '15' => 'Cantal',
      '16' => 'Charente',
      '17' => 'Charente-Maritime',
      '18' => 'Cher',
      '19' => 'Corrèze',
      '21' => 'Côte-d\'Or',
      '22' => 'Côtes-d\'Armor',
      '23' => 'Creuse',
      '24' => 'Dordogne',
      '25' => 'Doubs',
      '26' => 'Drôme',
      '27' => 'Eure',
      '28' => 'Eure-et-Loir',
      '29' => 'Finistère',
      '30' => 'Gard',
      '31' => 'Haute-Garonne',
      '32' => 'Gers',
      '33' => 'Gironde',
      '34' => 'Hérault',
      '35' => 'Ille-et-Vilaine',
      '36' => 'Indre',
      '37' => 'Indre-et-Loire',
      '38' => 'Isère',
      '39' => 'Jura',
      '40' => 'Landes',
      '41' => 'Loir-et-Cher',
      '42' => 'Loire',
      '43' => 'Haute-Loire',
      '44' => 'Loire-Atlantique',
      '45' => 'Loiret',
      '46' => 'Lot',
      '47' => 'Lot-et-Garonne',
      '48' => 'Lozère',
      '49' => 'Maine-et-Loire',
      '50' => 'Manche',
      '51' => 'Marne',
      '52' => 'Haute-Marne',
      '53' => 'Mayenne',
      '54' => 'Meurthe-et-Moselle',
      '55' => 'Meuse',
      '56' => 'Morbihan',
      '57' => 'Moselle',
      '58' => 'Nièvre',
      '59' => 'Nord',
      '60' => 'Oise',
      '61' => 'Orne',
      '62' => 'Pas-de-Calais',
      '63' => 'Puy-de-Dôme',
      '64' => 'Pyrénées-Atlantiques',
      '65' => 'Hautes-Pyrénées',
      '66' => 'Pyrénées-Orientales',
      '67' => 'Bas-Rhin',
      '68' => 'Haut-Rhin',
      '69' => 'Rhône',
      '70' => 'Haute-Saône',
      '71' => 'Saône-et-Loire',
      '72' => 'Sarthe',
      '73' => 'Savoie',
      '74' => 'Haute-Savoie',
      '75' => 'Paris',
      '76' => 'Seine-Maritime',
      '77' => 'Seine-et-Marne',
      '78' => 'Yvelines',
      '79' => 'Deux-Sèvres',
      '80' => 'Somme',
      '81' => 'Tarn',
      '82' => 'Tarn-et-Garonne',
      '83' => 'Var',
      '84' => 'Vaucluse',
      '85' => 'Vendée',
      '86' => 'Vienne',
      '87' => 'Haute-Vienne',
      '88' => 'Vosges',
      '89' => 'Yonne',
      '90' => 'Territoire de Belfort',
      '91' => 'Essonne',
      '92' => 'Hauts-de-Seine',
      '93' => 'Seine-Saint-Denis',
      '94' => 'Val-de-Marne',
      '95' => 'Val-d\'Oise',
    ];

    return $departments[$code] ?? '';
  }

  /**
   * Sanitizes free text input.
   */
  private function sanitizeText(mixed $value): ?string {
    if (!is_string($value) || trim($value) === '') {
      return NULL;
    }
    $cleaned = preg_replace('/[^\p{L}\p{N}\s\-\']/u', '', substr(trim($value), 0, 100));
    return $cleaned !== '' ? $cleaned : NULL;
  }

}
