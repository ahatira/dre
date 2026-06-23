<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\node\NodeInterface;

/**
 * Attaches compare client assets on the homepage offers carousel.
 */
final class CompareHomepageHooks {

  /**
   * Ensures compare toggle JS loads on the front page.
   */
  #[Hook('page_attachments')]
  public function pageAttachments(array &$attachments): void {
    if (\Drupal::routeMatch()->getRouteName() !== 'entity.node.canonical') {
      return;
    }

    $node = \Drupal::routeMatch()->getParameter('node');
    if (!$node instanceof NodeInterface) {
      return;
    }

    $front = (string) (\Drupal::config('system.site')->get('page.front') ?? '');
    if ($front === '' || $front !== '/node/' . $node->id()) {
      return;
    }

    /** @var \Drupal\ps_compare\Service\CompareLazyBuilder $lazyBuilder */
    $lazyBuilder = \Drupal::service('ps_compare.lazy_builder');
    $attachments['#attached'] = BubbleableMetadata::mergeAttachments(
      $attachments['#attached'] ?? [],
      $lazyBuilder->buildClientAttachments(),
    );
  }

}
