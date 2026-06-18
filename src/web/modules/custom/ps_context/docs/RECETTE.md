# Recette QA — Context (`ps_context`)

Scénarios de validation du module Context et scripts automatisés associés.

## Prérequis

```bash
make up && make drush-cr
make rbac-sync
make create-test-users
```

Comptes test : voir `bnp_admin/docs/RBAC.md` (mot de passe par défaut `test`).

## Scripts automatisés

| Commande | Périmètre |
|----------|-----------|
| `cd src && composer test:manual-recette-ctx` | CTX-FORM, CTX-DEF, CTX-SEARCH, CTX-ADM-03 à 05 |
| `cd src && composer test:rbac-sec-e2e` | CTX-ADM-01/02, SEC-01 à SEC-06 |
| `cd src && vendor/bin/phpunit web/modules/custom/ps_context/tests/` | Unit/kernel `ContextRuleEvaluator` |

Scripts sources : `bnp_admin/tests/e2e_manual_recette_ctx.sh`, `e2e_rbac_sec_ctx.sh`.

## Scénarios administration (CTX-ADM)

| ID | Scénario | Compte | Résultat attendu |
|----|----------|--------|------------------|
| CTX-ADM-01 | Liste règles `/admin/ps/config/matrix` | `site.admin` | 15 règles, statut actif/inactif |
| CTX-ADM-02 | Accès éditeur | `content.editor` | 403 |
| CTX-ADM-03 | Édition `asset_type_cow` | `site.admin` | Conditions COW + actions hide Surface / show Capacity |
| CTX-ADM-04 | Désactiver `operation_selected_show_budget` | `site.admin` | Onglet Price ne réapparaît plus à LOC/VEN |
| CTX-ADM-05 | Réactiver la règle | `site.admin` | Comportement restauré |

## Scénarios formulaire (CTX-FORM)

| ID | Scénario | Résultat attendu |
|----|----------|------------------|
| CTX-FORM-01 | `/node/add/offer` état initial | Price, Surface, Capacity, Lots masqués |
| CTX-FORM-02 | Choisir ENT | Onglet Surface visible |
| CTX-FORM-03 | Choisir COW | Capacity visible ; Surface + Divisible masqués |
| CTX-FORM-04 | Choisir LOC ou VEN | Onglet Price visible |
| CTX-FORM-05 | BUR + LOC + Divisible | Onglet Lots visible |
| CTX-FORM-06 | BUR → COW sans sauvegarder | Mise à jour live sans rechargement |
| CTX-FORM-07 | Parcours CTX-FORM-01 à 06 | Aucune erreur `console.error` |

Cas alignés OFF-14 à OFF-18 (voir `ps_offer/docs/RECETTE.md`).

## Défauts budget (CTX-DEF)

| ID | Combinaison | Résultat attendu |
|----|-------------|------------------|
| CTX-DEF-01 | LOC + BUR | Période YEAR, unité PER_M2, devise EUR |
| CTX-DEF-02 | LOC + COW | Unité PER_POSTE |
| CTX-DEF-03 | VEN + BUR | Unité GLOBAL, période masquée |
| CTX-DEF-04 | Toute opération | Devise EUR |

## Recherche front (CTX-SEARCH)

| ID | Scénario | URL | Résultat attendu |
|----|----------|-----|------------------|
| CTX-SEARCH-01 | Sans filtre actif | `/find-property` | Surface visible, capacité masquée |
| CTX-SEARCH-02 | Actif COW | Filtre Coworking | Capacité visible, surface masquée |
| CTX-SEARCH-03 | Actif BUR | Filtre Bureau | Surface visible |
| CTX-SEARCH-04 | BUR + LOC | drupalSettings | Libellés « Loyer », €/m²/an |
| CTX-SEARCH-05 | COW + LOC | drupalSettings | €/poste/an |
| CTX-SEARCH-06 | VEN | drupalSettings | « Prix », pas de période |
| CTX-SEARCH-07 | Homepage | `/` | Hero : même logique surface/capacité |
| CTX-SEARCH-08 | Modif matrix + `drush cr` | — | Filtres à jour (test manuel) |

## Validation navigateur

Obligatoire après changement matrix ou JS :

1. `make drush-cr`
2. Ouvrir `http://com.localhost:8080/node/add/offer`
3. Parcourir CTX-FORM-01 à 06 + CTX-SEARCH-01 à 03
