<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\Service\OfferReferenceBuilder;

/**
 * Hooks for Offer nodes.
 */
class OfferHooks {

  /**
   * Constructs the OfferHooks object.
   */
  public function __construct(
    protected LoggerChannelFactoryInterface $loggerFactory,
    protected OfferReferenceBuilder $referenceBuilder,
    protected MessengerInterface $messenger,
  ) {}

  /**
   * Implements hook_node_presave() for offer nodes.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node entity.
   *
   * @hook node_presave
   */
  #[Hook('node_presave')]
  public function nodePresave(NodeInterface $node): void {
    if ($node->getType() !== 'offer') {
      return;
    }

    $this->ensureReference($node);
    $this->ensureMinDivisibleSurface($node);

    $this->loggerFactory->get('ps_offer')->debug(
      'Offer node @id presave triggered with reference @reference',
      [
        '@id' => $node->id() ?? 'new',
        '@reference' => (string) ($node->get('field_reference')->value ?? 'pending'),
      ],
    );
  }

  /**
   * Implements hook_node_insert() for offer nodes.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node entity.
   *
   * @hook node_insert
   */
  #[Hook('node_insert')]
  public function nodeInsert(NodeInterface $node): void {
    if ($node->getType() !== 'offer') {
      return;
    }

    $this->loggerFactory->get('ps_offer')->info(
      'Offer node @id created',
      ['@id' => $node->id()],
    );
  }

  /**
   * Implements hook_node_update() for offer nodes.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node entity.
   *
   * @hook node_update
   */
  #[Hook('node_update')]
  public function nodeUpdate(NodeInterface $node): void {
    if ($node->getType() !== 'offer') {
      return;
    }

    $this->loggerFactory->get('ps_offer')->info(
      'Offer node @id updated',
      ['@id' => $node->id()],
    );
  }

  /**
   * Implements hook_node_delete() for offer nodes.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node entity.
   *
   * @hook node_delete
   */
  #[Hook('node_delete')]
  public function nodeDelete(NodeInterface $node): void {
    if ($node->getType() !== 'offer') {
      return;
    }

    $this->loggerFactory->get('ps_offer')->info(
      'Offer node @id deleted',
      ['@id' => $node->id()],
    );
  }

  /**
   * Ensures the offer reference follows the required 12-character format.
   */
  protected function ensureReference(NodeInterface $node): void {
    if (!$node->hasField('field_reference')) {
      return;
    }

    // Once persisted, offer references are immutable. Keep original value even
    // if a submitted form/client attempts to change it.
    if (!$node->isNew() && isset($node->original) && $node->original instanceof NodeInterface && $node->original->hasField('field_reference')) {
      $original_reference = strtoupper(trim((string) $node->original->get('field_reference')->value));
      if ($original_reference !== '') {
        $node->set('field_reference', $original_reference);
        return;
      }
    }

    $reference = strtoupper(trim((string) $node->get('field_reference')->value));
    if ($this->referenceBuilder->isReferenceValid($reference)) {
      $node->set('field_reference', $reference);
      return;
    }

    $generated = $this->referenceBuilder->generate($node);
    $node->set('field_reference', $generated['reference']);

    foreach ($generated['warnings'] as $warning) {
      $this->loggerFactory->get('ps_offer')->warning($warning);
      $this->messenger->addWarning(t('@message', ['@message' => $warning]));
    }
  }

  /**
   * Ensures minimum divisible surface remains consistent with offer data.
   */
  protected function ensureMinDivisibleSurface(NodeInterface $node): void {
    if (!$node->hasField('field_min_divisible_surface') || !$node->hasField('field_is_divisible')) {
      return;
    }

    $isDivisible = (bool) ($node->get('field_is_divisible')->value ?? FALSE);
    if (!$isDivisible) {
      $node->set('field_min_divisible_surface', NULL);
      return;
    }

    $existing = $node->get('field_min_divisible_surface')->value;
    if ($existing !== NULL && $existing !== '' && (float) $existing > 0.0) {
      return;
    }

    $computed = $this->resolveMinDivisibleSurfaceFromDivisions($node);
    if ($computed !== NULL) {
      $node->set('field_min_divisible_surface', $computed);
    }
  }

  /**
   * Computes minimum divisible surface from linked division surfaces.
   */
  protected function resolveMinDivisibleSurfaceFromDivisions(NodeInterface $node): ?float {
    if (!$node->hasField('field_divisions') || $node->get('field_divisions')->isEmpty()) {
      return NULL;
    }

    $min = NULL;
    foreach ($node->get('field_divisions') as $divisionReference) {
      $division = $divisionReference->entity;
      if (!$division || !$division->hasField('surfaces')) {
        continue;
      }

      foreach ($division->get('surfaces') as $surface) {
        $value = $surface->get('value')->getValue();
        $unit = strtoupper((string) ($surface->get('unit')->getValue() ?? ''));

        if (!is_numeric($value) || (float) $value <= 0.0) {
          continue;
        }

        // Keep the computation safe by considering explicit m2 values only.
        if ($unit !== '' && $unit !== 'M2') {
          continue;
        }

        $floatValue = (float) $value;
        $min = $min === NULL ? $floatValue : min($min, $floatValue);
      }
    }

    return $min;
  }

}
