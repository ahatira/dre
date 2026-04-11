<?php

declare(strict_types=1);

namespace Drupal\ps_features\Service;

use Drupal\ps_features\Entity\FeatureInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Builds ordered comparison sections for features.
 *
 * @see \Drupal\ps_features\Service\CompareBuilderInterface
 * @see docs/specs/04-ps-features.md#feature-comparison
 */
final class CompareBuilder implements CompareBuilderInterface {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly FeatureManagerInterface $featureManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function build(array $features): array {
    // Index features by group.
    $grouped = [];
    foreach ($features as $feature) {
      $group = $feature->getGroup() ?: 'other';
      $grouped[$group][] = $feature;
    }

    // Sort features inside groups by weight.
    foreach ($grouped as &$items) {
      usort($items, static fn (FeatureInterface $a, FeatureInterface $b): int => $a->getWeight() <=> $b->getWeight());
    }
    unset($items);

    $sections = [];
    $ordered = $this->getSections();
    foreach ($ordered as $section) {
      if (!empty($grouped[$section])) {
        $sections[$section] = [
          'label' => $this->formatSectionLabel($section),
          'features' => $grouped[$section],
        ];
        unset($grouped[$section]);
      }
    }

    // Append remaining groups as "other" or their own label.
    foreach ($grouped as $group => $items) {
      $code = $group === 'other' ? 'other' : $group;
      $sections[$code] = [
        'label' => $this->formatSectionLabel($group),
        'features' => $items,
      ];
    }

    return $sections;
  }

  /**
   * {@inheritdoc}
   */
  public function getSections(): array {
    $config = $this->configFactory->get('ps_features.settings');
    $sections = $config->get('compare_sections') ?? [];
    // Normalize codes (trim + lowercase).
    $normalized = [];
    foreach ($sections as $code) {
      $code = strtolower(trim((string) $code));
      if ($code !== '') {
        $normalized[] = $code;
      }
    }
    return $normalized;
  }

  /**
   * Formats section code to a human readable label.
   */
  private function formatSectionLabel(string $code): string {
    if ($code === 'other') {
      return 'Other';
    }
    return ucwords(str_replace('_', ' ', $code));
  }

}
