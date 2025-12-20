---
title: Drupal Integration Standards
version: 4.0.0
lastUpdated: 2025-12-19
priority: CRITICAL
status: ACTIVE
---

# 06 - Drupal Integration Standards

**Purpose**: Drupal-specific integration patterns that **respect and reinforce** Atomic Design hierarchy. This document ensures seamless Drupal 10/11 compatibility while maintaining component autonomy.

**When to use**: Consult when integrating components into Drupal, writing preprocess hooks, creating render arrays, or debugging Drupal-specific issues.

**Related files**:
- [01-core-principles.md](01-core-principles.md) - Atomic Design foundations
- [02-component-development.md](02-component-development.md) - Component workflow
- [03-technical-implementation.md](03-technical-implementation.md) - Twig/CSS standards
- [04-quality-assurance.md](04-quality-assurance.md) - Validation

---

## 📐 Table of Contents

1. [Atomic-Aware Drupal Integration](#1-atomic-aware-drupal-integration)
2. [Render Arrays by Atomic Level](#2-render-arrays-by-atomic-level)
3. [Preprocess Hooks Strategy](#3-preprocess-hooks-strategy)
4. [Libraries Attachment](#4-libraries-attachment)
5. [Theme Suggestions](#5-theme-suggestions)
6. [Cache Tags & Contexts](#6-cache-tags--contexts)
7. [Form API Integration](#7-form-api-integration)
8. [Drupal Validation Workflow](#8-drupal-validation-workflow)

---

## 1. Atomic-Aware Drupal Integration

### 1.1 Critical Rule: Preserve Atomic Autonomy

**NEVER compromise Atomic Design principles for Drupal integration.**

| Atomic Level | Drupal Integration Allowed | Rationale |
|--------------|---------------------------|-----------|
| **Atoms** | ❌ Minimal (avoid preprocess) | Must remain autonomous, reusable |
| **Molecules** | ✅ Full integration | Composition layer (ideal for Drupal) |
| **Organisms** | ✅ Full integration | Business logic layer |
| **Templates** | ✅ Full integration | Layout/region layer |
| **Pages** | ✅ Full integration | Content layer |

### 1.2 Integration Decision Matrix

```
Component needs Drupal data?
├─ ATOM → Use direct props in parent's preprocess
│          ❌ DO NOT create atom preprocess
│
├─ MOLECULE → Create preprocess hook
│              ✅ Compose atom data
│              ✅ Register #theme
│
├─ ORGANISM → Create preprocess hook
│              ✅ Business logic
│              ✅ Cache tags
│
└─ TEMPLATE → Use page_preprocess
               ✅ Region variables
```

### 1.3 Drupal Integration Levels

**Level 1: Direct Twig Include** (Atoms)
```twig
{# In parent template #}
{% include '@elements/button/button.twig' with {
  text: 'Click me',
  variant: 'primary'
} only %}
```

**Level 2: Theme Hook + Preprocess** (Molecules+)
```php
// ps.theme
function ps_theme_theme($existing, $type, $theme, $path) {
  return [
    'ps_card' => [
      'variables' => [
        'title' => NULL,
        'image' => NULL,
        'attributes' => [],
      ],
      'template' => 'card',
      'path' => $path . '/source/patterns/components/card',
    ],
  ];
}
```

**Level 3: Render Array** (Controller/Module)
```php
$build['card'] = [
  '#theme' => 'ps_card',
  '#title' => $node->getTitle(),
  '#image' => $image_url,
  '#attributes' => ['class' => ['context-specific']],
];
```

---

## 2. Render Arrays by Atomic Level

### 2.1 Atoms - Direct Props (NO #theme)

**Rule**: Atoms are included via parent's template, NOT standalone render arrays.

**❌ WRONG - Atom with #theme hook**:
```php
// Don't do this for atoms
$build['button'] = [
  '#theme' => 'ps_button', // ❌ Atoms don't need #theme
  '#text' => 'Click',
];
```

**✅ CORRECT - Atom via parent molecule**:
```php
// In molecule preprocess
function ps_preprocess_ps_card(&$variables) {
  $variables['cta_button'] = [
    'text' => 'Learn More',
    'variant' => 'primary',
    'attributes' => \Drupal\Core\Template\Attribute::create(),
  ];
}
```

```twig
{# card.twig (molecule) #}
<div class="ps-card">
  <h2>{{ title }}</h2>
  
  {% include '@elements/button/button.twig' with cta_button only %}
</div>
```

**Exception**: Atoms with JS behavior MAY have #theme for library attachment.

### 2.2 Molecules - Full #theme Support

**Standard Pattern**:

```php
// ps.theme - Register theme hook
function ps_theme_theme($existing, $type, $theme, $path) {
  return [
    'ps_card' => [
      'variables' => [
        'title' => NULL,
        'description' => NULL,
        'image_url' => NULL,
        'badge_text' => NULL,
        'badge_variant' => 'default',
        'cta_text' => NULL,
        'cta_url' => NULL,
        'attributes' => [],
      ],
      'template' => 'card',
      'path' => $path . '/source/patterns/components/card',
    ],
  ];
}

// Preprocess - Compose atoms
function ps_preprocess_ps_card(&$variables) {
  // Prepare badge atom props
  if (!empty($variables['badge_text'])) {
    $variables['badge'] = [
      'text' => $variables['badge_text'],
      'variant' => $variables['badge_variant'] ?? 'default',
    ];
  }
  
  // Prepare button atom props
  if (!empty($variables['cta_text'])) {
    $variables['cta_button'] = [
      'text' => $variables['cta_text'],
      'url' => $variables['cta_url'],
      'variant' => 'primary',
      'attributes' => \Drupal\Core\Template\Attribute::create()
        ->addClass('ps-card__cta'),
    ];
  }
  
  // Attributes
  $variables['attributes'] = $variables['attributes'] ?? \Drupal\Core\Template\Attribute::create();
}
```

**Render Array**:
```php
$build['property_card'] = [
  '#theme' => 'ps_card',
  '#title' => $node->label(),
  '#description' => $node->get('field_description')->value,
  '#image_url' => $image_url,
  '#badge_text' => 'For Sale',
  '#badge_variant' => 'success',
  '#cta_text' => 'View Details',
  '#cta_url' => $node->toUrl(),
  '#cache' => [
    'tags' => $node->getCacheTags(),
    'contexts' => ['user'],
  ],
];
```

### 2.3 Organisms - Business Logic Layer

**Pattern**:

```php
// ps.theme
function ps_theme_theme($existing, $type, $theme, $path) {
  return [
    'ps_property_grid' => [
      'variables' => [
        'items' => [],
        'filters' => [],
        'pagination' => NULL,
        'attributes' => [],
      ],
      'template' => 'property-grid',
      'path' => $path . '/source/patterns/collections/property-grid',
    ],
  ];
}

// Preprocess - Business logic
function ps_preprocess_ps_property_grid(&$variables) {
  $items = $variables['items'];
  
  // Compose molecules (cards)
  $variables['cards'] = [];
  foreach ($items as $item) {
    $variables['cards'][] = [
      '#theme' => 'ps_card',
      '#title' => $item['title'],
      '#image_url' => $item['image'],
      '#badge_text' => $item['status'],
      '#badge_variant' => $item['status_color'],
    ];
  }
  
  // Cache tags aggregation
  $cache_tags = [];
  foreach ($items as $item) {
    $cache_tags = array_merge($cache_tags, $item['cache_tags'] ?? []);
  }
  $variables['#cache']['tags'] = $cache_tags;
}
```

### 2.4 Templates - Page Layout

**Use Drupal regions** (standard pattern):

```php
// page.html.twig (extends Drupal core)
<div class="ps-layout-homepage">
  <header class="ps-layout-homepage__header">
    {{ page.header }}
  </header>
  
  <main class="ps-layout-homepage__main">
    {{ page.content }}
  </main>
  
  <aside class="ps-layout-homepage__sidebar">
    {{ page.sidebar }}
  </aside>
  
  <footer class="ps-layout-homepage__footer">
    {{ page.footer }}
  </footer>
</div>
```

---

## 3. Preprocess Hooks Strategy

### 3.1 When to Create Preprocess

**Create preprocess ONLY for**:

✅ **Molecules** - Composing atoms, data transformation  
✅ **Organisms** - Business logic, entity loading  
✅ **Templates** - Layout variables, region data  
❌ **Atoms** - Avoid (exception: JS attachment)

### 3.2 Preprocess Naming Convention

```php
// Pattern: ps_preprocess_COMPONENT_NAME
function ps_preprocess_ps_card(&$variables) { }
function ps_preprocess_ps_property_grid(&$variables) { }
function ps_preprocess_page(&$variables) { }
```

### 3.3 Standard Preprocess Structure

```php
function ps_preprocess_ps_COMPONENT(&$variables) {
  // 1. Extract variables
  $title = $variables['title'] ?? '';
  $items = $variables['items'] ?? [];
  
  // 2. Transform/validate data
  $variables['title'] = \Drupal\Component\Utility\Html::escape($title);
  
  // 3. Compose child components (atoms/molecules)
  $variables['child_atoms'] = [
    'text' => 'Value',
    'variant' => 'primary',
  ];
  
  // 4. Prepare attributes
  $variables['attributes'] = $variables['attributes'] 
    ?? \Drupal\Core\Template\Attribute::create();
  
  // 5. Add cache metadata (if needed)
  $variables['#cache']['tags'] = ['node:1'];
  $variables['#cache']['contexts'] = ['user'];
}
```

### 3.4 Composing Atoms in Preprocess (PREFERRED)

**✅ CORRECT - Prepare atom data in preprocess**:

```php
function ps_preprocess_ps_alert(&$variables) {
  // Compose icon atom
  $variables['icon'] = [
    'icon' => $variables['type'] === 'success' ? 'check-circle' : 'info',
    'size' => 'md',
  ];
  
  // Compose button atom (dismiss)
  if ($variables['dismissible']) {
    $variables['dismiss_button'] = [
      'text' => 'Close',
      'variant' => 'text',
      'attributes' => \Drupal\Core\Template\Attribute::create()
        ->addClass('ps-alert__dismiss')
        ->setAttribute('data-dismiss', 'alert'),
    ];
  }
}
```

```twig
{# alert.twig #}
<div class="ps-alert ps-alert--{{ type }}">
  {% if icon %}
    {% include '@elements/icon/icon.twig' with icon only %}
  {% endif %}
  
  <div class="ps-alert__content">{{ message }}</div>
  
  {% if dismiss_button %}
    {% include '@elements/button/button.twig' with dismiss_button only %}
  {% endif %}
</div>
```

---

## 4. Libraries Attachment

### 4.1 Libraries by Atomic Level

**Global** - All pages
```yaml
# ps.libraries.yml
global:
  css:
    base:
      dist/css/styles.css: {}
```

**Per Component** - Molecules/Organisms with JS
```yaml
accordion:
  js:
    dist/js/accordion.js: {}
  dependencies:
    - core/drupal
    - core/once
    - ps_theme/vendors
```

**Atoms** - Only if essential JS behavior
```yaml
button:
  js:
    dist/js/button.js: {}  # Only for ripple effect, tracking, etc.
  dependencies:
    - core/drupal
    - core/once
```

### 4.2 Attaching Libraries to Components

**Method 1: In Twig Template**
```twig
{# accordion.twig #}
{{ attach_library('ps_theme/accordion') }}

<div class="ps-accordion">
  {# Component markup #}
</div>
```

**Method 2: In Render Array**
```php
$build['accordion'] = [
  '#theme' => 'ps_accordion',
  '#items' => $items,
  '#attached' => [
    'library' => ['ps_theme/accordion'],
  ],
];
```

**Method 3: In Preprocess**
```php
function ps_preprocess_ps_accordion(&$variables) {
  $variables['#attached']['library'][] = 'ps_theme/accordion';
}
```

### 4.3 Library Dependency Strategy

```yaml
# Core dependencies (all components)
vendors:
  js:
    dist/js/vendors/vendors.js: {}
  dependencies:
    - core/drupal       # Drupal object
    - core/once         # Idempotency
    - core/drupalSettings  # Config passing

# Component library (depends on vendors)
modal:
  js:
    dist/js/modal.js: {}
  dependencies:
    - ps_theme/vendors  # Ensure vendors loaded first
```

---

## 5. Theme Suggestions

### 5.1 Atomic-Safe Suggestions

**Rule**: Theme suggestions MUST NOT break component composition.

**✅ ALLOWED - Context-specific variants**:
```php
function ps_theme_suggestions_ps_card_alter(array &$suggestions, array $variables) {
  // Add view mode suggestion
  if (!empty($variables['view_mode'])) {
    $suggestions[] = 'ps_card__' . $variables['view_mode'];
  }
  
  // Add content type suggestion
  if (!empty($variables['content_type'])) {
    $suggestions[] = 'ps_card__' . $variables['content_type'];
  }
}
```

**Available templates**:
- `card.html.twig` (default)
- `card--teaser.html.twig` (view mode)
- `card--property.html.twig` (content type)

**❌ FORBIDDEN - Breaking atom includes**:
```twig
{# card--custom.html.twig - BAD! #}
<div class="ps-card">
  {# ❌ Reimplementing button instead of including atom #}
  <a href="{{ url }}" class="custom-button">{{ text }}</a>
</div>
```

### 5.2 Standard Suggestion Patterns

**By Bundle/Type**:
```php
$suggestions[] = 'ps_card__' . $node->bundle();
// → ps_card__property, ps_card__article
```

**By View Mode**:
```php
$suggestions[] = 'ps_card__' . $view_mode;
// → ps_card__teaser, ps_card__full
```

**By Context**:
```php
$suggestions[] = 'ps_card__' . $context;
// → ps_card__search_results, ps_card__homepage
```

---

## 6. Cache Tags & Contexts

### 6.1 Cache Strategy by Atomic Level

| Level | Cache Responsibility | Example |
|-------|---------------------|---------|
| **Atom** | None (inherited from parent) | Button inherits card's cache |
| **Molecule** | Own data cache tags | Card: `['node:123']` |
| **Organism** | Aggregated cache tags | Grid: All card cache tags |
| **Template** | Page-level cache | `['node_list', 'user:1']` |

### 6.2 Cache Tags in Render Arrays

```php
// Molecule
$build['card'] = [
  '#theme' => 'ps_card',
  '#title' => $node->label(),
  '#cache' => [
    'tags' => $node->getCacheTags(),  // ['node:123']
    'contexts' => ['user'],
  ],
];

// Organism (aggregate child tags)
function ps_preprocess_ps_property_grid(&$variables) {
  $cache_tags = [];
  foreach ($variables['items'] as $item) {
    if (isset($item['#cache']['tags'])) {
      $cache_tags = array_merge($cache_tags, $item['#cache']['tags']);
    }
  }
  
  $variables['#cache']['tags'] = array_unique($cache_tags);
  $variables['#cache']['contexts'] = ['url.query_args:filter'];
}
```

### 6.3 Common Cache Contexts

```php
'contexts' => [
  'user',                    // Varies by logged-in user
  'user.roles',              // Varies by user role
  'languages:language_interface',  // Varies by language
  'url.path',                // Varies by URL
  'url.query_args:filter',   // Varies by query param
]
```

---

## 7. Form API Integration

### 7.1 Mapping Form API to PS Atoms

**Drupal Form API → PS Theme Atoms**:

| Form API `#type` | PS Atom | Template |
|------------------|---------|----------|
| `textfield` | Input | `@elements/input/input.twig` |
| `textarea` | Textarea | `@elements/textarea/textarea.twig` |
| `select` | Select | `@elements/select/select.twig` |
| `checkbox` | Checkbox | `@elements/checkbox/checkbox.twig` |
| `radios` | Radio | `@elements/radio/radio.twig` |
| `submit` | Button | `@elements/button/button.twig` |

### 7.2 Form Theme Hooks

**Register form element themes**:

```php
// ps.theme
function ps_theme_theme($existing, $type, $theme, $path) {
  return [
    'input__textfield' => [
      'base hook' => 'input',
      'template' => 'input',
      'path' => $path . '/source/patterns/elements/input',
    ],
    'textarea' => [
      'base hook' => 'textarea',
      'template' => 'textarea',
      'path' => $path . '/source/patterns/elements/textarea',
    ],
  ];
}
```

### 7.3 Form Element Preprocess

```php
function ps_preprocess_input(&$variables) {
  $element = $variables['element'];
  
  // Add PS classes
  $variables['attributes']['class'][] = 'ps-input';
  $variables['attributes']['class'][] = 'form-control';
  
  // Add state modifiers
  if (!empty($element['#errors'])) {
    $variables['attributes']['class'][] = 'ps-input--error';
  }
  
  // Add size variant
  if (!empty($element['#size_variant'])) {
    $variables['attributes']['class'][] = 'ps-input--' . $element['#size_variant'];
  }
}
```

### 7.4 Using Form Field Molecule

**Molecule wraps Form API elements**:

```php
$form['email'] = [
  '#type' => 'textfield',
  '#title' => t('Email Address'),
  '#required' => TRUE,
  '#theme_wrappers' => ['ps_form_field'],
  '#ps_form_field' => [
    'optional' => FALSE,
    'helper_text' => t('We will never share your email.'),
  ],
];
```

**Preprocess form field wrapper**:
```php
function ps_preprocess_ps_form_field(&$variables) {
  $element = $variables['element'];
  
  // Extract form field props
  $variables['label'] = $element['#title'];
  $variables['required'] = !empty($element['#required']);
  $variables['optional'] = $element['#ps_form_field']['optional'] ?? !$element['#required'];
  $variables['error'] = $element['#errors'] ?? NULL;
  $variables['helper_text'] = $element['#ps_form_field']['helper_text'] ?? NULL;
  
  // Render child input atom
  $variables['input'] = $element;
}
```

---

## 8. Drupal Validation Workflow

### 8.1 Validation Checklist

**Before committing Drupal integration**:

- [ ] **Attributes Parameter**: All Twig templates have `attributes` parameter
- [ ] **create_attribute()**: Used in preprocess for attribute objects
- [ ] **Render Arrays**: Tested with actual Drupal render arrays
- [ ] **Cache Tags**: Appropriate cache tags added
- [ ] **Libraries**: JS/CSS libraries properly attached
- [ ] **Preprocess Logic**: Only in Molecules+ (not Atoms)
- [ ] **Theme Suggestions**: Don't break atom composition
- [ ] **Drupal Baseline**: Templates extend starterkit patterns

### 8.2 Testing with Drupal

**Step 1: Create test module**
```bash
mkdir -p modules/custom/ps_test
```

**Step 2: Test render arrays**
```php
// modules/custom/ps_test/src/Controller/ComponentTestController.php
public function testCard() {
  return [
    '#theme' => 'ps_card',
    '#title' => 'Test Property',
    '#description' => 'Beautiful apartment in Paris',
    '#image_url' => '/path/to/image.jpg',
    '#badge_text' => 'For Sale',
    '#cta_text' => 'View Details',
    '#cache' => ['max-age' => 0],
  ];
}
```

**Step 3: Clear cache & verify**
```bash
drush cr
# Visit /ps-test/card
```

### 8.3 Common Drupal Integration Errors

**Error 1: Missing attributes object**
```
Error: Call to a member function addClass() on null
```
**Fix**: Add to preprocess:
```php
$variables['attributes'] = $variables['attributes'] 
  ?? \Drupal\Core\Template\Attribute::create();
```

**Error 2: Theme hook not found**
```
Error: Theme hook 'ps_card' not found
```
**Fix**: Clear cache after adding to `hook_theme()`:
```bash
drush cr
```

**Error 3: Library not loaded**
```
JS Error: Drupal.behaviors.accordion is undefined
```
**Fix**: Check library dependency chain in `ps.libraries.yml`

---

## 9. Drupal-First Workflow Summary

### 9.1 Atomic Integration Workflow

```
1. CREATE ATOM (no Drupal integration)
   ↓
2. CREATE MOLECULE
   ├─ Register #theme hook
   ├─ Write preprocess (compose atoms)
   ├─ Add library (if JS needed)
   └─ Test render array
   ↓
3. CREATE ORGANISM  
   ├─ Register #theme hook
   ├─ Write preprocess (business logic)
   ├─ Aggregate cache tags
   └─ Test in Drupal page
   ↓
4. INTEGRATE IN TEMPLATE
   ├─ Use Drupal regions
   ├─ Add page preprocess
   └─ Cache contexts
```

### 9.2 Quick Reference

**What needs Drupal integration?**
- ❌ Atoms → No (include only)
- ✅ Molecules → Yes (#theme, preprocess, library)
- ✅ Organisms → Yes (#theme, preprocess, cache)
- ✅ Templates → Yes (regions, page preprocess)

**Where to write Drupal code?**
- `ps.theme` → Theme hooks, preprocess, suggestions
- `ps.libraries.yml` → JS/CSS attachment
- `templates/` → Twig overrides (extend starterkit)
- `preprocess/` → Complex preprocess (optional organization)

---

## 10. Resources

**Drupal Documentation**:
- [Theming Drupal](https://www.drupal.org/docs/theming-drupal)
- [Render API](https://www.drupal.org/docs/drupal-apis/render-api)
- [Twig in Drupal](https://www.drupal.org/docs/theming-drupal/twig-in-drupal)
- [Theme System](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21theme.api.php)

**Starterkit Templates**:
- [Drupal Core Starterkit](https://github.com/drupal/drupal/tree/11.x/core/themes/starterkit_theme/templates)

---

**Version**: 4.0.0  
**Last Updated**: 2025-12-19  
**Maintainers**: Design System Team + Drupal Integration Team
