# Design Tokens - Système de tokens

**Fondation du système de design PS Theme**

---

## 🎨 Qu'est-ce qu'un design token ?

Un **design token** est une **variable CSS nommée** qui stocke une valeur de design (couleur, taille, police, etc.) de manière centralisée et réutilisable.

### Avantages des tokens

1. **Cohérence** : Une seule source de vérité pour toutes les valeurs
2. **Maintenabilité** : Modifier une valeur met à jour tout le système
3. **Scalabilité** : Facile d'ajouter des variantes (dark mode, thèmes)
4. **Lisibilité** : `var(--primary)` > `#00915A` (intention vs valeur)
5. **Standardisation** : Garantit le respect de la charte graphique

### Exemple concret

```css
/* ❌ MAUVAIS : Valeurs hardcodées */
.button {
  background: #00915A;
  padding: 16px;
  font-size: 14px;
  border-radius: 8px;
}

/* ✅ BON : Design tokens */
.button {
  background: var(--primary);
  padding: var(--size-4);
  font-size: var(--font-size-2);
  border-radius: var(--radius-2);
}
```

**Bénéfices** :
- Intention claire (`--primary` = action principale)
- Échelle cohérente (`--size-4` = 1rem dans tout le système)
- Changement global possible (modifier `--primary` met à jour TOUS les boutons)

---

## 📚 Catégories de tokens

PS Theme utilise **100+ design tokens** organisés en 9 catégories :

