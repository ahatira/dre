# Property Search — `src/` (artefact déployable)

Dossier versionné déployé tel quel sur INT, staging et production.

## Prérequis serveur

- PHP 8.3 + extensions (pgsql, …)
- Composer (pour le build initial)
- Node.js 20 + npm (build front)
- Drush via `vendor/bin/drush` (après `composer install`)

## Commandes

```bash
make build --production   # vendor + assets
make verify               # contrôle vendor/libs
make deploy               # cim + updb + cr (tous pays)
make deploy fr            # un pays
make drush fr uli
```

Configuration : variables système (pas de `.env` en prod). Template : `../docs/env.prod.example` (monorepo) ou document ops.

## Scripts

```bash
scripts/main.sh tools build
scripts/main.sh drupal deploy [country...]
scripts/main.sh drupal install [country...]
```

Pas de logique Docker dans `src/scripts/` — Docker est géré à la racine du repo (dev uniquement).

## Multisite

`web/sites/countries.yml` est **généré** depuis `scripts/multisite/countries.yml` (racine repo) via `make generate-multisite` avant commit ou déploiement.

En prod, ce fichier doit être présent dans `src/web/sites/` (livré avec l’artefact).
