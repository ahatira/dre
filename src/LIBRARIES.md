# JavaScript Libraries Management

This document describes the npm-based workflow for managing third-party JavaScript libraries in the PS Project.

## Overview

JavaScript libraries are managed via npm and automatically copied to `web/libraries/` using the `copy-files-from-to` package. This approach ensures version control, reproducibility, and easy updates.

## Installed Libraries

### 1. Ace Editor (v1.36.5)
- **Path**: `web/libraries/ace/`
- **Files**: 485 files (modes, themes, extensions, snippets)
- **Size**: ~11MB
- **Usage**: Code editor for Drupal's Ace Editor module
- **Drupal Module**: `ace_editor`

### 2. Clipboard.js (v2.0.11)
- **Path**: `web/libraries/clipboard/`
- **Files**: 2 files (clipboard.js, clipboard.min.js)
- **Size**: ~40KB
- **Usage**: Copy-to-clipboard functionality
- **Drupal Module**: Various custom implementations

### 3. Dropzone (v6.0.0-beta.2)
- **Path**: `web/libraries/dropzone/`
- **Files**: 4 files (JS + CSS)
- **Size**: ~208KB
- **Usage**: Drag & drop file uploads
- **Drupal Module**: `dropzonejs`
- **Detection**: Confirmed at `libraries/dropzone/dropzone-min.js`

### 4. Slick Carousel (v1.8.1)
- **Path**: `web/libraries/slick-carousel/slick/`
- **Files**: 14 files (JS, CSS, fonts, assets)
- **Size**: Variable
- **Usage**: Responsive carousel/slider
- **Drupal Module**: `slick` (optional, not installed)

## Workflow Commands

### Install All Dependencies

```bash
docker exec ps_php bash -c "cd /var/www/html && npm install"
```

### Copy Libraries to web/libraries/

```bash
docker exec ps_php bash -c "cd /var/www/html && npm run libs"
```

This command:
1. Cleans `web/libraries/` directory (via `rimraf`)
2. Copies configured files from `node_modules/` to `web/libraries/`
3. Preserves or flattens directory structure as configured

### Clear Drupal Cache

After copying libraries, always clear Drupal cache:

```bash
docker exec ps_php bash -c "cd /var/www/html/web && ../vendor/bin/drush cr"
```

## Configuration Files

### package.json

Defines npm dependencies and scripts:

```json
{
  "dependencies": {
    "ace-builds": "^1.36.5",
    "clipboard": "^2.0.11",
    "dropzone": "^6.0.0-beta.2",
    "slick-carousel": "^1.8.1"
  },
  "devDependencies": {
    "copy-files-from-to": "^3.2.4",
    "rimraf": "^6.0.1"
  },
  "scripts": {
    "libs": "npm run libs:clean && npm run libs:copy",
    "libs:clean": "rimraf web/libraries",
    "libs:copy": "copy-files-from-to"
  }
}
```

### copy-files-from-to.json

Defines copy rules for each library:

```json
{
  "copyFiles": [
    {
      "//": "--- Ace Editor Library ---",
      "from": ["node_modules/ace-builds/src-min-noconflict/**/*.js"],
      "to": "web/libraries/ace/",
      "toFlat": true
    },
    {
      "//": "--- Clipboard.js Library ---",
      "from": ["node_modules/clipboard/dist/clipboard*.js"],
      "to": "web/libraries/clipboard/",
      "toFlat": true
    },
    {
      "//": "--- Dropzone Library ---",
      "from": [
        "node_modules/dropzone/dist/dropzone-min.js",
        "node_modules/dropzone/dist/dropzone.js",
        "node_modules/dropzone/dist/dropzone.css",
        "node_modules/dropzone/dist/basic.css"
      ],
      "to": "web/libraries/dropzone/",
      "toFlat": true
    },
    {
      "//": "--- Slick Carousel Library ---",
      "from": ["node_modules/slick-carousel/slick/**/*"],
      "to": "web/libraries/slick-carousel/slick/"
    }
  ],
  "settings": {
    "whenFileExists": "overwrite"
  }
}
```

## Adding a New Library

1. **Add to package.json**:
   ```bash
   npm install --save <library-name>
   ```

