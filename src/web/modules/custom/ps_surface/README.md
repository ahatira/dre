# Module `ps_surface`

> Statut : 🟡 En développement — formatters matrix-driven, projection active

Domaine surfaces physiques (m²/ha) pour les offres : modèle divisions, projections agrégées, affichage FO piloté par la matrix Context.

## Responsabilité

`ps_surface` porte uniquement la sémantique surface physique:

- qualifications de surface (TOTAL, DISPO, ETREF)
- divisions/lots (modèle canonique cible)
- projection agrégée vers l'offre

Le module ne porte pas la capacité en nombre de postes (pilotée par `ps_offer` + matrix `ps_context`).

## Affichage matrix-driven

Les formatters FO (`ps_surface_contextual`, compare, division table) délèguent à `ps_offer.surface_kpi_builder` et respectent `isCapacityDriven()` — pas de hardcode COW.

Voir [`docs/MATRIX_DISPLAY.md`](docs/MATRIX_DISPLAY.md).

## État actuel

- Field Type `ps_surface_item` (type + widget + formatter)
- Content entity révisionnable `ps_surface_division`
- Service de projection (`SurfaceProjectionManager`)
- Formatters FO matrix-driven

## Documentation

- [docs/MATRIX_DISPLAY.md](docs/MATRIX_DISPLAY.md) — Formatters et pont Context
- [docs/IMPLEMENTATION_NOTES.md](docs/IMPLEMENTATION_NOTES.md) — Notes techniques
- Matrix source : `ps_context/docs/MATRIX_ARCHITECTURE.md`
