# PropertySearch Dictionary Module

Centralized business code management with validation, resolution, and reusable field storages.

## Overview

The `ps_dictionary` module provides a foundation layer for managing business
dictionaries (code taxonomies) throughout the PropertySearch platform. It handles
validation, resolution, and localization of business codes such as property types,
transaction types, and currencies.

## Core Dictionaries (v1.0)

The module includes 3 essential dictionaries extracted from PropertySearch CRM data:

| Type | Code | Entries | Purpose |
|------|------|---------|---------|
| **transaction_type** | Type of transaction | LOC | Location-based transaction |
| **currency** | Currency codes | EUR | Euro |
| **property_type** | Property classification | ACT, BUR | Activity, Office |

*Additional dictionaries can be added as needed. This minimal set covers core CRM data.*

## Features

- **Foundation Layer**: Config entities for types/entries, manager service, validation APIs
- **Reusable Field Type**: Custom `ps_dictionary` field type extends Drupal's List (text) field
- **Cached Resolution**: O(1) lookups with intelligent cache invalidation
- **Status Management**: Active/inactive and deprecated flags
- **Metadata Support**: Extensible metadata schema with select fields (list_string type)
- **Dynamic Form Fields**: Automatically generates metadata input fields based on schema definition
- **Translatable Metadata**: Support for translatable metadata fields marked with `translate: true`
- **Drush Commands**: CLI tools for listing, exporting, cache management
- **Full Admin UI**: Dictionary type/entry management and settings
- **Multi-language Ready**: All configuration in English, translatable via Drupal Locale

## Architecture

### Layer

**Foundation** - Depends only on `ps` (core) module

### Dependencies

- Drupal Core 11+
- `ps` module (PropertySearch foundation)

### Service

- **`ps_dictionary.manager`** (`DictionaryManagerInterface`)
  - `isValid(string $type, string $code): bool`
  - `getLabel(string $type, string $code): ?string`
  - `getOptions(string $type, bool $activeOnly = TRUE): array`
  - `getEntry(string $type, string $code): ?DictionaryEntryInterface`
  - `getEntries(string $type, bool $activeOnly = TRUE): array`
  - `isDeprecated(string $type, string $code): bool`
  - `getMetadata(string $type, string $code): array`
  - `clearCache(?string $type = NULL): void`

### Entities

#### DictionaryType (Config Entity)

- **ID**: Machine name (e.g., `property_type`)
- **Label**: Human-readable name
- **Description**: Optional description
- **Locked**: Whether type can be deleted
- **Metadata**: Custom attributes
- **Metadata Schema**: YAML definition of metadata fields for entries

#### DictionaryEntry (Config Entity)

- **ID**: Composite `{type}.{code}` (e.g., `property_type.SALE`)
- **dictionary_type**: Parent type ID
- **code**: Machine code (e.g., `SALE`, `RENT`)
- **label**: Human-readable label
- **description**: Optional description
- **weight**: Sort order (lower = first)
- **status**: Active/inactive flag
- **deprecated**: Deprecation flag
- **metadata**: Custom attributes (e.g., currency symbol)
  - *Note*: Entries use `metadata` property for actual data values
  - Dictionary *types* use `metadata_schema` to define allowed metadata fields
  - Schema drives dynamic form generation for entry editing

## Configuration

### Install Behavior

