# PropertySearch Price (`ps_price`)

**Type**: Domain Specialized Module
**Layer**: Domain Layer
**Dependencies**: `ps`, `ps_dictionary`
**Drupal**: 11.3.2
**PHP**: 8.3+

---

## 📋 Purpose

Provides a **generic, reusable price field type** with support for:

- ✅ **Composite price data**: amount, currency, unit, period
- ✅ **Business flags**: on_request, from, VAT excluded, charges included
- ✅ **Locale-aware formatting**: Numeric formatter with locale support
- ✅ **Price normalization**: Convert prices to reference unit (€/m²/year) for search comparison
- ✅ **Dictionary integration**: Period multipliers and display labels from dictionaries

**Important**: This is a **generic field module**. Business logic and validation are handled by the consuming modules (e.g., `ps_offer`).

---

## 🏗️ Architecture

```
┌─────────────────────────────────┐
│  Functional Modules             │ ← ps_search, ps_compare, etc.
├─────────────────────────────────┤
│  Business Layer                 │ ← ps_offer (validates prices)
├─────────────────────────────────┤
│  Domain: ps_price ← YOU ARE HERE │ (generic field type)
│  + ps_features, ps_diagnostic    │
├─────────────────────────────────┤
│  Foundation: ps, ps_dictionary   │
└─────────────────────────────────┘
```

### Responsibility Separation

| Module | Responsibility |
|--------|---------------|
| **ps_price** | Generic field type, formatting, normalization |
| **ps_offer** | Business rules, validation, context-specific logic |

**Example**:
- `ps_price` stores: `amount=1200, unit=SUR, period=MEN, currency=EUR`
- `ps_offer` validates: "For LOC transaction, price must be SUR + ANN + HT/HC"

---

## 🎯 Features

### Field Type: `ps_price`

Composite field with 9 subfields:

| Subfield | Type | DB Type | Description |
|----------|------|---------|-------------|
| `amount` | float | numeric(15,2) | Main price amount |
| `currency_code` | string | varchar(3) | ISO currency code (from dictionary) |
| `value_type_code` | string | varchar(32) | MIN/MAX qualifier (from dictionary) |
| `unit_code` | string | varchar(64) | Price unit: SUR (/m²), GLO (global), OTH |
| `period_code` | string | varchar(32) | Period: ANN, MEN, TRI, SEM |
| `is_on_request` | bool | tinyint | "On request" flag |
| `is_from` | bool | tinyint | "From" prefix flag |
| `is_vat_excluded` | bool | tinyint | VAT excluded flag (HT) |
| `is_charges_included` | bool | tinyint | Charges included flag (CC) |

#### Database Indexes

- `amount` — For price range queries
- `currency_code` — For filtering by currency

---

## 🔧 Components

### Widget: `ps_price_default`

Comprehensive form with all fields and checkboxes.

**Form Layout**:
```
Amount:              [______]
Currency code:       [EUR]
Value type:          [- None -] (MIN/MAX)
Unit code:           [/m²/an]
Period:              [Year]     ☐ On request
                               ☐ From
                               ☐ VAT excluded (HT)
                               ☐ Charges included (CC)
```

**Settings**:
- `show_unit` (bool) — Show unit field
- `show_period` (bool) — Show period field
- `show_value_type` (bool) — Show value type field
- `show_flags` (bool) — Show business flags

---

### Formatters

#### `ps_price_full` (default)

Displays complete price with all details.

**Example Outputs**:
```
From 1,250.00 EUR SUR /ANN (excl. VAT, charges incl.)
1,250.00 EUR Per m² /Per Year (excl. VAT, charges incl.)  ← with display_codes_as_labels
```

**Options**:
- `show_currency` (bool, default TRUE)
- `show_unit` (bool, default TRUE)
- `show_period` (bool, default TRUE)
- `show_flags` (bool, default TRUE)

#### `ps_price_short`

Displays simplified format (amount + currency only).

**Example Output**:
```
1,250.00 EUR
```

---

## 📦 Services

### `ps_price.formatter`

**Class**: `Drupal\ps_price\Service\PriceFormatter`

Provides locale-aware formatting with business flag support.

#### Methods

```php
public function format(FieldItemInterface $item, array $options = []): string;
public function formatShort(FieldItemInterface $item, array $options = []): string;
```

#### Usage

```php
$formatter = \Drupal::service('ps_price.formatter');

// Full format (codes)
$formatted = $formatter->format($price_item, [
  'show_currency' => TRUE,
  'show_unit' => TRUE,
  'show_period' => TRUE,
  'show_flags' => TRUE,
]);
// Output: "From 1,250.00 EUR SUR /MEN (excl. VAT, charges incl.)"

// Full format (labels) - requires display_codes_as_labels = TRUE
// Output: "From 1,250.00 EUR Per m² /Per Month (excl. VAT, charges incl.)"

// Short format
$short = $formatter->formatShort($price_item);
// Output: "1,250.00 EUR"
```

