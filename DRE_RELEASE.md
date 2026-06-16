# Release initiale vers [ahatira/dre](https://github.com/ahatira/dre.git)

Préparation du **premier install** du site Property Search dans le dépôt `dre` : pas de `hook_update_N`, versions modules et libraries `1.0.0`.

## État actuel vs cible

| Élément | État local | Cible release |
|--------|------------|---------------|
| Structure | `src/` + `docker/` + Makefile | À pousser (remplace l’ancien layout `web/` à la racine) |
| `src/config/sync/` | ~1020 YAML, **non trackés** | **Obligatoire** — `drush cex` puis `git add` |
| `.cursor/`, `.claude/` | ~290 fichiers trackés | **Exclure** du dépôt cible |
| `archived-scripts/` | 84 fichiers trackés | **Exclure** |
| `backup/`, `src/backup/` | untracked | **Exclure** (`.gitignore`) |
| `hook_update_N` | ~70+ fonctions | **Supprimer** — logique dans `hook_install()` |
| `post_update` | 4 (`ps_agent`) | **Supprimer** — idem |
| Versions `*.info.yml` | 17 modules sans `version:` | **`version: 1.0.0`** sur tous les custom |
| `*.libraries.yml` | nombreuses versions ≠ 1.0.0 | **`version: 1.0.0`** (sauf `VERSION` contrib pattern) |

## Workflow de préparation

```bash
# 1. Audit
bash scripts/prepare-dre-push.sh

# 2. Nettoyer l’index git (fichiers dev — ne supprime pas le disque)
git rm -r --cached .cursor .claude archived-scripts 2>/dev/null || true

# 3. Export config à jour
make drush-cr
docker exec -i ps_php sh -lc 'cd /var/www/html && vendor/bin/drush cex -y'

# 4. Vérifier le diff config avant add
git status src/config/sync/

# 5. Remote cible (une fois)
git remote add dre https://github.com/ahatira/dre.git
# ou: git remote set-url dre https://github.com/ahatira/dre.git

# 6. Branche release (recommandée)
git checkout -b release/dre-initial
```

## Fichiers obligatoires pour le dépôt

### Inclure

- `Makefile`, `docker/`, `patches/`
- `src/composer.json`, `src/composer.lock`, `src/package.json`, `src/gulpfile.js`, etc.
- `src/config/sync/` — configuration CMI production
- `ps_demo/config/install/` — démo structure (mega-menu, blocs) via module `ps_demo`
- `src/web/modules/custom/` — tous modules `ps_*`, `bnp_*`, `views_promo_card`
- `src/web/themes/custom/` — `ps_theme`, `ui_suite_bnp`
- `src/scripts/` — CLI install (`main.sh`, `drupal/install.sh`, …)
- `src/web/sites/default/default.settings.php`, `example.settings.php`, `services.yml`
- `AGENTS.md`, `CLAUDE.md` (optionnel — outils IA)

### Exclure (`.gitignore` + index)

- `src/vendor/`, `src/web/core/`, contrib, `node_modules/`
- `src/web/sites/default/settings.php`, `settings.local.php`, `files/`
- `.env`, `.cursor/`, `.claude/`, `backup/`, `archived-scripts/`
- `docs/` (doc interne — optionnel pour dre)
- `archived-scripts/i18n-tooling/` — scripts Python i18n one-shot (hors pipeline install)
- Logs, `data/`, `tmp/`

## Modules avec `hook_update_N` à consolider

Consolider chaque `*_update_*()` dans le `hook_install()` correspondant (ou `config/install/`), puis supprimer les fonctions update.

| Module | Fichier | Updates |
|--------|---------|---------|
| `ps_homepage` | `ps_homepage.install` | 11001–11030 |
| `ps_compare` | `ps_compare.install` | 9001–9012 |
| `ps_market_study` | `ps_market_study.install` | 9001–9006 |
| `ps_offer` | `ps_offer.install` | 9001–9005 |
| `ps_feature` | `ps_feature.install` | 9001–9006 |
| `ps_dictionary` | `ps_dictionary.install` | 11001–11003 |
| `views_promo_card` | `views_promo_card.install` | 11001–11005 |
| `bnp_admin` | `bnp_admin.install` | 9001–9003 |
| `bnp_media` | `bnp_media.install` | 9001–9002 |
| `bnp_editor` | `bnp_editor.install` | 9001 |
| `ps_seo` | `ps_seo.install` | 9001–9004 |
| `ps_form` | `ps_form.install` | 10001–10004 |
| `ps_core` | `ps_core.install` | 9001–9002 |
| `ps_search` | `ps_search.install` | 10001 |
| `ps_news` | `ps_news.install` | 9001–9002 |
| `ps_demo` | `ps_demo.install` | 9001 |
| `ps_agent` | `ps_agent.install` + `post_update.php` | 10001 + 4 post_update |

## Modules sans `version: 1.0.0` dans `*.info.yml`

Ajouter `version: 1.0.0` :

- `views_promo_card`, `ps_homepage`, `ps_diagnostic`, `ps_compare`, `ps_migrate`, `ps_feature`, `ps_block`, `ps_form`, `ps_favorite`, `entity_browser_generic_embed`
- Thèmes : `ui_suite_bnp`, starterkits, `ui_suite_bnp_companion`

## Push vers dre (quand prêt — pas maintenant)

```bash
# Historique dre actuel = ancien codebase (modules ps_* legacy). Push initial = remplacement.
git push dre release/dre-initial:main --force-with-lease
# OU merge après revue : git push dre release/dre-initial
```

**Ne pas** `--force` sans confirmation : le dépôt `dre` contient déjà ~1040 commits.

## Validation post-push

```bash
make up
make install          # ou install --minimal + post-install
make drush-status
# Navigateur : http://localhost:8080
```

## Commande d’audit

```bash
bash scripts/prepare-dre-push.sh
```
