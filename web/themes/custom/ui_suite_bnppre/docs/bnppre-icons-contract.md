# BNPPRE Icon Pack — Contract & Usage

> **Authority**: This document defines the authoritative contract for the BNPPRE custom icon pack.
> **Last updated**: March 2026
> **Icon count**: 132 icons across 12 categories
> **Format**: Optimized SVG (24×24 viewBox)

---

## Quick Reference

### Before Committing Icons
```bash
# 1. Place raw SVG files in appropriate category folder
#    assets/icons/{category}/{icon-name}.svg

# 2. Optimize with SVGO
npm run build:bnppre-icons:svgo

# 3. Normalize naming + remove fills
npm run build:bnppre-icons

# 4. Validate compliance
npm run build:bnppre-icons:check

# 5. If check passes, commit
git add assets/icons/
git commit -m "Add/update icons: {description}"
```

---

## Icon Categories (12)

Icons are organized by these categories:

```
assets/icons/
├── ad/               (Advertising/marketing icons)
├── blog/             (Blog/content icons)
├── generic/          (Generic UI icons)
├── metropole/        (Metropolitan/urban icons)
├── mobile-only/      (Mobile-specific icons)
├── other/            (Miscellaneous)
├── search/           (Search-related icons)
├── social-media/     (Social platform icons)
├── tools/            (Tool/utility icons)
├── tutoffice/        (Tutorial/office icons)
├── univers/          (Universe/world icons)
└── website/          (Website/web icons)
```

**Rule**: New categories require approval and documentation update.

---

## Naming Convention (STRICT)

### Rules

✅ **Valid naming**:
- Lowercase only: `user-profile.svg` ✅
- ASCII only (no accents): `recherche.svg` → `search.svg` or `rechercher.svg` ✅
- Hyphens separate words: `arrow-left.svg` ✅
- Max 30 characters (without `.svg`): `very-long-descriptive-name.svg` (28 chars) ✅
- Descriptive, searchable: `home.svg`, `settings.svg`, `add-user.svg` ✅

❌ **Invalid naming**:
- Uppercase: `UserProfile.svg` ❌
- Spaces: `user profile.svg` ❌
- Underscores: `user_profile.svg` ❌ (use hyphens)
- Accents: `télécharger.svg` ❌
- Special chars: `user@profile.svg`, `user&profile.svg` ❌
- Too long: `this-is-an-extremely-long-descriptive-icon-name.svg` (47 chars) ❌
- Non-descriptive: `icon1.svg`, `test.svg` ❌

### Name Uniqueness

**CRITICAL**: Icon names must be unique **across all categories**.

❌ **Forbidden**:
```
assets/icons/generic/home.svg
assets/icons/website/home.svg   ← CONFLICT
```

✅ **Correct**:
```
assets/icons/generic/home.svg
assets/icons/website/home-page.svg   ← UNIQUE
```

**Reason**: Drupal UI Icons module references icons by name only, not by category path.

---

## SVG Requirements

### Structure

**Required attributes**:
```xml
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
  <path d="..."/>
</svg>
```

✅ **Mandatory**:
- `xmlns="http://www.w3.org/2000/svg"`
- `viewBox="0 0 24 24"` (24×24 pixel canvas)
- No `width`/`height` attributes (responsive sizing via CSS)

❌ **Forbidden attributes** (removed by build):
- `fill="#000000"` or any color value
- `fill="currentColor"` (injected dynamically by CSS)
- Inline `style="fill:..."` declarations

✅ **Allowed attributes** (preserved):
- `fill-rule="evenodd"` (geometric rendering rule)
- `clip-rule="evenodd"` (clipping path rule)
- `d="..."` (path data)
- `stroke`, `stroke-width` (if needed)

### Color Rules

**CRITICAL**: Icons must be color-agnostic for theming.

❌ **Forbidden colors** (detected by `--check`):
```xml
<path fill="#000000" .../>         ❌ Hardcoded black
<path fill="#333" .../>            ❌ Hardcoded dark gray
<circle style="fill:#1a1a1a" .../> ❌ Inline style
<rect stroke="#000" .../>          ❌ Hardcoded stroke (use currentColor)
```

✅ **Correct approach**:
```xml
<!-- Raw SVG (before build) -->
<path fill="#333" d="..."/>

<!-- After build processing -->
<path d="..."/>

<!-- At runtime (CSS injects color) -->
<svg class="icon" style="color: var(--icon-color)">
  <path d="..."/>
</svg>
```

**Dark colors detected**:
- `#000`, `#000000` (black)
- `#333`, `#333333` (dark gray)
- `#1a1a1a` (near-black)
- `#050505` (almost black)
- `black` (keyword)

**Why**: Icons are styled via CSS `color` property, which becomes `currentColor` in SVG context.

---

## Build Pipeline

### Step 1: SVGO Optimization

**Command**: `npm run build:bnppre-icons:svgo`

