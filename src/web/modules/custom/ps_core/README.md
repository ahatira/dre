# Module `ps_core`

> Statut : 🟢 Stable

Socle transversal de l'écosystème Property Search : hub d'administration, RBAC, services communs et points d'extension pour les modules PS.

## Responsabilité

`ps_core` fournit l'infrastructure commune à tous les modules `ps_*` : le hub d'administration `/admin/ps`, le système de permissions sectorielles, et des services utilitaires (mapping de champs, résolution de config, protection d'entités, gestion de permissions).

Les **rôles Drupal** (personas BNP) sont gérés par `bnp_admin` — voir `bnp_admin/docs/RBAC.md`. `ps_core` ne livre plus de rôles `ps_admin` / `ps_content_editor`.

Ce module **ne gère pas** de contenu (nodes, entités de contenu), de types de champs custom, ni de logique métier spécifique à un domaine. Les nodes restent sous `/admin/content`.

## Fonctionnalités

- Hub admin `/admin/ps` avec 5 sections hub : Contenu, Structure, CRM import, Configuration, Platform health
- 17 permissions granulaires dont 6 sectorielles (hub, content, structure, import, config, health)
- Redirect legacy `/admin/ps/config/settings` → paramètres contact `ps_form` (`/admin/ps/config/forms/contact`)
- Dashboard **Platform health** `/admin/ps/health` (checks plugin, cache 60 s)
- Services utilitaires réutilisables par les modules PS
- Filtrage RBAC des liens de section par permission

## Architecture

### Services

| Service ID | Classe | Rôle |
|---|---|---|
| `logger.channel.ps_core` | *(logger factory)* | Canal de log dédié PS Core |
| `ps_core.conflict_window_provider` | `NullConflictWindowProvider` | Fenêtre conflit import (0 par défaut ; surchargé par `ps_migrate`) |
| `ps_core.entity_protection_manager` | `EntityProtectionManager` | Protection entités, checksums, détection conflits import |
| `ps_core.field_mapper` | `FieldMapper` | Normalisation de valeurs de champ (string, decimal, boolean) |
| `ps_core.config_resolver` | `ConfigResolver` | Accès à la config Drupal avec valeurs par défaut |
| `ps_core.permission_manager` | `PermissionManager` | Vérification de permissions et filtrage de routes |
| `ps_core.health_check_manager` | `HealthCheckManager` | Discovery plugins HealthCheck |
| `ps_core.health_check_collector` | `HealthCheckCollector` | Exécution checks + cache 60 s |
| `ps_core.health_check_overview_builder` | `HealthCheckOverviewBuilder` | Render array dashboard Platform health |

### Controllers

| Classe | Routes desservies | Rôle |
|---|---|---|
| `HealthAdminOverviewController` | `/admin/ps/health` | Dashboard Platform health (cartes par domaine) |
| `GovernanceAdminOverviewController` | `/admin/ps/config/governance` | Hub gouvernance import |
| `LegacySettingsRedirectController` | `/admin/ps/config/settings` | Redirect 301 vers `ps_form.contact_settings` |

### Forms

| Classe | Chemin | Rôle |
|---|---|---|
| `GovernanceGlobalSettingsForm` | `/admin/ps/config/governance/settings` | Gouvernance import globale |

> Paramètres contact (urgence téléphone, hub webforms, mode d'affichage) : module **`ps_form`** — `/admin/ps/config/forms/contact`.

## Routes & Accès

| Route | Chemin | Permission requise |
|---|---|---|
| `ps_core.hub` | `/admin/ps` | `access ps_core hub` |
| `ps_core.content` | `/admin/ps/content` | `access ps_core content section` |
| `ps_core.structure` | `/admin/ps/structure` | `access ps_core structure section` |
| `ps_core.config` | `/admin/ps/config` | `access ps_core config section` |
| `ps_migrate.admin_overview` | `/admin/ps/import` | `access ps_core import section` |
| `ps_core.settings_form` | `/admin/ps/config/settings` | Redirect vers `ps_form.contact_settings` |
| `ps_core.health` | `/admin/ps/health` | `access ps_core health section` |

> **Note architecture** : `/admin/ps`, `/admin/ps/content`, `/admin/ps/structure`, `/admin/ps/config` et `/admin/ps/import` utilisent `SystemController::systemAdminMenuBlockPage` pour un rendu 100 % natif Drupal. La section CRM import est déclarée par `ps_migrate` sous le hub `ps_core.hub`.

## Permissions

| Permission | Description |
|---|---|
| `access ps_core hub` | Accès aux pages du hub Property Search |
| `access ps_core content section` | Accès à la section Contenu (`/admin/ps/content`) |
| `access ps_core structure section` | Accès à la section Structure (`/admin/ps/structure`) |
| `access ps_core config section` | Accès à la section Configuration (`/admin/ps/config`) |
| `access ps_core import section` | Accès à la section CRM import (`/admin/ps/import`) |
| `access ps_core health section` | Accès au dashboard Platform health (`/admin/ps/health`) — `site_admin` uniquement via RBAC |
| `administer ps_core` | Administration complète de ps_core (settings) |
| `manage ps_dictionary` | Gestion du module dictionnaire |
| `manage ps_offer` | Gestion du module offre |
| `manage ps_feature` | Gestion du module feature |
| `manage ps_diagnostic` | Gestion du module diagnostic |
| `manage ps_agent` | Gestion du module agent |
| `manage ps_context` | Gestion du module context |
| `manage ps_migrate` | Gestion du module migrate |

## Rôles et RBAC

Les rôles éditeurs/admin PS sont définis dans **`bnp_admin`** (`content_editor`, `content_admin`, `site_admin`, …).

```bash
make rbac-sync
make create-test-users
```

Référence : `bnp_admin/docs/RBAC.md`.

## Configuration initiale (`config/install/`)

| Fichier | Contenu |
|---|---|
| `ps_core.governance.yml` | Defaults gouvernance import |
| `ps_core.offer_section_settings.yml` | Registre sections offre |

## Tests

| Classe | Type | Scénarios couverts |
|---|---|---|
| `PsCoreHubAccessKernelTest` | Kernel | Routes de section, permissions `content_editor` / `content_admin` (3 tests) |
| `EntityProtectionManagerTest` | Unit | Protection, checksums, fenêtre de conflit |
| `ConfigResolverTest` | Unit | Résolution de config avec valeur par défaut |
| `FieldMapperTest` | Unit | Normalisation string, decimal, boolean |

**Résultat** : 5 classes de tests, 0 dépréciations PHPUnit.

## Dépendances

- `drupal:system` — API système Drupal
- `drupal:user` — Gestion des rôles et permissions
- `drupal:field` — API de champs
- `drupal:views` — Vues Drupal (kernel test)

## Installation

```bash
drush pm:enable ps_core -y
```

Le module installe automatiquement les paramètres par défaut via `config/install/`. Les rôles BNP sont importés séparément via `make rbac-sync`.
