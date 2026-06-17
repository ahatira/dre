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
bash src/scripts/main.sh tools build --production
```

## Build manuel (npm / thèmes)

Équivalent de `make build` sans `main.sh` — utile sur Jenkins ou pour debug.
Remplacer `/path/to/ps_project_wsl` par la racine du checkout (`${WORKSPACE}`, etc.).

**Prérequis :** Node ≥ 18 (recommandé 20), Composer, bash (pas `sh`).

```bash
# Composer (optionnel si vendor/ déjà présent)
cd /path/to/ps_project_wsl/src
composer install --no-interaction --optimize-autoloader --no-dev

# Libs JS Drupal (racine src/)
cd /path/to/ps_project_wsl/src
rm -rf node_modules web/libraries
npm ci
npm run libs

# Thème parent ui_suite_bnp (CSS + icônes)
cd /path/to/ps_project_wsl/src/web/themes/custom/ui_suite_bnp
rm -rf node_modules
npm ci
npm run build

# Bootstrap pour ps_theme (copie, pas symlink)
cd /path/to/ps_project_wsl/src
rm -rf web/libraries/bootstrap
cp -a web/themes/custom/ui_suite_bnp/node_modules/bootstrap web/libraries/bootstrap

# Thème ps_theme (Gulp production)
cd /path/to/ps_project_wsl/src/web/themes/custom/ps_theme
rm -rf node_modules
npm ci
npm run gulp-prod
```

**Sans `package-lock.json`** — remplacer chaque `npm ci` par `npm install --no-save`.

**Vérification :**

```bash
test -f /path/to/ps_project_wsl/src/web/libraries/bootstrap/scss/_functions.scss && echo "bootstrap OK"
test -f /path/to/ps_project_wsl/src/web/themes/custom/ps_theme/assets/css/styles.css && echo "ps_theme CSS OK"
bash /path/to/ps_project_wsl/src/scripts/main.sh tools check
```

**CI / Jenkins** — préférer le script (Docker Node 20 si `CI=true`) :

```bash
cd /path/to/ps_project_wsl
export CI=true
bash src/scripts/main.sh tools build --production
```

## Structure

```
scripts/
├── main.sh
├── _core/          # bootstrap, config, log, runtime, drush, multisite, helpers
├── drupal/         # install, import, demo, deploy, cache-clear, export-solr, rbac-sync, create-test-users
└── tools/          # env, build, check
```

## Workflow (3 steps)

| Step | Command | Content |
|------|---------|---------|
| 1 | `make install [country]` | Drupal shell, modules, dictionary, theme |
| 2 | `make import [country]` | Migrate CRM XML + Solr |
| 3 | `make demo [country]` | ps_demo menus / homepage |

Solr config export (commit `conf/solr/{core_name}/` for prod):

```bash
make export-solr              # default country com
make export-solr -- --finalize-all
```

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
