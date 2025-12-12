# Présentation du projet PS Theme

**Vue d'ensemble du système de design et de l'architecture technique**

---

## 📋 Contenu de cette section

### [architecture.md](./architecture.md)
Architecture technique complète du projet :
- Stack technologique (Drupal 10/11, Storybook, Vite, PostCSS)
- Structure des fichiers
- Système de build
- Intégration Drupal

### [methodologie.md](./methodologie.md)
Méthodologie de développement :
- Atomic Design (Brad Frost)
- Token-First Composition Workflow
- BEM (Block Element Modifier)
- Approche Mobile-First
- WCAG 2.2 AA

### [glossaire.md](./glossaire.md)
Terminologie française normalisée du projet :
- Termes techniques traduits
- Conventions de nommage
- Vocabulaire métier immobilier

---

## 🎯 Qu'est-ce que PS Theme ?

**PS Theme** (anciennement "Surface") est le **système de design** et **thème Drupal personnalisé** pour **BNP Paribas Real Estate**.

### Objectifs

1. **Cohérence visuelle** : Interface unifiée sur tous les sites immobiliers
2. **Efficacité** : Composants réutilisables accélérant le développement
3. **Accessibilité** : Conformité WCAG 2.2 niveau AA garantie
4. **Maintenabilité** : Architecture Token-First facilitant les évolutions
5. **Qualité** : Standards stricts avec audit 100 points

### Principes fondamentaux

#### 1. Atomic Design
Hiérarchie à 5 niveaux :
- **Atomes** (19) : Éléments de base (button, badge, icon)
- **Molécules** (20) : Combinaisons simples (card, form-field)
- **Organismes** (12) : Sections complexes (header, footer, grid)
- **Templates** (8) : Structures de page
- **Pages** (8) : Implémentations complètes

#### 2. Token-First
Tous les styles utilisent des **design tokens** :
```css
/* ❌ Hardcodé */
background: #00915A;
padding: 16px;

/* ✅ Token-First */
background: var(--primary);
padding: var(--size-4);
```

#### 3. BEM Strict
Nommage normalisé avec préfixe `ps-` :
```css
.ps-button { }              /* Block */
.ps-button__icon { }        /* Element */
.ps-button--primary { }     /* Modifier */
```

#### 4. Composition intelligente
Workflow en 4 étapes pour composer des composants :
1. Paramètres natifs
2. Classes utilitaires
3. **Surcharge de tokens** ⭐ (approche préférée)
4. CSS ciblé (dernier recours)

---

## 📊 État actuel

### Progression globale
- **6/87 composants** implémentés (7%)
- **100+ design tokens** définis
- **6 composants** documentés dans Storybook
- **Standards v4.0.0** consolidés

### Composants implémentés
✅ **Atomes** (6/19)
- badge
- button
- icon
- avatar
- divider
- link

### Phases suivantes
📋 **Phase 4** : Molécules de base (card, form-field, alert)  
📋 **Phase 5** : Organismes (header, footer, navigation)  
📋 **Phase 6** : Templates et pages

---

## 🏗️ Architecture en bref

### Stack technique
```
Drupal 10/11 (CMS)
├── Twig (templating)
├── YAML (données mock)
└── Behaviors (JavaScript)

Storybook HTML Edition (dev)
├── Vite (build + dev server)
├── PostCSS (CSS processing)
└── @faker-js/faker (données réalistes)

Design Tokens (source/props/)
├── colors.css (palette + sémantiques)
├── sizes.css (0-32, échelle 0.25rem)
├── fonts.css (tailles + poids)
└── 7 autres fichiers
```

### Structure des composants
Chaque composant = **5 fichiers obligatoires** :
```
button/
├── button.twig        # Template Drupal
├── button.css         # Styles (tokens uniquement)
├── button.yml         # Données mock (Real Estate)
├── button.stories.jsx # Storybook (Autodocs)
└── README.md          # Documentation
```

---

## 🎨 Identité visuelle

### Couleurs principales
- **Primary** : Vert `#00915A` (actions principales, brand)
- **Secondary** : Rose `#A12B66` (actions secondaires, accents)
- **Success** : Teal `#198754` (confirmations)
- **Danger** : Rouge `#EB3636` (erreurs, destructions)
- **Info** : Bleu `#2563EB` (informations)
- **Warning** : Jaune `#FBBF24` (avertissements)
- **Gold** : Or `#D1AE6E` (premium, highlights)

### Typographie
- **Font principale** : BNP Paribas Sans (si disponible) / Open Sans (fallback)
- **Échelle** : Ratio 1.2 (0.694rem → 2.488rem, 9 tailles)
- **Poids** : 400 (normal), 600 (semibold), 700 (bold)

### Espacements
- **Échelle** : 0 → 32 (incrément 0.25rem)
- **Base** : 16px = 1rem
- **Usage** : Padding, margin, gap (toujours via tokens)

---

## 📚 Ressources complémentaires

### Documentation technique
- `.github/instructions/` – 6 fichiers consolidés v4.0.0
- `.github/prompts/` – 13 prompts AI prêts à l'emploi

### Composants de référence
Implémentations parfaites à étudier :
- **button** : Tous les états, nesting parfait
- **badge** : Couleurs sémantiques, variants
- **avatar** : Sizing adaptatif, fallback SVG

### Outils de développement
- `npm run watch` – Dev server (Vite + Storybook)
- `npm run build` – Build production + validation
- `npm run generate:pattern` – Scaffolding composant
- `npm run tokens:check` – Recherche tokens

---

## 🚀 Prochaines étapes

1. **Lire** `architecture.md` pour comprendre le stack technique
2. **Étudier** `methodologie.md` pour maîtriser Atomic Design + Token-First
3. **Consulter** `glossaire.md` pour la terminologie normalisée
4. **Explorer** `../02-composants/` pour découvrir les spécifications

---

**Navigation** : [← Retour documentation](../README.md) | [Composants →](../02-composants/)
