# ps_agent — Architecture Documentation

## Design decisions

### 1. Bundle entity architecture

**Decision**: Use ConfigEntityBundleBase for `ps_agent_type` instead of a fixed single-type entity.

**Rationale**:
- Enables Field UI integration out-of-the-box
- Allows future extension (e.g., different agent types: internal, external, partner)
- Provides native Drupal management UI at `/admin/ps/structure/agent`
- Supports per-bundle field configurations

**Trade-offs**:
- Slightly more complex setup (bundle entity + content entity)
- Requires migration path if upgrading from non-bundle version

### 2. Computed display_name field

**Decision**: Create a computed field `display_name` that concatenates `first_name` and `last_name`.

**Rationale**:
- Single field in display configs instead of two separate fields
- Consistent rendering across all view modes
- No need to manually concatenate in templates or formatters
- Cleaner Field UI experience

**Implementation**: `DisplayNameItemList` using `ComputedItemListTrait`

**Trade-offs**:
- Read-only field (not editable directly)
- Computed on every entity load (minimal performance impact)

### 3. Image base field vs Media reference

**Decision**: Use base field `image` type instead of entity_reference to Media.

**Rationale**:
- Simpler for agents (no need for full media entity management)
- Direct file attachment workflow
- Less storage overhead (no separate media entity)
- Faster rendering (no additional entity load)

**Trade-offs**:
- No media library integration
- No advanced media features (revisions, metadata, reusability)

### 4. View modes configuration

**Decision**: Three view modes (default, full, card) with pre-configured displays.

**Rationale**:
- `default`: General-purpose display for admin and listings
- `full`: Complete agent profile display
- `card`: Compact display for contact cards and sidebars

**Configuration**: All displays include `region: content` (required since Drupal 8.4).

### 5. Views admin list with avatar__target_id

**Decision**: Use `avatar__target_id` field identifier in Views instead of `avatar`.

**Rationale**:
- Views exposes image base fields with `__target_id` suffix
- This is the correct Views field identifier for image fields
- Direct reference to the file ID

**Gotcha**: Using `avatar` in Views configuration causes "Broken/missing handler" errors.

### 6. Field UI integration via field_ui_base_route

**Decision**: Set `field_ui_base_route: 'entity.ps_agent_type.edit_form'` in Agent entity annotation.

**Rationale**:
- Enables native Drupal Field UI
- Tabs appear at bundle edit route
- No custom field management UI needed
- Leverages core's mature field management

**Requirements**:
- `drupal:field_ui` dependency in ps_agent.info.yml
- Bundle entity must exist before Field UI routes work

## Entity relationship diagram

```
ps_agent_type (config entity)
    ↑ bundle_of
    |
ps_agent (content entity)
    ├── id: integer
    ├── uuid: uuid
    ├── type: entity_reference → ps_agent_type
    ├── first_name: string
    ├── last_name: string
    ├── display_name: string (computed)
    ├── civility: ps_dictionary → ps_dictionary entry
    ├── job_title: string
    ├── internal_external: list_string
    ├── email: email
    ├── phone: telephone
    ├── avatar: image → file
    ├── has_avatar: boolean (computed)
    ├── status: boolean
    ├── uid: entity_reference → user
    ├── created: timestamp
    └── changed: timestamp
```

## Route structure

```
/admin/ps/structure/agent
├── Collection (AgentTypeListBuilder)
├── /add → AgentTypeForm
└── /manage/{ps_agent_type}
    ├── → AgentTypeForm (Edit tab)
    ├── /fields → Field UI (Manage fields tab)
    ├── /form-display → Field UI (Manage form display tab)
    ├── /display → Field UI (Manage display tab)
    │   ├── /default (Default view mode)
    │   ├── /full (Full view mode)
    │   └── /card (Card view mode)
    └── /delete → AgentTypeDeleteForm

/admin/ps/content/agent
└── Views list (ps_agent_admin)
```

## Config export order

When exporting configuration, export in this order to maintain dependencies:

1. `ps_agent.ps_agent_type.default.yml` (bundle)
2. `core.entity_view_mode.ps_agent.full.yml` (view modes)
3. `core.entity_view_mode.ps_agent.card.yml`
4. `image.style.agent_avatar_sm.yml` (image styles)
5. `image.style.agent_avatar_md.yml`
6. `image.style.agent_avatar_lg.yml`
7. `core.entity_view_display.ps_agent.default.default.yml` (displays)
8. `core.entity_view_display.ps_agent.default.full.yml`
9. `core.entity_view_display.ps_agent.default.card.yml`
10. `views.view.ps_agent_admin.yml` (Views)

## Performance considerations

### Computed fields

- `display_name` and `has_avatar` are computed on every entity load
- Impact: Minimal (simple string concatenation and boolean check)
- Caching: Benefit from entity render cache

### Views query

- Admin list view queries the `ps_agent` base table
- Avatar images use image styles (cached derivatives)
- Exposed filters use indexed database columns

### Image styles

- Three image styles created on-demand
- Stored in `public://styles/agent_avatar_{sm|md|lg}/`
- Automatically cleared when original image changes

## Extension points

### Adding new agent types

```php
// Create via UI at /admin/ps/structure/agent/add
// Or programmatically:
$agent_type = \Drupal\ps_agent\Entity\AgentType::create([
  'id' => 'external_partner',
  'label' => 'External Partner',
]);
$agent_type->save();
```

### Adding fields via Field UI

1. Navigate to `/admin/ps/structure/agent/manage/{bundle}/fields`
2. Click "Add field"
3. Configure field (standard Drupal workflow)
4. Manage display at `/admin/ps/structure/agent/manage/{bundle}/display`

### Creating custom view modes

```yaml
# config/install/core.entity_view_mode.ps_agent.custom.yml
id: ps_agent.custom
label: Custom
targetEntityType: ps_agent
```

Then configure display at `/admin/ps/structure/agent/manage/{bundle}/display/custom`.

## Migration path

### From non-bundle version to bundle version

Update hook `ps_agent_update_10001()` performs:

1. Install `ps_agent_type` entity definition
2. Add `type` field storage to `ps_agent`
3. Set all existing agents to `type = 'default'`
4. Create default bundle config

```bash
drush updb -y
drush cr
```

## Known limitations

### Views display_name limitation

- Views cannot make the entire rewritten text (`{{ first_name }} {{ last_name }}`) clickable
- Only the primary field (first_name) is linkable with `make_link: true`
- Workaround: Use computed `display_name` field in views (future improvement)

### Field UI dependency

- Field UI tabs only appear when `drupal:field_ui` module is enabled
- Without Field UI, field management requires manual config YAML editing

## Testing strategy

### Unit tests

- Test computed fields (DisplayNameItemList)
- Test calculated fields (has_avatar in preSave)
- Test bundle entity CRUD operations

### Functional tests

- Test Field UI integration
- Test Views admin list filters
- Test view mode rendering

### Kernel tests

- Test entity field definitions
- Test bundle installation
- Test update hooks