**What it does**:
- Removes XML declarations, comments, metadata
- Optimizes path data (reduces file size)
- Removes empty `<defs>`, unused IDs
- Cleans up whitespace

**Config**: `svgo.config.js`

**DOES NOT**:
- Remove `fill` attributes (handled by next step)
- Rename files
- Validate naming

### Step 2: BNPPRE Normalization

**Command**: `npm run build:bnppre-icons`

**What it does**:
1. **Renames files** to comply with naming convention
   - Converts to lowercase
   - Removes accents (é → e, ç → c)
   - Replaces spaces/underscores with hyphens
   - Removes French stop words (le, la, les, de, etc.)
   - Truncates to 30 characters
   - Ensures uniqueness with suffix if needed

2. **Replaces dark colors** with `currentColor`
   - Detects `fill="#000"`, `fill="#333"`, etc.
   - Replaces with `fill="currentColor"`
   - Same for `stroke` attributes

3. **Removes fill attributes**
   - Strips `fill="..."` from all elements
   - Strips `fill:` from inline styles
   - Reason: color controlled by CSS, not hardcoded

**Script**: `scripts/bnppre-icon.js`

**Example transformation**:
```xml
<!-- BEFORE -->
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
  <path fill="#333333" d="M12 2L2 7v10l10 5 10-5V7z"/>
</svg>

<!-- AFTER -->
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
  <path d="M12 2L2 7v10l10 5 10-5V7z"/>
</svg>
```

### Step 3: Validation

**Command**: `npm run build:bnppre-icons:check`

