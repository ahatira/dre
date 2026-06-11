<?php

declare(strict_types=1);

namespace Drupal\ps_seo\Sitemap;

/**
 * Maps Simple Sitemap variants to offer asset type dictionary codes.
 *
 * Aligns with BNPPRE split sitemaps (offices, retail, warehouse, etc.).
 */
final class OfferSitemapDefinition {

  /**
   * Sitemap variant ID => asset type code (ps_dictionary).
   */
  public const VARIANT_ASSET_CODES = [
    'offer_bur' => 'BUR',
    'offer_com' => 'COM',
    'offer_ent' => 'ENT',
    'offer_act' => 'ACT',
    'offer_ter' => 'TER',
    'offer_log' => 'LOG',
    'offer_cow' => 'COW',
  ];

  /**
   * Human-readable labels for admin UI.
   */
  public const VARIANT_LABELS = [
    'offer_bur' => 'Offers — Offices (BUR)',
    'offer_com' => 'Offers — Retail (COM)',
    'offer_ent' => 'Offers — Warehouse (ENT)',
    'offer_act' => 'Offers — Activity (ACT)',
    'offer_ter' => 'Offers — Land (TER)',
    'offer_log' => 'Offers — Logistics (LOG)',
    'offer_cow' => 'Offers — Coworking (COW)',
  ];

  /**
   * Returns the asset type code for a sitemap variant, if any.
   */
  public static function getAssetCodeForVariant(string $variantId): ?string {
    return self::VARIANT_ASSET_CODES[$variantId] ?? NULL;
  }

  /**
   * Returns TRUE when the variant is managed by ps_seo offer sitemaps.
   */
  public static function isOfferVariant(string $variantId): bool {
    return isset(self::VARIANT_ASSET_CODES[$variantId]);
  }

}
