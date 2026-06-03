# BNP Editor - Architecture Documentation

## Overview

`bnp_editor` is a Drupal 11 custom module that provides centralized CKEditor 5 configuration and plugin management for the BNP Property Search platform.

## Design Principles

The module follows project standards defined in `.ai/PROJECT_RULES.md`:

- **Config-First**: All configurations are exportable and version-controlled
- **Dependency Injection**: Services are injected via constructor
- **Drupal Native**: Uses Drupal APIs before creating custom abstractions
- **Internationalization**: Full i18n support for 7 languages
- **Code in English**: All code identifiers, comments, and documentation in English
- **UI Translatable**: All user-facing strings pass through translation system

## Architecture Layers

```
┌─────────────────────────────────────────────────────┐
│              User Interface Layer                    │
│  - Admin forms                                       │
│  - Editor UI (CKEditor 5)                           │
│  - Configuration pages                              │
└─────────────────────────────────────────────────────┘
                        ↕
┌─────────────────────────────────────────────────────┐
│              Service Layer                           │
│  - EditorManager (configuration management)         │
│  - Plugin validation                                │
│  - Format management                                │
└─────────────────────────────────────────────────────┘
                        ↕
┌─────────────────────────────────────────────────────┐
│              Plugin Layer                            │
│  - CKEditor5 plugins                                │
│  - Custom toolbar items                             │
│  - Editor behaviors                                 │
└─────────────────────────────────────────────────────┘
                        ↕
┌─────────────────────────────────────────────────────┐
│              Configuration Layer                     │
│  - Text formats (filter.format.*)                   │
│  - Editor configs (editor.editor.*)                 │
│  - Module settings (bnp_editor.settings)            │
└─────────────────────────────────────────────────────┘
                        ↕
┌─────────────────────────────────────────────────────┐
│              Drupal Core APIs                        │
│  - Filter API                                       │
│  - Editor API                                       │
│  - CKEditor 5 API                                   │
└─────────────────────────────────────────────────────┘
```

## Module Structure

```
bnp_editor/
├── config/
│   ├── install/
│   │   └── bnp_editor.settings.yml
│   ├── optional/
│   │   ├── filter.format.full_html.yml
│   │   ├── filter.format.basic_html.yml
│   │   ├── filter.format.restricted_html.yml
│   │   ├── filter.format.plain_text.yml
│   │   ├── editor.editor.full_html.yml
│   │   └── editor.editor.basic_html.yml
│   └── schema/
│       └── bnp_editor.schema.yml
├── css/
│   └── bnp-editor-admin.css
├── js/
│   └── ckeditor5_plugins/
│       └── bnpExample/
│           └── bnp-example.js
├── src/
│   ├── Form/
│   │   └── BnpEditorSettingsForm.php
│   └── Service/
│       ├── EditorManager.php
│       └── BnpEditorRoleInstaller.php
├── tests/                    # PHPUnit tests
├── translations/             # Translation files (.po)
│   ├── fr.po
│   ├── nl.po
│   ├── es.po
│   ├── it.po
│   ├── lb.po
│   ├── pl.po
│   └── de.po
├── bnp_editor.info.yml      # Module metadata
├── bnp_editor.libraries.yml # Asset libraries
├── bnp_editor.links.menu.yml # Admin menu links
├── bnp_editor.module        # Module hooks
├── bnp_editor.permissions.yml # Permissions
├── bnp_editor.routing.yml   # Routes
├── bnp_editor.services.yml  # Service definitions
├── ARCHITECTURE.md          # This file
├── INSTALL.md              # Installation guide
└── README.md               # User documentation
```

## Core Components

### 1. EditorManager Service

**Purpose**: Centralized service for managing CKEditor configurations

**Responsibilities**:
- Load and validate editor configurations
- Provide configuration data to other services
- Log errors and events

**Dependencies**:
- `EntityTypeManagerInterface` — editor entity storage
- `LoggerInterface` — error logging

**Usage**:
```php
$editor_manager = \Drupal::service('bnp_editor.manager');
$configs = $editor_manager->getEditorConfigurations();
$is_valid = $editor_manager->validateEditorConfig('full_html');
```

### 2. BnpEditorRoleInstaller Service

**Purpose**: Grant text format permissions to BNP baseline roles (and PS roles when `ps_core` is present).

**Invoked from**: `hook_install()` and `bnp_editor_update_9001()`.

**Service id**: `bnp_editor.role_installer` (`public: true` — required for install/update hooks).

**Does not** create roles — only grants permissions on existing roles from `bnp_admin` / `ps_core`.

### 3. BnpEditorSettingsForm

**Route**: `/admin/config/content/bnp-editor`  
**Permission**: `administer bnp editor`

### 4. Custom CKEditor 5 plugins (future)

No PHP plugin classes are shipped yet. See `js/ckeditor5_plugins/bnpExample/` for a JavaScript example and use `hook_bnp_editor_plugins_alter()` to register plugins.

## Text Format Configuration

### Full HTML (`full_html`)

**Purpose**: Full-featured rich text format for trusted editors.

**Config files**: `config/optional/filter.format.full_html.yml`, `editor.editor.full_html.yml`