**What it validates**:
- ✅ Naming convention (lowercase, ASCII, max 30 chars)
- ✅ No `fill` attributes present
- ✅ No `fill:` style declarations
- ✅ No hardcoded dark colors (#000, #333, etc.)
- ✅ Unique names across all icons

**Exit codes**:
- `0` → All icons valid
- `1` → Violations found (details printed to console)

**Example violations**:
```
Check failed. Icon files are not normalized:
- generic/User-Profile.svg: expected filename user-profile.svg
- tools/hammer.svg: has fill attribute
- social-media/facebook.svg: has hardcoded dark fill/stroke color
Total violations: 3
```

---

## Drupal Integration

### Icon Pack Registration

**File**: `config/optional/ui_suite_bnppre.icons.yml`

```yaml
label: 'BNPPRE Custom Icons'
description: '132 icons, 12 categories'
template: '@ui_suite_bnppre/bnppre-icon'
```

### Using Icons in Twig

```twig
{# Via UI Icons module #}
{{ ui_icon('bnppre', 'home') }}

{# With custom classes #}
{{ ui_icon('bnppre', 'search', {
  class: ['text-primary', 'fs-3']
}) }}

{# With inline style #}
{{ ui_icon('bnppre', 'user', {
  style: 'color: var(--bs-danger);'
}) }}
```

### Icon Discovery

Icons are discovered automatically from:
```
assets/icons/{category}/*.svg
```

**Drupal cache**: After adding/modifying icons, run:
```bash
vendor/bin/drush cr
```

---

## Adding New Icons

### Workflow

1. **Source SVG Requirements**
   - Obtain SVG from designer or icon library
   - Ensure 24×24 viewBox (or square aspect ratio)
   - Prefer single-path icons (easier optimization)

2. **Place in Category**
   ```bash
   # Example: adding a new "download" icon
   cp ~/Downloads/download-icon.svg assets/icons/generic/download.svg
   ```

3. **Run Full Build**
   ```bash
   npm run build:bnppre-icons:svgo
   npm run build:bnppre-icons
   ```

4. **Validate**
   ```bash
   npm run build:bnppre-icons:check
   # Expected: "Check passed. 133 SVG files are normalized."
   ```

5. **Review Changes**
   ```bash
   git diff assets/icons/
   # Verify:
   # - Icon was renamed correctly
   # - fill attributes removed
   # - File is clean
   ```

6. **Update Metadata** (if adding many icons)
   ```bash
   # Count icons
   find assets/icons -name "*.svg" | wc -l

   # Update config/optional/ui_suite_bnppre.icons.yml
   # description: 'X icons, 12 categories'
   ```

7. **Clear Drupal Cache**
   ```bash
   vendor/bin/drush cr
   ```

8. **Test in UI**
   - Admin → Appearance → Settings → UI Suite BNPPRE
   - Verify icon appears in icon picker
   - Test rendering with `{{ ui_icon('bnppre', 'new-icon-name') }}`

9. **Commit**
   ```bash
   git add assets/icons/ config/optional/
   git commit -m "Add download icon to generic category"
   ```

---

## Modifying Existing Icons

### Workflow

1. **Edit Raw SVG**
   ```bash
   # Edit with Inkscape, Figma export, or text editor
   vim assets/icons/generic/home.svg
   ```

2. **Rebuild**
   ```bash
   npm run build:bnppre-icons:svgo
   npm run build:bnppre-icons
   ```

3. **Validate**
   ```bash
   npm run build:bnppre-icons:check
   ```

4. **Clear Cache & Test**
   ```bash
   vendor/bin/drush cr
   # Test in UI to verify changes
   ```

5. **Commit**
   ```bash
   git add assets/icons/generic/home.svg
   git commit -m "Update home icon: improve contrast"
   ```

---

## CI/CD Integration

### Pre-commit Hook (Recommended)

`.git/hooks/pre-commit`:
```bash
#!/bin/bash
# Validate BNPPRE icons before commit

if git diff --cached --name-only | grep -q "^web/themes/custom/ui_suite_bnppre/assets/icons/"; then
  echo "🔍 Validating BNPPRE icons..."
  cd web/themes/custom/ui_suite_bnppre
  npm run build:bnppre-icons:check

  if [ $? -ne 0 ]; then
    echo "❌ Icon validation failed. Run: npm run build:bnppre-icons"
    exit 1
  fi

  echo "✅ Icons validated successfully"
fi
```

### CI Pipeline

**.github/workflows/validate.yml** (example):
```yaml
jobs:
  validate-icons:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Setup Node
        uses: actions/setup-node@v3
        with:
          node-version: '18'

      - name: Install dependencies
        run: |
          cd web/themes/custom/ui_suite_bnppre
          npm ci

      - name: Validate BNPPRE icons
        run: |
          cd web/themes/custom/ui_suite_bnppre
          npm run build:bnppre-icons:check
```

---

## Troubleshooting

### Icon Not Appearing in Drupal

**Symptoms**: New icon not visible in UI Icons picker.

**Solutions**:
1. Clear Drupal cache: `drush cr`
2. Verify file is in `assets/icons/{category}/{name}.svg`
3. Check naming: `npm run build:bnppre-icons:check`
4. Verify icon pack enabled: Admin → Config → UI Icons

### Icon Colors Not Working

**Symptom**: Icon stays black regardless of CSS `color`.

**Cause**: `fill` attribute not removed.

**Solution**:
```bash
npm run build:bnppre-icons
npm run build:bnppre-icons:check
```

### Validation Fails After Adding Icon

**Symptom**: `npm run build:bnppre-icons:check` exits with error.

**Common causes**:
- Uppercase in filename → Rename to lowercase
- Fill attribute present → Run `npm run build:bnppre-icons`
- Name too long (>30 chars) → Shorten name
- Duplicate name → Rename to unique value

**Fix**:
```bash
# Let script auto-fix naming issues
npm run build:bnppre-icons

# Re-validate
npm run build:bnppre-icons:check
```

### Name Collision After Build

**Symptom**: Script renames icon with `-2` suffix.

**Example**:
```
assets/icons/generic/user.svg
assets/icons/tools/user.svg    → renamed to user-2.svg
```

**Solution**: Manually rename to descriptive unique names:
```bash
mv assets/icons/tools/user-2.svg assets/icons/tools/user-tools.svg
```

---

## Maintenance

### Quarterly Tasks

1. **Audit icon usage** (identify unused icons)
   ```bash
   # Find icons not referenced in Twig templates
   for icon in assets/icons/*/*.svg; do
     name=$(basename "$icon" .svg)
     if ! grep -r "ui_icon.*$name" templates/; then
       echo "Unused: $icon"
     fi
   done
   ```

2. **Update metadata** if icon count changed
   ```bash
   # Count icons
   find assets/icons -name "*.svg" | wc -l

   # Update config/optional/ui_suite_bnppre.icons.yml
   ```

3. **Re-validate all icons** (in case rules evolved)
   ```bash
   npm run build:bnppre-icons:check
   ```

### When Upgrading UI Icons Module

1. Test icon rendering after upgrade
2. Check for new icon pack schema requirements
3. Update `ui_suite_bnppre.icons.yml` if needed
4. Clear cache: `drush cr`

---

## Reference Files

- **Icon pack config**: [config/optional/ui_suite_bnppre.icons.yml](../config/optional/ui_suite_bnppre.icons.yml)
- **Build script**: [scripts/bnppre-icon.js](../scripts/bnppre-icon.js)
- **SVGO config**: [svgo.config.js](../svgo.config.js)
- **Build documentation**: [docs/build-assets-strategy.md](build-assets-strategy.md)
- **Icon source**: [assets/icons/](../assets/icons/)

---

## Summary

| Aspect | Requirement |
|--------|-------------|
| **Naming** | Lowercase, ASCII, hyphens, ≤30 chars, unique |
| **Format** | SVG with `viewBox="0 0 24 24"` |
| **Colors** | No hardcoded fills/strokes (color via CSS) |
| **Categories** | 12 predefined (new require approval) |
| **Validation** | `npm run build:bnppre-icons:check` before commit |
| **Build** | SVGO → normalize → validate |
| **Drupal integration** | UI Icons module, auto-discovery |
| **CI/CD** | Pre-commit hook + CI validation recommended |

**Golden rule**: If `--check` passes, it's safe to commit.
