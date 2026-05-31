# Notes d'implémentation ps_surface

## Point de départ

Le module a été recréé avec un socle minimal pour relancer l'implémentation.

## Frontière de domaine

- Surface physique: oui (ps_surface)
- Capacité postes: non (ps_offer/ps_context)

## Prochain lot technique

1. Créer le Field Type `ps_surface_item` (fait)
2. Créer la content entity `ps_surface_division` (fait)
3. Ajouter la projection offer (`field_surf_*_proj`) (fait)
4. Ajouter les commandes Drush de maintenance
