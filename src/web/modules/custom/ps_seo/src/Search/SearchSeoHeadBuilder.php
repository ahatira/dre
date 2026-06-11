<?php

declare(strict_types=1);

namespace Drupal\ps_seo\Search;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\ps_search\Search\Filter\SearchResultsHeaderBuilder;
use Drupal\ps_search\Service\SearchSeoCanonicalUrlBuilder;
use Drupal\views\ViewExecutable;

/**
 * Builds dynamic search listing head tags (title, description, canonical).
 */
final class SearchSeoHeadBuilder {

  public function __construct(
    private readonly SearchResultsHeaderBuilder $resultsHeaderBuilder,
    private readonly SearchSeoCanonicalUrlBuilder $canonicalUrlBuilder,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Builds Metatag-ready values for the search listing page.
   *
   * @return array{title: string, description: string, canonical_url: string}|null
   *   Head tag values, or NULL when canonical URL cannot be resolved.
   */
  public function build(ViewExecutable $view): ?array {
    $copy = $this->resultsHeaderBuilder->buildSeoHeadData($view);
    $canonicalUrl = $this->canonicalUrlBuilder->buildAbsoluteUrl();
    if ($canonicalUrl === NULL) {
      return NULL;
    }

    $siteName = (string) $this->configFactory->get('system.site')->get('name');
    $title = $copy['title'];
    if ($siteName !== '') {
      $title .= ' | ' . $siteName;
    }

    return [
      'title' => $title,
      'description' => $copy['description'],
      'canonical_url' => $canonicalUrl,
    ];
  }

}
