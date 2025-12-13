# 📑 Index de la documentation

Guide de navigation pour toute la documentation PS Theme.

---

## 🎯 Navigation rapide

### **"Je veux comprendre le projet"**
→ Lisez [`README.md`](README.md) - Vue d'ensemble + structure

### **"Je veux planifier le développement"**
→ Consultez [`ROADMAP.md`](ROADMAP.md) - 4 phases avec timeline + dépendances

### **"Je veux implémenter un composant"**
→ Allez dans [`atomic/`](atomic/) pour identifier le composant  
→ Puis suivez `.github/instructions/02-component-development.md`

### **"Je veux spécifier une page"**
→ Lisez [`RESPONSIVE-GUIDE.md`](RESPONSIVE-GUIDE.md) - Guide méthodologique  
→ Voir exemples dans [`design/pages/`](design/pages/)

### **"Je veux voir les maquettes"**
→ Ouvrez le dossier [`maquettes/`](maquettes/) (8 fichiers images)

---

## 📄 Fichiers principaux

| Fichier | Rôle | Temps lecture | Audience |
|---------|------|---------------|----------|
| **README.md** | Point d'entrée + vue d'ensemble | 5 min | Tous |
| **INDEX.md** | Navigation (ce fichier) | 5 min | Tous |
| **ROADMAP.md** | Phases développement 0-4, timeline | 15 min | PMs, Devs |
| **RESPONSIVE-GUIDE.md** | Guide specs responsive | 10 min | Specs, Devs |
| **atomic/README.md** | Inventaires Atomic Design | 2 min | Devs |
| **design/README.md** | Navigation specs détaillées | 2 min | Specs, Devs |

---

## 📂 Inventaires Atomic Design

**Dossier** : [`atomic/`](atomic/)

| Fichier | Contenu | Total | Implémentés |
|---------|---------|-------|-------------|
| **elements.md** | Atoms (HTML de base) | 25 | 13 (52%) |
| **components.md** | Molecules (unités fonctionnelles) | 24 | 5 (21%) |
| **collections.md** | Organisms (sections complexes) | 15 | 2 (13%) |
| **layouts.md** | Templates (structures pages) | 10 | 0 (0%) |
| **pages.md** | Pages (exemples complets) | 12 | 0 (0%) |

**Total** : 86 composants documentés, 20 implémentés (~23%)

---

## 🎨 Spécifications de pages

**Dossier** : [`design/pages/`](design/pages/)

### ✅ Offre (Property Detail)
**Fichier** : [`design/pages/property-detail.md`](design/pages/property-detail.md)  
**Status** : Spécifications complètes (desktop uniquement)

**Sous-spécifications** (dans `property-detail/`) :
- `offer-header.md` - En-tête avec titre, localisation, prix, badges
- `offer-actions.md` - Barre d'actions (surfaces, brochure, visite)
- `consultant-card.md` - Carte consultant avec contact
- `description.md` - Description longue avec read-more
- `specs-sections.md` - Équipements, Services, État bâtiment
- `energy.md` - DPE et GES
- `surface-table.md` - Tableau des lots/surfaces
- `location.md` - Adresse, transports, carte
- `poi-filters.md` - Filtres points d'intérêt sur carte
- `travel-time.md` - Calculateur temps de trajet
- `gallery-modal.md` - Galerie photos modale
- `similar-properties.md` - Carrousel biens similaires

### ✅ Recherche (Search Results)
**Fichier** : [`design/pages/search-results.md`](design/pages/search-results.md)  
**Status** : Spécifications complètes responsive (desktop + mobile)

**Sous-spécifications** (dans `search-results/`) :
- `mobile-navigation.md` - Top bar fixe + actions bar sticky (mobile uniquement)
- `filters-sort.md` - Barre filtres sidebar + dropdown tri (responsive)
- `results-cards.md` - Liste résultats + cartes individuelles (responsive)
- `map-distance.md` - Carte interactive + widget zone distance (responsive)
- `comparator.md` - Panel comparaison 3 biens (responsive)
- `calculator-banner.md` - Bannière calculateur loyer (responsive)

### ✅ Compte (Account)
**Fichier** : [`design/pages/account.md`](design/pages/account.md)  
**Status** : Spécifications complètes responsive (desktop + mobile)

**Sous-spécifications** (dans `account/`) :
- `navigation.md` - Sidebar desktop + hub menu mobile (responsive)
- `profile.md` - Vue/édition profil + changement mot de passe (responsive)
- `favorites.md` - Grille desktop + liste mobile (responsive)
- `alerts.md` - Liste alertes (responsive)
- `support.md` - Bannière support + déconnexion (responsive)

---

## 🏗️ Structure par niveau

### **Atoms (Elements)** - 25 composants
Briques de base : buttons, badges, inputs, headings, icons, images, etc.  
→ Voir : [`atomic/elements.md`](atomic/elements.md)

### **Molecules (Components)** - 24 composants
Unités fonctionnelles : cards, form fields, breadcrumbs, tabs, dropdowns, etc.  
→ Voir : [`atomic/components.md`](atomic/components.md)

### **Organisms (Collections)** - 15 composants
Sections complexes : header, footer, hero, carousel, accordion, modals, data tables, etc.  
→ Voir : [`atomic/collections.md`](atomic/collections.md)

