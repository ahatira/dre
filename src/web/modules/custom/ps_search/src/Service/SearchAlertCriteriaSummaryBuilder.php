<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Builds human-readable summaries from stored search criteria.
 */
final class SearchAlertCriteriaSummaryBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly LocationSearchFilter $locationSearchFilter,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Builds a short summary for digest emails and legacy markup.
   *
   * @param array<string, mixed> $criteria
   */
  public function build(array $criteria): string {
    $structured = $this->buildStructured($criteria);
    $parts = array_merge($structured['zones'], $structured['criteria']);

    if ($parts === []) {
      return (string) $this->t('All properties matching your current filters.');
    }

    return implode(' · ', $parts);
  }

  /**
   * Builds structured display data for the alert offcanvas form.
   *
   * @param array<string, mixed> $criteria
   *
   * @return array{
   *   zones: list<string>,
   *   criteria: list<string>,
   *   default_title: string
   *   }
   */
  public function buildStructured(array $criteria): array {
    $zones = $this->buildZones($criteria);
    $tags = $this->buildCriteriaTags($criteria);

    return [
      'zones' => $zones,
      'criteria' => $tags,
      'default_title' => $this->buildDefaultTitle($criteria, $zones),
    ];
  }

  /**
   * @param array<string, mixed> $criteria
   *
   * @return list<string>
   */
  private function buildZones(array $criteria): array {
    $zones = [];

    if ($this->isContextCriteria($criteria)) {
      $label = trim((string) ($criteria['context']['geo']['label'] ?? ''));
      if ($label !== '') {
        $zones[] = $label;
      }
      return $zones;
    }

    if (!empty($criteria['locality']) && is_array($criteria['locality'])) {
      foreach ($criteria['locality'] as $token) {
        $meta = $this->locationSearchFilter->resolveTokenMetadata((string) $token);
        $label = trim((string) ($meta['label'] ?? $token));
        if ($label !== '') {
          $zones[] = $label;
        }
      }
    }
    elseif (!empty($criteria['locations'])) {
      foreach (explode(',', (string) $criteria['locations']) as $part) {
        $label = trim($part);
        if ($label !== '') {
          $zones[] = $label;
        }
      }
    }

    return $zones;
  }

  /**
   * @param array<string, mixed> $criteria
   *
   * @return list<string>
   */
  private function buildCriteriaTags(array $criteria): array {
    $tags = [];

    $operationType = $this->isContextCriteria($criteria)
      ? ($criteria['context']['filters']['operationType'] ?? NULL)
      : ($criteria['operation_type'] ?? NULL);
    $assetType = $this->isContextCriteria($criteria)
      ? ($criteria['context']['filters']['assetType'] ?? NULL)
      : ($criteria['asset_type'] ?? NULL);

    if (!empty($operationType)) {
      $tags[] = $this->dictionaryLabel('operation_type', (string) $operationType);
    }
    if (!empty($assetType)) {
      $tags[] = $this->dictionaryLabel('asset_type', (string) $assetType);
    }

    if ($this->isContextCriteria($criteria)) {
      $this->appendContextRangeTags($tags, $criteria['context']['filters'] ?? []);
      return $tags;
    }

    $this->appendRangeTags($tags, $criteria, 'surface', (string) $this->t('Surface'), ' m²');
    $this->appendRangeTags($tags, $criteria, 'budget', (string) $this->t('Budget'), ' €');
    $this->appendCapacityTags($tags, $criteria);

    return $tags;
  }

  /**
   * @param list<string> $tags
   * @param array<string, mixed> $criteria
   */
  private function appendRangeTags(array &$tags, array $criteria, string $key, string $label, string $unit): void {
    $min = $criteria[sprintf('%s_min', $key)] ?? NULL;
    $max = $criteria[sprintf('%s_max', $key)] ?? NULL;

    if ($min !== NULL && $min !== '') {
      $tags[] = (string) $this->t('Minimum @label: @value@unit', [
        '@label' => mb_strtolower($label),
        '@value' => $min,
        '@unit' => $unit,
      ]);
    }
    if ($max !== NULL && $max !== '') {
      $tags[] = (string) $this->t('Maximum @label: @value@unit', [
        '@label' => mb_strtolower($label),
        '@value' => $max,
        '@unit' => $unit,
      ]);
    }
  }

  /**
   * @param list<string> $tags
   * @param array<string, mixed> $criteria
   */
  private function appendCapacityTags(array &$tags, array $criteria): void {
    $min = $criteria['capacity_min'] ?? NULL;
    $max = $criteria['capacity_max'] ?? NULL;
    if (($min === NULL || $min === '') && ($max === NULL || $max === '')) {
      return;
    }

    $unit = ucfirst((string) ($this->configFactory->get('ps_offer.settings')->get('surface_capacity_unit') ?: 'seats'));
    if ($min !== NULL && $min !== '') {
      $tags[] = (string) $this->t('Minimum @unit: @value', [
        '@unit' => $unit,
        '@value' => $min,
      ]);
    }
    if ($max !== NULL && $max !== '') {
      $tags[] = (string) $this->t('Maximum @unit: @value', [
        '@unit' => $unit,
        '@value' => $max,
      ]);
    }
  }

  /**
   * @param list<string> $zones
   */
  private function buildDefaultTitle(array $criteria, array $zones): string {
    $parts = [];

    $operationType = $this->isContextCriteria($criteria)
      ? ($criteria['context']['filters']['operationType'] ?? NULL)
      : ($criteria['operation_type'] ?? NULL);
    $assetType = $this->isContextCriteria($criteria)
      ? ($criteria['context']['filters']['assetType'] ?? NULL)
      : ($criteria['asset_type'] ?? NULL);

    if (!empty($operationType)) {
      $parts[] = $this->dictionaryLabel('operation_type', (string) $operationType);
    }
    if (!empty($assetType)) {
      $parts[] = $this->dictionaryLabel('asset_type', (string) $assetType);
    }
    if ($zones !== []) {
      $parts[] = implode(', ', $zones);
    }

    if ($parts === []) {
      return (string) $this->t('Property search');
    }

    return implode(', ', $parts);
  }

  /**
   * @param list<string> $tags
   * @param array<string, mixed> $filters
   */
  private function appendContextRangeTags(array &$tags, array $filters): void {
    $this->appendContextRange($tags, $filters['surface'] ?? NULL, (string) $this->t('Surface'), ' m²');
    $this->appendContextRange($tags, $filters['budget'] ?? NULL, (string) $this->t('Budget'), ' €');
    $this->appendCapacityTagsFromValues(
      $tags,
      $filters['capacity']['min'] ?? NULL,
      $filters['capacity']['max'] ?? NULL,
    );
  }

  /**
   * @param list<string> $tags
   * @param array<string, float|null>|null $range
   */
  private function appendContextRange(array &$tags, ?array $range, string $label, string $unit): void {
    if (!is_array($range)) {
      return;
    }

    $min = $range['min'] ?? NULL;
    $max = $range['max'] ?? NULL;
    if ($min !== NULL && $min !== '') {
      $tags[] = (string) $this->t('Minimum @label: @value@unit', [
        '@label' => mb_strtolower($label),
        '@value' => $min,
        '@unit' => $unit,
      ]);
    }
    if ($max !== NULL && $max !== '') {
      $tags[] = (string) $this->t('Maximum @label: @value@unit', [
        '@label' => mb_strtolower($label),
        '@value' => $max,
        '@unit' => $unit,
      ]);
    }
  }

  /**
   * @param list<string> $tags
   */
  private function appendCapacityTagsFromValues(array &$tags, mixed $min, mixed $max): void {
    if (($min === NULL || $min === '') && ($max === NULL || $max === '')) {
      return;
    }

    $unit = ucfirst((string) ($this->configFactory->get('ps_offer.settings')->get('surface_capacity_unit') ?: 'seats'));
    if ($min !== NULL && $min !== '') {
      $tags[] = (string) $this->t('Minimum @unit: @value', [
        '@unit' => $unit,
        '@value' => $min,
      ]);
    }
    if ($max !== NULL && $max !== '') {
      $tags[] = (string) $this->t('Maximum @unit: @value', [
        '@unit' => $unit,
        '@value' => $max,
      ]);
    }
  }

  /**
   * @param array<string, mixed> $criteria
   */
  private function isContextCriteria(array $criteria): bool {
    return (int) ($criteria['schema_version'] ?? 1) >= 2
      && is_array($criteria['context'] ?? NULL);
  }

  /**
   * Loads a dictionary label for a business code.
   */
  private function dictionaryLabel(string $type, string $code): string {
    $storage = $this->entityTypeManager->getStorage('ps_dictionary_entry');
    $entryId = $type . '.' . strtolower($code);
    $entry = $storage->load($entryId);
    return $entry ? (string) $entry->label() : $code;
  }

}