---

### `ps_price.normalizer`

**Class**: `Drupal\ps_price\Service\PriceNormalizer`

Normalizes prices to reference unit (`€/m²/year`) for search comparison.

#### Methods

```php
public function normalize(FieldItemInterface $item, float $surfaceM2 = 0.0): ?float;
```

#### Normalization Logic

**Period Conversion** (reads from dictionary `price_period.*.metadata.multiplier`):
```php
MEN (monthly)   × 12  → yearly
TRI (quarterly) × 4   → yearly
SEM (weekly)    × 52  → yearly
ANN (annual)    × 1   → yearly (no change)
```

**Unit Conversion**:
```php
SUR (per m²)  → amount (already normalized)
GLO (global)  → amount / surface (if surface > 0)
OTH (other)   → amount (no conversion)
```

**Zero Surface Handling** (configurable via `normalize_on_zero_surface`):
- `'null'` (default) → Returns `NULL` (prevents invalid comparisons)
- `'zero'` → Returns `0.0`
- `'original'` → Returns original amount

#### Usage

```php
$normalizer = \Drupal::service('ps_price.normalizer');

// Normalize monthly global price to EUR/m²/year
$price_item->amount = 10000;      // 10,000 EUR
$price_item->unit_code = 'GLO';   // Global (total price)
$price_item->period_code = 'MEN'; // Monthly
$surface = 500.0;                 // 500 m²

$normalized = $normalizer->normalize($price_item, $surface);
// Result: 240.0 EUR/m²/year
// Calculation: (10,000 × 12) / 500 = 240

// Zero surface → NULL (by default)
$normalized = $normalizer->normalize($price_item, 0.0);
// Result: NULL (prevents division by zero)
```

---

## ⚙️ Configuration

### Settings (`/admin/ps/config/price`)

**Default Currency**:
- Default: `EUR`
- Used when creating new price fields

**Normalization**:
- `normalize_on_zero_surface` — Behavior when dividing by zero surface
  - `null` (recommended) — Returns NULL to prevent invalid comparisons
  - `zero` — Returns 0.0
  - `original` — Returns original amount
- `reference_unit` — Documentation only (default: `EUR/m²/year`)

**Display**:
- `display_codes_as_labels` — Show labels instead of codes
  - `false` (default) — Shows `MEN`, `SUR`, etc.
  - `true` — Shows `Per Month`, `Per m²`, etc. (from dictionaries)

---

## 📊 Dictionary Integration

The module relies on `ps_dictionary` for:

### Period Multipliers

**Dictionary Type**: `price_period`

**Entries**:
```yaml
# ps_dictionary.entry.price_period_men.yml
code: MEN
label: "Per Month"
metadata:
  multiplier: 12  # ← Used by PriceNormalizer

# ps_dictionary.entry.price_period_tri.yml
code: TRI
label: "Per Quarter"
metadata:
  multiplier: 4

# ps_dictionary.entry.price_period_sem.yml
code: SEM
label: "Per Week"
metadata:
  multiplier: 52

# ps_dictionary.entry.price_period_ann.yml
code: ANN
label: "Per Year"
metadata:
  multiplier: 1
```

### Other Dictionaries

- `currency` — Currency codes (EUR, USD, etc.)
- `price_unit` — Unit codes (SUR, GLO, OTH)
- `price_value_type` — Value qualifiers (MIN, MAX)

---

## 🎯 Business Rules (Implemented in `ps_offer`)

This module **does NOT enforce** business rules. Context-specific validation is done in `ps_offer`.

### Expected Rules (for reference)

**Location (LOC)** — Rental offers:
- Unit: `SUR` (per m²)
- Period: `ANN` (yearly)
- Flags: `is_vat_excluded=true`, `is_charges_included=false` (HT/HC)
- Example: `1,200 EUR SUR /ANN (excl. VAT, charges incl.)`

**Sale (VEN)** — Sale offers:
- Unit: `GLO` (global/total)
- Period: `NULL` (no period for sales)
- Flags: `is_vat_excluded=true`
- Example: `450,000 EUR GLO (excl. VAT)`

**Coworking** — Flexible office space:
- Unit: Custom (e.g., `DESK`, `POSTE`)
- Period: `DAY` (daily rate)
- Example: `25 EUR DESK /DAY`

**Implementation**: See `ps_offer` module for validation logic.

---

## 🧪 Testing

