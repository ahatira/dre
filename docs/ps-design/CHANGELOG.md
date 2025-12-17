# Changelog - PS Theme

All notable changes to component implementations will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

---

## [2025-01-17] - Architecture: HTML List Components

### Added
- **List (Elements)**: New atom for basic HTML lists (ul/ol)
  - **Variants**: `bulleted` (disc markers), `numbered` (decimal), `unstyled` (no markers)
  - **Nested support**: Automatic style cascade (disc → circle → square for bullets, decimal → alpha → roman for numbers)
  - **Marker customization**: CSS variable `--ps-list-marker-color` (default: `--primary`)
  - **Stories**: 6 stories (Default, Numbered, Unstyled, Nested, PropertyFeatures, StepsProcess)

- **Definition List (Components)**: New molecule for key-value pairs (dl/dt/dd)
  - **Variants**: `default` (stacked), `inline` (horizontal term/definition), `grid` (2-column responsive @640px)
  - **Icon support**: Optional icon per term via `data-icon` attribute
  - **Styling**: Term bold gray-900, definition gray-700, gap between items via `--size-4`
  - **Stories**: 6 stories (Default, Inline, Grid, WithoutIcons, BuildingAmenities, PropertyFinancials)

### Changed
- **Documentation**:
  - `elements.md`: 23/23 → 24/24 (100%) - Added List atom with full description
  - `components.md`: 25/29 → 26/29 (90%) - Added Definition List, noted List Item confusion for future clarification

### Technical Details
- Build impact: CSS 492.56 kB → 496.23 kB (+3.67 kB)
- Conformity: 100% (tokens, nesting, BEM, Autodocs)
- Auto-imported via glob patterns in `styles.css`
- Commit: `8e1af02`

### Rationale
- **Critical architecture gap**: Design system lacked basic HTML list components (ul/ol/dl)
- **Semantic HTML**: Provides foundation for content lists before implementing complex list-based components
- **Definition List vs "Specs List"**: Replaces poorly-named "Specs List" concept with semantic HTML `<dl>` structure
- **Composition ready**: List Item (Result List Item?) can now properly compose List atom when clarified

---

## [2025-01-15] - Refactor: Tag Atom + Tag List Molecule

### Changed
- **Tag List (Components)**: Complete architectural refactor to respect Atomic Design principles
  - Reduced CSS from 253 lines to 24 lines (layout only: flex, wrap, gap)
  - Now composes Tag atoms via `{% include '@elements/tag/tag.twig' %}`
  - No longer defines tag structure directly

### Added
- **Tag (Elements)**: New autonomous atom for interactive chips/badges
  - **Variants**: `filled` (solid background) and `outline` (border only) per maquette
  - **Colors**: 8 semantic options (neutral, primary, secondary, success, danger, warning, info, gold)
  - **Sizes**: sm, md (default), lg
  - **States**: `selected` (aria-pressed), `removable` (with close icon)
  - **Icon positioning**: `iconStart` (search inputs) or end (filters)
  - **Rendering**: Can be `<button>` (default) or `<a>` when `url` param provided
  - **Pills design**: Uses `--radius-full` for rounded corners
  - **Stories**: 8 stories covering all variants (Default, StyleVariants, ColorsFilled, ColorsOutline, Sizes, RemovableVariants, SelectedState, AsLink)

### Technical Details
- Build impact: CSS 495.65 kB → 496.36 kB (+0.71 kB)
- Conformity: 100% (3-layer CSS, BEM nesting, all tokens, Autodocs)
- Commit: `77eb7a4`

### Rationale
- **Maquette analysis**: Design showed two distinct tag styles (filled/outline) with pills and icon positioning
- **Atomic Design violation**: Previous implementation had tag structure defined in molecule instead of separate atom
- **Reusability**: Tag atom can now be used standalone across project (search inputs, filters, categories) while Tag List only handles collection layout

---

## [2025-01-15] - Enhancement: Table Color Variants

### Changed
- **Table (Collections)**: Enhanced color variants for better visual contrast
  - Headers now use full semantic colors (`--primary`, `--secondary`, etc.) instead of subtle variants
  - Header text changed to white for proper contrast
  - Striped rows continue using subtle variants (`--primary-subtle`, etc.)

### Technical Details
- Build impact: CSS 493.81 kB → 495.65 kB (+1.84 kB)
- 10 color variants: neutral, primary, secondary, success, danger, warning, info, gold, light, dark
- Commit: `38dd5b7`

---

## Component Inventory

**Current Progress**: 6/87 components (7%)

### Completed (6)
1. **Button** (Elements) - 38 stories, all states, semantic colors
2. **Avatar** (Elements) - 8 stories, size variants, image/initials/icon
3. **Badge** (Elements) - 9 stories, semantic colors, pill variant
4. **Divider** (Elements) - 6 stories, orientation variants
5. **Tag** (Elements) - 8 stories, filled/outline, 8 colors, 3 sizes ✨ NEW
6. **Tag List** (Components) - 6 stories, composition pattern ✨ REFACTORED

### In Progress (0)
- None

### Planned Next (3)
1. **Icon** (Elements) - Standard icon system documentation
2. **Link** (Elements) - Text links with states
3. **Heading** (Elements) - Typography hierarchy (h1-h6)

---

**Maintainers**: Design System Team  
**Last Updated**: 2025-01-15
