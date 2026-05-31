<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\Entity\OfferReferencePatternInterface;

final class OfferReferenceManager implements OfferReferenceManagerInterface {

  private const MAX_SEQUENCE_ATTEMPTS = 99999;

  public function __construct(
    private readonly OfferReferencePatternResolver $patternResolver,
    private readonly OfferReferenceGeneratorInterface $referenceGenerator,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public function buildContextFromNode(NodeInterface $node): array {
    return [
      'field_operation_type' => $this->extractNodeFieldValue($node, 'field_operation_type'),
      'field_asset_type' => $this->extractNodeFieldValue($node, 'field_asset_type'),
    ];
  }

  public function buildContextFromFormValues(array $values): array {
    return [
      'field_operation_type' => $this->extractSubmittedValue($values, 'field_operation_type'),
      'field_asset_type' => $this->extractSubmittedValue($values, 'field_asset_type'),
    ];
  }

  public function generateForBundle(string $bundle, array $context, int $sequence = 1): string {
    $pattern = $this->patternResolver->resolveForBundle($bundle);
    if ($pattern === NULL) {
      return '';
    }

    return trim($this->referenceGenerator->generate($pattern, $context, $sequence));
  }

  public function applyReferenceMode(NodeInterface $node): void {
    if ($node->bundle() !== 'offer' || !$node->hasField('field_reference')) {
      return;
    }

    $auto_mode_enabled = TRUE;
    if ($node->hasField('field_reference_auto')) {
      $raw_mode = $node->get('field_reference_auto')->value;
      $auto_mode_enabled = $this->isAutoModeEnabled(is_scalar($raw_mode) ? (string) $raw_mode : NULL);
    }

    $reference = trim((string) ($node->get('field_reference')->value ?? ''));
    if (!$auto_mode_enabled && $reference !== '') {
      return;
    }

    $pattern = $this->patternResolver->resolveForBundle($node->bundle());
    if ($pattern === NULL) {
      return;
    }

    $generated = $this->generateForNode($node, $pattern, $this->buildContextFromNode($node));
    if ($generated !== '') {
      $node->set('field_reference', $generated);
    }
  }

  private function generateForNode(NodeInterface $node, OfferReferencePatternInterface $pattern, array $context): string {
    if (!$pattern->requiresUniqueness()) {
      return trim($this->referenceGenerator->generate($pattern, $context, 1));
    }

    $node_id = $node->id();
    $exclude_nid = is_scalar($node_id) ? (int) $node_id : 0;

    for ($sequence = 1; $sequence <= self::MAX_SEQUENCE_ATTEMPTS; $sequence++) {
      $candidate = trim($this->referenceGenerator->generate($pattern, $context, $sequence));
      if ($candidate === '') {
        continue;
      }

      if ($this->isReferenceAvailable($node->bundle(), $candidate, $exclude_nid)) {
        return $candidate;
      }
    }

    throw new \RuntimeException('Unable to generate a unique offer reference.');
  }

  private function isReferenceAvailable(string $bundle, string $reference, int $excludeNid = 0): bool {
    $query = $this->entityTypeManager
      ->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', $bundle)
      ->condition('field_reference', $reference)
      ->range(0, 1);

    if ($excludeNid > 0) {
      $query->condition('nid', $excludeNid, '<>');
    }

    $matches = (int) $query->count()->execute();
    return $matches === 0;
  }

  private function isAutoModeEnabled(?string $rawMode): bool {
    if ($rawMode === NULL || $rawMode === '') {
      return TRUE;
    }

    $normalized = mb_strtolower(trim($rawMode));
    return in_array($normalized, ['1', 'true', 'auto', 'on', 'yes'], TRUE);
  }

  private function extractNodeFieldValue(NodeInterface $node, string $fieldName): string {
    if (!$node->hasField($fieldName) || $node->get($fieldName)->isEmpty()) {
      return '';
    }

    $item = $node->get($fieldName)->first();
    if ($item === NULL) {
      return '';
    }

    $value = $item->getValue();
    if (isset($value['value']) && is_scalar($value['value'])) {
      return (string) $value['value'];
    }

    if (isset($value['target_id']) && is_scalar($value['target_id'])) {
      return (string) $value['target_id'];
    }

    return '';
  }

  private function extractSubmittedValue(array $values, string $fieldName): string {
    $field = $values[$fieldName] ?? NULL;

    if (is_array($field)) {
      if (isset($field[0]) && is_scalar($field[0])) {
        return trim((string) $field[0]);
      }
      if (isset($field[0]['value'])) {
        return trim((string) $field[0]['value']);
      }
      if (isset($field[0]['target_id'])) {
        return trim((string) $field[0]['target_id']);
      }
      if (isset($field['value'])) {
        return trim((string) $field['value']);
      }
      if (isset($field['target_id'])) {
        return trim((string) $field['target_id']);
      }
      if (isset($field['widget'][0]['value'])) {
        return trim((string) $field['widget'][0]['value']);
      }
      if (isset($field['widget'][0]['target_id'])) {
        return trim((string) $field['widget'][0]['target_id']);
      }
    }

    return is_scalar($field) ? trim((string) $field) : '';
  }

}