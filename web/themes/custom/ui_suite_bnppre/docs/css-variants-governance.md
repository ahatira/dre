# CSS Variants — Governance & Strategy

> **Context**: `ui_suite_bnppre` provides two CSS stylesheet variants selectable via theme settings.
> **Decision point**: Should these variants stay in the main theme or move to sub-themes?
> **Recommendation**: Detailed below.

---

## Current Implementation

### Two CSS Variants

| Variant | Library Name | File | Use Case |
|---------|-------------|------|----------|
| **Realestate** (default) | `ui_suite_bnppre/framework_css_bnppre` | `assets/css/styles.css` | BNP Paribas Real Estate corporate sites |
| **Property Search** | `ui_suite_bnppre/framework_css_bnppre_ps` | `assets/css/styles-ps.css` | Property search portals |

### How It Works

**User Selection**:
```
Admin → Appearance → Settings → UI Suite BNP PRE
└── Stylesheet dropdown:
    • Realestate (default) ✓
    • Property Search
    • (empty = custom loading)
```

**Configuration Storage**:
```yaml
# config/install/ui_suite_bnppre.settings.yml
library:
  css_loading: 'ui_suite_bnppre/framework_css_bnppre'
```

**Library Definitions**:
```yaml
# ui_suite_bnppre.libraries.yml
framework_css_bnppre:
  css:
    theme:
      assets/css/styles.css: {}

framework_css_bnppre_ps:
  css:
    theme:
      assets/css/styles-ps.css: {}
```

**SCSS Sources**:
```scss
// assets/scss/styles.scss (Realestate)
@import 'configuration-import';
@import 'bootstrap';
@import 'components/brand-foundations';
@import 'components/buttons';
...

// assets/scss/styles-ps.scss (Property Search)
@import 'configuration-import';  // Same base
@import 'bootstrap';             // Same framework
@import 'components/...';        // Same components

// -- Property Search specific overrides below --
// (currently empty, ready for PS-specific styles)
```

### Implementation Details

**Theme settings form** (`src/Hook/ThemeSettings.php`):
```php
$form['ui_suite_bnppre']['library']['css_loading'] = [
  '#type' => 'select',
  '#title' => $this->t('Stylesheet'),
  '#options' => [
    'ui_suite_bnppre/framework_css_bnppre' => $this->t('Realestate'),
    'ui_suite_bnppre/framework_css_bnppre_ps' => $this->t('Property Search'),
  ],
  '#empty_value' => '',  // Allows "none" for custom sub-theme loading
  '#config_target' => $config_key . ':library.css_loading',
];
```

**Dynamic loading** (`src/Hook/LibraryInfo.php`):
- Reads `library.css_loading` from config
- Attaches selected library to page
- Empty value = no automatic loading (for sub-themes)

---

## Current State Analysis

### What's Actually Different?

**As of March 2026**:
```diff
# styles.scss (Realestate) — 12 lines
@import 'configuration-import';
@import 'bootstrap';
@import 'components/brand-foundations';
@import 'components/buttons';
@import 'components/breadcrumb';
@import 'components/accordion';
@import 'components/typography';

# styles-ps.scss (Property Search) — 22 lines
@import 'configuration-import';
@import 'bootstrap';
@import 'components/brand-foundations';
@import 'components/buttons';
@import 'components/breadcrumb';
@import 'components/accordion';
@import 'components/typography';

+ // -- Property Search specific overrides ---------------------------------------
+ // Add PS-only styles below.
```

**Observation**: Currently, **Property Search has NO unique styles**. It's a placeholder ready for customization.

### Design Intent

**Original intention** (inferred from code structure):
1. **Realestate** = base theme for corporate real estate sites
2. **Property Search** = variant with search-specific UI tweaks (filters, maps, listings)

**Benefits of current approach**:
✅ Single theme maintains both variants
✅ Shared Bootstrap + component SCSS
✅ Easy A/B comparison during development
✅ No code duplication for shared components

**Drawbacks**:
❌ Main theme complexity increases with variants
❌ Unclear which styles are shared vs variant-specific
❌ Testing burden (2× CSS files to validate)
❌ Deployment confusion (which variant for which site?)
❌ Version mismatch risks (variant falls behind base)

---

## Governance Options

### Option A: Keep Variants in Main Theme ✅ (Current)

**When to choose**:
- Property Search is a **minor variant** of Realestate
- Variants share 90%+ of styles
- Small team maintains both products
- Variants evolve in lockstep

**Pros**:
- ✅ No code duplication
- ✅ Shared component updates benefit both
- ✅ Simple deployment (one theme package)
- ✅ Easy to compare variants side-by-side

