# Instructions Accessibilité

## Standard : WCAG 2.2 AA minimum

Tous les composants DOIVENT respecter WCAG 2.2 niveau AA.

## 1. Contraste des couleurs

### Texte
- **AA** : 4.5:1 (texte normal), 3:1 (texte large ≥18px)
- **AAA** : 7:1 (texte normal), 4.5:1 (texte large)

```css
/* ✅ CORRECT - Contraste suffisant */
.ps-button--primary {
  background: var(--primary); /* #00915A */
  color: white; /* Contraste 4.8:1 */
}

/* ❌ INTERDIT - Contraste insuffisant */
.ps-badge--light {
  background: var(--light); /* Gray 100 */
  color: var(--text-secondary); /* Gray 400 - Contraste 2.3:1 */
}
```

### Composants interactifs
- **Minimum 3:1** pour bordures, focus indicators, états

## 2. Focus visible OBLIGATOIRE

```css
/* ✅ REQUIS pour tous les interactifs */
.ps-button,
.ps-link,
.ps-input,
[role="button"],
[tabindex="0"] {
  &:focus-visible {
    outline: 2px solid var(--border-focus);
    outline-offset: 2px;
  }
}

/* ❌ INTERDIT */
.ps-component {
  &:focus {
    outline: none; /* Jamais sans alternative ! */
  }
}
```

## 3. Navigation clavier

### Interactifs natifs
- Utiliser `<button>`, `<a>`, `<input>` quand possible
- Éviter `<div onclick>` ou `<span onclick>`

### Custom controls
```html
<!-- ✅ CORRECT -->
<div 
  role="button" 
  tabindex="0"
  @keydown.enter="onClick"
  @keydown.space.prevent="onClick"
>
  Action
</div>

<!-- ❌ INTERDIT -->
<div onclick="action()">Action</div>
```

