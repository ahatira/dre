# Twig Templates

> 84 Twig templates organized across 14 directories. All templates override contrib or core defaults to render Bootstrap 5-compatible HTML.

---

## Template Suggestion Hierarchy

Drupal resolves templates from most specific to least specific. The theme's `ThemeSuggestionsAlter` class adds custom suggestions at runtime.

### Custom Suggestions Added by ThemeSuggestionsAlter

| Hook | Condition | Suggestion added |
|------|-----------|-----------------|
| `theme_suggestions_details_alter` | `<details>` element with `bootstrap_accordion` property, not in display builder | `details__accordion` → `templates/system/details--accordion.html.twig` |
| `theme_suggestions_input_alter` | Input element that `.isButton()` | `input__button` → `templates/input/input--button.html.twig` |
| `theme_suggestions_links_alter` | Links render element with a `context.usb_suggestion` key | Uses value as suggestion (e.g., `links__dropbutton`, `links__contextual`) |

### Standard Drupal Suggestion Patterns

```
block.html.twig
block--{region}.html.twig
block--{plugin-id}.html.twig         ← most specific

node.html.twig
node--{bundle}.html.twig
node--{view-mode}.html.twig
node--{bundle}--{view-mode}.html.twig

field.html.twig
field--{field-name}.html.twig
field--{entity-type}.html.twig
field--{bundle}.html.twig
field--{entity-type}--{field-name}.html.twig  ← most specific
```

---

## Template Inventory

### Block Templates (`templates/block/`)

| Template | Purpose |
|----------|---------|
| `block--bare.html.twig` | Block with no wrapper (for embedding without a card/box) |
| `block--header-actions.html.twig` | Header actions zone (search, CTA) |
| `block--header-navigation.html.twig` | Desktop navigation block |
| `block--header-switcher.html.twig` | Language/context switcher in header |
| `block--local-actions-block.html.twig` | "Add" buttons in admin, BS-styled |
| `block--page-title-block.html.twig` | Page title as `<h1>` with Bootstrap spacing |
| `block--system-branding-block.html.twig` | Logo + site name |
| `block--ui-suite-bnppre-account-menu.html.twig` | User account menu block |

### Commerce Templates (`templates/commerce/`)

| Template | Purpose |
|----------|---------|
| `cart/commerce-cart-block.html.twig` | Cart block in header |
| `cart/commerce-cart-empty-page.html.twig` | Empty cart page |
| `checkout/commerce-checkout-form.html.twig` | Checkout form layout |
| `checkout/commerce-checkout-form--with-sidebar.html.twig` | Checkout with order summary sidebar |
| `checkout/commerce-checkout-order-summary.html.twig` | Order summary component |
| `checkout/commerce-checkout-progress.html.twig` | Step-progress indicator |
| `promotion/commerce-coupon-redemption-form.html.twig` | Coupon code form |

### Content Moderation (`templates/content_moderation/`)

| Template | Purpose |
|----------|---------|
| `entity-moderation-form.html.twig` | Moderation state form as Bootstrap select |

### Form / Input Templates (`templates/input/`)

| Template | Purpose |
|----------|---------|
| `datetime-form.html.twig` | Date and time field layout |
| `datetime-wrapper.html.twig` | Date/time group wrapper |
| `form-element.html.twig` | Core form element — applies Bootstrap form group structure |
| `input.html.twig` | Default input — adds `form-control` dynamically |
| `input--button.html.twig` | Buttons — adds `btn btn-*` classes (suggestion from `ThemeSuggestionsAlter`) |
| `select.html.twig` | `<select>` — adds `form-select` |
| `textarea.html.twig` | `<textarea>` — adds `form-control` |

### System Templates (`templates/system/`)

