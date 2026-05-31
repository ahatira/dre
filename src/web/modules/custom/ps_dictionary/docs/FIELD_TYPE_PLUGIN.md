# Plugin de champ `ps_dictionary` — Documentation technique

## Vue d'ensemble

Le plugin `ps_dictionary` est un **FieldType Drupal** (`@FieldType(id="ps_dictionary")`) qui stocke un code de référence dictionnaire en base de données (`varchar(16)`) et délègue la résolution label ↔ code au service `DictionaryResolver`.

Il implémente `OptionsProviderInterface` pour s'intégrer nativement avec les widgets de type select/radios de Drupal.

---

## Schéma de stockage

```php
// DictionaryItemFieldType::schema()
'value' => [
  'type'   => 'varchar',
  'length' => 16,
]
```

La valeur stockée est **toujours un code en majuscules** (ex : `BUR`, `M2`, `YEAR`). La normalisation est faite dans `preSave()`.

---

## Cycle de vie du champ

### 1. Affichage du formulaire (widget)

Trois widgets sont disponibles :

| Widget ID | Classe | Comportement |
|---|---|---|
| `ps_dictionary_options_select` | `DictionaryOptionsSelectWidget` | Select list avec options issues de `DictionaryResolver::all()` |
| `ps_dictionary_options_buttons` | `DictionaryOptionsButtonsWidget` | Groupe de boutons radio |
| `ps_dictionary_autocomplete` | `DictionaryItemWidget` | Textfield avec autocomplete JSON (`/ps-dictionary/autocomplete/{type}`) |

Les options sont construites via `DictionaryItemFieldType::buildOptions()` :

```php
private function buildOptions(): array {
    $type = $this->getSetting('dictionary_type');
    $entries = \Drupal::service('ps_dictionary.resolver')->all($type);
    return array_column($entries, 'label', 'code');
    // → ['BUR' => 'Bureau', 'COW' => 'Coworking', ...]
}
```

### 2. Pré-sauvegarde (`preSave`)

Le code est normalisé en majuscules avant stockage :

```php
public function preSave(): void {
    $this->value = strtoupper((string) $this->value);
}
```

### 3. Validation (`isEmpty`)

Un champ est vide si sa valeur est `NULL` ou `''`.

### 4. Affichage (formatter)

Le formatter `ps_dictionary_formatter` utilise `DictionaryResolver::resolveLabel()` pour afficher le label humain du code stocké.

---

## Configuration du champ (settings)

Un seul setting obligatoire : `dictionary_type`.

```yaml
# Exemple dans field.field.node.offer.field_asset_type.yml
settings:
  dictionary_type: asset_type
```

Ce setting est sélectionné à la création du champ depuis la liste des `ps_dictionary_type` existants.

---

## Autocomplete

L'endpoint `GET /ps-dictionary/autocomplete/{dictionary_type}?q=bur` retourne :

```json
[
  {"value": "Bureau", "label": "Bureau (BUR)"}
]
```

Géré par `DictionaryAutocompleteController::handle()`, filtre sur code ou label (insensible à la casse).

---

## Ajouter un nouveau widget ou formatter

1. Créer la classe dans `src/Plugin/Field/FieldWidget/` ou `FieldFormatter/`
2. Annoter avec `#[FieldWidget]` ou `#[FieldFormatter]`
3. Déclarer `field_types: ['ps_dictionary']` dans l'annotation
4. Aucune déclaration de service nécessaire (autodiscovery Drupal)

---

## Compatibilité

- Drupal 11 / PHP 8.3
- S'intègre avec tout module déclarant un champ `type: ps_dictionary`
- Dépend de `ps_dictionary.resolver` (service `ps_dictionary`)
