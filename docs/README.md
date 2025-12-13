# PS Theme Documentation

Documentation complète du thème Drupal 10/11 pour BNP Paribas Real Estate.

---

## 🎯 Démarrage rapide

**Nouveau ?** Suivez ce parcours :

1. **Comprendre la structure** → Lisez [`INDEX.md`](INDEX.md) (10 min)
2. **Consulter les specs** → Explorez [`design/`](design/) pour les pages détaillées
3. **Voir les composants** → Parcourez [`atomic/`](atomic/) pour les inventaires
4. **Planifier le dev** → Consultez [`ROADMAP.md`](ROADMAP.md) pour les phases

---

## 📂 Structure de la documentation

```
docs/
├── README.md              # Ce fichier - Point d'entrée
├── INDEX.md               # Navigation complète de la doc
├── ROADMAP.md             # Plan de développement par phases
├── RESPONSIVE-GUIDE.md    # Guide méthodologique responsive
│
├── atomic/                # Inventaires Atomic Design
│   ├── README.md          # Navigation inventaires
│   ├── elements.md        # Atoms (25)
│   ├── components.md      # Molecules (24)
│   ├── collections.md     # Organisms (15)
│   ├── layouts.md         # Templates (10)
│   └── pages.md           # Pages (12)
│
├── design/                # Spécifications détaillées
│   ├── README.md          # Navigation specs
│   └── pages/             # Specs pages responsive
│       ├── property-detail/       # Page Offre
│       ├── search-results/        # Recherche desktop
│       ├── search-results-mobile/ # Recherche mobile
│       ├── account/               # Compte desktop
│       └── account-mobile/        # Compte mobile
│
└── maquettes/             # Maquettes design (8 images)
```

---

## 📚 Documentation par usage

### **Je développe un composant**
1. Vérifier l'inventaire : [`atomic/`](atomic/)
2. Lire la spec détaillée : [`design/pages/`](design/pages/) (si composant de page)
3. Suivre le workflow : `.github/instructions/02-component-development.md`
4. Appliquer les standards : `.github/instructions/03-technical-implementation.md`

### **Je prépare un sprint**
1. Consulter le plan : [`ROADMAP.md`](ROADMAP.md)
2. Identifier les dépendances : Voir "Critical Dependencies" dans ROADMAP
3. Prioriser : Phases 0 (fait) → Phase 1 (MVP) → Phase 2+

### **Je spécifie une page**
1. Lire le guide : [`RESPONSIVE-GUIDE.md`](RESPONSIVE-GUIDE.md)
2. Voir les exemples : [`design/pages/`](design/pages/)
3. Créer overview + sous-specs desktop/mobile

### **Je cherche une info**
1. Consulter l'index : [`INDEX.md`](INDEX.md)
2. Naviguer vers le fichier pertinent
3. Utiliser la recherche dans fichiers si besoin

---

## 🎨 Pages documentées

### ✅ Offre (Property Detail)
**Status** : Spécifications complètes  
**Desktop** : Header, actions, consultant, description, specs, énergie, surfaces, localisation, POI, trajets, galerie, similaires  
**Fichier** : [`design/pages/property-detail.md`](design/pages/property-detail.md)

### ✅ Recherche (Search Results)
**Status** : Spécifications complètes desktop + mobile  
**Desktop** : Filtres, tri, liste, carte, distance, comparateur  
**Mobile** : Top bar, actions, liste pile, tiroir comparateur, carte plein écran  
**Fichier** : [`design/pages/search-results/index.md`](design/pages/search-results/index.md)

### ✅ Compte (Account)
**Status** : Spécifications complètes desktop + mobile  
**Desktop** : Sidebar nav, profil (vue/édition), mot de passe, favoris grille, alertes, support  
**Mobile** : Hub nav, profil mobile, édition champ, favoris liste, support  
**Fichier** : [`design/pages/account/index.md`](design/pages/account/index.md)

### ⏳ Homepage
**Status** : À spécifier  
**Contenu** : Hero recherche, services, biens mis en avant, FAQ, actualités

---

## 📊 Métriques projet

**Composants** : 86 documentés
- Elements (Atoms) : 25 → 13 implémentés (52%)
- Components (Molecules) : 24 → 5 implémentés (21%)
- Collections (Organisms) : 15 → 2 implémentés (13%)
- Layouts (Templates) : 10 → 0 implémentés (0%)
- Pages : 12 → 0 implémentées (0%)

**Pages spécifiées** : 3/12 (Offre, Recherche, Compte)

**Progression globale** : ~15% implémenté

---

## 🔗 Liens externes

- **Storybook démo** : [Surface Storybook](https://dev-ucla-surface-training.pantheonsite.io/themes/custom/surface/storybook/)
- **Instructions dev** : `.github/instructions/` (6 fichiers numérotés)
- **Copilot instructions** : `.github/copilot-instructions.md`

---

## 🆘 Besoin d'aide ?

**Question sur la structure ?** → Lisez [`INDEX.md`](INDEX.md)  
**Question sur un composant ?** → Consultez [`atomic/`](atomic/)  
**Question sur une page ?** → Explorez [`design/pages/`](design/pages/)  
**Question sur le dev ?** → Voir `.github/instructions/02-component-development.md`  
**Question sur le responsive ?** → Lisez [`RESPONSIVE-GUIDE.md`](RESPONSIVE-GUIDE.md)

---

**Dernière mise à jour** : 13 décembre 2025  
**Mainteneurs** : Design System Team
