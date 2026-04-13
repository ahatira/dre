# PS Default Content Module (ps_default_content)

Module custom destine a centraliser les contenus exportes avec `default_content`.

## Pourquoi ce module ?

- Fournir un emplacement unique pour les contenus d'initialisation du projet.
- Versionner les contenus applicatifs (YAML) dans Git.
- Permettre une reinstallation de site reproductible avec le meme socle de donnees.

## Structure

- `ps_default_content.info.yml`
  - declaration du module et des dependances
  - liste `default_content` des UUID exportes
- `content/menu_link_content/*.yml`
  - export YAML du menu principal (liens `menu_link_content`)

## Prerequis

- Module contrib `default_content` active
- Module core/contrib de menu actif (`menu_link_content`)
- Drush disponible (`vendor/bin/drush`)

## Commandes utiles (Drush)

Le module `default_content` expose ces commandes:

```bash
drush default-content:export <entity_type> <entity_id> [--file=...]
drush default-content:export-references <entity_type> [entity_id] [--folder=...]
drush default-content:export-module <module_name>
drush default-content:export-module-with-references <module_name>
```

Exemple pour un lien de menu:

```bash
drush default-content:export menu_link_content 34 --file=web/modules/custom/ps_default_content/content/menu_link_content/UUID.yml
```

## Commandes Make ajoutees

Depuis la racine projet:

```bash
make default-content-export ENTITY_TYPE=menu_link_content ENTITY_ID=34
make default-content-export ENTITY_TYPE=menu_link_content ENTITY_ID=34 OUT=web/modules/custom/ps_default_content/content/menu_link_content/xxx.yml

make default-content-export-references ENTITY_TYPE=node ENTITY_ID=12 FOLDER=web/modules/custom/ps_default_content/content

make default-content-export-module MODULE=ps_default_content
make default-content-export-module-references MODULE=ps_default_content

make default-content-export-main-menu
make default-content-export-footer-menus
```

## Initialisation du menu principal

Le script `scripts/export-main-menu-default-content.sh`:

1. Cree ou met a jour l'arborescence demandee dans le menu `main`.
2. Exporte chaque entree avec `default-content:export`.
3. Ecrit les fichiers dans:
   - `web/modules/custom/ps_default_content/content/menu_link_content/`

Commande recommandee:

```bash
make default-content-export-main-menu
```

## Export des menus footer

Le script `scripts/export-footer-menus-default-content.sh`:

1. Lit les liens des menus `footer_business_websites`, `footer_about_bnppre` et `footer`.
2. Exporte chaque lien avec `default-content:export`.
3. Ecrit les fichiers dans:
  - `web/modules/custom/ps_default_content/content/menu_link_content/`

Commande recommandee:

```bash
make default-content-export-footer-menus
```

## Import sur environnement cible

Activation des modules:

```bash
drush en -y default_content ps_default_content
```

Le hook `default_content_modules_installed()` importe automatiquement les YAML du dossier `content/` du module active.

## Notes de maintenance

- Si vous modifiez le menu principal dans l'admin, relancez `make default-content-export-main-menu` puis committez les YAML.
- Si vous modifiez les menus footer dans l'admin, relancez `make default-content-export-footer-menus` puis committez les YAML.
- Garder ce module concentre sur du contenu de reference (menus, contenus de base), pas de logique metier.
