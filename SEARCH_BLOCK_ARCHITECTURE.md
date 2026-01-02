# Search Block Architecture Implementation

## ✅ Status: COMPLETE

**Date**: 2025-01-02  
**Commit**: cb46bc3 - feat(layouts): Integrate search form component into block-search  
**Build**: ✓ Passes (4.55s)

---

## 📋 Summary

The search block component now follows the **architectural pattern** established by other blocks (CTA, User Account). The search form component is **directly integrated** into the block template via `{% include %}`, ensuring:

1. ✅ Self-contained block functionality (button + form together)
2. ✅ Proper JavaScript attachment via `attach_library()`
3. ✅ Follows Atomic Design patterns (block ⊃ molecule component)
4. ✅ Drupal-ready for integration

---

## 🏗️ Architecture Pattern

### Block Integration Layer

```
Block (Layouts/Blocks/Search)
  ├─ Button Component (ps-search-trigger)
  │  ├─ Icon: search
  │  └─ Label: "Search" (mobile only)
  │
  └─ Search Form Component (@components/search-form)
     ├─ Input: searchable query
     ├─ Submit Button: form submission
     └─ Close Button: collapse form
```

### Extends Block Layout

**Pattern**: All blocks extend `@layouts/block/block.twig` base structure

```twig
{% extends '@layouts/block/block.twig' %}
{{ attach_library('ps_theme/search-form') }}

{%- block content -%}
  {# Button + Form here #}
{%- endblock -%}
```

---

## 📝 Implementation Details

### 1. Template (block-search.twig)

**Key Changes**:
- ✅ Extends `@layouts/block/block.twig` (base block structure)
- ✅ Attaches `ps_theme/search-form` library (JavaScript behavior)
- ✅ Includes search form component via `{% include '@components/search-form/search-form.twig' %}`
- ✅ Passes `form_config` to search-form for customization

**Code**:
```twig
{% extends '@layouts/block/block.twig' %}

{{ attach_library('ps_theme/search-form') }}

{%- block content -%}
  <button class="ps-search-trigger" ...>
    <span class="ps-search-trigger__label">{{ button_label }}</span>
  </button>
  
  {%- include '@components/search-form/search-form.twig' with form_config only -%}
{%- endblock -%}
```

### 2. Configuration (block-search.yml)

**Key Changes**:
- ✅ Added `form_config` property with sensible defaults
- ✅ Configuration passed directly to search-form.twig

**Code**:
```yaml
form_config:
  placeholder: 'What are you looking for ?'
  action: '/search'
  method: 'GET'
  input_name: 'q'
  show: false  # Initial state (toggled by JS)
```

**Property Details**:
| Property | Type | Default | Purpose |
|----------|------|---------|---------|
| `placeholder` | string | 'What are you looking for ?' | Input placeholder text |
| `action` | string | '/search' | Form action URL |
| `method` | string | 'GET' | Form method (GET or POST) |
| `input_name` | string | 'q' | Form input field name |
| `show` | boolean | false | Initial display state (toggled by JavaScript) |

### 3. JavaScript Integration

**How it Works**:
1. Template includes `{{ attach_library('ps_theme/search-form') }}`
2. Drupal loads `ps_theme/search-form` library (from `ps.libraries.yml`)
3. Library loads `dist/js/search-form.js` (Drupal behavior)
4. Behavior attaches to:
   - `.ps-search-trigger` - Click to toggle form visibility
   - `[data-search-form]` - Initialize form with event handlers
   - `[data-search-close]` - Close button handler
   - `[data-search-input]` - Auto-focus on open
5. ESC key closes form, click outside doesn't (by design)

**Libraries Definition** (`ps.libraries.yml`):
```yaml
search-form:
  js:
    dist/js/search-form.js: {}
  dependencies:
    - core/drupal
    - core/once
```

---

## 🎨 Storybook Stories

### Three Variants Available

#### 1. Default Story
- Renders search button + form (form initially hidden)
- Button click toggles form visibility
- All defaults applied

#### 2. WithCustomLabel Story
- Customizable button label (e.g., "Find properties")
- Form still integrated and functional

#### 3. WithSearchForm Story
- Complete header-like layout with navigation
- Demonstrates form in context
- Interactive Storybook JavaScript for demo only

**Note**: In real Drupal, buttons 1 & 2 will automatically show/hide the form via the attached JavaScript behavior.

---

## ✨ Features

### Button Component
- **Desktop (1024px+)**: Icon-only (1.5rem size)
- **Mobile (< 640px)**: Icon + "Search" label
- **States**: Default, Hover (primary color), Focus (focus-visible ring)
- **All values token-based**: No hardcoded colors/sizes

