# BNP Editor - Compatible Contrib Modules (Drupal 11)

This document lists optional contrib modules that work with BNP Editor on **Drupal 11**.

## ✅ Installed & Ready

All modules below are **already installed** via Composer. Enable them as needed:

```bash
cd /home/amine/ps_project_wsl/src
drush en [module_name] -y
drush cr
```

## 📦 Available Modules

### Links & Navigation

#### 1. Linkit (drupal/linkit v7.0.15)
**Purpose**: Autocomplete internal entity links

**Enable**:
```bash
drush en linkit -y
```

**Setup**:
1. Go to `/admin/config/content/linkit`
2. Create a linkit profile
3. Add matchers (Content, Taxonomy, etc.)
4. Add to CKEditor at `/admin/config/content/formats/manage/full_html`

**Benefits**:
- Easy internal linking with search
- Links to nodes, terms, users, files
- Reduces broken internal links
- Auto-updates when content titles change

---

#### 2. Editor Advanced Link (v2.3.4)
**Purpose**: Advanced link attributes (rel, target, class, title)

**Enable**:
```bash
drush en editor_advanced_link -y
```

**Setup**:
- Automatically adds fields to CKEditor link dialog
- No additional configuration needed

**Benefits**:
- Add `rel="nofollow"` for SEO
- Add `target="_blank"` with security
- Add CSS classes to links
- Add title attribute for accessibility

---

#### 3. Anchor Link (v3.0.4)
**Purpose**: Create HTML anchors and jump links

**Enable**:
```bash
drush en anchor_link -y
```

**Setup**:
- Automatically adds anchor button to CKEditor toolbar
- Configure at `/admin/config/content/formats/manage/full_html`

**Benefits**:
- Table of contents with jump links
- Long page navigation
- One-page websites
- Accessible skip links

---

### Content & Entities

#### 4. Entity Embed (v1.6.0)
**Purpose**: Embed any Drupal entity in content

**Enable**:
```bash
drush en entity_embed -y
```

**Setup**:
1. Go to `/admin/config/content/embed`
2. Create embed buttons (e.g., "Node", "Taxonomy", "User")
3. Add buttons to CKEditor toolbar at `/admin/config/content/formats`
4. Enable filter "Display embedded entities"

**Benefits**:
- Embed nodes, taxonomy terms, media, users
- Reusable content blocks
- Auto-updates when entity changes
- Choose view mode per embed

**Example Use Cases**:
- Embed related articles
- Insert taxonomy term descriptions
- Display user profiles inline
- Reusable call-to-action blocks

---

### Paths & URLs

#### 5. Pathologic (v2.0.0)
**Purpose**: Auto-correct internal link paths

**Enable**:
```bash
drush en pathologic -y
```

**Setup**:
1. Go to `/admin/config/content/formats/manage/full_html`
2. Enable filter "Correct URLs with Pathologic"
3. Configure base URL (usually auto-detected)
4. Set path processing settings

**Benefits**:
- Fixes relative links (`/node/123` → full URL)
- Corrects domain changes (dev → prod)
- Handles multisite installs
- Protocol-relative URLs

**Note**: Filter-based, processes output on render.

---

#### 6. Token Filter (v2.2.1)
**Purpose**: Replace tokens in text content

**Enable**:
```bash
drush en token_filter -y
```

**Setup**:
1. Go to `/admin/config/content/formats/manage/full_html`
2. Enable filter "Replace tokens"
3. Browse available tokens at `/admin/help/token`

**Benefits**:
- Dynamic content: `[node:title]`, `[current-user:name]`
- Site info: `[site:name]`, `[site:url]`
- Date/time: `[current-date:short]`
- User info: `[current-user:mail]`

**Example**:
```
Welcome [current-user:name]! 
Today is [current-date:custom:l, F j, Y].
You're reading [node:title] on [site:name].
```

**Note**: Filter-based, processes on render.

---

### Media & Images

#### 7. Blazy (v3.0.17)
**Purpose**: Lazy loading for images and media