### **Templates (Layouts)** - 10 templates
Structures de page : base page, article, landing, grid, two-column, three-column, etc.  
→ Voir : [`atomic/layouts.md`](atomic/layouts.md)

### **Pages** - 12 pages complètes
Exemples : homepage, property listing, property detail, contact, about, services, etc.  
→ Voir : [`atomic/pages.md`](atomic/pages.md)

---

## 📊 Par cas d'usage

### **"Comment implémenter un composant ?"**
1. Identifier dans [`atomic/`](atomic/)
2. Lire spec détaillée dans [`design/pages/`](design/pages/) si applicable
3. Suivre `.github/instructions/02-component-development.md`
4. Appliquer `.github/instructions/03-technical-implementation.md`

### **"Que développer en premier ?"**
→ [`ROADMAP.md`](ROADMAP.md) - Phase 1 Semaine 1  
**Ordre prioritaire** :
1. Header (organism)
2. Navigation (organism)
3. Footer (organism)
4. Input (atom)
5. Select (atom)

### **"Quels composants pour la recherche ?"**
→ [`ROADMAP.md`](ROADMAP.md) - Phase 2
→ [`design/pages/search-results.md`](design/pages/search-results.md) - Vue d'ensemble
→ [`design/pages/search-results/`](design/pages/search-results/) - Specs détaillées
→ Voir inventaire dans [`atomic/collections.md`](atomic/collections.md)

### **"Comment sont liés les composants ?"**
→ [`ROADMAP.md`](ROADMAP.md) - Section "Critical Dependencies"  
→ Voir dépendances par phase

---

## 🎓 Parcours d'apprentissage

### **Parcours 1 : Compréhension complète (1-2h)**
1. Lire `README.md` (5 min) - Vue d'ensemble
2. Parcourir `ROADMAP.md` (15 min) - Structure développement
3. Explorer `atomic/elements.md`, `components.md`, `collections.md` (30 min)
4. Consulter exemples `design/pages/` (30 min)
5. Voir `maquettes/` (15 min) - Designs actuels

### **Parcours 2 : Démarrage rapide (30 min)**
1. Lire `README.md` (5 min)
2. Consulter `ROADMAP.md` Phase 1 (10 min)
3. Explorer un composant existant (Button/Card) (10 min)
4. Lire `.github/instructions/02-component-development.md` (5 min)

### **Parcours 3 : Développement immédiat (15 min)**
1. Trouver votre composant dans `ROADMAP.md` Phase 1/2
2. Lire spec dans `atomic/` ou `design/pages/`
3. Commencer avec `.github/instructions/`

### **Parcours 4 : Prise de décision (20 min)**
1. Lire `ROADMAP.md` toutes phases (10 min)
2. Consulter dépendances (5 min)
3. Décider phase/priorité (5 min)

---

## 🔍 Conseils de recherche

### **Par nom de composant**
- Button → `atomic/elements.md` + `.github/instructions/`
- Card → `atomic/components.md` + `source/patterns/components/card/`
- Hero → `atomic/collections.md` + `source/patterns/collections/hero/`

### **Par page**
- Offre → `design/pages/property-detail.md`
- Recherche → `design/pages/search-results.md`
- Compte → `design/pages/account.md`

### **Par phase de développement**
- Phase 1 (MVP) → `ROADMAP.md` - 18 composants, 2-3 semaines
- Phase 2 (Avancé) → `ROADMAP.md` - 15 composants, 2 semaines
- Phase 3 (Utilitaire) → `ROADMAP.md` - 12 composants, 1-2 semaines
- Phase 4 (Pages) → `ROADMAP.md` - 22 pages/templates, 1 semaine

### **Par dépendance**
- Qui utilise Button ? → Presque toutes les sections
- Qui utilise Card ? → Recherche, offres, actualités
- Qui utilise Accordion ? → FAQ, specs bien

---

## 📈 Tableau de bord progression

**Dernière mise à jour** : 13 décembre 2025
**Dernière mise à jour** : 17 décembre 2025

```
Composants documentés :   86/86 (100%) ✅
  ├─ Elements              25/25 (100%) ✅
  ├─ Components            24/24 (100%) ✅
  ├─ Collections           15/15 (100%) ✅
  ├─ Layouts               10/10 (100%) ✅
  └─ Pages                 12/12 (100%) ✅

Composants implémentés :   20/86 (23%) 🔄
  ├─ Elements              13/25 (52%) ⏳
  ├─ Components             5/24 (21%) 🔄
  ├─ Collections            2/15 (13%) 🔄
  ├─ Layouts                0/10 (0%)  ❌
  └─ Pages                  0/12 (0%)  ❌

Pages spécifiées :          3/12 (25%) ⏳
  ├─ Offre                  ✅
  ├─ Recherche              ✅
  ├─ Compte                 ✅
  └─ Homepage, etc.         ⏳
```

---

## 🔗 Ressources externes

- **Storybook démo** : [Surface Storybook](https://dev-ucla-surface-training.pantheonsite.io/themes/custom/surface/storybook/)
- **Instructions développement** : `.github/instructions/` (6 fichiers numérotés)
- **Copilot instructions** : `.github/copilot-instructions.md`

---

**Mainteneurs** : Design System Team  
**Support** : Voir README projet pour canaux
