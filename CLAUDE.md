# PS Project — Instructions Claude / Cursor Agent

> Fichier de contexte pour agents IA travaillant sur Property Search (BNPPRE).
> Complète les règles `.cursor/rules/` — lire `AGENTS.md` en premier.

## Mission

Développer et maintenir une plateforme immobilière Drupal 11 en respectant strictement :
**Config > Core > Contrib > SDC > Layout Builder > PHP custom**.

## Environnement

- **OS dev** : WSL2 Linux
- **Runtime** : Docker Compose (conteneur PHP `ps_php`)
- **DB** : PostgreSQL 17 — jamais supposer MySQL
- **URL** : http://localhost:8080

## Commandes — NE PAS improviser

```bash
# ✅ Drush — TOUJOURS dans le conteneur
make drush-cr
make drush-uli
docker exec -i ps_php sh -lc 'cd /var/www/html && vendor/bin/drush cex -y'

# ✅ Composer — hôte WSL (workflow hybride du projet)
make composer-install

# ✅ Frontend
cd src && npm run gulp-prod

# ✅ Docker
make up / make down / make restart
```

```bash
# ❌ INTERDIT
drush cr                    # sur l'hôte WSL
composer install            # dans le conteneur (sauf migration future)
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
docker exec -i ps_php sh -lc 'cd /var/www/html && vendor/bin/drush cex -y'
git diff src/config/sync/
```

## Frontend

- Composants génériques → `ui_suite_bnp/components/`
- Composants métier PS → `ps_theme/components/`
- Minimal custom : blocs core + menus + LB avant module PHP
- Doc : `src/web/themes/custom/ps_theme/docs/ARCHITECTURE.md`

## Qualité avant livraison

1. `make up` + `make drush-cr` — site opérationnel
2. **Validation navigateur obligatoire** — ouvrir `http://localhost:8080`, parcours impacté (MCP navigateur Cursor pour les agents)
3. Behat / E2E si suite existante pour le module modifié
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
- Exécuter drush sur l'hôte
- **Marquer une tâche terminée sans test navigateur** sur http://localhost:8080

## Références

- `AGENTS.md` — contexte complet
- `docs/PROJECT_CONTEXT.md` — architecture
- `docs/DRUPAL_ARCHITECTURE.md` — standards
- `docs/DEVELOPMENT_WORKFLOW.md` — workflow quotidien
- `.cursor/rules/` — règles spécialisées par domaine
