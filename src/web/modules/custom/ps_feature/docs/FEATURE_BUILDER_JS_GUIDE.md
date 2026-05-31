# Feature Builder JS - Documentation complète

## Objectif

Cette documentation couvre l'architecture JavaScript du widget `feature_builder` du module `ps_feature`.

Portée:
- bootstrap Drupal behavior;
- état (store) et synchronisation hidden field;
- rendu UI et interactions;
- drag and drop;
- éditeurs payload par type;
- contrat de données avec le backend PHP;
- extension et debug.

## Fichiers JS

- `js/feature-builder-app.js`: point d'entrée Drupal behavior.
- `js/feature-builder-state.js`: store observable des features actives.
- `js/feature-builder-catalogue.js`: accès catalogue groupes/définitions.
- `js/feature-builder-renderer.js`: rendu DOM du widget + interactions UI.
- `js/feature-builder-sortable.js`: intégration SortableJS.
- `js/feature-builder-sync.js`: sync état JS vers hidden field.
- `js/payload-editors/base.js`: classe de base éditeurs.
- `js/payload-editors/*.js`: éditeurs type-specific (`flag`, `yes_no`, `numeric`, `range`, `text`, `list`, `date`, `dictionary`).

## Librairie Drupal

Déclarée dans `ps_feature.libraries.yml` sous `feature-builder`.

Dépendances runtime:
- Drupal core: `core/drupal`, `core/drupal.locale`, `core/once`, `core/drupalSettings`.
- Externes:
  - SortableJS (`Sortable.min.js`)
  - Bootstrap Icons (CSS)

## Bootstrap (feature-builder-app.js)

`Drupal.behaviors.featureBuilder.attach()`:

1. Lit `drupalSettings.featureBuilder`.
2. Détecte le `fieldName` depuis l'ID mount:
   - convention: `fb-{fieldName}-{entityId}`.
3. Récupère le hidden field `fb-state-{fieldName}`.
4. Déplace ce hidden field hors du mount avant rendu:
   - évite sa suppression lors de `mount.innerHTML = ...`.
5. Instancie:
   - `FeatureBuilderStateManager`
   - `FeatureBuilderCatalogueService`
   - `FeatureBuilderRenderer`
   - `FeatureBuilderSortableController`
   - `FeatureBuilderHiddenFieldSync`
6. Lance rendu initial + attache sortable + attache sync.
7. Abonne le renderer aux changements de state.

## Contrat drupalSettings

Injecté côté PHP par `FeatureBuilderWidget`:

```json
{
  "featureBuilder": {
    "field_features": {
      "widgetId": "fb-field_features-1",
      "fieldName": "field_features",
      "initialState": {
        "features": [
          {
            "id": "surface_totale",
            "label": "Surface totale",
            "type": "numeric",
            "group": "surface",
            "payload": {"value": 120, "unit": "m²"},
            "delta": 0
          }
        ]
      },
      "catalogue": {
        "groups": [
          {"id": "surface", "label": "Surface", "weight": 0}
        ],
        "definitions": [
          {
            "id": "surface_totale",
            "label": "Surface totale",
            "group": "surface",
            "type": "numeric",
            "payload_defaults": {"unit": "m²"},
            "options": []
          }
        ]
      }
    }
  }
}
```

## State manager (feature-builder-state.js)

Structure d'état:

```json
{
  "features": [
    {
      "id": "feature_id",
      "type": "numeric",
      "group": "group_id",
      "label": "Feature label",
      "payload": {},
      "delta": 0
    }
  ]
}
```

API principale:
- `getFeatures()`
- `getState()`
- `subscribe(fn)`
- `addFeature(definition)`
- `removeFeature(featureId)`
- `updatePayload(featureId, newPayload)`
- `reorder(orderedIds)`

Note importante:
- `addFeature()` ne copie pas tout `payload_defaults`.
- Seules les clés métier connues par type sont copiées dans `payload`.

## Catalogue service (feature-builder-catalogue.js)

Rôles:
- indexation des définitions par ID;
- recherche catalogue (`search`);
- regroupement des définitions (`groupedDefinitions`).

Fallback `_other`:
- les définitions dont le groupe n'existe pas dans `groups` sont envoyées dans `_other`.

## Renderer (feature-builder-renderer.js)

