# PropertySearch Core Module (ps)

**Foundation infrastructure for PropertySearch Drupal 11 platform**

## Overview

The `ps` module is the core foundation of PropertySearch, providing essential services for settings management, health monitoring, validation, and notifications. It establishes a clean, service-oriented architecture pattern for all other modules to follow.

## Architecture

### 2 Core Services

1. **SettingsManager** (`ps.settings`)
   - Centralized configuration management
   - Dot notation support (`validation.strictMode`)
   - Persistent storage via Drupal config
   - Used by: all modules

2. **NotificationManager** (`ps.notification`)
   - Multi-channel notification system
   - Email validation and sending
   - Extensible for SMS, webhooks, etc.
   - Logging and error handling
   - Used by: alerts, user notifications, ps_dictionary

## Installation

Enable the module:

```bash
drush en ps
```

## Usage

### Settings Manager

```php
/** @var Drupal\ps\Service\SettingsManagerInterface $settings */
$settings = \Drupal::service('ps.settings');

// Get setting with dot notation
$strictMode = $settings->get('validation.strictMode', FALSE);

// Set setting
$settings->set('validation.strictMode', TRUE);

// Get all settings
$allSettings = $settings->getAll();
```

### Notifications

```php
/** @var Drupal\ps\Service\NotificationManagerInterface $notifier */
$notifier = \Drupal::service('ps.notification');

// Send email
$notifier->send('email', 'user@example.com', 'Subject', 'Body text');

// Validate email
$valid = $notifier->validateEmail('test@example.com');

// Get available channels
$channels = $notifier->getChannels();
```

## Field Type Categories

The `ps` module defines a custom field type category **"Property Search"** (`propertysearch`) to group all PropertySearch custom field types in the Drupal field UI.

### Configuration

Defined in `ps.field_type_categories.yml`:

```yaml
propertysearch:
  label: 'Property Search'
  description: 'Custom field types for PropertySearch platform'
  weight: 10
  libraries:
    - ps/admin
```

### Custom Icons

The category displays a custom house icon in:
- **Field selection UI** (full page and AJAX modals)
- **Admin toolbar** (Gin theme integration)

Icons are defined in:
- `images/field-category-icon.svg` (primary house icon)
- `images/field-category-icon-building.svg` (alternative)

### Library Loading

The `ps/admin` library is automatically attached to admin pages via `PsAdminHooks`:

```php
// src/Hook/PsAdminHooks.php

#[Hook('page_attachments')]
public function pageAttachments(array &$attachments): void {
  // Loads on admin routes or users with toolbar permission
}

#[Hook('form_alter')]
public function formAlter(array &$form, FormStateInterface $form_state, string $form_id): void {
  // Attaches to field_ui forms for AJAX modal support
}
```

### Styling

Custom CSS in `assets/css/ps-admin.css`:

```css
/* Field category icon in field selection grid */
.field-icon-propertysearch {
  background-image: url('../images/field-category-icon.svg');
  background-size: 36px 36px;
}

/* Toolbar icon (Gin theme) */
.toolbar .toolbar-bar .toolbar-icon-ps-admin::before {
  background-image: var(--icon);
}
```

### Field Types Using This Category

All PropertySearch field types use `category: 'propertysearch'`:
- `ps_price` (Price field with currency)
- `ps_surface` (Surface field with unit)
- `ps_feature_value` (Feature with dynamic value types)
- `ps_dictionary` (Dictionary code reference)

## Admin Routes

- `/admin/ps` — Dashboard (requires: `access ps admin`)
- `/admin/ps/config` — Settings form (requires: `configure ps settings`)
- `/admin/ps/structure` — Structure configuration

## Configuration

Default settings in `config/install/ps.settings.yml`:

```yaml
validation:
  strictMode: true
  enableLogging: true

notifications:
  enabled: true
  channels:
    - email
```

Edit via admin form at `/admin/ps/config` or drush:

```bash
drush config:set ps.settings validation.strictMode 1
```

## Testing

Run unit tests:

```bash
vendor/bin/phpunit web/modules/custom/ps/tests/src/Unit/
```

All services have unit test coverage.

## Development

### Service Registration

All services must be registered in `ps.services.yml` with clear dependencies and interfaces.

### Coding Standards

- Drupal 11 coding standards
- Strict type hints on all methods
- PHPDoc for all public methods
- 100% unit test coverage for services
- PHPCS and PHPStan validation

### Adding a New Service

1. Create interface in `src/Service/YourServiceInterface.php`
2. Create implementation in `src/Service/YourService.php`
3. Register in `ps.services.yml`
4. Create tests in `tests/src/Unit/Service/YourServiceTest.php`
5. Run QA: `composer phpcs`, `composer phpstan`, `composer test:unit`

## Dependencies

- drupal:system
- drupal:config
- drupal:logger
- egulias/email-validator (for email validation)

## Module Dependencies

The `ps` module is foundational and has NO dependencies on other PropertySearch modules. All other modules depend on `ps`.

## License

See LICENSE.txt in root directory.

## Author

PropertySearch Development Team
