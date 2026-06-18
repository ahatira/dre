# Recette QA — Formulaire offre (`ps_offer`)

Scénarios de validation du bundle `offer` et scripts automatisés.

## Prérequis

```bash
make up && make drush-cr
make rbac-sync && make create-test-users
```

## Scripts automatisés

| Commande | Périmètre |
|----------|-----------|
| `cd src && composer test:manual-offer-val` | OFF-01→18, VAL-01→10 |
| `cd src && composer test:manual-offer-full` | §5.3 données minimales + publication matrix |
| `cd src && composer test:behat -- --suite=ps_offer_reference` | Références auto/manuel |
| `cd src && composer test:offer-ref-e2e` | E2E références bash |

Scripts sources : `bnp_admin/tests/e2e_manual_offer_val_recette.sh`, `e2e_manual_offer_full_recette.sh`.

## Matrice principale (OFF-01 à OFF-12)

12 combinaisons `client_type × operation_type × asset_type × divisible`.

| ID | Op | Actif | Divisible | Surface | Capacity | Price | Lots |
|----|-----|-------|-----------|---------|----------|-------|------|
| OFF-01 | LOC | BUR | Non | ✅ | ❌ | ✅ | ❌ |
| OFF-02 | LOC | BUR | Oui | ✅ | ❌ | ✅ | ✅ |
| OFF-03 | LOC | COW | — | ❌ | ✅ | ✅ | ❌ |
| OFF-10 | VEN | COW | — | ❌ | ✅ | ✅ | ❌ |

Codes dictionnaire : LOC/VEN (operation), BUR/COW/ENT/ACT/COM/TER (asset). Voir `ps_dictionary/data/dictionary_entries.csv`.

## Cas dynamiques (OFF-14 à OFF-18)

| ID | Scénario | Validation |
|----|----------|------------|
| OFF-14 | État initial vide | Price/Surface/Capacity/Lots masqués |
| OFF-15 | BUR → COW live | Surface masquée, Capacity visible |
| OFF-16 | COW → BUR live | Inverse OFF-15 |
| OFF-17 | Divisible sans op/actif | Lots reste masqué |
| OFF-18 | Édition = create | `/node/{id}/edit` identique à create |

Validation navigateur obligatoire : `http://com.localhost:8080/node/add/offer` avec `content.editor`.

## Validations (VAL-01 à VAL-10)

| ID | Règle | Brouillon | Publication |
|----|-------|-----------|-------------|
| VAL-01 | Surface TOTAL > 0 | Warning | **Blocant** (sauf matrix masque Surface) |
| VAL-02 | Galerie non vide | — | **Blocant** (form validate) |
| VAL-03 | Agent principal | Warning + draft | Dépublication auto |
| VAL-04 | Capacité total > 0 (onglet visible) | Warning | **Blocant** |
| VAL-05 | Capacité available ≤ total | Warning | **Blocant** |
| VAL-06 | PER_POSTE → capacité > 0 | Warning | **Blocant** |
| VAL-07 | Budget 0 → NULL | Silencieux | Silencieux |
| VAL-08 | Non divisible + DISPO < TOTAL | Warning UX | Warning |
| VAL-09 | Référence manuelle dupliquée | **Blocant** | **Blocant** |
| VAL-10 | Self-edit même ref | OK | OK |

Détail implémentation : [VALIDATION.md](VALIDATION.md).

## Context

Scénarios CTX-* : `ps_context/docs/RECETTE.md`.

## Références techniques

| Sujet | Fichier |
|-------|---------|
| Form display | `config/install/core.entity_form_display.node.offer.default.yml` |
| Validations | `src/Service/OfferValidationManager.php` |
| Pont Context | `src/OfferContextResolverInterface.php`, [CONTEXT_INTEGRATION.md](CONTEXT_INTEGRATION.md) |
| RBAC personas | `bnp_admin/docs/RBAC.md` |
