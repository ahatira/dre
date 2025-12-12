# Démarrage Rapide

**15 minutes pour être opérationnel sur PS Theme**

---

## 📋 Prérequis

### Logiciels requis

- **Node.js** : v18+ (LTS recommandé)
- **npm** : v9+ (inclus avec Node.js)
- **Git** : Version récente
- **Éditeur** : VS Code recommandé (snippets inclus)

### Vérifier l'installation

```bash
node --version  # v18.0.0 ou supérieur
npm --version   # v9.0.0 ou supérieur
git --version   # v2.0.0 ou supérieur
```

---

## ⚡ Installation (5 minutes)

### 1. Cloner le projet

```bash
git clone [URL_DU_PROJET] ps_theme
cd ps_theme
```

### 2. Installer les dépendances

```bash
npm install
```

**Durée** : ~2 minutes (dépend de la connexion)

### 3. Build initial

```bash
npm run build
```

**Durée** : ~1 minute  
**Vérification** : Le build doit terminer sans erreur

### 4. Lancer Storybook

```bash
npm run watch
```

**Ouverture automatique** : http://localhost:6006  
**Hot reload** : Activé (modifications visibles instantanément)

---

## 🎯 Commandes essentielles

### Développement quotidien

```bash
# Lancer Storybook + Hot reload (commande principale)
npm run watch

# Build complet (avant commit)
npm run build

# Build Storybook statique (déploiement)
npm run storybook:build
```

### Génération

```bash
# Créer un nouveau composant (interactif)
npm run generate:pattern

# Créer avec flags
npm run generate:pattern -- --type=element --name="Spinner"

# Générer sprite d'icônes
npm run build:icons
```

### Utilitaires

```bash
# Rechercher un token
npm run tokens:check -- --primary
npm run tokens:check -- --size-4

# Synchroniser libraries Drupal
npm run sync:libraries
```

---

## 🏗️ Structure du projet (5 minutes)

### Arborescence clé

```
ps_theme/
├── source/                      # 🎨 Code source
│   ├── patterns/                # Composants Storybook
│   │   ├── elements/            # Atoms (19)
│   │   ├── components/          # Molecules (20)
│   │   ├── collections/         # Organisms (12)
│   │   ├── layouts/             # Templates (8)
│   │   ├── pages/               # Pages (8)
│   │   └── base/                # Stories tokens (colors, fonts...)
│   ├── props/                   # 🎯 Design tokens (176)
│   │   ├── colors.css           # Palette (88 tokens)
│   │   ├── sizes.css            # Espacements (33+)
│   │   ├── fonts.css            # Typographie (60)
│   │   └── ...                  # Autres tokens
│   └── assets/                  # Ressources statiques
│       ├── icons/               # Sprite SVG généré
│       ├── fonts/               # BNPP Sans, Open Sans
│       └── images/              # Images
├── docs/                        # 📚 Documentation (vous êtes ici)
├── .github/                     # Configuration projet
│   ├── instructions/            # Documentation technique (v4.0.0)
│   └── prompts/                 # Prompts AI (13)
├── storybook/                   # 📦 Build statique Storybook
└── templates/                   # 🔧 Templates Drupal (.twig)
```

### Fichiers de configuration

| Fichier | Rôle |
|---------|------|
| `vite.config.js` | Build Vite (compilation CSS) |
| `postcss.config.js` | PostCSS (nesting, custom-media) |
| `biome.json` | Linter/Formatter (JS, JSON) |
| `ps.libraries.yml` | Libraries Drupal (CSS, JS) |
| `.storybook/` | Configuration Storybook |

---

## 🧩 Créer votre premier composant (5 minutes)

### Méthode rapide : Scaffolding

```bash
npm run generate:pattern
```

**Interactif** : Suivez les prompts :
1. Type : `element` (atom)
2. Nom : `Spinner`
3. Confirmation : `Y`

**Résultat** : 5 fichiers créés dans `source/patterns/elements/spinner/`

### Fichiers générés

```
source/patterns/elements/spinner/
├── spinner.twig         # Template Twig
├── spinner.css          # Styles CSS
├── spinner.yml          # Données mock
├── spinner.stories.jsx  # Stories Storybook
└── README.md            # Documentation
```

### Visualiser dans Storybook

1. Storybook recharge automatiquement (hot reload)
2. Navigation : **Elements > Spinner > Default**
3. Modifier `spinner.css` → Voir changements instantanés

---

## 📖 Storybook (Interface)

### Navigation

