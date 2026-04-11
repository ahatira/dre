# PropertySearch Division Module

## Overview

The **ps_division** module provides a complete division entity system for managing real estate subdivisions (lots, floors, apartments) as spatial units within parent properties.

### Key Features

- **Bundle-based Division Entity** with configurable types
- **Division Types** (lot, floor, apartment, etc.) with field customization
- **Integration with ps_surface** field type for surface measurements
- **Dictionary validation** via `ps_dictionary` module
- **Administrative Views** at `/admin/ps/content/divisions`
- **Settings Management** at `/admin/ps/config/divisions`
- **Type Management** at `/admin/ps/structure/division-types`
- **Cache invalidation** for parent entities
- **Surface aggregation** services with caching

**Architecture Layer**: Business (depends on ps, ps_dictionary, ps_surface)

---

## Installation

```bash
# Enable dependencies first
vendor/bin/drush en ps ps_dictionary ps_surface -y

# Enable ps_division
vendor/bin/drush en ps_division -y

# Rebuild cache
vendor/bin/drush cr
```

---

## Entity Structure

### Division Entity (ps_division)

Content entity with bundled types for flexible field configuration.

| Field | Type | Description |
|-------|------|-------------|
| `type` | entity_reference | Bundle (division_type) |
| `entity_id` | integer | Weak reference to parent offer/node (nullable) |
| `floor` | ps_dictionary | Floor code (dictionary: floor; PB, RDC, R+1, etc.) |
| `building_name` | string | Building name (entity label, translatable) |
| `division_type` | ps_dictionary | Type code (configurable dictionary, default: surface_type) |
| `nature` | ps_dictionary | Nature code (configurable dictionary, default: surface_nature) |
| `lot` | string | Lot identifier (alphanumeric, translatable) |
| `surfaces` | ps_surface (multi) | Surface measurements (field from ps_surface module) |
| `availability` | text_long | Availability notes (translatable) |
| `status` | boolean | Published status (from EntityPublishedTrait) |

### Division Type (division_type)

Config entity serving as bundle for Division entities.

| Field | Type | Description |
|-------|------|-------------|
| `id` | string | Machine name |
| `label` | string | Human-readable label |
| `description` | text | Description |

---

## Services

### ps_division.manager

Main service for division business logic.

**Methods:**
- `getByParent(int $entityId): array` — Get all divisions for parent entity (cached)
- `calculateTotalSurface(int $entityId): float` — Sum surfaces across divisions
- `validate(DivisionInterface $division): array` — Validate against dictionary
- `getSummary(DivisionInterface $division): array` — Generate summary data

### ps_division.aggregates

Cached aggregation service for expensive calculations.

**Methods:**
- `getTotalSurface(int $entityId): float` — Cached total surface
- `invalidate(int $entityId): void` — Clear cache for parent

---

## Usage Examples

### Create Division via Drush

```php
vendor/bin/drush eval "
\$storage = \\Drupal::entityTypeManager()->getStorage('ps_division');
\$division = \$storage->create([
  'type' => 'division',
  'building_name' => 'Building A',
  'entity_id' => 123, // Parent offer ID
  'lot' => 'LOT-001',
  'floor' => 'P1',
  'division_type' => 'USABLE',
  'nature' => 'ACT',
  'availability' => 'Available immediately',
  'status' => 1,
]);

// Add surfaces
\$division->surfaces->appendItem([
  'value' => 150.5,
  'unit' => 'M2',
  'type' => 'USABLE',
  'nature' => 'ACT',
  'qualification' => 'TOTAL',
]);
\$division->surfaces->appendItem([
  'value' => 80.0,
  'unit' => 'M2',
  'type' => 'GLA',
  'nature' => 'BUR',
]);

\$division->save();
echo 'Created division ID: ' . \$division->id() . PHP_EOL;
echo 'Total surface: ' . \$division->getTotalSurface() . ' m²' . PHP_EOL;
"
```

### Get Divisions for Parent

```php
$manager = \Drupal::service('ps_division.manager');
$divisions = $manager->getByParent(123);
foreach ($divisions as $division) {
  echo $division->getBuildingName() . ' - ' . $division->getTotalSurface() . " m²\n";
}
```

### Validate Division

```php
$manager = \Drupal::service('ps_division.manager');
$errors = $manager->validate($division);
if (empty($errors)) {
  echo "Division is valid\n";
} else {
  foreach ($errors as $error) {
    echo "Error: $error\n";
  }
}
```

