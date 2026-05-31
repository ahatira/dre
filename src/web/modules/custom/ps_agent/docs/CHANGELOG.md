# Changelog — ps_agent

All notable changes to this module will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

### Added
- Bundle entity `ps_agent_type` for agent type management
- Field UI integration with native Drupal field management
- Three view modes: `default`, `full`, `card`
- Computed field `display_name` (first_name + last_name concatenation)
- Computed field `has_avatar` (boolean check for avatar presence)
- Local tasks (tabs) for bundle management: Edit, Manage fields, Manage form display, Manage display, List
- Three image styles: `agent_avatar_sm` (64×64), `agent_avatar_md` (96×96), `agent_avatar_lg` (160×160)
- Admin Views list at `/admin/ps/content/agent` with exposed filters (civility, search)
- Bundle management routes at `/admin/ps/structure/agent`
- Update hook `ps_agent_update_10001()` for migration to bundle architecture
- Classes:
  - `Agent` entity (src/Entity/Agent.php)
  - `AgentType` bundle entity (src/Entity/AgentType.php)
  - `AgentTypeForm` (src/Form/AgentTypeForm.php)
  - `AgentTypeDeleteForm` (src/Form/AgentTypeDeleteForm.php)
  - `AgentTypeListBuilder` (src/AgentTypeListBuilder.php)
  - `DisplayNameItemList` computed field (src/Field/DisplayNameItemList.php)
- Configuration:
  - Default bundle: `ps_agent.ps_agent_type.default`
  - View modes: `core.entity_view_mode.ps_agent.full`, `core.entity_view_mode.ps_agent.card`
  - Entity view displays for all modes (default, full, card)
  - Views admin list: `views.view.ps_agent_admin`
  - Local tasks: `ps_agent.links.task.yml`
  - Permissions: `ps_agent.permissions.yml`

### Changed
- Entity annotation: Added `bundle_entity_type`, `bundle_label`, `field_ui_base_route`
- Entity keys: Added `bundle` key mapped to `type` field
- Field definitions: Set `setDisplayConfigurable('view', TRUE)` for all display-able fields
- Views configuration: Use `avatar__target_id` instead of `avatar` for image field
- Display configs: Add `region: content` to all fields in content array (Drupal 8.4+ requirement)
- Views display_name column: Use rewrite with `{{ first_name }} {{ last_name }}`
- Hidden fields in displays: `first_name`, `last_name` (replaced by `display_name`)
- Admin routes: Moved from `/admin/content/ps-agent` to `/admin/ps/content/agent`

### Fixed
- Views "Broken/missing handler" error for avatar field (use `avatar__target_id`)
- Fields appearing in "Disabled" section of Manage Display (add `region: content`)
- View modes not appearing in secondary tabs (rename from `entity.view_mode.*` to `core.entity_view_mode.*`)
- Missing Edit tab in bundle management (add `ps_agent.links.task.yml`)

### Deprecated
- Direct field management without Field UI (still possible via YAML but not recommended)

### Removed
- None (backward compatible with update hook)

## Development notes

### Version schema
This module follows Drupal module versioning: `{major}.{feature}.{patch}`
- Update hooks are numbered: `ps_agent_update_{drupal_version}{sequence}`
- Example: `ps_agent_update_10001` = Drupal 11.x, first update

### Breaking changes policy
- Update hooks ensure backward compatibility
- Config changes are additive when possible
- Deprecated features maintained for 1 major version

### Future roadmap
- [ ] Add hook_update for automated field config migration
- [ ] Create dedicated agent entity form (currently using generic entity form)
- [ ] Add bulk operations in Views admin list
- [ ] Implement agent search autocomplete widget for entity reference fields
- [ ] Add export/import functionality for agent data
- [ ] Create REST/JSON:API endpoints for agent entity
- [ ] Add entity revision support (optional)
- [ ] Integrate with CRM import system (ps_migrate)
