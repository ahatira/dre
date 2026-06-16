# PS Project Scripts

CLI bash + Drush — portable (host-first, Docker optionnel).

## Usage

```bash
# From project root via Makefile (recommended)
make env
make build
make verify          # CI gate — vendor + libraries
make install es      # shell greenfield (default: all countries)
make import es       # CRM sample + Solr (default: com)
make demo es         # demo content (default: com)
make rbac-sync       # separate — not part of install
make deploy          # cim + updb + cr (all countries)

# Direct CLI
bash src/scripts/main.sh drupal install --help
bash src/scripts/main.sh tools build
```

## Structure

```
scripts/
├── main.sh
├── _core/          # bootstrap, config, log, runtime, drush, multisite, helpers
├── drupal/         # install, import, demo, deploy, cache-clear, rbac-sync, create-test-users
└── tools/          # env, build, check
```

## Workflow (3 steps)

| Step | Command | Content |
|------|---------|---------|
| 1 | `make install [country]` | Drupal shell, modules, dictionary, theme |
| 2 | `make import [country]` | Migrate CRM XML + Solr |
| 3 | `make demo [country]` | ps_demo menus / homepage |

## Configuration

| Environment | Source |
|-------------|--------|
| dev | `src/.env` (from `make env`) |
| prod/staging | System environment variables |

Optional: `GOOGLE_MAPS_API_KEY` — applied at install to `geofield_map.settings` and the Google geocoder provider (maps search + offer detail).

Drush runs on **host** if `src/vendor/bin/drush` exists, else in **ps_php** container.

Multisite Drush: **`@ps.{country}`** site aliases (`src/drush/sites/ps.site.yml`).
The alias `uri` is the site directory name (`es` → `web/sites/es`), not a full HTTP URL.
Front URLs for browsing remain in `.env` (`APP_DOMAIN_*`).

## Verify (CI)

`make verify` checks only:
- `src/vendor/autoload.php`
- Required `web/libraries/*` paths

No Drupal bootstrap. Tests: `composer test:*` separately.

## Archive

Previous scripts: `archived-scripts/scripts-2026-06-16/`
