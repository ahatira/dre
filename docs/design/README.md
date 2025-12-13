# Design Specs - Spécifications détaillées

Spécifications responsive complètes des pages et composants complexes.

## 📐 Structure

```
design/
├── pages/                    # Spécifications de pages complètes
│   ├── property-detail.md    # Overview page Offre
│   ├── property-detail/      # Sous-specs détaillées Offre
│   ├── search-results.md     # Overview page Recherche (responsive)
│   ├── search-results/       # Sous-specs détaillées Recherche (responsive)
│   ├── account.md            # Overview page Compte (responsive)
│   └── account/              # Sous-specs détaillées Compte (responsive)
└── README.md                 # Ce fichier
```

## 🎯 Approche Responsive

Chaque page suit le modèle **Spécification responsive unifiée** :

1. **Fichier overview** (`{page}.md`) - Vue d'ensemble avec architecture responsive
2. **Sous-spécifications** (dossier `{page}/`) - Détails composants avec sections "UX par breakpoint"
	- Desktop (≥768px) et Mobile (<768px) dans **un seul fichier** par composant

**Avantages** :
- ✅ Réduction de 60% de la redondance (ex: account 15→6 fichiers)
- ✅ Maintenance simplifiée (un lieu de vérité par composant)
- ✅ Vision complète responsive sans navigation multiple

📖 Consultez [`../RESPONSIVE-GUIDE.md`](../RESPONSIVE-GUIDE.md) pour le guide méthodologique complet.

## 📄 Pages documentées

### Offre immobilière (Property Detail)
**Overview** : [`property-detail.md`](pages/property-detail.md)

Détails d'un bien immobilier avec galerie photos, informations techniques, localisation et biens similaires.

**Sous-specs** (dans `property-detail/`) :
- `offer-header.md` - En-tête avec titre, localisation, prix
- `offer-actions.md` - Barre d'actions (surfaces, brochure, visite)
- `consultant-card.md` - Carte consultant avec contact
- `description.md` - Description longue avec read-more
- `specs-sections.md` - Équipements, services, état
- `energy.md` - DPE et GES
- `surface-table.md` - Tableau des lots/surfaces
- `location.md` - Adresse, transports, carte
- `poi-filters.md` - Filtres points d'intérêt sur carte
- `travel-time.md` - Calculateur temps de trajet
- `gallery-modal.md` - Galerie photos modale
- `similar-properties.md` - Carrousel biens similaires

### Recherche (Search Results)
**Overview** : [`search-results.md`](pages/search-results.md)

Résultats de recherche avec filtres, tri, cartes, carte interactive et comparateur. Spécifications responsive unifiées.

**Sous-spécifications** (dans `search-results/`) :
- `mobile-navigation.md` - Top bar fixe + actions bar sticky (mobile uniquement)
- `filters-sort.md` - Barre filtres sidebar + dropdown tri (responsive)
- `results-cards.md` - Liste résultats + cartes individuelles (responsive)
- `map-distance.md` - Carte interactive + widget zone distance (responsive)
- `comparator.md` - Panel comparaison 3 biens (responsive)
- `calculator-banner.md` - Bannière calculateur loyer (responsive)

Chaque fichier contient sections "Desktop (≥768px)" et "Mobile (<768px)" avec détails comportement, layout, interactions.

### Compte utilisateur (Account)
**Overview** : [`account.md`](pages/account.md)

Gestion du compte avec profil, favoris, alertes et support. Spécifications responsive unifiées.

**Sous-spécifications** (dans `account/`) :
- `navigation.md` - Sidebar desktop + hub menu mobile (responsive)
- `profile.md` - Vue/édition profil + changement mot de passe (responsive)
- `favorites.md` - Grille desktop (3 colonnes) + liste mobile (responsive)
- `alerts.md` - Liste alertes (responsive)
- `support.md` - Bannière support + déconnexion (responsive)

Chaque fichier contient sections "Desktop (≥768px)" et "Mobile (<768px)" avec détails comportement, layout, interactions.

## 🔗 Liens utiles

- **Guide responsive** : `docs/RESPONSIVE-GUIDE.md`
- **Inventaires atomiques** : `docs/atomic/`
- **Plan développement** : `docs/ROADMAP.md`
- **Instructions implémentation** : `.github/instructions/`

## 📝 Convention de nommage

- **Fichiers overview** : `{page}.md` (ex: `account.md`, `search-results.md`)
- **Sous-specs** : nom descriptif en kebab-case (ex: `filters-sort.md`, `profile.md`)
- **Dossiers pages** : nom de la page en kebab-case (ex: `account/`, `search-results/`)
- **Structure responsive** : sections "Desktop (≥768px)" et "Mobile (<768px)" dans un seul fichier

**Anciennes conventions obsolètes** (avant refactor 2025-12-17) :
- ❌ Dossiers séparés `-mobile/` (ex: `account-mobile/`, `search-results-mobile/`)
- ❌ Fichiers `index.md` pour overview (maintenant `{page}.md` directement)
- ❌ Duplication desktop/mobile (maintenant unifié avec sections breakpoint)
