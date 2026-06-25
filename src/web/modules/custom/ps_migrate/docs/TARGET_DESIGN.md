# Document de conception cible — Import CRM / Migrate (PS Project)

**Version** : 1.0  
**Statut** : Conception validée (questionnaire) — prêt pour implémentation par phases  
**Périmètre** : Optimisation de l'existant `ps_migrate` (sans `ps_migrate_pipeline` à court terme)  
**Module principal** : `src/web/modules/custom/ps_migrate`  
**Date** : 2025-06-25

---

## 0. Décisions validées (questionnaire)

| Sujet | Décision |
|-------|----------|
| Sources | SFTP/dépôt fichier **+** upload BO |
| Multisite | 1 XML / pays (`@ps.fr`, `@ps.be`…) |
| Modes | Full initial → delta récurrent |
| Offres absentes CRM | **Ignore** (pas de sync sortie) |
| Erreurs non critiques | Skip + continuer |
| Lock BO | **Log only par défaut**, stratégie configurable BO |
| Déclenchement | **Cron + Drush + Queue** (tous requis) |
| Rollback | **Par run/lot** — pas `migrate:rollback` en prod |
| SLA | ≤ **1 h** / lot |
| Alerting | Logs + **email** |
| Config migrations | **Export CMI** (`src/config/sync/`) |
| Trajectoire | Optimiser Migrate existant |

---

## 1. Vision cible

### 1.1 Objectifs

1. Rendre la pipeline **exploitable en prod** (SLA 1 h, alerting, traçabilité run).
2. Centraliser la **gouvernance métier dans le BO** (fréquence, criticité, lock, alertes).
3. Supprimer les **goulots performance** identifiés (re-parsing XML, timeout HTTP).
4. Aligner la **config Migrate** sur le workflow CMI du projet.
5. Préparer un **rollback par run** sans dépendre de `migrate:rollback`.

### 1.2 Architecture cible

```
                    ┌─────────────────────────────────────┐
                    │  SOURCES                             │
                    │  SFTP → private://crm/incoming/      │
                    │  BO upload → incoming/               │
                    └──────────────┬──────────────────────┘
                                   │
         ┌─────────────────────────┼─────────────────────────┐
         │                         │                         │
         ▼                         ▼                         ▼
   Drush ps:import:run      Cron (config BO)         Queue worker
   (immédiat / ops)         (polling incoming)       (1 file = 1 job)
         │                         │                         │
         └─────────────────────────┴─────────────────────────┘
                                   │
                                   ▼
                    ┌─────────────────────────────────────┐
                    │  ImportPipeline (inchangé concept.)  │
                    │  incoming → processing → staging     │
                    │  → migrations → archive/failed       │
                    │  → import_run + snapshot run         │
                    └──────────────┬──────────────────────┘
                                   │
                    ┌──────────────┴──────────────┐
                    ▼                             ▼
         XmlParseCache (1 parse/run)     10 migrations ordonnées
         (service partagé)               (full ou delta)
                    │                             │
                    └──────────────┬──────────────┘
                                   ▼
                    ┌─────────────────────────────────────┐
                    │  Post-run                            │
                    │  • Solr index (option BO)            │
                    │  • Email si échec / seuil skip       │
                    │  • KPI sur import_run                │
                    └─────────────────────────────────────┘
```

### 1.3 Hors périmètre immédiat

- Module `ps_migrate_pipeline` (staging DB one-pass).
- API CRM directe (XML reste la norme — voir §9.5).
- Unpublish offres absentes du CRM.
- `migrate:rollback` en production.

---

## 2. Évolutions BO Settings

### 2.1 État actuel

Formulaire `/admin/ps/import/settings` (`ImportPipelineSettingsForm`) :

- Chemins pipeline, staging URI, mode default, batch limit, upload max, cron on/off + intervalle.

### 2.2 Nouvelle config : `ps_migrate.import_pipeline_settings` (extension)

