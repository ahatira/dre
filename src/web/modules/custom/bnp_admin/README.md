# BNP Admin

Module Drupal portable qui installe un **baseline d'administration BNP** réutilisable sur n'importe quel projet Drupal 10/11.

## Responsabilité

À l'activation, `bnp_admin` :

1. **Active ses dépendances modules** (Coffee, Masquerade, Config Split, Gin Login, etc.) via `bnp_admin.info.yml`
2. **Installe le thème Gin** (prérequis Composer `drupal/gin`, activé dans `hook_install`)
3. **Importe sa configuration** depuis `config/install/` (rôles BNP + `bnp_admin.settings`)
4. **Applique le branding Gin** via `BnpAdminConfigurator` (chemins d'assets calculés dynamiquement)

Ce module **ne contient aucune configuration métier projet** (pas de `ps_*`, pas de thème front).

## Structure

```
bnp_admin/
├── config/install/           # Config owned by bnp_admin
│   ├── bnp_admin.settings.yml
│   └── user.role.*.yml       # 7 rôles baseline BNP
├── config/schema/
├── images/                   # Logos et favicon BNP
├── recipes/bnp_admin_base/   # Recipe optionnelle (miroir des dépendances)
└── src/BnpAdminConfigurator.php
```

## Configuration livrée

### Module-owned (`bnp_admin.settings`)

Paramètres portables décrivant le baseline (core + Gin). Les chemins logo/favicon sont résolus depuis le chemin du module à l'install — **pas de chemin hardcodé projet**.

### Rôles BNP (`user.role.*`)

| Rôle | Périmètre |
|------|-----------|
| `content_editor` | Édition de base + Coffee + alertes publiées |
| `content_admin` | Administration contenu + menus + trash + alertes |
| `translate_editor` | Traduction contenu/interface |
| `translate_admin` | Gestion des langues et traductions |
| `seo_admin` | Alias URL |
| `site_admin` | Configuration site, users, masquerade, contrib admin |
| `administrator` | Super administrateur (`is_admin: true`) + masquerade super user |

Les permissions ne référencent **que** des modules activés comme dépendances de `bnp_admin`.

### Contrib configuré à l'install

Via `BnpAdminConfigurator` (pas d'écrasement de config projet front) :

- `user.settings` → inscription admin only
- `node.settings` → admin theme sur les formulaires node
- `system.theme` → admin = gin (sans toucher au thème default)
- `gin.settings` → branding BNP
- `gin_login.settings` → logo login BNP

## Installation

```bash
drush en bnp_admin -y
drush cr
```

Dans ce projet PS, l'install est aussi orchestrée par `scripts/drupal/install.sh`.

## Désinstallation

`hook_uninstall()` supprime les 7 rôles BNP créés par le module. Les modules contrib activés restent en place (comportement Drupal standard).

## Tests

```bash
cd src
./vendor/bin/phpunit web/modules/custom/bnp_admin/tests
```

## Frontières

- ❌ Pas de config PS (`ps_admin`, `ps_core`, etc.)
- ❌ Pas de thème front (`ui_suite_bnp`)
- ❌ Pas de content types / vues métier
- ✅ Baseline admin BNP portable cross-projets
