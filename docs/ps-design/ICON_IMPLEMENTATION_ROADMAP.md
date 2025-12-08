# 🎯 Icon System Implementation Roadmap

**Status**: Ready for Multi-Expert Review  
**Timeline**: 3 weeks (phased approach)  
**Risk Level**: LOW (backward compatible)

---

## 📊 Current State vs Proposed

### BEFORE: Fragmented System

```
┌─────────────────────────────────────────────────────────────────┐
│  ICON SOURCE                                                     │
│  source/icons-source/*.svg (139 SVGs)                           │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ├─→ scripts/build-icons.mjs
                         │
        ┌────────────────┼────────────────┬─────────────────┐
        │                │                │                 │
        ↓                ↓                ↓                 ↓
   [Sprite SVG]  [icons-list.json]  [icons.css MANUAL]  [Storybook]
   (139 SVGs)    (139 icons)        (35 rules only)      (partial)
        │                │                │                 │
        └────────────────┴────────────────┴─────────────────┘
                         │
        ┌────────────────┼────────────────┬─────────────────┐
        │                │                │                 │
        ↓                ↓                ↓                 ↓
   Pattern 1      Pattern 2           Pattern 3          🚨 CONFUSION
   <span           {% include         <svg><use>         ❌ Not all
    data-icon      '@elements/icon'    ...                  139 icons
    "check">       with {...}>         </svg>               accessible
   </span>                                                ❌ CSS not
                                                           auto-generated
                                                         ❌ Manual sync
                                                           3 files
```

### AFTER: Unified System

```
┌──────────────────────────────────────────────────────────────────┐
│  ICON SOURCE                                                      │
│  source/icons-source/*.svg (139 SVGs)                            │
└────────────────┬─────────────────────────────────────────────────┘
                 │
                 ├─→ scripts/build-icons.mjs (ENHANCED)
                 │
     ┌───────────┼────────────────┬──────────────┬────────────┐
     │           │                │              │            │
     ↓           ↓                ↓              ↓            ↓
  [Sprite]  [icons-generated.css] [icons-     [Storybook]  [Validation]
  139 SVGs  ALL 139 rules         registry.   ALL 139      icons-types
            AUTO-GENERATED        json]       icons        .d.ts
                                  (map)
     │           │                │              │            │
     └───────────┴────────────────┴──────────────┴────────────┘
                 │
        ┌────────┼────────┬────────────┐
        │        │        │            │
        ↓        ↓        ↓            ↓
    Pattern 1  Pattern 2 Pattern 3  Validation
    <span      {% icon  <svg><use>  icon-types
     data-icon  %}       </svg>      registry
     "check">   (SVG)                checks
    </span>
    (CSS)
    
    ✅ UNIFIED: 1 Build Backend, 3 Access Methods
    ✅ COMPLETE: ALL 139 icons supported
    ✅ AUTOMATIC: Drop SVG → CSS + JSON auto-regenerated
    ✅ VALIDATED: No broken icon references
```

---

## 🔧 Implementation Tasks

### PHASE 1: Build System Enhancement (3-4 hours)

#### Task 1.1: Enhance build-icons.mjs

**File**: `scripts/build-icons.mjs`

**Changes**:
1. Add function `generateIconsCss(symbols, iconNames)`
   - Input: Array of icon names from SVG scan
   - Output: Complete CSS rules for [data-icon="*"]
   - File: `source/props/icons-generated.css`

2. Add function `generateIconsRegistry(iconNames)`
   - Input: Array of icon names
   - Output: JSON map {names: [...], categories: {...}}
   - File: `source/patterns/documentation/icons-registry.json`

3. Enhance main `buildIcons()` to call both
   ```javascript
   async function buildIcons() {
     const svgFiles = await glob('*.svg', { cwd: ICONS_SOURCE_DIR });
     const iconNames = svgFiles.map(f => path.basename(f, '.svg'));
     
     // Existing
     await generateSpriteFile(svgFiles);
     
     // NEW
     await generateIconsCss(iconNames);      // → icons-generated.css
     await generateIconsRegistry(iconNames); // → icons-registry.json
     
     console.log(`✔ Built 139 icons (sprite, css, registry)`);
   }
   ```

4. Update CLI output to show all 3 outputs
   ```
   ✔ Updated sprite (139 symbols) → source/assets/icons/icons-sprite.svg
   ✔ Generated CSS (139 rules) → source/props/icons-generated.css
   ✔ Generated registry → source/patterns/documentation/icons-registry.json
   ```

