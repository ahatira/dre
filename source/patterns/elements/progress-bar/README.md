# Progress Bar

**Catégorie**: Elements (Atoms)  
**Statut**: ✅ Production Ready  
**Version**: 1.1.0

---

## 📋 Description

Indicateur visuel de progression pour des tâches déterminées ou indéterminées. Affiche le pourcentage de complétion d'une opération (upload, téléchargement, formulaire multi-étapes, traitement de données). Disponible en variantes linéaire (barre horizontale) et circulaire (anneau). Supporte les états indéterminés avec animation infinie, les couleurs sémantiques, et l'affichage optionnel du pourcentage.

**Points clés:**
- 2 variantes: linéaire (barre) et circulaire (anneau SVG)
- 6 couleurs sémantiques (primary, secondary, success, warning, danger, info)
- 5 tailles (xs, sm, md, lg, xl)
- État indéterminé avec animation infinie
- Rayures animées pour la variante linéaire
- Label accessible pour lecteurs d'écran
- Conforme WCAG 2.1 avec `role="progressbar"` et attributs ARIA

---

## 🎨 Aperçu visuel

```
Linéaire - Standard:
[████████████░░░░░░░░] 60%

Circulaire - Standard:
    ◷
   60%

Indéterminé - Animation:
[▓▓▓▓░░░░░░░░░░░░░░░░] → animation infinie
```

---

## 📐 Props (API du composant)

| Prop | Type | Défaut | Options | Description |
|------|------|--------|---------|-------------|
| `value` | `number` | `0` | `0-100` | Valeur actuelle de progression (omis si indeterminate) |
| `min` | `number` | `0` | - | Valeur minimale |
| `max` | `number` | `100` | - | Valeur maximale |
| `variant` | `string` | `'linear'` | `linear` \| `circular` | Type d'indicateur |
| `color` | `string` | `'default'` | `default` \| `primary` \| `secondary` \| `info` \| `warning` \| `success` \| `danger` \| `dark` \| `light` | Couleur sémantique (9 variants) |
| `size` | `string` | `'md'` | `xs` \| `sm` \| `md` \| `lg` \| `xl` \| `xxl` | Taille de l'indicateur (6 variants) |
| `indeterminate` | `boolean` | `false` | - | Active l'animation indéterminée (pas de valeur) |
| `striped` | `boolean` | `false` | - | Active les rayures animées (linéaire uniquement) |
| `showLabel` | `boolean` | `false` | - | Affiche le pourcentage en texte |
| `label` | `string` | - | - | Label pour lecteurs d'écran (ARIA) |
| `attributes` | `Attribute` | - | - | Attributs Drupal additionnels |

---

## 🏗️ Structure BEM

```
ps-progress                                  // Block principal
  ps-progress__track                         // Conteneur de piste (linéaire)
  ps-progress__fill                          // Barre de remplissage (linéaire)
  ps-progress__svg                           // Conteneur SVG (circulaire)
  ps-progress__track-circle                  // Cercle de fond (circulaire)
  ps-progress__fill-circle                   // Cercle de progression (circulaire)
  ps-progress__label                         // Label texte (pourcentage)

Modificateurs:
  ps-progress--linear                        // Variante linéaire (défaut)
  ps-progress--circular                      // Variante circulaire
  
  ps-progress--primary                       // Couleur primary (vert)
  ps-progress--secondary                     // Couleur secondary (purple)
  ps-progress--info                          // Couleur info (bleu)
  ps-progress--warning                       // Couleur warning (orange)
  ps-progress--success                       // Couleur success (vert)
  ps-progress--danger                        // Couleur danger (rouge)
  ps-progress--dark                          // Couleur dark (near black)
  ps-progress--light                         // Couleur light (near white)
  
  ps-progress--xs                            // Extra small (2px / 24px)
  ps-progress--sm                            // Small (4px / 32px)
  ps-progress--md                            // Medium (8px / 40px - défaut)
  ps-progress--lg                            // Large (12px / 48px)
  ps-progress--xl                            // Extra large (16px / 64px)
  ps-progress--xxl                           // Extra extra large (24px / 80px)
  
  ps-progress--indeterminate                 // Animation indéterminée
  ps-progress--striped                       // Rayures animées (linéaire)
  ps-progress--with-label                    // Avec label texte
```

### Exemple HTML (Linear)

```html
<div class="ps-progress" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" aria-label="Upload en cours">
  <div class="ps-progress__track">
    <div class="ps-progress__fill" style="width: 60%;"></div>
  </div>
  <span class="ps-progress__label">60%</span>
</div>
```

### Exemple HTML (Circular)

