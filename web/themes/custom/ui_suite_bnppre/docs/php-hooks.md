# PHP Hook Classes

> **Pattern**: Drupal 11 OOP hooks via `#[Hook]` PHP 8 attributes + constructor dependency injection.

---

## Pattern Overview

All hooks live in `src/Hook/` as standalone PHP classes following the Drupal 11 OOP hook convention. A class method is registered as a hook by decorating it with `#[Hook('hook_name')]`.

```php
<?php
declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Hook\Attribute\Hook;

class PreprocessPage {

    public function __construct(
        protected ThemeSettingsProvider $themeSettings,  // ← Injected, not static
    ) {}

    #[Hook('preprocess_page')]
    #[Hook('preprocess_maintenance_page')]
    public function preprocess(array &$variables): void {
        $variables['container'] = $this->themeSettings->getSetting('container') ?? 'container';
    }
}
```

**Key rules**:
- Constructor injection for all services — no `\Drupal::service()` in method bodies.
- A single method can handle multiple hooks by stacking `#[Hook]` attributes.
- Class names match the hook responsibility, not the hook name verbatim.

---

## Hook Class Inventory

### Preprocess Hooks

| Class | Hook(s) | Variables injected / What it does |
|-------|---------|----------------------------------|
| `PreprocessPage` | `preprocess_page`, `preprocess_maintenance_page` | `$variables['container']`, `$variables['header_container_class']` from theme settings |
| `PreprocessInput` | `preprocess_input` | Adds Bootstrap classes to form inputs by input type (`form-control`, `form-check-input`, etc.) |
| `PreprocessFormElement` | `preprocess_form_element` | Adds Bootstrap classes for form groups, labels, descriptions, inline/checkbox/radio layout |
| `PreprocessLinks` | `preprocess_links` | Attaches contextual suggestion (dropbutton, layout-builder-links, media-library-menu, etc.) via `context.usb_suggestion` |
| `PreprocessLinksDropbutton` | `preprocess_links__dropbutton` | Transforms dropbutton arrays into Bootstrap split-button structure |
| `PreprocessMenuLocalAction` | `preprocess_menu_local_action` | Wraps local actions as Bootstrap buttons |
| `PreprocessMenuLocalTasks` | `preprocess_menu_local_tasks` | Converts local tasks into Bootstrap nav-tabs or nav-pills |
| `PreprocessPager` | `preprocess_pager` | Reformats pager data for Bootstrap pagination component |
| `PreprocessViewsMiniPager` | `preprocess_views_mini_pager` | Same as above for Views mini-pager |
| `PreprocessViewsViewTable` | `preprocess_views_view_table` | Adds `table`, `table-striped`, Bootstrap responsive wrapper classes |
| `PreprocessFileLink` | `preprocess_file_link` | Adds file type class + icon via Bootstrap Icons |

### Alter Hooks

| Class | Hook(s) | What it does |
|-------|---------|-------------|
| `ThemeSuggestionsAlter` | `theme_suggestions_details_alter`, `theme_suggestions_input_alter`, `theme_suggestions_links_alter` | Adds `details__accordion` (for non-display-builder `<details>`), `input__button`, link context suggestions |
| `ThemeRegistryAlter` | `theme_registry_alter` | Adjusts the theme registry for component compatibility |
| `LibraryInfoAlter` | `library_info_alter` | Dynamically injects the selected CSS + JS library variant into `framework` library dependencies |
| `ElementInfoAlter` | `element_info_alter` | Modifies element defaults (e.g., default classes on certain element types) |
| `FormAlter` | `form_alter` | Adds Bootstrap classes and structural changes to specific forms |

### Page / Attachment Hooks

| Class | Hook(s) | What it does |
|-------|---------|-------------|
| `PageAttachments` | `page_attachments` | Conditionally attaches libraries (e.g., password strength, media library) based on route/context |
| `ThemeSettings` | `form_system_theme_settings_alter` | Adds theme settings form: container type, header container, CSS/JS variant selectors |

### Optional Module Hooks

These classes are always loaded but their hooks only have effect when the corresponding module is installed.

| Class | Hook(s) | Module | What it does |
|-------|---------|--------|-------------|
| `Commerce` | Various preprocess | `commerce` | Adapts commerce cart, checkout, coupon templates to Bootstrap |
| `MediaLibrary` | Various preprocess | `media_library` | Fixes media library widget layout for Bootstrap grid |
| `Node` | `preprocess_node` | `node` | Adds display-mode classes and view-mode utilities |
| `Search` | `preprocess_search_result`, etc. | `search` | Bootstrap-friendly search result markup |
| `Views` | Various | `views` | Bootstrap classes on Views output (grids, tables, exposed filters) |
| `Filter` | `preprocess_filter_tips` | `filter` | Styling for filter tips |
| `Fences` | Various | `fences` | Compatibility with Fences field wrappers |
| `ContentModeration` | Various | `content_moderation` | Styles moderation state selectors |
| `UiSkins` | Various | `ui_skins` | CSS custom properties injection for skin system |

---

## Utility Classes

Located in `src/` (outside `Hook/`):

| Class | Purpose |
|-------|---------|
| `Utility\Variables` | Helper to safely access preprocess `$variables` and element arrays with typed accessors |
| `Utility\Element` | Wrapper around Drupal render elements — provides `.isButton()`, `.getProperty()`, etc. |

---

## Adding a New Hook

1. Create a class in `src/Hook/MyHook.php`:

```php
<?php
declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Hook\Attribute\Hook;

class MyHook {

    // Inject services via constructor — no \Drupal:: static calls in the method
    public function __construct(
        protected SomeService $someService,
    ) {}

    #[Hook('preprocess_node')]
    public function preprocessNode(array &$variables): void {
        $variables['my_variable'] = $this->someService->compute($variables);
    }
}
```

2. No registration needed — Drupal 11 autodiscovers `#[Hook]` attributes.
3. Run `vendor/bin/drush cr` to register the new hook.

---

## DI Checklist

Before committing a Hook class:

- [ ] All services are constructor-injected (typed properties).
- [ ] No `\Drupal::service()`, `\Drupal::entityTypeManager()`, or similar static calls in method bodies.
- [ ] If a static call is unavoidable (rare), it is behind a dedicated protected helper method.
- [ ] New dependencies are listed as constructor parameters, not created with `new`.