| Template | Purpose |
|----------|---------|
| `breadcrumb.html.twig` | Bootstrap `<nav aria-label="breadcrumb">` |
| `container--media-library-widget-selection.html.twig` | Media library selection container |
| `details--accordion.html.twig` | `<details>` rendered as Bootstrap Accordion (via suggestion) |
| `feed-icon.html.twig` | RSS feed icon link |
| `field.html.twig` | Generic field wrapper |
| `field--node--created.html.twig` | Node creation date field |
| `field--node--title.html.twig` | Node title field |
| `field--node--uid.html.twig` | Node author field |
| `fieldset.html.twig` | `<fieldset>` with Bootstrap styling |
| `html.html.twig` | `<html>` + `<head>` + `<body>` structure |
| `image.html.twig` | `<img>` with `img-fluid` |
| `item-list--layouts.html.twig` | Layout item list (Layout Builder) |
| `links.html.twig` | Default links list |
| `links--comment.html.twig` | Comment action links |
| `links--contextual.html.twig` | Contextual edit links overlay |
| `links--dropbutton.html.twig` | Dropbutton as Bootstrap split-button |
| `links--layout-builder-links.html.twig` | Layout Builder edition links |
| `links--media-library-menu.html.twig` | Media library tab navigation |
| `maintenance-page.html.twig` | Maintenance/offline page |
| `mark.html.twig` | "New" / "Updated" mark badge |
| `page.html.twig` | Main page template — renders all 14 regions |
| `pager.html.twig` | Bootstrap pagination |
| `pager--media-library.html.twig` | Pagination for media library modal |
| `progress-bar.html.twig` | Bootstrap `progress` component |
| `status-messages.html.twig` | Bootstrap alerts for Drupal messages |
| `table.html.twig` | `<table class="table">` with responsive wrapper |
| `tablesort-indicator.html.twig` | Sort indicator icons for sortable tables |

### Menu Templates (`templates/menu/`)

| Template | Purpose |
|----------|---------|
| `menu.html.twig` | Base recursive menu — Bootstrap nav |
| `menu--account.html.twig` | Account menu with user icon |
| `menu-local-action.html.twig` | Local action as `btn` |
| `menu-local-tasks.html.twig` | Local tasks as Bootstrap nav-tabs |

### Other Templates

| Template | Path | Purpose |
|----------|------|---------|
| `comment.html.twig` | `comment/` | Comment with Bootstrap card layout |
| `file-link.html.twig` | `file/` | File download link + Bootstrap icon |
| `filter-tips.html.twig` | `filter/` | Text format tips accordion |
| `image-widget.html.twig` | `image/` | Image upload widget |
| `media.html.twig` | `media/` | Generic media entity |
| `media--bare.html.twig` | `media/` | Media without wrapper |
| `media--media-library.html.twig` | `media/` | Media in the library grid |
| `media-library-item.html.twig` | `media_library/` | Media library grid item |
| `media-library-wrapper.html.twig` | `media_library/` | Media library modal wrapper |
| `node.html.twig` | `node/` | Default node layout |
| `node--bare.html.twig` | `node/` | Node without wrapper (for embedding) |
| `paragraph--bare.html.twig` | `paragraphs/` | Paragraph without wrapper |
| `region--help.html.twig` | `region/` | Help region with dismissal |
| `region--navigation-collapsible.html.twig` | `region/` | Mobile collapsible nav region |
| `region--no-wrapper.html.twig` | `region/` | Region without any wrapper div |
| `taxonomy-term.html.twig` | `taxonomy/` | Taxonomy term display |
| `taxonomy-term--bare.html.twig` | `taxonomy/` | Taxonomy term without wrapper |
| `book-navigation.html.twig` | `book/` | Book module navigation |

### UI Suite / UI Patterns Templates

| Template | Purpose |
|----------|---------|
| `ui_examples/ui-examples-overview-page.html.twig` | UI Examples overview |
| `ui_icons/icon-selector.html.twig` | Icon picker widget |
| `ui_patterns_library/ui-patterns-*.html.twig` | Component library page templates |

---

## How to Add or Override a Template

### Override an existing Drupal template

1. Find the core template: `web/core/modules/{module}/templates/{template}.html.twig`
2. Copy it to the matching path in this theme: `templates/{category}/{template}.html.twig`
3. Adapt the markup to Bootstrap 5.
4. Run `vendor/bin/drush cr`.

### Add a template for a specific suggestion

1. Determine the suggestion key (e.g., `block__system_main_block`).
2. Create `templates/block/block--system-main-block.html.twig` (underscores → hyphens in filename).
3. Run `vendor/bin/drush cr`.

### Add a custom suggestion programmatically

Add a method to `src/Hook/ThemeSuggestionsAlter.php`:

```php
#[Hook('theme_suggestions_node_alter')]
public function node(array &$suggestions, array $variables): void {
    $node = $variables['elements']['#node'];
    $suggestions[] = 'node__' . $node->bundle() . '__' . $node->getType();
}
```

Run `vendor/bin/drush cr` after.
