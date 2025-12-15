# Components (Molecules)

Composants fonctionnels composés de plusieurs atomes.

**Total** : 30 composants  
**Implémentés** : 24/30 (80%)

---

## Content Cards

**Card** - Conteneur avec image, titre, texte, CTA  
Usage : Cartes de biens, actualités, études de marché  
Specs : Réutilisé dans `design/pages/search-results/result-card.md`  
Implémenté : ✅

**Card Offer Search** - Carte offre pour résultats de recherche  
Usage : Résultats recherche immobilière  
Specs : `design/pages/search-results/result-card.md`  
Implémenté : ✅

**Card Offer Slide** - Carte offre pour carrousel  
Usage : Carrousel offres mises en avant  
Implémenté : ✅

**Consultant Card** - Fiche conseiller avec avatar, contact, CTA  
Usage : Sidebar page offre  
Specs : `design/pages/property-detail/consultant-card.md`  
Implémenté : ✅

---

## Forms

**Form** - Élément formulaire avec gestion d'état  
Usage : Formulaires contact, recherche  
Implémenté : ✅

**Form Field** - Input/textarea + label + message validation  
Usage : Champs recherche bien, formulaires contact  
Implémenté : ✅

**Checkboxes** - Groupe de cases à cocher liées  
Usage : Sélection type bien, checklist équipements  
Implémenté : ✅

**Radios** - Groupe de boutons radio liés  
Usage : Options achat/location/investissement, filtres  
Implémenté : ✅

**Search Bar** - Input avec icône recherche + submit  
Usage : Widget recherche hero, localisation bien  
Implémenté : ✅

---

## Navigation

**Breadcrumb** - Fil d'Ariane avec séparateurs  
Usage : Page détail bien, article  
Implémenté : ✅

**Menu Item** - Lien nav avec icône, badge, sous-menu  
Usage : Navigation principale (Résidentiel, Commercial, Services)  
Implémenté : ✅

**Pagination** - Navigation pages avec numéros + flèches  
Usage : Résultats recherche, archive actualités  
Implémenté : ✅

**Language Selector** - Sélecteur langue/pays  
Usage : Toggle FR/EN dans header  
Implémenté : ✅

**Tabs** - Container d'onglets avec variantes sémantiques  
Usage : Onglets détail bien (description, photos, énergie)  
Variants : neutral, primary, success, danger, warning, info, gold, light, dark  
États : default, pill, with-icon  
Implémenté : ✅

---

## Feedback

**Alert** - Message notification avec icône + fermer  
Usage : Validation formulaire, messages succès/erreur  
Implémenté : ✅

**Toast** - Notification temporaire auto-dismiss  
Usage : Notification après actions, messages système  
Implémenté : ✅

**Tooltip** - Aide contextuelle hover/focus  
Usage : Texte aide sur champs, icônes info  
Implémenté : ✅

**Modal** - Dialog overlay avec header, contenu, actions  
Usage : Contact conseiller, filtres, rendez-vous  
Implémenté : ✅

**Gallery Modal** - Lightbox galerie avec prev/next + thumbnails  
Usage : Photos de biens  
Specs : `design/pages/property-detail/gallery-modal.md`  
Implémenté : ❌

---

## Interactive

**Dropdown** - Bouton trigger avec menu overlay  
Usage : Tri, filtres  
Specs : `design/pages/search-results/sort-dropdown.md`  
Implémenté : ✅

**Stepper** - Indicateur étapes avec statut  
Usage : Wizard recherche multi-étapes  
Implémenté : ✅

---

## Lists & Tables

**List Item** - Item unique avec icône, texte, actions  
Usage : Item vue liste biens, entrée résultat  
Implémenté : ❌

**Table** - Tableau de données avec headers et rangées  
Usage : Données marché, comparaison  
Implémenté : ✅

**Specs List** - Liste clé-valeur avec icônes  
Usage : Équipements, services, état (listes structurées)  
Specs : `design/pages/property-detail/specs-sections.md`  
Implémenté : ❌

**Surface Table Row** - Ligne lot : étage, nature, surface, dispo  
Usage : Tableau des surfaces offre  
Specs : `design/pages/property-detail/surface-table.md`  
Implémenté : ❌

**Tag List** - Collection de chips/tags  
Usage : Tags de bien, filtres actifs  
Implémenté : ✅

---

## Media

**Video** - Lecteur vidéo avec contrôles  
Usage : Visites virtuelles, rapports marché  
Implémenté : ✅

**Carousel** - Carrousel avec slides et navigation  
Usage : Carrousel photos bien, biens mis en avant  
Implémenté : ✅

**Map Widget** - Bloc carte embarquée avec markers + contrôles  
Usage : Intégration Google/Leaflet (placeholder)  
Specs : `design/pages/property-detail/location.md`  
Implémenté : ❌

**POI Filter Group** - Groupe filtres checkbox POI sur carte  
Usage : Transports, restaurants, hôtels  
Specs : `design/pages/property-detail/poi-filters.md`  
Implémenté : ❌

**Travel Time Calculator** - Input + widget calcul routes/ETA  
Usage : Champ adresse + résultat temps trajet  
Specs : `design/pages/property-detail/travel-time.md`  
Implémenté : ❌

**Skeleton** - État de chargement placeholder  
Usage : Chargement contenu asynchrone  
Implémenté : ✅

---

## 📚 Références

- **Specs détaillées** : `docs/design/pages/` pour composants de pages
- **Workflow** : `.github/instructions/02-component-development.md`
- **Standards** : `.github/instructions/03-technical-implementation.md`
