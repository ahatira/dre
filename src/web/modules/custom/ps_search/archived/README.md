# Archived Files - ps_search v1.x

This directory contains files from ps_search version 1.x that were replaced in version 2.0.0.

## Migration to v2.0.0

In version 2.0.0, ps_search was refactored to use two new standalone modules:

1. **ps_location_autocomplete** - Provides location autocomplete widget with chips
2. **ps_search_filters** - Provides complete modular filter system with 15 plugins

## Archived Files

### JavaScript
- `js/search-filter-bar.js` (v1.4.0) - Replaced by `ps_search_filters/js/search-filters.js`

### CSS
- `css/search-filter-bar.css` (v1.4.0) - Replaced by `ps_search_filters/css/search-filters.css`

### Templates
- `templates/ps-search-filter-bar.html.twig` - Replaced by multiple templates in ps_search_filters

### Block Plugin
- `src/Plugin/Block/SearchFilterBarBlock.php` - Replaced by `ps_search_filters/src/Plugin/Block/SearchFiltersBlock.php`

## Why Archive Instead of Delete?

These files are preserved for:
- Reference during migration
- Rollback capability if needed
- Understanding previous implementation

## Deprecated Routes

The following routes now redirect to new endpoints:
- `/ps-search/count` → `/ps-search-filters/count`
- `/ps-search/location-suggest` → `/ps-location/suggest`
- `/ps-search/location-data` → `/ps-location/data`

## Safe to Delete?

These archived files can be safely deleted once:
1. Migration to v2.0.0 is complete and tested
2. All custom code is updated to use new modules
3. No rollback is needed

---

**Date Archived:** 2026-05-26  
**ps_search Version:** 2.0.0  
**Replaced By:** ps_search_filters + ps_location_autocomplete
