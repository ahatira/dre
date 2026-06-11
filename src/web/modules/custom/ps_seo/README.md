# ps_seo

Central SEO module for Property Search (BNPPRE alignment).

## Scope

| Area | Owner | Status |
|------|-------|--------|
| Metatag defaults (global, front, offer) | `ps_seo` | Phase 1 |
| Schema.org (WebPage, Organization, Place) | `ps_seo` | Phase 1 |
| Per-offer Metatag overrides | `ps_offer` (`field_metatag`) | Existing |
| Search SEO URLs | `ps_search` | Existing |
| Search `<title>` / description / canonical | `ps_seo` + `ps_search` | Phase 2 |
| Search hreflang alternates | `ps_seo` + `ps_search` | Phase 3 |
| XML sitemap (offers by asset type) | `ps_seo` | Phase 4 |
| Search robots / noindex facet URLs | `ps_seo` + `ps_search` | Phase 5 |
| Offer Metatag + JSON-LD enrichment | `ps_seo` | Phase 6 |

## Admin

`/admin/ps/config/seo` — hub linking Metatag, Pathauto, Redirect, Simple Sitemap and search URL mappings.

## Metatag defaults

Shipped in `config/metatag_defaults/` and imported via `ps_seo.install` / update hooks (not `config/install`, to avoid conflicts with existing Metatag config on upgraded sites). Stale language overrides are removed on import so EN token patterns apply on FR pages too.

## XML sitemap

Shipped in `config/sitemap/` and imported via `ps_seo.install` / `ps_seo_update_9003()`.

- **Index** : `/sitemap.xml` (`default_variant: index`)
- **Variants** : one sitemap per asset type (`offer_bur`, `offer_com`, …) at `/{variant}/sitemap.xml`
- **Scope** : published `node.offer` only, filtered by `field_asset_type` (no search listing pages)
- **Regenerate** : `drush simple-sitemap:generate` (in `ps_php`)

Filter logic : `OfferSitemapHooks` (`entity_query_tag__node__simple_sitemap_alter`).

## Search robots (Phase 5)

On the property search listing route, any **user-visible query string** (facets, sort, pagination, map bounds, etc.) sets `robots: noindex,follow` and removes hreflang alternates. Clean SEO paths without query params remain `index,follow` with canonical and hreflang unchanged.

Logic : `SearchSeoIndexabilityChecker` (`ps_search`) + `SearchMetatagHooks` (`ps_seo`).

## Offer pages (Phase 6)

BNPPRE-style `<title>`, meta description, OG/Twitter image and Schema.org (`WebPage`, `Place` + `PostalAddress`, `GeoCoordinates`, `ImageObject`, `Organization` publisher) on canonical offer routes.

Logic : `OfferSeoHeadBuilder` + `OfferMetatagHooks` (`hook_metatags_alter`). Reuses `OfferSurfaceKpiBuilder` for surface fragments and adds hreflang alternates when translations exist.

Import : `ps_seo_update_9004()` (enables `schema_image_object` + re-imports Metatag defaults).

## Dependencies

Requires `ps_offer` for offer Metatag defaults. Search URL settings remain in `ps_search`.
