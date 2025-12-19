# Collections (Organisms)

Composants complexes formant des sections complètes.

**Total** : 16 composants  
**Implémentés** : 6/16 (38%)

---

## Site Structure

**Header** - En-tête site avec logo, nav, recherche, actions  
Usage : Logo BNP, toggle Résidentiel/Commercial, search bar, CTA Contact, sélecteur langue  
Implémenté : ❌

**Footer** - Pied de page avec menus, logo, social, légal  
Usage : Services, Ressources, Contact, Légal, liens sociaux, infos bureau  
Implémenté : ❌

**Navigation** - Navigation principale avec dropdowns  
Usage : Achat/Location Résidentiel, Commercial, Services, Études marché, À propos, Contact  
Specs : Compose Menu Item (molecule), responsive avec hamburger mobile, JavaScript toggle + ESC + click outside  
Implémenté : ✅

**Sidebar** - Panneau latéral navigation + contenu  
Usage : Filtres type bien, prix, surface, dropdown tri  
Specs : `design/pages/search-results/filters-bar.md`  
Implémenté : ❌

---

## Content Sections

**Hero** - Bannière grande avec image, titre, sous-titre, CTA  
Usage : Hero homepage "What are you looking for?" avec widget recherche overlay  
Implémenté : ✅

**Card Grid** - Grille de cartes avec filtres  
Usage : Grille 4 col biens mis en avant, recherches commerciales 4 col, articles actualités 3 col  
Implémenté : ❌

**Feature Section** - Section thématique avec icône, titre, liste  
Usage : Sections équipements, services, état du bâtiment (pages offres)  
Specs : Compose Icon + Heading + List atoms, layout deux colonnes responsive  
Implémenté : ✅

**Testimonials** - Citations clients avec avatars  
Usage : Profils experts avec nom, rôle, bureau + section image  
Implémenté : ❌

**Offer Header** - En-tête offre bien : titre, localisation, surface, prix, badges, actions rapides  
Usage : Compteur photos, visite 3D, plan  
Specs : `design/pages/property-detail/offer-header.md`  
Implémenté : ❌

**Offer Actions** - Barre CTA : accès tableau surfaces, télécharger brochure, planifier visite  
Specs : `design/pages/property-detail/offer-actions.md`  
Implémenté : ❌

**Offer Details** - Section description longue avec toggle read-more  
Specs : `design/pages/property-detail/description.md`  
Implémenté : ❌

**Specifications Sections** - Listes groupées : Équipements, Services, État bâtiment, Plus d'infos  
Usage : Chaque groupe avec icône + liste  
Specs : `design/pages/property-detail/specs-sections.md`  
Implémenté : ❌

**Energy Section** - Widgets consommation énergie et émissions avec échelles + labels  
Specs : `design/pages/property-detail/energy.md`  
Implémenté : ❌

**Surface Table** - Tableau données lots (lot, étage, nature, surface, disponibilité)  
Specs : `design/pages/property-detail/surface-table.md`  
Implémenté : ❌

**Location Section** - Bloc adresse avec infos transports, accès routier, carte embarquée  
Specs : `design/pages/property-detail/location.md`  
Implémenté : ❌

**Similar Properties** - Carrousel/grille biens apparentés  
Specs : `design/pages/property-detail/similar-properties.md`  
Implémenté : ❌

---

## Interactive

**Tags** - Collection de chips/tags (compose Tag atom)  
Usage : Tags de bien, filtres actifs, listes de catégories  
Pattern : Composition via `{% include '@elements/tag/tag.twig' %}`  
Note : Tag atom peut être utilisé seul (search input, filter dropdown)  
Implémenté : ✅ (déplacé de Components 2025-12-17 - Correct Atomic Design)

**Accordion** - Plusieurs sections collapsibles  
Usage : Section FAQ : 6+ items Q&A, expansible/collapsible  
Implémenté : ✅

**Tabs** - Container d'onglets avec navigation clavier  
Usage : Onglets détail bien (description, photos, énergie), dashboards  
Compose : Tab (molecule) avec logique JavaScript roving tabindex  
Variants : neutral, primary, success, danger, warning, info, gold, light, dark  
États : horizontal, vertical, auto, manual, pill  
Implémenté : ✅ (déplacé de Components 2025-12-17 - Correct Atomic Design)

**Carousel** - Slider image/contenu avec contrôles  
Usage : Carrousel photos bien avec boutons prev/next + compteur  
Implémenté : ❌

**Modal Manager** - Système modales avec empilement  
Usage : Modal contact conseiller, contact expert, filtres recherche  
Implémenté : ❌

**Gallery** - Galerie photos avec thumbnails + lightbox modal  
Specs : `design/pages/property-detail/gallery-modal.md`  
Implémenté : ❌

**Map + POI Filters** - Carte interactive avec markers, filtres checkbox POI  
Specs : `design/pages/property-detail/location.md` + `poi-filters.md`  
Implémenté : ❌

**Travel Time Tool** - Calculer temps trajet depuis adresse saisie  
Specs : `design/pages/property-detail/travel-time.md`  
Implémenté : ❌

---

## Data Display

**Data Table** - Table complète avec header, tri, filtres, pagination  
Usage : Tables comparaison études marché, données prix, tendances marché  
Implémenté : ❌

**List** - Liste verticale/grille avec filtres + tri  
Usage : Vue liste biens, résultats recherche avec filtres à facettes  
Specs : `design/pages/search-results/results-list.md`  
Implémenté : ❌

---

## Forms

**Form** - Formulaire multi-étapes avec validation + soumission  
Usage : Wizard recherche bien (localisation/budget/surface), formulaire contact (nom/email/message), réservation rendez-vous  
Implémenté : ❌

---

## 📚 Références

- **Specs détaillées** : `docs/design/pages/` pour composants de pages
- **Workflow** : `.github/instructions/02-component-development.md`
- **Standards** : `.github/instructions/03-technical-implementation.md`
