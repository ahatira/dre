# 🚀 Icon System Migration - Quick Start Guide

## Where to Find Instructions

### 📖 Main Documents
1. **Overall Workflow**: `docs/ps-design/ICON_MIGRATION_WORKFLOW.md`
   - Strategy overview
   - Two migration approaches
   - Step-by-step process
   - Drupal compatibility notes

2. **Component Prompts**: `docs/ps-design/COMPONENT_PROMPTS.md`
   - 6 detailed component-specific instructions
   - Before/after code examples
   - Testing checklists
   - Commit templates

---

## 🎯 Start Here (Recommended Order)

### Phase 1: Simple Components (30 min each)

**1. Badge** ← Start here (simplest case)
- Single optional icon
- Straightforward include replacement

**2. Divider** 
- Center icon positioning
- Minimal changes

**3. Eyebrow**
- Prefix icon decoration
- Similar pattern to badge

### Phase 2: Dual-Position Components (45 min each)

**4. Button**
- Icon positions: left & right
- Both must work together

**5. Link**
- Similar to button
- Right-aligned by default

### Phase 3: Complex Component (60 min)

**6. Field**
- Conditional left/right icons
- Input field integration
- Most complex case

---

## 📋 Per-Component Workflow

For **each component**, follow this pattern:

### Step 1: Read the Prompt
Go to `docs/ps-design/COMPONENT_PROMPTS.md` → Find your component section

### Step 2: Update Files
- `{component}.twig` - Replace `data-icon` with icon component include
- `{component}.css` - Verify spacing/alignment
- `{component}.stories.jsx` - Update icon examples
- `{component}.yml` - Add/update icon property
- `{component}/README.md` - Document icon usage

### Step 3: Test
```bash
npm run lint:check      # ✅ Must pass
npm run format:check    # ✅ Must pass
npm run watch           # Test in Storybook
```

### Step 4: Commit
Follow the template in the prompt (git commit message provided)

### Step 5: Continue
Move to next component

---

## 🔄 The Key Migration Pattern

### OLD (data-icon):
```twig
<span class="ps-badge__icon" data-icon="{{ icon }}"></span>
```

### NEW (SVG sprite with ps-icon component):
```twig
{% include '@elements/icon/icon.twig' with {
  name: icon,
  size: 'md',
  baseClass: 'ps-badge__icon'
} only %}
```

**Key points**:
- `name`: Icon name from sprite (e.g., 'check', not 'icon-check')
- `baseClass`: Parent BEM class (makes component-scoped)
- `only`: Drupal requirement (isolates variable scope)
- Everything else handled by icon component

---

## 💡 Important Notes

### Icon Names
Get from: `source/patterns/documentation/icons-list.json`
Examples: `check`, `arrow-right`, `calendar`, `search`, etc.

### Drupal Compatibility ✅
- ✅ Uses Twig `include` with `only`
- ✅ No arrow functions
- ✅ No `.filter()` or `.map()`
- ✅ Works with Drupal 10/11

### Component API (ps-icon)
**Required**:
- `name` - icon name

**Optional**:
- `size` - xs|sm|md|lg|xl|xxl (default: md)
- `color` - default|primary|secondary|success|warning|danger|info (default: default)
- `baseClass` - parent BEM class for composition
- `ariaLabel` - accessibility label
- `disabled` - disabled state
- `attributes` - extra HTML attributes

---

## ✅ Completion Checklist

- [ ] **Badge** migrated and tested
- [ ] **Button** migrated and tested
- [ ] **Divider** migrated and tested
- [ ] **Eyebrow** migrated and tested
- [ ] **Field** migrated and tested
- [ ] **Link** migrated and tested
- [ ] All lint/format pass
- [ ] CHANGELOG.md updated
- [ ] Final summary commit created

---

## 🔗 Resources

| Resource | Location |
|----------|----------|
| Icon Component | `source/patterns/elements/icon/icon.twig` |
| Icon List | `source/patterns/documentation/icons-list.json` |
| Icon Sprite | `source/assets/icons/icons-sprite.svg` |
| Build Script | `scripts/build-icons.mjs` |
| Migration Workflow | `docs/ps-design/ICON_MIGRATION_WORKFLOW.md` |
| Component Prompts | `docs/ps-design/COMPONENT_PROMPTS.md` |

---

## 🚨 Troubleshooting

**Issue**: Icon doesn't render
- **Check**: Icon name exists in `icons-list.json`
- **Check**: Using `name` not `class`
- **Check**: SVG sprite built (`npm run icons:build`)

**Issue**: Lint fails
- **Check**: Using `{% include ... only %}`
- **Check**: No arrow functions (`v => v`)
- **Check**: No `.filter()` or `.map()`

**Issue**: Icon styling wrong
- **Check**: CSS class specificity (baseClass handling)
- **Check**: Icon size prop matches context
- **Check**: Spacing margins in parent CSS

---

## 📞 Next Steps

1. Open: `docs/ps-design/COMPONENT_PROMPTS.md`
2. Find: **BADGE** section
3. Follow: Step-by-step migration for badge
4. Test: `npm run lint:check && npm run format:check`
5. View: `npm run watch` → http://localhost:6006
6. Commit: Use provided template
7. Repeat: For each remaining component

---

**Ready? Go to `docs/ps-design/COMPONENT_PROMPTS.md` and start with BADGE!** 🎉