```html
<div class="ps-progress ps-progress--circular" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
  <svg class="ps-progress__svg" viewBox="0 0 100 100">
    <circle class="ps-progress__track-circle" cx="50" cy="50" r="45" fill="none" stroke-width="8"></circle>
    <circle class="ps-progress__fill-circle" cx="50" cy="50" r="45" fill="none" stroke-width="8" stroke-dasharray="282.743" stroke-dashoffset="70.686"></circle>
  </svg>
  <span class="ps-progress__label">75%</span>
</div>
```

---

## 🎨 Design Tokens Utilisés

### System (3-Layer CSS Variables)

**Layer 1: Root primitives** (source/props/*.css)
```css
/* Colors */
--green-600: hsl(162, 72%, 38%);
--blue-600: hsl(220, 89%, 53%);
--red-600: hsl(0, 75%, 51%);
--yellow-500: hsl(37, 97%, 39%);
--gray-200: hsl(220, 13%, 91%);
--gray-300: hsl(216, 12%, 84%);
--gray-500: hsl(220, 9%, 46%);
--gray-600: hsl(215, 14%, 34%);
--gray-700: hsl(217, 19%, 27%);
--gray-900: hsl(221, 39%, 11%);

/* Sizes */
--size-1: 0.25rem;  /* 4px */
--size-2: 0.5rem;   /* 8px */
--size-3: 0.75rem;  /* 12px */
--size-4: 1rem;     /* 16px */
--size-6: 1.5rem;   /* 24px */
--size-8: 2rem;     /* 32px */
--size-10: 2.5rem;  /* 40px */
--size-12: 3rem;    /* 48px */
--size-16: 4rem;    /* 64px */

/* Borders */
--radius-round: 1e5px; /* Pill shape */

/* Typography */
--font-condensed: 'BNPP Sans Condensed', sans-serif;
--font-size-0: 0.75rem;  /* 12px */
--font-size-1: 1rem;     /* 16px */
--font-size-2: 1.125rem; /* 18px */
--font-size-3: 1.25rem;  /* 20px */
--font-size-4: 1.5rem;   /* 24px */
--font-weight-500: 500;

/* Animations */
--duration-normal: 0.3s;
--duration-slower: 0.75s;
--duration-slowest: 1s;
--ease-3: cubic-bezier(0.25, 0, 0.3, 1);
--ease-in-out-3: cubic-bezier(0.65, 0, 0.35, 1);
```

**Layer 2: Component variables** (.ps-progress)
```css
/* Layout */
--ps-progress-gap: var(--ps-spacing-2, var(--size-2));

/* Track (background) */
--ps-progress-track-bg: var(--gray-200);
--ps-progress-track-height: var(--size-2); /* 8px - MD default */
--ps-progress-track-radius: var(--radius-round);

/* Fill (foreground) */
--ps-progress-fill-bg: var(--gray-500); /* Default neutral */
--ps-progress-fill-transition: width var(--duration-normal) var(--ease-3);

/* Circular */
--ps-progress-circular-size: var(--size-10); /* 40px - MD default */
--ps-progress-stroke-width: 8;
--ps-progress-stroke-transition: stroke-dashoffset var(--duration-normal) var(--ease-3);

/* Label */
--ps-progress-label-size: var(--font-size-1); /* 16px - MD default */
--ps-progress-label-weight: var(--font-weight-500);
--ps-progress-label-color: var(--gray-700);

/* Typography */
--ps-progress-font-family: var(--font-condensed);
```

**Layer 3: Context overrides** (modifier classes)
```css
/* Example: Primary color modifier */
.ps-progress--primary {
  --ps-progress-fill-bg: var(--green-600);
}

/* Example: Large size modifier */
.ps-progress--lg {
  --ps-progress-track-height: var(--size-3);
  --ps-progress-circular-size: var(--size-12);
  --ps-progress-label-size: var(--font-size-2);
}
```

---

## 🧩 Exemples d'utilisation

### Twig (Drupal)

#### Upload de fichier (linear)

```twig
{% include '@ps_theme/ps-progress/ps-progress.twig' with {
  variant: 'linear',
  value: 65,
  color: 'primary',
  size: 'md',
  showLabel: true,
  label: 'Upload de document.pdf'
} %}
```

#### Statut de profil (circular)

```twig
{% include '@ps_theme/ps-progress/ps-progress.twig' with {
  variant: 'circular',
  value: 75,
  color: 'success',
  size: 'lg',
  showLabel: true,
  label: 'Complétion du profil'
} %}
```

#### Chargement indéterminé

```twig
{% include '@ps_theme/ps-progress/ps-progress.twig' with {
  variant: 'linear',
  indeterminate: true,
  color: 'info',
  label: 'Chargement en cours'
} %}
```

#### Traitement avec avertissement (striped)

```twig
{% include '@ps_theme/ps-progress/ps-progress.twig' with {
  variant: 'linear',
  value: 45,
  color: 'warning',
  striped: true,
  showLabel: true,
  label: 'Traitement des données'
} %}
```

#### Espace disque critique

```twig
{% include '@ps_theme/ps-progress/ps-progress.twig' with {
  variant: 'linear',
  value: 30,
  color: 'danger',
  showLabel: true,
  label: 'Espace disque restant: 30%'
} %}
```

---

## 💼 Cas d'usage réels

### 1. Upload/Download de fichiers
```twig
{# Progression d'upload avec label dynamique #}
{% include '@ps_theme/ps-progress/ps-progress.twig' with {
  variant: 'linear',
  value: file_upload_percentage,
  color: 'primary',
  size: 'md',
  showLabel: true,
  label: 'Upload: ' ~ file_name
} %}
```

### 2. Formulaire multi-étapes
```twig
{# Étape 3 sur 5 = 60% #}
{% include '@ps_theme/ps-progress/ps-progress.twig' with {
  variant: 'linear',
  value: (current_step / total_steps * 100)|round,
  color: 'info',
  showLabel: true,
  label: 'Étape ' ~ current_step ~ ' sur ' ~ total_steps
} %}
```

### 3. Complétion de profil utilisateur
```twig
{# Affichage circulaire avec couleur conditionnelle #}
{% set completion = profile_completion_percentage %}
{% set color = completion < 40 ? 'danger' : (completion < 80 ? 'warning' : 'success') %}

{% include '@ps_theme/ps-progress/ps-progress.twig' with {
  variant: 'circular',
  value: completion,
  color: color,
  size: 'lg',
  showLabel: true,
  label: 'Profil complété à ' ~ completion ~ '%'
} %}
```

### 4. Chargement de données (indéterminé)
```twig
{# Animation infinie pendant le fetch API #}
{% include '@ps_theme/ps-progress/ps-progress.twig' with {
  variant: 'linear',
  indeterminate: true,
  color: 'primary',
  label: 'Chargement des données en cours'
} %}
```

### 5. Traitement longue durée avec alerte
```twig
{# Traitement avec rayures et couleur warning #}
{% include '@ps_theme/ps-progress/ps-progress.twig' with {
  variant: 'linear',
  value: processing_percentage,
  color: 'warning',
  striped: true,
  showLabel: true,
  label: 'Traitement intensif: ' ~ processing_percentage ~ '%'
} %}
```

### 6. Quota/Limite (espace disque, bande passante)
```twig
{# Usage à 85% = danger imminent #}
{% include '@ps_theme/ps-progress/ps-progress.twig' with {
  variant: 'linear',
  value: disk_usage_percentage,
  color: disk_usage_percentage > 80 ? 'danger' : 'primary',
  size: 'lg',
  showLabel: true,
  label: 'Espace utilisé: ' ~ disk_usage_percentage ~ '%'
} %}
```

---

## ♿ Accessibilité (WCAG 2.1)

### Attributs ARIA obligatoires

```html
<div 
  role="progressbar"
  aria-valuenow="60"        <!-- Valeur actuelle (omis si indeterminate) -->
  aria-valuemin="0"         <!-- Valeur minimale -->
  aria-valuemax="100"       <!-- Valeur maximale -->
  aria-label="Upload en cours"  <!-- Description du contexte -->
>
```

### Points clés

- **`role="progressbar"`** : Identifie l'élément comme indicateur de progression
- **`aria-valuenow`** : Valeur actuelle (omis en mode indeterminate)
- **`aria-valuemin`/`aria-valuemax`** : Plage de valeurs
- **`aria-label`** : Description contextuelle pour lecteurs d'écran
- **Pas de focus** : Élément non-interactif (pas de tabindex)
- **Contraste** : Minimum 3:1 entre track et fill (conforme WCAG AA)
- **Animation réduite** : Les animations respectent `prefers-reduced-motion` (à implémenter si nécessaire)

### Test de conformité

```bash
# Lighthouse Accessibility Score
# - Progress bar correctement identifié
# - ARIA attributes valides
# - Contraste suffisant (vérifié avec outils comme Axe)
```

---

## 📱 Comportement Responsive

### Linéaire
- **Largeur fluide** : `flex: 1` permet adaptation au conteneur
- **Hauteur fixe** : Déterminée par le modificateur de taille
- **Label** : Reste lisible sur mobile (nowrap, taille adaptée)

### Circulaire
- **Taille fixe** : Basée sur les tokens `--size-*`
- **Centré** : `inline-flex` permet alignement facile
- **SVG responsive** : `viewBox` assure proportions correctes

### Recommandations mobile
- Utiliser `size="xs"` ou `size="sm"` sur petits écrans pour économiser l'espace
- Label externe recommandé si `showLabel: false` pour clarté

---

## 🎯 Bonnes pratiques

### ✅ À faire
- Toujours fournir un `label` pour l'accessibilité
- Utiliser `indeterminate` quand la durée est inconnue
- Choisir la couleur sémantique appropriée au contexte (success = validation, danger = critique)
- Afficher `showLabel: true` quand la précision du pourcentage importe
- Utiliser `striped` pour attirer l'attention sur des traitements longs

### ❌ À éviter
- Ne pas utiliser sans `aria-label` (mauvais pour accessibilité)
- Ne pas utiliser `striped` avec `circular` (non supporté)
- Ne pas mettre `value` en mode `indeterminate` (ignoré)
- Ne pas utiliser pour des actions instantanées (< 1s)
- Ne pas changer de couleur sans raison sémantique

---

## 🔗 Ressources

### Documentation officielle
- [WAI-ARIA Progressbar Pattern](https://www.w3.org/WAI/ARIA/apg/patterns/meter/)
- [MDN: role="progressbar"](https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Roles/progressbar_role)
- [WCAG 2.1 - Contrast Requirements](https://www.w3.org/WAI/WCAG21/Understanding/contrast-minimum.html)

### Design tokens référencés
### v2.0.0 (2025-12-02)
- 🔥 **BREAKING**: Migration vers système CSS 3 couches (Bootstrap 5 inspired)
  - Layer 1: Root primitives (--green-600, --size-*, etc.)
  - Layer 2: Component-scoped variables (--ps-progress-*, runtime customizable)
  - Layer 3: Context overrides (modifier classes)
- ✅ **NEW**: Support de 9 couleurs sémantiques (ajout de `dark` et `light`)
- ✅ **NEW**: Support de 6 tailles (ajout de `xxl`: 24px/80px)
- 🔧 Harmonisation des tokens : suppression hardcoded values (2px, 10px, 24px, etc.)
- 🔧 Fix animations : utilisation tokens corrects (--duration-*, --ease-*)
- 🔧 Amélioration structure CSS : nesting complet, ordre cascade respecté
- 📚 Documentation complète du système 3 couches dans README
- 🌍 Placeholders immobiliers dans Stories (property, lease, agent, etc.)

### v1.1.0 (2025-11-29)
- 🔧 **BREAKING**: Renommage tailles `small/medium/large` → `xs/sm/md/lg/xl`
- 🔧 Correction couleur `secondary`: gray → purple (#E0388C)
- 🔧 Font-family: `'BNPP Sans Condensed'` avec tailles adaptatives de label
- 🐛 Fix animation `striped`: gradient corrigé, animation fonctionnelle
- ✅ Support 5 tailles (xs | sm | md | lg | xl)
- ✅ Label responsive selon taille composant (10px → 18px)

### v1.0.0 (2025-11-29)
- ✅ Implémentation initiale (linear + circular)
- ✅ Support 6 couleurs sémantiques (primary, secondary, success, warning, danger, info)
- ✅ État indeterminate avec animation
- ✅ Rayures animées (striped) pour linear
- ✅ Accessibilité WCAG 2.1 complète
- ✅ Stories Storybook complètes (toutes variantes + showcases)
- ✅ Documentation README complète

### v1.1.0 (2025-11-29)
- 🔧 **BREAKING**: Renommage tailles `small/medium/large` → `xs/sm/md/lg/xl`
- 🔧 Correction couleur `secondary`: gray → purple (#E0388C)
- 🔧 Font-family: `'BNPP Sans Condensed'` avec tailles adaptatives de label
- 🐛 Fix animation `striped`: gradient corrigé, animation fonctionnelle
- ✅ Support 5 tailles (xs | sm | md | lg | xl)
- ✅ Label responsive selon taille composant (10px → 18px)

### v1.0.0 (2025-11-29)
- ✅ Implémentation initiale (linear + circular)
- ✅ Support 6 couleurs sémantiques (primary, secondary, success, warning, danger, info)
- ✅ État indeterminate avec animation
- ✅ Rayures animées (striped) pour linear
- ✅ Accessibilité WCAG 2.1 complète
- ✅ Stories Storybook complètes (toutes variantes + showcases)
- ✅ Documentation README complète