**Cons**:
- ❌ Main theme carries unused code for each project
- ❌ Variants can't diverge significantly without conflict
- ❌ Testing requires validating both stylesheets

**Recommended maintenance**:
```scss
// assets/scss/styles-ps.scss
@import 'configuration-import';
@import 'bootstrap';
@import 'components/**/*';  // All shared components

// -- Property Search specific --------------------------------
@import 'ps-overrides/filters';      // Search filters styling
@import 'ps-overrides/map-view';     // Map integration
@import 'ps-overrides/listings';     // Property listing cards

// -- Minor tweaks -------------------------------------------
.ps-only-feature { ... }
```

**Folder structure**:
```
assets/scss/
├── styles.scss              (Realestate base)
├── styles-ps.scss           (Property Search variant)
├── components/              (Shared by both)
│   ├── buttons.scss
│   ├── breadcrumb.scss
│   └── ...
└── ps-overrides/            (PS-specific styles)
    ├── _filters.scss
    ├── _map-view.scss
    └── _listings.scss
```

---

### Option B: Move Property Search to Sub-Theme 🔄 (Recommended for Growth)

**When to choose**:
- Property Search needs **significant divergence** from Realestate
- Different teams maintain each product
- Independent release cycles required
- Variants share <70% of styles

**Implementation**:
```
web/themes/custom/
├── ui_suite_bnppre/          (Base theme — Realestate only)
│   ├── assets/scss/
│   │   └── styles.scss       (No more styles-ps.scss)
│   └── ui_suite_bnppre.info.yml
│       base theme: false
│
└── ui_suite_bnppre_ps/       (Sub-theme — Property Search)
    ├── assets/scss/
    │   └── styles.scss       (Inherits + overrides)
    ├── ui_suite_bnppre_ps.info.yml
    │   base theme: ui_suite_bnppre
    └── ui_suite_bnppre_ps.libraries.yml
        framework_css:
          css:
            theme:
              assets/css/styles.css: {}
```

**Sub-theme SCSS**:
```scss
// ui_suite_bnppre_ps/assets/scss/styles.scss
// Inherits all base theme components via Drupal theme inheritance

// Property Search specific overrides
@import 'variables-ps';          // PS-specific design tokens
@import 'filters';               // Search filter components
@import 'map-integration';       // Map view styling
@import 'property-listings';     // Property card layouts

// Override base theme components if needed
.btn-primary {
  // PS-specific button styling
}
```

**Pros**:
- ✅ Clear separation of concerns
- ✅ Independent versioning (base 2.0, PS 1.5)
- ✅ Each theme is simpler
- ✅ No unused code in deployments
- ✅ Different teams can maintain separately

**Cons**:
- ❌ Code duplication for shared tweaks
- ❌ Base theme updates require sub-theme testing
- ❌ Two repositories to maintain
- ❌ Shared component fixes need dual commits

---

### Option C: Starterkit-Based Approach 🚀 (Future-Proof)

**When to choose**:
- Multiple real estate products planned (3+)
- Each needs unique branding
- Reusable component library desired
- Agency/SaaS multi-tenant model

**Implementation**:
```
web/themes/custom/
├── ui_suite_bnppre/               (Framework theme — no CSS)
│   ├── components/                (UI components only)
│   ├── templates/                 (Twig templates)
│   └── ui_suite_bnppre.info.yml
│       base theme: false
│       generator: ui_suite_bnppre:1.0.0  ← Starterkit
│
├── bnppre_realestate/             (Generated from starterkit)
│   ├── assets/scss/styles.scss   (Realestate branding)
│   └── bnppre_realestate.info.yml
│       base theme: ui_suite_bnppre
│
├── bnppre_property_search/        (Generated from starterkit)
│   ├── assets/scss/styles.scss   (Property Search branding)
│   └── bnppre_property_search.info.yml
│       base theme: ui_suite_bnppre
│
└── bnppre_investments/            (Future product)
    └── ...
```

**Starterkit scaffold** (`starterkits/ui_suite_bnppre_starterkit/`):
```
starterkits/ui_suite_bnppre_starterkit/
├── {project_machine_name}.info.yml
├── assets/scss/
│   ├── styles.scss              (Minimal base)
│   └── _variables.scss          (Customization points)
├── package.json
└── README.md
```

**Generation command**:
```bash
php core/scripts/drupal generate-theme bnppre_realestate \
  --name "BNPPRE Realestate" \
  --starterkit ui_suite_bnppre
```

**Pros**:
- ✅ Scalable to many products
- ✅ Each theme is fully independent
- ✅ Clear upgrade path (regenerate from new starterkit)
- ✅ Base theme decoupled from branding

**Cons**:
- ❌ Most complex architecture
- ❌ Starterkit maintenance overhead
- ❌ Generator script complexity
- ❌ Overkill for 2 variants

