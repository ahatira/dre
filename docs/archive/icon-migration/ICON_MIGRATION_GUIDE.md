# Icon Migration Guide for Component Developers

**Date**: 2025-12-08  
**Phase**: 3  
**Context**: Now that all 141 icons are accessible via `data-icon`, components can leverage more icons without code changes.

---

## Overview

With Phase 1-2 complete, **all 141 icons are now available** across 3 coordinated access patterns:

1. ✅ **Twig component** - `{% include '@elements/icon/icon.twig' %}`
2. ✅ **data-icon attribute** - `<span data-icon="name"></span>` 
3. ✅ **SVG direct** - `<use href="/icons/icons-sprite.svg#icon-name"/>`

**Before**: Limited to ~35 manually-maintained icons  
**After**: Full access to 141 icons, auto-generated, zero maintenance

---

## Migration Paths

### Path A: No Changes Needed ✅

Components using **Twig icon component** work as-is:

```twig
{# Already using proper pattern - no changes needed #}
{% include '@elements/icon/icon.twig' with { icon: 'check' } only %}
```

**Components in this category**:
- Button (icon support via Twig include)
- Badge (icon integration)
- Other atom-based icon usage

**Action**: None - already optimal

---

### Path B: Upgrade from Inline SVG to Twig Component

**Current approach** (inline SVG):
```twig
{# components/search-bar/search-bar.twig #}
<svg class="ps-search-bar__icon" aria-hidden="true">
  <use xlink:href="#icon-search" />
</svg>
```

**Upgraded approach** (Twig component):
```twig
{# components/search-bar/search-bar.twig #}
{% include '@elements/icon/icon.twig' with {
  icon: 'search',
  class: 'ps-search-bar__icon'
} only %}
```

**Benefits**:
- ✅ Consistent with atom-based approach
- ✅ Built-in size/color support
- ✅ Better accessibility handling
- ✅ Smaller generated HTML (no viewBox duplication)
- ✅ Automatic fallback for unsupported browsers

**Components to migrate**:

1. **search-bar** - Currently uses inline `<use xlink:href="#icon-search">`
   ```twig
   {# Before (4 lines) #}
   <svg class="ps-search-bar__icon" aria-hidden="true">
     <use xlink:href="#icon-search" />
   </svg>

   {# After (1 line) #}
   {% include '@elements/icon/icon.twig' with { icon: 'search', class: 'ps-search-bar__icon' } only %}
   ```

2. **form-field** - Currently uses inline `<use xlink:href="#icon-alert-circle">`
   ```twig
   {# Before #}
   <svg class="form-error-icon" aria-hidden="true">
     <use xlink:href="#icon-alert-circle" />
   </svg>

   {# After #}
   {% include '@elements/icon/icon.twig' with { icon: 'alert', class: 'form-error-icon' } only %}
   ```

3. **pagination** - Currently uses inline SVG for prev/next
   ```twig
   {# Before #}
   <svg class="icon icon--prev" aria-hidden="true">
     <use xlink:href="#icon-arrow-left" />
   </svg>

   {# After #}
   {% include '@elements/icon/icon.twig' with { icon: 'arrow-left', class: 'icon icon--prev' } only %}
   ```

4. **dropdown** - Currently uses inline SVG
   ```twig
   {# Before #}
   <svg class="ps-dropdown__icon" aria-hidden="true">
     <use xlink:href="#icon-chevron-down" />
   </svg>

   {# After #}
   {% include '@elements/icon/icon.twig' with { icon: 'chevron-down', class: 'ps-dropdown__icon' } only %}
   ```

5. **stepper** - Currently uses inline SVG
6. **tooltip** - Currently uses inline SVG

---

### Path C: Use data-icon Attribute (Lightweight)

For simple, lightweight icon needs (buttons, badges, form elements):

```html
<!-- Simple span element -->
<span data-icon="check"></span>

<!-- With classes -->
<span class="button__icon" data-icon="check"></span>

<!-- In component slots -->
<div class="badge">
  <span class="badge__icon" data-icon="award"></span>
  <span class="badge__text">Premium</span>
</div>
```

