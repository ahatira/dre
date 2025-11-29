# Spinner

**Version**: 1.0.0  
**Status**: ✅ Stable  
**Type**: Atom / Element  
**Category**: Feedback / Loading

Indicateur de chargement animé pour états asynchrones (chargement de données, soumission de formulaire, etc.). Trois variantes visuelles disponibles : circular (défaut), dots, et bars. Support complet des couleurs sémantiques (default, primary, secondary, success, info, warning, danger, white).

---

## Props

| Prop | Type | Default | Required | Description |
|------|------|---------|----------|-------------|
| `variant` | `string` | `'circular'` | ❌ | Type de spinner : `circular` \| `dots` \| `bars` |
| `size` | `string` | `'md'` | ❌ | Taille : `xs` (16px) \| `sm` (24px) \| `md` (32px) \| `lg` (48px) \| `xl` (64px) |
| `color` | `string` | `'default'` | ❌ | Couleur : `default` \| `primary` \| `secondary` \| `success` \| `info` \| `warning` \| `danger` \| `white` |
| `text` | `string` | `'Chargement en cours...'` | ❌ | Texte pour lecteurs d'écran (annoncé mais masqué visuellement) |
| `centered` | `boolean` | `false` | ❌ | Centrer dans le conteneur parent (position absolute) |
| `attributes` | `Attribute` | — | ❌ | Attributs HTML additionnels Drupal |

---

## BEM Structure

```
.ps-spinner                      ← Container avec role="status"
  .ps-spinner__svg               ← SVG container (circular uniquement)
  .ps-spinner__circle            ← Cercle animé (circular)
  .ps-spinner__dot               ← Point animé (dots, 3x)
  .ps-spinner__bar               ← Barre animée (bars, 3x)
  .ps-spinner__text              ← Texte masqué visuellement (a11y)

Modifiers:
  .ps-spinner--circular          ← Variante cercle rotatif (défaut, pas de classe)
  .ps-spinner--dots              ← Variante 3 points bouncing
  .ps-spinner--bars              ← Variante 3 barres stretching
  .ps-spinner--xs|sm|md|lg|xl    ← Tailles
  .ps-spinner--default|primary|secondary|success|info|warning|danger|white ← Couleurs
  .ps-spinner--centered          ← Centrage absolu
```

---

## Design Tokens Used

### Sizes
- `--size-4` (16px) — xs
- `--size-6` (24px) — sm
- `--size-8` (32px) — md (défaut)
- `--size-12` (48px) — lg
- `--size-16` (64px) — xl
- `--size-1` (4px) — Gap entre dots
- `--size-105` (6px) — Gap entre bars

