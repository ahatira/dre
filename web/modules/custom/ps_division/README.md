# PropertySearch Division (ps_division)

## Overview
ps_division provides a bundled content entity to model offer subdivisions (lots/floors/spaces), with:
- Structural fields (building, lot, floor, type, nature, availability)
- Multi-value surfaces via ps_surface
- Back-office CRUD and type management
- Validation helper service and summary helper

This module is business-layer oriented and is consumed by ps_offer through field_divisions.

## What Is Implemented Today

### Entity: ps_division
Main fields exposed by the entity:
- type (bundle: division_type)
- building_name (required)
- lot
- floor (ps_dictionary, floor)
- division_type (ps_dictionary, configurable dictionary type)
- nature (ps_dictionary, configurable dictionary type)
- availability
- surfaces (ps_surface, multi)
- status, owner, created, changed

### Config Entity: division_type
- Bundle definitions for ps_division
- Field UI support per bundle

### Services
- ps_division.manager (DivisionManager)
  - validate(DivisionInterface): array
  - getSummary(DivisionInterface): array

### Hooks
- Cache tag invalidation on insert/update/delete:
  - ps_division_list

## Out of Scope (by design)
The following concepts were considered and explicitly excluded from ps_division:

- **Parent link field `entity_id`**: ps_division has no back-reference to its parent offer.
  The ownership relationship is owned by ps_offer via `field_divisions` (entity_reference).
- **`getByParent(int)` / `calculateTotalSurface(int)`**: not part of DivisionManagerInterface.
  Cross-offer surface aggregation lives in `ps_offer.surface_division_search_value_resolver`.
- **Parent-scoped cache tags `ps_division_parent:{id}`**: not implemented.
  The `ps_division_list` tag is the granularity used for cache invalidation.

These are deliberate boundaries, not missing features.

## Installation

```bash
vendor/bin/drush en ps_division -y
vendor/bin/drush cr
```

Dependencies are declared in module info and include ps, ps_dictionary and ps_surface.

## Admin Routes
- Divisions list: /admin/ps/content/divisions
- Add division: /admin/ps/content/divisions/add/{division_type}
- Division types: /admin/ps/structure/division-types
- Settings: /admin/ps/config/divisions

## Integration With ps_offer
In ps_offer, `field_divisions` is an entity reference to ps_division (cardinality unlimited).
Use this field to model subdivisible offers and lot-level surfaces.

### Surface consistency contract
The surface coherence between an offer's global surface (`field_surfaces`) and the sum of
its division surfaces is managed by `ps_offer.surface_division_search_value_resolver`. It exposes:

- `ps_offer_surface_main_value` — first positive M2 value from `field_surfaces` (source of truth)
- `ps_offer_surface_main_unit` — always M2 when set
- `ps_offer_surface_total_divisions` — sum of M2 surfaces across all linked divisions
- `ps_offer_surface_consistency_status` — `ok` (delta ≤5%), `warning` (≤20% or partial data), `mismatch` (>20%)

**Rule**: `field_surfaces` is the authoritative source. Division surfaces are additive/complementary.
An offer can have zero divisions without triggering a mismatch.

### Entity method getTotalSurface()
`Division::getTotalSurface()` returns the sum of all positive numeric surface values on the
division's `surfaces` field (M2 and other units summed without conversion). Use it for
display only. For M2-safe cross-division aggregation, use the resolver above.

## Validation Notes
`DivisionManager::validate()` delegates surface-level validation (value, unit, type, nature,
qualification completeness) to `ps_surface.validator`. It then adds division-level dictionary
checks (floor, division_type, nature) according to `ps_division.settings`. This avoids
duplicating surface validation rules between the two modules.

## Testing
Project tests related to this module are under:
- tests/src/Unit
- tests/src/Kernel
- tests/src/Functional

Run targeted tests according to your local tooling availability.
