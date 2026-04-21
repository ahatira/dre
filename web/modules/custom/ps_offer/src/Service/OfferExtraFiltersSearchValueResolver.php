<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\ps_features\Service\FeatureManagerInterface;

/**
 * Resolves additional offer filter values for Search API indexing.
 */
final class OfferExtraFiltersSearchValueResolver {

  /**
   * Feature IDs exposed under accessibility in the UI.
   *
   * @var list<string>
   */
  private const ACCESSIBILITY_FEATURE_IDS = [
    'has_elevator',
    'highly_flexible',
  ];

  /**
   * Features whose values feed building condition filters.
   *
   * @var list<string>
   */
  private const BUILDING_CONDITION_FEATURE_IDS = [
    'building_condition',
    'premises_condition',
  ];

  /**
   * Feature IDs considered as ceiling-height related.
   *
   * @var list<string>
   */
  private const CEILING_HEIGHT_FEATURE_IDS = [
    'ceiling_height',
    'clear_height',
    'storage_height',
    'ceiling_clearance',
  ];

  public function __construct(
    private readonly FeatureManagerInterface $featureManager,
    private readonly ?DictionaryManagerInterface $dictionaryManager = NULL,
  ) {}

  /**
   * Resolves derived values used by Search API.
   *
   * @return array{
   *   has_virtual_tour:bool,
   *   has_video:bool,
   *   accessibility:list<string>,
   *   equipments:list<string>,
   *   services:list<string>,
   *   building_conditions:list<string>,
   *   transport_text:string,
   *   ceiling_height_min:?float,
   *   ceiling_height_max:?float
   *   }
   *   Derived values for indexing.
   */
  public function resolve(NodeInterface $node): array {
    $accessibility = [];
    $equipments = [];
    $services = [];
    $buildingConditions = [];
    $transportTokens = [];
    $ceilingMin = NULL;
    $ceilingMax = NULL;

    if ($node->hasField('field_features') && !$node->get('field_features')->isEmpty()) {
      foreach ($node->get('field_features') as $item) {
        if (!$item instanceof FieldItemInterface) {
          continue;
        }

        $featureId = trim((string) $item->get('feature_id')->getValue());
        if ($featureId === '') {
          continue;
        }

        $feature = $this->featureManager->getFeature($featureId);
        $featureLabel = trim((string) ($feature?->label() ?? $featureId));
        $valueType = (string) ($feature?->getValueType() ?? $item->get('value_type')->getValue() ?? '');
        $group = strtolower(trim((string) ($feature?->getGroup() ?? '')));
        $dictionaryType = $feature?->getDictionaryType();
        $metadata = is_array($feature?->getMetadata()) ? $feature->getMetadata() : [];

        if (!$this->hasIndexableValue($item, $valueType)) {
          continue;
        }

        if ($group === 'equipments') {
          $equipments[] = $featureId;
        }
        if ($group === 'services') {
          $services[] = $featureId;
        }

        if ($this->isAccessibilityFeature($featureId, $featureLabel, $group, $metadata)) {
          $accessibility[] = $featureId;
        }

        if ($group === 'building_condition' || in_array($featureId, self::BUILDING_CONDITION_FEATURE_IDS, TRUE)) {
          $condition = $this->extractSearchValueText($item, $valueType, $dictionaryType);
          if ($condition !== '') {
            $buildingConditions[] = $condition;
          }
        }

        if ($group === 'transport' || str_starts_with($featureId, 'transport_')) {
          $value = $this->extractSearchValueText($item, $valueType, $dictionaryType);
          if ($value !== '') {
            $transportTokens[] = $featureLabel . ' ' . $value;
          }
          else {
            $transportTokens[] = $featureLabel;
          }
        }

        if ($this->isCeilingHeightFeature($featureId, $featureLabel)) {
          [$min, $max] = $this->extractMinMax($item, $valueType);
          if ($min !== NULL && ($ceilingMin === NULL || $min < $ceilingMin)) {
            $ceilingMin = $min;
          }
          if ($max !== NULL && ($ceilingMax === NULL || $max > $ceilingMax)) {
            $ceilingMax = $max;
          }
        }
      }
    }

    return [
      'has_virtual_tour' => $this->hasReferencedItems($node, 'field_media_virtual_tours'),
      'has_video' => $this->hasReferencedItems($node, 'field_media_videos'),
      'accessibility' => array_values(array_unique($accessibility)),
      'equipments' => array_values(array_unique($equipments)),
      'services' => array_values(array_unique($services)),
      'building_conditions' => array_values(array_unique($buildingConditions)),
      'transport_text' => trim(implode(' ', array_unique($transportTokens))),
      'ceiling_height_min' => $ceilingMin,
      'ceiling_height_max' => $ceilingMax,
    ];
  }

