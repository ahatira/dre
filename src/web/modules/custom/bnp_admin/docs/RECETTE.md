# Recette QA — scripts E2E (`bnp_admin`)

Le module `bnp_admin` héberge les **scripts de recette manuelle** cross-modules (offre, Context, RBAC). Ils ne sont pas des tests PHPUnit mais des helpers bash + evaluate PHP pour QA locale.

## Prérequis

```bash
make up && make drush-cr
make rbac-sync
make create-test-users
```

## Commandes Composer

| Script | Fichier | Périmètre |
|--------|---------|-----------|
| `composer test:rbac-sec-e2e` | `tests/e2e_rbac_sec_ctx.sh` | SEC-01→06, CTX-ADM-01/02 |
| `composer test:manual-recette-ctx` | `tests/e2e_manual_recette_ctx.sh` | CTX-FORM, CTX-DEF, CTX-SEARCH, CTX-ADM-03→05 |
| `composer test:manual-offer-val` | `tests/e2e_manual_offer_val_recette.sh` | OFF-01→18, VAL-01→10 |
| `composer test:manual-offer-full` | `tests/e2e_manual_offer_full_recette.sh` | Données minimales §5.3 + publication |

Cibles Makefile : `make rbac-sec-e2e`, `make rbac-sync`, `make create-test-users`.

## Structure

```
tests/
├── e2e_rbac_sec_ctx.sh              # SEC + CTX-ADM-01/02
├── e2e_rbac_sec_ctx.evaluate.php
├── e2e_manual_recette_ctx.sh        # Context
├── e2e_manual_ctx_form.evaluate.php
├── e2e_manual_ctx_adm45.php
├── e2e_manual_offer_val_recette.sh   # Offre + validations
├── e2e_manual_offer_val_recette.evaluate.php
├── e2e_manual_offer_full_recette.sh
├── e2e_manual_offer_full_recette.evaluate.php
└── e2e_manual_offer_off01.php
```

Les scripts utilisent `docker exec ps_php drush` et `curl` sur `http://localhost:8080`.

## Documentation par module

| Domaine | Doc module |
|---------|------------|
| Context CTX-* | `ps_context/docs/RECETTE.md` |
| Offre OFF-* / VAL-* | `ps_offer/docs/RECETTE.md` |
| RBAC SEC-* | [RBAC.md](RBAC.md) |

## Validation navigateur

Les scripts ne remplacent pas la validation navigateur pour les parcours UI (OFF-14→18, CTX-FORM-07, OFF-03-UI). Ouvrir `http://localhost:8080` après `make drush-cr`.
