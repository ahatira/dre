# Intégration Context (`ps_context`) dans `ps_offer`

## Pont optionnel

`ps_offer` déclare l'interface `OfferContextResolverInterface` sans dépendre de `ps_context` dans `.info.yml`.

À l'activation de `ps_context`, le service `ps_context.offer_context_resolver` (`OfferMatrixContextResolver`) est injecté via `ps_offer.services.yml` :

```yaml
ps_offer.validation_manager:
  arguments:
    $contextResolver: '@?ps_context.offer_context_resolver'
```

Le préfixe `@?` rend l'injection optionnelle : sans `ps_context`, les validateurs ne consultent pas la matrix.

## Méthodes disponibles

| Méthode | Usage |
|---------|-------|
| `isTabVisible($offer, 'group_surface')` | Validation surface, divisibilité |
| `isTabVisible($offer, 'group_capacity')` | Validation capacité obligatoire |
| `isFieldVisible($offer, 'field_divisible')` | Affichage conditionnel (futur) |
| `isCapacityDriven($offer)` | KPI surface vs capacité |

## OfferValidationManager

| Validateur | Comportement matrix-driven |
|------------|---------------------------|
| `validateSurface()` | **Ignoré** si `group_surface` masqué (ex. COW) |
| `validateDivisibility()` | **Ignoré** si `group_surface` masqué |
| `validateCapacity()` | **Blocant** si `group_capacity` visible et total ≤ 0 |

Cas OFF-03 / OFF-10 : publication COW sans surface TOTAL → OK (matrix-driven).

## OfferSurfaceKpiBuilder

Service `ps_offer.surface_kpi_builder` :

- `isCapacityDriven()` → affiche postes (`field_capacity_*`), pas de m²
- Sinon règles bnppre.fr (TER, divisible, DISPO/TOTAL)

Utilisé par les formatters `ps_surface` et les blocs carte offre.

## Publication via UI

| Rôle | Case « Published » | Publication offre |
|------|-------------------|-------------------|
| `content_editor` | Masquée (`#access = false` via Gin/bnp_editor) | Brouillon uniquement via UI |
| `content_admin` | Visible | Publication manuelle possible |

Recette : OFF-03-UI avec `content.admin` sur `/node/{id}/edit`.

## Références

- Architecture matrix : `ps_context/docs/MATRIX_ARCHITECTURE.md`
- Validations détaillées : [VALIDATION.md](VALIDATION.md)
- Recette offre : [RECETTE.md](RECETTE.md)
