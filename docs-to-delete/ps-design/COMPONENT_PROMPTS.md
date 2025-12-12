# Component Migration Prompts

## 1️⃣ BADGE Component - SVG Icon System Migration

**Objective**: Migrate Badge component from `data-icon` to SVG sprite-based icon component.

**Current Issue**: Badge uses deprecated `data-icon` attribute with old icon-font system.

**Solution**: Replace with `ps-icon` component (SVG sprite based, Drupal-compatible).

### Files to Modify
- `source/patterns/elements/badge/badge.twig`
- `source/patterns/elements/badge/badge.css`
- `source/patterns/elements/badge/badge.stories.jsx`
- `source/patterns/elements/badge/badge.yml`
- `source/patterns/elements/badge/README.md`

### Migration Details

**badge.twig** (Line 29):

CURRENT:
```twig
{%- if icon -%}
  <span class="ps-badge__icon" data-icon="{{ icon }}"></span>
{%- endif -%}
```

TO:
```twig
{%- if icon -%}
  {% include '@elements/icon/icon.twig' with {
    name: icon,
    size: 'md',
    baseClass: 'ps-badge__icon'
  } only %}
{%- endif -%}
```

**badge.css**: No changes needed (icon component handles styling)

**badge.stories.jsx**: Ensure icon examples use valid icon names from `source/patterns/documentation/icons-registry.json`

**badge.yml**: Add icon to props if not present

**README.md**: Update icon section to reference ps-icon component

### Testing Checklist
- [ ] `npm run lint:check` passes
- [ ] `npm run format:check` passes
- [ ] Storybook renders icons correctly
- [ ] Icon sizes/colors display properly
- [ ] Both with and without icon examples work

### Commit Message
```
refactor(badge): Migrate to SVG sprite-based icon component

- Replace data-icon attribute with ps-icon component
- Update badge.twig to include icon component with baseClass
- Ensure icon examples use valid sprite icon names
- Update documentation
```

---

## 2️⃣ BUTTON Component - SVG Icon System Migration

**Objective**: Migrate Button component from `data-icon` to SVG sprite-based icon component.

**Current Issue**: Button uses deprecated `data-icon` at two locations (icon-left and icon-right).

**Solution**: Replace both with `ps-icon` component with proper positioning.

### Files to Modify
- `source/patterns/elements/button/button.twig` (2 locations)
- `source/patterns/elements/button/button.css`
- `source/patterns/elements/button/button.stories.jsx`
- `source/patterns/elements/button/button.yml`
- `source/patterns/elements/button/README.md`

### Migration Details

**button.twig** (Lines 60 & 68):

CURRENT:
```twig
{% if icon %}
  <span class="{{ el_icon }}" data-icon="{{ icon }}" aria-hidden="true"></span>
{% endif %}
```

TO:
```twig
{% if icon %}
  {% include '@elements/icon/icon.twig' with {
    name: icon,
    size: 'md',
    baseClass: el_icon
  } only %}
{% endif %}
```

**Key Point**: Keep both icon-left and icon-right positions working

**button.css**: Verify icon spacing (margin between icon and text)

**button.stories.jsx**: Update all icon examples

**README.md**: Document icon positioning (left vs right)

### Testing Checklist
- [ ] Icon-left position works ✅
- [ ] Icon-right position works ✅
- [ ] Icon sizing matches text size ✅
- [ ] Spacing correct between icon and text ✅
- [ ] All button variants work with icons ✅
- [ ] Lint/format pass ✅

### Commit Message
```
refactor(button): Migrate to SVG sprite-based icon component

- Replace data-icon with ps-icon component (both positions)
- Update button.twig for icon-left and icon-right
- Ensure proper icon spacing and alignment
- Update documentation and examples
```

---

## 3️⃣ FIELD Component - SVG Icon System Migration

**Objective**: Migrate Field component from `data-icon` to SVG sprite-based icon component.