**Acceptance Criteria**:
- [ ] `npm run build:icons` generates all 3 files
- [ ] icons-generated.css has 139 [data-icon="..."] rules
- [ ] icons-registry.json is valid JSON with all names
- [ ] Watch mode regenerates on SVG changes
- [ ] No manual icons.css edits needed going forward

---

#### Task 1.2: Generate Initial Files

```bash
cd c:/wamp64/www/ps_theme
npm run build:icons
# Should generate:
# ✔ source/assets/icons/icons-sprite.svg (139 symbols)
# ✔ source/props/icons-generated.css (139 rules) [NEW]
# ✔ source/patterns/documentation/icons-registry.json [NEW]
```

**Verify**:
```bash
# Check file sizes
wc -l source/props/icons-generated.css
# Should be ~200+ lines

grep -c '\[data-icon=' source/props/icons-generated.css
# Should be 139

cat source/patterns/documentation/icons-registry.json | jq '.names | length'
# Should be 139
```

---

### PHASE 2: CSS Integration (2-3 hours)

#### Task 2.1: Update Main CSS Import

**File**: `source/patterns/styles.css`

```css
/* Before */
@import 'props/icons.css'; /* Manual, only 35 icons */

/* After */
@import 'props/icons-generated.css'; /* Auto-generated, 139 icons */
```

#### Task 2.2: Deprecate Manual icons.css

**File**: `source/props/icons.css`

Add header:
```css
/**
 * DEPRECATED - Use icons-generated.css instead
 * This file is no longer maintained.
 * All [data-icon="..."] rules are auto-generated by scripts/build-icons.mjs
 * 
 * To add an icon:
 * 1. Drop new SVG in source/icons-source/
 * 2. npm run build:icons
 * 3. icons-generated.css updates automatically
 */
```

**Decision**: Keep for reference or delete? (Recommend: delete after verification)

---

#### Task 2.3: Verify CSS Generation

```bash
# Build system
npm run build

# Check that styles.css compiles
npm run build:styles

# Verify all 139 icons accessible
for icon in check search arrow-right chevron-down; do
  grep "\[data-icon=\"$icon\"\]" source/props/icons-generated.css || echo "Missing: $icon"
done
```

---

### PHASE 3: Documentation & Migration (3-4 hours)

#### Task 3.1: Create ICON_SYSTEM.md (NEW)

**File**: `docs/ps-design/ICON_SYSTEM.md`

Structure:
```markdown
# Icon System Documentation

## 🎯 Overview
- 139 icons available
- 3 access patterns (unified backend)
- Auto-generated CSS + validation registry

## 📚 3 Access Patterns

### Pattern 1: ps-icon Component (RECOMMENDED)
Use when you need styling control (size, color, states)

### Pattern 2: data-icon Attribute (SIMPLE)
Use for static badges, simple decorative icons

### Pattern 3: SVG Direct (FALLBACK)
Use for custom SVG styling, animations

## ✅ Available Icons
[Dynamic list from icons-registry.json]

## 🔍 Icon Reference
[Storybook link]

## ⚙️ Adding Icons
1. Drop SVG in source/icons-source/
2. npm run build
3. Done! (CSS + registry auto-updated)
```

---

#### Task 3.2: Update copilot-instructions.md

**File**: `.github/copilot-instructions.md`

**Changes** to "Icon System Reference" section:

```markdown
### 🎭 Icon System Reference (Updated)

**Status**: Unified system with 3 access patterns (1 backend)

#### The 3 Patterns

1. **ps-icon Component** (RECOMMENDED)
   ```twig
   {% include '@elements/icon/icon.twig' with {
     name: 'check',
     size: 'md',
     color: 'primary'
   } only %}
   ```

2. **data-icon Attribute** (SIMPLE)
   ```html
   <span class="ps-badge__icon" data-icon="check"></span>
   ```

3. **SVG Sprite Direct** (FALLBACK)
   ```html
   <svg class="ps-button__icon">
     <use href="/icons/icons-sprite.svg#icon-check"></use>
   </svg>
   ```

#### Icon Prefix Rules

**Backend**: SVG IDs have `icon-` prefix (build generated)
- Sprite IDs: `#icon-check`, `#icon-search`
- CSS generated: `[data-icon="check"]`, `[data-icon="search"]`

