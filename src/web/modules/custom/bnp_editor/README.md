# BNP Editor

Module Drupal qui centralise la configuration **CKEditor 5** et les **formats de texte standards** pour les projets BNP, avec activation des modules contrib requis.

## Responsabilité

À l'activation, `bnp_editor` :

1. **Active ses dépendances modules** (Linkit, Entity Embed, CKEditor plugins, etc.) via `bnp_editor.info.yml`
2. **Importe les formats et éditeurs** depuis `config/optional/` (quand les dépendances sont satisfaites)
3. **Importe** `bnp_editor.settings` depuis `config/install/`
4. **Accorde les permissions de formats** aux rôles BNP via `BnpEditorRoleInstaller`

Ce module **ne définit pas** de formats custom (`bnp_rich_text` n'existe pas). Il configure les formats Drupal standards : `full_html`, `basic_html`, `restricted_html`, `plain_text`.

## Formats de texte

| Format | Éditeur | Usage typique |
|--------|---------|---------------|
| `full_html` | CKEditor 5 complet | Administrateurs, gestionnaires site |
| `basic_html` | CKEditor 5 standard | Éditeurs de contenu |
| `restricted_html` | Aucun (filtres HTML) | Utilisateurs limités, commentaires |
| `plain_text` | Aucun | Texte brut |

## Permissions par rôle (baseline BNP)

Appliquées à l'install et via `bnp_editor_update_9001()` :

| Rôle | Formats | Admin BNP Editor |
|------|---------|------------------|
| `administrator` | Tous | ✅ |
| `site_admin` | full, basic, restricted | ✅ |
| `content_admin` | full, basic, restricted | — |
| `content_editor` | basic, restricted | — |
| `translate_admin` / `translate_editor` | basic, restricted | — |
| `seo_admin` | restricted | — |
| `authenticated` | restricted | — |

Si `ps_core` est activé :

| Rôle | Formats |
|------|---------|
| `ps_admin` | full, basic, restricted + admin BNP Editor |
| `ps_content_editor` | basic, restricted |

Le rôle Drupal legacy `editor` n'est **pas** utilisé.

## Installation

```bash
drush en bnp_editor -y
drush cr
```

Sur ce projet : activé par `scripts/drupal/install.sh` juste après `bnp_admin` (avant les modules PS).

## Configuration

### Paramètres globaux

`/admin/config/content/bnp-editor` (permission `administer bnp editor`)

- `enable_custom_plugins` — réservé aux extensions via `hook_bnp_editor_plugins_alter()`
- `enable_media_embed` — paramètre documenté (intégration média via config optional)
- `allowed_protocols` — protocoles de liens autorisés (validation via `hook_bnp_editor_config_validate`)

### Structure des configs

```
bnp_editor/
├── config/install/
│   └── bnp_editor.settings.yml      # Paramètres du module
├── config/optional/
│   ├── filter.format.*.yml          # 4 formats standards
│   └── editor.editor.*.yml          # full_html + basic_html (CKEditor 5)
├── config/schema/
│   └── bnp_editor.schema.yml
└── src/
    ├── Form/BnpEditorSettingsForm.php
    └── Service/
        ├── EditorManager.php
        └── BnpEditorRoleInstaller.php
```

Les fichiers `config/optional/` sont importés automatiquement à l'activation du module (sans dépendance circulaire vers `bnp_editor`).

## Dépendances

Toutes les extensions listées dans `bnp_editor.info.yml` sont **requises** à l'activation. Elles doivent être présentes dans le `composer.json` du projet hôte.

Enhancements vraiment optionnels (non requis) : `blazy`, `slick` — signalés dans le status report uniquement.

## Internationalisation

7 fichiers `.po` dans `translations/` (fr, nl, es, it, lb, pl, de).

```bash
drush locale:import fr web/modules/custom/bnp_editor/translations/fr.po -y
drush cr
```

## Tests

```bash
cd src
SIMPLETEST_DB=pgsql://drupal:drupal@postgres:5432/drupal \
  ./vendor/bin/phpunit -c web/core/phpunit.xml.dist web/modules/custom/bnp_editor/tests
```

## Documentation

- [QUICKSTART.md](QUICKSTART.md) — démarrage rapide
- [INSTALL.md](INSTALL.md) — installation détaillée
- [ARCHITECTURE.md](ARCHITECTURE.md) — architecture technique
- [CONTRIB_MODULES.md](CONTRIB_MODULES.md) — modules contrib
- [bnp_editor.api.php](bnp_editor.api.php) — hooks documentés

## Frontières

- ❌ Pas de formats custom propriétaires (standards Drupal uniquement)
- ❌ Pas de plugins CKEditor PHP livrés (exemple JS dans `js/ckeditor5_plugins/`)
- ✅ Portable sur tout projet Drupal 11 avec les mêmes dépendances Composer
- ✅ Compatible avec `bnp_admin` (rôles `content_editor`, `administrator`, etc.)

---

**Version** : 1.0.0  
**Drupal** : 11.x  
**Package** : BNP
