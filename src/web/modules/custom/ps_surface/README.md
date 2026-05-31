# Module `ps_surface`

> Statut : 🟡 En développement — socle recréé, implémentation en cours

Domaine surfaces physiques (m2/ha) pour les offres: modèle divisions, projections agrégées, gouvernance import/BO/search.

## Responsabilité

`ps_surface` porte uniquement la sémantique surface physique:

- qualifications de surface (TOTAL, DISPO, ETREF)
- divisions/lots (modèle canonique cible)
- projection agrégée vers l'offre

Le module ne porte pas la capacité en nombre de postes.

## État actuel

- Socle module Drupal 11 activable
- Field Type `ps_surface_item` (type + widget + formatter)
- Content entity révisionnable `ps_surface_division`
- Service de projection initial (`SurfaceProjectionManager`)
- Contrat d'architecture disponible dans la documentation projet

## Documentation

- Voir [docs/](docs/) pour les notes techniques du module
- Contrat cible global: `docs/modules/PS_SURFACE/docs/TARGET_CONTRACT.md`
