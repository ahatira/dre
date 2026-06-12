<?php

declare(strict_types=1);

namespace Drupal\ps_favorite\Service;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Builds compact offer summaries for favorite panel cards.
 */
final class FavoriteOfferSummaryBuilder {

  public function __construct(
    private readonly ModuleHandlerInterface $moduleHandler,
    private readonly ContainerInterface $container,
  ) {}

  /**
   * @return array<string, mixed>|null
   *   Summary props or NULL when the node cannot be summarized.
   */
  public function build(NodeInterface $node): ?array {
    if ($node->bundle() !== 'offer') {
      return NULL;
    }

    if ($this->moduleHandler->moduleExists('ps_compare')) {
      /** @var \Drupal\ps_compare\Service\CompareOfferSummaryBuilder $compareBuilder */
      $compareBuilder = $this->container->get('ps_compare.offer_summary_builder');
      return $compareBuilder->build($node);
    }

    return NULL;
  }

}
