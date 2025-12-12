# PS Design System

**Version** : 1.0.0  
**Dernière mise à jour** : 28 novembre 2025  
**Statut** : 🟡 En développement actif (6% complété)  
**Projet** : PS Theme (BNP Paribas RealEstate)

---

Documentation du système de design pour **PS Theme**, thème Drupal 10/11 pour BNP Paribas RealEstate.

## 🎯 Objectif

Implémenter **87 composants** suivant les spécifications de `docs/design/`, avec :
- **BEM strict** avec préfixe `ps-`
- **Design tokens CSS** (`var(--ps-*)`)
- **Storybook** pour développement isolé
- **Accessibilité WCAG 2.2 AA**
- **Intégration Drupal** via Twig + libraries

## 📊 État Actuel

- **Composants implémentés** : 5 / 87 (6%)
- **Design tokens** : ✅ Disponibles dans `source/props/*.css` (colors, fonts, sizes, brand, etc.)
- **Documentation** : ✅ Complète (`docs/design/` - 87 specs)
- **Template standard** : ✅ Disponible (`docs/ps-design/COMPONENT_TEMPLATE.md`)

---

---

## 📁 Structure du Projet

```
ps_theme/
├── docs/
│   ├── design/              # 📚 Spécifications complètes (87 composants)
│   │   ├── atoms/          # 19 atoms documentés
│   │   ├── molecules/      # 20 molecules documentés
│   │   ├── organisms/      # 12 organisms documentés
│   │   ├── templates/      # 8 templates documentés
│   │   ├── pages/          # 8 pages documentées
│   │   └── tokens/         # 7 fichiers YAML de tokens
│   │
│   └── ps-design/           # 📖 Documentation du projet réel
│       ├── README.md       # Ce fichier
│       ├── INDEX.md        # Inventaire + progression
│       ├── CHANGELOG.md    # Historique des implémentations
│       └── COMPONENT_TEMPLATE.md  # Template standard
│
├── source/
│   ├── patterns/            # 🎨 Composants à implémenter
│   │   ├── elements/       # ✅ button, badge, label (3/19)
│   │   ├── components/     # ✅ alert, breadcrumb (2/20)
│   │   ├── collections/    # ⏳ À faire (0/12)
│   │   ├── layouts/        # ⏳ À faire (0/8)
│   │   └── pages/          # ⏳ À faire (0/8)
│   │
│   └── props/               # 🎨 Design tokens (couleurs + typo + spacing)
│       ├── colors.css      # ✅ Palettes base (GREEN, PINK, TEAL, RED, GREY)
│       ├── brand.css       # ✅ Tokens sémantiques (primary, secondary, success, etc.)
│       ├── fonts.css       # ✅ Typography tokens
│       ├── sizes.css       # ✅ Spacing & sizing scale
│       ├── shadows.css     # ✅ Shadow definitions
│       ├── borders.css     # ✅ Border styles
│       └── ... (+ autres)  # ✅ Tokens organisés par catégorie
│
├── storybook/               # 📚 Storybook build statique
├── templates/               # 🔧 Templates Drupal
└── vite.config.js          # ⚙️ Configuration build
```

---

## 🏗️ Architecture et Conventions

### Atomic Design avec Nomenclature Personnalisée

### Atomic Design avec Nomenclature Personnalisée

PS Theme suit l'**Atomic Design** avec des catégories personnalisées :

| Atomic Design | PS Theme | Description | Exemples |
|---------------|----------|-------------|----------|
| Atoms | **Elements** | Composants de base non décomposables | button, icon, badge, field |
| Molecules | **Components** | Combinaisons d'elements | card, dropdown, alert, modal |
| Organisms | **Collections** | Structures complexes | header, footer, hero, search-form |
| Templates | **Layouts** | Gabarits de mise en page | page-container, two-column, grid |
| Pages | **Pages** | Compositions complètes | home-page, property-search |

### Conventions de Nommage BEM

Le projet suit **BEM strict avec préfixe `ps-`** :

