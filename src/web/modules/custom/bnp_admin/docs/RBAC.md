# RBAC Property Search — rôles BNP

## Source de vérité

Les rôles et permissions PS sont gérés par **`bnp_admin`**, pas par `ps_core`.

| Emplacement | Contenu |
|-------------|---------|
| `config/install/user.role.*.yml` | Baseline BNP portable (sans permissions `ps_*`) |
| `config/rbac/user.role.*.yml` | Rôles complets avec permissions PS (post-install) |

Les anciens rôles `ps_admin` et `ps_content_editor` ont été **supprimés** (update `bnp_admin_update_9003` migre les utilisateurs vers les rôles BNP).

## Synchronisation

```bash
make rbac-sync          # Import config/rbac/ après activation modules PS
make rbac-export        # Export permissions modifiées via UI
make create-test-users  # Comptes QA (mot de passe : test ou TEST_USER_PASS)
```

Intégré dans `scripts/drupal/install.sh` (post-install).

## Rôles et personas

| Rôle | Machine name | Persona recette | Périmètre principal |
|------|--------------|-----------------|---------------------|
| Super administrateur | `administrator` | `admin` (install), `admin.test` | Accès total |
| Administrateur site | `site_admin` | `site.admin` | Matrix Context, config site, users |
| Responsable contenu | `content_admin` | `content.admin` | Toute offre, hub PS, **publication UI** |
| Éditeur offres | `content_editor` | `content.editor` | Ses offres, pas matrix, **pas de case Published** |
| Référenceur | `seo_admin` | `seo.admin` | Alias URL |
| Admin traduction | `translate_admin` | `translate.admin` | Langues |
| Traducteur | `translate_editor` | `translate.editor` | Traduction contenu |

Emails test : `{username}@test.ps.local` (voir `create-test-users.sh`).

## Permissions PS clés

| Permission | Rôles typiques |
|------------|----------------|
| `administer ps_context matrix` | `site_admin`, `administrator` |
| `manage ps_offer` | `content_editor`, `content_admin` |
| `edit any offer content` | `content_admin` |
| `edit own offer content` | `content_editor` |
| `access ps_core config section` | `content_admin`, `site_admin` |
| `access ps_core import section` | `site_admin` |

## Scénarios sécurité (SEC)

| ID | Scénario | Compte | Attendu |
|----|----------|--------|---------|
| SEC-01 | Créer offre | `content.editor` | OK |
| SEC-02 | Accès matrix | `content.editor` | 403 |
| SEC-03 | Accès matrix | `site.admin` | OK |
| SEC-04 | Créer offre | `authenticated` seul | Refusé |
| SEC-05 | Éditer offre tierce | `content.admin` | OK |
| SEC-06 | Éditer offre tierce | `content.editor` | Refusé |

```bash
cd src && composer test:rbac-sec-e2e
```

## Publication offre (UI)

- `content_editor` : case « Published » masquée dans le formulaire Gin
- `content_admin` : peut publier manuellement (recette OFF-03-UI)

## Références

- Import/export : [config/rbac/README.md](../config/rbac/README.md)
- Recette scripts : [RECETTE.md](RECETTE.md)
- Permissions hub : `ps_core/README.md`
