# BNP Editor - Installation Guide

## Prerequisites

- Drupal 11.x
- CKEditor 5 (Drupal core)
- PHP 8.2+
- Modules listés dans `bnp_editor.info.yml` (Linkit, Entity Embed, plugins CKEditor contrib, etc.) installés via Composer
- Rôles baseline `bnp_admin` recommandés (`content_editor`, `administrator`, …)

## Installation Steps

### 1. Enable the module

Via Drush:

```bash
cd /path/to/drupal/root
drush en bnp_editor -y
drush cr
```

Via UI:

1. Navigate to `/admin/modules`
2. Search for "BNP Editor"
3. Enable and install

À l'activation :

- Les configs `config/optional/` (formats + éditeurs `full_html` / `basic_html`) sont importées si les dépendances sont satisfaites
- `BnpEditorRoleInstaller` accorde les permissions `use text format *` aux rôles BNP (et `ps_*` si `ps_core` est actif)

### 2. Import translations

For each language you need:

```bash
drush locale:import fr web/modules/custom/bnp_editor/translations/fr.po --type=customized --override=all
drush cr
```

(Répéter pour `nl`, `es`, `it`, `lb`, `pl`, `de` — voir `translations/`.)

### 3. Verify text formats

1. Navigate to `/admin/config/content/formats`
2. Confirm `full_html`, `basic_html`, `restricted_html`, `plain_text` are present and configured
3. There is **no** custom `bnp_rich_text` format

### 4. Configure BNP Editor settings

1. Navigate to `/admin/config/content/bnp-editor` (permission `administer bnp editor`)
2. Options: custom plugins flag, media embed, allowed link protocols

### 5. Permissions

Applied automatically on install. On existing sites:

```bash
drush updb -y
```

Manual check at `/admin/people/permissions` :

- **Administer BNP Editor** — `administrator`, `site_admin`, `ps_admin` (if PS)
- **Use Full HTML text format** — admins / content_admin / ps_admin
- **Use Basic HTML text format** — content_editor, translate roles, ps_content_editor
- **Use Restricted HTML text format** — seo_admin, authenticated, etc.

See [README.md](README.md) for the full role matrix.

### 6. Test the installation

1. Edit content with a body or text field
2. Select **Full HTML** or **Basic HTML**
3. Confirm CKEditor 5 loads with the expected toolbar
4. Test links, lists, embeds as configured in `config/optional/`

## Configuration Export

```bash
drush cex -y
```

Export includes `bnp_editor.settings` and any overridden filter/editor configs.

## Updating

1. Replace module files
2. `drush updb -y` (runs `bnp_editor_update_9001` for role permissions on existing sites)
3. `drush cr`

## Troubleshooting

### CKEditor doesn't load

1. `drush cr`
2. Check browser console
3. Verify `editor.editor.full_html` / `basic_html` config exists: `drush config:get editor.editor.full_html`

### Translations missing

Re-import `.po` files and clear cache.

### Custom CKEditor 5 plugins

1. Add plugin class under `src/Plugin/CKEditor5Plugin/`
2. Register library in `bnp_editor.libraries.yml`
3. `drush cr`

See [ARCHITECTURE.md](ARCHITECTURE.md).

### Permissions missing after upgrade

```bash
drush updb -y
drush cr
```

Or re-apply via:

```bash
drush php:eval "\Drupal::service('bnp_editor.role_installer')->applyDefaultPermissions();"
```

## Uninstallation

```bash
drush pmu bnp_editor -y
drush config:delete bnp_editor.settings
drush cr
```

Filter formats `full_html` / `basic_html` are **core** formats — do not delete them unless you know the impact. Remove only module-specific overrides if exported separately.

## Support

- [README.md](README.md)
- [QUICKSTART.md](QUICKSTART.md)
- [ARCHITECTURE.md](ARCHITECTURE.md)
- [MIGRATION.md](MIGRATION.md) — migration depuis `bnp_rich_text`
