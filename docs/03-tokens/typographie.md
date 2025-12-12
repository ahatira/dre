# Typographie - Design Tokens

**60 tokens typographiques** (5 familles + 9 poids + 17 tailles + 18 hauteurs de ligne + 6 espaces lettres)

---

## 📚 Vue d'ensemble

Le système typographique PS Theme repose sur **BNPP Sans** (police corporate) et **Open Sans** (police alternative), avec une échelle harmonieuse basée sur un ratio ~1.125.

---

## 🔤 Familles de polices

| Token | Valeur | Usage |
|-------|--------|-------|
| `--font-sans` | BNPP Sans + fallbacks | **Police principale** (corps, titres) |
| `--font-alt` | Open Sans + fallbacks | Texte alternatif |
| `--font-condensed` | BNPP Sans Condensed | Titres compacts, espaces restreints |
| `--font-system` | Polices système | Interfaces système |
| `--font-mono` | Monospace | Code, données techniques |
| `--font-body` | `var(--font-sans)` | **Alias sémantique pour corps** |
| `--font-heading` | `var(--font-sans)` | **Alias sémantique pour titres** |

**Exemple** :
```css
body {
  font-family: var(--font-body);
}

h1, h2, h3 {
  font-family: var(--font-heading);
}

code {
  font-family: var(--font-mono);
}
```

---

## ⚖️ Poids de police

| Token | Valeur | Nom commun | Usage |
|-------|--------|------------|-------|
| `--font-weight-100` | 100 | Thin | Rarement utilisé |
| `--font-weight-200` | 200 | Extra Light | Très léger |
| `--font-weight-300` | 300 | Light | Texte léger |
| `--font-weight-400` | 400 | **Regular** | **Corps par défaut** |
| `--font-weight-500` | 500 | Medium | Emphase légère |
| `--font-weight-600` | 600 | **Semi Bold** | **Titres** |
| `--font-weight-700` | 700 | **Bold** | **Emphase forte** |
| `--font-weight-800` | 800 | Extra Bold | Très fort |
| `--font-weight-900` | 900 | Black | Maximum |

**Recommandations** :
- **Corps** : `--font-weight-400`
- **Titres** : `--font-weight-600` ou `--font-weight-700`
- **Liens/Boutons** : `--font-weight-500` ou `--font-weight-600`

---

## 📏 Échelle de tailles

**Ratio ~1.125** avec base 16px (1rem) :

| Token | Rem | Px | Usage principal |
|-------|-----|-----|-----------------|
| `--font-size--2` | 0.625rem | 10px | Avatar XS initiales |
| `--font-size--1` | 0.75rem | 12px | Légendes, hints |
| `--font-size-0` | 0.875rem | 14px | Texte secondaire |
| `--font-size-1` | 1rem | **16px** | **Corps par défaut** |
| `--font-size-2` | 1.125rem | 18px | Corps moyen |
| `--font-size-3` | 1.25rem | 20px | H6 |
| `--font-size-4` | 1.375rem | 22px | H5 |
| `--font-size-5` | 1.5rem | 24px | H4 |
| `--font-size-6` | 1.75rem | 28px | H3 |
| `--font-size-7` | 2rem | 32px | H2 |
| `--font-size-8` | 2.25rem | 36px | H1 desktop |
| `--font-size-9` | 2.5rem | 40px | H1 large |
| `--font-size-10` | 3rem | 48px | Hero H1 |
| `--font-size-11` | 3.5rem | 56px | Hero large |
| `--font-size-12` | 4rem | 64px | Display titles |
| `--font-size-13` | 5rem | 80px | Display large |
| `--font-size-14` | 7.5rem | 120px | Display XL |

**Exemple** :
```css
body {
  font-size: var(--font-size-1); /* 16px */
}

h1 { font-size: var(--font-size-8); } /* 36px */
h2 { font-size: var(--font-size-7); } /* 32px */
h3 { font-size: var(--font-size-6); } /* 28px */
```

---

## 📐 Hauteurs de ligne (Line Height)

### Valeurs fixes (rem)

