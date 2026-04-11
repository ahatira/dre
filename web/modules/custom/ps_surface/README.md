# PropertySearch Surface (ps_surface)

Production-grade Drupal 11 field module providing the `ps_surface` field
type with dictionary-backed validation, configurable defaults, and reusable
aggregation/validation services.

## Features
- **Field Type** (`ps_surface`): Composite field with value + 4
  dictionary-backed qualifiers (unit, type, nature, qualification)
- **Dictionary Validation**: All codes validated against whitelist via
  `ps_dictionary.manager`
- **Custom Constraint**: Symfony validator enforcing business rules
  (negative values, required fields, code validity)
- **Widget + Formatter**: DI-based components with configurable display
  options and dynamic dictionary loading
- **Reusable Services**: `ps_surface.validator` and `ps_surface.aggregator`
  for other modules
- **Admin Configuration**: ConfigFormBase at `/admin/ps/config/surface`
  with 3 settings sections
- **PHPStan Level 6**: Full static analysis compliance + strict typing

## Installation

### Prerequisites
- Drupal 11.3.2+
- PHP 8.3+
- `ps_dictionary` module (foundation dependency)

### Enable Module
```bash
vendor/bin/drush en ps_surface -y
vendor/bin/drush cr
```

## Configuration

**Admin Path**: `/admin/ps/config/surface`
*(PropertySearch Administration → Configuration → Surface Settings)*

### Settings Sections

1. **Dictionary Type Mappings** — Configure which dictionaries supply codes
  for unit/type/nature/qualification
2. **Field Defaults** — Default unit, formatter decimals, label visibility
3. **Validation Rules** — Allow negative values, require/optional fields

See [SETTINGS_FORM.md](SETTINGS_FORM.md) for detailed form documentation.

## Services

### Validator Service
```php
$validator = \Drupal::service('ps_surface.validator');

// Validate field item
$violations = $validator->validateItem($item);

// Validate field item list
$violations = $validator->validateField($field_items);

// Validate data row
$violations = $validator->validateRow(['value' => 100, 'unit' => 'M2']);
```

### Aggregator Service
```php
$aggregator = \Drupal::service('ps_surface.aggregator');

// Sum from field item list
$total = $aggregator->sum($field_items);

// Sum from entity field
$total = $aggregator->sumField($offer, 'field_surface');

// Sum from data rows
$total = $aggregator->sumRows($data);
```

## Field Type Reference

### Properties
- `value` (float) — Numeric surface value (required)
- `unit` (string) — Dictionary-backed unit code (e.g., M2, FT2)
- `type` (string) — Dictionary-backed type code (e.g., USABLE, GLA)
- `nature` (string) — Dictionary-backed nature code
- `qualification` (string) — Dictionary-backed qualification code

### Field Settings (in widget)
```php
$field = BaseFieldDefinition::create('ps_surface')
  ->setLabel('Surface Area')
  ->setSetting('default_unit', 'M2')
  ->setRequired(TRUE);
```

## Module Documentation

- [IMPLEMENTATION_COMPLETE.md](IMPLEMENTATION_COMPLETE.md) — Full technical reference & architecture
- [SETTINGS_FORM.md](SETTINGS_FORM.md) — Admin form configuration guide

## Testing

### Manual Validation
```bash
# Enable module
vendor/bin/drush en ps_surface -y

# Clear cache
vendor/bin/drush cr

# Test validator service
vendor/bin/drush eval \
  '$v = \Drupal::service("ps_surface.validator"); echo "OK";'

# Test aggregator service
vendor/bin/drush eval \
  '$a = \Drupal::service("ps_surface.aggregator"); echo "OK";'

# Access admin form
# Navigate to /admin/ps/config/surface
```

## Quality Standards

- ✅ **PHPStan Level 6** — Zero static analysis errors
- ✅ **Strict Typing** — All parameters/returns typed,
  `declare(strict_types=1)` in all files
- ✅ **Dependency Injection** — Services via constructor,
  no `\Drupal::service()` in objects
- ✅ **Documentation** — Full PHPDoc for all public APIs
- ✅ **Standards** — Drupal 11 coding standards + PSR-12

## Integration Examples

### In ps_offer Entity
```php
$field = BaseFieldDefinition::create('ps_surface')
  ->setLabel(t('Total Surface'))
  ->setCardinality(1)
  ->setRequired(FALSE);
```

### In ps_import Process
```php
$validator = \Drupal::service('ps_surface.validator');
$violations = $validator->validateRow(['value' => 100, 'unit' => 'M2']);
if (empty($violations)) {
  // Safe to import
}
```

## Dependencies

| Module | Type | Purpose |
|--------|------|---------|
| `ps_dictionary` | Hard | Dictionary validation (codes whitelist) |
| `core` (field) | Hard | Field Plugin API |
| `core` (form) | Hard | Form API + ConfigFormBase |

## Performance

- **Field Storage**: 5 database columns (minimal overhead)
- **Dictionary Lookups**: Cached via `ps_dictionary.manager` (O(1))
- **Aggregation**: O(n) where n = number of items
- **Validation**: Early exit on first error (fail-fast)

## Support

- **Bug Reports**: File in PS project issue tracker
- **Code Quality**: PHPStan level 6, Drupal standards compliance
- **Maintenance**: Follows Drupal 11 security advisories

---

**Status**: 🟢 Production Ready | **Last Updated**: January 2026
