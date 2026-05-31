# PS Migrate Module

## Overview

The **PS Migrate** module provides integration between Drupal's Migrate API and the **PS Core Entity Protection system**. It enables automated CRM/XML imports while respecting manual edits made in the backoffice.

## Key Features

- ✅ **Automatic conflict detection** using checksums
- ✅ **Source tracking** for all imported entities
- ✅ **Event-driven protection** via Migrate events
- ✅ **Protected entity logging** for auditing
- ✅ **Ready for merge strategies** (future destination plugin)
- ✅ **Feature XML parsing foundation** for `TECHNICAL_ELEMENTS_LIST` imports
- ✅ **Feature writers** for `fb_feature_group` and `fb_feature_definition`
- ✅ **Offer feature persistence** via `field_features` process mapping
- ✅ **Country-driven canonical source language** for offer body, availability, langcode and translation source metadata
- ✅ **Country-driven feature definition labels** resolved from each offer's parent country before deduplication

## Technical Specifications

- Staging one-pass pipeline specification (schema + state machine + Drush + BO MVP):
   `docs/PIPELINE_STAGING_ONE_PASS_SPEC.md`

## How It Works

### Architecture

```
[Source CRM/XML]
       ↓
[Migrate Source Plugin]
       ↓
[Process Plugins]
       ↓
PRE_ROW_SAVE Event ← EntityProtectionSubscriber
   - Check if entity is protected
   - Detect conflicts via checksum
   - Log warnings
       ↓
[Destination: Save Entity]
       ↓
POST_ROW_SAVE Event ← EntityProtectionSubscriber
   - Track source metadata
   - Update checksum
   - Log info
       ↓
[Entity Saved with Tracking]
```

### Event Subscriber

The `EntityProtectionSubscriber` listens to two Migrate events:

1. **PRE_ROW_SAVE**: Before entity save
   - Checks if entity exists and is protected (`field_internal_lock = TRUE`)
   - Compares checksums to detect conflicts
   - Logs warnings for protected entities
   - Sets `_is_protected` and `_has_conflict` flags on the row

2. **POST_ROW_SAVE**: After entity save
   - Tracks source metadata in `field_source_tracking` (JSON)
   - Computes and stores checksum in `field_source_checksum`
   - Logs successful tracking

## Installation

1. Enable dependencies:
   ```bash
   drush en migrate migrate_plus ps_core
   ```

2. Enable ps_migrate:
   ```bash
   drush en ps_migrate
   ```

3. Clear caches:
   ```bash
   drush cr
   ```

## Usage

### 1. Configure Your Migration

Copy the example migration from `config/examples/migrate_plus.migration.offer_import_crm_example.yml` and customize:

```yaml
source:
  plugin: url
  source_system: 'CRM_SYSTEM_X'  # ⬅️ IMPORTANT: For tracking
  urls:
    - 'https://crm.example.com/api/offers.xml'
```

Feature imports now use the dedicated `ps_feature_technical_elements` source plugin with two writers:

- `ps_feature_groups_from_xml`
- `ps_feature_definitions_from_xml`

The offer migration can then map `field_features` from the same XML technical elements.

Offer imports also compute a canonical source language per row from the offer country and the languages actually present in XML. That canonical language now drives:

- the source node `langcode`
- body extraction from `ML_DESCRIPTION_*`
- availability extraction from `ML_AVAILABILITY`
- `content_translation_source` on translation migrations
- skipping a translation migration when the target language is already the canonical source language

Feature group and definition migrations now reuse the same country-aware resolution logic from the parent offer XML node. When the same feature definition appears multiple times in a file, the first valid occurrence wins so a later duplicate cannot overwrite the canonical label chosen for that import pass.

When you change any `ps_offer*` migration YAML in `config/install`, reload the active migration config with:

```bash
drush scr scripts/tools/migrate-config.php
```

`drush cr` alone does not update existing active migration configuration.

### 2. Map Protection Fields

Ensure your migration process includes:

```yaml
process:
  field_source_checksum:
    plugin: callback
    callable: sha256
    source:
      - '@row'
  
  field_source_system:
    plugin: default_value
    default_value: 'CRM_SYSTEM_X'
```

### 3. Run the Migration

```bash
drush migrate:import offer_import_crm_example
```

### 4. Monitor Logs

Protected entities and conflicts are logged:

```bash
drush watchdog:show --type=ps_migrate
```

Example log output:
```
[warning] Migration offer_import_crm: Entity node:123 is protected - import will be skipped or merged
[warning] Migration offer_import_crm: Conflict detected for node:123
[info] Migration offer_import_crm: Tracking saved for node:124
```

## Protection Workflow

