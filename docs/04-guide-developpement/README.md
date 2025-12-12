# Guide de Développement

**Documentation pratique pour développer avec PS Theme**

---

## 🎯 Vue d'ensemble

Cette section contient les guides pratiques pour développer, composer et valider les composants PS Theme selon les standards du projet.

---

## 📚 Contenu

### 1. [Démarrage rapide](./demarrage-rapide.md)
**15 minutes** | Installation, configuration, premier composant

- Prérequis et installation
- Configuration de l'environnement
- Commandes essentielles
- Premier composant (création guidée)
- Storybook et développement

### 2. [Créer un composant](./creer-composant.md)
**Guide complet** | Workflow en 11 étapes du design token à la validation

- Workflow Token-First (étapes 1-11)
- Structure des 5 fichiers obligatoires
- Standards CSS/Twig/YAML/Storybook
- Composition de composants existants
- Validation et commit

### 3. [Composition de composants](./composition.md)
**Méthodologie Token-First** | Comment composer sans modifier les parents

- Token-First Composition Workflow (3 STEPs)
- Cascade des tokens (Layer 1 → Layer 2 → Layer 3)
- Composition d'atoms (badge + icon, card + button)
- Patterns de composition avancés
- Anti-patterns à éviter

### 4. [Tests et qualité](./tests-qualite.md)
**Audit 100 points** | Validation complète avant commit

- Checklist de conformité (100 points)
- Build et tests automatisés
- Validation accessibilité (WCAG 2.2 AA)
- Troubleshooting (15 erreurs courantes)
- Outils de validation

---

## 🚀 Parcours d'apprentissage

### Niveau 1 : Débuter (1 jour)
1. [Démarrage rapide](./demarrage-rapide.md) → Comprendre l'environnement
2. [Tests et qualité](./tests-qualite.md) → Valider le build
3. Consulter [02-composants/](../02-composants/) → Voir les 6 composants implémentés

### Niveau 2 : Développer (1 semaine)
1. [Créer un composant](./creer-composant.md) → Workflow complet
2. Étudier référence : `source/patterns/elements/button/` (100/100)
3. Implémenter un atom simple (ex: divider, spinner)
4. [Tests et qualité](./tests-qualite.md) → Audit 100%

### Niveau 3 : Composer (2 semaines)
1. [Composition de composants](./composition.md) → Token-First
2. Étudier référence : `source/patterns/components/card/` (molecule)
3. Implémenter une molecule (ex: card, form-field)
4. Utiliser [.github/prompts/](../../.github/prompts/) → Prompts AI

---

## 🛠️ Outils et ressources

### Scripts disponibles

```bash
# Développement
npm run watch                # Storybook + Hot reload
npm run build                # Compile + Lint + Format
npm run storybook:build      # Build statique Storybook

# Génération
npm run generate:pattern     # Scaffolding interactif
npm run build:icons          # Génère sprite SVG

# Utilitaires
npm run tokens:check -- <nom> # Recherche token
```

### VS Code snippets

Taper `ps` + `TAB` dans les fichiers :

**Twig** : `psheader`, `psclasses`, `psinclude`, `psdefault`  
**CSS** : `pscomponent`, `pselement`, `psmodifier`  
**Storybook** : `psstory`, `psargtype`  
**Markdown** : `psreadme`

### Prompts AI

Consultez [.github/prompts/](../../.github/prompts/) pour les prompts qualité :

- `create-component.md` → Créer un nouveau composant
- `audit-component.md` → Auditer un composant existant
- `debug-build.md` → Résoudre erreurs de build
- `token-search.md` → Rechercher/documenter tokens
- `refactor-legacy.md` → Standardiser composant legacy

---

## 📋 Standards du projet

### Méthodologie

**Atomic Design** : 5 niveaux (Atoms → Molecules → Organisms → Templates → Pages)  
**Token-First** : Tous les styles via design tokens (`var(--token)`)  
**BEM** : Nomenclature `.ps-component__element--modifier`  
**Composition** : Réutilisation via `{% include %}` avec Token-First cascade

### Règles Zero Tolerance

❌ **JAMAIS** :
- Valeurs hardcodées (`16px`, `#00915A`, `0.3s`)
- Fichier manquant (5 obligatoires : `.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md`)
- `tags: ['autodocs']` manquant (Storybook export default)
- Arrow functions en Twig (`filter(v => v)`)
- Modifier composant parent directement (utiliser Token-First)
- Noms de couleurs au lieu de sémantiques (`green` → `success`)
- Préfixe `icon-` dans le code (`data-icon="icon-check"` → `data-icon="check"`)

✅ **TOUJOURS** :
- Design tokens pour TOUS les styles
- 5 fichiers complets + validés
- Autodocs activé (Storybook)
- Ternaires Twig (`condition ? 'class' : null`)
- Token-First pour composition (override tokens, pas CSS)
- Couleurs sémantiques (`--primary`, `--success`, `--danger`)
- Noms d'icônes sans préfixe (auto-ajouté par CSS)

---

## 🎓 Composants de référence

Étudiez ces implémentations parfaites (100/100) :

| Composant | Niveau | Pourquoi étudier |
|-----------|--------|------------------|
| **Button** | Atom | CSS nesting, tous états, stories complètes |
| **Avatar** | Atom | Markup minimal, sizing adaptatif, fallback SVG |
| **Badge** | Atom | Couleurs sémantiques, pill, intégration icon |
| **Icon** | Atom | Système de sprite SVG, sizing, couleurs |
| **Divider** | Atom | Simplicité, variants orientation, code minimal |
| **Link** | Atom | États, external links, icons, accessibilité |

---

## 📞 Support

### Documentation technique

- **Instructions complètes** : [.github/instructions/](../../.github/instructions/) (v4.0.0)
- **Principes fondamentaux** : `01-core-principles.md`
- **Développement composants** : `02-component-development.md`
- **Standards techniques** : `03-technical-implementation.md`
- **Assurance qualité** : `04-quality-assurance.md`
- **Maintenance** : `05-maintenance.md`

### Troubleshooting

1. **Erreur de build ?** → [Tests et qualité](./tests-qualite.md) section "Troubleshooting"
2. **Token manquant ?** → `npm run tokens:check -- --token-name`
3. **Prompt AI ?** → [.github/prompts/debug-build.md](../../.github/prompts/debug-build.md)
4. **Référence composant ?** → Consulter `source/patterns/elements/button/`

---

**Navigation** : [← 03 Tokens](../03-tokens/) | [Démarrage rapide →](./demarrage-rapide.md)