---

## Recommendation Matrix

| Scenario | Recommended Option | Reason |
|----------|-------------------|--------|
| **2 variants, 90% shared** | A: Keep in main theme | Current state is fine |
| **2 variants, growing divergence** | B: Sub-theme for PS | Clear separation |
| **3+ products planned** | C: Starterkit approach | Future-proof scaling |
| **Different teams per product** | B: Sub-theme for PS | Independent maintenance |
| **Infrequent PS updates** | A: Keep in main theme | Don't over-architect |
| **PS needs frequent iteration** | B: Sub-theme for PS | Deploy independently |

---

## Current Recommendation (March 2026)

### ⚠️ **Keep Option A (Current State)** — With Conditions

**Rationale**:
1. Property Search currently has **ZERO unique styles** — moving to sub-theme now would be premature optimization
2. Both variants share 100% of components today
3. No evidence of divergent requirements yet
4. Team size/structure unknown (sub-theme may add unnecessary complexity)

**Conditions to maintain Option A**:
- ✅ Document PS-specific overrides in dedicated SCSS folder (`ps-overrides/`)
- ✅ Limit PS uniqueness to <20% of total styles
- ✅ Test both stylesheets in CI
- ✅ Monitor style divergence quarterly

**Triggers to migrate to Option B**:
- 🔴 Property Search needs >20% unique styles
- 🔴 Independent release cycles required (base 2.0, PS still on 1.x)
- 🔴 Different teams maintaining each product
- 🔴 Conflicting design system requirements

---

## Implementation Guide (If Staying with Option A)

### Best Practices

**1. Organize PS-specific styles**:
```scss
// assets/scss/styles-ps.scss
@import 'configuration-import';
@import 'bootstrap';
@import 'components/**/*';    // All shared components

// == Property Search Specific ====================================
// Group PS-only styles here for easy extraction if sub-theme needed

@import 'ps-overrides/variables';  // PS design token overrides
@import 'ps-overrides/filters';    // Search filters component
@import 'ps-overrides/map-view';   // Map integration
@import 'ps-overrides/listings';   // Property listing cards

// Minor tweaks
.ps-header-variant { ... }
```

**2. Document which components are PS-specific**:
```scss
// assets/scss/ps-overrides/_filters.scss
// ============================================================================
// PROPERTY SEARCH ONLY — Search Filters Component
// ============================================================================
// This component is NOT used in Realestate variant.
// If extracting PS to sub-theme, move this file entirely.

.ps-filter-panel { ... }
```

**3. Test both variants in development**:
```bash
# Build both stylesheets
npm run build:css

# Manually QA:
# 1. Set stylesheet to "Realestate" → test site
# 2. Set stylesheet to "Property Search" → test site
# 3. Verify no visual regressions
```

**4. CI validation**:
```yaml
# .github/workflows/test.yml
- name: Build CSS variants
  run: |
    npm run build:css

    # Verify both files exist
    test -f assets/css/styles.css
    test -f assets/css/styles-ps.css

    # Verify reasonable file sizes (not empty)
    test $(wc -c < assets/css/styles.css) -gt 10000
    test $(wc -c < assets/css/styles-ps.css) -gt 10000
```

**5. Quarterly review**:
```bash
# Report PS-specific style count
echo "Total SCSS lines: $(wc -l assets/scss/**/*.scss)"
echo "PS-only lines: $(wc -l assets/scss/ps-overrides/*.scss)"

# Calculate divergence percentage
# If PS-only > 20% of total → consider sub-theme migration
```

---

## Implementation Guide (If Migrating to Option B)

### Migration Steps

**Phase 1: Create Sub-Theme Structure** (1-2 hours)
```bash
# Create sub-theme folder
mkdir -p web/themes/custom/ui_suite_bnppre_ps

# Create info.yml
cat > ui_suite_bnppre_ps/ui_suite_bnppre_ps.info.yml << 'EOF'
name: 'UI Suite BNP PRE — Property Search'
type: theme
description: 'Property Search variant (sub-theme of ui_suite_bnppre)'
core_version_requirement: ^11
base theme: ui_suite_bnppre

libraries:
  - ui_suite_bnppre_ps/framework_css

regions:
  # Inherit from base theme
EOF

# Create library definition
cat > ui_suite_bnppre_ps/ui_suite_bnppre_ps.libraries.yml << 'EOF'
framework_css:
  css:
    theme:
      assets/css/styles.css: {}
EOF

# Copy build tools
cp ui_suite_bnppre/package.json ui_suite_bnppre_ps/
cp ui_suite_bnppre/gulpfile.js ui_suite_bnppre_ps/
```