Responsabilités:
- construction du shell widget;
- rendu de la liste features groupée par groupe;
- menu d'ajout avec recherche;
- suppression feature;
- toggles collapse des groupes;
- instanciation des payload editors.

Points clés:
- escaping HTML systématique (`_esc`) pour les valeurs injectées en template string.
- labels/type préférés depuis state, fallback depuis catalogue.
- éditeur choisi via mapping dynamique:
  - `window.FeatureBuilderEditors[definition.type]`
  - fallback `BasePayloadEditor` si type non implémenté en JS.

Support icônes natif:
- `flag`, `yes_no`, `numeric`, `range`, `text`, `list`, `date`, `dictionary`.
- autres types: icône puzzle fallback.

## Drag and drop (feature-builder-sortable.js)

- attache SortableJS sur chaque conteneur `.fb-items`.
- autorise le déplacement inter-groupes (`group: 'features'`).
- handle: `.drag-handle`.
- à la fin d'un drag (`onEnd`), recalcul ordre global par lecture DOM puis `state.reorder(allIds)`.

## Sync hidden field (feature-builder-sync.js)

- sérialise l'état dans le hidden field (`JSON.stringify(state.getState())`).
- debounce de 150 ms en régime normal.
- flush immédiat sur submit form (capture) pour éviter race condition.

## Payload editors

Classe de base: `BasePayloadEditor` (`base.js`).

Contrat d'un éditeur:
- `render()` retourne HTML.
- `bindEvents(container)` branche les événements et appelle `emit(payload)`.
- `formatPreview()` fournit une preview textuelle.

Éditeurs existants:
- `flag`: payload canonique `{present: boolean}` avec compat legacy lecture `presence`.
- `yes_no`: `{value: boolean}`.
- `numeric`: `{value, unit}`.
- `range`: `{min, max, unit}`.
- `text`: `{value}`.
- `list`: `{codes: string[]}` via `definition.options`.
- `date`: `{date, end_date?}` avec format local basé sur `document.documentElement.lang`.
- `dictionary`: `{code}` via `definition.options`.

Type sans éditeur dédié:
- `taxonomy` n'a pas d'éditeur JS dédié actuellement.
- comportement: fallback `BasePayloadEditor` (placeholder) dans le widget JS.
- alternative: utiliser widget Form API `feature_default` ou implémenter `js/payload-editors/taxonomy.js`.

## Sécurité et robustesse

Côté JS:
- escaping HTML dans renderer et éditeurs.
- submit flush pour éviter perte de données.

Côté PHP (complémentaire mais critique pour le flux JS):
- le JSON reçu depuis hidden field est revalidé/filtré dans `FeatureBuilderWidget::extractFormValues()`.
- whitelist des IDs de définitions actives.
- garde-fous taille payload et structure JSON.

Tests associés:
- `tests/src/Kernel/Plugin/Field/FieldWidget/FeatureBuilderWidgetExtractionTest.php`
- `tests/src/Unit/Plugin/Field/FieldWidget/FeatureBuilderWidgetSecurityTest.php`
- `tests/src/Unit/Service/FeatureBuilderStateBuilderTest.php`

## Extension: ajouter un nouvel éditeur JS

Exemple pour type `taxonomy`:

1. Créer `js/payload-editors/taxonomy.js`.
2. Étendre `BasePayloadEditor`.
3. Enregistrer la classe sur `window.FeatureBuilderEditors.taxonomy`.
4. Ajouter le fichier dans `ps_feature.libraries.yml` (library `feature-builder`).
5. Vérifier que le catalogue fournit les données nécessaires (`options` ou structure dédiée).
6. Tester l'édition BO + soumission + rendu formatter.

## Debug rapide

Checklist:
- `drupalSettings.featureBuilder` présent sur la page d'édition.
- hidden field `fb-state-{fieldName}` présent et mis à jour.
- aucun erreur JS console sur `FeatureBuilderEditors` ou `Sortable`.
- type sans éditeur dédié: comportement fallback attendu.
- après submit, vérifier que `field_features` contient les valeurs attendues.

## Références

- `src/Plugin/Field/FieldWidget/FeatureBuilderWidget.php`
- `src/Service/FeatureBuilderStateBuilder.php`
- `src/Service/FeatureCatalogueBuilder.php`
- `ps_feature.libraries.yml`