| Token | Rem | Px | Usage |
|-------|-----|-----|-------|
| `--leading-3` | 0.75rem | 12px | Très serré |
| `--leading-4` | 1rem | 16px | Badges, boutons |
| `--leading-5` | 1.25rem | 20px | Texte compact |
| `--leading-6` | 1.5rem | 24px | Corps |
| `--leading-7` | 1.75rem | 28px | Corps large |
| `--leading-h4` | 1.875rem | 30px | H4 spécifique |
| `--leading-8` | 2rem | 32px | Titres H3 |
| `--leading-9` | 2.25rem | 36px | Titres H2 |
| `--leading-10` | 2.5rem | 40px | Titres H1 |
| `--leading-11` | 2.75rem | 44px | Hero |
| `--leading-12` | 3rem | 48px | Hero large |
| `--leading-13` | 3.25rem | 52px | Display |
| `--leading-14` | 3.75rem | 60px | Display XL |

### Valeurs relatives (ratio)

| Token | Valeur | Usage |
|-------|--------|-------|
| `--leading-none` | 1 | Titres serrés |
| `--leading-tight` | 1.25 | Titres |
| `--leading-snug` | 1.375 | Sous-titres |
| `--leading-normal` | 1.5 | **Corps par défaut** |
| `--leading-relaxed` | 1.625 | Corps aéré |
| `--leading-loose` | 2 | Très aéré |

**Exemple** :
```css
p {
  line-height: var(--leading-normal); /* 1.5 */
}

h1 {
  line-height: var(--leading-tight); /* 1.25 */
}
```

---

## ✉️ Espacements de lettres (Letter Spacing)

| Token | Valeur | Usage |
|-------|--------|-------|
| `--tracking-tighter` | -0.05em | Titres compacts |
| `--tracking-tight` | -0.025em | Titres |
| `--tracking-normal` | 0 | **Corps par défaut** |
| `--tracking-wide` | 0.025em | Sous-titres |
| `--tracking-wider` | 0.05em | Boutons, labels |
| `--tracking-widest` | 0.1em | Capslock, acronymes |

**Exemple** :
```css
h1 {
  letter-spacing: var(--tracking-tight);
}

button {
  letter-spacing: var(--tracking-wider);
}
```

---

## 🎯 Guide d'utilisation

### Hiérarchie typographique

```css
/* ✅ CORRECT : Tokens pour cohérence */
.ps-hero__title {
  font-family: var(--font-heading);
  font-size: var(--font-size-10); /* 48px */
  font-weight: var(--font-weight-700);
  line-height: var(--leading-tight);
  letter-spacing: var(--tracking-tight);
}

.ps-text {
  font-family: var(--font-body);
  font-size: var(--font-size-1); /* 16px */
  font-weight: var(--font-weight-400);
  line-height: var(--leading-normal);
}

/* ❌ MAUVAIS : Valeurs hardcodées */
.ps-hero__title {
  font-size: 48px;
  line-height: 1.25;
}
```

### Responsive

```css
.ps-heading {
  font-size: var(--font-size-7); /* 32px mobile */
  
  @media (--viewport-md) {
    font-size: var(--font-size-8); /* 36px tablet */
  }
  
  @media (--viewport-lg) {
    font-size: var(--font-size-10); /* 48px desktop */
  }
}
```

---

## ✅ Checklist

- [ ] Utiliser `--font-body` ou `--font-heading` pour familles
- [ ] Corps : `--font-size-1` (16px) + `--leading-normal` (1.5)
- [ ] Titres : `--font-weight-600` minimum
- [ ] Hauteur de ligne : Relative (`--leading-tight`) pour titres, fixe pour layouts
- [ ] Espacements lettres : `--tracking-tight` pour titres larges

---

## 🔍 Recherche

```bash
# Voir tous les tokens typo
npm run tokens:check -- --font-

# Détecter hardcodé
grep -rE "font-size: [0-9]|font-weight: [0-9]" source/patterns/**/*.css | grep -v "var(--"
```

---

**Navigation** : [← Espacements](./espacements.md) | [Autres tokens →](./autres.md)
