# BNP Editor - Installation Guide

## Prerequisites

- Drupal 11.x
- CKEditor 5 (included in Drupal core)
- PHP 8.2 or higher

## Installation Steps

### 1. Enable the module

Via Drush:
```bash
cd /path/to/drupal/root
drush en bnp_editor -y
drush cr
```

Via UI:
1. Navigate to `/admin/modules`
2. Search for "BNP Editor"
3. Check the checkbox
4. Click "Install"

### 2. Import translations

For each language you need:

```bash
# French
drush locale:import fr modules/custom/bnp_editor/translations/fr.po --type=customized --override=all

# Dutch
drush locale:import nl modules/custom/bnp_editor/translations/nl.po --type=customized --override=all

# Spanish
drush locale:import es modules/custom/bnp_editor/translations/es.po --type=customized --override=all

# Italian
drush locale:import it modules/custom/bnp_editor/translations/it.po --type=customized --override=all

# Luxembourgish
drush locale:import lb modules/custom/bnp_editor/translations/lb.po --type=customized --override=all

# Polish
drush locale:import pl modules/custom/bnp_editor/translations/pl.po --type=customized --override=all

# German
drush locale:import de modules/custom/bnp_editor/translations/de.po --type=customized --override=all
```

### 3. Configure text formats

The module automatically installs the "BNP Rich Text" format. To use it:

1. Navigate to `/admin/config/content/formats`
2. Verify "BNP Rich Text" is enabled
3. Assign appropriate roles to the format

### 4. Configure BNP Editor settings

1. Navigate to `/admin/config/content/bnp-editor`
2. Configure:
   - Custom plugins (enabled/disabled)
   - Media embed (enabled/disabled)
   - Allowed link protocols

### 5. Assign permissions

Navigate to `/admin/people/permissions` and assign:

- **Administer BNP Editor**: For administrators who need to configure editor settings
- **Use BNP Rich Text format**: For users who should use the rich text editor

### 6. Test the installation

1. Create or edit content that uses a text format
2. Select "BNP Rich Text" as the format
3. Verify that CKEditor 5 loads with the configured toolbar
4. Test various formatting options (bold, italic, lists, links, tables)

## Configuration Export

After configuring the module, export configuration:

```bash
drush cex -y
```

This ensures your configuration is version-controlled and deployable.

## Updating

To update the module:

1. Replace module files
2. Run updates: `drush updb -y`
3. Clear cache: `drush cr`
4. Import configuration: `drush cim -y` (if needed)

## Troubleshooting

### CKEditor doesn't load

1. Clear cache: `drush cr`
2. Verify JavaScript aggregation is working
3. Check browser console for errors
4. Verify filter format is properly configured

### Translations missing

1. Verify .po files are in `/translations` folder
2. Re-import using `drush locale:import`
3. Clear cache: `drush cr`

### Custom plugins not appearing

1. Verify plugin class is in `src/Plugin/CKEditor5Plugin/`
2. Clear cache: `drush cr`
3. Check that plugin annotation is correct
4. Verify library dependencies are defined

## Uninstallation

To remove the module:

```bash
# Export content using the format first
drush en content_translation -y

# Uninstall module
drush pmu bnp_editor -y

# Remove configuration
drush config:delete filter.format.bnp_rich_text
drush config:delete editor.editor.bnp_rich_text
drush config:delete bnp_editor.settings

# Clear cache
drush cr
```

## Support

For issues or questions, consult:
- Module README.md
- Module ARCHITECTURE.md
- Project documentation in `/docs`
