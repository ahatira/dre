# Hooks OOP et validation des offres — Documentation technique

## Vue d'ensemble

`ps_offer` implémente la validation métier à la pré-sauvegarde via le pattern **Hook OOP Drupal 11** (`#[Hook()]` attribute), sans aucun code procédural dans `.module`.

```
OfferHooks (src/Hook/)
    ↓ appelle
OfferValidationManager (service)
  ↓ délègue à 2 méthodes privées
validateBudget | validatePrimaryAgent
```

---

## Pattern Hook OOP (Drupal 11)

### Classe de hook

```php
// src/Hook/OfferHooks.php
final class OfferHooks {
  public function __construct(
    private readonly OfferValidationManagerInterface $offerValidationManager,
  ) {}

  #[Hook('node_presave')]
  public function nodePresave(NodeInterface $node): void {
    $this->offerValidationManager->apply($node);
  }
}
```

**Règles** :
- La classe est dans `src/Hook/`, nommée `*Hooks.php`
- Le service est déclaré avec la classe elle-même comme ID (autowiring)
- L'attribut `#[Hook('hook_name')]` remplace la fonction procédurale `hook_*()` du `.module`
- La logique métier est dans le service, pas dans le hook

### Pourquoi ce pattern ?

| Critère | Hook procédural | Hook OOP |
|---|---|---|
| Testabilité | Difficile (fonctions globales) | Facile (mock via interface) |
| DI | Impossible directement | Natif via constructeur |
| Cohérence | `.module` gonflé | Classe dédiée, découplée |
| Drupal 11 | Compatible | Recommandé |

---

## Service de validation

### Interface

```php
interface OfferValidationManagerInterface {
  public function apply(NodeInterface $node): void;
}
```

L'interface permet de mocker le service dans les tests unitaires du hook.

### Orchestration

`apply()` appelle séquentiellement les validateurs métier actifs.

- En **publication** (`isPublished() = true`), les incohérences bloquantes lèvent une `EntityStorageException`.
- En **publication** (`isPublished() = true`), un message métier explicite est ajouté via `MessengerInterface::addError()` puis une `EntityStorageException` est levée.
- En **brouillon** (`isPublished() = false`), ces mêmes incohérences remontent en `addWarning()` pour permettre une saisie progressive.
- Le node peut être modifié directement (ex : dépublication forcée si agent principal absent).

---

## Règles de validation détaillées

### 1. `validateBudget()`

- **Condition** : `field_budget_period` défini mais `field_budget_value` absent ou ≤ 0
- **Action (publication)** : exception bloquante
- **Action (brouillon)** : warning non bloquant (message courant: `Price value must be greater than 0 when a price period is set.`)

### 2. `validatePrimaryAgent()`

- **Condition** : `field_primary_agent` vide au moment de la sauvegarde
- **Action** : `$node->setUnpublished()` + `addWarning()` (pas d'exception — l'offre est sauvée en brouillon)

---

## Lecture des champs (helper interne)

```php
private function fieldValue(FieldItemListInterface $list): ?string {
    $item = $list->first();
    if ($item === NULL) {
        return NULL;
    }
    return $item->getValue()['value'] ?? NULL;
}
```

Retourne `NULL` si le champ est vide, sinon la valeur scalaire. Utilisé par tous les validateurs.

---

## Tests unitaires

Les tests utilisent des mocks PHPUnit sur `FieldItemListInterface` pour simuler les états de champs sans base de données.

Helpers de test :
- `fieldListWithValue(string $value)` — Retourne un mock de liste avec une valeur
- `emptyFieldList()` — Retourne un mock de liste vide

Cas couverts :
1. Budget invalide (period sans value) en publication → exception
2. Budget invalide (period sans value) en brouillon → warning
3. Offre publiée sans agent → dépublication + warning
4. No-op pour un node hors bundle `offer`

Voir [`tests/src/Unit/OfferValidationManagerTest.php`](../tests/src/Unit/OfferValidationManagerTest.php).

---

## Ajouter une règle de validation

1. Ajouter une méthode privée `validate*()` dans `OfferValidationManager`
2. L'appeler dans `apply()`
3. Ajouter un test unitaire dans `OfferValidationManagerTest`
4. Ne pas modifier `OfferHooks` ni `OfferValidationManagerInterface`