**Allowed HTML** (summary):
- Headings: h2-h6
- Text formatting: em, strong, cite, code
- Lists: ul, ol, li, dl, dt, dd
- Blocks: p, blockquote, br
- Links: a (with href, target, rel)
- Tables: table, caption, thead, tbody, tfoot, th, td, tr
- Images: img (with alt, data attributes)
- Generic: span, div

**CKEditor Toolbar**:
1. Text formatting: bold, italic
2. Links: link
3. Lists: bulletedList, numberedList
4. Content blocks: blockQuote, insertTable
5. Structure: heading (h2-h6)
6. Advanced: sourceEditing
7. Actions: undo, redo

**Filters**:
- `filter_html` - Restrict HTML tags
- `filter_align` - Image alignment
- `filter_caption` - Image captions
- `filter_html_image_secure` - Secure image URLs
- `filter_autop` - Line breaks to paragraphs
- `filter_htmlcorrector` - Fix broken HTML

## Extension Points

### Creating Custom Plugins

1. **Create plugin class**:
   - Location: `src/Plugin/CKEditor5Plugin/YourPlugin.php`
   - Extend: `CKEditor5PluginDefault`
   - Implement: `CKEditor5PluginConfigurableInterface` (optional)

2. **Create JavaScript**:
   - Location: `js/ckeditor5_plugins/yourPlugin/your-plugin.js`
   - Follow CKEditor 5 plugin structure

3. **Define library**:
   - In `bnp_editor.libraries.yml`
   - Include JS and CSS assets

4. **Add configuration schema**:
   - In `config/schema/bnp_editor.schema.yml`

5. **Clear cache**: `drush cr`

### Altering Editor Configuration

Other modules can alter configurations via hooks:

```php
/**
 * Implements hook_editor_settings_alter().
 */
function mymodule_editor_settings_alter(array &$settings, EditorInterface $editor): void {
  if ($editor->id() === 'full_html') {
    // Alter settings
  }
}

/**
 * Implements hook_ckeditor5_plugin_info_alter().
 */
function mymodule_ckeditor5_plugin_info_alter(array &$plugin_definitions): void {
  // Alter plugin definitions
}
```

## Security Considerations

### XSS Prevention

- All HTML is filtered through `filter_html`
- Only whitelisted tags and attributes allowed
- User input sanitized before display

### Permission Model

- **Administer BNP Editor**: Restricted to site administrators
- **Use text format ***: Granted per role via `BnpEditorRoleInstaller` (see README)

### Link Protocols

- Only configured protocols allowed (default: http, https, mailto, tel)
- Configurable via admin settings

## Performance

### Asset Loading

- JavaScript and CSS aggregation enabled
- Libraries loaded only when editor is active
- Minimal dependencies

### Caching

- Editor configurations cached by Drupal
- Plugin definitions cached by plugin system
- Cache invalidation on configuration change

## Internationalization

### Translation System

- All UI strings pass through `$this->t()` or `new TranslatableMarkup()`
- JavaScript strings use `Drupal.t()`
- YAML strings automatically translatable

### Supported Languages

1. French (fr)
2. Dutch (nl)
3. Spanish (es)
4. Italian (it)
5. Luxembourgish (lb)
6. Polish (pl)
7. German (de)

### Translation Workflow

1. Extract strings: `drupal locale:export`
2. Translate in .po files
3. Import: `drush locale:import [langcode] [file.po]`
4. Clear cache: `drush cr`

## Testing Strategy

### Unit Tests

Location: `tests/src/Unit/`

Coverage:
- Service logic
- Plugin configuration
- Validation logic

### Kernel Tests

Location: `tests/src/Kernel/`

Coverage:
- Configuration schemas
- Entity storage
- Service integration

### Functional Tests

Location: `tests/src/Functional/`

Coverage:
- Admin UI
- Editor rendering
- Permission checks

## Dependencies

### Required

- `drupal:ckeditor5` - CKEditor 5 core
- `drupal:editor` - Editor entity API
- `drupal:filter` - Filter API

### Optional

- `bnp_media` - For media embedding
- `entity_embed` - For entity embedding

## Future Enhancements

### Planned Features

1. **Custom image upload handler** - Optimized for BNP media workflow
2. **Template snippets** - Pre-defined content blocks
3. **Collaborative editing** - Real-time collaboration features
4. **Advanced table editor** - Enhanced table management
5. **Custom link widgets** - BNP-specific link types

### Plugin Ideas

- Property reference inserter
- Agent contact block
- Location map embed
- Document library browser

## Changelog

### Version 1.0.0 (2026-06-02)

- Initial release
- BNP Rich Text format
- EditorManager service
- Admin configuration form
- Example plugin structure
- Full i18n support (7 languages)
- Comprehensive documentation

## References

- [CKEditor 5 Documentation](https://ckeditor.com/docs/ckeditor5/)
- [Drupal CKEditor 5 API](https://www.drupal.org/docs/core-modules-and-themes/core-modules/ckeditor-5-module)
- [Filter API](https://www.drupal.org/docs/drupal-apis/filter-api)
- Project conventions: `.ai/PROJECT_RULES.md`
