<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\NodeInterface;

final class OfferValidationManager implements OfferValidationManagerInterface {

  public function __construct(
    private readonly MessengerInterface $messenger,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public function apply(NodeInterface $node): void {
    if ($node->bundle() !== 'offer') {
      return;
    }

    // Skip validations when saving translations to avoid false positives
    // on non-translatable fields inherited from the source language.
    if ($this->isTranslationContext($node)) {
      return;
    }

    $this->validateBudget($node);
    $this->validateCapacity($node);
    $this->validateSurface($node);
    $this->validateDivisibility($node);
    $this->validatePrimaryAgent($node);
    $this->validateManualReferenceUniqueness($node);
  }

  /**
   * Check if the current save operation is in a translation context.
   *
   * When saving a translation, non-translatable fields are inherited from
   * the source language. Validations on these fields would incorrectly fail,
   * so we skip all validations during translation saves.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node being saved.
   *
   * @return bool
   *   TRUE if saving a translation (non-default language), FALSE otherwise.
   */
  private function isTranslationContext(NodeInterface $node): bool {
    // New nodes are never translations
    if ($node->isNew()) {
      return FALSE;
    }

    // Check if we're working on a non-default language version
    $langcode = $node->language()->getId();
    $default_langcode = $node->getUntranslated()->language()->getId();

    // If language differs from default, it's a translation
    return $langcode !== $default_langcode;
  }

  private function validateManualReferenceUniqueness(NodeInterface $offer): void {
    if (!$offer->hasField('field_reference') || !$offer->hasField('field_reference_auto')) {
      return;
    }

    $auto_mode = mb_strtolower((string) ($this->fieldValue($offer->get('field_reference_auto')) ?? '1'));
    if (in_array($auto_mode, ['1', 'true', 'auto', 'on', 'yes'], TRUE)) {
      return;
    }

    $manual_reference = trim((string) ($this->fieldValue($offer->get('field_reference')) ?? ''));
    if ($manual_reference === '') {
      return;
    }

    $query = $this->entityTypeManager
      ->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'offer')
      ->condition('field_reference', $manual_reference)
      ->range(0, 1);

    $offer_id = $offer->id();
    if (is_scalar($offer_id) && (int) $offer_id > 0) {
      $query->condition('nid', (int) $offer_id, '<>');
    }

    if ((int) $query->count()->execute() > 0) {
      $message = 'Manual reference value is already used by another offer.';
      $this->messenger->addError(new TranslatableMarkup($message));
      throw new EntityStorageException($message);
    }
  }

  private function validateBudget(NodeInterface $offer): void {
    if (!$offer->hasField('field_budget_period') || !$offer->hasField('field_budget_value')) {
      return;
    }

    $period = trim((string) ($this->fieldValue($offer->get('field_budget_period')) ?? ''));
    $raw_value = trim((string) ($this->fieldValue($offer->get('field_budget_value')) ?? ''));

    // Import normalization rule:
    // - price 0 (or invalid) becomes NULL
    // - when price is NULL, period/unit are forced to NULL as well.
    if ($raw_value === '') {
      if ($period !== '') {
        $offer->set('field_budget_period', NULL);
      }
      if ($offer->hasField('field_budget_unit')) {
        $offer->set('field_budget_unit', NULL);
      }
      return;
    }

    $normalized_value = str_replace(',', '.', $raw_value);
    if (!is_numeric($normalized_value) || (float) $normalized_value <= 0.0) {
      $offer->set('field_budget_value', NULL);
      if ($period !== '') {
        $offer->set('field_budget_period', NULL);
      }
      if ($offer->hasField('field_budget_unit')) {
        $offer->set('field_budget_unit', NULL);
      }
    }
  }

  private function validatePrimaryAgent(NodeInterface $offer): void {
    if (!$offer->isPublished() || !$offer->hasField('field_primary_agent')) {
      return;
    }

    if ($offer->get('field_primary_agent')->isEmpty()) {
      $offer->setUnpublished();
      $this->messenger->addWarning(new TranslatableMarkup('The offer has been saved as a draft because no primary agent is set.'));
    }
  }

