<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Hook;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\node\NodeInterface;

/**
 * Hooks for Offer nodes.
 */
class OfferHooks {

  /**
   * Constructs the OfferHooks object.
   */
  public function __construct(
    protected LoggerChannelFactoryInterface $loggerFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
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

    $reference = strtoupper(trim((string) $node->get('field_reference')->value));
    if ($reference !== '' && preg_match('/^[OD][LVC][A-Z]{3}\d{7}$/', $reference) === 1) {
      $node->set('field_reference', $reference);
      return;
    }

    $node->set('field_reference', $this->generateReference($node));
  }

  /**
   * Generates a business reference for an offer.
   */
  protected function generateReference(NodeInterface $node): string {
    $transaction_code = $this->getFieldCode($node, 'field_transaction_types');
    $property_code = $this->getFieldCode($node, 'field_property_type');
    $year = date('y', (int) ($node->getCreatedTime() ?: time()));

    $prefix = 'O'
      . $this->mapTransactionType($transaction_code)
      . $this->mapPropertyType($property_code)
      . $year;

    $counter = $this->entityTypeManager->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'offer')
      ->condition('field_reference', $prefix . '%', 'LIKE')
      ->count()
      ->execute() + 1;

    return $prefix . str_pad((string) $counter, 5, '0', STR_PAD_LEFT);
  }

  /**
   * Extracts a normalized code from an offer dictionary field.
   */
  protected function getFieldCode(NodeInterface $node, string $field_name): string {
    if (!$node->hasField($field_name) || $node->get($field_name)->isEmpty()) {
      return '';
    }

    return strtoupper(trim((string) $node->get($field_name)->value));
  }

  /**
   * Maps transaction business codes to the one-letter reference format.
   */
  protected function mapTransactionType(string $transaction_code): string {
    return match ($transaction_code) {
      'LOC', 'L', 'LEASE', 'RENT', 'RENTAL', 'LOCATION' => 'L',
      'V', 'VTE', 'VEN', 'VENTE', 'SALE', 'SAL' => 'V',
      'C', 'CES', 'CESSION' => 'C',
      default => $transaction_code !== '' ? substr($transaction_code, 0, 1) : 'L',
    };
  }

  /**
   * Maps property business codes to a three-letter reference block.
   */
  protected function mapPropertyType(string $property_code): string {
    $normalized = strtoupper(substr($property_code, 0, 3));
    return str_pad($normalized !== '' ? $normalized : 'BUR', 3, 'X');
  }

}
