---
description: "Use when working on Drupal theme files in ui_suite_bnppre (Twig, SCSS, theme hooks, libraries, icons, or theme build scripts)."
name: "UI Suite BNP PRE Theme Guidelines"
applyTo: "web/themes/custom/ui_suite_bnppre/**"
---

# UI Suite BNP PRE Theme Guidelines

- Treat this path as source-of-truth for project customization: `web/themes/custom/ui_suite_bnppre/`.
- Follow Drupal 11 OOP theme hook patterns in `src/Hook/`; keep business logic out of templates.
- Do not edit generated assets directly (`assets/css/**`, generated component CSS). Update SCSS or source files and rebuild.

## Build Workflow

- Run theme commands from `web/themes/custom/ui_suite_bnppre/`.
- Install dependencies with `npm install`.
- Use `npm run build` for full asset pipeline, or `npm run build:css` for SCSS-only changes.
- For icon updates, run `npm run build:bnppre-icons:check`.
- For theme metadata consistency, run `npm run build:theme-yaml:check`.
- After theme/library/config-impacting changes, clear caches with `vendor/bin/drush cr` from repo root.

## Theme Conventions

- Prefer dependency injection in PHP classes; avoid introducing new direct service-locator usage in business logic.
- Keep library overrides deliberate and documented because this theme overrides multiple core/contrib libraries.
- For icon changes, follow naming and normalization contract before committing SVG updates.

## SDC Genericity Doctrine

This theme MUST remain a generic Drupal theme — no coupling to project-specific entities or business logic.

**Allowed entity references** (Drupal defaults only): `node`, `user`, `media`, `taxonomy_term`, `comment`, `block`, `views` — in generic rendering context only.

### MUST NOT
- Use `if bundle == '...'` or `if node.bundle == '...'` in Twig or PHP preprocess.
- Reference project-specific fields: `content.field_offer_*`, `entity.field_project_*`, etc.
- Hardcode business routes in the theme layer.
- Put business/entity logic in `src/Hook/*` — hooks are for rendering structure and styling only.
- Create bundle-specific templates (`node--article.html.twig`, `taxonomy-term--product_category.html.twig`) as a theming base.

### SHOULD
- Use generic templates (`node.html.twig`, `taxonomy-term.html.twig`) and View Modes for content variation.
- Isolate any project-specific integration in an optional, documented module layer — never in the theme.

## SDC Component Contract

Every SDC component in `components/` must conform to this contract.

### Structure
- One component per folder: `components/<component_name>/`
- Component name in `snake_case`.
- Required files: `<component_name>.component.yml` and `<component_name>.twig`.
- Optional sub-folders: `js/`, `styles/`, `stories/`.

### YAML (`*.component.yml`) — MUST
- Declare `name`, `description`, `group`, `links`, `props`.
- `props` must be `type: object` with explicit `properties`.
- Use UI Patterns refs where applicable: `ui-patterns://attributes`, `ui-patterns://links`, `ui-patterns://url`, `ui-patterns://identifier`.
- Expose booleans as `type: boolean`; enums via `enum` (+ `meta:enum` when needed).
- Declare `libraryOverrides` (minimum `css: {}`) to support sub-theme overrides.
- All slots rendered in Twig MUST be declared in YAML. Never render an undeclared slot.

### YAML — MUST NOT
- No business props (`node_bundle`, `field_machine_name`, `product_type`, etc.).
- No dependency on a specific entity schema.

### Slots — MUST
- Slot names must be semantic and generic: `content`, `items`, `header`, `footer`, `title`, `body`.
- Never impose a specific entity field to feed a slot — slots accept any renderable.

## Twig Conventions

### Attributes — MUST
- Manipulate attributes via Drupal/Twig API only: `attributes.addClass(...)`, `attributes.setAttribute(...)`.
- Initialize nested attributes with `create_attribute(...)` before mutation.
- Apply variant classes deterministically via explicit Bootstrap class mapping.

### Composition — MUST
- Include sub-components in isolated context: `include('ui_suite_bootstrap:<component>', {...}, with_context = false)`.
- Pass all required interface variables explicitly when including.

### SHOULD
- Normalize collection inputs (single item vs list) before looping.
- Keep Twig focused on structure/rendering — no data logic or business conditions.

## Slot Rendering Rules

### Slot Taxonomy
- **Text slot**: `string` or simple markup — render directly with `{{ slot }}`.
- **Render array slot**: Drupal renderable array — render directly with `{{ slot }}` (Drupal handles rendering).
- **Sequence slot**: array of renderables — render via loop or direct `{{ slot }}`.
- **Structural slot**: composed of nested `#type: component` render arrays.
- **Links slot**: use `ui-patterns://links` schema for structured nav/dropdown link lists.

### MUST NOT
- Mix raw strings and render arrays uncontrollably in the same slot.
- Pass a complex render array into a slot declared as plain text.

### Family Rules