| Clé | Type | Défaut | Description |
|-----|------|--------|-------------|
| **Existant — enrichi** | | | |
| `cron_enabled` | bool | `false` | Polling incoming |
| `cron_interval` | int | `900` | Secondes entre runs cron |
| `mode` | string | `full` | Mode default (full/delta) |
| `batch_limit` | int | `0` | Fichiers max / run (0 = illimité) |
| **Nouveau — déclenchement** | | | |
| `queue_enabled` | bool | `true` | Dépôt incoming → queue automatique |
| `queue_process_on_cron` | bool | `true` | Cron consomme la queue |
| `queue_items_per_cron` | int | `1` | Jobs traités par passage cron |
| **Nouveau — post-run** | | | |
| `post_run_index_solr` | bool | `true` | Index Solr offres après succès |
| `post_run_cache_rebuild` | bool | `false` | `drush cr` après import |
| **Nouveau — alerting** | | | |
| `alert_email_enabled` | bool | `true` | Email sur échec run |
| `alert_email_recipients` | string | `''` | Liste emails (CSV) |
| `alert_email_on_warning` | bool | `false` | Email si taux skip > seuil |
| `alert_skip_threshold_percent` | int | `10` | Seuil skip (%) pour warning email |
| **Nouveau — protection lock** | | | |
| `lock_strategy_default` | string | `log_only` | `log_only` \| `skip_row` \| `skip_field` |
| `lock_field_strategies` | mapping | `{}` | Override par champ |
| **Nouveau — criticité domaines** | | | |
| `domain_criticality` | mapping | voir §2.3 | Comportement par domaine si erreur |
| **Nouveau — rejouabilité** | | | |
| `skip_unchanged_offers` | bool | `false` | Skip si checksum source identique (delta) |
| `offer_checksum_fields` | sequence | liste champs | Champs inclus dans checksum offre |

### 2.3 Criticité par domaine (configurable BO)

| Domaine | Critique par défaut | Comportement si erreur | Validé questionnaire |
|---------|---------------------|------------------------|----------------------|
| `offers` | Oui | Fail migration offre (ligne) | Oui |
| `budget` | Oui | Fail ligne offre | Oui |
| `geo` | Oui | Fail ligne offre | Oui |
| `features` | Oui | Skip item + log (non-bloquant offre) | Oui |
| `media` | Non | Skip média + continuer | skip_continue |
| `agents` | Non | Skip agent absent + continuer | skip_continue |
| `virtual_tour` | Non | Skip + continuer | skip_continue |
| `surface_divisions` | Non | Skip division + continuer | skip_continue |
| `feature_catalog` | Non | Skip définition + log | skip_continue |

Comportements possibles (enum admin) :

- `skip_continue` — ignorer et continuer
- `fail_row` — ligne en erreur Migrate
- `fail_migration` — arrêter la migration
- `fail_run` — arrêter tout le fichier

### 2.4 Sections UI cible

```
/admin/ps/import/settings
├── Pipeline folders (existant)
├── Execution (mode, batch, queue, post-run Solr)
├── Scheduling (Cron + queue items per tick)
├── Alerts (email, seuils)
├── Data governance (lock, criticality, checksum)
└── Advanced (staging URI, max upload)
```

**Permission** : `manage ps_migrate import pipeline`.

### 2.5 Service : `ImportPipelineSettingsResolver`

Responsabilité unique : lire la config et exposer des DTO typés aux subscribers, queue worker et process plugins.

---

## 3. Queue worker

### 3.1 Problème adressé

- Timeout PHP **120 s** en dev Docker (`docker/php/zz-performance.ini`).
- Import synchrone bloque Drush et upload BO « Process immediately ».
- Besoin validé : **Queue en plus de Cron et Drush**.

### 3.2 Design

**Queue ID** : `ps_migrate.import_file`  
**Plugin** : `ImportFileQueueWorker`

**Payload item** :

```yaml
filename: string
source_uri: string
import_mode: full|delta
import_run_id: int|null
enqueued_at: int
enqueued_by: uid
checksum: string
```

### 3.3 Flux

1. Dépôt fichier → `incoming/`
2. Si `queue_enabled` → enqueue (dedup par checksum)
3. Consommation : cron, Drush, `queue:run`
4. Worker : lock applicatif → `processFile()` → release → email si failed

### 3.4 Commandes Drush (extension)

