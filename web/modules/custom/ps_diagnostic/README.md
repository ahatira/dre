# PropertySearch Diagnostic Module

Provides diagnostic field type and configuration entities for regulatory diagnostics (DPE, GES) with automatic class calculation, visual display, and dimmed state for incomplete data.

## Purpose

The `ps_diagnostic` module is a **Domain layer** module that provides:

- **Structured field type** for storing diagnostic data (energy performance, emissions, technical indicators)
- **Config entities** for diagnostic configuration (DPE, GES with color tables and ranges)
- **Automatic class calculation** from numeric values using configured ranges
- **Visual display** with colored energy class bars (horizontal SVG/vertical/compact layouts)
- **Dimmed state rendering** for diagnostics with missing data (configurable opacity)
- **Services** for normalization, completeness scoring, search indexing, and comparison

## Architecture

**Layer**: Domain
**Dependencies**: `ps` (Foundation), `ps_dictionary` (Foundation)
**Consumed by**: `ps_offer` (Business), `ps_import` (Business), `ps_search` (Functional)

## Features

### Config Entity: `diagnostic`

Configurable diagnostics with class ranges and colors.

**Default diagnostics installed**:
- **DPE** (Consommations énergétiques): A-G classes with green→red gradient (kWh/m²/year)
- **GES** (Émissions de gaz à effet de serre): A-G classes with lavender→violet gradient (kg CO₂/m²/year)

**Admin UI**: `/admin/ps/structure/diagnostic`

### Field Type: `ps_diagnostic`

Structured field with **7 subfields**:

| Subfield | Type | Description |
|----------|------|-------------|
| `type_id` | string | Diagnostic ID (e.g., `dpe`, `ges`) |
| `value` | float | Numeric diagnostic value |
| `class` | string | Energy class label (A-G, auto-calculated if empty) |
| `valid_from` | string | Start date of validity (ISO 8601) |
| `valid_to` | string | End date of validity (ISO 8601) |
| `no_classification` | boolean | Special state: not classified (displays "?") |
| `non_applicable` | boolean | Special state: not applicable (displays "N/A") |

### Field Widget: `ps_diagnostic_default`

Admin form input widget with AJAX auto-calculation and smart suggestions.

**Features**:
- Type selector with dictionary validation
- Numeric value input with real-time calculation
- Class label auto-suggestion (alphabetical: A→B→C, plus suffixes: G+→G++, minus suffixes: A-→A--)
- Validity date pickers
- Special state checkboxes (no_classification, non_applicable)
- AJAX callbacks for type change and value updates

### Field Formatter: `ps_diagnostic_default`

Display formatter with multiple layout options and configurable visibility.

**Features**:
- **Three layout modes**:
  - **Horizontal**: Colored energy scale bar (A-G segments with current class highlighted)
  - **Vertical**: Card layout (left scale, right info panel with large value)
  - **Compact**: Inline badge (minimal format)
- **Per-display configuration**:
  - `show_type_label` — Display diagnostic type (e.g., DPE)
  - `show_numeric_value` — Display numeric value with unit
  - `show_validity_dates` — Display validity date range
  - `default_layout` — Select layout variant (horizontal/vertical/compact)
  - `dim_empty` — Dim diagnostics without value/class
  - `dim_opacity` — Opacity % (10-90) when dimmed

### Services

Three core services handle diagnostic operations (all implement interfaces):

#### DiagnosticClassSuggester (DiagnosticClassSuggesterInterface)

**Service ID**: `ps_diagnostic.class_suggester`

Intelligent next-class suggestion for admin forms.

**Features**:
- Alphabetical progression: A → B → C
- Plus suffix progression: G+ → G++ → G+++
- Minus suffix progression: A- → A-- → A---
- DPE standard color interpolation (green → red)
- Color darkening for classes beyond G

```php
$suggester = \Drupal::service('ps_diagnostic.class_suggester');
$suggestion = $suggester->suggestNextClass([
  'a' => ['label' => 'A', 'color' => '#00A651', 'range_max' => 70],
  'b' => ['label' => 'B', 'color' => '#8DC63F', 'range_max' => 110],
]);
// Returns: ['label' => 'C', 'color' => '#FFF200', 'range_max' => NULL]
```