**When to use**:
- ✅ HTML-based components (non-Twig)
- ✅ Dynamic icon rendering
- ✅ Minimal markup
- ✅ CSS-controlled styling

**Example component**:
```twig
{# components/badge/badge.twig #}
<div class="ps-badge {{ attributes.addClass('ps-badge--' ~ variant) }}">
  {% if icon %}
    <span class="ps-badge__icon" data-icon="{{ icon }}"></span>
  {% endif %}
  <span class="ps-badge__text">{{ text }}</span>
</div>
```

```yml
# components/badge/badge.yml
variant: 'default'
text: 'Badge'
icon: 'award'
```

---

## Available Icons by Category

All 141 icons are now categorized and documented. Access the full list:

- **File**: `source/patterns/documentation/icons-registry.json`
- **Storybook**: Elements/Icon → CategorizedGallery story
- **CLI**: `npm run tokens:check -- "--primary"` (for token search)

### Quick Category Reference

| Category | Count | Examples |
|----------|-------|----------|
| **UI** | ~8 | alert, check, info, help, star, heart |
| **Navigation** | ~25 | arrow-down, arrow-left, arrow-right, chevron-*, menu |
| **Forms** | ~6 | checkbox-off, checkbox-on, radio-off, radio-on |
| **Communication** | 3 | email, phone, share |
| **Media** | ~3 | download, upload, picture |
| **Business** | ~8 | map, pin, building, office, parking, store |

---

## Migration Checklist

### For Each Component

- [ ] Identify current icon implementation (inline SVG, Twig include, data-icon)
- [ ] Check if migrating to Twig component is beneficial
- [ ] Update Twig template (replace inline SVG with icon Twig include)
- [ ] Update component YAML with icon prop (if applicable)
- [ ] Update component stories to showcase icon variations
- [ ] Test in Storybook (visual regression check)
- [ ] Update README.md to document icon usage
- [ ] Run conformity audit: `npm run build`
- [ ] Commit with message: `refactor(components): Update {ComponentName} to use icon component`

### Code Changes Template

```twig
{# BEFORE: Inline SVG (high maintenance) #}
<svg class="component__icon" aria-hidden="true" focusable="false">
  <use xlink:href="#icon-search" />
</svg>

{# AFTER: Twig component (maintainable) #}
{% include '@elements/icon/icon.twig' with {
  icon: 'search',
  class: 'component__icon'
} only %}
```

---

## Benefits of Migration

### Code Reduction
- ✅ Fewer lines per component (4 → 1 for inline SVG replacements)
- ✅ Consistent approach across all components
- ✅ Easier to maintain and debug

### Functionality Expansion
- ✅ Now supports size control (xs, sm, md, lg, xl, xxl)
- ✅ Now supports color variations (primary, secondary, success, danger, warning, info)
- ✅ Built-in disabled state handling
- ✅ Proper accessibility defaults

### Maintenance
- ✅ Zero manual CSS maintenance (auto-generated)
- ✅ Icon additions require no code changes
- ✅ Registry-based validation
- ✅ Single source of truth

### Performance
- ✅ Fewer bytes in generated HTML (Twig handles duplication)
- ✅ CSS sprite remains identical (no size increase)
- ✅ Shared icon component = code deduplication

---

## Real-World Example

### Search Bar Component Migration

**Before** (manually maintained inline SVG):
```twig
{# components/search-bar/search-bar.twig #}
<div class="{{ attributes.addClass('ps-search-bar') }}">
  <input 
    type="text" 
    class="ps-search-bar__input" 
    placeholder="{{ placeholder }}"
  />
  {% if show_icon %}
    <svg class="ps-search-bar__icon" aria-hidden="true" focusable="false">
      <use xlink:href="#icon-search" />
    </svg>
  {% endif %}
</div>
```

