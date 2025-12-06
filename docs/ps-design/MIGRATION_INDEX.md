# Icon System Migration - Documents Index

**Status**: Complete documentation package ready for execution  
**Created**: December 6, 2025  
**Format**: Markdown guides with step-by-step instructions  

---

## 📖 Documentation Files

### 1. [ICON_MIGRATION_START.md](./ICON_MIGRATION_START.md)
**Purpose**: Quick entry point for migration  
**Read Time**: 5 minutes  
**Contains**:
- Where to find main instructions
- Recommended component order
- Quick reference of key migration pattern
- Drupal compatibility notes
- Troubleshooting guide

**👉 Start here for orientation**

---

### 2. [ICON_MIGRATION_WORKFLOW.md](./ICON_MIGRATION_WORKFLOW.md)
**Purpose**: Detailed migration strategy and process  
**Read Time**: 15 minutes  
**Contains**:
- Migration strategy overview (Approach A vs B)
- List of all components requiring migration
- 6-step migration workflow template
- Migration checklist per component
- Drupal compatibility requirements
- Icon names and sprite documentation
- Tips and completion criteria

**👉 Read for understanding the overall process**

---

### 3. [COMPONENT_PROMPTS.md](./COMPONENT_PROMPTS.md)
**Purpose**: Specific step-by-step instructions for each component  
**Read Time**: 10 minutes per component  
**Contains**: 6 detailed prompts, one per component:

#### Component 1: Badge ✏️
- **Complexity**: Easy (single optional icon)
- **Time**: 30 minutes
- **Files**: badge.twig, badge.css, badge.stories.jsx, badge.yml, README.md
- **Includes**: Before/after code, testing checklist, commit template

#### Component 2: Button ✏️
- **Complexity**: Medium (dual positions: left & right)
- **Time**: 45 minutes
- **Files**: button.twig (2 locations), button.css, button.stories.jsx, button.yml, README.md
- **Includes**: Icon position handling, dual-location updates

#### Component 3: Divider ✏️
- **Complexity**: Easy (center icon)
- **Time**: 30 minutes
- **Files**: divider.twig, divider.css, divider.stories.jsx, divider.yml, README.md
- **Includes**: Centered positioning, minimal changes

#### Component 4: Eyebrow ✏️
- **Complexity**: Easy (prefix icon)
- **Time**: 30 minutes
- **Files**: eyebrow.twig, eyebrow.css, eyebrow.stories.jsx, eyebrow.yml, README.md
- **Includes**: Icon prefix decoration

#### Component 5: Field ✏️
- **Complexity**: Complex (dual positions with input context)
- **Time**: 60 minutes
- **Files**: field.twig (2 locations), field.css, field.stories.jsx, field.yml, README.md
- **Includes**: Conditional left/right icons, input field integration

#### Component 6: Link ✏️
- **Complexity**: Medium (single position: right)
- **Time**: 45 minutes
- **Files**: link.twig, link.css, link.stories.jsx, link.yml, README.md
- **Includes**: Icon positioning, direction support

**👉 Use for executing migration of each component**

---

## 🎯 Quick Reference

### Migration Pattern
```twig
{# OLD - data-icon attribute #}
<span class="ps-badge__icon" data-icon="{{ icon }}"></span>

{# NEW - SVG sprite ps-icon component #}
{% include '@elements/icon/icon.twig' with {
  name: icon,
  size: 'md',
  baseClass: 'ps-badge__icon'
} only %}
```

### Component Order (Recommended)
1. **Badge** ← Start here (easiest)
2. Divider
3. Eyebrow
4. Button
5. Link
6. **Field** ← Most complex

### Execution Checklist (Per Component)
- [ ] Read component prompt in COMPONENT_PROMPTS.md
- [ ] Update .twig file (replace data-icon)
- [ ] Update .css if needed (verify styling)
- [ ] Update .stories.jsx (icon examples)
- [ ] Update .yml (icon property)
- [ ] Update README.md (documentation)
- [ ] Run: `npm run lint:check`
- [ ] Run: `npm run format:check`
- [ ] Test: `npm run watch` (Storybook visual check)
- [ ] Commit: Use provided template
- [ ] Move to next component

### Total Estimated Time
- Badge: 30 min
- Divider: 30 min
- Eyebrow: 30 min
- Button: 45 min
- Link: 45 min
- Field: 60 min
- **Total: ~4.5 hours**

---

## 📋 Files Modified

### Documentation
- `docs/ps-design/ICON_MIGRATION_START.md` ✅ Created
- `docs/ps-design/ICON_MIGRATION_WORKFLOW.md` ✅ Created
- `docs/ps-design/COMPONENT_PROMPTS.md` ✅ Created
- `docs/ps-design/README.md` ✅ Updated with guide links
- `docs/ps-design/INDEX.md` (to be updated when components complete)

### Source Files (To be modified per prompt)
- `source/patterns/elements/badge/*` 
- `source/patterns/elements/button/*`
- `source/patterns/elements/divider/*`
- `source/patterns/elements/eyebrow/*`
- `source/patterns/elements/field/*`
- `source/patterns/elements/link/*`

### Config Files
- `source/props/icons.css` ✅ Updated (legacy removed)

---

## 🚀 Getting Started

### Step 1: Orient Yourself (5 min)
```bash
# Read quick start guide
cat docs/ps-design/ICON_MIGRATION_START.md
```

### Step 2: Plan Your Work (10 min)
```bash
# Read overall workflow
cat docs/ps-design/ICON_MIGRATION_WORKFLOW.md
```

### Step 3: Execute Component 1 (30 min)
```bash
# Open component prompts
cat docs/ps-design/COMPONENT_PROMPTS.md

# Find and follow Badge section
# Execute each file change
# Test and commit
```

### Step 4-6: Repeat for Remaining Components
Continue with Button, Field, etc.

---

## ✅ Completion Verification

When all components are migrated:

- [ ] Badge migrated ✅
- [ ] Button migrated ✅
- [ ] Divider migrated ✅
- [ ] Eyebrow migrated ✅
- [ ] Field migrated ✅
- [ ] Link migrated ✅
- [ ] All lint/format pass ✅
- [ ] All Storybook stories updated ✅
- [ ] CHANGELOG.md updated ✅
- [ ] Final summary commit created ✅

---

## 📞 Support Resources

### Documentation References
- **Icon Component**: `source/patterns/elements/icon/icon.twig`
- **Icon List**: `source/patterns/documentation/icons-list.json` (139 icons)
- **Icon Sprite**: `source/assets/icons/icons-sprite.svg`
- **Build Script**: `scripts/build-icons.mjs`

### Drupal Compatibility
- Uses Twig template includes with `only` parameter
- No JavaScript-style array methods
- Compatible with Drupal 10/11 Twig engine
- Works with Libraries API (ps.libraries.yml)

### Testing
```bash
# Linting
npm run lint:check

# Formatting
npm run format:check

# Development
npm run watch        # http://localhost:6006

# Build
npm run build
```

---

## 📝 Notes

- Each prompt is self-contained and can be executed independently
- Commit templates provided for git history
- All components follow same pattern (replace data-icon with icon component)
- Documentation updates included in each prompt
- Testing procedures ensure quality before commit

---

**Created**: December 6, 2025  
**Package**: Complete icon system migration documentation  
**Ready for**: Component-by-component execution  
**Estimated Duration**: 4.5 hours total  
**Output**: 6 migrated components + updated documentation  
