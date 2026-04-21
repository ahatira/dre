# Module ps_geo_directions

## Résumé fonctionnel

Le module **ps_geo_directions** fournit un formatter de champ personnalisé pour les champs Geofield dans Drupal 11, permettant d'afficher une carte Google Maps enrichie d'un widget de directions interactif.

### Fonctionnalités principales
- **Formatter personnalisé** : `PsGeofieldDirectionsFormatter` étend le formatter natif `GeofieldGoogleMapFormatter` du module Geofield Map.
- **Widget directions** : Ajoute une surcouche (overlay) sur la carte pour permettre à l'utilisateur de calculer des itinéraires depuis une origine configurable (coordonnées ou token).
- **Paramétrage avancé** :
  - Activation/désactivation du widget directions.
  - Position de l'overlay (TOP_LEFT, TOP_RIGHT, BOTTOM_LEFT, BOTTOM_RIGHT).
  - Origine personnalisable (coordonnées ou token).
  - Mode debug pour afficher des logs dans la console JS.
- **Intégration Drupal** :
  - Utilisation de `drupalSettings` pour passer les paramètres JS côté client.
  - Ajout de la librairie JS/CSS spécifique du module.
  - Respect des bonnes pratiques d'injection de dépendances et d'architecture OOP Drupal 11.

### Points techniques
- Le formatter fusionne la carte et le widget directions dans un wrapper HTML/CSS positionné dynamiquement.
- Les paramètres sont configurables via le formulaire d'administration du formatter.
- Le module est conçu pour être extensible et compatible avec les évolutions du module Geofield Map.

### Utilisation
1. Ajouter un champ Geofield à un type d'entité.
2. Choisir le formatter "PS Geographic directions widget" dans l'affichage du champ.
3. Configurer les options du widget directions selon les besoins.

---

**Auteur** : Équipe DRE / BNP Paribas Real Estate
**Compatibilité** : Drupal 11, Geofield, Geofield Map
