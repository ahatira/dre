# Audit des Utilities CSS - PS Theme

**Date**: 2025-12-13  
**Objectif**: Identifier les utilities manquantes pour implémenter une approche utility-first

---

## 📊 État actuel des utilities

### ✅ Utilities EXISTANTES (bien structurées)

#### 1. **Backgrounds** (`backgrounds.css`) - **COMPLET ✅**
- ✅ 9 couleurs sémantiques: `bg-primary`, `bg-secondary`, `bg-success`, `bg-danger`, `bg-warning`, `bg-info`, `bg-gold`, `bg-light`, `bg-dark`
- ✅ Variants subtils: `bg-*-subtle` pour toutes les couleurs
- ✅ Grays: `bg-gray-50` à `bg-gray-900`
- ✅ Contextuels: `bg-white`, `bg-black`, `bg-transparent`, `bg-page`, `bg-section`

#### 2. **Typography - Headings** (`typography.css`) - **COMPLET ✅**
- ✅ Classes heading: `.h1` à `.h6` (responsive)
- ✅ Display: `.display-1` à `.display-6`
- ✅ Body: `.lead`, `.small`, `.caption`, `.code`, `.overline`

#### 3. **Typography - Alignment** (`typography.css`) - **COMPLET ✅**
- ✅ Text align: `.text-left`, `.text-center`, `.text-right`, `.text-justify`

#### 4. **Typography - Transform** (`typography.css`) - **COMPLET ✅**
- ✅ Transform: `.uppercase`, `.lowercase`, `.capitalize`, `.normal-case`

#### 5. **Typography - Decoration** (`typography.css`) - **COMPLET ✅**
- ✅ Decoration: `.underline`, `.no-underline`, `.line-through`, `.overline-text`

#### 6. **Spacing** (`spacing.css`) - **PARTIEL ⚠️**
- ✅ Margin bottom: `.mb-0`, `.mb-2`, `.mb-5`, `.mb-10`, `.mb-15`
- ✅ Margin top: `.mt-5`, `.mt-10`, `.mt-15`
- ✅ Padding: `.p-5`, `.p-10`
- ❌ **MANQUE**: Échelle complète spacing (m-0 à m-20, p-0 à p-20, mx, my, px, py, etc.)

#### 7. **Autres utilities** - **COMPLETS ✅**
- ✅ Container: `.container`, `.container-fluid`
- ✅ Grid: `.grid`, colonnes
- ✅ Aspect ratio: `.aspect-16x9`, etc.
- ✅ Overflow: `.overflow-hidden`, `.overflow-auto`
- ✅ Position: `.relative`, `.absolute`, `.fixed`, `.sticky`
- ✅ Sizing: `.w-full`, `.h-full`, etc.
- ✅ Visibility: `.visually-hidden`, `.hidden`, `.visible`
- ✅ Align: `.align-center`, `.align-left`, `.align-right`
- ✅ SVG: Utilities SVG
- ✅ Prose: Content formatting
- ✅ List: `.list-unstyled`

---

## ❌ Utilities MANQUANTES (critiques pour utility-first)

### 1. **TEXT COLORS SÉMANTIQUES** - **CRITIQUE ❌**

**Actuellement** (typography.css):
```css
.text-primary   { color: var(--text-primary, var(--gray-700)); }  /* ❌ Mauvais token */
.text-secondary { color: var(--text-secondary, var(--gray-500)); } /* ❌ Mauvais token */
.text-tertiary  { color: var(--text-tertiary, var(--gray-400)); }
.text-inverse   { color: var(--white); }
.text-link      { color: var(--primary); }
```

**PROBLÈME**: `.text-primary` utilise `--text-primary` (token gris) au lieu de `--primary` (token vert brand)!

**MANQUE** toutes les couleurs sémantiques:
```css
/* ❌ N'EXISTENT PAS */
.text-primary   { color: var(--primary); }      /* Vert brand */
.text-secondary { color: var(--secondary); }    /* Pink brand */
.text-success   { color: var(--success); }      /* Teal */
.text-danger    { color: var(--danger); }       /* Red */
.text-warning   { color: var(--warning); }      /* Yellow */
.text-info      { color: var(--info); }         /* Blue */
.text-gold      { color: var(--gold); }         /* Gold */
.text-light     { color: var(--light); }        /* Light gray */
.text-dark      { color: var(--dark); }         /* Dark gray */
```

---

### 2. **FONT WEIGHTS** - **CRITIQUE ❌**

**Actuellement**: AUCUNE classe utility pour font-weight!

**MANQUE**:
```css
/* ❌ N'EXISTENT PAS */
.font-thin       { font-weight: var(--font-weight-100); }
.font-extralight { font-weight: var(--font-weight-200); }
.font-light      { font-weight: var(--font-weight-300); }
.font-regular    { font-weight: var(--font-weight-400); }
.font-medium     { font-weight: var(--font-weight-500); }
.font-semibold   { font-weight: var(--font-weight-600); }
.font-bold       { font-weight: var(--font-weight-700); }
.font-extrabold  { font-weight: var(--font-weight-800); }
.font-black      { font-weight: var(--font-weight-900); }
```

---

### 3. **BORDERS** - **CRITIQUE ❌**

**Actuellement**: AUCUN fichier `borders.css` dans utilities!