```css
/* ✅ Convention moderne (nouveaux composants) */
.ps-button { }                          /* Block */
.ps-button__label { }                   /* Element */
.ps-button--primary { }                 /* Modifier */
.ps-button--green { }                   /* Variant */
.ps-button__icon--left { }              /* Element + Modifier */

/* ⚠️ Convention legacy (anciens composants à migrer) */
.card { }
.card__image { }
.card--featured { }
```

**Règle pour nouveaux composants** : Toujours utiliser `ps-{component}` + BEM strict.

---

**Règle pour nouveaux composants** : Toujours utiliser `ps-{component}` + BEM strict.

---

## ✅ Composants Implémentés (5/87)

### Elements (3/19)
1. **button** - `source/patterns/elements/button/`
   - ✅ 5 fichiers complets (`.twig`, `.css`, `.yml`, `.stories.jsx`, `.mdx`)
   - ✅ Variants : primary/secondary × green/purple/white
   - ✅ Tailles : small/medium/large
   - ✅ États : hover, focus, active, disabled, loading
   - ✅ Icônes : left/right/only
   - ✅ 10+ stories Storybook

2. **badge** - `source/patterns/elements/badge/`
   - ✅ BEM avec préfixe `ps-badge`
   - ✅ Tailles : small/medium/large
   - ✅ Formes : rounded/square/pill/count
   - ✅ Tokens CSS utilisés

3. **label** - `source/patterns/elements/label/`
   - ⚠️ Implémentation minimale (à enrichir)

### Components (2/20)
1. **alert** - `source/patterns/components/alert/`
   - ⚠️ Structure de base présente (à compléter)

2. **breadcrumb** - `source/patterns/components/breadcrumb/`
   - ⚠️ Structure de base présente (à compléter)

---

## ⏳ À Implémenter (82/87)

Voir **[INDEX.md](./INDEX.md)** pour :
- Liste complète des 82 composants restants
- Priorisation en 6 phases
- Estimations de temps (297h total)
- Plan de route Q1-Q4 2026

**Prochaine priorité** : Phase 1 (icon, heading, text, link, field, checkbox, radio, image, card)

---

## 🎨 Design Tokens

Tous les tokens sont centralisés dans `source/props/*.css` sous forme de **CSS Custom Properties** organisés par catégorie (colors, fonts, sizes, shadows, borders, animations, easing, zindex).

### Architecture des Tokens de Couleur

**3 couches :**
1. **colors.css** - Palettes de base (50-900) avec couleurs officielles BNP
2. **brand.css** - Tokens sémantiques (primary, secondary, success, danger, etc.)
3. **Components** - Références aux tokens sémantiques via `var(--primary)`, etc.

### Palettes Officielles BNP (colors.css)

```css
/* Primary Green (Vert BNP #00915A) */
--green-50: #ebf7f4;   /* Très clair */
--green-600: #00915a;  /* Principal */
--green-700: #017f4f;  /* Hover */
--green-800: #016b44;  /* Active */
--green-900: #01563a;  /* Emphasis */

/* Secondary Pink (Rose BNP #A12B66) */
--pink-50: #f9ecf2;    /* Très clair */
--pink-600: #ba3075;   /* Principal */
--pink-700: #a12b66;   /* Darker */
--pink-900: #751d4e;   /* Emphasis */

/* Success Teal (Vert succès #198754) */
--teal-50: #e7f4f1;    /* Très clair */
--teal-600: #198754;   /* Principal */
--teal-700: #167a48;   /* Darker */
--teal-900: #124a3b;   /* Emphasis */

/* Error Red (Rouge BNP #EB3636) */
--red-50: #fef7f7;     /* Très clair */
--red-600: #eb3636;    /* Principal */
--red-700: #d43131;    /* Darker */
--red-900: #a62626;    /* Emphasis */

/* Grey Scale (Gris BNP #333333 → #FFFFFF) */
--gray-50: #f9f9fb;    /* Très clair */
--gray-100: #ebedef;   /* Clair */
--gray-200: #d6dbde;   /* Light */
--gray-400: #b4babe;   /* Medium */
--gray-500: #977e83;   /* Medium-dark */
--gray-700: #434f57;   /* Dark */
--gray-900: #333333;   /* Très sombre */
```

### Tokens Sémantiques (brand.css)

