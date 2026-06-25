# CRM import pipeline — runbook ops

Guide opérationnel pour l'import XML CRM (`ps_migrate`) en dev WSL et en prod Kubernetes.

Références : [TARGET_DESIGN.md](./TARGET_DESIGN.md), [README](../README.md).

## Vue d'ensemble

```
CRM dépose .xml → incoming/ → (queue) → processing/ → staging public://crm/offers.xml
       → Migrate (10 migrations) → archive/ ou failed/
       → import_run + stats + alerte email + Solr post-run
```

| Composant | Détail |
|-----------|--------|
| Worker dev | Drush **hôte WSL** (`make drush`, `vendor/bin/drush @ps.{code}`) |
| Worker prod | Kubernetes CronJob / Job (PHP CLI, 1 Go, deadline 3900 s) |
| Lock | `state: ps_migrate.import_pipeline.lock` — 1 run actif par site |
| Rollback prod | `ps:import:rollback --run-id` (pas `migrate:rollback`) |

## Chemins fichiers (défaut dev)

Config BO : `/admin/ps/import/settings` (`ps_migrate.import_pipeline_settings`).

| Dossier | URI défaut |
|---------|------------|
| Incoming | `private://crm/incoming` |
| Processing | `private://crm/processing` |
| Archive | `private://crm/archive` |
| Failed | `private://crm/failed` |
| Staging Migrate | `public://crm/offers.xml` |

En prod, `APP_PRIVATE_PATH` est monté — **ne pas `mkdir`** sur private en prod.

## Multisite

| Code pays | Drush | URL dev (ex.) | `site_dir` |
|-----------|-------|---------------|------------|
| `fr` | `@ps.fr` | http://fr.localhost:8083 | `france` |
| `com` | `@ps.com` | http://com.localhost:8080 | `com` |
| … | `@ps.{code}` | voir `scripts/multisite/countries.yml` | |

Manifest source : `scripts/multisite/countries.yml` → `make generate-multisite`.

## Commandes Drush (hôte WSL)

```bash
# Depuis la racine repo
make drush fr ps:import:run -- --sync=1 --limit=1 --mode=full
make drush fr ps:import:queue-status
make drush fr ps:import:queue-process -- --count=1
make drush fr ps:import:enqueue
make drush fr ps:import:recover-stale
make drush fr ps:import:rollback -- --run-id=42
make drush fr ps:import:rollback -- --run-id=42 --force=1
```

| Commande | Usage |
|----------|-------|
| `ps:import:run` | `--sync=1` traite immédiatement ; sans sync → enqueue si queue activée |
| `ps:import:enqueue` | Scan `incoming/` → queue Drupal |
| `ps:import:queue-process` | Consomme N jobs queue |
| `ps:import:queue-status` | Pending / processing / failed |
| `ps:import:recover-stale` | Reprend fichiers bloqués en `processing/` |
| `ps:import:rollback` | Rollback run (snapshot requis, garde-fou run plus récent) |

**Interdit** : `docker exec ps_php drush …` — Drush s'exécute sur l'hôte WSL uniquement.

## Modes import

| Mode | Migrations exécutées |
|------|----------------------|
| `full` | 10 migrations (agents, features, médias, offres, traductions, …) |
| `delta` | `ps_offer_from_xml` + `ps_offer_translations_from_xml` |

Config BO : mode par défaut, `skip_unchanged_offers` (delta), retry médias, Solr post-run, alertes.

## Procédures courantes

### Import manuel (dev)

1. `make up`
2. Déposer XML dans incoming **ou** upload BO `/admin/ps/import/upload`
3. `make drush fr ps:import:run -- --sync=1 --limit=1 --mode=full`
4. Vérifier `/admin/ps/import/runs`

### Import via queue (prod-like)

```bash
make drush fr ps:import:enqueue
make drush fr ps:import:queue-process -- --count=1
make drush fr ps:import:queue-status
```

### Rollback d'un run