2. **Configure copy rules** in `copy-files-from-to.json`:
   ```json
   {
     "//": "--- New Library ---",
     "from": ["node_modules/<library-name>/dist/**/*"],
     "to": "web/libraries/<library-name>/",
     "toFlat": false
   }
   ```

3. **Run the workflow**:
   ```bash
   npm install
   npm run libs
   ```

4. **Fix permissions** (if needed):
   ```bash
   docker exec ps_php bash -c "cd /var/www/html && chown -R www-data:www-data web/libraries/"
   ```

5. **Clear Drupal cache**:
   ```bash
   docker exec ps_php bash -c "cd /var/www/html/web && ../vendor/bin/drush cr"
   ```

## Copy Modes

### Flat Mode (`toFlat: true`)
Files are copied directly to the target directory without preserving subdirectories.

**Example**: `node_modules/clipboard/dist/clipboard.min.js` → `web/libraries/clipboard/clipboard.min.js`

### Preserve Structure Mode (`toFlat: false` or omitted)
Directory structure is preserved.

**Example**: `node_modules/slick-carousel/slick/fonts/slick.woff` → `web/libraries/slick-carousel/slick/fonts/slick.woff`

## Drupal Integration

### Status Report Verification

Check library detection at: **Admin** → **Reports** → **Status report**

Expected results:
- ✅ **Ace library**: Installed at `/libraries/ace/`
- ✅ **Dropzone library found**: `libraries/dropzone/dropzone-min.js`

### Module Requirements

Each Drupal module expects specific file paths:

| Module | Expected Path | Configured Path |
|--------|---------------|-----------------|
| ace_editor | `/libraries/ace/ace.js` | ✅ Correct |
| dropzonejs | `/libraries/dropzone/dropzone-min.js` | ✅ Correct |
| slick | `/libraries/slick-carousel/slick/slick.min.js` | ✅ Correct (if installed) |

## Troubleshooting

### Library Not Detected

1. Verify file exists:
   ```bash
   docker exec ps_php bash -c "ls -lh /var/www/html/web/libraries/<library-name>/"
   ```

2. Check permissions:
   ```bash
   docker exec ps_php bash -c "ls -la /var/www/html/web/libraries/"
   ```
   Owner should be `www-data:www-data`

3. Clear Drupal cache:
   ```bash
   docker exec ps_php bash -c "cd /var/www/html/web && ../vendor/bin/drush cr"
   ```

### Permission Issues

If files are owned by `root:root`, fix with:

```bash
docker exec ps_php bash -c "cd /var/www/html && chown -R www-data:www-data web/libraries/"
```

### Wrong Files Copied

1. Check `copy-files-from-to.json` configuration
2. Verify source files in `node_modules/<library>/`
3. Use glob patterns to match specific files:
   - `**/*` - All files recursively
   - `*.js` - All JS files in current directory
   - `dist/**/*.min.js` - All minified JS files in dist/

## Node.js in Docker

Node.js v20.19.2 and npm v9.2.0 are installed in the `ps_php` Docker container.

To access Node/npm directly:

```bash
docker exec -it ps_php bash
cd /var/www/html
npm --version
node --version
```

## Version Control

### Committed Files
- ✅ `package.json`
- ✅ `package-lock.json`
- ✅ `copy-files-from-to.json`

### Ignored Files
- ❌ `node_modules/` (excluded via `.gitignore`)
- ❌ `web/libraries/` (generated, not committed)

Libraries are generated during deployment via `npm install && npm run libs`.

## Deployment Workflow

1. Clone repository
2. Install npm dependencies: `npm install`
3. Copy libraries: `npm run libs`
4. Fix permissions: `chown -R www-data:www-data web/libraries/`
5. Clear Drupal cache: `drush cr`

## References

- [copy-files-from-to Documentation](https://www.npmjs.com/package/copy-files-from-to)
- [Drupal Libraries Management](https://www.drupal.org/docs/8/creating-custom-modules/adding-javascript-and-css-to-custom-modules)
- [Example Implementation](https://github.com/rlhawk/drupal-libraries-npm)

---

**Last Updated**: June 2, 2026  
**Author**: PS Project Team
