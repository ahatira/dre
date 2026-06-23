<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_feature\Entity\FeatureDefinition;

/**
 * Builds grouped autocomplete suggestions for the Nearby transport filter.
 */
final class TransportSuggestBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly TransportFeatureSearchTextBuilder $transportTextBuilder,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly Connection $database,
  ) {}

  /**
   * @return array{groups: list<array<string, mixed>>}
   */
  public function build(string $query, int $limit = 8): array {
    $limit = max(1, min($limit, 15));
    $needle = mb_strtolower(trim($query));
    if (mb_strlen($needle) < 2) {
      return ['groups' => []];
    }

    $definitions = $this->loadTransportDefinitions();
    if ($definitions === []) {
      return ['groups' => []];
    }

    $nameItems = [];
    $valueItems = [];
    $seen = [];

    foreach ($definitions as $definition) {
      $label = trim((string) $definition->label());
      if ($label === '') {
        continue;
      }

      $code = mb_strtolower(trim((string) $definition->getCode()));
        if ($this->containsNeedle($label, $needle) || ($code !== '' && str_contains($code, $needle))) {
        $key = 'name:' . mb_strtolower($label);
        if (!isset($seen[$key])) {
          $seen[$key] = TRUE;
          $nameItems[] = [
            'label' => $label,
            'value' => $label,
            'type' => 'feature_name',
            'definition_id' => (string) $definition->id(),
          ];
        }
      }
    }

    $this->appendValueSuggestions($definitions, $needle, $limit, $valueItems, $seen);

    $groups = [];
    if ($nameItems !== []) {
      $groups[] = [
        'key' => 'feature_name',
        'label' => (string) $this->t('Transport type'),
        'items' => array_slice($nameItems, 0, $limit),
      ];
    }
    if ($valueItems !== []) {
      $groups[] = [
        'key' => 'feature_value',
        'label' => (string) $this->t('Lines & details'),
        'items' => array_slice($valueItems, 0, $limit),
      ];
    }

    return ['groups' => $groups];
  }

  /**
   * @return array<string, FeatureDefinition>
   */
  private function loadTransportDefinitions(): array {
    $groupId = $this->transportTextBuilder->getTransportGroupId();
    $ids = $this->entityTypeManager->getStorage('fb_feature_definition')->getQuery()
      ->condition('group', $groupId)
      ->condition('status', TRUE)
      ->sort('weight')
      ->accessCheck(FALSE)
      ->execute();

    if ($ids === []) {
      return [];
    }

    return $this->entityTypeManager->getStorage('fb_feature_definition')->loadMultiple($ids);
  }

  /**
   * @param array<string, FeatureDefinition> $definitions
   * @param list<array<string, mixed>> $valueItems
   * @param array<string, true> $seen
   */
  private function appendValueSuggestions(
    array $definitions,
    string $needle,
    int $limit,
    array &$valueItems,
    array &$seen,
  ): void {
    $definitionIds = array_keys($definitions);
    if ($definitionIds === []) {
      return;
    }

    $select = $this->database->select('node__field_features', 'f');
    $select->join('node_field_data', 'n', 'n.nid = f.entity_id AND n.vid = f.revision_id');
    $select->fields('f', ['field_features_feature_definition_id', 'field_features_payload']);
    $select->condition('f.field_features_feature_definition_id', $definitionIds, 'IN');
    $select->condition('n.status', 1);
    $select->range(0, 400);

    foreach ($select->execute() as $row) {
      if (count($valueItems) >= $limit) {
        break;
      }

      $definitionId = (string) $row->field_features_feature_definition_id;
      $definition = $definitions[$definitionId] ?? NULL;
      if (!$definition instanceof FeatureDefinition) {
        continue;
      }

      $payload = json_decode((string) $row->field_features_payload, TRUE);
      if (!is_array($payload)) {
        $payload = [];
      }

      $typeDriver = (string) $definition->getTypeDriver();
      if ($this->transportTextBuilder->shouldSkipIndexedFeature($typeDriver, $payload)) {
        continue;
      }

      $label = trim((string) $definition->label());
      $formatted = $this->transportTextBuilder->formatPayloadValue($typeDriver, $payload);
      $snippet = $this->transportTextBuilder->formatFeatureSnippet($definition, $typeDriver, $payload);

      if (!$this->containsNeedle($snippet, $needle) && !$this->containsNeedle($formatted, $needle)) {
        continue;
      }

      $displayLabel = $typeDriver === 'flag' || $formatted === ''
        ? $label
        : $label . ': ' . $formatted;
      $searchValue = $typeDriver === 'flag' || $formatted === ''
        ? $label
        : $formatted;

      $key = 'value:' . mb_strtolower($displayLabel);
      if (isset($seen[$key])) {
        continue;
      }
      $seen[$key] = TRUE;

      $valueItems[] = [
        'label' => $displayLabel,
        'value' => $searchValue,
        'type' => 'feature_value',
        'definition_id' => $definitionId,
      ];
    }
  }

  private function containsNeedle(string $text, string $needle): bool {
    return str_contains(mb_strtolower(trim($text)), $needle);
  }

}