**config/install/** (Always installed):
- 3 Dictionary Types: transaction_type, currency, property_type
- 4 Dictionary Entries: LOC, EUR, ACT, BUR
- Module settings: ps_dictionary.settings.yml

### Field Type Registration

The module registers the `ps_dictionary` custom field type, which extends Drupal's List (text) field.
Fields are created on-demand via Drupal's field UI or programmatically. The field type automatically:
- Loads allowed values from the configured dictionary type
- Supports standard list widgets (select, buttons)
- Uses standard list formatters
- Caches allowed values with smart invalidation

## Installation & Setup

```bash
# Enable module
drush en ps_dictionary -y

# Clear cache
drush cr

# Verify installation
drush ps:dictionary-list
```

### Creating Dictionary Fields

To create a field using this field type in any entity bundle:

1. Use Drupal UI: `/admin/structure/types/manage/{bundle}/fields/add-field`
   - Choose field type: **Dictionary**
   - Select a **Dictionary type** from dropdown (e.g., transaction_type)
   - Configure cardinality and display settings

2. Or create field config programmatically:
```php
$field_storage = FieldStorageConfig::create([
  'field_name' => 'field_transaction_types',
  'entity_type' => 'node',
  'type' => 'ps_dictionary',
  'cardinality' => -1, // Unlimited
  'settings' => ['dictionary_type' => 'transaction_type'],
]);
$field_storage->save();

$field = FieldConfig::create([
  'field_storage' => $field_storage,
  'bundle' => 'offer',
  'label' => 'Transaction Types',
]);
$field->save();
```

## Admin Routes

- **Dictionary Types/Entries**: `/admin/ps/structure/dictionaries`
- **Module Settings**: `/admin/ps/config/dictionary`

## Usage

### In Code (Services)

```php
// Inject service
public function __construct(
  private readonly DictionaryManagerInterface $dictionaryManager,
) {}

// Validate code
if ($this->dictionaryManager->isValid('property_type', 'ACT')) {
  // Code is valid and active
}

// Get label
$label = $this->dictionaryManager->getLabel('property_type', 'ACT');
// Returns: "Activity"

// Get form options
$options = $this->dictionaryManager->getOptions('property_type');
// Returns: ['ACT' => 'Activity', 'BUR' => 'Office', ...]

// Get entry with metadata
$entry = $this->dictionaryManager->getEntry('currency', 'EUR');
$symbol = $entry->getMetadata()['symbol'] ?? '€';

// Check if deprecated
if ($this->dictionaryManager->isDeprecated('currency', 'EUR')) {
  // Handle deprecated code
}
```

### Drush Commands

```bash
# List all dictionary types
drush ps:dictionary-list

# Show entries for a type
drush ps:dictionary-show property_type

# Export dictionary as YAML
drush ps:dictionary-export property_type

# Export as JSON
drush ps:dictionary-export property_type --format=json

# Clear cache
drush ps:dictionary-cache-clear
drush ps:dictionary-cache-clear property_type
```

### Admin UI

1. **Dictionary Types**: `/admin/ps/structure/dictionaries`

   - List all dictionary types
   - Add/edit/delete types
   - View entry count per type

2. **Dictionary Entries**: `/admin/ps/structure/dictionaries/{type}/entries`

   - Sortable list by weight
   - Add/edit/delete entries
   - Filter by status

3. **Settings**: `/admin/ps/config/dictionary`
   - Import behavior options
   - Deprecation policy

## Permissions

- **administer dictionaries**: Full CRUD access to types and entries
- **view dictionaries**: Read-only access
- **edit dictionary entries**: Edit entries only (not types)

## Settings

Configure dictionary behavior at `/admin/ps/config/dictionary`:

### Import Behavior

- **allow_unknown_codes**: Accept unknown codes with warning (default: `false`)
- **auto_create_on_unknown**: Auto-create entries for unknown codes (default:
  `false`)
- **default_status_new_items**: Status for auto-created entries: `active` or
  `inactive` (default: `inactive`)

## Core Dictionaries Reference

### Mapping from CRM Data

The 3 core dictionaries are derived from PropertySearch CRM XML feeds:

#### transaction_type (LOC)

| Code | Label | CRM Source | Notes |
|------|-------|-----------|-------|
| LOC | Location | OPERATIONS_LIST/OPERATION_CODE = "LOC" | Location-based transaction |

**Used for**: Classifying transaction type in offers

#### currency (EUR)

| Code | Label | CRM Source | Notes |
|------|-------|-----------|-------|
| EUR | Euro | BUDGETS_LIST/BUDGET/CURRENCY = "EUR" | European currency |

**Used for**: Price field currency selection

#### property_type (ACT, BUR)

| Code | Label | CRM Source | Notes |
|------|-------|-----------|-------|
| ACT | Activity | TYPE_CODE = "ACT" | Commercial/activity space |
| BUR | Office | TYPE_CODE = "BUR" | Office building |

**Used for**: Classifying property type in offers

### Future Dictionaries

Additional dictionaries can be added as needed (surface types, diagnostic codes, features, etc.).
See [ps_modules.md](../../specs/docs/ps_modules.md) for complete module catalog.

## Performance

- **First load**: Query database + populate cache
- **Subsequent loads**: O(1) cache retrieval
- **Cache duration**: Permanent (invalidated on entity changes)
- **Cache invalidation**: Automatic on entry save/delete, manual via
  `clearCache()`

## Testing

```bash
# Run all tests
vendor/bin/phpunit web/modules/custom/ps_dictionary

# Run unit tests only
vendor/bin/phpunit web/modules/custom/ps_dictionary/tests/src/Unit

# Run with coverage
vendor/bin/phpunit --coverage-html coverage web/modules/custom/ps_dictionary
```

## Integration

### Architecture Layer

**Foundation** - Provides infrastructure for all PropertySearch modules:
- Dictionary config entities
- DictionaryManager service
- Field storage configurations
- Validation and resolution APIs
- `ps_price`: Validates currencies, units, periods
- `ps_features`: Validates feature dictionary codes
- `ps_diagnostic`: Validates diagnostic types and statuses
- `ps_import`: Resolves CRM codes to canonical codes

### Events

- Cache invalidation triggers via `#[Hook('entity_insert')]`,
  `#[Hook('entity_update')]`
- PSR-14 `ConfigEvents::SAVE` and `ConfigEvents::DELETE` listeners
- Cache tags: `ps_dictionary:{type_id}`

## Configuration Export/Import

```bash
# Export config
drush cex -y

# Files exported to:
# - config/sync/ps_dictionary.type.*.yml
# - config/sync/ps_dictionary.entry.*.yml

# Import config
drush cim -y
```

## Extending

### Adding New Dictionary Types

1. Create config file: `config/install/ps_dictionary.type.my_type.yml`
2. Add entries: `config/install/ps_dictionary.entry.my_type.*.yml`
3. Clear cache: `drush cr`

### Custom Metadata

Define metadata schema in dictionary type with support for multiple field types:

```yaml
# ps_dictionary.type.currency.yml
metadata_schema: |
  symbol:
    type: string
    label: "Currency symbol (e.g., €, $)"
    translate: true
  iso_code:
    type: string
    label: "ISO 4217 code (e.g., EUR, USD)"
  decimal_places:
    type: integer
    label: "Number of decimal places"
  symbol_position:
    type: list_string
    label: "Position of symbol relative to amount"
    options:
      before: "Before amount (€100)"
      after: "After amount (100€)"
```

**Supported field types:**
- `string` - Text input field
- `textarea` - Multi-line text area
- `integer` - Number input
- `number` - Decimal number
- `checkbox` - Boolean toggle
- `select` - Single option dropdown (legacy)
- `list_string` - Single option dropdown with options (modern, recommended)

**Field options:**
- `label` - Human-readable label shown in admin forms
- `description` - Help text for the field
- `translate: true` - Marks field as translatable via Drupal Locale system
- `required` - Whether field is mandatory
- `options` - For select/list_string types, key-value pairs for dropdown options

Then set metadata values in entries:

```yaml
# ps_dictionary.entry.currency.EUR.yml
metadata:
  symbol: '€'
  iso_code: 'EUR'
  decimal_places: 2
  symbol_position: 'after'
```

```php
// Access in code
$entry = $this->dictionaryManager->getEntry('currency', 'EUR');
$metadata = $entry->getMetadata();
$symbol = $metadata['symbol'] ?? '';
$decimals = $metadata['decimal_places'] ?? 2;

// Use typed access for safe type coercion
$decimals = $entry->getMetadataTyped('decimal_places', 'int', 2);
// Automatically converts string '2' to int 2

// Or via manager
$decimals = $this->dictionaryManager->getMetadataTyped(
  'currency',
  'EUR',
  'decimal_places',
  'int',
  2
);
```

For comprehensive metadata documentation, see
[METADATA_SYSTEM.md](METADATA_SYSTEM.md).

#### Metadata Type Coercion

The metadata system supports automatic type conversion:

```php
// Stored as strings but retrieved typed
$entry->setMetadata([
  'validity_years' => '10',       // string
  'is_active' => 'true',          // string
  'multiplier' => '1.5',          // string
]);

// Type-safe retrieval
$years = $entry->getMetadataTyped('validity_years', 'int');      // Returns: 10 (int)
$active = $entry->getMetadataTyped('is_active', 'bool');         // Returns: true (bool)
$multi = $entry->getMetadataTyped('multiplier', 'float');        // Returns: 1.5 (float)
$items = $entry->getMetadataTyped('single', 'array');            // Returns: ['single'] (array)
```

Supported types: `string`, `int`, `float`, `bool`, `array`

## Troubleshooting

### Cache Issues

```bash
# Clear specific type
drush ps:dictionary-cache-clear property_type

# Clear all
drush ps:dictionary-cache-clear
drush cr
```

### Missing Entries

```bash
# Verify installation
drush ps:dictionary-list

# Show specific type
drush ps:dictionary-show property_type

# Reimport config
drush cim -y
```

## Architecture Compliance

- ✅ Drupal 11 ConfigEntityType with attributes
- ✅ OOP Hooks with #[Hook] attributes
- ✅ PSR-14 Event Subscribers
- ✅ Dependency Injection (no static service calls)
- ✅ PHP 8.3 promoted properties + strict types
- ✅ Config schema validation
- ✅ Cache tags + context management
- ✅ Comprehensive docblocks
- ✅ Unit + Kernel tests
- ✅ **Views integration** - Filters with dynamic options

## Views Integration

The module provides full Views integration for filtering by dictionary values.

**Features**:
- Automatic filter handler (`ps_dictionary_filter`) for all ps_dictionary fields
- Dynamic options loaded from dictionary manager
- Support for select, checkboxes, and radio widgets
- Intelligent caching with automatic invalidation

**See**: [VIEWS_INTEGRATION.md](VIEWS_INTEGRATION.md) for complete documentation.

**Quick Example**:
```yaml
# In a View, any ps_dictionary field automatically gets:
filters:
  field_property_type_value:
    plugin_id: ps_dictionary_filter  # Auto-applied
    # Options loaded from property_type dictionary: ACT, BUR, etc.
```

## API Reference

See:

- [DictionaryManagerInterface](src/Service/DictionaryManagerInterface.php) -
  Main service interface
- [DictionaryTypeInterface](src/Entity/DictionaryTypeInterface.php) - Type
  entity interface
- [DictionaryEntryInterface](src/Entity/DictionaryEntryInterface.php) - Entry
  entity interface

## License

Proprietary - PropertySearch Platform
