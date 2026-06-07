# PS Demo — Property Search

Module central pour **exporter**, **versionner** et **réimporter** la base contributeur :
contenu multilingue (default content), copy homepage (config), structure démo (CMI partiel).

## Principes

| Couche | Rôle | Emplacement |
|--------|------|-------------|
| **Structure thème** | Menus vides, blocs en régions (`make install` importe `ps_theme/config/install/`) | `ps_theme/config/install/` |
| **Contenu démo** | Menus Stellar (Login, Contact, mega-menu…), homepage LB, alias, copy | `export/content/` → `make demo` |
| **Copy homepage** | Textes EN/FR des blocs LB (hero, univers, éditorial) | `config/install/ps_demo.homepage.yml` |
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
make reinstall          # site + modules + ps_theme (vide)
make demo               # menus, homepage, mega-menu
make import-sample-xml  # offres sample (optionnel)
make index-solr         # index Solr (optionnel)
```

Enchaînement :

1. **`make reinstall`** — Drupal + modules PS + dictionnaire + dépendances thème (`ps_block`, `ps_homepage`, mega-menu) + **`ps_theme`** (blocs/menus vides)
2. **`make demo`** — contenu Stellar (menus, homepage) + normalisation footer (`DemoMenuNormalizer`) + CMI mega-menu / multilingue / bloc Follow us
3. **`make import-sample-xml`** — migrate offres CRM
4. **`make index-solr`** — index Search API / Solr

## Multilingue

- **Contenu** : chaque export `menu_link_content` / `node` inclut une section `translations.fr`
- **URL** : alias `/home` (EN), `/fr/accueil` (FR) via `ps_demo.settings` → `front_paths` (sync à l'import)
- **Traduction** : `language.content_settings.*` dans `config/demo/`
- **Copy blocs** : clés `en` / `fr` dans `ps_demo.homepage.yml` (modifiable au BO via config sync ou ré-export)

## Homepage (node/1, Layout Builder)

Le node homepage est exporté avec 4 sections LB :

1. Search hero (`ps_homepage_search_hero_block`)
2. Business univers (`ps_homepage_business_univers_block`)
3. Featured offers (`ps_homepage_featured_offers_block`)
4. Editorial promo (`ps_homepage_editorial_promo_block`)

UUID stable : `b2000001-0000-4000-8000-000000000001` — doit rester le **premier** node importé (nid 1).

`ps_demo.settings.yml` :

```yaml
front_page: /node/1
homepage_uuid: b2000001-0000-4000-8000-000000000001
```

Les textes affichés par les blocs viennent de **`ps_demo.homepage`** (config), pas du code PHP.

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
