# Changelog

All notable changes to the BNP Editor module will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed
- Text formats: standards Drupal (`full_html`, `basic_html`, …) via `config/optional/` — plus de format `bnp_rich_text`
- Permissions: `BnpEditorRoleInstaller` accorde les droits aux rôles `bnp_admin` + `ps_admin` / `ps_content_editor` si `ps_core` actif
- `hook_requirements`: seuls `blazy` / `slick` signalés comme enhancements optionnels
- Documentation alignée (README, INSTALL, QUICKSTART, API examples)
- `install.sh`: `bnp_editor` activé après `bnp_admin`, avant les modules PS

### Added
- `bnp_editor_update_9001()` — ré-application des permissions sur sites existants
- Tests `BnpEditorRoleInstallerTest`

### Removed
- Références au rôle legacy `editor` et à la permission `use bnp rich text`

### Planned
- Custom image upload handler for BNP media workflow
- Template snippets for pre-defined content blocks
- Collaborative editing features
- Advanced table editor
- Custom link widgets for BNP-specific link types

## [1.0.0] - 2026-06-02

### Added
- Initial module release
- `EditorManager` service for centralized CKEditor configuration management
- `BnpEditorSettingsForm` for admin configuration
- CKEditor 5 configuration for standard Drupal text formats
- Example CKEditor 5 plugin structure (JS under `js/ckeditor5_plugins/`)
- Complete internationalization support for 7 languages:
  - French (fr)
  - Dutch (nl)
  - Spanish (es)
  - Italian (it)
  - Luxembourgish (lb)
  - Polish (pl)
  - German (de)
- Permissions system:
  - `administer bnp editor` - For administrators
  - `use text format *` - Granted per role via installer
- Configuration schemas for all settings
- Comprehensive documentation:
  - README.md - User guide
  - INSTALL.md - Installation instructions
  - ARCHITECTURE.md - Technical documentation
  - CHANGELOG.md - Version history
- Admin UI at `/admin/config/content/bnp-editor`
- Asset libraries for custom plugins and admin styles
- Service definitions with dependency injection
- Example JavaScript plugin structure
- Admin CSS styling

### Technical Details
- Config-First architecture (all settings exportable)
- Dependency Injection for all services
- Drupal native APIs (Filter, Editor, CKEditor 5)
- Full code in English with translatable UI
- Follows project standards defined in `.ai/PROJECT_RULES.md`

### Configuration
- Default text formats: Drupal standards (full_html, basic_html, …)
- Default toolbar: bold, italic, link, lists, blockquote, table, heading, sourceEditing, undo/redo
- Default filters: HTML restriction, image alignment, captions, autop, HTML corrector
- Default settings: custom plugins enabled, media embed enabled, protocols (http, https, mailto, tel)

### Dependencies
- drupal:ckeditor5 (^11)
- drupal:editor (^11)
- drupal:filter (^11)

## [Unreleased - Backlog]

### Ideas for Future Releases

#### 2.0.0 (Major)
- Breaking changes to plugin API if needed
- Migration path from CKEditor 4 (if upgrading from Drupal 9)

#### 1.1.0 (Minor)
- Property reference inserter plugin
- Agent contact block plugin
- Location map embed plugin
- Document library browser integration

#### 1.0.1 (Patch)
- Bug fixes
- Translation updates
- Documentation improvements

---

## Version Guidelines

### Version Numbering

- **MAJOR** (X.0.0): Breaking changes, incompatible API changes
- **MINOR** (1.X.0): New features, backward-compatible
- **PATCH** (1.0.X): Bug fixes, backward-compatible

### Release Process

1. Update CHANGELOG.md with changes
2. Update version in bnp_editor.info.yml
3. Export configuration: `drush cex`
4. Run tests: `drush test-run`
5. Tag release: `git tag v1.0.0`
6. Create release notes

### Deprecation Policy

- Deprecated features will be marked with `@deprecated` annotation
- Deprecations will remain for at least one minor version
- Removal will happen in next major version

---

[Unreleased]: https://github.com/yourorg/ps_project/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/yourorg/ps_project/releases/tag/v1.0.0