| Commande | Rôle |
|----------|------|
| `ps:import:run` | `--sync` direct ; défaut prod = enqueue |
| `ps:import:queue-status` | Jobs pending/processing/failed |
| `ps:import:queue-process` | Force N jobs |
| `ps:import:recover-stale` | Reprend fichiers bloqués en processing/ |

### 3.5 Runtime worker — décision validée (2025-06-25)

| Environnement | Runtime | Rôle |
|---------------|---------|------|
| **Dev (WSL)** | Drush CLI **hôte WSL** | Worker : `vendor/bin/drush @ps.{code} ps:import:queue-process` |
| **Prod** | **Kubernetes CronJob / Job** | Worker import (PHP CLI 1 Go, deadline 3900 s) |
| **Enqueue prod** | CronJob léger **ou** cron Drupal (enqueue only) | Scan `incoming/` → queue — **sans** exécuter l'import complet |
| **Exclu** | Cron Drupal web / PHP-FPM | Timeout incompatible SLA 1 h |

**Hébergement prod** : Kubernetes (validé).

**Réactivité delta** : fichier SFTP traité en **≤ 15 min** → CronJob consommateur toutes les **5 min** (marge vs objectif 15 min).

**Orchestration multisite (9 pays)** — mode **mixte** (validé) :

| Mode | Fenêtre | Orchestration K8s |
|------|---------|-------------------|
| **Delta** (quotidien) | Journée | **1 CronJob** `ps-import-delta` : boucle séquentielle `@ps.fr` → `@ps.be` → … (`--count=1` / pays) |
| **Full** (hebdo) | Nuit | **9 Jobs parallèles** (ou CronJob `parallelism: 9`) — 1 Job par `@ps.{code}`, ressources isolées |

**Garde-fous** :

- Lock applicatif Drupal par site (`state: ps_migrate.import.lock`) — empêche double run même si CronJob overlap.
- `activeDeadlineSeconds: 3900` (65 min) par Job.
- `resources.limits.memory: 1Gi` ; `requests.memory: 512Mi`.
- Pas de `migrate:rollback` en prod ; rollback = `ps:import:rollback --run-id` (Phase 4).

**Schéma prod cible** :

```
SFTP / BO → incoming/ (@ps.fr)
       ↓
CronJob enqueue (*/5 * * * *) — scan incoming, enqueue only — ~30s
       ↓
Queue ps_migrate.import_file
       ↓
CronJob worker-delta (*/5 * * * *) — jour — séquentiel 9 sites
       OR
CronJob worker-full (0 2 * * 0) — dimanche 02:00 — 9 Jobs parallèles
       ↓
import_run + archive/failed + email alert
```

**Dev vs prod** : en dev, pas de K8s — équivalence via Drush hôte + cron WSL optionnel ; même queue, même lock, même commandes.

### 3.6 Manifestes K8s (livrables Phase 1)

Fichiers cibles (repo ops / chart Helm — hors `src/` deployable) :

- `cronjob-import-enqueue.yaml` — léger, fréquent
- `cronjob-import-worker-delta.yaml` — consomme queue, séquentiel multisite
- `cronjob-import-worker-full.yaml` — déclenche 9 Jobs parallèles
- `configmap-import-script.sh` — script shell bootstrap Drupal + drush

Variables par pays : `DRUSH_ALIAS=@ps.fr`, secrets DB/files via montages existants PS.

---

## 4. Plan export CMI des migrations

### 4.1 Stratégie

| Emplacement | Rôle |
|-------------|------|
| `src/config/sync/migrate_plus.migration.*.yml` | **Source de vérité** — `drush cim` |
| `config/install/` | Bootstrap greenfield — synchronisé avec sync |

### 4.2 Fichiers à exporter

- `migrate_plus.migration_group.ps_crm_import.yml`
- Les 10 migrations `ps_*_from_xml`
- `ps_migrate.import_pipeline_settings.yml` (+ extension §2.2)

Ne pas exporter : fichiers `config/optional/language/*/migrate_plus.*` (labels i18n).

### 4.3 Workflow développeur

1. Modifier YAML
2. `drush @ps.fr migrate:status`
3. `make drush-cex`
4. PR + `drush cim` en INT/staging

