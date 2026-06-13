# Architecture matrix Context — source de vérité

## Principe

La matrice métier offre (visibilité onglets/champs, défauts budget, filtres recherche, validation publication) est **entièrement pilotée** par les règles actives `ps_context_rule`. Aucun hardcode `COW` / `BUR` dans les consommateurs PHP.

Référence métier : `.ai/PROJECT_MATRIX.md` (DEC-0020).

## Couches

```
ps_context_rule (config entity, 15 règles seed)
        ↓
ContextRuleEvaluator          ps-context-offer-form.js
(même algorithme PHP/JS)      (visibilité live formulaire)
        ↓
OfferContextState
        ↓
OfferMatrixContextResolver → OfferContextResolverInterface
        ↓
Consommateurs optionnels (ps_offer, ps_surface, ps_search)
```

## Services clés

| Service | Rôle |
|---------|------|
| `ps_context.rule_evaluator` | Évalue conditions + actions ; produit `OfferContextState` |
| `ps_context.offer_context_resolver` | Implémente `OfferContextResolverInterface` pour `ps_offer` |
| `ps_context.offer_matrix_rules` | Form alter : required/disabled/defaults + attache JS |
| `ps_context.search_filter_visibility` | Filtres recherche dérivés de la matrix |

## OfferContextState

Value object immuable (`src/Value/OfferContextState.php`) :

- `isTabVisible('group_surface' | 'group_capacity' | 'group_budget' | 'group_lots')`
- `isFieldVisible('field_divisible')`
- `isCapacityDriven()` — surface masquée **et** capacité visible

Onglets/champs non référencés par une règle → visibles par défaut.

## Consommateurs

| Module | Classe | Usage matrix |
|--------|--------|--------------|
| `ps_offer` | `OfferValidationManager` | Skip surface si onglet masqué ; capacité requise si onglet visible |
| `ps_offer` | `OfferSurfaceKpiBuilder` | KPI capacité vs surface selon `isCapacityDriven()` |
| `ps_surface` | `SurfaceContextualFormatter`, `SurfaceCompareFormatter`, `SurfaceDivisionTableFormatter` | Rendu FO matrix-driven |
| `ps_search` | `SearchFilterVisibilityResolver` | Visibilité filtres surface/capacité/budget |

Le pont est **optionnel** : si `ps_context` est désactivé, `OfferContextResolverInterface` reste `NULL` et les validateurs conservent le comportement legacy (surface toujours requise).

## Règles seed

Liste canonique : `ContextSeedRules::SEED_IDS` (15 machine names).

- Livrées en `config/install/ps_context.rule.*.yml`
- Suppression : `PsContextRuleDeleteForm` affiche un avertissement pour les règles seed
- Désactivation : préférer désactiver (`status: false`) plutôt que supprimer

## Administration

- BO : `/admin/ps/config/matrix`
- Permission : `administer ps_context matrix` (rôles `site_admin`, `administrator`)
- Après modification : `make drush-cr`

## Tests

```bash
cd src && vendor/bin/phpunit web/modules/custom/ps_context/tests/
```

Voir aussi [RECETTE.md](RECETTE.md) pour les scénarios CTX-* et scripts E2E.
