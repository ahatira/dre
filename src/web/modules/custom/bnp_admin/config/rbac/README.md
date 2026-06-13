# RBAC — permissions complètes (post-install PS)

Ce dossier contient les **7 rôles BNP** avec toutes les permissions Property Search.
Il complète `config/install/` qui ne peut pas référencer des permissions `ps_*` tant que
les modules PS ne sont pas activés.

## Import

Après activation de tous les modules PS requis :

```bash
make rbac-sync
# ou
bash src/scripts/main.sh drupal rbac-sync
```

Équivalent Drush :

```bash
drush config:import --partial \
  --source=modules/custom/bnp_admin/config/rbac -y
drush cr
```

## Export (mise à jour des YAML)

Après modification des permissions via `/admin/people/permissions` :

```bash
make rbac-export
# ou
bash src/scripts/main.sh drupal rbac-sync --export
```

## Rôles couverts

| Fichier | Rôle | Persona |
|---------|------|---------|
| `user.role.administrator.yml` | `administrator` | Super administrateur |
| `user.role.site_admin.yml` | `site_admin` | Administrateur site / technique |
| `user.role.content_admin.yml` | `content_admin` | Responsable contenu PS |
| `user.role.content_editor.yml` | `content_editor` | Éditeur offres |
| `user.role.seo_admin.yml` | `seo_admin` | Référencement |
| `user.role.translate_admin.yml` | `translate_admin` | Admin traduction |
| `user.role.translate_editor.yml` | `translate_editor` | Traducteur |

Voir `docs/RBAC.md` pour la matrice détaillée et les personas de test.
