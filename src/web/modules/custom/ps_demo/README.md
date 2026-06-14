# PS Demo — Property Search

Module central pour **exporter**, **versionner** et **réimporter** la base contributeur :
contenu multilingue (default content), copy homepage (config), structure démo (CMI partiel).

## Principes

| Couche | Rôle | Emplacement |
|--------|------|-------------|
| **Structure thème** | Menus vides, blocs en régions (`make install` importe `ps_theme/config/install/`) | `ps_theme/config/install/` |
| **Contenu démo** | Menus Stellar (Login, Contact, mega-menu…), homepage LB, alias, copy | `export/content/` → `make demo` |
| **Copy homepage** | Métadonnées node (titres, alias) | `config/install/ps_demo.homepage.yml` |
| **Paramètres démo** | Page d'accueil, références UUID | `config/install/ps_demo.settings.yml` |
| **Structure démo** | Mega-menu panels, langues, bloc Follow us | `src/config/demo/` → `make demo` |
| **Référence menus** | Schéma YAML pour régénérer les exports | `config/stellar_menus.yml` |

Rien de métier en dur dans le PHP : textes, langues, médias (chemins) et config sont exportables.

## Prérequis

- Drupal **11.3+**
- Thème **`ps_theme`** actif
- Langues **EN** (défaut) + **FR**
- Modules : `ps_homepage`, `content_translation`, `layout_builder`, `advanced_mega_menu`, `social_media_links`

## Structure

```
ps_demo/
├── export/content-structural/   # Référence Login + Contact (doublon partiel de content/)
├── export/content/              # Demo complet (make demo)
│   ├── node/
│   └── menu_link_content/
├── config/stellar_menus.yml     # Référence pour régénérer les exports menu
├── config/install/
│   ├── ps_demo.settings.yml
│   └── ps_demo.homepage.yml
├── scripts/import-structural-content.php
└── src/Service/DemoInstaller.php

src/config/demo/                 # Partial CMI (mega-menu, multilingual)
```

## Installation complète

```bash
make up
bash src/scripts/main.sh tools build
make install              # site + demo + offres sample + Solr + ps_search/ps_seo
# ou, pour repartir de zéro :
make reinstall              # DB reset + install complete
```

Étapes exécutées automatiquement par `make install` / `make reinstall` :

1. **Install Drupal** — modules PS, dictionnaire, thème `ps_theme`
2. **Post-install** (`post-install.sh`) — demo, migrate offres, `ps_compare` / `ps_search` / `ps_seo`, index Solr

Options :

```bash
make post-install                    # compléter un site installé avec --minimal
scripts/main.sh drupal install --minimal   # coquille seule (sans demo/offres/Solr)
scripts/main.sh drupal post-install --skip-offers   # si offres déjà importées
```

Enchaînement manuel (équivalent) :

1. **`make reinstall --minimal`** ou **`make install --minimal`** — coquille vide
2. **`make demo`** — contenu Stellar (menus, homepage) + CMI mega-menu
3. **`make import-sample-xml`** — migrate offres CRM
4. **`make index-solr`** — index Search API / Solr

## Multilingue

- **Contenu** : chaque export `menu_link_content` / `node` inclut une section `translations.fr`
- **URL** : alias `/home` (EN), `/fr/accueil` (FR) via `ps_demo.settings` → `front_paths` (sync à l'import)
- **Traduction** : `language.content_settings.*` dans `config/demo/`
- **Copy blocs** : clés `en` / `fr` dans `ps_demo.homepage.yml` (modifiable au BO via config sync ou ré-export)

## Homepage (node/1, Layout Builder)

Le node homepage est exporté puis **réécrit à l'import** avec **9 sections LB** peuplées (EN+FR) via `HomepageDefaultLayoutBuilder` :

1. Search hero (`ps_homepage_search_hero_block`)
2. Services (`ps_homepage_services_block`)
3. Tools (`ps_homepage_tools_block`)
4. Offers carousel (`ps_homepage_offers_carousel_block`)
5. Search shortcuts (`ps_homepage_search_shortcuts_block`)
6. Expert journey (`ps_homepage_expert_journey_block`)
7. News (`ps_homepage_news_block`)
8. Market studies (`ps_homepage_market_studies_block`)
9. FAQ (`ps_homepage_faq_block`)

UUID stable : `b2000001-0000-4000-8000-000000000001` — doit rester le **premier** node importé (nid 1).

`ps_demo.settings.yml` :

```yaml
front_page: /node/1
homepage_uuid: b2000001-0000-4000-8000-000000000001
```

Les textes des blocs LB sont stockés dans la **configuration de chaque bloc** (onglets EN/FR au BO), plus dans `ps_demo.homepage`.

## Workflow contributeur

### 1. Modifier via le BO

- Homepage : `/node/1/layout` (sections LB, réordonnancement)
- Menus : traductions FR sur chaque lien
- Copy homepage : `/admin/config` → export `ps_demo.homepage` ou édition YAML

### 2. Exporter le contenu

```bash
cd src/web
CONTENT=modules/custom/ps_demo/export/content

# Homepage + LB
php core/scripts/drupal content:export node 1 --with-dependencies --dir="$CONTENT"

# Liens menu
for id in $(../vendor/bin/drush sql:query "SELECT DISTINCT id FROM menu_link_content_data"); do
  php core/scripts/drupal content:export menu_link_content "$id" --dir="$CONTENT"
done

# Alias homepage : mettre à jour ps_demo.settings front_paths (ou exporter la config)
vendor/bin/drush config:get ps_demo.settings front_paths
```

### 3. Exporter la config démo

```bash
cd src
vendor/bin/drush config:export --destination=/tmp/demo_export
# Copier les fichiers pertinents vers config/demo/ et ps_demo/config/install/
```

### 4. Valider

```bash
make reinstall
# Vérifier : /node/1, /fr/node/1, mega-menu, menus traduits
```

## Tester l'import seul

```bash
vendor/bin/drush pm:uninstall ps_demo -y
vendor/bin/drush php:script web/modules/custom/ps_demo/scripts/purge-ps-demo-content.php
vendor/bin/drush pm:install ps_demo -y
vendor/bin/drush config:import --partial --source=../config/demo -y
vendor/bin/drush cr
```

## Notes

- Les UUID exportés doivent rester **stables** entre exports.
- Médias hero : chemins theme dans `ps_demo.homepage` aujourd'hui ; migration vers entités `media` exportées prévue.
- UI Patterns / SDC : composition visuelle à venir ; la structure LB + blocs config-first est en place.