  /**
   * Checks whether a node reference field contains at least one item.
   */
  private function hasReferencedItems(NodeInterface $node, string $fieldName): bool {
    return $node->hasField($fieldName) && !$node->get($fieldName)->isEmpty();
  }

  /**
   * Checks whether a feature value should be indexed.
   */
  private function hasIndexableValue(FieldItemInterface $item, string $valueType): bool {
    return match ($valueType) {
      'flag' => TRUE,
      'yesno', 'boolean' => (bool) $item->get('value_boolean')->getValue(),
      'dictionary', 'string' => trim((string) $item->get('value_string')->getValue()) !== '',
      'numeric' => is_numeric($item->get('value_numeric')->getValue()),
      'range' => is_numeric($item->get('value_range_min')->getValue()) || is_numeric($item->get('value_range_max')->getValue()),
      default => trim((string) $item->get('value_string')->getValue()) !== '',
    };
  }

  /**
   * Extracts a textual value suitable for indexing/search.
   */
  private function extractSearchValueText(FieldItemInterface $item, string $valueType, ?string $dictionaryType = NULL): string {
    return match ($valueType) {
      'flag' => '',
      'yesno', 'boolean' => (bool) $item->get('value_boolean')->getValue() ? 'Yes' : '',
      'dictionary' => $this->resolveDictionaryLabel($dictionaryType, trim((string) $item->get('value_string')->getValue())),
      'numeric' => (string) $item->get('value_numeric')->getValue(),
      'range' => trim((string) $item->get('value_range_min')->getValue() . ' ' . (string) $item->get('value_range_max')->getValue()),
      default => trim((string) $item->get('value_string')->getValue()),
    };
  }

  /**
   * Resolves a dictionary code to its label when possible.
   */
  private function resolveDictionaryLabel(?string $dictionaryType, string $code): string {
    if ($code === '' || $dictionaryType === NULL || $dictionaryType === '' || $this->dictionaryManager === NULL) {
      return $code;
    }

    $label = $this->dictionaryManager->getLabel($dictionaryType, $code);
    return $label !== NULL ? trim((string) $label) : $code;
  }

  /**
   * Determines whether a feature contributes to accessibility filters.
   *
   * Accessibility can be declared by metadata,
   * dedicated group, or fallback IDs.
   */
  private function isAccessibilityFeature(
    string $featureId,
    string $featureLabel,
    string $group,
    array $metadata,
  ): bool {
    if ($group === 'transport') {
      return FALSE;
    }

    $category = strtolower(trim((string) (
      $metadata['search_filter_category']
      ?? $metadata['search_category']
      ?? $metadata['category']
      ?? ''
    )));
    if ($category === 'accessibility' || $group === 'accessibility') {
      return TRUE;
    }

    if (!empty($metadata['accessibility'])) {
      return TRUE;
    }

    if (in_array($featureId, self::ACCESSIBILITY_FEATURE_IDS, TRUE)) {
      return TRUE;
    }

    $haystack = strtolower($featureId . ' ' . $featureLabel);
    return str_contains($haystack, 'accessibil')
      || str_contains($haystack, 'elevator')
      || str_contains($haystack, 'ascenseur')
      || str_contains($haystack, 'mobility')
      || str_contains($haystack, 'pmr')
      || str_contains($haystack, 'ramp');
  }

  /**
   * Returns min/max numeric values from numeric or range item.
   *
   * @return array{0:?float,1:?float}
   *   Min/max values.
   */
  private function extractMinMax(FieldItemInterface $item, string $valueType): array {
    if ($valueType === 'numeric') {
      $value = $item->get('value_numeric')->getValue();
      if (!is_numeric($value)) {
        return [NULL, NULL];
      }
      $numeric = (float) $value;
      return [$numeric, $numeric];
    }

    if ($valueType === 'range') {
      $min = $item->get('value_range_min')->getValue();
      $max = $item->get('value_range_max')->getValue();
      return [
        is_numeric($min) ? (float) $min : NULL,
        is_numeric($max) ? (float) $max : NULL,
      ];
    }

    return [NULL, NULL];
  }

  /**
   * Determines whether a feature should feed the ceiling-height range filter.
   */
  private function isCeilingHeightFeature(string $featureId, string $featureLabel): bool {
    if (in_array($featureId, self::CEILING_HEIGHT_FEATURE_IDS, TRUE)) {
      return TRUE;
    }

    $haystack = strtolower($featureId . ' ' . $featureLabel);
    return str_contains($haystack, 'ceiling') || str_contains($haystack, 'height');
  }

}