**Enable**:
```bash
drush en blazy -y
```

**Setup**:
1. Go to `/admin/config/media/blazy`
2. Configure lazy load settings
3. Go to field display settings (e.g., `/admin/structure/types/manage/article/display`)
4. Change image formatter to "Blazy"

**Benefits**:
- Faster initial page load
- Reduced bandwidth usage
- Responsive images support
- Native lazy loading or JavaScript fallback

**Note**: Field formatter, not CKEditor plugin.

---

#### 8. Slick (v3.0.7)
**Purpose**: Carousel/slider for media

**Enable**:
```bash
# Requires Blazy
drush en blazy slick -y
```

**Setup**:
1. Go to `/admin/config/media/slick`
2. Create slick optionsets
3. Apply to multi-value image fields
4. Or create Views with Slick format

**Benefits**:
- Image carousels
- Video sliders
- Touch-friendly swipe
- Responsive breakpoints
- Accessibility (keyboard navigation)

**Note**: Field formatter and Views format, not CKEditor plugin.

---

## 🔧 Quick Setup Scenarios

### Scenario 1: Basic Enhanced Editing
```bash
drush en linkit editor_advanced_link anchor_link -y
drush cr
```
**Use case**: Better links and navigation for standard content editing.

---

### Scenario 2: Full Media Experience
```bash
drush en entity_embed blazy slick -y
drush cr
```
**Use case**: Rich media content with embeds, lazy loading, and carousels.

---

### Scenario 3: Enterprise Publishing
```bash
drush en linkit editor_advanced_link anchor_link entity_embed \
  pathologic token_filter blazy -y
drush cr
```
**Use case**: Professional content management with all features.

---

## 📊 Compatibility Matrix

| Module | Drupal 11 | CKEditor 5 | Integration Type |
|--------|-----------|------------|------------------|
| Linkit | ✅ 7.x | ✅ | CKEditor plugin |
| Editor Advanced Link | ✅ 2.x | ✅ | CKEditor plugin |
| Anchor Link | ✅ 3.x | ✅ | CKEditor plugin |
| Entity Embed | ✅ 1.x | ✅ | CKEditor plugin + Filter |
| Pathologic | ✅ 2.x | ⚠️ | Text format filter |
| Token Filter | ✅ 2.x | ⚠️ | Text format filter |
| Blazy | ✅ 3.x | N/A | Field formatter |
| Slick | ✅ 3.x | N/A | Field formatter + Views |

**Legend**:
- ✅ CKEditor 5: Native editor integration (toolbar buttons, dialogs)
- ⚠️ Filter: Works via text format filters (no editor UI)
- N/A: Not an editor plugin (display/formatter)

---

## ❌ Incompatible with Drupal 11

These modules are **NOT** compatible with Drupal 11:

| Module | Reason | Alternative |
|--------|--------|-------------|
| `drupal/ckeditor_bidi` | Requires Drupal 8.x | Use HTML `dir="rtl"` in Source Editing |
| `drupal/ckeditor_emoji` | No Drupal 11 version | Use Unicode emojis or OS emoji picker (⌘+. / Win+.) |
| `drupal/ckeditor5_paste_filter` | No stable release | CKEditor 5 has built-in paste filtering |
| `drupal/ckeditor5_plugin_pack` | No stable release | Use individual plugins as needed |
| `drupal/ckeditor_media_embed` | Dev version only | Use core Media module (already configured) |
| `drupal/ckeditor_media_resize` | No Drupal 11 version | Use image upload settings in editor config |
| `drupal/edit_media_modal` | No Drupal 11 version | Use core media library modal |
| `drupal/ace_editor` | No Drupal 11 version | Use CKEditor 5 Source Editing (already enabled) |

---

## 🔍 Post-Installation Checks

### Verify Installed Modules
```bash
drush pml | grep -E "(linkit|anchor|editor_advanced|entity_embed|pathologic|token_filter|blazy|slick)"
```

