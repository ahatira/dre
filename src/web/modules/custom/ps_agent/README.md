# Module `ps_agent`

> Statut : 🟢 Stable

Entity directory for real-estate agents used by Property Search modules.

## Responsibility

`ps_agent` provides a content entity `ps_agent` with **bundle support** (`ps_agent_type`), native **Field UI**, and **View Modes** (default, full, card).

## Features

- Content entity `ps_agent` with bundle entity type support
- Bundle management: `/admin/ps/structure/agent`
- Field UI: `/admin/ps/structure/agent/manage/{bundle}/fields|display|form-display`
- Admin list view: `/admin/ps/content/agent` with exposed filters (civility, search)
- View modes: `default`, `full`, `card` (configurable displays)
- Computed field `display_name` (first_name + last_name)
- Base fields: `first_name`, `last_name`, `civility` (ps_dictionary), `email`, `phone`, `job_title`, `internal_external`, `avatar` (image), `has_avatar` (computed)
- Three avatar image styles: `agent_avatar_sm` (64x64), `agent_avatar_md` (96x96), `agent_avatar_lg` (160x160)

## Entity model

### Base fields

| Field | Type | Required | Description |
|---|---|---|---|
| `id` | `integer` | Auto | Entity ID |
| `uuid` | `uuid` | Auto | UUID |
| `type` | `entity_reference` | Yes | Bundle reference (ps_agent_type) |
| `first_name` | `string` (128) | Yes | First name |
| `last_name` | `string` (128) | Yes | Last name |
| `display_name` | `string` (computed) | No | First name + Last name (read-only) |
| `civility` | `ps_dictionary` | No | Civility (Mr/Mrs/etc.) |
| `job_title` | `string` (128) | No | Job title |
| `internal_external` | `list_string` | No | Internal or external agent |
| `email` | `email` | No | Contact email |
| `phone` | `telephone` | No | Contact phone |
| `avatar` | `image` | No | Avatar image (base field) |
| `has_avatar` | `boolean` (computed) | No | True if avatar is set |
| `status` | `boolean` | Yes | Published status (default: TRUE) |
| `uid` | `entity_reference` | Yes | Authored by (user) |
| `created` | `timestamp` | Auto | Creation timestamp |
| `changed` | `timestamp` | Auto | Last modification timestamp |

### Bundle entity type

`ps_agent_type` (ConfigEntityBundleBase):
- Default bundle: `default` (label: "Agent")
- Routes: `/admin/ps/structure/agent`
- Manage: `/admin/ps/structure/agent/manage/{bundle}`

## Admin interface

### Routes

- **Agent types list**: `/admin/ps/structure/agent` (bundle management)
- **Edit bundle**: `/admin/ps/structure/agent/manage/{bundle}` (tabs: Edit / Manage fields / Manage form display / Manage display / List / Devel)
- **Agents list**: `/admin/ps/content/agent` (Views admin list with filters)

### Admin list view

View `views.view.ps_agent_admin` at `/admin/ps/content/agent`:

**Visible columns**:
- Avatar (image, `avatar__target_id`, style: agent_avatar_sm)
- Display name (rewrite: `{{ first_name }} {{ last_name }}`, linked to edit page)
- Email (mailto formatter)
- Phone (telephone_link formatter)
- Status (published/unpublished toggle)

**Hidden columns**: Civility, Job title, Internal or external (can be exposed via Field UI)

**Exposed filters**:
- Civility (ps_dictionary exposed filter)
- Search (combine first_name + last_name + email)
- Has avatar (boolean)
- Status (boolean)

**Operations**:
- **Edit**: Click on agent name or use edit icon
- **Delete**: Click delete icon in operations column

**Note**: Views exposes image base fields under `avatar__target_id`, not `avatar`.

### Bulk operations

Bulk publish/unpublish/delete operations can be performed using:

1. **Admin UI**: Edit individual agents at `/admin/ps/content/agent/{id}/edit` or delete via icon
2. **Drush eval** for custom bulk queries (examples):
   ```bash
   # Publish all unpublished agents
   drush eval "
   \$query = \Drupal::entityTypeManager()->getStorage('ps_agent')->getQuery()
     ->condition('status', 0);
   \$ids = \$query->execute();
   foreach (\Drupal\ps_agent\Entity\Agent::loadMultiple(\$ids) as \$agent) {
     \$agent->set('status', 1)->save();
   }
   \$count = count(\$ids);
   \Drupal::logger('ps_agent')->info('Published @count agents', ['@count' => \$count]);
   "
   
   # Delete all external agents
   drush eval "
   \$query = \Drupal::entityTypeManager()->getStorage('ps_agent')->getQuery()
     ->condition('internal_external', 'EXTERNAL');
   \$ids = \$query->execute();
   entity_delete_multiple('ps_agent', \$ids);
   "
   ```

