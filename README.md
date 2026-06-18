# Property Search — développement local (repo racine)

Ce dépôt contient l’environnement de **développement** et le dossier **`src/`** déployable sur INT/staging/prod.

## Structure

```
├── Makefile              # Docker + délégation vers src/
├── docker/               # Compose, nginx, postgres, solr (dev only)
├── scripts/
│   ├── docker/           # env, fix-permissions, init Solr cores
│   └── multisite/        # countries.yml (source), generate, verify
├── conf/solr/            # Config Solr exportée (dev)
└── src/                  # ← Artefact déployé (Drupal + scripts projet)
```

## Démarrage rapide

```bash
make bootstrap          # .env + Docker + sync multisite + build
make verify-multisite   # valider les variables pays
make install            # greenfield (9 pays)
```

Sites locaux : `http://fr.localhost:8083`, `http://com.localhost:8080`, etc.

## Rôles des Makefiles

| Makefile | Usage |
|----------|--------|
| **Racine** | `make up`, `make env`, `make generate-multisite`, délègue le reste à `src/` |
| **src/** | `make build`, `make deploy`, `make drush` — utilisé aussi **en prod** |

Drush s’exécute **sur l’hôte** (WSL). `src/.env` utilise `DB_HOST=127.0.0.1` ; le conteneur PHP reçoit `DB_HOST=postgres` via `docker-compose.yml`.

## Multisite

- Source : `scripts/multisite/countries.yml`
- Sync : `make generate-multisite` → `src/web/sites/countries.yml` + `src/drush/sites/ps.site.yml`

Voir `docs/MULTISITE_OPS.md` et `docs/env.prod.example`.
