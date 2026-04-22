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

## Architecture technique et extensibilité

### Base du module
- Le module s’appuie sur le plugin FieldFormatter Drupal (`PsGeofieldDirectionsFormatter`) pour injecter dynamiquement la configuration côté front.
- Le widget Directions est un formulaire Drupal (`DirectionsForm`) rendu dans un overlay sur la carte, totalement découplé du métier.
- Toute la configuration (types de POI, rayon, debug, etc.) est passée via `drupalSettings` pour garantir la synchronisation BO/FO.
- Le JS principal (`ps-nearby-places.js`) gère l’affichage dynamique des POI Google Places, la création/suppression de markers custom SVG, et la gestion des filtres utilisateurs.

### API Google Maps/Places
- Utilisation de Google Maps JS API v3 pour le rendu de la carte et des markers custom.
- Utilisation de Google PlacesService pour la recherche dynamique de POI par catégorie, avec gestion du radius et du mapping type métier → type Google.
- Les markers sont personnalisés via SVG (voir `POI_CONFIG` dans le JS) pour chaque type de POI.
- Le module masque les POI natifs Google et ne gère que ses propres markers pour un contrôle total.
- Le polling JS garantit la robustesse même si la carte ou l’API Google met du temps à charger.

### Bonnes pratiques et extension
- Toute nouvelle catégorie de POI ou logique de filtrage peut être ajoutée côté BO (formatter) sans toucher au JS : la synchronisation est automatique.
- Pour ajouter un nouveau plugin ou widget :
  - Créer un nouveau FieldFormatter ou Formulaire Drupal.
  - Injecter la configuration dans `drupalSettings`.
  - Utiliser le pattern d’initialisation JS existant (polling, mapping, markers custom, gestion des filtres).
- Le module est un exemple de découplage Drupal/JS moderne : aucune logique métier n’est codée en dur côté JS, tout est piloté par la config BO.

### Référence pour extension
- Pour tout nouveau plugin, respecter :
  - Injection de dépendances (services) en PHP.
  - Passage de la config dynamique via `drupalSettings`.
  - Utilisation de composants JS réutilisables et robustes (polling, gestion d’erreur, markers SVG, etc.).
  - Aucune dépendance métier ou bundle spécifique : tout doit rester générique et extensible.

---

**Ce module est une référence pour tout développement de widget cartographique avancé, POI custom, ou intégration Google Maps/Places dans Drupal 11.**

Pour toute extension, se référer à ce module pour :
- La structure BO/FO découplée.
- L’injection dynamique de configuration.
- La gestion robuste des API Google.
- L’extensibilité sans couplage métier.