#### DiagnosticClassCalculator (DiagnosticClassCalculatorInterface)

**Service ID**: `ps_diagnostic.class_calculator`

Calculates energy class from numeric value using configured ranges.

```php
$calculator = \Drupal::service('ps_diagnostic.class_calculator');
$class = $calculator->calculateClass('dpe', 150.0); // Returns 'C'
$class = $calculator->calculateClass('dpe', 50.0);  // Returns 'A'
```

#### DiagnosticNormalizer (DiagnosticNormalizerInterface)

**Service ID**: `ps_diagnostic.normalizer`

Validates and normalizes diagnostic field data before save.

**Features**:
- Type ID validation (checks config entity + dictionary)
- Numeric value normalization (negatives → NULL)
- Auto-calculates `class` if empty via DiagnosticClassCalculator
- Date coherence checks (valid_to ≥ valid_from)
- Special state handling (no_classification XOR non_applicable)

```php
$normalizer = \Drupal::service('ps_diagnostic.normalizer');
$normalized = $normalizer->normalize($diagnosticItem);
// Returns: normalized DiagnosticItem with validated data
```

## Installation

1. Enable dependencies:

   ```bash
   drush en ps ps_dictionary -y
   ```

2. Enable ps_diagnostic:

   ```bash
   drush en ps_diagnostic -y
   ```

3. Clear cache:

   ```bash
   drush cr
   ```

## Configuration

### Dictionary Setup

Diagnostic types are validated via `ps_dictionary` using the `diagnostic_type` dictionary:

```php
$isValid = \Drupal::service('ps_dictionary.manager')
  ->isValid('diagnostic_type', 'dpe');
```

### Admin Routes

| Route | Purpose |
|-------|---------|
| `/admin/ps/structure/diagnostic` | List/CRUD diagnostic types |
| `/admin/ps/structure/diagnostic/add` | Create new diagnostic type |
| `/admin/ps/structure/diagnostic/{id}/edit` | Edit diagnostic type |

### Formatter Settings

Configure per display mode (e.g., `node.offer.full`):

1. Visit `/admin/structure/types/manage/offer/display`
2. Configure `field_diagnostic` formatter:
   - **Layout**: Select Horizontal / Vertical / Compact
   - **Show type label**: Toggle diagnostic name (e.g., "DPE")
   - **Show numeric value**: Toggle value + unit display
   - **Show validity dates**: Toggle date range
   - **Dim empty**: Dim diagnostics without value/class
   - **Dim opacity**: Opacity % (10-90) when dimmed

## Usage

### Create Diagnostic with Auto-Calculation

```php
$offer = Node::load(123);
$diagnostic = $offer->field_diagnostics->appendItem([
  'type_id' => 'dpe',
  'value' => 175.0,
  // 'class' will auto-calculate to 'C'
  'valid_from' => '2022-01-15',
  'valid_to' => '2032-01-15',
]);
$offer->save();
```

### Display with Different Layouts

```php
// Horizontal in search results (grid layout)
$view = entity_view($offer, 'search_result');

// Vertical on detail page (card layout)
$view = entity_view($offer, 'full');

// Compact in teaser (inline badge)
$view = entity_view($offer, 'teaser');
```

### Suggest Next Class in Form

```php
$suggester = \Drupal::service('ps_diagnostic.class_suggester');
$current_classes = $diagnostic->getClasses();
$next = $suggester->suggestNextClass($current_classes);

// $next = ['label' => 'B', 'color' => '#8DC63F', 'range_max' => NULL]
```

## Testing

```bash
# Unit tests
vendor/bin/phpunit web/modules/custom/ps_diagnostic/tests/src/Unit

# Kernel tests
vendor/bin/phpunit web/modules/custom/ps_diagnostic/tests/src/Kernel
```

## License

GPL-2.0-or-later
