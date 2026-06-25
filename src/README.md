# Property Search — `src/` (artefact déployable)

Dossier versionné déployé tel quel sur INT, staging et production.

## Prérequis serveur

- PHP 8.3 + extensions (pgsql, …)
- Composer (pour le build initial)
- Node.js 20+ + npm 10+ (build front — WSL Linux, not Windows `/mnt/c/` path)
- Drush via `vendor/bin/drush` (après `composer install`)

## Build

### Dev local — une seule commande

```bash
make build                # Composer + NPM (équivalent des 2 stages Jenkins)
make build-npm            # SCSS/CSS uniquement (itération front)
make verify               # contrôle artefacts
```

### Jenkins — 2 stages séparés

| Stage | Commande | Prérequis agent |
|-------|----------|-----------------|
| 1 — Composer | `make build-composer --production` | PHP 8.3, Composer 2 |
| 2 — NPM | `make build-npm --production` | Node.js 20+, npm 10+ |
| Gate | `make verify` | après stage 2 |

Équivalent scripts :

```bash
# Stage 1
bash scripts/main.sh tools build --composer-only --production

# Stage 2
bash scripts/main.sh tools build --npm-only --production --keep-npm

# Gate
bash scripts/main.sh tools check
```

Le stage 2 suppose que `vendor/` est déjà présent (artefact ou workspace du stage 1).

## Deploy et ops

```bash
make deploy               # cim + updb + cr (tous pays)
make deploy fr            # un pays
make drush fr uli
```

Configuration : variables système (pas de `.env` en prod). Template : `../docs/env.prod.example` (monorepo) ou document ops.

## Scripts

```bash
scripts/main.sh tools build [--composer-only | --npm-only] [OPTIONS]
scripts/main.sh tools check
scripts/main.sh drupal deploy [country...]
scripts/main.sh drupal install [country...]
```

Options build : `--production`, `--no-cache`, `--keep-npm`

Pas de logique Docker dans `src/scripts/` — Docker est géré à la racine du repo (dev uniquement).

## Multisite

`web/sites/countries.yml` est **généré** depuis `scripts/multisite/countries.yml` (racine repo) via `make generate-multisite` avant commit ou déploiement.

En prod, ce fichier doit être présent dans `src/web/sites/` (livré avec l’artefact).