**Code (Twig/HTML)**: NEVER include `icon-` prefix
```twig
✅ CORRECT: name: 'check'
❌ WRONG: name: 'icon-check'
```

#### All 139 Icons Available

See: `source/patterns/documentation/icons-registry.json`

Storybook: [Elements → Icon story](http://localhost:6006)
```

---

#### Task 3.3: Add Icon Validation Guide

**File**: `.github/instructions/icons.instructions.md` (NEW)

```markdown
# Icon System Instructions

**ApplyTo**: `source/patterns/**/*.twig`, `source/props/icons*.css`

## Rules

### Icon Naming
- ✅ Use icon name without prefix: `name: 'check'`
- ❌ Never include `icon-` prefix in code
- ✅ Names match filenames: `check.svg` → `name: 'check'`
- Verify in: `source/patterns/documentation/icons-registry.json`

### Access Patterns

#### When to Use ps-icon Component
```twig
{% include '@elements/icon/icon.twig' with {
  name: 'check',
  size: 'md',
  color: 'primary',
  ariaLabel: 'Optional label'
} only %}
```
- Need styling control (size, color)
- Need accessibility label
- Part of styled component

#### When to Use data-icon Attribute
```html
<span data-icon="check"></span>
```
- Simple decorative icon
- Static sizing (1em = inherit font size)
- No styling variations

#### When to Use SVG Direct
```html
<svg class="custom-class">
  <use href="/icons/icons-sprite.svg#icon-check"></use>
</svg>
```
- Custom SVG animations
- Complex styling context
- Raw SVG control needed

### Validation

**Available icons**: Run
```bash
npm run tokens:check -- icons-registry
# Or check: source/patterns/documentation/icons-registry.json
```

**During build**: 
```bash
npm run build
# Fails if:
# - Icon name in code not in registry
# - SVG missing from source/icons-source/
# - CSS rules don't match registry
```

### Adding New Icons

1. Save SVG: `source/icons-source/my-icon.svg`
   - Must have viewBox="0 0 24 24"
   - No inline fill/stroke attributes

2. Run build:
   ```bash
   npm run build
   ```

3. Verify:
   ```bash
   grep '"my-icon"' source/patterns/documentation/icons-registry.json
   ```

4. Use immediately:
   ```twig
   {% include '@elements/icon/icon.twig' with { name: 'my-icon' } only %}
   ```

### Zero-Tolerance Rules

- ❌ Icon names with `icon-` prefix in code
- ❌ Hard-coded SVG paths (always use sprite)
- ❌ Missing ariaLabel for informative icons
- ❌ data-icon attribute for non-existent icons
- ❌ Manual edits to icons-generated.css

### Examples

✅ **CORRECT - ps-icon component**
```twig
{% include '@elements/icon/icon.twig' with {
  name: 'check',
  size: 'lg',
  color: 'success',
  ariaLabel: 'Form validation successful'
} only %}
```

✅ **CORRECT - data-icon attribute**
```html
<span class="ps-badge__icon" data-icon="check" aria-hidden="true"></span>
```

✅ **CORRECT - SVG direct with animations**
```html
<svg class="ps-loading" aria-hidden="true">
  <use href="/icons/icons-sprite.svg#icon-spinner"></use>
</svg>
```

❌ **WRONG - Prefix in code**
```twig
{% include '@elements/icon/icon.twig' with { name: 'icon-check' } %}
```

❌ **WRONG - No validation**
```html
<span data-icon="typo-not-exist"></span>
```

❌ **WRONG - Missing aria**
```twig
{# Icon is informative, needs label #}
{% include '@elements/icon/icon.twig' with { name: 'alert' } %}
```
```

---

#### Task 3.4: Update Migration Workflow

**File**: `docs/ps-design/ICON_MIGRATION_WORKFLOW.md`

Update status:
```markdown
### Current Status: Icon System Unified ✅

| Element | File | Status | Notes |
|---------|------|--------|-------|
| Icon | ✅ Complete | Component + Sprite | SSOT |
| Badge | 🔄 In Progress | Migrate data-icon → icon component | [WIP] |
| Button | 🔄 In Progress | Migrate data-icon → icon component | [WIP] |
| Divider | Pending | Migrate data-icon | Next |
| Field | Pending | Migrate data-icon (left/right) | Next |
| Link | Pending | Migrate data-icon | Next |
| Eyebrow | Pending | Migrate data-icon | Next |

All icons now accessible via:
- icons-generated.css (139 [data-icon] rules)
- ps-icon component (Twig)
- icons-registry.json (validation map)
```

---

### PHASE 4: Validation & Testing (2-3 hours)

#### Task 4.1: Build System Validation

```bash
npm run build

# Check output
ls -la source/props/icons-generated.css
ls -la source/patterns/documentation/icons-registry.json
ls -la source/assets/icons/icons-sprite.svg

# Verify counts
echo "Icons in registry:" && cat source/patterns/documentation/icons-registry.json | jq '.names | length'
echo "CSS rules:" && grep -c '\[data-icon=' source/props/icons-generated.css
echo "SVG symbols:" && grep -c '<symbol' source/assets/icons/icons-sprite.svg
# All should show: 139
```

#### Task 4.2: CSS Compilation

```bash
npm run build:styles

# No errors?
echo "Build success: $?"
```

#### Task 4.3: Storybook Verification

```bash
npm run watch
# Open http://localhost:6006
# → Elements → Icon
# Should show all 139 icons (auto-generated from registry)
```

#### Task 4.4: Integration Test

Create sample Twig file to test all 3 patterns:

**File**: `test-icons-all-patterns.twig`

```twig
{# Pattern 1: ps-icon component #}
{% include '@elements/icon/icon.twig' with { name: 'check', size: 'lg' } only %}

{# Pattern 2: data-icon attribute #}
<span data-icon="search"></span>

{# Pattern 3: SVG direct #}
<svg><use href="/icons/icons-sprite.svg#icon-arrow-right"></use></svg>
```

Verify in browser: All 3 render correctly

---

### PHASE 5: Component Migration (ongoing)

#### Task 5.1: Badge Component

**File**: `source/patterns/elements/badge/badge.twig`

Replace:
```twig
{%- if icon -%}
  <span class="ps-badge__icon" data-icon="{{ icon }}"></span>
{%- endif -%}
```

With:
```twig
{%- if icon -%}
  {% include '@elements/icon/icon.twig' with {
    name: icon,
    size: 'sm',
    ariaLabel: null,
    attributes: { class: 'ps-badge__icon' }
  } only %}
{%- endif -%}
```

(Similar for Button, Field, Divider, Link, Eyebrow)

---

## 📅 Timeline

| Phase | Duration | Week | Status |
|-------|----------|------|--------|
| **1: Build Enhancement** | 3-4h | 1 | Ready |
| **2: CSS Integration** | 2-3h | 1 | Ready |
| **3: Documentation** | 3-4h | 2 | Ready |
| **4: Validation** | 2-3h | 2 | Ready |
| **5: Component Migration** | 4-6h | 3+ | Ongoing |

**Total**: ~15 hours (2-3 weeks phased)

---

## 🎯 Success Criteria

- [ ] All 139 icons accessible via data-icon
- [ ] icons-generated.css auto-generated (no manual edits)
- [ ] icons-registry.json validates icon names
- [ ] 3 access patterns documented + examples
- [ ] copilot-instructions.md updated
- [ ] Storybook shows all 139 icons
- [ ] 0 manual CSS maintenance for icons
- [ ] Build warns if icon used but not available

---

## 🚨 Risk Assessment

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|-----------|
| CSS generation breaks styling | LOW | MEDIUM | Test all 3 patterns in browser |
| Icons-registry.json sync issues | LOW | LOW | Validate counts match SVG folder |
| Backward compat (data-icon) | NONE | NONE | data-icon still supported + CSS |
| Component migration issues | MEDIUM | LOW | Migrate one at a time, test Storybook |

---

## 📎 Deliverables

1. ✅ Enhanced `scripts/build-icons.mjs`
2. ✅ Generated `source/props/icons-generated.css`
3. ✅ Generated `source/patterns/documentation/icons-registry.json`
4. ✅ New `docs/ps-design/ICON_SYSTEM.md`
5. ✅ Updated `.github/copilot-instructions.md`
6. ✅ New `.github/instructions/icons.instructions.md`
7. ✅ Updated `docs/ps-design/ICON_MIGRATION_WORKFLOW.md`
8. ✅ All components migrated to consistent pattern

---

**Next Step**: Approve plan → Implement Phase 1-2 → Test → Deploy