```
Storybook/
├── 📘 Documentation/         # Index, icônes, guides
├── 🎨 Base/                  # Colors, fonts, sizes...
├── 🧩 Elements/              # Atoms (badge, button, icon...)
├── 🏗️ Components/            # Molecules (card, form-field...)
├── 🏛️ Collections/           # Organisms (header, footer...)
├── 📐 Layouts/               # Templates (page layouts)
└── 📄 Pages/                 # Pages complètes
```

### Fonctionnalités

**Controls** : Modifier props en temps réel  
**Docs** : Documentation auto-générée (Autodocs)  
**Canvas** : Vue isolée du composant  
**Accessibility** : Tests a11y automatiques  
**Viewport** : Tester responsive (mobile, tablet, desktop)

---

## ✅ Vérifications (2 minutes)

### Checklist de démarrage

- [ ] `npm run build` termine sans erreur
- [ ] `npm run watch` ouvre Storybook (http://localhost:6006)
- [ ] Navigation Storybook fonctionne (Elements > Button)
- [ ] Hot reload actif (modifier `button.css` → changement visible)
- [ ] `npm run generate:pattern` crée les 5 fichiers

### Tester un composant existant

1. Ouvrir Storybook : http://localhost:6006
2. Naviguer : **Elements > Button > Default**
3. Tester **Controls** : Changer `variant` → `secondary`
4. Voir changement instantané

---

## 🚨 Troubleshooting

### Build échoue

```bash
# Nettoyer et réinstaller
rm -rf node_modules package-lock.json
npm install
npm run build
```

### Storybook ne démarre pas

```bash
# Vérifier le port 6006 (libre ?)
lsof -ti:6006 | xargs kill -9  # Tuer processus sur port 6006
npm run watch
```

### Hot reload ne fonctionne pas

1. Sauvegarder le fichier (`Ctrl+S`)
2. Vérifier la console Storybook (erreurs ?)
3. Recharger manuellement (`Ctrl+R`)

### Token non trouvé

```bash
# Rechercher token dans props/
npm run tokens:check -- --nom-du-token

# Ou manuellement
grep -r "--nom-du-token" source/props/
```

---

## 📚 Prochaines étapes

### Apprendre (1 semaine)

1. **Lire** : [01-presentation/](../01-presentation/) → Comprendre PS Theme
2. **Explorer** : [02-composants/](../02-composants/) → Voir les 6 composants implémentés
3. **Étudier** : `source/patterns/elements/button/` → Composant référence (100/100)
4. **Consulter** : [03-tokens/](../03-tokens/) → Système de design tokens

### Développer (2 semaines)

1. **Guide** : [Créer un composant](./creer-composant.md) → Workflow complet en 11 étapes
2. **Implémenter** : Un atom simple (ex: `divider`, `spinner`)
3. **Valider** : [Tests et qualité](./tests-qualite.md) → Audit 100/100
4. **Commiter** : Suivre format de commit (voir `.github/copilot-instructions.md`)

### Composer (1 mois)

1. **Méthodologie** : [Composition de composants](./composition.md) → Token-First Workflow
2. **Implémenter** : Une molecule (ex: `card`, `form-field`)
3. **Utiliser** : [Prompts AI](../../.github/prompts/) → Aide à la création
4. **Contribuer** : Participer aux reviews, améliorer les composants

---

## 🎓 Ressources

### Documentation technique

- **Instructions** : `.github/instructions/` (v4.0.0, 6 fichiers)
- **Prompts AI** : `.github/prompts/` (13 prompts qualité)
- **Composants** : `docs/02-composants/` (87 composants, 6 implémentés)
- **Tokens** : `docs/03-tokens/` (176 tokens documentés)

### Exemples de code

| Composant | Fichier | Pourquoi |
|-----------|---------|----------|
| Button | `source/patterns/elements/button/button.css` | CSS nesting, états, tokens |
| Badge | `source/patterns/elements/badge/badge.twig` | Template simple, composition |
| Card | `source/patterns/components/card/card.css` | Token-First, composition |

### Storybook démo

**URL de production** : [Surface Storybook](https://dev-ucla-surface-training.pantheonsite.io/themes/custom/surface/storybook/)

---

## 💬 Support

### Erreur bloquante ?

1. Consulter [Tests et qualité](./tests-qualite.md) → Section "Troubleshooting"
2. Utiliser prompt AI : `.github/prompts/debug-build.md`
3. Chercher dans logs : `npm run build` (erreurs détaillées)

### Question méthodologie ?

1. Lire [.github/instructions/](../../.github/instructions/) (documentation complète)
2. Consulter composants référence (button, badge, card)
3. Utiliser prompts AI adaptés (create-component, audit-component)

---

**Navigation** : [← Guide développement](./README.md) | [Créer un composant →](./creer-composant.md)