  private function validateCapacity(NodeInterface $offer): void {
    if (
      !$offer->hasField('field_capacity_mode') ||
      !$offer->hasField('field_capacity_total') ||
      !$offer->hasField('field_capacity_available')
    ) {
      return;
    }

    $mode = mb_strtoupper((string) ($this->fieldValue($offer->get('field_capacity_mode')) ?? ''));
    $total_raw = $this->fieldValue($offer->get('field_capacity_total'));
    $available_raw = $this->fieldValue($offer->get('field_capacity_available'));

    $has_total = $total_raw !== NULL && $total_raw !== '';
    $total = $has_total ? (int) $total_raw : NULL;
    $has_available = $available_raw !== NULL && $available_raw !== '';
    $available = $has_available ? (int) $available_raw : NULL;

    if ($mode === 'SEAT_BASED' && (!$has_total || $total <= 0)) {
      $this->raiseValidationIssue($offer, 'Capacity total must be greater than 0 for seat-based offers.');
      return;
    }

    if ($has_available) {
      if ($available < 0) {
        $this->raiseValidationIssue($offer, 'Capacity available cannot be negative.');
        return;
      }

      if (!$has_total || $total < 0 || $available > $total) {
        $this->raiseValidationIssue($offer, 'Capacity available must be lower than or equal to capacity total.');
        return;
      }
    }

    if ($offer->hasField('field_budget_unit')) {
      $budget_unit = mb_strtoupper((string) ($this->fieldValue($offer->get('field_budget_unit')) ?? ''));
      if ($budget_unit === 'PER_POSTE' && (!$has_total || $total <= 0)) {
        $this->raiseValidationIssue($offer, 'Capacity total must be greater than 0 when budget unit is PER_POSTE.');
      }
    }
  }


  private function validateSurface(NodeInterface $offer): void {
    if (!$offer->hasField('field_surfaces')) {
      return;
    }

    // Check if at least one TOTAL surface exists and > 0
    $has_valid_total = FALSE;
    foreach ($offer->get('field_surfaces') as $item) {
      if ($item->qualification === 'TOTAL' && !empty($item->value) && (float) $item->value > 0.0) {
        $has_valid_total = TRUE;
        break;
      }
    }

    if (!$has_valid_total) {
      $this->raiseValidationIssue($offer, 'At least one TOTAL surface with value greater than 0 is required.');
    }
  }

  private function validateDivisibility(NodeInterface $offer): void {
    if (!$offer->hasField('field_divisible') || !$offer->hasField('field_surfaces')) {
      return;
    }

    $is_divisible = (bool) ($this->fieldValue($offer->get('field_divisible')) ?? FALSE);
    
    // Find TOTAL and DISPO surfaces
    $total_surface = NULL;
    $dispo_surface = NULL;
    
    foreach ($offer->get('field_surfaces') as $item) {
      if ($item->qualification === 'TOTAL' && !empty($item->value)) {
        $total_surface = (float) $item->value;
      }
      if ($item->qualification === 'DISPO' && !empty($item->value)) {
        $dispo_surface = (float) $item->value;
      }
    }

    // If not divisible but DISPO < TOTAL, show warning
    if (!$is_divisible && $total_surface !== NULL && $dispo_surface !== NULL) {
      if ($dispo_surface < $total_surface && $dispo_surface > 0) {
        $this->messenger->addWarning(new TranslatableMarkup(
          'The offer is marked as non-divisible, but available surface (@dispo m²) is less than total surface (@total m²). Consider marking it as divisible.',
          ['@dispo' => $dispo_surface, '@total' => $total_surface]
        ));
      }
    }
  }

  private function raiseValidationIssue(NodeInterface $offer, string $message): void {
    if ($offer->isPublished()) {
      $this->messenger->addError(new TranslatableMarkup($message));
      throw new EntityStorageException($message);
    }

    $this->messenger->addWarning(new TranslatableMarkup($message));
  }

  private function fieldValue(FieldItemListInterface $items): ?string {
    if ($items->isEmpty()) {
      return NULL;
    }

    $item = $items->first();
    if ($item === NULL) {
      return NULL;
    }

    $value = $item->getValue();
    return isset($value['value']) ? (string) $value['value'] : NULL;
  }

}
