# BNPPRE Icon Pack

> **132 icons** · **12 categories** · **24×24 viewBox SVG** · Managed via UI Icons module

---

## Quick Reference

```bash
# Full workflow when adding/modifying icons
cd web/themes/custom/ui_suite_bnppre

# 1. Place raw SVG in the right category folder
#    assets/icons/{category}/{icon-name}.svg

# 2. Optimize (remove XML declarations, metadata, empty defs)
npm run build:bnppre-icons:svgo

# 3. Normalize filenames + remove hardcoded fill colors
npm run build:bnppre-icons

# 4. Validate compliance (naming + SVG rules)
npm run build:bnppre-icons:check

# 5. Clear Drupal cache
vendor/bin/drush cr

# 6. Verify in UI: /admin/appearance/ui/icons
```

---

## Icon Categories (12)

```
assets/icons/
├── ad/            Advertising / property listing icons
├── blog/          Blog and content-type icons
├── generic/       Generic UI icons (arrows, close, check, search…)
├── metropole/     Urban / metropolitan icons
├── mobile-only/   Mobile-specific icons
├── other/         Miscellaneous icons
├── search/        Search interface icons
├── social-media/  Social platform icons (Facebook, LinkedIn, Twitter…)
├── tools/         Tool and workspace icons
├── tutoffice/     Tutorial / instructional icons
├── univers/       Real-estate universe icons (offices, retail…)
└── website/       Generic web / site icons
```

**New category**: requires approval. Add the entry to `ui_suite_bnppre.icons.yml` and update this document.

---

## Naming Convention

### Rules

| Rule | Valid ✅ | Invalid ❌ |
|------|---------|----------|
| Lowercase only | `user-profile.svg` | `UserProfile.svg` |
| Hyphens as word separator | `arrow-left.svg` | `arrow_left.svg`, `arrow left.svg` |
| ASCII only (no accents) | `search.svg` | `télécharger.svg` |
| Max 30 characters (without `.svg`) | `meeting-rooms-number.svg` (21) | `this-is-extremely-long-icon-name.svg` (35) |
| Descriptive, searchable | `home.svg`, `settings.svg` | `icon1.svg`, `test.svg` |

### Uniqueness Across All Categories

Icon names must be **globally unique** — not just within their category. The UI Icons module references icons by name, not by path.

```
❌ Conflict:
  assets/icons/generic/home.svg
  assets/icons/website/home.svg

✅ Correct:
  assets/icons/generic/home.svg
  assets/icons/website/home-page.svg
```

---

## SVG Requirements

### Required Structure

```xml
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
  <path d="..."/>
</svg>
```

| Attribute | Required | Value |
|-----------|----------|-------|
| `xmlns` | ✅ | `http://www.w3.org/2000/svg` |
| `viewBox` | ✅ | `0 0 24 24` |
| `width` / `height` | ❌ Remove | Sizing is done via CSS |
| `fill` with a color | ❌ Remove | Build pipeline strips these |

### Color Rules — Color-Agnostic Icons

Icons are styled at runtime via CSS `color` property (maps to `currentColor` in SVG context). Hardcoded colors break theming.

**Forbidden fills detected by `--check`**:

```xml
<path fill="#000000" .../>      ❌
<path fill="#333" .../>         ❌
<rect style="fill:#1a1a1a" .../> ❌
<circle stroke="#000" .../>     ❌
```

**After the build pipeline**, all dark fill colors are replaced with `currentColor` and then stripped. The final SVG has no fill attributes.

**Allowed attributes** (preserved through the pipeline):
- `fill-rule="evenodd"`, `clip-rule="evenodd"`
- Path data `d="..."`
- `stroke`, `stroke-width` (if design requires outlines)

---

## Build Pipeline

### Step 1 — SVGO Optimization

**Command**: `npm run build:bnppre-icons:svgo`
**Config**: `svgo.config.js`

Reduces file size by removing:
- XML declarations, comments, metadata tags
- Empty `<defs>`, unused IDs
- Redundant whitespace and precision

Does **not** rename files or touch fill attributes.

### Step 2 — BNPPRE Normalization

**Command**: `npm run build:bnppre-icons`
**Script**: `scripts/bnppre-icon.js`

1. **Renames files** to comply with naming convention:
   - Converts to lowercase
   - Removes accents (é → e, ç → c, etc.)
   - Replaces spaces and underscores with hyphens
   - Removes French stop words (le, la, les, de, du, des…)
   - Truncates to 30 chars
   - Appends numeric suffix if name collision detected

2. **Removes hardcoded dark fills**:
   - Detects `fill` values: `#000`, `#000000`, `#333`, `#333333`, `#1a1a1a`, `#050505`, `black`
   - Replaces with `currentColor`, then strips the attribute

3. **Strips all remaining `fill` attributes** from SVG elements

### Step 3 — Validation

**Command**: `npm run build:bnppre-icons:check`

Validates every SVG in `assets/icons/` against:
- Naming convention (lowercase, hyphens, max 30 chars, no accents)
- Uniqueness across categories
- `viewBox="0 0 24 24"` presence
- No hardcoded dark colors remaining
- No `width`/`height` attributes

Exit code `0` = all clear. Non-zero = list of violations printed.

---

## UI Icons Integration

The icon pack is declared in `ui_suite_bnppre.icons.yml`. Once Drupal cache is cleared, icons appear in:

- **Icon picker UI**: `/admin/appearance/ui/icons`
- **CKEditor 5**: icon insertion (if `ui_icons_ckeditor5` enabled)
- **Field formatters**: (if `ui_icons_field` enabled)
- **Menus**: (if `ui_icons_menu` enabled)

### Using an Icon in Twig

```twig
{# Using the UI Icons Twig extension #}
{{ icon('bnppre', 'arrow-right') }}

{# With custom CSS class #}
{{ icon('bnppre', 'search', { attributes: { class: 'icon icon--lg' } }) }}
```

### Bootstrap Icons (Separate Pack)

In addition to the custom BNPPRE pack, the theme copies Bootstrap Icons into `web/libraries/bootstrap-icons/icons/` via `npm run build:icons`. These are a separate icon pack registered independently.

---

## Windows Local Environment Note

Due to a core `IconFinder.php` bug on Windows (drive letters `C:\` treated as URL schemes), icon previews may show as blank in local WAMP environments. A local patch exists at `web/core/lib/Drupal/Core/Theme/Icon/IconFinder.php`. Do not revert this patch when updating Drupal core without verifying Windows compatibility first.
