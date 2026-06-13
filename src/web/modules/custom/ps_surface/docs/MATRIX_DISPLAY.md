# Affichage surface matrix-driven (`ps_surface`)

## Principe

Les formatters FO des surfaces offre délèguent la logique métier à `ps_offer.surface_kpi_builder`, qui consulte `OfferContextResolverInterface` (pont `ps_context`) pour distinguer offres **capacity-driven** (COW) vs **surface-driven**.

Pas de hardcode `COW` dans `ps_surface`.

## Formatters concernés

| Formatter | ID | Comportement |
|-----------|-----|--------------|
| `SurfaceContextualFormatter` | `ps_surface_contextual` | KPI principal carte/liste |
| `SurfaceCompareFormatter` | `ps_surface_compare` | Comparaison offres |
| `SurfaceDivisionTableFormatter` | `ps_surface_division_table` | Table divisions/lots |

### Capacity-driven (matrix)

- Onglet `group_surface` masqué + `group_capacity` visible
- Formatter surface → rendu vide (capacité affichée ailleurs via champs capacity)

### Surface-driven

- TER : `{TOTAL} {unit}` uniquement
- Divisible : suffixe « divisible dès X m² » si MINIM/ETREF < TOTAL
- Non divisible : TOTAL ou DISPO

## Service délégué

`ps_offer.surface_kpi_builder` (`OfferSurfaceKpiBuilder`) :

```php
$this->contextResolver?->isCapacityDriven($offer)
```

## Tests

```bash
cd src && vendor/bin/phpunit web/modules/custom/ps_surface/tests/src/Unit/SurfaceContextualFormatterTest.php
```

## Références

- KPI builder : `ps_offer/src/Service/OfferSurfaceKpiBuilder.php`
- Matrix : `ps_context/docs/MATRIX_ARCHITECTURE.md`
- Intégration offer : `ps_offer/docs/CONTEXT_INTEGRATION.md`
