# Autres Tokens - Design Tokens

**35 tokens supplémentaires** (Bordures, Ombres, Animations, Easing, Z-index, Media Queries)

---

## 📐 Bordures

### Largeurs de bordure

| Token | Valeur | Usage |
|-------|--------|-------|
| `--border-size-1` | 1px | **Bordure standard** (cartes, inputs) |
| `--border-size-15` | 1.5px | Bordure intermédiaire |
| `--border-size-2` | 2px | Bordure emphase (focus) |
| `--border-size-3` | 3px | Bordure forte |
| `--border-size-4` | 4px | Bordure très forte |
| `--border-size-5` | 5px | Bordure maximum |

### Rayons de bordure

| Token | Rem | Px | Usage |
|-------|-----|----|-------|
| `--radius-1` | 0.125rem | 2px | Rayon léger |
| `--radius-2` | 0.25rem | 4px | Rayon standard (badges) |
| `--radius-3` | 0.375rem | 6px | Rayon moyen |
| `--radius-4` | 0.5rem | 8px | **Rayon par défaut** (boutons, cartes) |
| `--radius-5` | 0.75rem | 12px | Rayon généreux |
| `--radius-6` | 1rem | 16px | Rayon large |
| `--radius-7` | 1.5rem | 24px | Rayon XL |
| `--radius-round` | 1e5px | ∞ | **Pill complet** (badges, avatars) |

**Exemple** :
```css
.ps-button {
  border: var(--border-size-1) solid var(--border-default);
  border-radius: var(--radius-4); /* 8px */
}

.ps-badge--pill {
  border-radius: var(--radius-round); /* Pill complet */
}
```

---

## 🌑 Ombres

### Ombres externes (élévation)

| Token | Description | Usage |
|-------|-------------|-------|
| `--shadow-1` | Ombrage minimal (2px) | Survol léger |
| `--shadow-2` | Élévation basse (3px) | Cartes posées |
| `--shadow-3` | Élévation moyenne (6px) | **Cartes standard** |
| `--shadow-4` | Élévation haute (15px) | Cartes surélevées, modals |
| `--shadow-5` | Élévation très haute (25px) | Modals importantes |
| `--shadow-6` | Élévation maximum (50px) | Overlays, dropdowns |

### Ombres internes

| Token | Description | Usage |
|-------|-------------|-------|
| `--inner-shadow-0` | Bordure interne | Inputs, zones cliquables |
| `--inner-shadow-1` | Ombrage interne léger | Inputs actifs |
| `--inner-shadow-2` | Ombrage interne moyen | Zones enfoncées |
| `--inner-shadow-3` | Ombrage interne fort | Zones enfoncées (emphase) |
| `--inner-shadow-4` | Ombrage interne intense | Zones très enfoncées |
| `--inner-shadow-5` | Ombrage interne maximum | Zones ultra-enfoncées |

**Exemple** :
```css
.ps-card {
  box-shadow: var(--shadow-3); /* Élévation standard */
  
  &:hover {
    box-shadow: var(--shadow-4); /* Survol */
  }
}

input {
  box-shadow: var(--inner-shadow-1); /* Input enfoncé */
}
```

---

## ⚡ Animations

### Durées

| Token | Valeur | Usage |
|-------|--------|-------|
| `--duration-instant` | 0.1s | Feedback immédiat |
| `--duration-fast` | 0.15s | Transitions rapides |
| `--duration-normal` | 0.3s | **Transitions par défaut** |
| `--duration-slow` | 0.5s | Animations délibérées |
| `--duration-slower` | 0.75s | Emphase |
| `--duration-slowest` | 1s | Grandes transitions |

### Animations prédéfinies

**Entrées/Sorties** :
- `--animation-fade-in` / `--animation-fade-out`
- `--animation-scale-up` / `--animation-scale-down`
- `--animation-slide-in-up/down/left/right`
- `--animation-slide-out-up/down/left/right`

**Boucles** :
- `--animation-spin` : Rotation continue
- `--animation-pulse` : Pulsation
- `--animation-bounce` : Rebond
- `--animation-float` : Flottement
- `--animation-ping` : Ping (radar)

**Exemple** :
```css
.ps-modal {
  animation: var(--animation-fade-in); /* Fade in 0.5s */
}

.ps-spinner {
  animation: var(--animation-spin); /* Spin infini */
}
```

---

## 🎨 Easing (Courbes d'accélération)

### Easing standard

