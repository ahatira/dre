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
| `color` | `string` | `'secondary'` | `primary` \| `secondary` \| `success` \| `warning` \| `danger` \| `info` | Couleur sémantique |
| `size` | `string` | `'md'` | `xs` \| `sm` \| `md` \| `lg` \| `xl` | Taille de l'indicateur |
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
  ps-progress--secondary                     // Couleur secondary (gris)
  ps-progress--success                       // Couleur success (vert)
  ps-progress--warning                       // Couleur warning (orange)
  ps-progress--danger                        // Couleur danger (rouge)
  ps-progress--info                          // Couleur info (bleu)
  
  ps-progress--xs                            // Extra small
  ps-progress--sm                            // Small
  ps-progress--md                            // Medium (défaut)
  ps-progress--lg                            // Large
  ps-progress--xl                            // Extra large
  
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

### Couleurs (Variantes sémantiques)

```css
/* Primary */
--ps-color-primary-600: #0DB089;

/* Secondary */
--ps-color-neutral-500: #6E7C89;

/* Success */
--ps-color-success-600: #0DB089;

/* Warning */
--ps-color-warning-600: #FB8C00;

/* Danger */
--ps-color-error-600: #E53935;
--red-600: #E53935;  /* Fallback */

/* Info */
--ps-color-info-600: #039BE5;
--blue-600: #039BE5;  /* Fallback */

/* Track background */
--ps-color-neutral-200: #E8EBEF;
```

### Tailles (Dimensions)

```css
/* Linear heights */
--size-1: 0.25rem;   /* 4px - sm */
--size-2: 0.5rem;    /* 8px - md */
--size-3: 0.75rem;   /* 12px - lg */
--size-4: 1rem;      /* 16px - xl */
/* xs = 2px (hardcoded) */

/* Circular sizes */
--size-8: 2rem;      /* 32px - sm */
--size-10: 2.5rem;   /* 40px - md */
--size-12: 3rem;     /* 48px - lg */
--size-16: 4rem;     /* 64px - xl */
/* xs = 24px (hardcoded) */

/* Spacing */
--ps-spacing-2: 0.5rem;  /* 8px - gap entre barre et label */
```

### Autres tokens

```css
/* Borders */
--ps-border-radius-full: 999px;  /* Bordures arrondies */

/* Typography */
font-family: 'BNPP Sans Condensed', sans-serif;
/* Label sizes adaptatifs selon taille composant: */
--xs: 10px
--sm: 11px
--md: 14px (--font-size-1)
--lg: 16px (--font-size-2)
--xl: 18px (--font-size-3)
--font-weight-medium: 500;

/* Colors (text) */
--ps-color-neutral-700: #3B4754;

/* Transitions */
--ps-transition-duration-normal: 0.3s;
--ps-transition-duration-fast: 0.15s;
--transition-soft-linear: 2s linear;
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
- `/docs/design/tokens/colors.yml`
- `/docs/design/tokens/spacing.yml`
- `/docs/design/tokens/typography.yml`
- `/docs/design/tokens/transitions.yml`
- `/docs/design/tokens/borders.yml`

### Fichiers du composant
- Template: `source/patterns/elements/progress-bar/progress-bar.twig`
- Styles: `source/patterns/elements/progress-bar/progress-bar.css`
- Stories: `source/patterns/elements/progress-bar/progress-bar.stories.jsx`
- Data: `source/patterns/elements/progress-bar/progress-bar.yml`
- Spec: `docs/design/atoms/progress-bar.md`

---

## 📝 Changelog

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
