# PS Theme Instructions - Guide de Navigation

**Version**: 4.0.0 (Restructuration majeure)  
**Dernière mise à jour**: 2025-12-12  
**Backup disponible**: `.github-backup-2025-12-12/` (commit 0a2cbf8)

---

## 🎯 Vue d'ensemble

Documentation consolidée du système de design PS Theme en **6 fichiers numérotés** pour une navigation intuitive et progressive.

**Architecture** : 01 (Fondations) → 02 (Workflow) → 03 (Technique) → 04 (Validation) → 05 (Évolution)

---

## 🗺️ Navigation Rapide

### 🆕 Je découvre le projet

→ Commencez par **[01-core-principles.md](01-core-principles.md)**

**Vous apprendrez** :
- Philosophie du design system (Atomic Design, Token-First)
- Méthodologie Atomic Design (5 niveaux : Atoms → Pages)
- Convention BEM (Block, Element, Modifier)
- Système de tokens (3 couches)
- Principe du markup minimal
- Terminologie standard
- Politique d'accessibilité (WCAG 2.2 AA)

**Temps de lecture** : 30-45 minutes

---

### 🏗️ Je crée un composant

→ Suivez **[02-component-development.md](02-component-development.md)**

**Workflow complet** (11 étapes) :
1. Lire la spec design
2. Vérifier les dépendances
3. Vérifier les tokens
4. Créer les 5 fichiers
5. Implémenter le Twig
6. Implémenter le CSS avec nesting
7. Créer le YAML
8. Créer les stories Storybook
9. Créer le README
10. Valider le build
11. Commiter + changelog

**Includes** :
- Workflow Token-First (4 étapes : params → utils → tokens ⭐ → CSS)
- Exemple réel complet (Card Offer Search)
- Checklist pré-commit

**Temps de lecture** : 45-60 minutes  
**Temps d'implémentation** : 2-4 heures par composant

---

### 💻 J'ai besoin d'une référence technique

→ Consultez **[03-technical-implementation.md](03-technical-implementation.md)**

**5 sections techniques** :

1. **CSS Standards** (Design tokens, nesting, variables)
   - Système de tokens 3 couches
   - Zéro valeur hardcodée (règle absolue)
   - CSS nesting avec `&` (obligatoire)
   - Variables component-scoped (Layer 2)
   - Ordre de cascade
   - Focus-visible (tous les interactifs)

2. **Twig & YAML Standards** (Templates, données)
   - Compatibilité Drupal (NO arrow functions, NO .map()/.filter())
   - Pattern de classes avec ternaire + null
   - Markup minimal (pas de classes par défaut)
   - Composition avec `{% include %}` + `only`
   - Contexte Real Estate

3. **Storybook Standards** (Documentation)
   - Édition HTML/Vite (PAS React)
   - `tags: ['autodocs']` (OBLIGATOIRE)
   - ArgTypes catégorisés (6 catégories)
   - Stories structure (Default + Showcases)
   - Langue : Anglais (docs) / Français (démo)

4. **JavaScript Standards** (Comportements)
   - Modules ES6
   - Drupal behaviors avec `once()`
   - Patterns d'accessibilité
   - Intégration Storybook

5. **Accessibility Standards** (WCAG 2.2 AA)
   - Ratios de contraste (4.5:1 texte, 3:1 UI)
   - Navigation clavier
   - ARIA patterns
   - Tests screen reader

**Temps de lecture** : 60-90 minutes  
**Usage** : Document de référence quotidien

---

### ✅ Je valide mon travail

→ Utilisez **[04-quality-assurance.md](04-quality-assurance.md)**

**Outils de validation** :

1. **Audit de conformité** (Checklist 100 points)
   - Minimum 90/100 pour production
   - 8 catégories : Architecture, Files, Twig, CSS, Storybook, YAML, README, BEM

2. **Validation de build**
   ```bash
   npm run build  # Doit passer (0 erreurs)
   npm run watch  # Vérification visuelle
   ```

3. **Guide de dépannage** (9+ erreurs communes)
   - Token introuvable
   - Valeur hardcodée
   - Syntaxe CSS nesting
   - Arrow function dans Twig
   - Autodocs manquant
   - React/JSX (mauvaise édition)
   - etc.