**Expected output** (after enabling):
```
 Enabled   linkit                        Linkit                      7.0.15
 Enabled   anchor_link                   Anchor Link                 3.0.4
 Enabled   editor_advanced_link          Editor Advanced Link        2.3.4
 Enabled   entity_embed                  Entity Embed                1.6.0
 Enabled   pathologic                    Pathologic                  2.0.0
 Enabled   token_filter                  Token Filter                2.2.1
 Enabled   blazy                         Blazy                       3.0.17
 Enabled   slick                         Slick Carousel              3.0.7
```

---

### Check CKEditor Configuration
```bash
drush config:get editor.editor.full_html
```

Look for enabled plugins and toolbar buttons.

---

### Test in UI
1. **Links**: Go to `/node/add/article`, create text, select it, click link button
   - With Linkit: Should see autocomplete
   - With Advanced Link: Should see rel/target/class fields
   
2. **Anchors**: Look for anchor button in toolbar (after enabling anchor_link)

3. **Entity Embed**: Look for embed buttons in toolbar (after configuring)

4. **Filters**: Create content with external links, tokens, relative paths
   - View rendered output to verify filters working

---

## 🐛 Troubleshooting

### Module not appearing in CKEditor toolbar
```bash
drush cr
drush cex -y
```

Then go to `/admin/config/content/formats/manage/full_html` and check toolbar configuration.

---

### Linkit autocomplete not working
1. **Check permissions**: `/admin/people/permissions` → "Use linkit"
2. **Check profile**: `/admin/config/content/linkit` → Edit profile → Ensure matchers enabled
3. **Check CKEditor**: `/admin/config/content/formats/manage/full_html` → Ensure Linkit enabled

---

### Entity Embed button missing
1. **Create button**: `/admin/config/content/embed` → Add embed button
2. **Add to toolbar**: `/admin/config/content/formats/manage/full_html` → Drag button to toolbar
3. **Enable filter**: Ensure "Display embedded entities" filter is enabled

---

### Pathologic not correcting links
1. **Check filter order**: Pathologic should run AFTER other filters
2. **Check base URL**: `/admin/config/content/formats/manage/full_html` → Pathologic settings
3. **Clear cache**: `drush cr`

---

### Blazy/Slick not lazy loading
1. **Check field formatter**: Field display settings must use "Blazy" formatter
2. **Check JavaScript**: Ensure Blazy library loading (`/admin/reports/status`)
3. **Check HTML output**: Inspect `<img>` tags for `loading="lazy"` or `data-src`

---

## 📚 Additional Resources

- [Linkit Documentation](https://www.drupal.org/docs/contributed-modules/linkit)
- [Entity Embed Documentation](https://www.drupal.org/docs/contributed-modules/entity-embed)
- [Blazy Documentation](https://www.drupal.org/docs/contributed-modules/blazy)
- [CKEditor 5 Module List](https://www.drupal.org/project/project_module?f%5B2%5D=&f%5B3%5D=drupal_core%3A11&text=ckeditor)
- [Drupal 11 Compatibility Tracker](https://www.drupal.org/docs/upgrading-drupal/how-to-prepare-your-drupal-7-or-8-site-for-drupal-9/upgrading-a-drupal-8-site-to-drupal-11)

---

## 💡 Tips & Best Practices

### Performance
- Enable Blazy on all image fields for better page load
- Use Slick sparingly (JavaScript overhead)
- Combine Pathologic with site:url tokens for portable content

### Security
- Use Editor Advanced Link to add `rel="noopener noreferrer"` to external and `target="_blank"` links
- Restrict Entity Embed permissions to trusted roles

### Accessibility
- Use Anchor Link for skip links and page navigation
- Add title attributes via Editor Advanced Link
- Ensure Slick carousels have keyboard navigation enabled

### Content Reusability
- Use Entity Embed for reusable content blocks
- Use Token Filter for dynamic, portable content
- Use Pathologic for environment-independent links

---

**Last updated**: June 2, 2026  
**Tested with**: Drupal 11.3.11, BNP Editor 1.0.0
