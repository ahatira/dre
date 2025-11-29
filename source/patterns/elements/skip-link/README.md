# Skip Link

**Version**: 1.0.0  
**Status**: ✅ Stable  
**Type**: Atom / Element  
**Category**: Accessibility

Lien d'évitement WCAG permettant aux utilisateurs clavier de naviguer directement vers le contenu principal en sautant la navigation répétitive.

---

## Props

| Prop | Type | Default | Required | Description |
|------|------|---------|----------|-------------|
| `targetId` | `string` | `'main-content'` | ✅ | ID de l'ancre HTML cible (ex: main-content, navigation, search) |
| `label` | `string` | `'Passer au contenu principal'` | ✅ | Texte affiché dans le lien |
| `attributes` | `Attribute` | — | ❌ | Attributs HTML additionnels Drupal |

---

## BEM Structure

```
.ps-skip-link                    ← Anchor link (no child elements)
```

**Note**: Composant simple sans éléments enfants ni modifiers. Un seul bloc BEM.

---

## Design Tokens Used

### Layout & Positioning
- `--size-4` (16px) — Positionnement top/left
- `--layer-important` — Z-index au-dessus de tout

### Spacing
- `--size-3` (12px) — Padding vertical
- `--size-4` (16px) — Padding horizontal

### Colors
- `--brand-primary` — Background (vert BNP #00915A)
- `--white` — Text color
- `--blue-600` — Focus outline

### Visual
- `--radius-2` (4px) — Border radius
- `--shadow-3` — Box shadow medium
- `--border-size-2` (2px) — Outline width

### Typography
- `--font-sans` — Police principale
- `--font-size-1` (14px) — Taille texte
- `--font-weight-500` — Poids medium
- `--leading-normal` — Line height

---

## Usage Examples

### Basic (Default)

```twig
{% include '@elements/skip-link/skip-link.twig' with {
  targetId: 'main-content',
  label: 'Passer au contenu principal',
} %}
```

### Skip to Navigation

```twig
{% include '@elements/skip-link/skip-link.twig' with {
  targetId: 'navigation',
  label: 'Passer à la navigation',
} %}
```

### Skip to Search

```twig
{% include '@elements/skip-link/skip-link.twig' with {
  targetId: 'search',
  label: 'Passer à la recherche',
} %}
```

### Page Header Integration

```twig
{# Place as FIRST element in <body> #}
<body>
  {% include '@elements/skip-link/skip-link.twig' with {
    targetId: 'main-content'
  } %}
  
  <header>
    {# Header content #}
  </header>
  
  <main id="main-content" tabindex="-1">
    {# Main content #}
  </main>
</body>
```

---

## Real-World Use Cases

1. **Contenu principal** — Sauter header et navigation pour aller au contenu
2. **Navigation** — Aller directement au menu principal
3. **Recherche** — Accéder rapidement au champ de recherche
4. **Footer** — Sauter tout le contenu pour atteindre le pied de page

---

## Accessibility

### ✅ WCAG 2.2 Compliance

- **Critère 2.4.1 (Niveau A)** : Bypass Blocks — Mécanisme pour sauter les blocs répétitifs
- **Critère 2.4.4 (Niveau A)** : Link Purpose — Le texte du lien décrit clairement la destination

### Best Practices

1. **Position**: DOIT être le premier élément focusable de la page
2. **Visibilité**: Invisible par défaut, visible uniquement au focus clavier
3. **Cible**: L'ancre cible DOIT exister et avoir `tabindex="-1"` pour focus programmatique
4. **Multiple skip links**: Possible (main, nav, search) mais rare — privilégier un seul vers le main

### Implementation Checklist

- [x] Link est le premier élément dans `<body>`
- [x] `href` pointe vers un ID valide existant dans la page
- [x] Cible a `tabindex="-1"` pour recevoir le focus
- [x] Visible au focus clavier (`:focus`)
- [x] Contraste suffisant (vert sur blanc = 4.5:1+)
- [x] Texte descriptif et explicite

---

## Behavior

### Default State
- `position: absolute` en top-left
- `transform: translateY(-150%)` pour cacher hors viewport
- `z-index: var(--layer-important)` pour être au-dessus de tout

### Focus State
- `transform: translateY(0)` pour révéler le lien
- Outline visible pour indiquer le focus
- Transition fluide 200ms

### Click Action
- Navigation vers `#targetId` avec scroll smooth natif du navigateur
- Focus déplacé vers la cible (si `tabindex="-1"`)

---

## Browser Support

✅ Tous navigateurs modernes (Chrome, Firefox, Safari, Edge)  
✅ Lecteurs d'écran (NVDA, JAWS, VoiceOver)  
✅ Navigation clavier native

---

## Notes Techniques

- **Transform**: Utilisé pour l'animation au lieu de `top/left` pour meilleures performances
- **Z-index**: `var(--layer-important)` garantit visibilité au-dessus des overlays/modals
- **Position absolute**: Permet de ne pas affecter le layout du reste de la page
- **White-space nowrap**: Empêche le wrap sur mobile pour garder un bouton compact
- **Focus-visible**: Seul `:focus` car on veut toujours montrer l'outline au clavier

---

## Related Components

- **Header** — Contient généralement le skip link
- **Main Content** — Cible principale du skip link
- **Navigation** — Peut être une cible alternative

---

## Testing

### Manual Testing

1. Ouvrir la page dans un navigateur
2. Appuyer sur `Tab` → Le skip link DOIT apparaître en haut à gauche
3. Appuyer sur `Enter` → La page DOIT scroller vers la cible
4. Vérifier que le focus est bien déplacé vers `#targetId`

### Automated Testing

```javascript
// Playwright example
await page.keyboard.press('Tab');
await expect(page.locator('.ps-skip-link')).toBeVisible();
await page.locator('.ps-skip-link').click();
await expect(page.locator('#main-content')).toBeFocused();
```

---

## Resources

- [WCAG 2.2 - Bypass Blocks (2.4.1)](https://www.w3.org/WAI/WCAG22/Understanding/bypass-blocks.html)
- [WebAIM - Skip Navigation Links](https://webaim.org/techniques/skipnav/)
- [A11y Project - Skip Links](https://www.a11yproject.com/posts/skip-nav-links/)
