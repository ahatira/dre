# PropertySearch Features Module

> **Version**: 1.0.0  
> **Status**: Production Ready  
> **Layer**: Domain  
> **Dependencies**: ps, ps_dictionary

## Overview

Manages configurable technical features for property offers with multiple value types, validation rules, and metadata organization. Feature groups are now managed via the `feature_group` dictionary type in `ps_dictionary` for centralized control and consistency.

### New Capabilities (2025-11)

The module now provides additional backend services enabling normalization, search facets generation and comparison grouping:

- **Value Normalizer** (`ps_features.value_normalizer`): Converts raw feature values into canonical structured forms (boolean flags, yes/no strings, numeric floats, range arrays `[min,max]`, dictionary codes validation, plain strings). Use this before indexing or persisting derived data.
- **Search Facets Builder** (`ps_features.search_facets_builder`): Translates facetable features (those with the new `is_facetable` flag) into facet definition arrays consumed by the search layer. Maps value types to facet types (`flag|yesno→boolean`, `dictionary→terms`, `numeric→numeric`, `range→range`, `string→text`).
- **Compare Builder** (`ps_features.compare_builder`): Groups features into ordered comparison sections based on `compare_sections` configuration (in `ps_features.settings`). Features whose group is not listed fall into an automatic `other` bucket.

### Extended Settings

The settings form (`/admin/ps/config/feature`) now includes:

- `packs_fallback_strategy`: Future use for feature packs (currently: `none` or `default_pack`).
- `compare_sections`: Ordered list of section codes (e.g. `general`, `comfort`, `technical`) driving comparison grouping.

### Facetable Features

Each feature now has a `Facetable` checkbox. When enabled the feature will be exposed as a candidate for search facets through `SearchFacetsBuilder`.

## Service Usage Examples

```php
use Drupal\ps_features\Service\SearchFacetsBuilderInterface;
use Drupal\ps_features\Service\CompareBuilderInterface;
use Drupal\ps_features\Service\ValueNormalizerInterface;

final class ExampleConsumer {
  public function __construct(
    private readonly SearchFacetsBuilderInterface $facetsBuilder,
    private readonly CompareBuilderInterface $compareBuilder,
    private readonly ValueNormalizerInterface $normalizer,
  ) {}

  public function buildFacets(): array {
    return $this->facetsBuilder->build();
  }

  public function buildComparison(array $features): array {
    return $this->compareBuilder->build($features);
  }

  public function normalize(string $featureId, mixed $raw): mixed {
    return $this->normalizer->normalize($featureId, $raw);
  }
}
```
## Field Type

The module provides the `ps_feature_value` field type:
- **Category**: `propertysearch` (groups with other PropertySearch fields)
- **Storage**: Composite field (code + value + unit)
- **Widget**: Custom FeatureValueWidget with dynamic value type handling
- **Formatter**: FeatureValueFormatter with label resolution

See `ps` module documentation for field type category system.
## Drush / Debug Helpers

```bash
# List facet definitions (simple ad-hoc example via php-eval):
drush php:eval "var_dump(\Drupal::service('ps_features.search_facets_builder')->build());"

# Normalize a value:
drush php:eval "var_dump(\Drupal::service('ps_features.value_normalizer')->normalize('floor_number', '12'));"
```

## Installation

```bash
drush en ps_features -y
drush cr
```

Automatically installs 17 feature configurations.

## Pre-configured Features (17)

### Feature Groups

Features are organized into 4 groups managed by the `feature_group` dictionary:

- **Equipments** (equipments): Equipment and appliances
- **Services** (services): Services and utilities
- **Building condition** (building_condition): Building state and construction
- **More information** (more_information): Additional features and details

\n### Equipments (5)
- has_elevator (flag)
- air_conditioning (dictionary)
- computer_cabling (dictionary)
- false_floor (string)
- heating_system (dictionary)

\n### Services (7)
- home_service (string)
- office_layout (string)
- partitioned_offices (yesno)
- meeting_room (yesno)
- cafeteria (yesno)
- security_system (string)
- terrace_garden (string)

\n### Building Condition (2)
- building_condition (string)
- premises_condition (string)

\n### More Information (3)
- several_courtyards (flag)
- garden_perfect_condition (flag)
- highly_flexible (flag)

## Admin UI

- Features: /admin/ps/structure/features
- Groups: /admin/ps/structure/groups
- Settings: /admin/ps/config/feature

## Configuration Keys

| Key | Purpose |
|-----|---------|
| `compare_sections` | Ordered sections for comparison builder grouping |
| `packs_fallback_strategy` | Strategy for future feature pack fallback (`none`, `default_pack`) |

## Testing

Unit tests cover new builder services:

- `SearchFacetsBuilderTest` ensures facet type/widget mapping.
- `CompareBuilderTest` ensures ordering and grouping logic.

Run tests:

```bash
vendor/bin/phpunit --configuration web/core/phpunit.xml.dist web/modules/custom/ps_features/tests/src/Unit/Service
```

## License

Proprietary - PropertySearch Platform
