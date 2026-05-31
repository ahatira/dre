# PS Diagnostic

Simple diagnostic module for real-estate offers.

## Scope (MVP)

- Manage diagnostic types from one simple BO list.
- Provide one field type `diagnostic_item` based on XML diagnostic structure.
- Keep everything configurable and translatable.
- Provide a simple and intuitive BO navigation.

## Features

- Settings page: `/admin/ps/config/diagnostic`
- Structure page: `/admin/ps/structure/diagnostic`
- Diagnostic types CRUD: `/admin/ps/structure/diagnostic/types`
- Field type: `diagnostic_item`
- Widget and 3 dedicated formatters for display styles:
	- `Diagnostic - Horizontal (default)`
	- `Diagnostic - Vertical`
	- `Diagnostic - Full`
- BO widget with live class suggestion from the entered value.
- Clear admin styling for DPE/GES offer fields.
- Per-type class administration (`unit`, `icon`, `classes` with color and range)
- Dedicated Twig template per formatter style

## XML Structure Mapping

The field stores one XML diagnostic item shape:

- `diagnostic_type`
- `class`
- `value`
- `diagnostic_date`
- `validity_end_date`
- `no_classification`
- `non_applicable`

## Diagnostic Type Configuration

Each diagnostic type can define:

- `unit` (for value display)
- `icon` (semantic key)
- `classes` sequence:
	- `label`
	- `color` (hex)
	- `range_max`

This enables a consistent visual rendering and class detection from explicit class or numeric value.

In the BO widget, the class can be auto-suggested from the numeric value while still allowing a manual override.

Shared state rules are applied by all formatter styles:

- Disabled mode when non applicable, no classification, expired, or missing value/class.
- Active class resolved from explicit class first, then inferred from numeric value and class ranges.

## Notes

This module is intentionally minimal to keep BO usage simple and maintainable.
