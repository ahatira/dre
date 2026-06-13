# Hooks OOP et validation des offres

## Vue d'ensemble

`ps_offer` valide à la pré-sauvegarde via **Hook OOP Drupal 11** (`#[Hook]`) et le service `OfferValidationManager`.

```
OfferHooks::nodePresave()
    → OfferValidationManager::apply()
        → validateBudget()
        → validateCapacity()      ← matrix : requis si group_capacity visible
        → validateSurface()       ← matrix : skip si group_surface masqué
        → validateDivisibility()  ← matrix : skip si group_surface masqué
        → validatePrimaryAgent()
        → validateManualReferenceUniqueness()
```

Form validate séparé : `OfferHooks::validateGallery()` — galerie obligatoire à la publication.

## Pattern Hook OOP

Voir `src/Hook/OfferHooks.php` — logique métier dans le service, pas dans le hook.

## Pont Context (optionnel)

Si `ps_context` est actif, `OfferContextResolverInterface` est injecté. Détail : [CONTEXT_INTEGRATION.md](CONTEXT_INTEGRATION.md).

## Règles de validation

### `validateBudget()`

- Valeur ≤ 0 ou vide → `field_budget_value` = NULL, reset period/unit
- Pas de message utilisateur (normalisation import)

### `validateCapacity()`

- Mode `SEAT_BASED` : total > 0
- `available` ≤ `total`, non négatif
- Unité `PER_POSTE` : total > 0
- **Matrix** : si `group_capacity` visible → total > 0 obligatoire (warning brouillon / blocant publication)

### `validateSurface()`

- Au moins une qualification `TOTAL` > 0
- **Matrix** : ignoré si onglet `group_surface` masqué (COW, etc.)

### `validateDivisibility()`

- Non divisible + DISPO < TOTAL → warning UX
- **Matrix** : ignoré si `group_surface` masqué

### `validatePrimaryAgent()`

- Publication sans agent → `setUnpublished()` + warning (pas d'exception)

### `validateManualReferenceUniqueness()`

- Mode manuel + doublon `field_reference` → blocant (brouillon et publication)

### `validateGallery()` (form)

- Publication sans `field_media_gallery` → erreur formulaire

## Comportement brouillon vs publication

| Mode | Incohérence bloquante |
|------|----------------------|
| Brouillon | `Messenger::addWarning()` |
| Publication | `addError()` + `EntityStorageException` |

Exception : agent absent → dépublication silencieuse + warning.

## Skip traduction

`apply()` retourne immédiatement si la langue courante ≠ langue par défaut (évite faux positifs sur champs non traduisibles).

## Tests

```bash
cd src && vendor/bin/phpunit web/modules/custom/ps_offer/tests/src/Unit/OfferValidationManagerTest.php
cd src && composer test:manual-offer-val
```

Recette complète : [RECETTE.md](RECETTE.md).

## Ajouter une règle

1. Méthode privée `validate*()` dans `OfferValidationManager`
2. Appel dans `apply()`
3. Test unitaire dans `OfferValidationManagerTest`
4. Si matrix-driven : consulter `OfferContextResolverInterface` comme `validateSurface()`
