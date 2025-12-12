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

**19 animations complètes** prêtes à l'emploi :

#### Entrées (apparition)

| Animation | Durée | Easing | Description |
|-----------|-------|--------|-------------|
| `--animation-fade-in` | 0.5s | ease-3 | Fondu entrant |
| `--animation-scale-up` | 0.5s | ease-3 | Agrandissement |
| `--animation-slide-in-up` | 0.5s | ease-3 | Glissement depuis bas |
| `--animation-slide-in-down` | 0.5s | ease-3 | Glissement depuis haut |
| `--animation-slide-in-left` | 0.5s | ease-3 | Glissement depuis gauche |
| `--animation-slide-in-right` | 0.5s | ease-3 | Glissement depuis droite |

#### Sorties (disparition)

| Animation | Durée | Easing | Description |
|-----------|-------|--------|-------------|
| `--animation-fade-out` | 0.5s | ease-3 | Fondu sortant |
| `--animation-scale-down` | 0.5s | ease-3 | Rétrécissement |
| `--animation-slide-out-up` | 0.5s | ease-3 | Glissement vers haut |
| `--animation-slide-out-down` | 0.5s | ease-3 | Glissement vers bas |
| `--animation-slide-out-left` | 0.5s | ease-3 | Glissement vers gauche |
| `--animation-slide-out-right` | 0.5s | ease-3 | Glissement vers droite |

#### Boucles infinies

| Animation | Durée | Easing | Description |
|-----------|-------|--------|-------------|
| `--animation-spin` | 2s | linear | **Rotation continue** (spinners) |
| `--animation-pulse` | 2s | ease-out-3 | Pulsation (notifications) |
| `--animation-bounce` | 2s | ease-squish-2 | Rebond (CTA) |
| `--animation-float` | 3s | ease-in-out-3 | Flottement (hero) |
| `--animation-ping` | 5s | ease-out-3 | Ping radar (online status) |
| `--animation-blink` | 1s | ease-out-3 | Clignotement (alertes) |
| `--animation-shake-x` | 0.75s | ease-out-5 | Secousse horizontale (erreur) |
| `--animation-shake-y` | 0.75s | ease-out-5 | Secousse verticale (erreur) |

**Exemple** :
```css
.ps-modal {
  animation: var(--animation-fade-in); /* Fade in 0.5s */
}

.ps-spinner {
  animation: var(--animation-spin); /* Spin infini */
}
```

### Keyframes disponibles

**15 keyframes** utilisables pour créer des animations personnalisées :

#### Opacité

| Keyframe | Effet | Transformation | Usage |
|----------|-------|----------------|-------|
| `fade-in` | Apparition progressive | opacity: 0 → 1 | Modales, popovers, toasts |
| `fade-out` | Disparition progressive | opacity: 1 → 0 | Fermeture d'éléments |
| `blink` | Clignotement | opacity: 1 ↔ 0.5 (loop) | Alertes, notifications live |

#### Échelle (scale)

| Keyframe | Effet | Transformation | Usage |
|----------|-------|----------------|-------|
| `scale-up` | Agrandissement | scale: 1 → 1.25 | Hover cards, zoom |
| `scale-down` | Rétrécissement | scale: 1 → 0.75 | Minimisation |
| `ping` | Ping radar | scale: 1 → 2 + opacity: 1 → 0 | Online status, alerts |
| `pulse` | Pulsation | scale: 1 → 1.1 → 1 | Boutons CTA, attention |

#### Glissements (slide)

| Keyframe | Direction | Transformation | Usage |
|----------|-----------|----------------|-------|
| `slide-in-up` | Depuis bas (↑) | translateY: 100% → 0 | Entré snackbar, bottom sheet |
| `slide-in-down` | Depuis haut (↓) | translateY: -100% → 0 | Dropdown, notification top |
| `slide-in-left` | Depuis gauche (→) | translateX: -100% → 0 | Sidebar, drawer |
| `slide-in-right` | Depuis droite (←) | translateX: 100% → 0 | Sidebar, drawer |
| `slide-out-up` | Vers haut (↑) | translateY: 0 → -100% | Fermeture top |
| `slide-out-down` | Vers bas (↓) | translateY: 0 → 100% | Fermeture bottom |
| `slide-out-left` | Vers gauche (←) | translateX: 0 → -100% | Fermeture drawer |
| `slide-out-right` | Vers droite (→) | translateX: 0 → 100% | Fermeture drawer |