### 4.4 Config Split

Évaluer split local pour `paths.*` et `alert_email_recipients` par environnement.

### 4.5 Corrections lors de l'export

- Ajouter `migration_group: ps_crm_import` sur `ps_surface_division_from_xml`
- Centraliser `shared_configuration` dans le groupe
- Lock strategy via config (pas en dur dans YAML)

---

## 5. Optimisation parsing XML

### 5.1 Problème

Un import **full** reparse `public://crm/offers.xml` **~12–15 fois** (migrations + loaders + POST_IMPORT).

### 5.2 Solution : `XmlParseCacheService`

**Service** : `ps_migrate.xml_parse_cache`  
**Cycle de vie** : scoped **par run pipeline**.

```
processFile() début → beginRun(stagingUri)
Migrations/loaders → cache->getOffers(), indexes
processFile() fin → clearRun()
```

### 5.3 Phases

- **Phase A** : `CachedFileFetcher` + loader features via cache (sans changer YAML)
- **Phase B** : source plugin unifié `ps_crm_offer_xml` avec modes

### 5.4 Médias HTTP

Config BO : timeout, retry, max failures. Wrapper `file_copy` avec retry ×2.

---

## 6. Rollback par run

### 6.1 Extension `import_run`

Champs : `source_checksum`, `snapshot` (JSON), `rollback_status`, `duration_ms`.

### 6.2 Snapshot JSON

Enregistre offres created/updated, feature definitions deactivated, stats migrations.

### 6.3 Commande

`drush ps:import:rollback --run-id=42` — unpublish created, restore revision updated si possible, garde-fou si run plus récent.

### 6.4 Limites

Pas de rollback fichiers/médias. Best effort documenté ops.

---

## 7. Protection lock & checksum offres

### 7.1 Corrections

- `EntityProtectionSubscriber` : champs `field_*` nodes + `nid`
- `EntityProtectionManager` : `field_internal_lock` sur nodes
- Calcul `field_source_checksum` dans migration offre

### 7.2 Stratégies lock (config BO)

| Stratégie | Comportement |
|-----------|--------------|
| `log_only` (défaut) | CRM écrase ; warning si lock |
| `skip_row` | Skip migration row |
| `skip_field` | Merge partiel par champ |

### 7.3 Rejouabilité

`skip_unchanged_offers` (delta) : compare checksum source vs `field_source_checksum`.

---

## 8. Observabilité & KPI BO

### 8.1 Stats `import_run` enrichies

`duration_ms`, compteurs imported/updated/failed/skipped/skipped_unchanged, domaines (media, features), `sla_breached`.

### 8.2 Dashboard

Durée, SLA 1 h, compteurs, lien archive, rollback run, process queue.

### 8.3 Email

Service `ImportPipelineAlertNotifier` — sujet `[PS Import][@ps.fr] FAILED — filename`.

---

## 9. Hypothèses opérationnelles — validation

> Questionnaire validé le 2025-06-25. Les entrées **⬜** restent ouvertes.

### 9.1 Volumes

| Indicateur | Hypothèse initiale | **Validé** | Écart |
|------------|-------------------|------------|-------|
| Dev / sample | ~300–500 offres, XML ~5–20 Mo | *(inchangé — dev)* | — |
| Prod typique / pays | 3 000–8 000 offres | **5 000 – 10 000 offres** | Volume actuel plus élevé que l'hypothèse basse |
| Cible +1 an / pays | 10 000–15 000 | **5 000 – 15 000** (×2–×3) | Fourchette confirmée |
| Taille XML / pays | 50–150 Mo | **100 – 200 Mo** | Bande haute — impact parse cache + mémoire |
| Médias / offre | 5–15 | **5 – 15** | Confirmé |
| Fréquence / pays | 1–4 delta + 1 full/sem. | **1 delta/jour + 1 full/semaine** | Confirmé |

**Impact conception** : avec 5–10k offres et XML 100–200 Mo, le **parse cache (§5)** et la **queue CLI (§3)** passent en **P0** — le SLA 1 h est serré sans optimisation médias HTTP.

### 9.2 Surveillance

