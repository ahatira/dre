# PS Project — Instructions Claude / Cursor Agent

> Fichier de contexte pour agents IA travaillant sur Property Search (BNPPRE).
> Complète les règles `.cursor/rules/` — lire `AGENTS.md` en premier.

## Mission

Développer et maintenir une plateforme immobilière Drupal 11 en respectant strictement :
**Config > Core > Contrib > SDC > Layout Builder > PHP custom**.

## Environnement

- **OS dev** : WSL2 Linux
- **Runtime web** : Docker Compose (nginx + PHP `ps_php`) — services uniquement
- **Drush** : hôte WSL (`src/vendor/bin/drush`, alias `@ps.{code}`)
- **DB** : PostgreSQL 17 — jamais supposer MySQL
- **URL** : multisite — ex. `http://fr.localhost:8083`, `http://com.localhost:8080`
- **Déployable** : `src/` seul en INT/staging/prod ; repo racine = dev (Docker, `scripts/`)

## Multisite

- **9 pays** — manifest source : `scripts/multisite/countries.yml`
- Sync : `make generate-multisite` → `src/web/sites/countries.yml` + `src/drush/sites/ps.site.yml`
- **Code pays** (`fr`, `be`…) : CLI, `@ps.fr`, `DB_NAME_FR`
- **`site_dir`** (`france`, `belgium`, `com`) : dossiers `web/sites/`, `private/`, suffixe chemins fichiers

Chemins fichiers (1 variable globale par type, vide = défaut dev) :

| Variable | Vide → défaut |
|----------|----------------|
| `APP_PUBLIC_PATH` | `sites/{site_dir}/files` |
| `APP_PRIVATE_PATH` | `src/private/{site_dir}` |
| `APP_ASSETS_PATH` | agrégats dans public files |
| `APP_TEMP_PATH` | temp système Drupal |

`APP_PRIVATE_PATH` en prod = montage dédié — **ne pas mkdir**. Détail : `docs/MULTISITE_OPS.md`.

## Commandes — NE PAS improviser

```bash
# ✅ Drush — TOUJOURS sur l'hôte WSL
make drush-cr
make drush-uli
make drush fr cex -y
make drush-cex

# ✅ Composer / npm — hôte WSL
make composer-install
cd src && npm run gulp-prod

# ✅ Docker + multisite (racine repo, dev)
make up / make down / make restart
make env / make verify-multisite
make generate-multisite
make bootstrap          # env + up + generate-multisite + build
```

```bash
# ❌ INTERDIT
docker exec ps_php drush …   # Drush n'est plus dans le conteneur
composer install             # dans le conteneur
drush …                      # sur l'hôte sans passer par src/vendor/bin/drush ou make drush
Modifier core/ ou contrib/
```

## Hiérarchie de décision

Avant d'écrire du code, vérifier dans l'ordre :

1. **Configuration Drupal** exportable (CMI) — champs, displays, blocs, LB
2. **API Core** — Entity API, Plugin API, Form API, Render API
3. **Module contrib** existant — Search API, Facets, Layout Builder, etc.
4. **SDC** — composant thème réutilisable
5. **Layout Builder** — assemblage page
6. **PHP custom** — service, plugin, controller (dernier recours)

## Zones d'édition

| ✅ Éditable | ❌ Interdit |
|-----------|-----------|
| `src/web/modules/custom/ps_*` | `src/web/core/` |
| `src/web/modules/custom/bnp_*` | `src/web/modules/contrib/` |
| `src/web/themes/custom/ps_theme/` | `src/vendor/` |
| `src/web/themes/custom/ui_suite_bnp/` | |
| `src/config/sync/` | |
| `scripts/multisite/countries.yml` (racine repo) | |

## Patterns PHP obligatoires

```php
// DI — jamais \Drupal:: dans une classe
final class MyService {
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}
}

// Hooks D11 — OOP obligatoire (src/Hook/, jamais .module)
#[Hook('form_node_offer_form_alter')]
public function formNodeOfferFormAlter(array &$form, FormStateInterface $form_state): void {}
```

## Config workflow

Après tout changement via UI admin :

```bash
make drush-cex
# ou: cd src && vendor/bin/drush @ps.fr cex -y
git diff src/config/sync/
```

Après modification du manifest pays : `make generate-multisite` puis commit des fichiers générés dans `src/`.

## Frontend

- Composants génériques → `ui_suite_bnp/components/`
- Composants métier PS → `ps_theme/components/`
- Minimal custom : blocs core + menus + LB avant module PHP
- Doc : `src/web/themes/custom/ps_theme/docs/ARCHITECTURE.md`

## Qualité avant livraison

1. `make up` + `make drush-cr` — site opérationnel
2. **Validation navigateur obligatoire** — site impacté (ex. `http://fr.localhost:8083`)
3. Behat / E2E si suite existante (`src/scripts/e2e/common.sh` pour scripts shell)
4. `git diff` — scope minimal, pas de bruit
5. PHPCS sur fichiers PHP modifiés
6. Config exportée si changements UI admin
7. Commit atomique avec message clair (si demandé) — **jamais sans étape 2**

## Anti-patterns fréquents des LLM

- Générer des hooks procéduraux `.module` — **interdit**, toujours `src/Hook/` + `#[Hook]`
- SQL MySQL-specific (backticks, GROUP_CONCAT, LIMIT x,y)
- `\Drupal::service()` dans controllers/forms/plugins
- Créer un nouveau module au lieu d'étendre un module existant
- Twig/page template monolithique au lieu de SDC + LB
- Oublier d'exporter la config après changements admin
- Oublier `make generate-multisite` après edit de `scripts/multisite/countries.yml`
- Confondre code pays (`fr`) et `site_dir` (`france`) pour les chemins fichiers
- Exécuter Drush via `docker exec` — utiliser `make drush` ou `vendor/bin/drush` sur l'hôte
- `mkdir` sur `APP_PRIVATE_PATH` monté en prod
- **Marquer une tâche terminée sans test navigateur** sur le site impacté

## Références

- `AGENTS.md` — contexte complet
- `docs/MULTISITE_OPS.md` — multisite 9 pays
- `docs/PROJECT_CONTEXT.md` — architecture
- `docs/DRUPAL_ARCHITECTURE.md` — standards
- `docs/DEVELOPMENT_WORKFLOW.md` — workflow quotidien
- `.cursor/rules/` — règles spécialisées par domaine