**Current Issue**: Field uses deprecated `data-icon` at two locations (left icon, right icon).

**Solution**: Replace both with `ps-icon` component with proper positioning.

### Files to Modify
- `source/patterns/elements/field/field.twig` (2 locations: lines 51 & 91)
- `source/patterns/elements/field/field.css`
- `source/patterns/elements/field/field.stories.jsx`
- `source/patterns/elements/field/field.yml`
- `source/patterns/elements/field/README.md`

### Migration Details

**field.twig** (Lines 51 & 91):

CURRENT (Left icon):
```twig
<span class="ps-field__icon ps-field__icon--left" data-icon="{{ icon }}" aria-hidden="true"></span>
```

CURRENT (Right icon):
```twig
<span class="ps-field__icon ps-field__icon--right" data-icon="{{ icon }}" aria-hidden="true"></span>
```

TO (Both):
```twig
{% include '@elements/icon/icon.twig' with {
  name: icon,
  size: 'md',
  baseClass: 'ps-field__icon',
  attributes: create_attribute({'class': icon_position == 'left' ? 'ps-field__icon--left' : 'ps-field__icon--right'})
} only %}
```

**Alternative Simpler Approach** (if icon_position logic not clear):
Create two separate includes for left/right with hardcoded classes.

**field.css**: Verify icon positioning within input context

**field.stories.jsx**: Update examples with left/right icon cases

**README.md**: Document left/right icon positioning

### Testing Checklist
- [ ] Left icon position works ✅
- [ ] Right icon position works ✅
- [ ] Icon doesn't interfere with input focus ✅
- [ ] Icon sizing appropriate for field height ✅
- [ ] Both icons together (left + right) work ✅
- [ ] Lint/format pass ✅

### Commit Message
```
refactor(field): Migrate to SVG sprite-based icon component

- Replace data-icon with ps-icon component (left and right positions)
- Update field.twig for proper icon positioning
- Maintain input field accessibility
- Update documentation
```

---

## 4️⃣ LINK Component - SVG Icon System Migration

**Objective**: Migrate Link component from `data-icon` to SVG sprite-based icon component.

**Current Issue**: Link uses deprecated `data-icon` with only right position (default).

**Solution**: Replace with `ps-icon` component with left/right positioning support.

### Files to Modify
- `source/patterns/elements/link/link.twig` (Line 45)
- `source/patterns/elements/link/link.css`
- `source/patterns/elements/link/link.stories.jsx`
- `source/patterns/elements/link/link.yml`
- `source/patterns/elements/link/README.md`

### Migration Details

**link.twig** (Line 45):

CURRENT:
```twig
<span class="{{ baseClass }}__icon" data-icon="{{ icon }}" aria-hidden="true"></span>
```

TO:
```twig
{% include '@elements/icon/icon.twig' with {
  name: icon,
  size: 'md',
  baseClass: baseClass ~ '__icon'
} only %}
```

**link.css**: Verify icon position (right-aligned by default)

**link.stories.jsx**: Add examples for icon-left and icon-right variants

**README.md**: Document icon positioning

### Testing Checklist
- [ ] Icon displays with link text ✅
- [ ] Icon position correct (right by default) ✅
- [ ] Icon-left variant works ✅
- [ ] Icon sizing appropriate ✅
- [ ] Link underline behavior preserved ✅
- [ ] Lint/format pass ✅

### Commit Message
```
refactor(link): Migrate to SVG sprite-based icon component

- Replace data-icon with ps-icon component
- Support icon-left and icon-right positioning
- Update link.twig with proper icon component include
- Update documentation
```

---

## 5️⃣ DIVIDER Component - SVG Icon System Migration

**Objective**: Migrate Divider component from `data-icon` to SVG sprite-based icon component.

**Current Issue**: Divider uses deprecated `data-icon` for center icon decoration.

**Solution**: Replace with `ps-icon` component for center positioning.