**After** (using icon component):
```twig
{# components/search-bar/search-bar.twig #}
<div class="{{ attributes.addClass('ps-search-bar') }}">
  <input 
    type="text" 
    class="ps-search-bar__input" 
    placeholder="{{ placeholder }}"
  />
  {% if show_icon %}
    {% include '@elements/icon/icon.twig' with {
      icon: 'search',
      class: 'ps-search-bar__icon'
    } only %}
  {% endif %}
</div>
```

**Benefits**:
- 4 lines → 1 line (icon rendering)
- Consistent with other icon usage in theme
- Supports color/size variations if needed
- Zero maintenance going forward

---

## FAQ

### Q: Do I have to migrate existing components?

**A**: No, but it's recommended for consistency. Current inline SVG implementations work fine, but:
- Twig component approach is cleaner
- Easier to support future icon variations (size, color)
- Reduces code duplication

### Q: What if I need a new icon not in the registry?

**A**: 
1. Add SVG file to `source/icons-source/` 
2. Run `npm run build`
3. Icon automatically available in all 3 patterns
4. No code changes needed in components

### Q: Can I mix patterns in the same component?

**A**: Yes, but not recommended. For consistency:
- Twig-based components: Use icon Twig component
- HTML-heavy components: Use data-icon attribute
- One approach per component

### Q: Does migrating break existing functionality?

**A**: No. All 3 patterns access the same sprite, so visual output is identical. Migration only changes how you reference icons in code.

### Q: How do I handle icon fallbacks for old browsers?

**A**: The Twig component automatically includes fallback handling. Using inline SVG or data-icon attribute requires manual fallback logic.

---

## Tools & References

### Registry Inspection
```bash
# View all icon names
cat source/patterns/documentation/icons-registry.json | grep '"names"' -A 200

# Count icons by category
cat source/patterns/documentation/icons-registry.json | grep -A 10 '"categories"'

# Search for specific icon
grep -i "search" source/patterns/documentation/icons-registry.json
```

### Storybook
- Open: `http://localhost:6006` (after `npm run watch`)
- Navigate: Elements/Icon
- Stories: Default, AllSizes, AllColors, AllStates, Gallery, **CategorizedGallery**

### Component Templates
All migrated components should follow this template:

```twig
{# Icon inclusion (recommended pattern) #}
{% include '@elements/icon/icon.twig' with {
  icon: icon_name,
  size: size,
  color: color,
  class: class,
  disabled: disabled,
  ariaLabel: ariaLabel
} only %}
```

---

## Timeline & Priority

### High Priority (Quick wins)
- [ ] Search Bar (1 icon, 2-3 min)
- [ ] Form Field (1 icon, 2-3 min)
- [ ] Pagination (2 icons, 3-5 min)

### Medium Priority
- [ ] Dropdown (1 icon, 2-3 min)
- [ ] Stepper (1+ icons, 5-10 min)
- [ ] Tooltip (1 icon, 2-3 min)

### Low Priority (Can defer)
- [ ] Components with conditional icon usage
- [ ] Dynamic icon rendering via props
- [ ] Old inline SVG implementations

**Estimated Total Time**: 30-45 minutes for all listed components

---

## Success Metrics

After migration:
- ✅ All components use one of 3 standard patterns
- ✅ Zero manual SVG maintenance
- ✅ All 141 icons documented in registry
- ✅ Storybook shows icon variations (size, color, state)
- ✅ Conformity audit passes 100%
- ✅ Code review complete

---

## Next Steps

1. **Immediate** (Next 30 min): Migrate search-bar, form-field, pagination
2. **Short-term** (This week): Migrate remaining components
3. **Long-term** (Ongoing): Use icon patterns in new component development

---

**Questions?** Refer to:
- Icon System docs: `docs/ps-design/ICON_SYSTEM_OVERHAUL.md`
- Quick Start: `docs/ps-design/ICON_QUICK_START.md`
- Implementation guide: `docs/ps-design/ICON_IMPLEMENTATION_ROADMAP.md`
