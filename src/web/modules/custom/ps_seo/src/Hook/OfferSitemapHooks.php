<?php

declare(strict_types=1);

namespace Drupal\ps_seo\Hook;

use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_seo\Sitemap\OfferSitemapDefinition;

/**
 * Filters offer nodes per asset type in Simple XML Sitemap entity queries.
 */
final class OfferSitemapHooks {

  /**
   * Restricts offer bundles to published nodes of the matching asset type.
   */
  #[Hook('entity_query_tag__node__simple_sitemap_alter')]
  public function entityQueryTagNodeSimpleSitemapAlter(QueryInterface $query): void {
    if ($query->getMetaData('bundle') !== 'offer') {
      return;
    }

    $sitemap = $query->getMetaData('sitemap');
    if ($sitemap === NULL) {
      return;
    }

    $assetCode = OfferSitemapDefinition::getAssetCodeForVariant($sitemap->id());
    if ($assetCode === NULL) {
      // Offers must not leak into non-offer sitemap variants.
      $query->condition('nid', 0);
      return;
    }

    $query->condition('status', 1);
    $query->condition('field_asset_type.value', $assetCode);
  }

}
