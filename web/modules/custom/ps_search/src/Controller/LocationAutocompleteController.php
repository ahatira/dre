<?php

declare(strict_types=1);

namespace Drupal\ps_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns location suggestions for the offer search autocomplete.
 */
final class LocationAutocompleteController extends ControllerBase {

  /**
   * Constructs the controller.
   */
  public function __construct(
    private readonly Connection $database,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('database'),
    );
  }

  /**
   * Provides location suggestions based on offer addresses.
   */
  public function autocomplete(Request $request): JsonResponse {
    $query = trim((string) $request->query->get('q', ''));
    $limit = (int) $request->query->get('limit', 8);
    $limit = max(1, min($limit, 20));

    if (mb_strlen($query) < 2) {
      return new JsonResponse(['items' => []]);
    }

    $like = '%' . $this->database->escapeLike($query) . '%';

    $select = $this->database->select('node__field_address', 'fa');
    $select->join('node_field_data', 'n', 'n.nid = fa.entity_id');
    $select->fields('fa', [
      'field_address_locality',
      'field_address_postal_code',
      'field_address_administrative_area',
      'field_address_country_code',
    ]);
    $select->condition('n.status', 1);
    $select->condition('n.type', 'offer');

    $or = $select->orConditionGroup()
      ->condition('fa.field_address_locality', $like, 'LIKE')
      ->condition('fa.field_address_postal_code', $like, 'LIKE')
      ->condition('fa.field_address_administrative_area', $like, 'LIKE');

    $select->condition($or);
    $select->range(0, 150);

    $rows = $select->execute()->fetchAll();

    $pool = [];
    foreach ($rows as $row) {
      $locality = trim((string) ($row->field_address_locality ?? ''));
      $postal_code = trim((string) ($row->field_address_postal_code ?? ''));
      $administrative_area = trim((string) ($row->field_address_administrative_area ?? ''));

      $this->registerCandidate($pool, $locality, $query);
      $this->registerCandidate($pool, $postal_code, $query);
      $this->registerCandidate($pool, $administrative_area, $query);

      if ($postal_code !== '' && $locality !== '') {
        $this->registerCandidate($pool, $postal_code . ' ' . $locality, $query);
      }
    }

    $items = array_values($pool);

    usort($items, static function (array $a, array $b): int {
      if ($a['score'] !== $b['score']) {
        return $a['score'] <=> $b['score'];
      }
      return strcasecmp($a['label'], $b['label']);
    });

    $items = array_slice($items, 0, $limit);

    return new JsonResponse([
      'items' => array_map(static fn(array $item): array => [
        'value' => $item['value'],
        'label' => $item['label'],
      ], $items),
    ]);
  }

  /**
   * Registers one candidate suggestion if it matches the current query.
   *
   * @param array<string, array{value: string, label: string, score: int}> $pool
   *   Aggregated suggestion pool.
   * @param string $candidate
   *   Candidate value.
   * @param string $needle
   *   User query string.
   */
  private function registerCandidate(array &$pool, string $candidate, string $needle): void {
    $candidate = trim(preg_replace('/\s+/', ' ', $candidate) ?? '');
    if ($candidate === '') {
      return;
    }

    if (mb_stripos($candidate, $needle) === FALSE) {
      return;
    }

    $key = mb_strtolower($candidate);
    if (isset($pool[$key])) {
      return;
    }

    $starts_with = mb_stripos($candidate, $needle) === 0;

    $pool[$key] = [
      'value' => $candidate,
      'label' => $candidate,
      'score' => $starts_with ? 0 : 1,
    ];
  }

}
