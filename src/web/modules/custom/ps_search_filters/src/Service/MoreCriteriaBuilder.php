<?php

declare(strict_types=1);

namespace Drupal\ps_search_filters\Service;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_search\Service\FeatureSearchFilterRegistry;

/**
 * Builds grouped "More criteria" options for the search filter bar.
 */
final class MoreCriteriaBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly FeatureSearchFilterRegistry $featureFilterRegistry,
  ) {}

  /**
   * Returns accordion group summaries (lazy-load friendly).
   *
   * @return array<string, array<string, mixed>>
   */
  public function buildSummaries(?string $activeAsset = NULL): array {
    $groups = $this->featureFilterRegistry->buildGroupSummaries($activeAsset);
    foreach ($groups as $groupId => $group) {
      $groups[$groupId]['lazy'] = TRUE;
    }
    $groups['core'] = $this->buildCoreGroup();
    return $groups;
  }

  /**
   * Returns full items for one group (AJAX lazy-load).
   *
   * @return list<array<string, mixed>>
   */
  public function getGroupItems(string $groupId, ?string $activeAsset = NULL): array {
    if ($groupId === 'core') {
      return $this->buildCoreGroup()['items'];
    }
    return $this->featureFilterRegistry->getGroupItems($groupId, $activeAsset);
  }

  /**
   * Builds filter schema for drupalSettings (param / widget / field).
   *
   * @return list<array<string, mixed>>
   */
  public function buildFilterSchema(?string $activeAsset = NULL): array {
    $schema = $this->featureFilterRegistry->buildFilterSchema($activeAsset);
    foreach ($this->buildCoreGroup()['items'] as $item) {
      $schema[] = [
        'param' => $item['param'],
        'field' => $item['field'],
        'widget' => $item['widget'],
      ];
    }
    return $schema;
  }

  /**
   * Core More-filters not driven by the feature catalogue.
   *
   * @return array<string, mixed>
   */
  private function buildCoreGroup(): array {
    return [
      'id' => 'core',
      'label' => (string) $this->t('Other criteria'),
      'weight' => 10000,
      'lazy' => FALSE,
      'item_count' => 5,
      'items' => [
        [
          'id' => 'nearby_transport',
          'label' => (string) $this->t('Nearby transport'),
          'widget' => 'text',
          'param' => 'nearby_transport',
          'field' => 'nearby_transport',
        ],
        [
          'id' => 'reference',
          'label' => (string) $this->t('Reference'),
          'widget' => 'text',
          'param' => 'reference',
          'field' => 'field_reference',
        ],
        [
          'id' => 'ceiling_height',
          'label' => (string) $this->t('Ceiling height (m)'),
          'widget' => 'range',
          'param' => 'ceiling_height',
          'field' => 'ceiling_height',
          'unit' => 'm',
          'step' => '0.5',
        ],
        [
          'id' => 'has_immersive_tour',
          'label' => (string) $this->t('Immersive tour'),
          'widget' => 'checkbox',
          'param' => 'has_immersive_tour',
          'field' => 'has_immersive_tour',
        ],
        [
          'id' => 'has_video',
          'label' => (string) $this->t('Video'),
          'widget' => 'checkbox',
          'param' => 'has_video',
          'field' => 'has_video',
        ],
      ],
    ];
  }

}