| Token | Description | Usage |
|-------|-------------|-------|
| `--ease-1` à `--ease-5` | Standard (ease-out graduels) | Transitions UI |
| `--ease-in-1` à `--ease-in-5` | Départ lent → Rapide | Sorties |
| `--ease-out-1` à `--ease-out-5` | Rapide → Lent | **Entrées (par défaut)** |
| `--ease-in-out-1` à `--ease-in-out-5` | Lent → Rapide → Lent | Boucles |

### Easing spéciaux

| Token | Description | Usage |
|-------|-------------|-------|
| `--ease-elastic-1` à `-5` | Rebond élastique | Animations ludiques |
| `--ease-squish-1` à `-5` | Squash (compression) | Effets physiques |
| `--ease-step-1` à `-5` | Pas discrets (steps) | Animations pixelisées |

**Exemple** :
```css
.ps-button {
  transition: transform var(--duration-fast) var(--ease-out-3);
  
  &:hover {
    transform: scale(1.05);
  }
}

.ps-notification {
  animation: var(--animation-slide-in-right);
  animation-timing-function: var(--ease-elastic-2); /* Rebond */
}
```

---

## 📚 Z-Index (Profondeur)

| Token | Valeur | Usage |
|-------|--------|-------|
| `--layer-0` | 0 | Base (par défaut) |
| `--layer-1` | 1 | Élévation minimale |
| `--layer-10` | 10 | Sticky headers |
| `--layer-20` | 20 | Dropdowns, tooltips |
| `--layer-30` | 30 | Fixed elements (navbars) |
| `--layer-40` | 40 | Modals |
| `--layer-50` | 50 | Notifications, toasts |
| `--layer-auto` | auto | Contexte par défaut |
| `--layer-important` | 2147483647 | **Absolu maximum** (urgences) |

**Exemple** :
```css
.ps-navbar--fixed {
  position: fixed;
  z-index: var(--layer-30);
}

.ps-modal {
  z-index: var(--layer-40);
}

.ps-toast {
  z-index: var(--layer-50);
}
```

---

## 📱 Breakpoints (Media Queries)

| Token | Seuil | Usage |
|-------|-------|-------|
| `--mobile-sm` | ≥ 400px | Petits mobiles |
| `--mobile` | ≥ 640px | **Mobiles** |
| `--tablet` | ≥ 768px | **Tablettes** |
| `--laptop` | ≥ 1024px | **Laptops** |
| `--desktop` | ≥ 1280px | **Desktops** |
| `--desktop-large` | ≥ 1440px | Grands écrans |
| `--toolbar` | ≥ 976px | Barre admin Drupal |

**Exemple** :
```css
.ps-section {
  padding: var(--size-4);
  
  @media (--tablet) {
    padding: var(--size-8);
  }
  
  @media (--desktop) {
    padding: var(--size-12);
  }
}
```

---

## 🎯 Guide d'utilisation

### Transitions standard

```css
/* ✅ CORRECT : Tokens pour cohérence */
.ps-button {
  transition: 
    background-color var(--duration-fast) var(--ease-out-3),
    transform var(--duration-fast) var(--ease-out-3);
  
  &:hover {
    transform: translateY(-2px);
  }
}

/* ❌ MAUVAIS : Hardcodé */
.ps-button {
  transition: all 0.2s ease;
}
```

### Élévation progressive

```css
.ps-card {
  box-shadow: var(--shadow-2); /* Posée */
  
  &:hover {
    box-shadow: var(--shadow-4); /* Surélevée */
  }
  
  &:active {
    box-shadow: var(--shadow-1); /* Enfoncée */
  }
}
```

---

## ✅ Checklist

- [ ] Bordures : `--border-size-1` + `--radius-4` par défaut
- [ ] Ombres : `--shadow-3` pour cartes standard
- [ ] Transitions : `--duration-normal` + `--ease-out-3`
- [ ] Z-index : Utiliser `--layer-{n}` (10/20/30/40/50)
- [ ] Responsive : `@media (--tablet)` / `(--desktop)`

---

## 🔍 Recherche

```bash
# Voir tous les tokens d'un type
npm run tokens:check -- --border-
npm run tokens:check -- --shadow-
npm run tokens:check -- --duration-
npm run tokens:check -- --ease-
npm run tokens:check -- --layer-

# Détecter hardcodé
grep -rE "border-radius: [0-9]|box-shadow: [0-9]|transition: [0-9]" source/patterns/**/*.css | grep -v "var(--"
```

---

**Navigation** : [← Typographie](./typographie.md) | [Index tokens ↑](./README.md)
