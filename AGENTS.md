# PS Project — Property Search (BNPPRE)

> Contexte agent pour Cursor, Codex, Claude Code et autres outils IA.
> Stack : Drupal 11.3+ / PHP 8.3 / PostgreSQL 17 / Solr 9 / Docker Compose / WSL.

## Identité projet

**Property Search (PS)** — plateforme immobilière BNPPRE sur Drupal 11.
Développement custom : modules `ps_*`, modules BNP `bnp_*`, thèmes `ps_theme` + `ui_suite_bnp`.

## Stack technique

| Composant | Version / Détail |
|-----------|-----------------|
| CMS | Drupal 11.3+ |
| PHP | 8.3 (conteneur `ps_php`) |
| Base de données | PostgreSQL 17 |
| Search | Solr 9 + Search API + Facets |
| Cache | Memcache |
| Admin theme | Gin |
| Front theme | `ps_theme` (sub-theme `ui_suite_bnp`) |
| Composants | SDC + UI Patterns + Layout Builder |
| Config | CMI (`src/config/sync/`) + Config Split + Config Ignore |
| Local | Docker Compose (`docker/docker-compose.yml`) — services runtime uniquement |
| URL locale | Multisite : `http://com.localhost:8080`, `http://fr.localhost:8083`, etc. |
| Multisite | 9 pays — manifest `scripts/multisite/countries.yml` → `make generate-multisite` |

## Chemins clés

| Élément | Chemin |
|---------|--------|
| Racine Composer | `src/` |
| Docroot Drupal | `src/web/` |
| Modules custom PS | `src/web/modules/custom/ps_*` |
| Modules BNP | `src/web/modules/custom/bnp_*` |
| Thème front | `src/web/themes/custom/ps_theme/` |
| Design system | `src/web/themes/custom/ui_suite_bnp/` |
| Config sync | `src/config/sync/` |
| Config demo | `ps_demo/config/install/` (module `ps_demo`) |
| Scripts CLI | `src/scripts/main.sh` |
| Ops Docker / multisite (dev) | `scripts/docker/`, `scripts/multisite/` |
| Manifest pays (source) | `scripts/multisite/countries.yml` |
| Manifest pays (runtime) | `src/web/sites/countries.yml` (généré) |
| Dossiers Drupal (`site_dir`) | `src/web/sites/{site_dir}/` — ex. `france`, `belgium`, `com` |
| Artefact déployable | `src/` seul (INT/staging/prod) |
| Makefile racine | `Makefile` — Docker + délégation |
| Makefile projet | `src/Makefile` — build, deploy, drush (prod aussi) |
| Règles Cursor | `.cursor/rules/` |

## Workflow hybride WSL

| Action | Environnement |
|--------|--------------|
| Drush | **Hôte WSL** (`make drush-cr`, `cd src && vendor/bin/drush @ps.fr …`) |
| PHP web (HTTP) | Conteneur `ps_php` via nginx |
| Composer install/update | Hôte WSL (`make composer-install`) |
| npm/gulp | Hôte WSL (`cd src && npm run gulp-prod`) |
| Docker lifecycle | Hôte (`make up/down/restart`) |

`src/.env` (dev) : `DB_HOST=127.0.0.1` pour Drush hôte ; le conteneur PHP reçoit `DB_HOST=postgres` via docker-compose.

### Multisite — codes vs dossiers

| Concept | Exemple | Usage |
|---------|---------|--------|
| **Code pays** | `fr`, `be`, `com` | CLI (`make install fr`), Drush `@ps.fr`, env `DB_NAME_FR` |
| **`site_dir`** | `france`, `belgium`, `com` | Dossier `web/sites/`, `private/`, chemins fichiers |

Chemins fichiers (variables globales optionnelles — voir `docs/MULTISITE_OPS.md`) :

| Variable vide | Défaut dev |
|---------------|------------|
| `APP_PUBLIC_PATH` | `sites/{site_dir}/files` |
| `APP_PRIVATE_PATH` | `src/private/{site_dir}` (mkdir en dev seulement) |
| `APP_ASSETS_PATH` | agrégats Drupal dans le public files |
| `APP_TEMP_PATH` | temp système Drupal |

`APP_PRIVATE_PATH` monté en prod : ne jamais `mkdir` — le chemin doit exister.

## Commandes essentielles

