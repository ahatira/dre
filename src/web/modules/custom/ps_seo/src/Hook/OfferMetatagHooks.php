<?php

declare(strict_types=1);

namespace Drupal\ps_seo\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_seo\Offer\OfferSeoHeadBuilder;

/**
 * Injects BNPPRE-style Metatag and Schema.org values on offer detail pages.
 */
final class OfferMetatagHooks {

  public function __construct(
    private readonly RouteMatchInterface $routeMatch,
    private readonly OfferSeoHeadBuilder $offerSeoHeadBuilder,
  ) {}

  /**
   * Overrides Metatag defaults on canonical offer pages.
   */
  #[Hook('metatags_alter')]
  public function metatagsAlter(array &$metatags, array &$context): void {
    if ($this->routeMatch->getRouteName() !== 'entity.node.canonical') {
      return;
    }

    $node = $this->routeMatch->getParameter('node');
    if (!$node instanceof NodeInterface || $node->bundle() !== 'offer') {
      return;
    }

    $head = $this->offerSeoHeadBuilder->build($node);
    if ($head === NULL) {
      return;
    }

    foreach ($head as $key => $value) {
      if ($value === NULL || $value === '') {
        continue;
      }
      $metatags[$key] = (string) $value;
    }

    $metatags['shortlink'] = $head['canonical_url'];

    foreach ($this->offerSeoHeadBuilder->buildAlternateUrls($node) as $langcode => $url) {
      if ($langcode === 'x-default') {
        $metatags['hreflang_xdefault'] = $url;
        continue;
      }

      $metatags['hreflang_per_language:hreflang_' . $langcode] = $url;
    }
  }

}
