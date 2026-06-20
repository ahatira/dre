# PS Project Scripts (`src/scripts/`)

CLI bash + Drush — **host only** (WSL dev et serveurs prod). Pas de Docker ici.

## Depuis la racine du repo (dev)

```bash
make env                  # scripts/docker/env.sh → src/.env
make generate-multisite   # scripts/multisite/ → src/web/sites/countries.yml
make verify-multisite
make build
make install fr
make deploy
```

## Depuis `src/` (prod ou direct)

```bash
make build
make deploy fr
bash scripts/main.sh drupal drush fr status
```

## Structure

```
scripts/
├── main.sh
├── _core/          # config, drush (host), multisite, countries-cli.php
├── drupal/         # install, deploy, import, …
└── tools/          # build, check
```

Ops Docker et multisite (generate/verify) : **`../scripts/`** à la racine du monorepo.

E2E modules : `scripts/e2e/common.sh` — Drush hôte (`@ps.com`) et URL par défaut `http://com.localhost:8080`.

## Configuration

| Environment | `APP_ENV` | Source |
|-------------|-----------|--------|
| dev | `dev` | `src/.env` (`make env` depuis la racine) |
| int / staging / prod | — | Variables système |

`DB_HOST=127.0.0.1` dans `src/.env` pour Drush sur l’hôte ; le conteneur PHP utilise `DB_HOST=postgres` (override docker-compose).

Mail, Solr connector, memcache : `docs/MULTISITE_OPS.md` § Infrastructure (config_ignore + `drush config:set`).

## Drush

Alias `@ps.{country}` — `drush/sites/ps.site.yml` (généré par `make generate-multisite`).
