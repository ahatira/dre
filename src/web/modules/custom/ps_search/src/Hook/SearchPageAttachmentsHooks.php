<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\ps_search\Search\Header\HeaderSearchPanelBuilder;
use Symfony\Component\Routing\Route;

/**
 * Page-level attachments for header search entry points.
 */
final class SearchPageAttachmentsHooks {

  public function __construct(
    private readonly HeaderSearchPanelBuilder $headerSearchPanelBuilder,
  ) {}

  /**
   * Attaches header search JS and drupalSettings on public routes.
   */
  #[Hook('page_attachments')]
  public function pageAttachments(array &$attachments): void {
    $route = \Drupal::routeMatch()->getRouteObject();
    if (!$route instanceof Route || $route->getOption('_admin_route')) {
      return;
    }

    $block = \Drupal::entityTypeManager()->getStorage('block')->load('ps_theme_header_search');
    if (!$block || !$block->status()) {
      return;
    }

    $panel = $this->headerSearchPanelBuilder->buildPanelContent();
    $attachments['#attached'] = BubbleableMetadata::mergeAttachments(
      $attachments['#attached'] ?? [],
      $panel['#attached'] ?? [],
    );
    CacheableMetadata::createFromRenderArray($panel)->applyTo($attachments);
  }

}