**Phase 2: Extract PS-Specific Styles** (2-4 hours)
```bash
# Move PS SCSS to sub-theme
mkdir -p ui_suite_bnppre_ps/assets/scss
mv ui_suite_bnppre/assets/scss/styles-ps.scss \
   ui_suite_bnppre_ps/assets/scss/styles.scss

# Move PS-only overrides
mv ui_suite_bnppre/assets/scss/ps-overrides \
   ui_suite_bnppre_ps/assets/scss/overrides

# Update imports in sub-theme styles.scss
# (relative paths to base theme may need adjustment)
```

**Phase 3: Remove PS from Base Theme** (30 min)
```bash
# Delete PS stylesheet from base theme
rm ui_suite_bnppre/assets/scss/styles-ps.scss
rm ui_suite_bnppre/assets/css/styles-ps.css

# Remove PS library from base theme
# Edit ui_suite_bnppre/ui_suite_bnppre.libraries.yml:
# - Delete framework_css_bnppre_ps

# Remove PS option from theme settings
# Edit ui_suite_bnppre/src/Hook/ThemeSettings.php:
# - Remove 'ui_suite_bnppre/framework_css_bnppre_ps' option
```

**Phase 4: Update Configuration** (30 min)
```bash
# Update default config to remove PS
# Edit ui_suite_bnppre/config/install/ui_suite_bnppre.settings.yml
# (Already correct if defaults to Realestate)

# Create config for PS sub-theme
cat > ui_suite_bnppre_ps/config/install/ui_suite_bnppre_ps.settings.yml << 'EOF'
library:
  css_loading: 'ui_suite_bnppre_ps/framework_css'
EOF
```

**Phase 5: Testing** (2-3 hours)
```bash
# Build sub-theme CSS
cd ui_suite_bnppre_ps
npm install
npm run build:css

# Enable sub-theme in Drupal
drush theme:enable ui_suite_bnppre_ps
drush config:set system.theme default ui_suite_bnppre_ps

# Clear cache
drush cr

# Test Property Search site with sub-theme
# Test Realestate site with base theme
```

**Phase 6: Documentation** (1 hour)
- Update README.md for both themes
- Document sub-theme relationship
- Update starterkit if applicable

**Total effort**: ~8-12 hours

---

## Monitoring & Metrics

### Key Metrics to Track

**Style divergence**:
```bash
# SCSS line count
base_lines=$(find ui_suite_bnppre/assets/scss -name "*.scss" -exec wc -l {} + | tail -1 | awk '{print $1}')
ps_lines=$(find ui_suite_bnppre/assets/scss/ps-overrides -name "*.scss" -exec wc -l {} + | tail -1 | awk '{print $1}')

echo "Divergence: $(( ps_lines * 100 / base_lines ))%"
# Target: <20%
```

**CSS file size**:
```bash
ls -lh assets/css/styles.css assets/css/styles-ps.css
# Watch for: PS > base (indicates significant overrides)
```

**Component overlap**:
```bash
# Count shared vs unique components
shared=$(comm -12 <(grep "@import 'components/" styles.scss | sort) \
                   <(grep "@import 'components/" styles-ps.scss | sort) | wc -l)
# Target: >80% shared
```

---

## Decision Log

| Date | Decision | Rationale |
|------|----------|-----------|
| March 2026 | **Keep Option A (variants in main theme)** | PS has 0% unique styles, premature to split |
| TBD | Review divergence | If PS grows >20% unique styles, revisit Option B |

---

## References

- **Theme settings implementation**: [src/Hook/ThemeSettings.php](../src/Hook/ThemeSettings.php)
- **Library definitions**: [ui_suite_bnppre.libraries.yml](../ui_suite_bnppre.libraries.yml)
- **Realestate SCSS**: [assets/scss/styles.scss](../assets/scss/styles.scss)
- **Property Search SCSS**: [assets/scss/styles-ps.scss](../assets/scss/styles-ps.scss)
- **Build documentation**: [docs/build-assets-strategy.md](build-assets-strategy.md)
- **Drupal sub-themes**: https://www.drupal.org/docs/theming-drupal/creating-sub-themes

---

## Summary

| Aspect | Current State | Recommendation |
|--------|---------------|----------------|
| **Variants** | 2 (Realestate, Property Search) | Keep both |
| **Divergence** | 0% (PS has no unique styles) | Monitor quarterly |
| **Architecture** | Option A (in main theme) | Maintain unless divergence >20% |
| **Trigger to change** | PS needs significant customization | Migrate to Option B (sub-theme) |
| **Testing** | Manual QA both variants | Add CI validation |

**Bottom line**: Current approach is appropriate for now. Revisit when Property Search develops unique requirements.
