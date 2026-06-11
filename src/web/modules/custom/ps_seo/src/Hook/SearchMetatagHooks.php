<?php

declare(strict_types=1);

namespace Drupal\ps_seo\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\ps_search\Service\SearchSeoCanonicalUrlBuilder;
use Drupal\ps_seo\Search\SearchSeoHeadBuilder;
use Drupal\views\Views;

/**
 * Injects dynamic Metatag values on the property search listing page.
 */
final class SearchMetatagHooks {

  private const SEARCH_ROUTE = 'view.ps_search_offers.page_list';

  public function __construct(
    private readonly RouteMatchInterface $routeMatch,
    private readonly SearchSeoHeadBuilder $searchSeoHeadBuilder,
    private readonly SearchSeoCanonicalUrlBuilder $canonicalUrlBuilder,
  ) {}

  /**
   * Overrides global Metatag defaults on search listing pages.
   */
  #[Hook('metatags_alter')]
  public function metatagsAlter(array &$metatags, array &$context): void {
    if ($this->routeMatch->getRouteName() !== self::SEARCH_ROUTE) {
      return;
    }

    $view = Views::getView('ps_search_offers');
    if ($view === NULL) {
      return;
    }

    $view->setDisplay('page_list');
    $head = $this->searchSeoHeadBuilder->build($view);
    if ($head === NULL) {
      return;
    }

    $metatags['title'] = $head['title'];
    $metatags['description'] = $head['description'];
    $metatags['canonical_url'] = $head['canonical_url'];
    $metatags['shortlink'] = $head['canonical_url'];
    $metatags['og_title'] = $head['title'];
    $metatags['og_description'] = $head['description'];
    $metatags['og_url'] = $head['canonical_url'];
    $metatags['twitter_cards_title'] = $head['title'];
    $metatags['twitter_cards_description'] = $head['description'];
    $metatags['schema_web_page_description'] = $head['description'];

    foreach ($this->canonicalUrlBuilder->buildAlternateUrls() as $langcode => $url) {
      if ($langcode === 'x-default') {
        $metatags['hreflang_xdefault'] = $url;
        continue;
      }

      $metatags['hreflang_per_language:hreflang_' . $langcode] = $url;
    }
  }

}
