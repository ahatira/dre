# PS Demo — Property Search

Module central pour **exporter**, **versionner** et **réimporter** la base contributeur :
contenu multilingue (default content), copy homepage (config), structure démo (CMI partiel).

## Principes

| Couche | Rôle | Emplacement |
|--------|------|-------------|
| **Structure thème** | Menus vides, blocs en régions (`make install` importe `ps_theme/config/install/`) | `ps_theme/config/install/` |
| **Contenu démo** | Menus Stellar (Login, Contact, mega-menu…), homepage LB, alias, copy | `content/` → `make demo` |
| **Copy homepage** | Métadonnées node (titres, alias) | `config/install/ps_demo.homepage.yml` |
| **Paramètres démo** | Page d'accueil, références UUID | `config/install/ps_demo.settings.yml` |
| **Structure démo** | Mega-menu panels, langues, bloc Follow us | `src/config/demo/` → `make demo` |
| **Référence menus** | Schéma YAML pour régénérer les exports | `config/stellar_menus.yml` |

Rien de métier en dur dans le PHP : textes, langues, médias (chemins) et config sont exportables.

## Prérequis

- Drupal **11.3+**
- Thème **`ps_theme`** actif
- Langues **EN** (défaut) + **FR**
- Modules : `default_content`, `ps_homepage`, `content_translation`, `layout_builder`, `advanced_mega_menu`, `social_media_links`

## Structure

```
ps_demo/
├── content-structural/          # Référence Login + Contact (doublon partiel de content/)
├── content/                     # Default content YAML (import auto via module contrib)
│   ├── node/
│   ├── menu_link_content/
│   ├── file/                    # (+ binaire à côté du .yml pour les médias)
│   └── media/
├── config/stellar_menus.yml     # Référence pour régénérer les exports menu
├── config/install/
│   ├── ps_demo.settings.yml
│   └── ps_demo.homepage.yml
└── src/Service/
    ├── DemoInstaller.php
    ├── DemoHeroMediaImporter.php
    ├── DemoTranslationSync.php
    └── DemoMenuNormalizer.php

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

## Default content (module contrib)

Import automatique à l’activation de `ps_demo` via le module **`drupal/default_content`** :

- Fichiers : `content/{entity_type}/{uuid}.yml`
- Dépendances résolues (`_meta.depends`), fichiers binaires copiés pour les entités `file`
- Export Drush : `drush default-content:export-references` (`dcer`)

`DemoInstaller` ne réimporte le contenu que si le node homepage est absent. Pour un reset dev complet :

```bash
drush php:script scripts/tools/purge_ps_demo_content.php
drush pm:uninstall ps_demo -y && drush en ps_demo -y
```

Pas de scripts PHP dans le module : import via `default_content` + services `DemoInstaller` / `DemoHeroMediaImporter`.

## Multilingue (dynamique)

1. **Import** : `default_content` charge EN + traductions dans chaque YAML (`translations.fr`, …).
2. **Sync** : `DemoTranslationSync` parcourt les **langues actives du site** (`language_manager`) et crée les traductions manquantes (menus + homepage) en copiant la meilleure source (langue du site → EN → FR ; `lb` → FR).
3. **Alias** : `applyPathAliases()` crée un alias pour chaque langue active — priorité `ps_demo.homepage` `node.path`, puis alias sur le node, puis fallback EN.
4. **Copy blocs LB** : overlays par langue dans `ps_homepage/data/homepage_block_defaults.{lang}.yml` (géré par `ps_homepage`).

Contributeur : ajouter titres/alias dans `config/install/ps_demo.homepage.yml` (clés libres par langcode) ou dans l’export node ; pas de liste de langues en dur dans le PHP.

## Homepage (node/1, Layout Builder)

Le node homepage est exporté puis **réécrit à l'import** avec **9 sections LB S-D** (EN+FR) via `HomepageDefaultLayoutBuilder` → `HomepageSectionLibraryTemplateBuilder` :

1. Hero — `layout_onecol` + `ps_homepage_search_hero_block`
2. Services — shell `ps_homepage_section` + `ps_content_services_grid_block` (body)
3. Tools — shell + `ps_content_outils_accordion_block`
4. Offers — shell + `ps_offer_offers_carousel_block` + footer CTA
5. Search shortcuts — shell + `ps_search_search_shortcuts_block`
6. Expert journey — shell + `ps_content_experts_accompagnement_block`
7. News — shell + `ps_news_news_block` + footer CTA
8. Market studies — shell + `ps_market_study_market_studies_block` + footer CTA
9. FAQ — shell + `ps_faq_faq_block` + footer CTA

Titre / sous-titre / footer « Voir plus » = blocs shell LB (`ps_homepage_section_header_block`, `ps_homepage_section_footer_block`).

UUID stable : `b2000001-0000-4000-8000-000000000001` — doit rester le **premier** node importé (nid 1).

`ps_demo.settings.yml` :

```yaml
front_page: /node/1
homepage_uuid: b2000001-0000-4000-8000-000000000001
```

Les textes des blocs LB sont stockés dans la **configuration de chaque bloc** (traductions EN/FR au BO). `ps_demo.homepage.yml` ne contient que titres et alias du node homepage.

## Workflow contributeur

### 1. Modifier via le BO

- Homepage : `/node/1/layout` (sections LB, réordonnancement)
- Menus : traductions FR sur chaque lien
- Copy homepage (titres/alias) : édition de `ps_demo.homepage.yml` ou export config

### 2. Exporter le contenu

```bash
cd src/web
MODULE=modules/custom/ps_demo
FOLDER="$MODULE/content"

# Homepage + dépendances (menus, files, media…)
vendor/bin/drush dcer node <nid> --folder="$FOLDER"

# Hero image : après dcer, copier le binaire à côté du YAML file/
# (dcer n’inclut pas toujours le PNG — copier depuis ps_theme ou content/file/)

# Liens menu individuels
vendor/bin/drush default-content:export menu_link_content <id> --file="$FOLDER/menu_link_content/<uuid>.yml"
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
vendor/bin/drush php:script scripts/tools/purge_ps_demo_content.php
vendor/bin/drush pm:uninstall ps_demo -y
vendor/bin/drush pm:install ps_demo -y
vendor/bin/drush cr
```

## Notes

- Les UUID exportés doivent rester **stables** entre exports.
- Médias hero : entités `media` + `file` dans `content/`, référencées par UUID dans `homepage_block_defaults.yml`.
- UI Patterns / SDC : composition visuelle à venir ; la structure LB + blocs config-first est en place.
