# Demo configuration (partial CMI)

YAML importés via `drush config:import --partial --source=../config/demo` après
l'activation de `ps_demo` (contenu menu + homepage LB déjà importés).

## Contenu

| Fichier | Rôle |
|---------|------|
| `advanced_mega_menu.settings.yml` | Menu principal activé, assets contrib désactivés |
| `advanced_mega_menu.megamenu_content.*.yml` | Panneaux mega-menu (4 entrées) |
| `block.block.ps_theme_mega_menu_tools.yml` | Bloc outils colonne « Find a property » |
| `menu_link_attributes.config.yml` | Labels attributs menu header (override config contrib) |
| `language.negotiation.yml` | Préfixes URL `/` (EN) et `/fr` (FR) |
| `language.types.yml` | Types de langue (interface, contenu, URL) |
| `language.content_settings.node.page.yml` | Traduction contenu pages |
| `language.content_settings.menu_link_content.menu_link_content.yml` | Traduction liens menu |

## Workflow contributeur

1. Modifier le contenu via le BO (menus, homepage LB, traductions FR).
2. Exporter le contenu : `ps_demo/export/content/` (core `content:export`).
3. Exporter la config démo modifiée :

```bash
cd src
vendor/bin/drush config:export --destination=/tmp/demo_export
# Copier les fichiers concernés vers config/demo/
```

4. Textes des blocs homepage LB (hero, univers, éditorial) : `ps_demo/config/install/ps_demo.homepage.yml`
5. Valider : `make reinstall`

## Contenu vs config

- **Contenu modifiable** (placeholders contributeur) : `ps_demo/export/content/`
- **Structure démo** (mega-menu, langues, placements) : `config/demo/`
- **Copy homepage blocs** (EN/FR, chemins médias) : `ps_demo.homepage` config
