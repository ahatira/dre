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
│   └── props/               # 🎨 Design tokens
│       ├── colors.css      # ✅ Semantic & brand colors
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

### Tokens Disponibles (source/props/*.css)

```css
/* Couleurs Brand (brand.css) */
--primary: var(--bnp-green);        /* #00915A - Vert BNP */
--secondary: var(--bnp-dark-green); /* #017F4F */
--accent: var(--bnp-mid-green);     /* #04A46E */
--bnp-accent-purple: #BA3075;
--bnp-accent-pink: #E0388C;

/* Couleurs Système (colors.css) */
--white: hsl(0 0% 100%);
--gray-50: hsl(210, 20%, 98%);
--gray-900: hsl(221, 39%, 11%);
--black: hsl(0 0% 0%);
--red-500: hsl(0, 84%, 63%);
--green-500: hsl(160, 84%, 34%);
--blue-500: hsl(218, 93%, 61%);

/* Typographie (fonts.css) */
--font-sans: 'BNPP Sans', sans-serif;     /* Police principale */
--font-alt: 'Open Sans', sans-serif;      /* Police secondaire */
--font-condensed: 'BNPP Sans Condensed';
--font-size-xs: 0.625rem;  /* 10px */
--font-size-1: 1rem;       /* 16px */
--font-size-10: 3rem;      /* 48px */
--font-weight-300: 300;
--font-weight-400: 400;
--font-weight-700: 700;
--font-weight-800: 800;
--leading-normal: 1.5;
--leading-tight: 1.25;

/* Tailles / Spacing (sizes.css) */
--size-1: 0.25rem;   /* 4px */
--size-2: 0.5rem;    /* 8px */
--size-4: 1rem;      /* 16px */
--size-6: 1.5rem;    /* 24px */
--size-8: 2rem;      /* 32px */
--size-10: 2.5rem;   /* 40px */
--size-16: 4rem;     /* 64px */

/* Borders (borders.css) */
--radius-1: 0.125rem;  /* 2px */
--radius-2: 0.25rem;   /* 4px */
--radius-3: 0.5rem;    /* 8px */
--radius-round: 50%;

/* Shadows (shadows.css) */
--shadow-1: 0 1px 2px rgba(0,0,0,0.05);
--shadow-3: 0 4px 6px rgba(0,0,0,0.1);
--shadow-5: 0 10px 15px rgba(0,0,0,0.1);

/* Z-index (zindex.css) */
--layer-1: 1;
--layer-10: 10;
--layer-50: 50;
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
```

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
