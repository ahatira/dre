# PS Project Scripts (`src/scripts/`)

CLI bash + Drush — **host only** (WSL dev et serveurs prod). Pas de Docker ici.

## Depuis la racine du repo (dev)

```bash
make env                  # scripts/docker/env.sh → src/.env
make generate-multisite   # scripts/multisite/ → src/web/sites/countries.yml
make verify-multisite
make build                # Composer + NPM (une commande)
make install fr
make deploy
```

## Depuis `src/` — dev

```bash
make build                # Composer + NPM
make build-npm            # CSS/themes uniquement
make verify
make deploy fr
```

## Depuis `src/` — Jenkins (2 stages)

```bash
# Stage 1 — Composer (PHP 8.3 + Composer)
make build-composer PRODUCTION=1

# Stage 2 — NPM (Node.js 20+)
make build-npm PRODUCTION=1

# Gate avant packaging / deploy
make verify
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

`DB_HOST=127.0.0.1` dans `src/.env` pour Drush sur l'hôte ; le conteneur PHP utilise `DB_HOST=postgres` (override docker-compose).

Mail, Solr connector, memcache : `docs/MULTISITE_OPS.md` § Infrastructure (config_ignore + `drush config:set`).

## Drush

Alias `@ps.{country}` — `drush/sites/ps.site.yml` (généré par `make generate-multisite`).