## View modes

Three configurable display modes:

| Mode | Avatar style | display_name label | Fields shown |
|---|---|---|---|
| `default` | agent_avatar_md (96x96) | Hidden | Avatar, display_name, civility, job_title, internal_external, email, phone, status |
| `full` | agent_avatar_lg (160x160) | Above | All fields + uid + created |
| `card` | agent_avatar_sm (64x64) | Hidden | Avatar, display_name, job_title, email, phone |

Fields `first_name` and `last_name` are hidden in all modes (replaced by computed `display_name`).

## Avatar image styles

Three image styles are defined for agent avatars:

- `agent_avatar_sm`: 64×64px (card mode, Views list)
- `agent_avatar_md`: 96×96px (default mode)
- `agent_avatar_lg`: 160×160px (full mode)

All styles use scale and crop effect.

## Computed fields

### DisplayNameItemList

`src/Field/DisplayNameItemList.php`

Computes `display_name` by concatenating `first_name` and `last_name`:

```php
protected function computeValue() {
  $entity = $this->getEntity();
  $first = $entity->get('first_name')->value ?? '';
  $last = $entity->get('last_name')->value ?? '';
  $display_name = trim($first . ' ' . $last);
  $this->list[0] = $this->createItem(0, $display_name);
}
```

### has_avatar (calculated field)

`has_avatar` is a boolean field automatically calculated in `preSave()` based on whether the avatar field is empty:

```php
// In Agent::preSave()
public function preSave(EntityStorageInterface $storage): void {
  parent::preSave($storage);
  $this->set('has_avatar', !$this->get('avatar')->isEmpty());
}
```

This is not a computed field (ComputedItemListTrait) but a regular boolean field updated before save.

## Architecture

| Element | Class/File | Role |
|---|---|---|
| Content entity | `src/Entity/Agent.php` | Main ps_agent entity with bundle support |
| Bundle entity | `src/Entity/AgentType.php` | Config entity for bundle management |
| Bundle form | `src/Form/AgentTypeForm.php` | Add/edit bundle form |
| Bundle delete form | `src/Form/AgentTypeDeleteForm.php` | Delete bundle confirmation |
| Bundle list builder | `src/AgentTypeListBuilder.php` | Bundle types list at /admin/ps/structure/agent |
| Computed field | `src/Field/DisplayNameItemList.php` | Computes display_name |
| Routes | `src/Routing/AgentHtmlRouteProvider.php` | Bundle-aware add form routing |
| Routes | `src/Routing/AgentTypeHtmlRouteProvider.php` | Config entity routing (disables config_translation) |
| Views integration | `config/install/views.view.ps_agent_admin.yml` | Admin list view |
| Local tasks | `ps_agent.links.task.yml` | Tabs for bundle management |

## Permissions

- `administer ps agent types`: Manage agent type bundles (required for Field UI access)

Field UI permissions are inherited from Drupal core (`administer {entity_type} display`, etc.).

## Usage examples

### Load and display an agent

```php
use Drupal\ps_agent\Entity\Agent;

// Load agent
$agent = Agent::load($agent_id);

// Get computed display name
$display_name = $agent->get('display_name')->value;

// Check if has avatar
$has_avatar = $agent->get('has_avatar')->value;

// Render with view mode
$view_builder = \Drupal::entityTypeManager()->getViewBuilder('ps_agent');
$build = $view_builder->view($agent, 'card');
```

### Render in Twig

```twig
{# Using drupal_entity #}
{{ drupal_entity('ps_agent', agent_id, 'card') }}

{# Accessing computed field #}
{{ agent.display_name.value }}
```

### Create an agent programmatically

```php
use Drupal\ps_agent\Entity\Agent;

$agent = Agent::create([
  'type' => 'default',
  'first_name' => 'John',
  'last_name' => 'Doe',
  'email' => 'john.doe@example.com',
  'phone' => '+33123456789',
  'job_title' => 'Sales Manager',
  'internal_external' => 'INTERNAL',
  'status' => TRUE,
]);
$agent->save();
```

## Dependencies

- `drupal:field_ui` — Native Field UI integration
- `ps_core` — PS Project core module
- `ps_dictionary` — For civility field type

## Installation notes

When installing, the module creates:
- Bundle entity type `ps_agent_type`
- Default bundle `default` (label: "Agent")
- Three image styles (agent_avatar_sm/md/lg)
- Three view modes (default, full, card)
- Admin Views list at `/admin/ps/content/agent`

If migrating from a version without bundles, run update hook `ps_agent_update_10001()` to add the `type` field and create the default bundle.

## Technical documentation

For detailed technical information:
- [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) — Design decisions, entity relationships, performance considerations
- [docs/CHANGELOG.md](docs/CHANGELOG.md) — Version history and migration notes
