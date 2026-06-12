<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Breadcrumb;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\Service\OfferSearchUrlBuilder;

/**
 * Builds offer detail breadcrumbs: Home > Results page > Offer title.
 */
final class OfferBreadcrumbBuilder implements BreadcrumbBuilderInterface {

  use StringTranslationTrait;

  public function __construct(
    private readonly OfferSearchUrlBuilder $searchUrlBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match, ?CacheableMetadata $cacheable_metadata = NULL): bool {
    if ($route_match->getRouteName() !== 'entity.node.canonical') {
      return FALSE;
    }

    $node = $route_match->getParameter('node');
    $applies = $node instanceof NodeInterface && $node->bundle() === 'offer';

    if ($applies && $cacheable_metadata !== NULL) {
      $cacheable_metadata->addCacheableDependency($node);
      $cacheable_metadata->addCacheContexts(['languages:language_interface', 'languages:language_url']);
      $cacheable_metadata->addCacheTags(['config:ps_search.seo_url_mappings']);
    }

    return $applies;
  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match): Breadcrumb {
    $breadcrumb = new Breadcrumb();
    $node = $route_match->getParameter('node');
    assert($node instanceof NodeInterface);

    $breadcrumb->addLink(Link::createFromRoute($this->t('Home'), '<front>'));

    $resultsUrl = $this->searchUrlBuilder->buildResultsPageUrl($node);
    $breadcrumb->addLink(Link::fromTextAndUrl(
      $this->t('Results page'),
      $resultsUrl,
    ));

    $breadcrumb->addLink(Link::fromTextAndUrl(
      $this->resolveOfferTitle($node),
      Url::fromRoute('<none>'),
    ));

    $breadcrumb->addCacheableDependency($node);
    $breadcrumb->addCacheContexts(['languages:language_interface', 'languages:language_url', 'route']);
    $breadcrumb->addCacheTags(['config:ps_search.seo_url_mappings']);

    return $breadcrumb;
  }

  /**
   * Resolves the breadcrumb label for the current offer.
   */
  private function resolveOfferTitle(NodeInterface $node): string {
    if ($node->hasField('field_commercial_title') && !$node->get('field_commercial_title')->isEmpty()) {
      return (string) $node->get('field_commercial_title')->value;
    }

    return $node->label();
  }

}
