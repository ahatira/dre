# Fiche Offre (Responsive)

Statut: Brouillon
Dernière mise à jour: 2025-12-13

## Vue d’ensemble
Une seule page d’offre qui adapte son affichage selon le viewport (desktop/tablette/mobile). Présente les photos, le consultant, les actions clés, les spécifications détaillées, les widgets énergie, le tableau des surfaces, la localisation (carte + POI + trajets) et les offres similaires.

## Objectifs
- Mettre en avant les infos critiques (titre, surface, localisation, prix) dès l’en-tête.
- Permettre les actions clés: surfaces, brochure, visite, contact consultant.
- Structurer les spécifications pour une lecture rapide.
- Garantir accessibilité, responsive et Token-First.

## Structure de page (commune)
1. Fil d’Ariane
2. En-tête d’offre
3. Barre d’actions
4. Carte consultant + CTA contact
5. Description (Voir plus)
6. Sections de spécifications (Équipements, Services, État du bâtiment, Informations complémentaires)
7. Section Énergie
8. Tableau des surfaces
9. Localisation + Carte + Filtres POI + Temps de trajet
10. Galerie modale (photos)
11. Offres similaires

## Dépendances
- Atomes: Button, Badge, Icon, Image, Heading, Text, Link, Label, Input, Select, Checkbox, Radio
- Molécules: Card, Breadcrumb, Form Field, Accordion Item, Table Row, Gallery Modal, Consultant Card, Contact Form
- Organismes: Carousel, Accordion, Surface Table, Map Widget

## UX par breakpoint
### Desktop
- Deux colonnes quand pertinent (contenu + côté).
- Barre d’actions visible sous l’en-tête; CTA contact accessible.
- Tableau des surfaces lisible en grille + scroll horizontal si besoin.

### Tablette
- Colonne unique avec priorisation verticale; actions regroupées sous l’en-tête.
- Sections réductibles si nécessaire pour limiter le scroll.

### Mobile
- Colonne unique; sections repliables.
- Galerie modale plein écran; barre d’actions condensée.
- Liens d’ancrage pour surfaces et contact.

## Accessibilité
- Navigation clavier complète; focus visible (tokens focus).
- Landmarks (header, main, footer), titres hiérarchisés.
- ARIA pour tabs/carrousel/galerie/modal; `alt` pour images.
- Modales (galerie, contact): `role="dialog"`, `aria-modal="true"`, focus trap, ESC.

## Tokens
- Espacements: `--size-*` pour padding/marges/gaps.
- Couleurs sémantiques: `--primary`, `--info`, `--neutral`, `--danger` pour alertes.
- Typo: usages conformes aux tokens de texte/heading.
- Overlays & focus: `--overlay-*`, `--border-focus`, `--focus-ring`.
- Tableaux: bordures, zebra rows via tokens neutres.

## Variantes & états
- Prix visible/masqué selon contexte légal.
- Badges: New, Exclusive, Favori (cœur).
- Disponibilité: Immediately / Date.
- Énergie: fourni / non fourni.

## Données d’entrée
- Titre, référence, surface (m²), localisation (adresse/ville), prix, disponibilité, type de mandat.
- Liste photos (URLs + légendes), consultant (nom, téléphone, email), URL brochure.
- Liste surfaces (lot, étage, nature, surface, disponibilité).
- Données de localisation (lat/lng), catégories POI, origine temps de trajet.

## Liens croisés
- La galerie s’ouvre depuis les contrôles du carrousel.
- Le modal de contact s’ouvre depuis la carte consultant.
- Le lien "tableau des surfaces" fait défiler jusqu’à la section.
- Les offres similaires pointent vers d’autres fiches.

## Sous-spécifications
- En-tête: `design/pages/property-detail/offer-header.md`
- Actions: `design/pages/property-detail/offer-actions.md`
- Carte consultant: `design/pages/property-detail/consultant-card.md`
- Description: `design/pages/property-detail/description.md`
- Spécifications: `design/pages/property-detail/specs-sections.md`
- Énergie: `design/pages/property-detail/energy.md`
- Surfaces: `design/pages/property-detail/surface-table.md`
- Localisation & carte: `design/pages/property-detail/location.md`
- Filtres POI: `design/pages/property-detail/poi-filters.md`
- Temps de trajet: `design/pages/property-detail/travel-time.md`
- Galerie modale: `design/pages/property-detail/gallery-modal.md`
- Biens similaires: `design/pages/property-detail/similar-properties.md`

## Notes d’implémentation
- Suivre `.github/instructions/02-component-development.md` et `03-technical-implementation.md`.
- Aucune valeur en dur — utiliser les tokens (espacement, couleurs, typo).
- Storybook: ajouter des stories pleine page avec mocks réalistes.
