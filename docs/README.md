# Documentation PS Theme

**Version** : 4.0.0  
**Dernière mise à jour** : 2025-12-12  
**Projet** : Thème Drupal 10/11 pour BNP Paribas Real Estate

---

## 📚 Navigation

### 🎯 [01. Présentation](./01-presentation/)
Vue d'ensemble du projet, architecture technique, méthodologie Atomic Design + Token-First.

**Commencer ici** si vous découvrez le projet.

### 🧩 [02. Composants](./02-composants/)
Spécifications complètes des **87 composants** organisés par niveau Atomic Design.

- **[01-atomes/](./02-composants/01-atomes/)** – 19 composants élémentaires
- **[02-molecules/](./02-composants/02-molecules/)** – 20 composants composés
- **[03-organismes/](./02-composants/03-organismes/)** – 12 composants complexes
- **[04-templates/](./02-composants/04-templates/)** – 8 structures de page
- **[05-pages/](./02-composants/05-pages/)** – 8 pages complètes

**Progression** : 6/87 composants implémentés (7%)

### 🎨 [03. Design Tokens](./03-tokens/)
Système complet de design tokens : couleurs, espacements, typographie, ombres, animations.

Référence obligatoire pour **tous les développements**.

### 🛠️ [04. Guide de développement](./04-guide-developpement/)
Guide pratique pour créer et maintenir des composants conformes aux standards v4.0.0.

- Démarrage rapide
- Workflow de création
- Composition Token-First
- Tests et qualité

### 📝 [05. Changelog](./05-changelog/)
Historique chronologique de toutes les implémentations et évolutions du système.

### 📦 [06. Ressources](./06-ressources/)
Maquettes Figma, références externes, assets du projet.

---

## 🚀 Démarrage rapide

### Pour développer un nouveau composant

1. **Lire la spec** → `docs/02-composants/{niveau}/{composant}.md`
2. **Consulter le guide** → `docs/04-guide-developpement/creer-composant.md`
3. **Utiliser les prompts AI** → `.github/prompts/create-{atom|molecule|organism}.md`
4. **Valider** → `.github/prompts/audit-component.md`

### Pour comprendre le système

1. **Architecture** → `docs/01-presentation/architecture.md`
2. **Méthodologie** → `docs/01-presentation/methodologie.md`
3. **Glossaire** → `docs/01-presentation/glossaire.md`

### Pour consulter les tokens

1. **Vue d'ensemble** → `docs/03-tokens/README.md`
2. **Couleurs** → `docs/03-tokens/couleurs.md`
3. **Espacements** → `docs/03-tokens/espacements.md`
4. **Typographie** → `docs/03-tokens/typographie.md`

---

## 📖 Documentation technique

La documentation technique détaillée (instructions de développement, workflows, standards) se trouve dans :

**`.github/instructions/`** (6 fichiers consolidés v4.0.0)

1. `01-core-principles.md` – Fondations du système
2. `02-component-development.md` – Workflow de création complet
3. `03-technical-implementation.md` – Standards de code
4. `04-quality-assurance.md` – Validation et tests
5. `05-maintenance.md` – Évolution et maintenance
6. `README.md` – Hub de navigation

---

## 🤖 Prompts AI

Bibliothèque de 13 prompts prêts à l'emploi pour accélérer le développement :

**`.github/prompts/`**

- Création : `create-atom.md`, `create-molecule.md`, `create-organism.md`
- Qualité : `audit-component.md`, `fix-component.md`, `find-issues.md`, `standardize-legacy.md`
- Maintenance : `create-token.md`, `refactor-css.md`, `update-storybook.md`
- Analyse : `analyze-project.md`, `check-accessibility.md`

---

## 🎓 Parcours d'apprentissage

### Niveau 1 : Découverte (1-2h)
1. Lire `docs/01-presentation/README.md`
2. Parcourir `docs/02-composants/README.md`
3. Explorer Storybook : http://localhost:6006

### Niveau 2 : Compréhension (2-4h)
1. Étudier `docs/01-presentation/methodologie.md`
2. Lire `.github/instructions/01-core-principles.md`
3. Analyser un composant de référence : `button`, `badge`, `avatar`

### Niveau 3 : Pratique (1 semaine)
1. Créer un atome simple avec `create-atom.md`
2. Auditer avec `audit-component.md`
3. Créer une molécule avec Token-First
4. Contribuer au système

---

## 📊 État du projet

- **Composants implémentés** : 6/87 (7%)
- **Design tokens** : 100+ tokens (couleurs, tailles, typographie, etc.)
- **Storybook** : 6 composants documentés
- **Tests** : Conformité audit 100 points
- **Accessibilité** : WCAG 2.2 AA

---

## 🔗 Liens utiles

- **Storybook (dev)** : http://localhost:6006
- **Storybook (prod)** : https://dev-ucla-surface-training.pantheonsite.io/themes/custom/surface/storybook/
- **Repository** : (lien GitHub si applicable)
- **Figma** : (lien vers les maquettes)

---

## 📞 Support

Pour toute question ou problème :

1. Consulter `docs/04-guide-developpement/`
2. Vérifier `.github/instructions/`
3. Utiliser les prompts AI (`.github/prompts/`)
4. Contacter l'équipe Design System

---

**Mainteneurs** : Équipe Design System BNP Paribas Real Estate  
**Licence** : Propriétaire