| Catégorie | Fichier source | Tokens | Documentation |
|-----------|----------------|--------|---------------|
| **Couleurs** | `colors.css` + `brand.css` | 88 tokens | [couleurs.md](./couleurs.md) |
| **Espacements** | `sizes.css` | 33 tokens | [espacements.md](./espacements.md) |
| **Typographie** | `fonts.css` | 18 tokens | [typographie.md](./typographie.md) |
| **Bordures** | `borders.css` | 8 tokens | [autres.md](./autres.md#bordures) |
| **Ombres** | `shadows.css` | 6 tokens | [autres.md](./autres.md#ombres) |
| **Animations** | `animations.css` | 5 tokens | [autres.md](./autres.md#animations) |
| **Easing** | `easing.css` | 5 tokens | [autres.md](./autres.md#easing) |
| **Z-index** | `zindex.css` | 7 tokens | [autres.md](./autres.md#z-index) |
| **Media queries** | `media.css` | 6 tokens | [autres.md](./autres.md#breakpoints) |

**Total** : ~176 tokens disponibles

---

## 🏗️ Architecture en 3 couches

PS Theme utilise une **architecture CSS en 3 couches** :

### Layer 1 : Tokens globaux (Foundation)
**Fichiers** : `source/props/*.css` (colors, sizes, fonts, etc.)  
**Portée** : Application entière  
**Mutabilité** : Rarement modifiés (charte graphique)

```css
/* source/props/colors.css */
:where(html) {
  --green-600: #00915A;
  --primary: var(--green-600);
}
```

### Layer 2 : Tokens composants (Component defaults)
**Fichiers** : `source/patterns/{level}/{component}/{component}.css`  
**Portée** : Composant spécifique  
**Mutabilité** : Modifiables au runtime (composition)

```css
/* source/patterns/elements/button/button.css */
.ps-button {
  --ps-button-bg: var(--primary);        /* Layer 2 */
  --ps-button-padding: var(--size-4);    /* Layer 2 */
  
  background: var(--ps-button-bg);        /* Utilise Layer 2 */
  padding: var(--ps-button-padding);      /* Utilise Layer 2 */
}
```

### Layer 3 : Surcharges contextuelles (Overrides)
**Fichiers** : Composants parents (molécules, organismes)  
**Portée** : Contexte spécifique  
**Mutabilité** : Surcharges ponctuelles (Token-First workflow)

```css
/* source/patterns/components/card/card.css */
.ps-card {
  /* Surcharge du button dans le contexte de la card */
  --ps-button-bg: var(--secondary);      /* Layer 3 override */
  --ps-button-padding: var(--size-3);    /* Layer 3 override */
}
```

**Flux** : Layer 1 (global) → Layer 2 (defaults) → Layer 3 (overrides)

---

## 🎯 Règles d'utilisation

### ✅ À FAIRE

1. **Toujours utiliser des tokens** pour les valeurs de style
2. **Préférer les tokens sémantiques** (`--primary` > `--green-600`)
3. **Utiliser Layer 2** pour les defaults composants
4. **Surcharger via Layer 3** pour la composition (Token-First)
5. **Vérifier l'existence** d'un token avant d'en créer un nouveau

```css
/* ✅ EXCELLENT : Tokens sémantiques + Layer 2 */
.ps-badge {
  --ps-badge-bg: var(--primary);
  --ps-badge-padding: var(--size-2) var(--size-3);
  
  background: var(--ps-badge-bg);
  padding: var(--ps-badge-padding);
}
```

### ❌ À ÉVITER

1. **Valeurs hardcodées** (`#00915A`, `16px`, `150ms`)
2. **Couleurs non sémantiques** (`--green-600` dans composants)
3. **Création de tokens sans gouvernance** (voir processus)
4. **Noms de tokens basés sur la valeur** (`--spacing-16px`)
5. **Duplication de tokens existants**

```css
/* ❌ MAUVAIS : Hardcodé + non sémantique */
.ps-badge {
  background: #00915A;           /* Hardcodé */
  padding: 8px 12px;             /* Hardcodé */
  color: var(--green-600);       /* Non sémantique */
}
```

---

## 🔍 Recherche de tokens

### Commande CLI
```bash
# Chercher un token spécifique
npm run tokens:check -- --primary

# Chercher par catégorie
npm run tokens:check -- --size-
npm run tokens:check -- --font-
```

### Grep manuel
```bash
# Définitions (dans source/props/)
grep -r "--primary" source/props/

# Utilisations (dans components)
grep -r "var(--primary)" source/patterns/

# Valeurs hardcodées (à corriger)
grep -rE "#[0-9a-fA-F]{3,6}" source/patterns/**/*.css
```

### Référence rapide
Tous les tokens disponibles : `source/props/README.md`

---

## 📖 Documentation détaillée

### [couleurs.md](./couleurs.md)
- 88 tokens sémantiques (primary, success, danger, etc.)
- 9 états par couleur (base, hover, active, text, border, subtle, etc.)
- Palette complète (grays, reds, greens, blues, etc.)
- Contrastes WCAG 2.2 AA validés

### [espacements.md](./espacements.md)
- 33 tokens d'espacement (--size-0 à --size-32)
- Échelle: 0.25rem (4px) incréments
- Usage: padding, margin, gap, width, height

### [typographie.md](./typographie.md)
- 9 tailles de police (ratio 1.2)
- 7 poids de police
- Hauteurs de ligne
- Familles de polices (BNP Sans / Open Sans)

### [autres.md](./autres.md)
- Bordures (radius, widths)
- Ombres (5 niveaux)
- Animations (durations)
- Easing (timing functions)
- Z-index (7 couches)
- Breakpoints (6 viewports)

---

## 🛠️ Créer un nouveau token

**Processus de gouvernance obligatoire** (2-5 jours) :

1. **Vérifier** qu'aucun token existant ne convient
2. **Documenter** 3+ cas d'usage
3. **Suivre** le processus : `.github/prompts/create-token.md`
4. **Obtenir** validation design
5. **Ajouter** au fichier props approprié
6. **Documenter** dans cette section

⚠️ **Ne JAMAIS ajouter de tokens directement sans validation**

---

## 📊 Statistiques

- **Tokens définis** : 176
- **Tokens utilisés** : ~150 (dans 6 composants)
- **Valeurs hardcodées restantes** : 0 (objectif atteint ✅)
- **Conformité WCAG 2.2 AA** : 100% (tous contrastes validés)

---

## 🔗 Ressources

- **Fichiers source** : `source/props/`
- **Référence complète** : `source/props/README.md`
- **Créer un token** : `.github/prompts/create-token.md`
- **Instructions** : `.github/instructions/01-core-principles.md`

---

**Navigation** : [← Composants](../02-composants/) | [Couleurs →](./couleurs.md) | [Guide →](../04-guide-developpement/)