**MANQUE**:
```css
/* ❌ N'EXISTENT PAS */
/* Border widths */
.border         { border: var(--border-size-1) solid var(--border-default); }
.border-0       { border: 0; }
.border-t       { border-top: var(--border-size-1) solid var(--border-default); }
.border-r       { border-right: var(--border-size-1) solid var(--border-default); }
.border-b       { border-bottom: var(--border-size-1) solid var(--border-default); }
.border-l       { border-left: var(--border-size-1) solid var(--border-default); }

/* Border colors */
.border-primary   { border-color: var(--primary-border); }
.border-secondary { border-color: var(--secondary-border); }
.border-success   { border-color: var(--success-border); }
.border-danger    { border-color: var(--danger-border); }
.border-warning   { border-color: var(--warning-border); }
.border-info      { border-color: var(--info-border); }

/* Border radius */
.rounded-none   { border-radius: 0; }
.rounded-sm     { border-radius: var(--radius-1); }
.rounded        { border-radius: var(--radius-2); }
.rounded-md     { border-radius: var(--radius-3); }
.rounded-lg     { border-radius: var(--radius-4); }
.rounded-xl     { border-radius: var(--radius-5); }
.rounded-full   { border-radius: var(--radius-round); }
```

---

### 4. **SHADOWS** - **CRITIQUE ❌**

**Actuellement**: AUCUN fichier `shadows.css` dans utilities!

**MANQUE**:
```css
/* ❌ N'EXISTENT PAS */
.shadow-none   { box-shadow: none; }
.shadow-sm     { box-shadow: var(--shadow-1); }
.shadow        { box-shadow: var(--shadow-2); }
.shadow-md     { box-shadow: var(--shadow-3); }
.shadow-lg     { box-shadow: var(--shadow-4); }
.shadow-xl     { box-shadow: var(--shadow-5); }
.shadow-2xl    { box-shadow: var(--shadow-6); }
```

---

### 5. **FLEXBOX** - **MANQUANT ❌**

**MANQUE**:
```css
/* ❌ N'EXISTENT PAS */
.flex           { display: flex; }
.inline-flex    { display: inline-flex; }
.flex-row       { flex-direction: row; }
.flex-col       { flex-direction: column; }
.flex-wrap      { flex-wrap: wrap; }
.items-center   { align-items: center; }
.items-start    { align-items: flex-start; }
.items-end      { align-items: flex-end; }
.justify-center { justify-content: center; }
.justify-between { justify-content: space-between; }
.justify-around { justify-content: space-around; }
.gap-2          { gap: var(--size-2); }
.gap-4          { gap: var(--size-4); }
.gap-6          { gap: var(--size-6); }
```

---

### 6. **DISPLAY** - **MANQUANT ❌**

**MANQUE**:
```css
/* ❌ N'EXISTENT PAS */
.block          { display: block; }
.inline-block   { display: inline-block; }
.inline         { display: inline; }
.hidden         { display: none; }
```

---

### 7. **OPACITY** - **MANQUANT ❌**

**MANQUE**:
```css
/* ❌ N'EXISTENT PAS */
.opacity-0      { opacity: 0; }
.opacity-25     { opacity: 0.25; }
.opacity-50     { opacity: 0.5; }
.opacity-75     { opacity: 0.75; }
.opacity-100    { opacity: 1; }
```

---

### 8. **TRANSITIONS** - **MANQUANT ❌**

**MANQUE**:
```css
/* ❌ N'EXISTENT PAS */
.transition          { transition: all var(--duration-normal) var(--ease-3); }
.transition-colors   { transition: color var(--duration-fast) var(--ease-3), background-color var(--duration-fast) var(--ease-3); }
.transition-opacity  { transition: opacity var(--duration-normal) var(--ease-3); }
.transition-transform { transition: transform var(--duration-normal) var(--ease-3); }
```

---

## 📋 Plan d'action recommandé

### Phase 1: **Utilities critiques pour Heading/Badge** (PRIORITAIRE)
1. ✅ Créer `colors.css` utility avec `.text-*` sémantiques (9 couleurs)
2. ✅ Ajouter font-weights dans `typography.css` (`.font-light` à `.font-black`)
3. ✅ Créer `borders.css` avec border-radius utilities

### Phase 2: **Utilities essentielles UI**
4. ✅ Créer `shadows.css` avec shadow utilities
5. ✅ Créer `flexbox.css` avec display flex utilities
6. ✅ Créer `display.css` avec display utilities
7. ✅ Compléter `spacing.css` (échelle complète m/p)

### Phase 3: **Utilities avancées**
8. ✅ Créer `opacity.css`
9. ✅ Créer `transitions.css`

---

## 🔧 Refactoring composants

Une fois utilities créées, refactorer:
1. **Heading** → Utiliser `.h1`, `.text-primary`, `.font-bold`, `.text-center`
2. **Badge** → Utiliser `.bg-primary`, `.text-white`, `.rounded-full`, `.font-bold`
3. **Button** → Idem
4. Tous les autres composants

---

## 📝 Notes

- **Source de vérité**: `source/props/*.css` pour tous les tokens
- **Convention naming**: Tailwind-inspired mais adapté aux tokens PS Theme
- **Responsive**: Ajouter breakpoints media queries si nécessaire
- **Documentation**: Chaque utility file doit avoir header explicatif
