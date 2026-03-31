# Libraries-Override Quick Reference

> **See detailed analysis**: [audit-libraries-override.md](audit-libraries-override.md)

## TL;DR

**20 library overrides** in `ui_suite_bnppre.info.yml`:
- **10 Core overrides** (always active)
- **1 Contrib installed** (paragraphs)
- **9 Contrib optional** (defensive, apply when modules are installed)

**Risk level**: 🔴 2 high-risk / 🟡 2 medium-risk / 🟢 16 low-risk

---

## High-Risk Overrides 🔴

These **replace core JS files** — must be reviewed with every Drupal core security update:

### 1. `core/drupal.active-link` — Custom JS
- **Replaces**: `misc/active-link.js`
- **Why**: Bootstrap 5 uses `.active` class, not `.is-active`
- **Risk**: Misses core security patches
- **Action**: Review after `composer update drupal/core`
- **File**: [assets/js/misc/active-link.js](../assets/js/misc/active-link.js)

### 2. `text/drupal.text` — Custom JS
- **Replaces**: Entire `text/drupal.text` library
- **Why**: Bootstrap styling for "Edit summary" button
- **Risk**: Misses core security patches
- **Action**: Review after `composer update drupal/core`
- **File**: Custom library in [ui_suite_bnppre.libraries.yml](../ui_suite_bnppre.libraries.yml)

---

## Medium-Risk Overrides 🟡

### 3. `core/drupal.dropbutton: false` — Fully Disabled
- **Why**: Admin-only component, not needed in front-end
- **Risk**: Breaks admin UI if theme is used as admin theme
- **Symptom**: Dropbutton menus unstyled (e.g., content operations, views bulk operations)
- **Mitigation**: Never use this theme as admin theme

### 4. `layout_builder_browser/modal` — Custom JS
- **Replaces**: `js/layout_builder_browser.modal.js`
- **Why**: Bootstrap modal integration
- **Risk**: Breaks if module API changes
- **Status**: Module not yet installed (defensive override)
- **Action**: Test when installing `layout_builder_browser`

---

## Low-Risk Overrides 🟢

These disable or replace CSS only — no security impact:

### Core CSS Disabled (replaced by Bootstrap)
- `core/drupal.autocomplete` — spinner CSS → Bootstrap spinner
- `core/drupal.dialog.off_canvas` — 13 off-canvas CSS files → Bootstrap Offcanvas
- `layout_builder/drupal.layout_builder` — 2 CSS files → custom Bootstrap layout
- `node/drupal.node.preview` — preview banner CSS (no replacement)
- `system/base` — clearfix + container-inline → Bootstrap utilities

### Core Admin Features Disabled (front-end only)
- `core/drupal.tableheader: false` — sticky table headers (admin-only)
- `core/drupal.tablesort: false` — sortable table columns (admin-only)
- `content_moderation/content_moderation: false` — workflow UI (admin-only)

### Contrib Installed
- `paragraphs/drupal.paragraphs.unpublished: false` — unpublished paragraph styling

### Contrib Optional (Defensive)
**These apply automatically when modules are installed:**
- `clientside_validation_jquery/cv.jquery.ife: false`
- `commerce_cart/cart_block: false`
- `commerce_checkout/form: false`
- `commerce_checkout/login_pane: false`
- `layout_builder_browser/browser: false`
- `media_library_edit/admin` — admin CSS disabled
- `section_library/section_library` — CSS disabled

---

## Maintenance Checklist

### On Every Drupal Core Update
```bash
# 1. Update Drupal
composer update drupal/core --with-all-dependencies

# 2. Check changelogs for these files:
git diff 11.x..HEAD -- core/misc/active-link.js
git diff 11.x..HEAD -- core/modules/text/text.js

# 3. If changed, review and merge fixes into:
web/themes/custom/ui_suite_bnppre/assets/js/misc/active-link.js
web/themes/custom/ui_suite_bnppre/...drupal.text library

# 4. Clear cache and test
vendor/bin/drush cr
```

