# Offer reference architecture

## Goal

The offer reference must be:

- unique;
- generated automatically when editors choose auto mode;
- manually overridable when allowed by configuration;
- fully configurable from the Back-Office;
- independent from canonical dictionary codes through dedicated reference aliases.

This document defines the target architecture and the implementation baseline delivered in LOT 1, LOT 2 and LOT 3.

## Functional model

The reference is produced by an ordered pattern made of segments.

Each segment contributes a fixed-length fragment to the final reference.

Example target pattern:

1. position 1: offer kind (`O` for offer, `D` for request)
2. position 2: operation alias (`L`, `V`, `C`)
3. positions 3-5: asset alias (`BUR`, `ACT`, `ENT`, `COM`)
4. positions 6-7: creation year on 2 digits
5. positions 8-12: sequence number on 5 digits

Generated example: `OLBUR2600001`

## Design principles

- Config-First: patterns are exportable Drupal config entities.
- Dictionary-safe: reference aliases must not mutate canonical business codes.
- Stable output: existing generated references are not regenerated silently.
- BO-first: editors and administrators can preview and manage generation rules.
- Extensible: segment processing stays open to new rule types.

## Data model

### Config entity: `ps_offer_reference_pattern`

Stores one configurable reference pattern.

Exported properties:

- `id`
- `label`
- `uuid`
- `status`
- `weight`
- `target_bundles`
- `allow_manual_override`
- `require_uniqueness`
- `validate_manual_value_against_pattern`
- `generate_on_create`
- `regenerate_on_source_change`
- `counter_scope_mode`
- `segments`

### Segment structure

Each pattern contains ordered `segments` entries with:

- `uuid`
- `label`
- `type`
- `weight`
- `length`
- `source_field`
- `resolution_mode`
- `alias_set_ids`
- `mapping`
- `fallback_value`
- `settings`

## Segment types

Baseline segment types:

- `literal`: fixed string value
- `field_map`: field value resolved through a mapping table
- `year_2_digits`: two-digit year extracted from a date or current time fallback
- `counter`: left-padded sequence number

Future segment types:

- `substring`
- `computed`
- `dictionary_alias`

## Resolution priority

For a mapped field segment, the resolved reference code must follow this priority:

1. explicit mapping configured on the pattern segment;
2. alias set configured for the segment or pattern;
3. fallback value defined on the segment;
4. canonical source value;
5. blocking error.

## BO screens

Target BO screens:

1. pattern collection
2. pattern add/edit form
3. alias set collection
4. alias set add/edit form
5. future custom field widget on the offer form

LOT 1 delivers the first two screens.

LOT 2 delivers the alias-set collection and form.

LOT 3 delivers the offer field widget behavior on node forms.

## LOT 1 scope

LOT 1 delivers:

- the `ps_offer_reference_pattern` config entity;
- Back-Office add/edit/list screens for patterns;
- a default pattern matching the current reference specification;
- a pure server-side generator service that can build a reference from pattern data and contextual values;
- unit tests for the generator service.

Remaining items after LOT 3:

- persistent sequence locking;
- uniqueness hardening under concurrency;
- additional browser coverage for edge-case flows.

## Default pattern delivered in LOT 1

The default install pattern is:

1. `Offer type`: `O`
2. `Operation code`: `field_operation_type` mapped to `RENT => L`, `SALE => V`, `CESSION => C`
3. `Asset code`: `field_asset_type` mapped to `BUR => BUR`, `ACT => ACT`, `ENT => ENT`, `COM => COM`
4. `Creation year`: current year on 2 digits
5. `Counter`: 5 digits, left padded with `0`

## Runtime service model

### `OfferReferenceGenerator`

Generates a reference from a pattern and a context.

Inputs:

- reference pattern entity;
- scalar context values keyed by field machine name;
- sequence number;
- optional creation date.

Output:

- generated reference string.

The service validates:

- segment type support;
- segment length compliance;
- mandatory mapping availability.

### `OfferReferenceManager`

Applies runtime behavior at save time on offer nodes.

Current canonical mode field:

- `field_reference_auto` (`boolean`)

Behavior:

- if `field_reference_auto = 1`, regenerate `field_reference` from active pattern;
- if `field_reference_auto = 0`, keep editor-provided `field_reference` value;
- legacy fallback on `field_reference_mode` has been removed from runtime path.

### `OfferReferenceWidget`

LOT 3 widget behavior on `field_reference`:

- renders `field_reference_auto` as a toggle control (button-like label in BO);
- disables manual input when auto mode is enabled;
- exposes preview message area;
- keeps helper text outside the control row for alignment consistency.

Key configurable widget settings:

- `toggle_button_text`
- `preview_manual_message`

## Next lots

### LOT 2

- alias-set config entity
- alias resolution service
- segment-level resolution modes
- generator support for alias priorities

### LOT 3

- offer field widget
- boolean auto/manual mode (`field_reference_auto`)
- preview on form state changes
- pre-save orchestration in `OfferReferenceManager`

### LOT 4

- uniqueness hardening
- sequence lock management

### LOT 5

- browser coverage
- live BO validation