```bash
make bootstrap             # env + up + generate-multisite + build (dev)
make up                    # Démarrer Docker
make generate-multisite    # Sync manifest → src/
make verify-multisite      # Valider variables pays
make drush-cr              # Cache rebuild (hôte)
make drush-uli             # Login admin
make drush fr uli          # Login pays FR
make composer-install      # Dépendances PHP
make deploy                # Workflow déploiement
make index-solr            # Index Solr
bash src/scripts/main.sh tools build   # Build complet
```

## Modules métier

| Module | Rôle |
|--------|------|
| `ps_core` | Foundation, RBAC, hub admin `/admin/ps` |
| `ps_dictionary` | Référentiels métier |
| `ps_offer` | Type de contenu offre immobilière |
| `ps_feature` | Caractéristiques configurables |
| `ps_agent` | Agents immobiliers |
| `ps_search` | Search API + Solr + Facets |
| `ps_migrate` | Import CRM/XML |
| `ps_form` | Formulaires custom |
| `ps_media` | Médias |
| `ps_favorite` | Favoris |
| `ps_homepage` | Homepage LB orchestration, Section Library, §1 hero, shell header/footer — voir `docs/HOMEPAGE_ARCHITECTURE.md` |
| `ps_demo` | Contenu démo |

## Conventions code

- Indentation : 2 espaces
- PHPCS : `Drupal,DrupalPractice` via `src/vendor/bin/phpcs`
- Ne pas modifier `core/`, `contrib/`, `vendor/`
- DI obligatoire — pas de `\Drupal::` dans les classes
- Config avant code — Core avant Contrib avant Custom
- SDC avant Twig custom — Layout Builder avant PHP

## Tests et validation navigateur

**Règle : développer ne suffit pas** — toute modification doit être validée dans le navigateur.

| Méthode | Commande / action |
|---------|-------------------|
| Navigateur Cursor (agents) | MCP → site impacté (ex. `http://fr.localhost:8083`) |
| Behat | `cd src && composer test:behat` |
| E2E offer | `cd src && composer test:offer-ref-e2e` |
| E2E shell modules | `src/scripts/e2e/common.sh` — Drush hôte `@ps.com`, URL `http://com.localhost:8080` |
| Login admin | `make drush-uli` ou `make drush fr uli` |

Prérequis : `make up`, `make drush-cr`. Si front modifié : `cd src && npm run gulp-prod`.

Voir `.cursor/rules/browser-validation.mdc` pour la checklist par type de changement.

## Règles Cursor

Voir `.cursor/rules/` :
- `ps-project.mdc` — contexte projet (always apply)
- `browser-validation.mdc` — **validation navigateur obligatoire** (always apply)
- `docker.mdc` — commandes conteneurs (always apply)
- `drupal.mdc`, `php.mdc`, `postgres.mdc`, `drush.mdc`, `drupal-hooks.mdc`
- `config-management.mdc`, `sdc.mdc`, `layout-builder.mdc`, `frontend.mdc`, `git.mdc`

## Documentation interne

- `docs/MULTISITE_OPS.md` — multisite 9 pays, ports, chemins fichiers
- `docs/PROJECT_CONTEXT.md` — architecture détaillée
- `docs/INSTALL_CONFIG.md` — install greenfield vs deploy CMI
- `docs/DRUPAL_ARCHITECTURE.md` — standards Drupal
- `docs/DEVELOPMENT_WORKFLOW.md` — workflow quotidien
- `src/web/themes/custom/ps_theme/docs/ARCHITECTURE.md` — front

## Skills Drupal (installés)

35 skills [edutrul/drupal-ai](https://github.com/edutrul/drupal-ai) installés dans `.claude/skills/`,
symlinkés vers `.cursor/rules/skills/*.mdc` pour Cursor.

Skills prioritaires PS : `drupal-hooks`, `drupal-services`, `drupal-config`, `drupal-entity-api`,
`drupal-search-api`, `drupal-migrations`, `drupal-plugins`, `docker-local`.

Mettre à jour : recloner ou `npx skills add https://github.com/edutrul/drupal-ai --skill <name>`

## Hooks — OOP obligatoire

Tous les hooks dans `src/Hook/` avec `#[Hook]` — voir `.cursor/rules/drupal-hooks.mdc`.
Ne jamais ajouter de fonctions hook dans `.module`.
