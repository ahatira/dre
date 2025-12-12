# Espacements - Design Tokens

**33 tokens d'espacement + 10 tokens fluides + 5 tokens de contenu**

---

## 📏 Vue d'ensemble

Le système d'espacement PS Theme utilise une **échelle basée sur 0.25rem (4px)** pour garantir la cohérence visuelle et faciliter le calcul mental.

**Formule** : `--size-{n}` = `n × 0.25rem` (pour n ≤ 20)

---

## 📊 Échelle de base (0-20)

| Token | Valeur (rem) | Valeur (px) | Usage principal |
|-------|--------------|-------------|-----------------|
| `--size-px` | 0.063rem | 1px | Bordures pixel-perfect |
| `--size-05` | 0.125rem | 2px | Bordures fines, outline |
| `--size-1` | 0.25rem | 4px | Gaps minimum, padding serré |
| `--size-105` | 0.375rem | 6px | Padding intermédiaire |
| `--size-2` | 0.5rem | 8px | Padding badges, petits gaps |
| `--size-205` | 0.625rem | 10px | Padding intermédiaire |
| `--size-3` | 0.75rem | 12px | Padding standard, gaps |
| `--size-305` | 0.875rem | 14px | Padding intermédiaire |
| `--size-4` | 1rem | **16px** | **Padding par défaut** |
| `--size-5` | 1.25rem | 20px | Padding moyen |
| `--size-6` | 1.5rem | 24px | Padding généreux |
| `--size-7` | 1.75rem | 28px | Padding large |
| `--size-8` | 2rem | 32px | Sections, spacing |
| `--size-9` | 2.25rem | 36px | Grandes sections |
| `--size-10` | 2.5rem | 40px | Spacing important |
| `--size-12` | 3rem | 48px | Sections principales |
| `--size-16` | 4rem | 64px | Sections Hero |
| `--size-20` | 5rem | 80px | Très grandes sections |

---

## 📐 Échelle large (22-96)

Pour les layouts et grandes sections :

| Token | Valeur | Px | Usage |
|-------|--------|-----|-------|
| `--size-24` | 6rem | 96px | Sections Hero |
| `--size-32` | 8rem | 128px | Très larges sections |
| `--size-40` | 10rem | 160px | Containers |
| `--size-48` | 12rem | 192px | Headers |
| `--size-64` | 16rem | 256px | Hero heights |
| `--size-96` | 24rem | 384px | Full sections |

---

## 🌊 Tokens fluides (Responsive)

Espacements adaptatifs avec `clamp()` :

| Token | Min | Préféré | Max | Usage |
|-------|-----|---------|-----|-------|
| `--size-fluid-1` | 0.5rem | 1vw | 1rem | Petits gaps |
| `--size-fluid-2` | 1rem | 2vw | 1.5rem | Padding cartes |
| `--size-fluid-3` | 1.5rem | 3vw | 2rem | Sections moyennes |
| `--size-fluid-4` | 2rem | 4vw | 3rem | Grandes sections |
| `--size-fluid-5` | 4rem | 5vw | 5rem | Hero padding |
| `--size-fluid-8` | 10rem | 20vw | 15rem | Hauteurs Hero |

**Exemple** :
```css
.ps-section {
  padding-block: var(--size-fluid-4); /* 2rem → 4vw → 3rem */
}
```

---

## 📝 Tokens de contenu (Largeurs)

Basés sur les caractères (ch) pour une lisibilité optimale :

| Token | Valeur | Usage |
|-------|--------|-------|
| `--size-content-1` | 20ch | Titres courts |
| `--size-content-2` | 45ch | Paragraphes courts |
| `--size-content-3` | 60ch | Paragraphes longs (optimal) |
| `--size-header-1` | 20ch | Titres niveau 1 |
| `--size-header-2` | 25ch | Titres niveau 2 |

**Exemple** :
```css
.ps-heading { max-width: var(--size-header-1); }
.ps-text { max-width: var(--size-content-3); }
```

---

## 🎯 Guide d'utilisation

### Padding (espacement interne)

```css
/* ✅ CORRECT : Tokens d'espacement */
.ps-button {
  padding: var(--size-3) var(--size-6); /* 12px 24px */
}

.ps-card {
  padding: var(--size-6); /* 24px uniforme */
}

/* ❌ MAUVAIS : Hardcodé */
.ps-button {
  padding: 12px 24px;
}
```

### Margin (espacement externe)

```css
/* ✅ CORRECT */
.ps-section {
  margin-block: var(--size-12); /* 48px vertical */
}

/* ❌ MAUVAIS */
.ps-section {
  margin-top: 48px;
  margin-bottom: 48px;
}
```

### Gap (espacement dans flexbox/grid)

```css
/* ✅ CORRECT */
.ps-card {
  display: flex;
  gap: var(--size-4); /* 16px entre éléments */
}

.ps-grid {
  display: grid;
  gap: var(--size-6); /* 24px */
}
```

---

## 📱 Responsive

Utiliser les tokens fluides OU les media queries :

### Option 1 : Tokens fluides
```css
.ps-hero {
  padding: var(--size-fluid-5); /* Auto-adaptatif */
}
```

### Option 2 : Media queries
```css
.ps-section {
  padding: var(--size-8);
  
  @media (--viewport-md) {
    padding: var(--size-12);
  }
  
  @media (--viewport-lg) {
    padding: var(--size-16);
  }
}
```

---

## ✅ Checklist

- [ ] Utiliser `--size-{n}` pour tous les espacements
- [ ] Privilégier `--size-4` (16px) comme base
- [ ] Utiliser `--size-fluid-{n}` pour responsive
- [ ] Utiliser `--size-content-{n}` pour largeurs de texte
- [ ] Jamais de valeurs px/rem hardcodées

---

## 🔍 Recherche

```bash
# Voir tous les tokens d'espacement
npm run tokens:check -- --size-

# Détecter les espacements hardcodés
grep -rE "[0-9]+px|[0-9\.]+rem" source/patterns/**/*.css | grep -v "var(--"
```

---

**Navigation** : [← Couleurs](./couleurs.md) | [Typographie →](./typographie.md)