4. **Flowcharts de décision**
   - Niveau de composant (Atom vs Molecule vs Organism)
   - Méthode de composition (include vs embed vs extend)
   - Usage de tokens (existant vs nouveau)
   - Stratégie CSS override (Token-First 4 étapes)

5. **Checklist de tests**
   - Visuel (responsive, états, icônes)
   - Fonctionnel (interactions, clavier)
   - Accessibilité (contraste, focus, ARIA, screen reader)
   - Cross-browser (Chrome, Firefox, Safari, Edge)

**Temps de lecture** : 30-45 minutes  
**Usage** : Avant chaque commit

---

### 🔧 Je crée des tokens ou migre du code legacy

→ Voir **[05-maintenance.md](05-maintenance.md)**

**Processus de création de tokens** (4 étapes) :
1. Documenter le besoin (README du composant)
2. Créer une issue de proposition (template fourni)
3. Revue par l'équipe design
4. Implémentation après approbation

**Catalogue de patterns legacy** (10 patterns à migrer) :
- CSS flat (pas de nesting) → PostCSS nesting
- Valeurs hardcodées → Tokens
- Arrow functions Twig → Ternaire + null
- Focus-visible manquant → Ajout obligatoire
- Autodocs manquant → tags: ['autodocs']
- Ordre de cascade incorrect → Base → Modifiers
- Variables component-scoped manquantes → Layer 2
- etc.

**Workflows de migration** :
- Refactoring de composants legacy
- Conversion flat CSS → nested
- Conversion hardcoded → tokens
- Conversion arrow functions → ternaire

**Cycle de dépréciation** (3 phases) :
1. Annonce (Jour 0)
2. Période de migration (3 mois standard)
3. Suppression (Après 3 mois)

**Gestion des breaking changes** :
- Semantic versioning
- Documentation CHANGELOG
- Guide de migration
- Plan de rollback

**Temps de lecture** : 45-60 minutes  
**Usage** : Périodique (évolution du système)

---

## 📂 Structure des Fichiers

```
.github/instructions/
├── 01-core-principles.md          # Fondations (Lire une fois)
├── 02-component-development.md    # Workflow (Utiliser quotidiennement)
├── 03-technical-implementation.md # Référence (Consulter souvent)
├── 04-quality-assurance.md        # Validation (Avant commit)
├── 05-maintenance.md              # Évolution (Périodique)
└── README.md                      # Ce fichier (Navigation)
```

---

## 🎯 Scénarios Courants

| Je veux... | Fichier(s) à lire | Temps estimé |
|-----------|-------------------|--------------|
| **Créer un atom** | 01 (principes) → 02 (workflow) → 03 (technique) | 3-4h |
| **Créer une molecule** (compose des atoms) | 02 (Token-First) → 03 (CSS) → 04 (valider) | 4-6h |
| **Corriger une erreur de build** | 04 (troubleshooting) | 15-30min |
| **Décider du niveau atomique** | 04 (decision flowcharts) | 5-10min |
| **Ajouter un nouveau token** | 05 (token creation) | 2-5 jours |
| **Migrer un composant legacy** | 05 (migration workflows) + 04 (audit) | 2-4h |
| **Implémenter l'accessibilité** | 03 (accessibility) → 04 (testing) | 1-2h |
| **Écrire des stories Storybook** | 03 (storybook standards) | 30-60min |
| **Débugger un template Twig** | 03 (twig standards) → 04 (twig errors) | 15-45min |
| **Comprendre la composition** | 01 (atomic design) + 02 (Token-First) | 30-45min |

---

## 📊 Progression d'Apprentissage

### Débutant (Semaine 1)

- [ ] Lire 01-core-principles.md (comprendre les fondations)
- [ ] Lire 02-component-development.md (mémoriser le workflow)
- [ ] Créer 1 atom simple (badge, divider, icon)
- [ ] Audit 100 points du premier atom (score ≥ 90)

### Intermédiaire (Semaine 2-3)

- [ ] Lire 03-technical-implementation.md (référence technique)
- [ ] Créer 1 molecule (card, alert, form-field)
- [ ] Appliquer Token-First workflow
- [ ] Passer tous les tests (build, visual, a11y)

### Avancé (Mois 1+)

- [ ] Lire 05-maintenance.md (évolution du système)
- [ ] Créer 1 organism (header, footer, grid)
- [ ] Proposer 1 nouveau token
- [ ] Migrer 1 composant legacy