1. Ouvrir `/admin/ps/import/runs/{id}` — vérifier snapshot et statut
2. BO : bouton Rollback **ou** CLI :

```bash
make drush fr ps:import:rollback -- --run-id=42
```

Limites : best effort — unpublish offres créées, restore révision backup pour updates ; **pas** de rollback fichiers/médias distants. Offres `field_internal_lock` ignorées.

### Fichier bloqué en processing

```bash
make drush fr ps:import:recover-stale
```

### Après `updatedb` avec nouveaux champs `import_run`

Les champs entity (`duration_ms`, `snapshot`, `rollback_status`) nécessitent un cache PHP à jour :

```bash
make drush fr updatedb -y
make restart          # vide opcache conteneur ps_php
make drush-cr
```

Sans restart, pages BO `/admin/ps/import/runs/{id}` peuvent renvoyer 500 (`Field X is unknown`).

## Alertes email

Config : `/admin/ps/import/settings` — `alert_email_enabled`, `alert_email_recipients`.

| Événement | Email |
|-----------|-------|
| Run `failed` | Oui (si activé + destinataires) |
| Skip rate élevé | `alert_email_on_warning` — **non implémenté** |

Dev : Mailpit http://localhost:8025 (transport dans `settings.local.php`).

Test automatisé :

```bash
bash src/web/modules/custom/ps_migrate/tests/e2e_import_alert.sh
bash src/web/modules/custom/ps_migrate/tests/b2b_import_full.sh
```

## Observabilité

| Source | Action |
|--------|--------|
| BO runs | `/admin/ps/import/runs` — durée, SLA, stats migration, rollback |
| Watchdog | `drush @ps.fr watchdog:show --type=ps_migrate` |
| Rejets publication | `/admin/ps/import/rejections` |
| Solr | Post-run auto si `post_run_index_solr` activé |

SLA cible : 1 h (warning si `duration_ms` > 3600 s — affiché BO).

## Prod Kubernetes (cible)

| CronJob | Rôle | Fréquence indicative |
|---------|------|----------------------|
| `import-enqueue` | Scan incoming → queue | */5 min |
| `import-worker-delta` | 9 sites séquentiels, `--count=1` | */5 min (jour) |
| `import-worker-full` | 9 Jobs parallèles | hebdo nuit |

Variables : `DRUSH_ALIAS=@ps.fr`, secrets DB/files via montages PS existants.

Garde-fous : lock Drupal par site, `activeDeadlineSeconds: 3900`, `memory: 1Gi`.

Manifestes : **à livrer** (repo ops / Helm — hors `src/` deployable).

## Dépannage

| Symptôme | Cause probable | Action |
|----------|----------------|--------|
| Page run 500 | Opcache stale après updatedb | `make restart && make drush-cr` |
| Double import | Lock contourné | Vérifier `queue-status`, un seul worker |
| `alert skipped` watchdog | Recipients vides | Renseigner emails BO |
| Migration occupée | Run précédent interrompu | Attendre ou `recover-stale` + libérer lock state |
| Rollback blocked | Run plus récent | `--force=1` (avec prudence) |
| Parse cache error | Staging XML absent | Vérifier `public://crm/offers.xml` après processing |

## Tests

```bash
# B2B shell (BO + Drush + Mailpit)
bash src/web/modules/custom/ps_migrate/tests/b2b_import_full.sh

# Kernel (depuis src/, base PostgreSQL du pays dans .env)
bash web/modules/custom/ps_migrate/tests/run-kernel.sh

# Unit
bash web/modules/custom/ps_migrate/tests/run-unit.sh
```

Remplacer `{DB_NAME}` par la base du site (ex. `DB_NAME_FR` dans `src/.env`).

## Ne pas faire en prod

- `migrate:rollback` sur les migrations CRM
- Import complet via cron web PHP-FPM (timeout)
- `mkdir` sur `APP_PRIVATE_PATH` monté
- Drush dans le conteneur Docker