```css
/* Couleurs Brand Sémantiques */
--primary: var(--green-600);      /* #00915A - Vert primaire BNP */
--secondary: var(--pink-700);     /* #A12B66 - Rose secondaire BNP */
--success: var(--teal-600);       /* #198754 - Vert succès */
--danger: var(--red-600);         /* #EB3636 - Rouge erreur BNP */
--warning: var(--yellow-400);     /* Jaune attention */
--info: var(--blue-600);          /* Bleu info */
--light: var(--gray-100);         /* Clair sur fond sombre */
--dark: var(--gray-700);          /* Sombre sur fond clair */

/* États (Hover, Active, Subtle) */
--primary-hover: var(--green-700);
--primary-active: var(--green-800);
--primary-subtle: var(--green-50);

/* Texte et Bordures */
--text-primary: var(--gray-700);     /* Texte principal */
--text-secondary: var(--gray-500);   /* Texte secondaire */
--border-default: var(--gray-200);   /* Bordures standard */
--border-success: var(--teal-600);   /* Bordures succès (distinct du primary) */
--border-error: var(--red-600);      /* Bordures erreur */
```

### Utilisation dans les Composants

```css
.ps-button {
  background-color: var(--primary);   /* brand.css */
  color: var(--white);                      /* colors.css */
  font-family: var(--font-sans);            /* fonts.css */
  font-size: var(--font-size-1);            /* fonts.css */
  padding: var(--size-2) var(--size-4);     /* sizes.css */
  border-radius: 0;                          /* Design carré par défaut */
  box-shadow: var(--shadow-3);              /* shadows.css */
  transition: all 0.15s var(--ease-out-2);  /* animations.css */
}

.ps-button:hover {
  background-color: var(--secondary); /* brand.css */
}

.ps-button:focus-visible {
  outline: 2px solid var(--blue-500);       /* colors.css */
  outline-offset: 2px;
}
```

### Règles Absolues

1. ❌ **JAMAIS de valeurs en dur** : `#00915A`, `16px`, `400`, etc.
2. ✅ **TOUJOURS utiliser les tokens** : `var(--primary)`, `var(--size-4)`, etc.
3. ⚠️ **Si token manquant** : L'ajouter dans `source/props/{category}.css` approprié
4. 📝 **Respecter les conventions** : Même structure/naming que tokens existants
5. 🔄 **Documenter** : Mettre à jour `CHANGELOG.md` après ajout de tokens

**Règle absolue** : ❌ **Jamais de valeurs en dur** dans le CSS. Toujours utiliser `var(--*)` depuis `source/props/*.css`.

---

## 🛠️ Workflow de Développement

### 1. Préparer l'Implémentation

```bash
# Consulter la spec du composant
cat docs/design/{level}/{component}.md

# Lire le template standard
cat docs/ps-design/COMPONENT_TEMPLATE.md
```

### 2. Vérifier les Tokens Disponibles

```bash
# 1. Consulter les tokens existants
cat source/props/colors.css   # Couleurs système
cat source/props/brand.css     # Couleurs BNP Paribas
cat source/props/fonts.css     # Typographie
cat source/props/sizes.css     # Spacing & dimensions

# 2. Chercher si tokens similaires existent déjà
grep -r "--gray-" source/props/     # Nuances de gris
grep -r "--size-" source/props/     # Tailles disponibles
grep -r "--font-size" source/props/ # Tailles de police

# 3. Vérifier la cohérence avant d'ajouter
# Exemple : Besoin de --size-5 ?
# → Vérifier que --size-4 (16px) et --size-6 (24px) existent
# → Vérifier que la progression est cohérente
# → Ajouter --size-5: 1.25rem (20px) seulement si nécessaire

# 4. Si vraiment nouveau token nécessaire
# → L'ajouter dans le bon fichier (colors.css, fonts.css, etc.)
# → Respecter STRICTEMENT la convention de nommage
# → Documenter dans CHANGELOG.md avec justification
```

### 3. Créer la Structure

```bash
# Créer le dossier du composant
mkdir -p source/patterns/{level}/{component}/
cd source/patterns/{level}/{component}/

# Créer les 5 fichiers obligatoires
touch {component}.twig
touch {component}.css
touch {component}.yml
touch {component}.stories.jsx
touch {component}.mdx
```