#### Mouvements (loops)

| Keyframe | Effet | Transformation | Usage |
|----------|-------|----------------|-------|
| `shake-x` | Secousse horizontale | translateX: ±5% (loop) | Erreur formulaire |
| `shake-y` | Secousse verticale | translateY: ±5% (loop) | Erreur formulaire |
| `spin` | Rotation continue | rotate: 0 → 360deg | Spinners, loading |
| `float` | Flottement | translateY: 0 → -25% → 0 | Hero, CTA, badges |
| `bounce` | Rebond | translateY: 0 → -20% → -3% (loop) | Scroll indicators, CTA |

**Exemple d'utilisation personnalisée** :

```css
/* Utiliser un keyframe directement */
.custom-animation {
  animation: slide-in-up 0.3s var(--ease-out-3) forwards;
}

/* Combiner plusieurs keyframes */
.complex-animation {
  animation: 
    fade-in 0.5s var(--ease-3),
    scale-up 0.5s var(--ease-elastic-2) 0.2s;
}

/* Modifier les paramètres d'un preset */
.custom-spin {
  animation: spin 1s linear infinite; /* Plus rapide que --animation-spin (2s) */
}
```

**Note** : Les animations prédéfinies (`--animation-*`) utilisent ces keyframes avec des durées et easings optimisés. Utiliser les keyframes directement permet un contrôle granulaire.

---

## 🎨 Easing (Courbes d'accélération)

**35 courbes d'easing** pour des animations naturelles et expressives.

### Easing standard (out)

Courbes **ease-out** (rapide → lent) pour les **entrées** (par défaut) :

| Token | Courbe Bézier | Accélération | Usage |
|-------|---------------|--------------|-------|
| `--ease-1` | cubic-bezier(0.25, 0, 0.5, 1) | Très douce | Transitions subtiles |
| `--ease-2` | cubic-bezier(0.25, 0, 0.4, 1) | Douce | Transitions standards |
| `--ease-3` | cubic-bezier(0.25, 0, 0.3, 1) | **Recommandée** | **Par défaut UI** |
| `--ease-4` | cubic-bezier(0.25, 0, 0.2, 1) | Marquée | Transitions rapides |
| `--ease-5` | cubic-bezier(0.25, 0, 0.1, 1) | Très marquée | Transitions express |

### Easing in (lent → rapide)