### Scenario 1: New Entity (No Protection)
1. Source data arrives via migration
2. PRE_ROW_SAVE: No existing entity → no protection check
3. Entity created with `field_internal_lock = FALSE`
4. POST_ROW_SAVE: Checksum and tracking stored

### Scenario 2: Unprotected Entity (Updated)
1. Source data arrives for existing entity
2. PRE_ROW_SAVE: Entity exists, `field_internal_lock = FALSE` → allowed
3. Checksum compared → conflict logged if different
4. Entity updated with new data
5. POST_ROW_SAVE: New checksum and tracking stored

### Scenario 3: Protected Entity (Manual Edits)
1. BO user modifies entity and enables `field_internal_lock = TRUE`
2. Next import: Source data arrives
3. PRE_ROW_SAVE: Entity protected → warning logged
4. **Currently**: Entity still overwritten (standard destination)
5. **Future**: Custom destination plugin will respect protection and apply merge strategies

## Field Mapping

The system handles both content entities and node bundles:

| Content Entity Field | Node Bundle Field | Purpose |
|----------------------|-------------------|---------|
| `internal_lock` | `field_internal_lock` | Protection flag (boolean) |
| `source_tracking` | `field_source_tracking` | JSON metadata (string_long) |
| `checksum` | `field_source_checksum` | SHA256 hash (string 64) |

## Future Enhancements

### Custom Destination Plugin with Merge Strategies

**Status**: Planned for Phase 4.2

The destination plugin `entity_protected` will allow per-field merge strategies:

```yaml
destination:
  plugin: 'entity_protected:node'
  merge_strategies:
    field_budget_value: 'EXTERNAL_WINS'      # CRM always overwrites
    field_commercial_title: 'INTERNAL_WINS'  # BO preserves if modified
    field_media_gallery: 'MERGE_APPEND'      # Merge arrays
```

Available strategies:
- **EXTERNAL_WINS**: Import data always overwrites
- **INTERNAL_WINS**: Preserve BO edits if entity is protected
- **MERGE_APPEND**: Merge arrays (for multi-value fields)
- **SKIP**: Skip entire entity if protected

### Rollback Protection

Protected entities will be excluded from migration rollbacks to prevent accidental deletion of manually-created content.

## Troubleshooting

### Entities Not Protected

**Problem**: Entities are being overwritten despite `field_internal_lock = TRUE`

**Solution**: Currently expected. The standard Migrate destination plugin does not respect protection. The custom destination plugin (future) will implement merge strategies.

**Workaround**: Check logs to verify conflicts are being detected. Use this data to manually reconcile protected entities.

### Checksum Always Changing

**Problem**: Conflicts detected on every import even when data hasn't changed

**Solution**: Ensure source data structure is consistent. The checksum is computed from the entire row. Changing field order or adding fields will change the checksum.

### Source Tracking Not Saved

**Problem**: `field_source_tracking` is empty after migration

**Solution**: 
1. Verify `source_system` is set in source configuration
2. Check that entity has `field_source_tracking` field
3. Review logs for errors in POST_ROW_SAVE event

## API Reference

### EntityProtectionSubscriber

**Service ID**: `ps_migrate.entity_protection_subscriber`

**Methods**:
- `onPreRowSave(MigratePreRowSaveEvent $event): void` - Check protection before save
- `onPostRowSave(MigratePostRowSaveEvent $event): void` - Track source after save

**Dependencies**:
- `@ps_core.entity_protection_manager` - Protection service
- `@entity_type.manager` - Entity loading
- `@logger.channel.ps_migrate` - Logging

## Testing

Run module tests:

```bash
vendor/bin/phpunit --group ps_migrate
```

Test a migration:

```bash
# Import with verbose logging
drush migrate:import offer_import_crm_example --feedback=10

# Check status
drush migrate:status

# Review logs
drush watchdog:show --type=ps_migrate --count=50

# Rollback if needed
drush migrate:rollback offer_import_crm_example
```

## Contributing

To extend ps_migrate:

1. **Add Process Plugins**: Create plugins in `src/Plugin/migrate/process/`
2. **Add Destination Plugins**: Create plugins in `src/Plugin/migrate/destination/`
3. **Add Event Subscribers**: Register in `ps_migrate.services.yml`
4. **Add Tests**: Create test cases in `tests/src/Kernel/`
5. **Add Feature Migrations**: Use `ps_feature_technical_elements` + the feature writers in `config/install/`

## Related Documentation

- [PS Core Entity Protection](../ps_core/README.md)
- [Implementation Plan](../../../docs/IMPLEMENTATION_PLAN_ENTITY_PROTECTION.md)
- [Entity Protection Audit](../../../docs/AUDIT_INTERNAL_EXTERNAL_SYSTEM.md)
- [Drupal Migrate API](https://www.drupal.org/docs/drupal-apis/migrate-api)

## License

GPL-2.0-or-later