### Run Tests

```bash
# All tests
vendor/bin/phpunit web/modules/custom/ps_price/tests

# Unit tests only
vendor/bin/phpunit web/modules/custom/ps_price/tests/src/Unit

# Kernel tests only
vendor/bin/phpunit web/modules/custom/ps_price/tests/src/Kernel
```

### Test Coverage

- ✅ **20 tests** (12 Unit + 8 Kernel)
- ✅ **72 assertions**
- ✅ 100% pass rate

**Test Suites**:
- `PriceFormatterTest` — Format methods, locale handling
- `PriceNormalizerTest` — Period/unit conversion, zero surface handling
- `PriceFieldTest` — Field storage, getters, multiple values
- `PriceValidationTest` — Dictionary code validation, constraints

---

## 📚 Usage Examples

### Creating a Price Field

```php
// In ps_offer or other module
$fields['field_price'] = BaseFieldDefinition::create('ps_price')
  ->setLabel(t('Price'))
  ->setCardinality(-1)
  ->setDisplayConfigurable('form', TRUE)
  ->setDisplayConfigurable('view', TRUE);
```

### Setting Price Values

```php
// Location rental price
$offer->field_price->amount = 1200.00;
$offer->field_price->currency_code = 'EUR';
$offer->field_price->unit_code = 'SUR';
$offer->field_price->period_code = 'ANN';
$offer->field_price->is_vat_excluded = TRUE;
$offer->field_price->is_charges_included = FALSE;

// Sale price
$offer->field_price->amount = 450000.00;
$offer->field_price->currency_code = 'EUR';
$offer->field_price->unit_code = 'GLO';
$offer->field_price->period_code = NULL;
$offer->field_price->is_vat_excluded = TRUE;
```

### Normalizing for Search

```php
// In ps_offer or ps_search
$normalizer = \Drupal::service('ps_price.normalizer');

foreach ($offer->field_price as $price_item) {
  $surface = $offer->surface_total->value ?? 0.0;
  $normalized = $normalizer->normalize($price_item, $surface);

  if ($normalized !== NULL) {
    // Index for search/comparison
    $search_index->addField('normalized_price', $normalized);
  }
}
```

---

## 🔄 Migration from v0.x

**Breaking Changes**:

1. ❌ **Removed `PriceRule` entity** — Business logic moved to `ps_offer`
2. ❌ **Removed `PriceRuleMatcher` service** — No automatic transformation
3. ❌ **Removed `AutoPriceRuleSubscriber`** — No event-based rules
4. ✅ **Added configurable settings** — `normalize_on_zero_surface`, `display_codes_as_labels`
5. ✅ **Dynamic multipliers** — Read from dictionaries instead of hardcoded

**Migration Steps**:

1. Remove references to `ps_price.rule_matcher` service
2. Remove PriceRule config entities (`ps_price.rule.*`)
3. Implement validation in `ps_offer` (or consuming module)
4. Update config: `drush cim -y`
5. Clear cache: `drush cr`

---

## 🛠️ Development

### Adding New Dictionary Codes

**Example**: Add new period type `BIANNUAL`

```yaml
# config/optional/ps_dictionary.entry.price_period_bia.yml
uuid: null
langcode: en
id: price_period_bia
dictionary_type: price_period
code: BIA
label: "Per 6 Months"
description: "Biannual (6 months) price"
weight: 4
status: true
deprecated: false
metadata:
  icon: "calendar"
  multiplier: 2  # ← 2 periods per year
```

The `PriceNormalizer` will automatically use the multiplier from metadata.

---

## 📖 Related Documentation

- **Architecture**: [web/modules/custom/ps/README.md](../ps/README.md)
- **Dictionaries**: [web/modules/custom/ps_dictionary/README.md](../ps_dictionary/README.md)
- **Module Catalog**: [specs/docs/ps_modules.md](../../../specs/docs/ps_modules.md)
- **Copilot Instructions**: [.github/copilot-instructions.md](../../../.github/copilot-instructions.md)

---

## ✅ Checklist for Consuming Modules

When using `ps_price` in your module:

- [ ] **Don't rely on automatic transformation** — `ps_price` is generic
- [ ] **Validate prices in your module** — Check unit/period/flags match your business rules
- [ ] **Use dictionary codes** — Validate via `ps_dictionary.manager::isValid()`
- [ ] **Normalize for search** — Use `ps_price.normalizer` for comparisons
- [ ] **Format for display** — Use `ps_price.formatter` with appropriate options
- [ ] **Document expected formats** — In your module's README or widget descriptions

---

**Author**: Property Search Team
**Last Updated**: January 15, 2026
**Version**: 1.0 (post-cleanup)
