# Offers by Feature (Iteration 1 - Views)

## Scope

This document describes the first implementation iteration for feature-based offer filtering, based only on Drupal Views.

Iteration 1 goal is to provide an operational admin filter without introducing Search API/Solr dependencies.

## Delivered View

- View ID: `ps_offer_by_feature`
- Config file: `config/install/views.view.ps_offer_by_feature.yml`
- Route path: `/admin/ps/content/offers-by-feature`
- Base table: `node_field_data`
- Bundle scope: `offer`

## Filters

The page exposes the following filters:

- `Feature ID` (query arg: `feature_id`): string `contains` filter on `node__field_features.field_features_feature_definition_id`
- `Status` (query arg: `status`): grouped selector (Published / Unpublished)

An internal fixed filter limits results to content type `offer`.

## Columns

- Title (linked to node)
- Status
- Updated
- Operations

The filter targets the feature definition ID stored on the field row. For the QA offer currently in the database, the matching value is `zz_order_feature`.

The query is configured with `distinct: true` to reduce duplicates when a node has multiple feature rows.

## Expected Usage

1. Open `/admin/ps/content/offers-by-feature`.
2. Enter a feature definition ID (or part of it) in `Feature ID`.
3. Optionally filter by publication status.
4. Submit to list matching offers.

## Known Limits (Iteration 1)

- Filtering is textual (`contains`) on raw feature definition IDs.
- No faceting, no relevance scoring, no typo tolerance.
- Multi-value field joins can still require careful interpretation for broad queries.

These limits are intentionally accepted for Iteration 1. Iteration 2 should introduce Search API/Solr for richer search behavior.
