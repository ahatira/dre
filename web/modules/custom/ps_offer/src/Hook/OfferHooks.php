<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Hook;

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

    // Log presave event
    $this->loggerFactory->get('ps_offer')->debug(
      'Offer node @id presave triggered',
      ['@id' => $node->id() ?? 'new'],
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

}
