<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\node\NodeInterface;
use Drupal\ps_theme\Utility\OfferCardPropsBuilder;

/**
 * Offer node preprocess hooks.
 */
final class Offer {

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_node')]
  public function preprocessNode(array &$variables): void {
    $node = $variables['node'] ?? NULL;
    if (!$node instanceof NodeInterface || $node->bundle() !== 'offer') {
      return;
    }

    if (($variables['view_mode'] ?? '') !== 'teaser') {
      return;
    }

    $variables['offer_card'] = OfferCardPropsBuilder::build($node);
  }

}
