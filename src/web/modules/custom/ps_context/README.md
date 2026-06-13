# Module Property Search - Context (`ps_context`)

> Statut : 🟢 Stable

Moteur de règles conditionnelles du formulaire offre : visibilité, obligation et valeurs par défaut selon `asset_type × operation_type × field_divisible`.

## Responsabilité

`ps_context` pilote l'UX adaptative du formulaire node `offer` via des règles configurables (`ps_context_rule`). Chaque règle combine des **conditions** (SI : champ = valeur) et des **actions** (ALORS : show/hide tab/field, required, optional, disabled, default).

Le module évalue les règles côté serveur (PHP : required/disabled/defaults) et côté client (JS : visibilité onglets/champs en temps réel).

Ce module **ne fait pas** :
- la définition des champs offre (`ps_offer`) ;
- la validation métier à la sauvegarde (`OfferValidationManager` dans `ps_offer`, via pont optionnel `OfferContextResolverInterface`) ;
- le catalogue de features (`ps_feature`) ;
- la recherche front-end (`ps_search`).

Référence métier complète : `.ai/PROJECT_MATRIX.md` (DEC-0020).

## Fonctionnalités

- Config entity `ps_context_rule` avec CRUD BO
- **15 règles seed** livrées en `config/install/`
- **`ContextRuleEvaluator`** : moteur PHP canonique (même algorithme que le JS formulaire)
- **`OfferMatrixContextResolver`** : pont `OfferContextResolverInterface` vers `ps_offer` / `ps_surface`
- Service `OfferMatrixRules` : chargement, tri par weight, sérialisation vers `drupalSettings.psContext.rules`
- JS `ps-context-offer-form.js` : réévaluation dynamique à chaque changement de champ discriminant
- Hooks OOP sur formulaires offer add/edit
- Traductions : fr, de, es, it, lb, nl, pl

## Architecture

### Services

| Service ID | Classe | Rôle |
|---|---|---|
| `ps_context.rule_evaluator` | `ContextRuleEvaluator` | Moteur PHP canonique (même algorithme que le JS formulaire) |
| `ps_context.offer_context_resolver` | `OfferMatrixContextResolver` | Pont `OfferContextResolverInterface` (validation, KPI, formatters) |
| `ps_context.offer_matrix_rules` | `OfferMatrixRules` | Charge les règles actives, applique actions PHP formulaire, attache JS |
| `ps_context.search_filter_visibility` | `SearchFilterVisibilityResolver` | Filtres recherche dérivés de la matrix |
| `Drupal\ps_context\Hook\OfferFormHooks` | `OfferFormHooks` | Hooks formulaire offer (injection via FQCN) |

### Entités

| Type | Machine name | Classe | Description |
|---|---|---|---|
| Config | `ps_context_rule` | `PsContextRule` | Règle SI/ALORS (conditions + actions, weight, status) |

**Structure d'une règle :**
- `conditions_logic` : `AND` ou `OR`
- `conditions[]` : `{ field_name, operator, value }`
- `actions[]` : `{ action_type, target, value }`

**Types d'actions supportés :** `show_tab`, `hide_tab`, `show_field`, `hide_field`, `required`, `optional`, `disabled`, `set_default`, `hide_surface_delta`

### Hooks OOP

| Classe | Hook | Méthode | Rôle |
|---|---|---|---|
| `OfferFormHooks` | `form_node_offer_form_alter` | `alterOfferAddForm()` | Applique la matrice sur création |
| `OfferFormHooks` | `form_node_offer_edit_form_alter` | `alterOfferEditForm()` | Applique la matrice sur édition |

### Plugins

Aucun (pas de field type, migrate, block).

### Value objects

| Classe | Rôle |
|---|---|
| `OfferContextState` | État résolu : onglets/champs visibles, défauts, `isCapacityDriven()` |
| `ContextSeedRules` | Liste des 15 règles seed (avertissement à la suppression) |

### Commandes Drush

Aucune.

## Consommateurs matrix

| Module | Usage |
|---|---|
| `ps_offer` | `OfferValidationManager`, `OfferSurfaceKpiBuilder` |
| `ps_surface` | Formatters `ps_surface_contextual`, compare, division table |
| `ps_search` | `SearchFilterVisibilityResolver` |