### Files to Modify
- `source/patterns/elements/divider/divider.twig` (Line 29)
- `source/patterns/elements/divider/divider.css`
- `source/patterns/elements/divider/divider.stories.jsx`
- `source/patterns/elements/divider/divider.yml`
- `source/patterns/elements/divider/README.md`

### Migration Details

**divider.twig** (Line 29):

CURRENT:
```twig
<span class="{{ baseClass }}__icon" data-icon="{{ icon }}" aria-hidden="true"></span>
```

TO:
```twig
{% include '@elements/icon/icon.twig' with {
  name: icon,
  size: 'md',
  baseClass: baseClass ~ '__icon'
} only %}
```

**divider.css**: Ensure icon is centered on divider line

**divider.stories.jsx**: Ensure examples match available icon names

**README.md**: Document icon usage with divider

### Testing Checklist
- [ ] Icon centers on divider line ✅
- [ ] Icon sizing visible ✅
- [ ] Divider with/without icon works ✅
- [ ] Color variants preserve icon visibility ✅
- [ ] Lint/format pass ✅

### Commit Message
```
refactor(divider): Migrate to SVG sprite-based icon component

- Replace data-icon with ps-icon component
- Ensure icon centers on divider
- Update divider.twig
- Update documentation
```

---

## 6️⃣ EYEBROW Component - SVG Icon System Migration

**Objective**: Migrate Eyebrow component from `data-icon` to SVG sprite-based icon component.

**Current Issue**: Eyebrow uses deprecated `data-icon` for optional prefix icon.

**Solution**: Replace with `ps-icon` component for prefix decoration.

### Files to Modify
- `source/patterns/elements/eyebrow/eyebrow.twig` (Line 36)
- `source/patterns/elements/eyebrow/eyebrow.css`
- `source/patterns/elements/eyebrow/eyebrow.stories.jsx`
- `source/patterns/elements/eyebrow/eyebrow.yml`
- `source/patterns/elements/eyebrow/README.md`

### Migration Details

**eyebrow.twig** (Line 36):

CURRENT:
```twig
<span class="ps-eyebrow__icon" data-icon="{{ icon }}" aria-hidden="true"></span>
```

TO:
```twig
{% include '@elements/icon/icon.twig' with {
  name: icon,
  size: 'md',
  baseClass: 'ps-eyebrow__icon'
} only %}
```

**eyebrow.css**: Verify icon margin/spacing before text

**eyebrow.stories.jsx**: Update examples with icon variations

**README.md**: Document icon prefix usage

### Testing Checklist
- [ ] Icon displays before eyebrow text ✅
- [ ] Spacing between icon and text correct ✅
- [ ] Icon sizing matches eyebrow ✅
- [ ] With/without icon works ✅
- [ ] Color variants work with icon ✅
- [ ] Lint/format pass ✅

### Commit Message
```
refactor(eyebrow): Migrate to SVG sprite-based icon component

- Replace data-icon with ps-icon component
- Update eyebrow.twig for icon prefix
- Maintain proper spacing
- Update documentation
```

---

## 📋 Overall Completion Checklist

- [ ] Badge - Migration complete
- [ ] Button - Migration complete
- [ ] Field - Migration complete
- [ ] Link - Migration complete
- [ ] Divider - Migration complete
- [ ] Eyebrow - Migration complete

### Final Steps
- [ ] All 6 components lint/format pass
- [ ] All 6 components tested in Storybook
- [ ] Update CHANGELOG.md with migration summary
- [ ] Single final commit summarizing all migrations
- [ ] Delete or empty `source/props/icons.css` (no longer needed)

---

## 🔗 Resources
- Icon Component Reference: `source/patterns/elements/icon/icon.twig`
- Icon Registry: `source/patterns/documentation/icons-registry.json` (auto-generated)
- Migration Guide: `docs/ps-design/ICON_MIGRATION_WORKFLOW.md`