---

## Configuration

### Division Settings (`/admin/ps/config/divisions`)

**Dictionary Configuration:**
- `division_type`: Dictionary type for the division type field (default: 'surface_type')
- `division_nature`: Dictionary type for the division nature field (default: 'surface_nature')

**Import Settings:**
- `default_type`: Default division type machine name (default: 'division')
- `auto_aggregate`: Auto-aggregate surfaces on save (default: true)
- `cache_aggregates`: Cache aggregate calculations (default: true)

**Business Rules:**
- `require_parent`: Make parent entity ID mandatory (default: false)
- `validate_codes`: Validate dictionary codes (default: true)
- `aggregate_on_save`: Aggregate surfaces on entity save (default: true)

---

## Dictionaries Provided

### Floor Dictionary

ps_division provides the `floor` dictionary type with the following entries:

- `S2` — Basement -2
- `S1` — Basement -1
- `PB` — Ground floor
- `P1` — Floor 1
- `P2` — Floor 2
- `P3` — Floor 3

The division type and nature fields use dictionaries from ps_surface module (surface_type and surface_nature) but can be configured via settings.

---

## Routes

### Content Management
- **List**: `/admin/ps/content/divisions`
- **Add**: `/admin/ps/content/divisions/add/{division_type}`
- **View**: `/division/{ps_division}`
- **Edit**: `/admin/ps/content/divisions/{ps_division}/edit`
- **Delete**: `/admin/ps/content/divisions/{ps_division}/delete`

### Structure Management
- **Types List**: `/admin/ps/structure/division-types`
- **Add Type**: `/admin/ps/structure/division-types/add`
- **Edit Type**: `/admin/ps/structure/division-types/manage/{division_type}`
- **Delete Type**: `/admin/ps/structure/division-types/manage/{division_type}/delete`

### Configuration
- **Settings**: `/admin/ps/config/divisions`

---

## Dependencies

- `ps` — Core PropertySearch services
- `ps_dictionary` — Dictionary validation
- `ps_surface` — Surface field type and surface_type/surface_nature dictionaries

---

## Permissions

- **administer ps_division entities** — Full CRUD + settings access
- **view division entities** — View divisions

---

## Cache Management

Division module uses cache tags for automatic invalidation:

- `ps_division_list` — All division lists
- `ps_division_parent:{entity_id}` — Divisions for specific parent
- Entity-level tags via Drupal core

Cache is automatically invalidated on division insert/update/delete.

---

## Field UI Integration

Division types support Field UI for custom field management:

1. Go to `/admin/ps/structure/division-types`
2. Click "Manage fields" for a type
3. Add custom fields as needed
4. Configure form/display settings

---

## Development

### Run Tests

```bash
# Unit tests
vendor/bin/phpunit web/modules/custom/ps_division/tests/src/Unit

# Kernel tests
vendor/bin/phpunit web/modules/custom/ps_division/tests/src/Kernel

# Functional tests
vendor/bin/phpunit web/modules/custom/ps_division/tests/src/Functional

# All tests
vendor/bin/phpunit web/modules/custom/ps_division
```

### Code Quality

```bash
# PHPCS
vendor/bin/phpcs web/modules/custom/ps_division

# PHPStan
vendor/bin/phpstan analyse web/modules/custom/ps_division
```

---

## Integration with ps_offer

ps_division is designed to work with ps_offer module:

```php
// In ps_offer entity
$offer->get('divisions')->appendItem(['target_id' => $division->id()]);
$offer->save();

// Get all divisions for an offer
$manager = \Drupal::service('ps_division.manager');
$divisions = $manager->getByParent($offer->id());
$totalSurface = $manager->calculateTotalSurface($offer->id());
```

---

## Troubleshooting

### Issue: Cache not invalidating

**Solution:** Clear all caches with `drush cr` and check that hooks are registered.

### Issue: Dictionary validation failing

**Solution:** Ensure ps_dictionary module is enabled and dictionaries are populated:
```bash
drush ps:dictionary-list
```

### Issue: Surfaces field not found

**Solution:** Add ps_surface field to division type:
```bash
drush en ps_surface -y
drush cr
```

---

## Further Documentation

- **Architecture**: See main project README.md
- **Module Catalog**: `specs/docs/ps_modules.md`
- **Guidelines**: `.github/guidelines/`

---

**Module Maintainer**: PropertySearch Development Team
**Last Updated**: January 2026
