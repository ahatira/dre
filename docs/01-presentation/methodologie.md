# Méthodologie de Développement - PS Theme

**Principes, workflows et standards de développement**

---

## 📖 Table des Matières

1. [Atomic Design](#atomic-design)
2. [Token-First Composition](#token-first-composition)
3. [BEM (Block Element Modifier)](#bem-block-element-modifier)
4. [Mobile-First Approach](#mobile-first-approach)
5. [Accessibilité WCAG 2.2 AA](#accessibilité-wcag-22-aa)
6. [Workflow de Développement](#workflow-de-développement)

---

## 🧬 Atomic Design

**Méthodologie** : Brad Frost (2013)  
**Concept** : Construire une interface comme un système chimique, du plus simple au plus complexe.

### 5 Niveaux Hiérarchiques

```
Atomes (19)           → Éléments de base indivisibles
    ↓
Molécules (20)        → Combinaisons simples d'atomes
    ↓
Organismes (12)       → Sections complexes de l'interface
    ↓
Templates (8)         → Structures de page avec placeholders
    ↓
Pages (8)             → Instances complètes avec contenu réel
```

### 1. Atomes (Elements/)

**Définition** : Plus petits composants de l'interface, indivisibles et autonomes.

**Caractéristiques** :
- ✅ Autonomes (pas de dépendances composants)
- ✅ Réutilisables partout
- ✅ Un seul rôle/responsabilité
- ✅ N'incluent PAS d'autres composants (sauf rendering systems comme Icon)

**Exemples PS Theme** :
- `button` – Bouton interactif
- `badge` – Étiquette visuelle
- `icon` – Icône SVG
- `input` – Champ de saisie
- `link` – Lien hypertexte
- `avatar` – Image de profil
- `divider` – Séparateur visuel

**Token-First** : ❌ Ne s'applique PAS  
→ Les atomes sont autonomes, ils exposent des tokens mais n'en overrident pas.

### 2. Molécules (Components/)

**Définition** : Groupes d'atomes fonctionnant ensemble pour former une unité cohérente.

**Caractéristiques** :
- ✅ Composent des atomes
- ✅ Une fonction claire et unique
- ✅ Réutilisables dans différents contextes
- ✅ Token-First s'applique pour customiser les atomes enfants

**Exemples PS Theme** :
- `card` – Conteneur de contenu avec image/titre/CTA
- `form-field` – Label + Input + Message d'erreur
- `breadcrumb` – Navigation fil d'Ariane
- `pagination` – Navigation par pages
- `alert` – Message de notification

**Token-First** : ✅ S'applique (customisation atomes via override tokens)

**Exemple** : Form Field compose Label + Input
```css
.ps-form-field {
  /* Override Label tokens */
  --ps-label-font-size: var(--font-size-1); /* Plus petit */
  
  /* Override Input tokens */
  --ps-input-border-radius: var(--radius-2); /* Coins arrondis */
}
```

### 3. Organismes (Collections/)

**Définition** : Sections complexes de l'interface composées de molécules et d'atomes.

**Caractéristiques** :
- ✅ Structures relativement complexes
- ✅ Forment des sections distinctes de l'interface
- ✅ Peuvent être réutilisées sur plusieurs pages
- ✅ Token-First s'applique (override molécules + atomes)

**Exemples PS Theme** :
- `header` – En-tête de site avec logo, navigation, recherche
- `footer` – Pied de page avec liens, copyright, réseaux sociaux
- `property-grid` – Grille de biens immobiliers
- `filter-panel` – Panneau de filtres de recherche
- `hero` – Bandeau accueil avec titre, description, CTA

**Token-First** : ✅ S'applique (cascade multi-niveaux)

**Exemple** : Header compose Navigation (molecule) compose Link (atom)
```css
.ps-header {
  /* Override Navigation tokens */
  --ps-nav-gap: var(--size-6); /* Plus d'espace entre liens */
  
  /* Override Link tokens (dans Navigation) */
  --ps-link-color: var(--white); /* Liens blancs sur fond foncé */
}
```

### 4. Templates (Layouts/)

**Définition** : Structures de page avec zones de contenu (placeholders), sans contenu réel.

**Caractéristiques** :
- ✅ Définissent la grille et le layout général
- ✅ Positionnent les organismes
- ✅ Responsive (breakpoints définis)
- ✅ Réutilisables pour plusieurs types de pages

**Exemples PS Theme** :
- `page-container` – Wrapper principal avec header/content/footer
- `two-column` – Sidebar + contenu principal
- `full-width` – Pleine largeur sans sidebar
- `hero-layout` – Hero + contenu en dessous

**Token-First** : ✅ S'applique (customisation organismes positionnés)

### 5. Pages (Pages/)

**Définition** : Instances complètes de templates avec contenu réel et contexte métier.

**Caractéristiques** :
- ✅ Contenu réel (pas de lorem ipsum)
- ✅ Contexte métier immobilier (BNP Paribas Real Estate)
- ✅ Démonstration complète du système
- ✅ Utilisées pour tests utilisateurs et validation

**Exemples PS Theme** :
- `home-page` – Page d'accueil
- `property-search` – Recherche de biens
- `property-detail` – Détail d'un bien
- `contact` – Formulaire de contact
- `agent-profile` – Profil d'un agent

**Token-First** : ✅ S'applique (customisations contextuelles page-specific)

---

## 🎯 Token-First Composition

**Principe** : Lorsqu'un composant en compose d'autres, customiser via **override de tokens** plutôt que CSS direct.

### Applicabilité

| Niveau | Token-First s'applique ? | Raison |
|--------|--------------------------|--------|
| Atomes | ❌ NON | Autonomes, pas de composition |
| Molécules | ✅ OUI | Composent des atomes |
| Organismes | ✅ OUI | Composent molécules + atomes |
| Templates | ✅ OUI | Positionnent organismes |
| Pages | ✅ OUI | Assemblent templates |

### Workflow en 4 Étapes

```
STEP 1: Check native params
    ↓ (Pas suffisant)
STEP 2: Check utility classes
    ↓ (Pas suffisant)
STEP 3: Override tokens ⭐ PRÉFÉRÉ
    ↓ (Vraiment pas suffisant)
STEP 4: Targeted CSS (dernier recours)
```

#### STEP 1 : Native Parameters

**Question** : Le composant parent expose-t-il des paramètres natifs ?

**Exemple** : Card expose `variant`, `imagePosition`, `borderRadius`, `size`

```twig
{# card-offer-search.twig #}
{% embed '@components/card/card.twig' with {
  variant: 'outlined',           # ✅ Utiliser param natif
  imagePosition: 'left',         # ✅ Utiliser param natif
  borderRadius: 'large'          # ✅ Utiliser param natif
} %}
```

**Avantages** :
- ✅ API documentée et testée
- ✅ Pas de CSS custom nécessaire
- ✅ Maintenabilité maximale

#### STEP 2 : Utility Classes

**Question** : Des classes utilitaires existent-elles pour ce besoin ?

**Exemple** : Espacement, alignement, display

```twig
{# card-offer-search.twig #}
{% set classes = [
  'u-padding-large',    # ✅ Utility class
  'u-gap-4',            # ✅ Utility class
  'u-text-center'       # ✅ Utility class
] %}
<div{{ attributes.addClass(classes) }}>
```

**Avantages** :
- ✅ Pas de CSS custom
- ✅ Cohérence visuelle globale
- ✅ Performance (classes réutilisées)

#### STEP 3 : Override Tokens ⭐

**Question** : Le composant parent expose-t-il des tokens CSS variables ?

**Exemple** : Card expose `--ps-card-padding-x`, `--ps-card-border-color`, etc.

```css
/* card-offer-search.css */
.ps-card-offer-search {
  /* Override Card tokens */
  --ps-card-padding-x: var(--size-6);        /* Plus d'espace */
  --ps-card-padding-y: var(--size-7);        /* Plus d'espace */
  --ps-card-border-color: var(--gray-200);   /* Bordure subtile */
  --ps-card-border-radius: var(--radius-3);  /* Coins arrondis */
  
  /* Override enfants (Badge dans Card header) */
  --ps-badge-font-size: var(--font-size-00); /* Badge plus petit */
  --ps-badge-padding-x: var(--size-2);       /* Moins de padding */
}
```

**Avantages** :
- ✅ Zéro modification du parent (card.css intact)
- ✅ Cascade CSS native (performance)
- ✅ Tokens documentés et maintenables
- ✅ Scopé au composant consommateur

**🔥 APPROCHE PRÉFÉRÉE** : 90% des cas doivent utiliser STEP 3.

#### STEP 4 : Targeted CSS

**Question** : Impossible de faire autrement ? (derniers recours)

**Exemple** : Layout très spécifique, responsive custom

```css
/* card-offer-search.css */
.ps-card-offer-search {
  /* Layout horizontal custom */
  .ps-card__media {
    width: 40%; /* Largeur fixe sur desktop */
  }
  
  .ps-card__content {
    width: 60%;
  }
  
  /* Responsive custom */
  @media (max-width: 768px) {
    .ps-card__media {
      width: 100%;
    }
  }
}
```

**⚠️ Attention** :
- ❌ CSS couplé à la structure HTML du parent
- ❌ Risque de casser si parent change
- ❌ Spécificité wars potentielles

**Utiliser UNIQUEMENT si** :
- Vraiment aucune autre solution (STEP 1-3 impossibles)
- Cas extrêmement spécifique (une seule instance)
- Layout complexe nécessitant structure custom

### Anti-Patterns (À ÉVITER)

```css
/* ❌ MAUVAIS - Modification directe du parent */
.ps-card {
  padding: 30px; /* Hardcodé, casse tous les autres usages */
}

/* ❌ MAUVAIS - Tokens palette directe */
.ps-card-offer-search {
  border-color: #EBEDEF; /* Hardcodé, pas de token */
}

/* ❌ MAUVAIS - Duplication styles */
.ps-card-offer-search {
  padding: var(--size-7);
  border-radius: var(--radius-3);
  /* Au lieu d'override --ps-card-padding-y / --ps-card-border-radius */
}

/* ❌ MAUVAIS - baseClass parameter */
{% include '@components/card/card.twig' with {
  baseClass: 'my-custom-card' /* FORBIDDEN - utiliser attributes.addClass() */
} %}
```

---

## 🎨 BEM (Block Element Modifier)

**Méthodologie** : Block Element Modifier (Yandex, 2009)  
**Objectif** : Nommage CSS prévisible, lisible, maintenable.

### Syntaxe PS Theme

```
.ps-{block}                    # Block (composant racine)
.ps-{block}__{element}         # Element (partie du block)
.ps-{block}--{modifier}        # Modifier (variante du block)
.ps-{block}__{element}--{modifier}  # Modifier sur element
```

**Préfixe obligatoire** : `ps-` (évite collisions CSS, identifie composants PS Theme)

### Exemples

```html
<!-- Block -->
<button class="ps-button">
  
  <!-- Element -->
  <span class="ps-button__icon" data-icon="check"></span>
  <span class="ps-button__text">Save</span>
  
</button>

<!-- Block + Modifier -->
<button class="ps-button ps-button--primary">
  <span class="ps-button__text">Primary</span>
</button>

<!-- Block + Multiple Modifiers -->
<button class="ps-button ps-button--primary ps-button--large">
  <span class="ps-button__text">Large Primary</span>
</button>

<!-- Element + Modifier -->
<div class="ps-card">
  <div class="ps-card__media ps-card__media--overlay">
    <img src="..." alt="...">
  </div>
</div>
```

### Règles BEM Strictes

#### 1. Modifiers doivent fonctionner seuls

```html
<!-- ✅ CORRECT - Chaque modifier fonctionne indépendamment -->
<button class="ps-button ps-button--primary"></button>
<button class="ps-button ps-button--large"></button>
<button class="ps-button ps-button--primary ps-button--large"></button>

<!-- ❌ MAUVAIS - Modifier nécessite combinaison -->
<button class="ps-button ps-button--primary ps-button--outlined"></button>
<!-- Si --primary ET --outlined sont requis ensemble, créer 1 modifier unique -->
```

#### 2. Pas de nesting profond

```html
<!-- ✅ CORRECT - Element directement sous block -->
<div class="ps-card">
  <div class="ps-card__media"></div>
  <div class="ps-card__body"></div>
</div>

<!-- ❌ MAUVAIS - Nesting profond -->
<div class="ps-card">
  <div class="ps-card__media">
    <div class="ps-card__media__overlay">  <!-- ❌ 3 niveaux -->
      <div class="ps-card__media__overlay__badge"></div>  <!-- ❌ 4 niveaux -->
    </div>
  </div>
</div>

<!-- ✅ CORRECT - Flatten structure -->
<div class="ps-card">
  <div class="ps-card__media">
    <div class="ps-card__overlay">  <!-- ✅ 2 niveaux max -->
      <span class="ps-card__badge"></span>
    </div>
  </div>
</div>
```

#### 3. Cascade correcte (Base → Modifiers)

```css
/* ✅ CORRECT - Base d'abord, modifiers après */
.ps-button {
  padding: var(--size-2) var(--size-4);
  background: var(--gray-500);
  color: var(--white);
}

.ps-button--primary {
  background: var(--primary);
}

.ps-button--large {
  padding: var(--size-3) var(--size-6);
}

/* ❌ MAUVAIS - Modifiers avant base */
.ps-button--primary {
  background: var(--primary);
}

.ps-button {
  background: var(--gray-500); /* Override le modifier, mauvaise cascade */
}
```

#### 4. Nesting CSS obligatoire (PostCSS)

```css
/* ✅ CORRECT - Nesting avec & */
.ps-button {
  padding: var(--size-2) var(--size-4);
  
  &__icon {
    width: 1em;
    height: 1em;
  }
  
  &--primary {
    background: var(--primary);
    
    &:hover {
      background: var(--primary-hover);
    }
  }
}

/* ❌ MAUVAIS - Flat CSS */
.ps-button {
  padding: var(--size-2) var(--size-4);
}

.ps-button__icon {
  width: 1em;
}

.ps-button--primary {
  background: var(--primary);
}

.ps-button--primary:hover {
  background: var(--primary-hover);
}
```

---

## 📱 Mobile-First Approach

**Principe** : Développer d'abord pour mobile, puis enrichir progressivement pour desktop.

### Rationale

1. **Performance** : Mobile = contraintes réseau/processeur → optimisation forcée
2. **Priorités** : Mobile = espace limité → focus sur l'essentiel
3. **Progressivité** : Ajouter features > retirer features (cascade CSS)
4. **Statistiques** : 60%+ du trafic web est mobile (2025)

### Breakpoints PS Theme

Définis dans `source/props/media.css` :

```css
:root {
  --breakpoint-mobile-sm: 22.5rem;   /* 360px - Petits mobiles */
  --breakpoint-mobile: 30rem;        /* 480px - Mobiles standards */
  --breakpoint-tablet: 48rem;        /* 768px - Tablettes */
  --breakpoint-laptop: 64rem;        /* 1024px - Laptops */
  --breakpoint-desktop: 80rem;       /* 1280px - Desktops */
  --breakpoint-desktop-large: 90rem; /* 1440px - Large desktops */
}
```

### Pattern Mobile-First

```css
/* Base = Mobile (360px+) */
.ps-property-grid {
  display: grid;
  grid-template-columns: 1fr; /* 1 colonne sur mobile */
  gap: var(--size-4);
  padding: var(--size-4);
}

/* Tablet (768px+) */
@media (min-width: 48rem) {
  .ps-property-grid {
    grid-template-columns: repeat(2, 1fr); /* 2 colonnes */
    gap: var(--size-6);
    padding: var(--size-6);
  }
}

/* Laptop (1024px+) */
@media (min-width: 64rem) {
  .ps-property-grid {
    grid-template-columns: repeat(3, 1fr); /* 3 colonnes */
    gap: var(--size-8);
  }
}

/* Desktop (1280px+) */
@media (min-width: 80rem) {
  .ps-property-grid {
    grid-template-columns: repeat(4, 1fr); /* 4 colonnes */
    max-width: 1280px;
    margin: 0 auto; /* Centré */
  }
}
```

### Responsive Obligatoire (v2.0.0+)

**Depuis Button v2.0.0**, tous les nouveaux composants DOIVENT documenter 6 breakpoints :

```markdown
## 📱 Comportement responsive

| Breakpoint | Taille | Comportement |
|------------|--------|--------------|
| Mobile SM | 360px+ | Touch targets 36px (WCAG AA acceptable) |
| Mobile | 480px+ | Idem mobile-sm |
| Tablet | 768px+ | Touch targets 40px (WCAG AAA) |
| Laptop | 1024px+ | Sizes maintenues |
| Desktop | 1280px+ | Sizes maintenues |
| Desktop Large | 1440px+ | Sizes maintenues |
```

**Rationale** :
- **Mobile** : Touch targets optimisés (36px = WCAG AA acceptable)
- **Tablet** : Touch targets augmentés (40px = WCAG AAA recommandé)
- **Desktop** : Mouse interaction précise (no need for larger targets)

---

## ♿ Accessibilité WCAG 2.2 AA

**Standard** : WCAG 2.2 niveau AA (minimum obligatoire)  
**Objectif** : Tous les composants PS Theme doivent être utilisables par TOUS les utilisateurs.

### Principes POUR (Perceivable, Operable, Understandable, Robust)

#### 1. Perceivable (Perceptible)

**Contraste des couleurs** : Minimum 4.5:1 pour texte normal, 3:1 pour texte large

```css
/* ✅ CORRECT - Contraste 7.2:1 (AAA) */
.ps-badge--default {
  background: var(--gray-100); /* #F3F4F6 */
  color: var(--gray-700);      /* #374151 */
}

/* ✅ CORRECT - Contraste 4.8:1 (AA) */
.ps-badge--gold {
  background: var(--gold-bg-subtle); /* #FEF3E2 */
  color: var(--gold);                /* #D1AE6E */
}

/* ❌ MAUVAIS - Contraste 2.1:1 (échec AA) */
.ps-badge--low-contrast {
  background: #E0E0E0;
  color: #A0A0A0; /* Trop faible */
}
```

**Alternatives textuelles** : Images, icons, SVG

```html
<!-- ✅ CORRECT - Icon décorative -->
<span class="ps-button__icon" data-icon="check" aria-hidden="true"></span>

<!-- ✅ CORRECT - Icon porteuse de sens -->
<button aria-label="Close dialog">
  <span data-icon="close" aria-hidden="true"></span>
</button>

<!-- ❌ MAUVAIS - Icon sans alternative -->
<button>
  <span data-icon="close"></span>
</button>
```

#### 2. Operable (Utilisable)

**Navigation clavier** : Tous les interactifs accessibles au clavier

```html
<!-- ✅ CORRECT - Natif keyboard accessible -->
<button class="ps-button">Click me</button>
<a href="/page" class="ps-link">Navigate</a>

<!-- ❌ MAUVAIS - Div clickable mais pas keyboard accessible -->
<div onclick="handleClick()">Click me</div>
```

**Focus visible** : Indicateur visible sur tous les interactifs (WCAG 2.2 Focus Appearance)

```css
/* ✅ CORRECT - Focus-visible avec outline */
.ps-button:focus-visible {
  outline: 2px solid var(--border-focus);
  outline-offset: 2px;
}

/* ✅ CORRECT - Alternative avec border */
.ps-input:focus-visible {
  border-width: 2px;
  border-color: var(--text-primary);
}

/* ❌ MAUVAIS - Pas de focus visible */
.ps-button:focus {
  outline: none; /* Jamais faire ça */
}
```

**Touch targets** : Minimum 44×44px (WCAG 2.2 Target Size)

```css
/* ✅ CORRECT - Touch target 44×44px */
.ps-button {
  min-width: 44px;
  min-height: 44px;
  padding: var(--size-2) var(--size-4);
}

/* ⚠️ ACCEPTABLE - 36×36px sur mobile (WCAG AA) */
@media (max-width: 48rem) {
  .ps-button--small {
    min-width: 36px;
    min-height: 36px;
  }
}

/* ❌ MAUVAIS - Touch target trop petit */
.ps-icon-button {
  width: 24px;
  height: 24px; /* Trop petit pour touch */
}
```

#### 3. Understandable (Compréhensible)

**Labels explicites** : Tous les formulaires ont des labels

```html
<!-- ✅ CORRECT - Label visible -->
<label for="email" class="ps-label">Email address</label>
<input id="email" type="email" class="ps-input">

<!-- ✅ CORRECT - Label avec aria-label -->
<input type="search" aria-label="Search properties" class="ps-input">

<!-- ❌ MAUVAIS - Pas de label -->
<input type="email" placeholder="Email"> <!-- Placeholder ≠ label -->
```

**Messages d'erreur clairs** : Validation formulaire

```html
<!-- ✅ CORRECT - Erreur associée avec aria-describedby -->
<label for="email">Email</label>
<input 
  id="email" 
  type="email" 
  aria-invalid="true"
  aria-describedby="email-error"
  class="ps-input ps-input--error"
>
<span id="email-error" class="ps-form-error">
  Please enter a valid email address
</span>

<!-- ❌ MAUVAIS - Erreur visuelle seulement -->
<input type="email" class="ps-input error-border">
<span class="error-text">Invalid email</span>
```

#### 4. Robust (Robuste)

**HTML sémantique** : Utiliser les bons éléments

```html
<!-- ✅ CORRECT - Sémantique appropriée -->
<nav aria-label="Main navigation">
  <ul class="ps-nav">
    <li><a href="/">Home</a></li>
    <li><a href="/properties">Properties</a></li>
  </ul>
</nav>

<article class="ps-card">
  <h2>Property Title</h2>
  <p>Description...</p>
</article>

<!-- ❌ MAUVAIS - Div soup -->
<div class="navigation">
  <div class="nav-item"><div onclick="navigate()">Home</div></div>
</div>
```

**ARIA approprié** : Utiliser ARIA pour améliorer, pas remplacer sémantique

```html
<!-- ✅ CORRECT - ARIA améliore sémantique native -->
<button aria-expanded="false" aria-controls="menu">
  Menu
</button>
<ul id="menu" hidden>...</ul>

<!-- ✅ CORRECT - Role landmark -->
<div role="search">
  <form>...</form>
</div>

<!-- ❌ MAUVAIS - ARIA redondant -->
<button role="button">Click me</button> <!-- role="button" inutile -->

<!-- ❌ MAUVAIS - ARIA incorrect -->
<div role="button" onclick="handleClick()">
  <!-- Utiliser <button> natif à la place -->
</div>
```

### Checklist Accessibilité (Pré-Commit)

```markdown
- [ ] Contraste couleurs ≥ 4.5:1 (texte), ≥ 3:1 (UI)
- [ ] Focus-visible sur tous les interactifs
- [ ] Touch targets ≥ 44×44px (mobile), ≥ 36×36px acceptable
- [ ] Navigation clavier complète (Tab, Enter, Space, Arrows)
- [ ] ARIA labels sur icons/buttons sans texte
- [ ] Labels associés aux inputs (for/id ou aria-label)
- [ ] Messages d'erreur avec aria-describedby + aria-invalid
- [ ] HTML sémantique (nav, article, button, a)
- [ ] Images avec alt (ou aria-hidden si décoratives)
- [ ] États communiqués (aria-expanded, aria-checked, etc.)
```

---

## 🔄 Workflow de Développement

### 11 Étapes Standard (Workflow Complet)

Défini dans `.github/instructions/02-component-development.md`

```
1. Read specification (docs/02-composants/{level}/{component}.md)
2. Analyze dependencies (atoms required for molecules/organisms)
3. Verify required tokens exist (grep in source/props/)
4. Create file structure (4 files: .twig .css .yml .stories.jsx)
5. Implement Twig template (JSDoc, defaults, ternary, attributes)
6. Implement CSS (3-layer tokens, nesting, semantic colors)
7. Create YAML mock data (Real Estate context, realistic)
8. Create Storybook stories (tags: autodocs, argTypes categories)
9. Build & visual check (npm run build, localhost:6006)
10. Conformity audit (100-point checklist, score ≥ 80/90)
11. Commit & changelog (structured message, update CHANGELOG.md)
```

### Workflow Simplifié (Développement Rapide)

```bash
# 1. Générer structure
npm run generate:pattern -- --type=element --name="Badge"

# 2. Implémenter 4 fichiers
# - badge.twig (template)
# - badge.css (styles tokens)
# - badge.yml (mock data)
# - badge.stories.jsx (Storybook)

# 3. Dev avec hot reload
npm run watch  # http://localhost:6006

# 4. Valider & commiter
npm run build
git add source/patterns/elements/badge/
git commit -m "feat(elements): Add Badge component"
```

---

## 📚 Ressources Complémentaires

### Documentation Interne
- `.github/instructions/01-core-principles.md` – Fondations
- `.github/instructions/02-component-development.md` – Workflow complet
- `.github/instructions/03-technical-implementation.md` – Standards code
- `.github/instructions/04-quality-assurance.md` – Validation

### Références Externes
- [Atomic Design](https://atomicdesign.bradfrost.com/) – Brad Frost
- [BEM Methodology](http://getbem.com/) – Block Element Modifier
- [WCAG 2.2](https://www.w3.org/WAI/WCAG22/quickref/) – Accessibilité
- [CSS Nesting](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_nesting) – MDN

---

**Navigation** : [← Architecture](./architecture.md) | [Glossaire →](./glossaire.md)