### Colors
- `--gray-500` — Default (gris neutre par défaut)
- `--brand-primary` — Primary (vert BNP #00915A)
- `--brand-secondary` — Secondary (rose accent #E0388C)
- `--btn-success` — Success (vert succès)
- `--btn-info` — Info (bleu information)
- `--btn-warning` — Warning (jaune avertissement)
- `--btn-danger` — Danger (rouge erreur)
- `--white` — White (sur fond sombre)

### Visual
- `--radius-round` — Border radius pour dots (cercles parfaits)

---

## Usage Examples

### Basic (Default)

```twig
{% include '@elements/spinner/spinner.twig' with {
  variant: 'circular',
  size: 'md',
  color: 'default',
} %}
```

### Variants

```twig
{# Circular (défaut) #}
{% include '@elements/spinner/spinner.twig' with {
  variant: 'circular',
} %}

{# Dots #}
{% include '@elements/spinner/spinner.twig' with {
  variant: 'dots',
} %}

{# Bars #}
{% include '@elements/spinner/spinner.twig' with {
  variant: 'bars',
} %}
```

### Sizes

```twig
{# Extra small (16px) #}
{% include '@elements/spinner/spinner.twig' with {
  size: 'xs',
} %}

{# Large (48px) #}
{% include '@elements/spinner/spinner.twig' with {
  size: 'lg',
} %}
```

### Colors

```twig
{# Default (gris) #}
{% include '@elements/spinner/spinner.twig' with {
  color: 'default',
} %}

{# Primary (vert BNP) #}
{% include '@elements/spinner/spinner.twig' with {
  color: 'primary',
} %}

{# Secondary (rose) #}
{% include '@elements/spinner/spinner.twig' with {
  color: 'secondary',
} %}

{# Success (vert succès) #}
{% include '@elements/spinner/spinner.twig' with {
  color: 'success',
} %}

{# Info (bleu) #}
{% include '@elements/spinner/spinner.twig' with {
  color: 'info',
} %}

{# Warning (jaune) #}
{% include '@elements/spinner/spinner.twig' with {
  color: 'warning',
} %}

{# Danger (rouge) #}
{% include '@elements/spinner/spinner.twig' with {
  color: 'danger',
} %}

{# White (sur fond sombre) #}
{% include '@elements/spinner/spinner.twig' with {
  color: 'white',
} %}
```

### Centered in Container

```twig
<div style="position: relative; height: 200px;">
  {% include '@elements/spinner/spinner.twig' with {
    centered: true,
    size: 'lg',
    text: 'Chargement de la page...',
  } %}
</div>
```

### Inline with Button

```twig
<button class="ps-button ps-button--primary" disabled>
  {% include '@elements/spinner/spinner.twig' with {
    size: 'xs',
    color: 'white',
  } %}
  Envoi en cours...
</button>
```

---

## Real-World Use Cases

1. **Chargement de page** — Spinner centré pendant le chargement initial
2. **Soumission de formulaire** — Inline dans le bouton submit
3. **Chargement de données** — Dans un tableau ou liste pendant fetch
4. **Upload de fichier** — Indique la progression d'upload
5. **Recherche asynchrone** — À côté du champ de recherche
6. **Navigation** — Pendant transition de page/route

---

## Accessibility

### ✅ WCAG 2.2 Compliance

- **role="status"** — Annonce les changements d'état aux lecteurs d'écran
- **aria-live="polite"** — Annonce non-intrusive (attend que l'utilisateur finisse sa tâche)
- **Texte masqué** — Visuellement caché mais annoncé (sr-only pattern)
- **Pas de focus** — Spinner non-interactif, pas de tabindex

### Best Practices

1. **Toujours inclure du texte** — Le prop `text` est annoncé aux lecteurs d'écran
2. **Contexte clair** — Le texte doit décrire ce qui charge ("Chargement des résultats...")
3. **Contraste suffisant** — Toutes les couleurs respectent WCAG AA (4.5:1)
4. **Animation réduite** — Respecter `prefers-reduced-motion` (à implémenter si besoin)

### Implementation Checklist

- [x] `role="status"` présent
- [x] `aria-live="polite"` présent
- [x] Texte descriptif fourni
- [x] Texte masqué visuellement (sr-only)
- [x] Pas de tabindex (non-interactif)
- [x] Contraste suffisant pour toutes les couleurs

---

## Animations

### Circular
- **Rotation** : 1s linear infinite (cercle SVG tourne)
- **Dash** : 1.5s ease-in-out infinite (stroke-dasharray animé)

### Dots
- **Bounce** : 1.4s ease-in-out infinite both
- Délais par dot : -0.32s, -0.16s, 0s (effet vague)

### Bars
- **Stretch** : 1.2s ease-in-out infinite
- Délais par barre : -0.24s, -0.12s, 0s (effet vague)

---

## Behavior

### Display
- `display: inline-flex` — S'intègre naturellement inline ou block selon contexte
- Centré via `align-items: center` et `justify-content: center`

### Centered Variant
- `position: absolute` + `top: 50%` + `left: 50%`
- `transform: translate(-50%, -50%)` pour centrage parfait
- Nécessite parent avec `position: relative`

### Color Inheritance
- Utilise `currentColor` pour les éléments animés
- Permet de contrôler la couleur via le modifier de couleur ou via CSS parent

---

## Browser Support

✅ Tous navigateurs modernes (Chrome, Firefox, Safari, Edge)  
✅ Animations CSS (keyframes)  
✅ SVG support (pour circular)  
✅ Lecteurs d'écran (NVDA, JAWS, VoiceOver)

---

## Performance

- **Animations GPU** — Utilise `transform` et `opacity` (pas de layout reflow)
- **SVG léger** — Circular utilise un seul `<circle>` SVG
- **Pas de JavaScript** — Animations 100% CSS

---

## Related Components

- **Button** — Spinner inline dans bouton loading
- **Progress Bar** — Alternative pour progression déterminée
- **Skeleton** — Alternative pour chargement de contenu

---

## Testing

### Manual Testing

1. Vérifier animation fluide dans tous navigateurs
2. Tester avec lecteur d'écran (texte annoncé correctement)
3. Vérifier contraste couleurs avec outils (Wave, axe DevTools)
4. Tester centered variant dans différents conteneurs

### Automated Testing

```javascript
// Playwright example
await expect(page.locator('.ps-spinner')).toHaveAttribute('role', 'status');
await expect(page.locator('.ps-spinner')).toHaveAttribute('aria-live', 'polite');
await expect(page.locator('.ps-spinner__text')).toHaveText('Chargement en cours...');
```

---

## Notes Techniques

- **Minimal HTML** : Classes modifiers ajoutées seulement si différent du défaut
- **currentColor** : Permet héritage de couleur depuis parent ou modifier
- **sr-only pattern** : Texte masqué mais accessible (position absolute + clip)
- **Animation delays** : Créent l'effet de vague/cascade pour dots et bars
- **SVG viewBox** : Permet scaling parfait du circular variant

---

## Resources

- [WAI-ARIA - role="status"](https://www.w3.org/TR/wai-aria-1.2/#status)
- [WebAIM - Screen Reader Testing](https://webaim.org/articles/screenreader_testing/)
- [CSS Animations Performance](https://web.dev/animations-guide/)