### Ordre de tabulation
- Logique : gauche → droite, haut → bas
- Éviter `tabindex > 0` (casse l'ordre naturel)
- Utiliser `tabindex="-1"` pour éléments non-tabbables mais focusables (modals, alerts)

## 4. ARIA

### Rôles
```html
<!-- Navigation -->
<nav role="navigation" aria-label="Main navigation">

<!-- Boutons custom -->
<div role="button" aria-pressed="false">Toggle</div>

<!-- Alertes -->
<div role="alert" aria-live="assertive">Error message</div>

<!-- Modals -->
<div role="dialog" aria-modal="true" aria-labelledby="modal-title">
```

### Labels
```html
<!-- Inputs -->
<label for="email">Email</label>
<input id="email" type="email" aria-required="true">

<!-- Boutons icon-only -->
<button aria-label="Close modal">
  <span data-icon="close" aria-hidden="true"></span>
</button>

<!-- Liens ambigus -->
<a href="/property/123" aria-label="View property details for 28010 Madrid">
  View property
</a>
```

### États
- `aria-expanded` : accordions, dropdowns
- `aria-selected` : tabs, options
- `aria-disabled` : désactivé mais visible
- `aria-hidden="true"` : icons décoratifs
- `aria-live` : contenus dynamiques

## 5. Structure sémantique

### Landmarks
```html
<header role="banner">
<nav role="navigation">
<main role="main">
<aside role="complementary">
<footer role="contentinfo">
```

### Headings
- **H1** : unique par page
- **H2-H6** : hiérarchie logique (pas de saut de niveau)

```html
<!-- ✅ CORRECT -->
<h1>Property Listing</h1>
<h2>Filters</h2>
<h3>Location</h3>
<h2>Results</h2>
<h3>28010 Madrid Office</h3>

<!-- ❌ INTERDIT -->
<h1>Title</h1>
<h3>Subtitle</h3> <!-- Saute H2 -->
```

## 6. Images et médias

### Alt text
```html
<!-- Informative -->
<img src="office.jpg" alt="Modern office space with glass walls">

<!-- Décorative -->
<img src="pattern.svg" alt="" role="presentation">

<!-- Complex (charts, maps) -->
<img src="chart.png" alt="Sales chart" aria-describedby="chart-desc">
<div id="chart-desc" class="sr-only">
  Sales increased from 100k to 150k between Jan and June.
</div>
```

### Vidéos
- Sous-titres pour contenu audio
- Transcription disponible
- Contrôles accessibles clavier

## 7. Formulaires

### Labels visibles
```html
<!-- ✅ CORRECT -->
<label for="email">Email address</label>
<input id="email" type="email">

<!-- ❌ INTERDIT - Placeholder seul -->
<input type="email" placeholder="Email">
```

### Erreurs
```html
<label for="email">Email</label>
<input 
  id="email" 
  type="email" 
  aria-invalid="true"
  aria-describedby="email-error"
>
<span id="email-error" role="alert">
  Please enter a valid email address
</span>
```

### Required
```html
<label for="name">
  Name <span aria-hidden="true">*</span>
</label>
<input id="name" type="text" required aria-required="true">
```

## 8. Composants complexes

### Modals
```html
<div 
  role="dialog" 
  aria-modal="true"
  aria-labelledby="modal-title"
  aria-describedby="modal-desc"
>
  <h2 id="modal-title">Contact consultant</h2>
  <p id="modal-desc">Fill out the form to schedule a visit.</p>
  <button aria-label="Close modal">×</button>
</div>
```

- Trap focus (tab cycle dans modal)
- Escape pour fermer
- Restaurer focus après fermeture

### Tabs
```html
<div role="tablist" aria-label="Property details">
  <button role="tab" aria-selected="true" aria-controls="panel-1">
    Description
  </button>
  <button role="tab" aria-selected="false" aria-controls="panel-2">
    Equipment
  </button>
</div>
<div id="panel-1" role="tabpanel" tabindex="0">
  Content 1
</div>
```

### Accordions
```html
<button 
  aria-expanded="false"
  aria-controls="accordion-content"
>
  Section title
</button>
<div id="accordion-content" hidden>
  Content
</div>
```

## 9. Lightbox / Carousels

### Lightbox
```html
<div role="dialog" aria-label="Image gallery" aria-modal="true">
  <button aria-label="Previous image">←</button>
  <img src="photo.jpg" alt="Office interior">
  <button aria-label="Next image">→</button>
  <button aria-label="Close gallery">×</button>
  <div aria-live="polite">Image 1 of 15</div>
</div>
```

### Carousel
```html
<div role="region" aria-label="Featured properties" aria-roledescription="carousel">
  <button aria-label="Previous property">←</button>
  <div role="group" aria-roledescription="slide" aria-label="1 of 8">
    <img src="property.jpg" alt="Modern office in Madrid">
  </div>
  <button aria-label="Next property">→</button>
  <button aria-label="Pause autoplay">⏸</button>
</div>
```

## 10. Screen reader only

```css
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}
```

```html
<button>
  <span data-icon="trash" aria-hidden="true"></span>
  <span class="sr-only">Delete property</span>
</button>
```

## 11. Map accessibility

```html
<div role="application" aria-label="Interactive property map">
  <div id="map" aria-describedby="map-instructions"></div>
  <div id="map-instructions" class="sr-only">
    Use arrow keys to pan, + and - to zoom. 
    Tab through markers to view property details.
  </div>
  
  <!-- Alternative list view -->
  <button aria-label="Switch to list view">
    Show list
  </button>
</div>
```

## 12. Animations et mouvement

### Respect prefers-reduced-motion
```css
.ps-component {
  transition: transform var(--transition-base);
  
  @media (prefers-reduced-motion: reduce) {
    transition: none;
  }
}
```

### Autoplay
- Vidéos : pause par défaut ou contrôle visible
- Carousels : pause button obligatoire

## 13. Tests A11y

### Outils
- **axe DevTools** (Chrome/Firefox extension)
- **WAVE** (Web Accessibility Evaluation Tool)
- **Lighthouse** (Chrome DevTools)
- **Screen readers** : NVDA (Windows), VoiceOver (Mac/iOS), JAWS

### Checklist validation
- [ ] Contraste 4.5:1 minimum (texte), 3:1 (UI components)
- [ ] Focus visible sur tous les interactifs
- [ ] Navigation clavier complète (Tab, Enter, Space, Escape, Arrows)
- [ ] ARIA roles, states, properties corrects
- [ ] Alt text pour images informatives, alt="" pour décoratives
- [ ] Labels pour inputs (visible + programmatique)
- [ ] Headings hiérarchie logique
- [ ] Landmarks sémantiques
- [ ] Erreurs formulaires annoncées
- [ ] Modals trap focus + Escape close
- [ ] Animations respectent prefers-reduced-motion

## Ressources

- [WCAG 2.2](https://www.w3.org/WAI/WCAG22/quickref/)
- [MDN Accessibility](https://developer.mozilla.org/en-US/docs/Web/Accessibility)
- [A11y Project](https://www.a11yproject.com/)
- [ARIA Authoring Practices](https://www.w3.org/WAI/ARIA/apg/)
