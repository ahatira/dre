# Skip Link

**Version**: 1.0.1  
**Status**: ✅ Stable  
**Type**: Atom / Element  
**Category**: Accessibility

WCAG skip link allowing keyboard users to bypass repeated navigation and jump directly to the main content. Hidden off-screen until it receives keyboard focus.
## Props

| Prop | Type | Default | Required | Description |
|------|------|---------|----------|-------------|
| `targetId` | `string` | `'main-content'` | ✅ | ID of the target element (e.g. `main-content`, `navigation`, `search`). Must exist in page. |
| `label` | `string` | `'Skip to main content'` | ✅ | Link text announced and displayed on focus. |
| `attributes` | `Attribute` | — | ❌ | Additional HTML attributes (Drupal Attribute object). |
## BEM Structure

```
.ps-skip-link   ← Anchor link (single block, no elements, no modifiers)
```

Single block component (no elements / variants).
## Component Variables (Layer 2)

Defined in `skip-link.css`; override any of these in context or via modifiers (none for now):

| Variable | Default (references) | Purpose |
|----------|----------------------|---------|
| `--ps-skip-link-top` | `var(--size-4)` | Top offset |
| `--ps-skip-link-left` | `var(--size-4)` | Left offset |
| `--ps-skip-link-z-index` | `var(--layer-important)` | Ensure visibility above overlays |
| `--ps-skip-link-padding-y` | `var(--size-3)` | Vertical padding |
| `--ps-skip-link-padding-x` | `var(--size-4)` | Horizontal padding |
| `--ps-skip-link-bg` | `var(--primary)` | Background color (brand primary) |
| `--ps-skip-link-hover-bg` | `var(--primary-hover)` | Hover background |
| `--ps-skip-link-color` | `var(--white)` | Text color |
| `--ps-skip-link-border-radius` | `var(--radius-2)` | Corner radius |
| `--ps-skip-link-shadow` | `var(--shadow-3)` | Elevation shadow |
| `--ps-skip-link-font-family` | `var(--font-sans)` | Typeface |
| `--ps-skip-link-font-size` | `var(--font-size-1)` | Font size |
| `--ps-skip-link-font-weight` | `var(--font-weight-500)` | Font weight |
| `--ps-skip-link-line-height` | `var(--leading-normal)` | Line height |
| `--ps-skip-link-transition-duration` | `var(--duration-fast)` | Transition duration |
| `--ps-skip-link-transition-timing` | `var(--ease-3)` | Timing function |
| `--ps-skip-link-focus-outline-width` | `var(--border-size-2)` | Outline width |
| `--ps-skip-link-focus-outline-color` | `var(--border-focus)` | Focus outline color |
| `--ps-skip-link-focus-outline-offset` | `var(--border-size-2)` | Outline offset |
| `--ps-skip-link-hidden-offset-y` | `-150%` | Off-screen translateY value |
## Usage Examples

### Basic (Default)

```twig
{% include '@elements/skip-link/skip-link.twig' with {
  targetId: 'main-content',
  label: 'Skip to main content',
} %}
```

### Skip to Navigation

```twig
{% include '@elements/skip-link/skip-link.twig' with {
  targetId: 'navigation',
  label: 'Skip to navigation',
} %}
```

### Skip to Search

```twig
{% include '@elements/skip-link/skip-link.twig' with {
  targetId: 'search',
  label: 'Skip to search',
} %}
```

### Page Header Integration

```twig
{# Place as FIRST focusable element inside <body> #}
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
## Real-World Use Cases

1. **Main content** — Bypass header/nav and land on primary content.
2. **Navigation** — Jump directly to the main menu container.
3. **Search** — Provide fast access to the search region.
4. **Footer** — (Optional) Jump to site footer / contact info.
## Accessibility

### ✅ WCAG 2.2 Compliance

- **2.4.1 Bypass Blocks (Level A)**: Provides a mechanism to skip repeated navigation.
- **2.4.4 Link Purpose (Level A)**: Link text clearly communicates destination.

### Best Practices

1. Must be the first focusable element in `<body>`.
2. Hidden off-screen until `:focus-visible` (keyboard access only).
3. Target element MUST exist and ideally have `tabindex="-1"` to receive programmatic focus.
4. Typically one skip link (to main), multiple allowed where justified.
5. Maintain sufficient contrast (≥ 4.5:1).

### Implementation Checklist

- [x] First focusable in document flow
- [x] `href` references valid in-page `id`
- [x] Target can receive focus (`tabindex="-1"` recommended)
- [x] Uses `:focus-visible` for reveal
- [x] Contrast passes WCAG AA
- [x] Descriptive, action-oriented label
## Behavior

### Default State

- Positioned absolutely (`top/left` via component variables)
- Translated out of view (`translateY(-150%)`)
- High stacking (`z-index: var(--layer-important)`) ensuring visibility over overlays

### Focus-Visible State

- Returns to natural position (`translateY(0)`)
- Draws outline using focus outline component variables
- Hover may occur simultaneously; background switches to `--primary-hover`

### Click Action

- Browser scrolls to target anchor
- Optionally set focus to target with script for improved screen reader context
## Browser Support

✅ Modern browsers (Chrome, Firefox, Safari, Edge)  
✅ Screen readers (NVDA, JAWS, VoiceOver)  
✅ Native keyboard navigation
## Technical Notes

- Uses transform for performant off-screen positioning.
- `--layer-important` ensures visibility above modals/overlays.
- Component-scoped variables allow runtime overrides (e.g. change offset or colors per layout).
- `white-space: nowrap` prevents unintended wrapping.
- Uses `:focus-visible` instead of `:focus` to avoid showing on mouse click.
## Related Components

- **Header** — Usually hosts first focusable element placement.
- **Main Content** — Primary target region.
- **Navigation** — Alternative skip target.
## Testing

### Manual
1. Load page; press `Tab` → skip link appears.
2. Press `Enter` → viewport scrolls to target.
3. (Optional) Apply script to focus target after navigation.
4. Verify contrast and outline visibility.

### Automated (Playwright)
```javascript
await page.keyboard.press('Tab');
await expect(page.locator('.ps-skip-link')).toBeVisible();
await page.locator('.ps-skip-link').click();
await expect(page.locator('#main-content')).toBeFocused();
```
## Resources

- [WCAG 2.2 - Bypass Blocks (2.4.1)](https://www.w3.org/WAI/WCAG22/Understanding/bypass-blocks.html)
- [WebAIM - Skip Navigation Links](https://webaim.org/techniques/skipnav/)
- [A11y Project - Skip Links](https://www.a11yproject.com/posts/skip-nav-links/)
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