---

## 🔄 Changements de cette Version

### v4.0.0 - Restructuration Majeure (2025-12-12)

**BREAKING CHANGE** : Réorganisation complète de `.github/instructions/`

**Avant** (v3.x) : 17+ fichiers fragmentés
```
atomic-design.instructions.md
components.instructions.md
css.instructions.md
templates.instructions.md
storybook.instructions.md
javascript.instructions.md
accessibility.instructions.md
workflows.instructions.md
composition-token-first.instructions.md
card-inheritance.instructions.md
CODE_EXAMPLES_STYLE_GUIDE.md
TERMINOLOGY.md
TOKEN_CREATION_PROCESS.md
TROUBLESHOOTING_GUIDE.md
DECISION_FLOWCHARTS.md
MIGRATION_GUIDES.md
base-stories.instructions.md
core.instructions.md
icon-system.instructions.md
multi-expert-mode.instructions.md
card-inheritance-prompt.md
```

**Après** (v4.0.0) : 6 fichiers consolidés numérotés
```
01-core-principles.md          (Fondations)
02-component-development.md    (Workflow)
03-technical-implementation.md (Technique)
04-quality-assurance.md        (Validation)
05-maintenance.md              (Évolution)
README.md                      (Navigation)
```

**Bénéfices** :
- ✅ Réduction de 66% du nombre de fichiers (17 → 6)
- ✅ Hiérarchie claire (01 fondations → 05 avancé)
- ✅ Numérotation intuitive et progression logique
- ✅ Élimination des redondances de contenu
- ✅ Toute l'information technique préservée
- ✅ Navigation et découvrabilité améliorées

**Migration** :
- Anciens fichiers sauvegardés : `.github-backup-2025-12-12/` (commit 0a2cbf8)
- Rollback si nécessaire : `cp -r .github-backup-2025-12-12/ .github/`
- `copilot-instructions.md` mis à jour avec références v4.0.0

**Contexte** : Feedback utilisateur - structure trop fragmentée, trop de fichiers, organisation peu claire

---

## 🔧 Commandes Utiles

```bash
# Créer un nouveau composant (génération interactive)
npm run generate:pattern

# Vérifier l'existence d'un token
npm run tokens:check -- --token-name

# Valider le build (obligatoire avant commit)
npm run build

# Watch mode (Vite + Storybook)
npm run watch
# → http://localhost:6006

# Build Storybook statique
npm run storybook:build
```

---

## 🆘 Support & Ressources

### Documentation Externe

- **Storybook Live** : [Surface Storybook](https://dev-ucla-surface-training.pantheonsite.io/themes/custom/surface/storybook/)
- **Design Specs** : `docs/design/` (spécifications complètes des 87 composants)
- **Project Status** : `docs/ps-design/INDEX.md` (inventaire + phases)
- **Changelog** : `docs/ps-design/CHANGELOG.md` (historique d'implémentation)

### Contact

- **Canal Slack** : #design-system
- **Issues GitHub** : Propositions de tokens, bugs, questions
- **Maintainers** : Design System Team

---

## 📏 Statistiques

**Composants** : 6/87 (7%)
- 19 Atoms (éléments)
- 20 Molecules (composants)
- 12 Organisms (collections)
- 8 Templates (layouts)
- 8 Pages (pages)

**Version actuelle** : 4.0.0  
**Standards** : Atomic Design + Token-First + BEM + WCAG 2.2 AA  
**Stack** : Storybook (HTML) + Vite + PostCSS + Drupal 10/11

---

## 🎓 Apprentissage Continu

**Ressources recommandées** :
- Brad Frost - Atomic Design : https://atomicdesign.bradfrost.com/
- BEM Methodology : https://en.bem.info/methodology/
- WCAG 2.2 Guidelines : https://www.w3.org/WAI/WCAG22/quickref/
- Drupal Twig : https://www.drupal.org/docs/theming-drupal/twig-in-drupal

**Mise à jour de la documentation** :
- Revue trimestrielle (ou sur changement majeur)
- Feedback continu via issues GitHub
- Propositions d'amélioration bienvenues

---

**Version** : 4.0.0  
**Date** : 2025-12-12  
**Maintainers** : Design System Team  
**Licence** : Projet interne BNP Paribas Real Estate