### 4. Développer avec Storybook

```bash
# Lancer Storybook + Vite en mode watch
npm run watch

# Storybook accessible sur http://localhost:6006
# Hot reload automatique sur modification
```

### 5. Implémenter les Fichiers

Suivre l'ordre recommandé :
1. `.yml` - Données par défaut
2. `.twig` - Template avec BEM
3. `.css` - Styles avec tokens uniquement (source/props/*.css)
4. `.stories.jsx` - Stories (Default + Variants)
5. `.mdx` - Documentation

### 6. Valider

- ✅ Tester tous les variants dans Storybook
- ✅ Vérifier accessibilité (navigation clavier, contraste, ARIA)
- ✅ Tester responsive (mobile, tablet, desktop)
- ✅ Valider avec lecteur d'écran
- ✅ Vérifier que tous les tokens sont utilisés (aucune valeur en dur)
- ✅ Si tokens manquants : Les ajouter dans source/props/{category}.css

### 7. Documenter

```bash
# Mettre à jour le CHANGELOG
echo "### [$(date +%Y-%m-%d)] - Ajout de {component}" >> docs/ps-design/CHANGELOG.md

# Si ajout de tokens, le mentionner aussi
echo "- Ajout tokens: --primary, --size-*, etc." >> docs/ps-design/CHANGELOG.md

# Commit
git add source/patterns/{level}/{component}/
git commit -m "feat({level}): add {component} component"
```

---

## 📚 Ressources

### Documentation
- **Index complet** : [`docs/ps-design/INDEX.md`](./INDEX.md) - Inventaire + progression
- **Template** : [`docs/ps-design/COMPONENT_TEMPLATE.md`](./COMPONENT_TEMPLATE.md) - Structure standard
- **Changelog** : [`docs/ps-design/CHANGELOG.md`](./CHANGELOG.md) - Historique
- **Spécifications** : `docs/design/` - 87 composants documentés

### Références Code
- **Exemple complet** : `source/patterns/elements/button/` - Référence à suivre
- **Design tokens** : `source/props/*.css` - Tokens organisés par catégorie
  - `colors.css` - Palette de couleurs système
  - `brand.css` - Couleurs de marque BNP Paribas
  - `fonts.css` - Typographie (families, sizes, weights, line-heights)
  - `sizes.css` - Système de tailles et spacing
  - `borders.css`, `shadows.css`, `animations.css`, `easing.css`, `zindex.css`
- **Storybook local** : [http://localhost:6006](http://localhost:6006)
- **Demo statique** : [lien](https://dev-ucla-surface-training.pantheonsite.io/themes/custom/surface/storybook/)

### Commandes NPM

```bash
npm run build              # Build complet (Vite + Storybook)
npm run watch              # Dev mode (Vite + Storybook watch)
npm run storybook:build    # Build Storybook uniquement
npm run storybook:dev      # Storybook dev server uniquement

# Icônes SVG (automatiquement exécuté pendant watch)
npm run icons:build        # Générer sprite à partir des SVG sources
npm run icons:watch        # Watch mode pour les changements SVG
```

### Système d'Icônes SVG

PS Theme utilise un système de **sprite SVG compilé** :

```
source/icons-source/              # 139 fichiers SVG sources (dev uniquement)
source/assets/icons/icons-sprite.svg  # Sprite généré (asset production)
source/props/icons.css            # CSS auto-généré pour le sprite
```

**Utilisation** :
```twig
{% include '@elements/icon/icon.twig' with { name: 'check' } only %}
{% include '@elements/icon/icon.twig' with { name: 'arrow-right' } only %}
```

**Build**:
- Le script `scripts/build-icons.mjs` compile les SVG sources en sprite optimisé
- Automatiquement executé lors de `npm run build` et `npm run watch`
- Les sources SVG ne sont **pas** incluées dans la distribution (optimisation bundle)

---

## ♿ Accessibilité (WCAG 2.2 AA)

Tous les composants doivent respecter :

### Checklist Obligatoire
- [ ] **Contraste** : Minimum 4.5:1 pour texte, 3:1 pour UI
- [ ] **Navigation clavier** : Tab, Enter, Space, Esc, Arrows
- [ ] **Focus visible** : Outline claire avec `:focus-visible`
- [ ] **ARIA** : Roles et attributs appropriés
- [ ] **HTML sémantique** : `<button>`, `<nav>`, `<main>`, etc.
- [ ] **Alternatives textuelles** : `alt` pour images, `aria-label` pour icônes
- [ ] **États** : disabled, loading, error accessibles
- [ ] **Lecteur d'écran** : Testé avec NVDA/JAWS

### Exemple Button Accessible

```html
<button 
  class="ps-button ps-button--primary ps-button--green"
  type="button"
  aria-label="Rechercher des propriétés"
  aria-describedby="search-hint"
>
  <span class="ps-button__label">Rechercher</span>
  <svg class="ps-button__icon" aria-hidden="true">...</svg>
</button>
<span id="search-hint" class="visually-hidden">
  Saisissez au moins 3 caractères
</span>
```

---

## 🔗 Intégration Drupal

### Template Twig

```twig
{# node--article--teaser.html.twig #}
{% include '@ps_theme/components/card/card.twig' with {
  'title': label,
  'description': content.body|render|striptags|slice(0, 150),
  'image': content.field_image,
  'eyebrow': content.field_category,
  'url': url,
  'variant': 'default'
} only %}
```

### Libraries YAML

```yaml
# ps_theme.libraries.yml
ps-card:
  version: 1.0.0
  css:
    component:
      source/patterns/components/card/card.css: {}
  dependencies:
    - ps_theme/ps-tokens
    - ps_theme/ps-icon
```

---

## 🔄 Icon System Migration Guides

**Status**: SVG sprite system implemented (December 2025)  
**Task**: Update all elements using `data-icon` to use modern SVG sprite approach  
**Documentation**: Three comprehensive guides available

### Migration Documents

1. **[ICON_MIGRATION_START.md](./ICON_MIGRATION_START.md)** - Quick start guide
   - Entry point with workflow overview
   - Recommended component order
   - Key migration pattern
   - **START HERE** ⬅️

2. **[ICON_MIGRATION_WORKFLOW.md](./ICON_MIGRATION_WORKFLOW.md)** - Detailed strategy
   - Overall migration strategy
   - Step-by-step workflow template
   - Drupal compatibility notes
   - Testing procedures

3. **[COMPONENT_PROMPTS.md](./COMPONENT_PROMPTS.md)** - Specific instructions
   - 6 component-specific detailed prompts
   - Before/after code examples
   - Testing checklists
   - Commit message templates

### Components to Migrate

| Priority | Component | Status | Guide |
|----------|-----------|--------|-------|
| 1 (Easy) | Badge | ⏳ Pending | [Prompt](#) |
| 1 (Easy) | Divider | ⏳ Pending | [Prompt](#) |
| 1 (Easy) | Eyebrow | ⏳ Pending | [Prompt](#) |
| 2 (Medium) | Button | ⏳ Pending | [Prompt](#) |
| 2 (Medium) | Link | ⏳ Pending | [Prompt](#) |
| 3 (Complex) | Field | ⏳ Pending | [Prompt](#) |

### Quick Start

1. Open: `COMPONENT_PROMPTS.md`
2. Find: **BADGE** section
3. Follow: Step-by-step instructions
4. Test: `npm run lint:check && npm run format:check`
5. Commit: Use provided template
6. Repeat: For each component

---

## 🎯 Prochaines Étapes

### Sprint Actuel (Décembre 2025)
1. ⏳ Implémenter `icon` (bibliothèque SVG complète)
2. ⏳ Implémenter `heading`, `text`, `link`
3. ⏳ Implémenter `card` (priorité #1)

### Q1 2026
- ✅ Phase 1 complète (13 composants fondamentaux)
- ✅ Phase 2 complète (8 composants navigation)
- **Total** : 21 composants (24%)

Voir **[INDEX.md](./INDEX.md)** pour le plan complet.

---

**Version** : 1.0.0  
**Dernière mise à jour** : 28 novembre 2025  
**Prochain composant** : icon (Element)