Voir [`docs/MATRIX_ARCHITECTURE.md`](docs/MATRIX_ARCHITECTURE.md).

## Routes & Accès

| Route | Chemin | Permission |
|---|---|---|
| `entity.ps_context_rule.collection` | `/admin/ps/config/matrix` | `administer ps_context matrix` |
| `entity.ps_context_rule.add_form` | `/admin/ps/config/matrix/add` | `administer ps_context matrix` |
| `entity.ps_context_rule.edit_form` | `/admin/ps/config/matrix/{ps_context_rule}` | `administer ps_context matrix` |
| `entity.ps_context_rule.delete_form` | `/admin/ps/config/matrix/{ps_context_rule}/delete` | `administer ps_context matrix` |

Menu : **Matrix rules** sous `ps_core.config`.

## Permissions

| Permission | Description |
|---|---|
| `administer ps_context matrix` | Configurer les règles de visibilité du formulaire offre |

Note : `manage ps_context` (déclarée dans `ps_core`) n'est pas utilisée dans les access checks de ce module.

## Configuration initiale (`config/install/`)

15 règles seed :

| ID règle | Rôle |
|---|---|
| `default_hide_surface` | Masque le groupe Surface par défaut |
| `default_hide_capacity` | Masque le groupe Capacité par défaut |
| `default_hide_budget` | Masque le groupe Budget par défaut |
| `default_hide_lots` | Masque le groupe Lots par défaut |
| `asset_type_cow` | COW → Capacité visible, Surface/Divisible cachés |
| `asset_selected_show_surface` | Types non-COW → Surface visible |
| `not_divisible_hide_surface_rows` | Non divisible → masque lignes DISPO/ETREF |
| `divisible_show_lots` | Divisible → Lots visible |
| `operation_selected_show_budget` | Opération sélectionnée → Budget visible |
| `operation_type_ven` | Vente → masque période et HT |
| `loc_budget_period_year` | Location → période YEAR par défaut |
| `loc_budget_unit_per_m2` | Location (hors COW) → unité PER_M2 |
| `loc_cow_budget_unit_per_poste` | Location COW → unité PER_POSTE |
| `ven_budget_unit_global` | Vente → unité GLOBAL |
| `default_budget_currency_eur` | Devise EUR par défaut |

## Tests

| Classe | Type | Scénarios |
|---|---|---|
| `ContextRuleEvaluatorTest` | Unit | Évaluation conditions, onglets COW/BUR, défauts budget |
| `SearchFilterVisibilityResolverTest` | Unit | Filtres recherche dérivés matrix |

```bash
cd src && vendor/bin/phpunit web/modules/custom/ps_context/tests/
cd src && composer test:manual-recette-ctx   # CTX-* (script bnp_admin)
```

Recette QA : [`docs/RECETTE.md`](docs/RECETTE.md).

## Dépendances

- `ps_offer:ps_offer` — Formulaire cible et champs référencés par les règles
- `drupal:node` — Content entity node
- `drupal:field` — API champs

**Couplage runtime :** dépend de `ps_offer` mais pas l'inverse — activer `ps_context` séparément après `ps_offer`.

## Installation & reset

```bash
drush pm:enable ps_context -y
drush cr
```

Les 15 règles seed sont importées via `config/install/`. Désactiver une règle depuis `/admin/ps/config/matrix` sans la supprimer.

## Documentation

| Fichier | Contenu |
|---|---|
| [`docs/MATRIX_ARCHITECTURE.md`](docs/MATRIX_ARCHITECTURE.md) | Source de vérité, services, consommateurs |
| [`docs/RECETTE.md`](docs/RECETTE.md) | Scénarios CTX-* et scripts E2E |

## Notes techniques

- Library : `ps_context/offer.form` (JS + CSS)
- Settings JS : `drupalSettings.psContext.rules` (tableau des règles actives triées par weight)
- Suppression règle seed : `PsContextRuleDeleteForm` affiche un avertissement
- Document central métier : `.ai/PROJECT_MATRIX.md`
- Décision architecture : DEC-0020 dans `.ai/PROJECT_DECISIONS.md`