**Table family** (`table`, `table_row`, `table_cell`):
- `table.header` → sequence of `table_cell` components.
- `table.rows` → sequence of `table_row` components.
- `table_row.cells` → sequence of `table_cell` components.
- `table_cell.content` → final cell content (markup/render array).
- Cell count per row MUST match header column count.
- Keep sort/active state in `#props` (attributes, tag, active); cell content in `#slots.content`.

**List / List Group family** (`list`, `list_group`, `list_group_item`):
- `list.items` → sequence of renderables.
- `list_group.items` → preferably a sequence of `list_group_item` components.
- `list_group_item.content` → text, markup, or render array.
- Convert Drupal `item_list` / `links` to `list_group_item` in preprocess when uniform Bootstrap rendering is needed.

**Nav / Dropdown / Navbar family** (`nav`, `dropdown`, `navbar`, `navbar_nav`):
- `nav.items` (prop) → normalized links schema.
- `nav.tab_content` (slot) → renderable sequence aligned with `nav.items` — **cardinality MUST match**: `count(items) == count(tab_content)`.
- `dropdown.title` → text slot; `dropdown.content` (prop) → links schema.
- Perform link normalization in PHP preprocess Hook, not inside component Twig.

**Dialog family** (`modal`, `offcanvas`, `toast`, `toast_container`):
- `modal.title` / `offcanvas.title` → text only (for proper heading/ARIA rendering).
- `modal.body`, `modal.footer`, `offcanvas.body`, `toast.content` → renderables.
- `toast_container.items` → sequence of toast renderables.

**Card / Grid family**:
- `card.content`, `card.header`, `card.footer`, `card.image` → renderable slots.
- `grid_row.content` → sequence of column contents.
- `grid_row_2/3/4` → fixed per-column slots (`col_1_content`, etc.).
- Use slots for layout content, props for structure options (classes, breakpoints, attributes).

### Field → Slot Pipeline — MUST follow this order
1. Raw Drupal data (field / link / table / views row).
2. Preprocess normalization → structural props + content slots.
3. Build `#type: component` render array.
4. Inject: `#props` for behavior/structure, `#slots` for renderable content.
5. Twig renders declaratively (print / loop / include).

**Views table** (`views-view-table.html.twig`): prepare `preparedContent` in preprocess, inject into `#slots.content` of `table_cell`. Keep Views field classes (`views-field-*`) on cell attributes, not inside the slot.

**Links / Dropbutton**: normalize in PHP Hook, then map to nav/dropdown props. Keep structural transformation in PHP — Twig stays declarative.

### Strict Anti-bug Constraints — MUST
- Rename or remove a slot → update YAML + Twig + all preprocess call sites simultaneously.
- Slot/Twig contract must stay in sync: every slot rendered in Twig must exist in YAML.
- Slot type discipline: text slots must not receive complex structural payloads.
- Never make slot mapping depend on a bundle or a business field.

## Anti-Patterns (Absolute MUST NOT)

- `node--article.html.twig`, `node--offer.html.twig`, bundle-specific templates as theming base.
- `if node.bundle == 'x'` or `if content.field_metier_*` conditions in Twig.
- Hardcoded business field access (`content.field_offer_price`, `entity.field_project_status`, etc.).
- Components that expect a specific entity instead of generic props/slots.
- Business behavior in the theme that belongs in a custom module.

## Pre-Merge Checklist (ui_suite_bnppre)

1. Search for any bundle/field references in `src/`, `templates/`, `components/`.
2. Verify all components require only generic props/slots.
3. Verify hooks alter structure/styling — not business logic.
4. Verify SDC/UI Patterns compatibility: `#type: component`, stable props, robust slots.
5. Verify Layout Builder compatibility: no entity coupling, editorial layer only.
6. Verify a11y: ARIA roles, translated labels (`|t`), `visually-hidden` where needed.

## Reference Docs

- **SDC master ruleset**: `UI-SDC.md` (root) — authoritative reference for all SDC/component/slot/genericity rules.
- Developer docs index: `web/themes/custom/ui_suite_bnppre/docs/README.md`
- Architecture (regions, dependencies, CSS variants): `web/themes/custom/ui_suite_bnppre/docs/architecture.md`
- Build workflow and SCSS layers: `web/themes/custom/ui_suite_bnppre/docs/development-workflow.md`
- PHP Hook classes and DI patterns: `web/themes/custom/ui_suite_bnppre/docs/php-hooks.md`
- Twig templates inventory: `web/themes/custom/ui_suite_bnppre/docs/templates.md`
- UI Patterns component system: `web/themes/custom/ui_suite_bnppre/docs/components.md`
- Library system and override audit: `web/themes/custom/ui_suite_bnppre/docs/libraries.md`
- BNPPRE icon pack contract: `web/themes/custom/ui_suite_bnppre/docs/icons.md`
- Theme roadmap: `web/themes/custom/ui_suite_bnppre/REFACTO.md`