### Form Component (Integrated)
- **Input field**: Full-width, searchable type, auto-focus on open
- **Submit button**: Icon-only search icon
- **Close button**: Icon-only close icon (X)
- **ESC key support**: Closes form when open
- **Attributes**: `data-search-form`, `data-search-input`, `data-search-close`

---

## 🔗 Drupal Integration Ready

### Template Flow
```
User clicks button.ps-search-trigger
  ↓
search-form.js (Drupal behavior) detects click
  ↓
Adds 'ps-search-form--open' class to [data-search-form]
  ↓
CSS slideDown animation displays form
  ↓
Input auto-focuses for immediate typing
```

### Production Implementation
```php
// In Drupal controller/preprocess
$build['search_block'] = [
  '#theme' => 'ps_block_search',  // If hook registered
  '#button_label' => t('Search'),
  '#form_config' => [
    'action' => '/search',
    'input_name' => 'query',
  ],
];

// Or simply place block via Drupal Block UI
// Block will auto-load with all defaults
```

---

## 📊 File Changes

| File | Change | Impact |
|------|--------|--------|
| `block-search.twig` | Added library attachment + form include | +3 lines, JS now integrated |
| `block-search.yml` | Added form_config property | +7 lines, configuration layer |
| `block-search.stories.jsx` | Updated story descriptions | Updated docs, no logic change |
| `ps.libraries.yml` | No change (search-form already exists) | Pre-existing library reused |
| `source/props/icons.css` | Auto-generated update | Icon registry sync |

---

## 🎯 Validation Checklist

- [x] Build passes: `npm run build` (✓ 4.55s)
- [x] Form included via `{% include %}` (✓ block-search.twig line 44)
- [x] Library attached via `{{ attach_library() }}` (✓ block-search.twig line 3)
- [x] form_config defaults defined (✓ block-search.yml)
- [x] All tokens used (0 hardcoded values)
- [x] Follows block architectural pattern (✓ like block-cta, block-user-account)
- [x] Story descriptions updated
- [x] Storybook renders without errors
- [x] Commit created with structured message

---

## 🚀 Next Steps (Optional Enhancements)

1. **Register Drupal Theme Hook** (for ps.theme integration)
   - If planning full Drupal integration, register `ps_block_search` hook
   - Create preprocess function (currently optional, direct include sufficient)

2. **Add Form Validation** (beyond scope, data layer)
   - Server-side validation on form submission
   - Client-side pre-validation (if needed)

3. **Customize Search Behavior** (per-site config)
   - Change action URL per environment
   - Modify input name if backend expects different field

4. **Responsive Icon Sizing** (design refinement)
   - Fine-tune icon size on different breakpoints
   - Currently: 1.5rem (desktop), 1.5rem (mobile with label)

---

## 📌 Architectural Notes

### Why Include Search Form?

**Pattern Consistency**: Other blocks follow this pattern:
- **block-cta** → includes button.twig
- **block-user-account** → includes button.twig + dropdown menu
- **block-search** → includes search form + button (search is the form's trigger)

### Why Attach Library in Template?

**Drupal Best Practice** (06-drupal-integration.md Section 4.2):
- **Method 1** (Twig): `{{ attach_library() }}` - For components with required JS
- **Method 2** (Render Array): For programmatic attachment
- **Method 3** (Preprocess): For conditional attachment

Template attachment is appropriate because:
- Form display toggle is essential functionality (not optional)
- Library is lightweight (search-form.js)
- Drupal will merge libraries (no duplicates)

### Why Not Direct Twig Include in Storybook Stories?

**Current Approach** (automatic inclusion):
- Stories call `blockSearch()` function
- Function renders block-search.twig
- Template automatically includes form
- ✅ Users see complete block in Storybook

**Previous Approach** (manual composition in stories):
- Story manually included both button + form
- ❌ Duplicated template logic
- ❌ Harder to maintain (changes in 2 places)
- ❌ Didn't match Drupal production behavior

---

## 📚 References

- **Design Spec**: `docs/design/layouts/blocks/search.md`
- **Component Development**: `.github/instructions/02-component-development.md`
- **Drupal Integration**: `.github/instructions/06-drupal-integration.md` (Section 4.2 - Libraries)
- **Storybook Standards**: `.github/instructions/03-technical-implementation.md` (Section 3)

---

## 🔍 How to Test

### Storybook (Development)
```bash
npm run watch
# Navigate to: Layouts → Blocks → Search
# Test all three stories
# Click button to toggle form (Default & WithCustomLabel)
```

### Drupal (Production)
```bash
# 1. Place block via Block UI or code
# 2. Clear cache: drush cr
# 3. Navigate to page with block
# 4. Click search button (should open form with animation)
# 5. Press ESC (should close form)
# 6. Type in input field to test focus management
```

---

**Status**: ✅ Complete and ready for Drupal integration  
**Maintainer**: Design System Team  
**Last Updated**: 2025-01-02