| Rôle | **Validé** |
|------|------------|
| Responsabilité | **Partagée** Ops + métier PS + dev/support |
| Ops / Run | Échecs pipeline, SLA, relance queue |
| Admin métier PS | Upload XML, validation post-import |
| Dev / Support | Régression migrations, rollback run |
| Automatisation CRM | Dépôt SFTP scheduled |

**Impact** : emails alertes à **destinataires multiples** (config BO CSV) ; dashboard runs accessible aux 3 profils (permissions existantes + doc runbook).

### 9.3 KPI & seuils alerte

| KPI | Seuil alerte | Statut |
|-----|--------------|--------|
| Durée totale | > 45 min warning, > 60 min critical | Proposé — à confirmer ops |
| Taux skip global | > 10% | Proposé |
| Médias download failed | > 5% | Proposé |
| Queue depth | > 5 pending > 2h | Proposé |

### 9.4 Infrastructure

| Contrainte | Dev | Prod | Statut |
|------------|-----|------|--------|
| memory_limit | 512M | **1 Go** | Validé |
| max_execution_time | 120s web | 0 / 3600s CLI | Proposé (découlant queue CLI) |
| Runtime worker | Conteneur ps_php | **Kubernetes CronJob / Job** | ✅ Validé 2025-06-25 |
| Orchestration 9 pays | — | **Mixte** : delta séquentiel jour, full parallèle nuit | ✅ Validé |
| Réactivité delta | — | **≤ 15 min** post-SFTP (CronJob worker */5) | ✅ Validé |
| Concurrence | — | 1 lock global / site ; full nuit = 9 Jobs parallèles | Proposé |

**Action ouverte** : décision infra worker avant Phase 1 queue (§3.5).

### 9.5 Rejouabilité & API

| Sujet | **Validé** |
|-------|------------|
| Skip offres inchangées (checksum) | **Oui en mode delta uniquement** — activable BO, off en full |
| API CRM | **XML fichier seul — horizon 18+ mois** ; pas de connecteur API à planifier à court terme |

---

## 10. Plan de livraison

### Phase 1 — Fondations ops (2–3 sprints)

- Export CMI migrations + settings
- Stats import_run + durée SLA
- Email alert on failure
- Queue worker + lock + Drush commands
- Fix EntityProtectionSubscriber

### Phase 2 — Gouvernance BO (1–2 sprints)

- Settings form étendu
- Lock strategy configurable
- Dashboard runs enrichi
- Post-run Solr index

### Phase 3 — Performance (1–2 sprints)

- XmlParseCacheService Phase A
- Media download retry
- skip_unchanged_offers

### Phase 4 — Rollback run (1 sprint)

- Snapshot subscriber
- `ps:import:rollback`
- UI rollback

### Phase 5 — Consolidation

- Source plugin unifié (Phase B)
- Tests Kernel pipeline
- Runbook `docs/IMPORT_OPS.md`

---

## 11. Risques & mitigations

| Risque | Mitigation |
|--------|------------|
| Queue + cron web timeout | Worker CLI only |
| Snapshot rollback incomplet | Best effort + UI warnings |
| CMI casse map tables | Runbook + backup |
| Mémoire 512M insuffisante | 1G prod + parse cache |
| Hypothèses volumes incorrectes | Ajuster après 1er import prod |

---

## 12. Critères de done

1. ✅ Questionnaire stratégique validé
2. ✅ Hypothèses §9 volumes/surveillance/rejouabilité/API confirmées (2025-06-25)
3. ⬜ Priorisation phases confirmée PO/tech lead
4. ⬜ Emails ops + rôles surveillance nommés (liste destinataires)
5. ✅ Runtime worker prod : **Kubernetes CronJob/Job** (2025-06-25)
6. ⬜ Seuils KPI alerte confirmés par ops
7. ⬜ Manifestes K8s (chart / repo ops) — livrable Phase 1

---

## Références

- [README ps_migrate](../README.md)
- [Audit architecture Migrate](./FEATURES_MIGRATION_STRATEGY.md)
- [Spec pipeline future](./PIPELINE_STAGING_ONE_PASS_SPEC.md)
- [AGENTS.md](../../../../../AGENTS.md)
