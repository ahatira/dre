<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Query;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_search\Service\LocationCentroidResolver;
use Drupal\search_api\Query\QueryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Applies BNPPRE list sort options to Search API queries.
 */
final class SearchListSortApplier {

  use StringTranslationTrait;

  public const DEFAULT_SORT_BY = 'surface_total';

  public const DEFAULT_SORT_ORDER = 'ASC';

  public const DISTANCE_SORT_FIELD = 'field_geo_point__distance';

  public const GEO_POINT_FIELD = 'field_geo_point';

  /**
   * Radius (km) used for sort-only spatial queries — avoids geofilt radius 0.
   */
  private const DISTANCE_SORT_RADIUS_KM = '20000';

  public function __construct(
    private readonly LocationCentroidResolver $locationCentroidResolver,
  ) {}

  /**
   * Returns the fixed sort menu options for the results header.
   *
   * @return list<array{value: string, label: string, selected: bool}>
   *   Sort options keyed for Twig rendering.
   */
  public function buildOptions(?string $currentSortBy, ?string $currentSortOrder): array {
    [$sortBy, $sortOrder] = $this->resolveSortParameters($currentSortBy, $currentSortOrder);
    $options = [];

    foreach ($this->getDefinitions() as $definition) {
      $options[] = [
        'value' => $definition['sort_by'] . '|' . $definition['sort_order'],
        'label' => (string) $definition['label'],
        'selected' => $definition['sort_by'] === $sortBy && $definition['sort_order'] === $sortOrder,
      ];
    }

    return $options;
  }

  /**
   * Returns the label of the active sort for the dropdown trigger.
   */
  public function buildSelectedLabel(?string $currentSortBy, ?string $currentSortOrder): string {
    [$sortBy, $sortOrder] = $this->resolveSortParameters($currentSortBy, $currentSortOrder);

    foreach ($this->getDefinitions() as $definition) {
      if ($definition['sort_by'] === $sortBy && $definition['sort_order'] === $sortOrder) {
        return (string) $definition['label'];
      }
    }

    return (string) $this->t('Increasing surface');
  }

  /**
   * Applies list sort parameters from the request to a Search API query.
   */
  public function apply(QueryInterface $query, Request $request): void {
    [$sortBy, $sortOrder] = $this->resolveSortParameters(
      is_string($request->query->get('sort_by')) ? (string) $request->query->get('sort_by') : NULL,
      is_string($request->query->get('sort_order')) || is_numeric($request->query->get('sort_order'))
        ? (string) $request->query->get('sort_order')
        : NULL,
    );

    if ($sortBy === self::DISTANCE_SORT_FIELD) {
      $origin = $this->resolveSortOrigin($request);
      if ($origin === NULL) {
        $query->sort(self::DEFAULT_SORT_BY, self::DEFAULT_SORT_ORDER);
        return;
      }

      $query->setOption('search_api_location', [
        [
          'field' => self::GEO_POINT_FIELD,
          'lat' => (string) $origin['lat'],
          'lon' => (string) $origin['lng'],
          'radius' => self::DISTANCE_SORT_RADIUS_KM,
        ],
      ]);
      $query->sort(self::DISTANCE_SORT_FIELD, $sortOrder);
      return;
    }

    $query->sort($sortBy, $sortOrder);
  }

  /**
   * Returns the five BNPPRE sort menu definitions.
   *
   * @return list<array{sort_by: string, sort_order: string, label: \Drupal\Core\StringTranslation\TranslatableMarkup}>
   *   Sort definitions in display order.
   */
  private function getDefinitions(): array {
    return [
      [
        'sort_by' => 'surface_total',
        'sort_order' => 'ASC',
        'label' => $this->t('Increasing surface'),
      ],
      [
        'sort_by' => 'surface_total',
        'sort_order' => 'DESC',
        'label' => $this->t('Decreasing surface'),
      ],
      [
        'sort_by' => self::DISTANCE_SORT_FIELD,
        'sort_order' => 'ASC',
        'label' => $this->t('Decreasing distance'),
      ],
      [
        'sort_by' => 'field_budget_value',
        'sort_order' => 'ASC',
        'label' => $this->t('Increasing price'),
      ],
      [
        'sort_by' => 'field_budget_value',
        'sort_order' => 'DESC',
        'label' => $this->t('Decreasing price'),
      ],
    ];
  }

  /**
   * Normalizes sort field and order from the request.
   *
   * @return array{0: string, 1: string}
   *   Normalized sort field and order.
   */
  private function resolveSortParameters(?string $sortBy, ?string $sortOrder): array {
    $allowed = [];
    foreach ($this->getDefinitions() as $definition) {
      $allowed[$definition['sort_by'] . '|' . $definition['sort_order']] = [
        $definition['sort_by'],
        $definition['sort_order'],
      ];
    }

    $order = strtoupper((string) ($sortOrder ?? self::DEFAULT_SORT_ORDER));
    if (!in_array($order, ['ASC', 'DESC'], TRUE)) {
      $order = self::DEFAULT_SORT_ORDER;
    }

    $field = is_string($sortBy) ? trim($sortBy) : '';
    if ($field !== '' && isset($allowed[$field . '|' . $order])) {
      return [$field, $order];
    }

    if ($field !== '') {
      foreach ($allowed as [$allowedField, $allowedOrder]) {
        if ($allowedField === $field) {
          return [$allowedField, $allowedOrder];
        }
      }
    }

    return [self::DEFAULT_SORT_BY, self::DEFAULT_SORT_ORDER];
  }

  /**
   * Resolves the map center used for distance sorting.
   *
   * @return array{lat: float, lng: float}|null
   *   Origin coordinates for distance sorting.
   */
  private function resolveSortOrigin(Request $request): ?array {
    $locationMap = $this->locationCentroidResolver->resolveFromRequest($request);
    if (is_array($locationMap) && is_numeric($locationMap['lat'] ?? NULL) && is_numeric($locationMap['lng'] ?? NULL)) {
      return [
        'lat' => (float) $locationMap['lat'],
        'lng' => (float) $locationMap['lng'],
      ];
    }

    return NULL;
  }

}
