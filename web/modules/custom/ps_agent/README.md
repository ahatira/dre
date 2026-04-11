# Module ps_agent

> **Layer**: Business/Domain
> **Dependencies**: ps, ps_dictionary
> **Status**: Production Ready

## Overview

The `ps_agent` module provides a complete real estate agent management system with CRM synchronization and BO-protected fields. It handles agent entities with structured data, field protection rules, and administrative management interfaces.

### Key Features

- **Agent Entity**: Translatable content entity with 15+ base fields
- **CRM/BO Field Protection**: Configure which fields preserve BO edits during CRM imports
- **Agent Manager Service**: Centralized management, lookup, and business logic
- **Admin Interface**: Complete CRUD at `/admin/ps/content/agents` with Views
- **Agent Type Management**: Field UI and form customization
- **Settings Management**: Field protection rules at `/admin/ps/config/agents`
- **Access Control**: Complete permission model

## Architecture

```
ps_agent (Business Layer)
  ↓ depends on
ps (Foundation) + ps_dictionary (Foundation)
  ↓ depends on
Drupal Core
```

**Used by**: `ps_offer` (agent references), `ps_import` (CRM sync)

## Installation

```bash
# Enable the module
drush en ps_agent -y

# Install
drush site:install

# Clear cache
drush cr
```

## Entity Structure

### Agent Entity (`agent`)

**Base Fields**:
- `id` — Unique identifier
- `uuid` — Unique machine name
- `type` — Agent type (default: `default`)
- `langcode` — Language code (translatable)
- `external_id` — CRM system identifier
- `reference` — Internal reference code
- `first_name` — Agent first name
- `last_name` — Agent last name (label)
- `email` — Email address (BO-protected)
- `phone` — Phone number (BO-protected)
- `mobile` — Mobile number (BO-protected)
- `internal_notes` — Internal notes (BO-protected)
- `status` — Active status
- `created` — Creation timestamp
- `changed` — Last modification timestamp

### Agent Type Entity (`agent_type`)

Manages agent bundles with field configuration and form display settings.

## Services

### `ps_agent.manager` — AgentManager

Provides CRUD operations, lookups, and business logic:

```php
$manager = \Drupal::service('ps_agent.manager');

// Get all active agents
$agents = $manager->getActiveAgents();

// Lookup by external ID (CRM)
$agent = $manager->getAgentByExternalId('CRM-12345');

// Check if agent exists
$exists = $manager->agentExists('crm-id', 'value');

// Create new agent
$agent = $manager->createAgent('firstname', 'lastname', [
  'external_id' => 'CRM-12345',
  'email' => 'agent@example.com',
]);

// Update agent
$agent->setFirstName('Updated');
$manager->saveAgent($agent);
```

### `ps_agent.field_protector` — AgentFieldProtector

Manages BO-protected fields during CRM imports:

```php
$protector = \Drupal::service('ps_agent.field_protector');

// Check if field is BO-protected
$protected = $protector->isBoEditableField('email');

// Get protected fields list
$fields = $protector->getBoEditableFields();

// Get protected values (restore during CRM sync)
$original = $protector->getBoEditableValues($agent);
```

### `ps_agent.access_handler` — AgentAccessHandler

Manages access control:

```php
$handler = \Drupal::service('ps_agent.access_handler');

// Check permissions
$can_view = $handler->canViewAgent($agent);
$can_edit = $handler->canEditAgent($agent);
$can_delete = $handler->canDeleteAgent($agent);
```

## Admin Routes

- `/admin/ps/content/agents` — Agent list (Views)
- `/admin/ps/content/agents/add` — Add new agent
- `/admin/ps/content/agents/{agent}/edit` — Edit agent
- `/admin/ps/content/agents/{agent}/delete` — Delete agent
- `/admin/ps/structure/agents` — Agent type management (Manage fields, form display, etc.)
- `/admin/ps/config/agents` — Settings (field protection rules)

## Forms

### AgentForm (Add/Edit)

Standard entity form with validation:

```php
$form = \Drupal::formBuilder()->getForm(AgentForm::class, $agent);
```

### AgentDeleteForm

Confirmation form for deletion.

### AgentSettingsForm

Settings for field protection and CRM sync configuration:

- BO-protected fields selection
- CRM import rules
- Validation settings

## Views Integration

The module includes a default View for agent listing at `/admin/ps/content/agents`:

**Features**:
- Agent list with filters
- Sortable columns
- Bulk operations (edit, delete)
- Search by name, email, external ID
- Status filtering
- Pagination

## Permissions

- `administer agent entities` — Full access to agents
- `view agent entities` — View agents
- `create agent entities` — Create new agents
- `edit own agent entities` — Edit own agents
- `edit any agent entities` — Edit any agent
- `delete own agent entities` — Delete own agents
- `delete any agent entities` — Delete any agent

## CRM Integration

The module works with `ps_import` for CRM synchronization:

1. **Field Protection**: BO-protected fields (email, phone, etc.) are preserved
2. **External ID Lookup**: Agents are matched by `external_id` from CRM
3. **Update or Create**: Updates existing agents or creates new ones
4. **Validation**: All data validated against dictionaries

## Testing

Run tests with:

```bash
# Unit tests
vendor/bin/phpunit web/modules/custom/ps_agent/tests/src/Unit

# Kernel tests
vendor/bin/phpunit web/modules/custom/ps_agent/tests/src/Kernel

# All tests
vendor/bin/phpunit web/modules/custom/ps_agent/tests/
```

## Configuration Export

Settings are exported in:
- `config/install/ps_agent.settings.yml`
- `config/optional/views.view.agents.yml`

## Development

### Adding Fields

Use Drupal Field UI or add to `baseFieldDefinitions()` in Agent.php.

### Customizing Forms

Override `AgentForm` or hooks in `ps_agent.module`.

### Access Control

Extend `AgentAccessHandler` or modify in `AgentAccessControlHandler`.

## References

- [Drupal Entity API](https://api.drupal.org/api/drupal/11.x)
- [Drupal Views](https://drupal.org/docs/user_guide/en/views-chapter)
- [CRM Integration](specs/docs/ps_modules.md#ps_import--crm-xml-import)
- [Architecture Overview](web/modules/custom/ps/README.md)
