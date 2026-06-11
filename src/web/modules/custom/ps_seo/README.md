# ps_seo

Central SEO module for Property Search (BNPPRE alignment).

## Scope

| Area | Owner | Status |
|------|-------|--------|
| Metatag defaults (global, front, offer) | `ps_seo` | Phase 1 |
| Schema.org (WebPage, Organization, Place) | `ps_seo` | Phase 1 |
| Per-offer Metatag overrides | `ps_offer` (`field_metatag`) | Existing |
| Search SEO URLs | `ps_search` | Existing |
| Search `<title>` / canonical / hreflang | `ps_seo` | Phase 2 (planned) |
| XML sitemap | `ps_seo` | Phase 4 (planned) |

## Admin

`/admin/ps/config/seo` — hub linking Metatag, Pathauto, Redirect, Simple Sitemap and search URL mappings.

## Metatag defaults

Shipped in `config/metatag_defaults/` and imported via `ps_seo.install` / update hooks (not `config/install`, to avoid conflicts with existing Metatag config on upgraded sites). Stale language overrides are removed on import so EN token patterns apply on FR pages too.

## Dependencies

Requires `ps_offer` for offer Metatag defaults. Search URL settings remain in `ps_search`.