Pour les **sorties** (éléments qui quittent l'écran) :

| Token | Courbe Bézier | Usage |
|-------|---------------|-------|
| `--ease-in-1` | cubic-bezier(0.25, 0, 1, 1) | Sortie très douce |
| `--ease-in-2` | cubic-bezier(0.5, 0, 1, 1) | Sortie douce |
| `--ease-in-3` | cubic-bezier(0.7, 0, 1, 1) | **Sortie standard** |
| `--ease-in-4` | cubic-bezier(0.9, 0, 1, 1) | Sortie rapide |
| `--ease-in-5` | cubic-bezier(1, 0, 1, 1) | Sortie très rapide |

### Easing out (rapide → lent)

Pour les **entrées** (éléments qui apparaissent) :

| Token | Courbe Bézier | Usage |
|-------|---------------|-------|
| `--ease-out-1` | cubic-bezier(0, 0, 0.75, 1) | Entrée très douce |
| `--ease-out-2` | cubic-bezier(0, 0, 0.5, 1) | Entrée douce |
| `--ease-out-3` | cubic-bezier(0, 0, 0.3, 1) | **Entrée standard** |
| `--ease-out-4` | cubic-bezier(0, 0, 0.1, 1) | Entrée rapide |
| `--ease-out-5` | cubic-bezier(0, 0, 0, 1) | Entrée très rapide |

### Easing in-out (lent → rapide → lent)

Pour les **boucles** et animations continues :

| Token | Courbe Bézier | Usage |
|-------|---------------|-------|
| `--ease-in-out-1` | cubic-bezier(0.1, 0, 0.9, 1) | Boucle très douce |
| `--ease-in-out-2` | cubic-bezier(0.3, 0, 0.7, 1) | Boucle douce |
| `--ease-in-out-3` | cubic-bezier(0.5, 0, 0.5, 1) | **Boucle standard** |
| `--ease-in-out-4` | cubic-bezier(0.7, 0, 0.3, 1) | Boucle marquée |
| `--ease-in-out-5` | cubic-bezier(0.9, 0, 0.1, 1) | Boucle très marquée |

### Easing élastique (rebond)

Pour des animations **ludiques** avec effet de rebond :

| Token | Courbe Bézier | Rebond | Usage |
|-------|---------------|--------|-------|
| `--ease-elastic-1` | cubic-bezier(0.5, 0.75, 0.75, 1.25) | Léger | Hover subtil |
| `--ease-elastic-2` | cubic-bezier(0.5, 1, 0.75, 1.25) | Moyen | **Notifications** |
| `--ease-elastic-3` | cubic-bezier(0.5, 1.25, 0.75, 1.25) | Fort | Animations express |
| `--ease-elastic-4` | cubic-bezier(0.5, 1.5, 0.75, 1.25) | Très fort | Animations ludiques |
| `--ease-elastic-5` | cubic-bezier(0.5, 1.75, 0.75, 1.25) | Extrême | Effets spéciaux |

### Easing squish (compression)

Pour des effets **physiques** de compression :

| Token | Courbe Bézier | Compression | Usage |
|-------|---------------|-------------|-------|
| `--ease-squish-1` | cubic-bezier(0.5, -0.1, 0.1, 1.5) | Légère | Hover subtil |
| `--ease-squish-2` | cubic-bezier(0.5, -0.3, 0.1, 1.5) | Moyenne | **Boutons pressés** |
| `--ease-squish-3` | cubic-bezier(0.5, -0.5, 0.1, 1.5) | Forte | Animations marquées |
| `--ease-squish-4` | cubic-bezier(0.5, -0.7, 0.1, 1.5) | Très forte | Effets physiques |
| `--ease-squish-5` | cubic-bezier(0.5, -0.9, 0.1, 1.5) | Extrême | Effets spéciaux |

### Easing steps (pas discrets)

Pour des animations **pixelisées** ou par étapes :

| Token | Steps | Usage |
|-------|-------|-------|
| `--ease-step-1` | steps(2) | 2 étapes |
| `--ease-step-2` | steps(3) | 3 étapes |
| `--ease-step-3` | steps(4) | 4 étapes |
| `--ease-step-4` | steps(7) | 7 étapes |
| `--ease-step-5` | steps(10) | 10 étapes |

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

## 📐 Aspects Ratios (Ratios d'image)

**7 ratios prédéfinis** pour des images et vidéos aux proportions harmonieuses :

| Token | Ratio | Valeur CSS | Usage principal |
|-------|-------|------------|-----------------|
| `--ratio-box` | 1:1 | 1 | **Avatars, thumbnails carrés** |
| `--ratio-landscape` | 4:3 | 4/3 | Photos paysage standard |
| `--ratio-photo` | 3:2 | 3/2 | Photos classiques |
| `--ratio-portrait` | 3:4 | 3/4 | Photos portrait |
| `--ratio-widescreen` | 16:9 | 16/9 | **Vidéos HD, images hero** |
| `--ratio-cinemascope` | 21:9 | 21/9 | Format cinéma ultra-large |
| `--ratio-golden` | φ:1 | 1.618/1 | Nombre d'or (harmonique) |

**Exemple** :
```css
/* Forcer un ratio d'image avec aspect-ratio */
.ps-card__image {
  aspect-ratio: var(--ratio-photo); /* 3:2 */
  object-fit: cover;
}

.ps-video {
  aspect-ratio: var(--ratio-widescreen); /* 16:9 */
}

.ps-avatar {
  aspect-ratio: var(--ratio-box); /* 1:1 carré */
}
```

**Alternative avec padding-hack** (support navigateurs anciens) :
```css
.ps-image-container {
  position: relative;
  padding-bottom: calc(100% / var(--ratio-widescreen)); /* 56.25% pour 16:9 */
}

.ps-image-container img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
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