### On Installing Optional Modules
```bash
# If installing layout_builder_browser, commerce, etc.:
# 1. Check if defensive override applies
grep "layout_builder_browser" ui_suite_bnppre.info.yml

# 2. Test functionality with override active
# 3. Document any issues in docs/audit-libraries-override.md
```

### Quarterly Review
- Verify no new security advisories affect overridden files
- Check if defensive overrides are still needed
- Test admin dropbuttons if theme is accidentally used as admin theme

---

## What Would Break If Removed?

| Override | If Removed... | Severity |
|----------|---------------|----------|
| `core/drupal.active-link` | Menu active state uses wrong class (`.is-active` instead of `.active`) | 🟡 Styling only |
| `text/drupal.text` | "Edit summary" button unstyled, vanilla Drupal appearance | 🟢 Works, looks different |
| `core/drupal.dropbutton` | Nothing (front-end theme, no dropbuttons used) | 🟢 None |
| `core/drupal.dialog.off_canvas` | Off-canvas dialogs use Drupal styling, conflicts with Bootstrap | 🟡 Layout issues |
| `core/drupal.tableheader` | Nothing (admin feature, unused in front-end) | 🟢 None |
| `core/drupal.tablesort` | Nothing (admin feature, unused in front-end) | 🟢 None |
| `layout_builder/drupal.layout_builder` | Layout Builder UI uses Drupal styling, conflicts with Bootstrap | 🟡 Admin UI issues |
| `paragraphs/drupal.paragraphs.unpublished` | Unpublished paragraphs show striped gray background | 🟢 Styling only |
| Defensive commerce/csv/etc. | Nothing (modules not installed) | 🟢 None |

---

## Adding New Overrides

**Before adding a new override**, ask:

1. **Is this replacing a security-sensitive file?** (JS core files)
   - ❌ Avoid if possible → Creates maintenance burden
   - ✅ If necessary → Document in [audit-libraries-override.md](audit-libraries-override.md) with upgrade checklist

2. **Is this disabling core functionality?**
   - ✅ OK if admin-only feature in front-end theme
   - ⚠️ Risky if feature may be needed by contrib modules

3. **Is this defensive (module not installed)?**
   - ✅ OK if module installation is planned
   - ❌ Remove if module will never be used (reduces noise)

4. **Does Bootstrap provide equivalent functionality?**
   - ✅ Override justified
   - ❌ Consider keeping Drupal's version

**Template for new override documentation:**
```markdown
### `module/library_name` — Action
- **Replaces/Disables**: [description]
- **Why**: [business justification]
- **Risk**: [security/compatibility impact]
- **Symptom if removed**: [what breaks]
- **File**: [path to custom asset if applicable]
```

---

## Summary Table

| Override Type | Count | Risk | Review Frequency |
|---------------|-------|------|------------------|
| JS replacements | 2 | 🔴 High | Every core update |
| CSS disables | 8 | 🟢 Low | Quarterly |
| Full disables | 3 | 🟡 Medium | When feature is used |
| Contrib installed | 1 | 🟢 Low | On paragraph updates |
| Contrib defensive | 9 | 🟢 None | When installing module |

**Total**: 20 overrides (23 individual file overrides when counting off-canvas CSS individually)

---

## FAQs

**Q: Why so many overrides?**
A: This is a Bootstrap 5 theme. Drupal core ships with its own design system (Olivero-style). Most overrides replace Drupal's admin-oriented styles with Bootstrap equivalents.

**Q: Are these safe?**
A: CSS-only overrides are safe. JS replacements require manual review on core updates.

**Q: Can I use this theme as admin theme?**
A: ❌ No — `core/drupal.dropbutton` and table features are disabled. Use Gin or Claro for admin.

**Q: What if a module requires a disabled library?**
A: The module may display unstyled or broken. Test thoroughly. Consider removing the override or extending with `libraries-extend` instead.

**Q: How do I know if a defensive override applies?**
A: Install the module and test. Drupal silently ignores overrides for non-existent libraries.
