# Design System CHANGELOG

Date: 2025-11-28

## Added
2025-11-28: Added pages `home-page.md`, `property-search.md`, `property-detail.md`, `user-account.md`, `contact.md`, `about.md`, `blog-listing.md`, `blog-article.md`:
  - Home Page: hero + search-form + feature-section + card-grid composition.
  - Property Search: filter-panel + card-grid + map-view + pagination layout.
  - Property Detail: hero + tabs + features table + map + related card-grid.
  - User Account: content + sidebar with main-menu.
  - Contact: contact form + map.
  - About: hero + content block + feature-section.
  - Blog Listing: article-list + pagination (+ optional sidebar).
  - Blog Article: article-layout + related features.
  - Updated COMPONENT_MANIFEST.yml to mark pages completed.
2025-11-28: Added templates `two-column.md`, `content-sidebar.md`, `block.md`, `full-width.md`, `hero-layout.md`, `article-layout.md`:
  - Two-Column: ratios 50-50, 60-40, 40-60; responsive stacking.
  - Content+Sidebar: left/right sidebar positions; responsive.
  - Block: generic section with header/content/actions; themes and width variants.
  - Full-Width: full band with optional contained inner; light/dark.
  - Hero Layout: page skeleton with hero organism and content slot.
  - Article Layout: article header/body/sidebar with meta; responsive grid.
  - Updated COMPONENT_MANIFEST.yml to mark templates completed.
2025-11-28: Enhancements to organisms:
  - Search Form: optional client-side validation (required + numeric checks) with aria-live error messages; small error style hook.
  - Map View: optional clustering toggle when Leaflet.markercluster is available; updates live region accordingly.
2025-11-28: Added organisms `feature-section.md`, `map-view.md`, `calculator.md`, `article-list.md`, `pre-footer.md`:
  - Feature Section: 2/3/4-column highlights with icon/title/text and optional CTA; left/center alignment; light/dark themes.
  - Map View: Leaflet/Google wrapper with markers, optional clustering, info panel live region, focus handling; basic Leaflet example.
  - Calculator: mortgage/payment tool with inputs and live results, annuity formula, compact/panel variants.
  - Article List: list/grid layout of articles with image, excerpt, meta, and pagination slot.
  - Pre-Footer: CTA/links/newsletter columns with 2/3/4 grids and light/dark theme.
  - Updated COMPONENT_MANIFEST.yml to mark organisms completed.
2025-11-28: Added organisms `hero.md`, `search-form.md`, `card-grid.md`, `filter-panel.md`:
  - Hero: media (image/video) + content (eyebrow, title, text, CTAs); media-left/right/center/full; optional overlay; responsive grid.
  - Search Form: primary fields (location/type/price/area) with inline/stacked/compact layouts, accessible labels, responsive grid; action buttons.
  - Card Grid: responsive list of `card` molecules with header controls and pagination slots; compact/comfortable densities.
  - Filter Panel: collapsible sections using disclosure pattern, sidebar/drawer variants, basic JS toggle behavior; actions apply/reset.
  - Updated COMPONENT_MANIFEST.yml to mark organisms completed.
2025-11-28: Added molecules `video.md`, `carousel.md`, `skeleton.md`:
  - Video: unified HTML5/YouTube/Vimeo wrapper, responsive ratios, captions tracks, poster/title/caption, `playsinline` + safe autoplay/muted handling, rounded style.
  - Carousel: images/cards slider with prev/next controls, bullets pagination, keyboard + touch support, aria-live viewport, focused active slide, loop/auto-height variants.
  - Skeleton: loading placeholders for text/avatar/card/table with optional shimmer animation respecting prefers-reduced-motion; exposed CSS variables for shimmer duration/gradient.
  - Updated COMPONENT_MANIFEST.yml to mark molecules completed.
2025-11-28: Created molecules `table.md`, `stepper.md`, `tag-list.md`:
  - Table: sortable columns with aria-sort, responsive variants (scroll/stacked mobile), striped/hover/bordered styles, data-label for mobile cards, minimal JS for client-side sorting, sticky headers.
  - Stepper: multi-step progress indicator for wizards/forms, numbered/icon/minimal variants, horizontal/vertical orientations, complete/current/upcoming/error states, clickable navigation, aria-current="step".
  - Tag-List: badge composition with maxVisible overflow (+X more), removable tags (close button), inline/wrap/scroll layouts, custom events for remove/expand actions.
  - All components reference existing tokens and follow SDC API structure.
  - Updated COMPONENT_MANIFEST.yml to mark completed.

 2025-11-28: Added atoms (icon, link, field, checkbox, radio), molecules (form-field, card), organisms (header, footer), templates (grid-layout). Recorded [QUESTION] on card variants strategy.
 2025-11-28: Added template `templates/page-container.md` with props, Twig, a11y, tokens references. Updated `design/COMPONENT_MANIFEST.yml` to mark `templates.grid-layout` and `templates.page-container` as completed.
2025-11-28: Synced `design/tokens/typography.yml` with `UI/02-text-styles.png`:
  - Added `font.family.secondary` = Open Sans.
  - Added explicit paragraph presets (Open Sans 16 regular; BNPP Sans 16/14 regular/bold; small).
  - Added responsive scales for `h3`, `h4`, `h5`.
  - Updated error text color to `#EB3636`.
  - Simplified css variables prefix to `--ps` and added examples.
2025-11-28: Validated font files in `source/assets/fonts/BNPPSans/` and updated `typography.yml`:
  - Added font weights: Light (300), ExtraBold (800) with file references.
  - Updated `font_loading.preload` paths to match actual structure (`source/assets/fonts/BNPPSans/*.woff2`).
  - Noted Open Sans as web font to be added (Google Fonts or local files).
  - Marked Light/ExtraBold as optional lazy-loaded weights for performance.
    - Added Condensed (Light) variant file reference; optional lazy-loaded.
2025-11-28: Installed Open Sans font files locally:
  - Created `source/assets/fonts/OpenSans/` directory.
  - Downloaded `OpenSans-Regular.woff2` (19K) and `OpenSans-Bold.woff2` (18K) from Google Fonts.
  - Updated `typography.yml` to reference local files with proper paths.
  - Added Open Sans files to `font_loading.preload` list.
2025-11-28: Expanded `design/atoms/icon.md` with comprehensive icon inventory from 13 UI categories:
  - Added categories: blog, metropole, mobile-only, search, tools, tutoffice, group (total: 13 categories).
  - Added state variants: default, disabled, hover, selected.
  - Added color variants: dark-grey, light-grey, green, white (per UI/03-icon-styles.png).
  - Documented 100+ icons across all categories with naming conventions.
  - Updated props schema, Twig template, and BEM modifiers to support states and colors.
2025-11-28: Simplified icon component API by removing `category` prop:
  - Removed `category` from props schema (categories remain in documentation for inventory organization).
  - Updated Twig template to remove category modifier from classes.
  - Removed category BEM modifiers (kept size, state, color, style).
  - Updated examples to reference icons by `name` only (e.g., `arrow-right`, `facebook`, `fav-filled`).
  - Icons are now referenced solely by unique `name`, simplifying component usage.
2025-11-28: Validated button component against UI mockups (UI/07-button-1.png, UI/07-button-2.png):
  - Confirmed alignment: Primary Green "Rechercher", Secondary Green "Découvrir".
  - Updated icon references to use simplified API (removed category prop).
  - Added `--ps` prefix CSS variable examples to design tokens section.
  - Button component fully aligned with color and typography tokens.
2025-11-28: Validated and updated form atoms (link, field, checkbox, radio):
  - Link: added `purple` and `white` color variants; added `disabled` prop and rendering; simplified icon usage; external only when enabled.
  - Field: added states (hover/focus/done) and `iconPosition` left/right with matching Twig; expanded modifiers accordingly.
  - Checkbox/Radio: added CSS Variables docs and verified tokens.
  - Added CSS Variables sections with `--ps` prefix for consistency (border colors, sizes, transitions).
  - Confirmed alignment with UI mockups: UI/08-links.png, UI/09-input-*.png, UI/10-textarea.png, UI/11-checkboxes.png, UI/12-radios.png.
  - All form components reference correct tokens (borders, colors, spacing).
2025-11-28: Created atoms `heading.md`, `text.md`, `label.md`, `image.md`, `skip-link.md`:
  - Added SDC-style APIs, Twig templates, SCSS using existing tokens.
  - A11y: proper semantics for headings/labels, images with `alt`, skip-link focus behavior.
  - Updated `COMPONENT_MANIFEST.yml` to mark these atoms as completed.
2025-11-28: Added molecules `breadcrumb.md`, `dropdown.md`, `alert.md`:
  - Breadcrumb: nav/ol structure with `aria-current`, truncation/compact modifiers.
  - Dropdown: button + listbox markup with native `<select>` fallback; sizes.
  - Alert: semantic variants info/success/warning/error with roles and dismissible option.
  - Updated `COMPONENT_MANIFEST.yml` to mark these molecules as completed.
2025-11-28: Added atom `flag.md` and molecule `language-selector.md`:
  - Flag: ISO code, size, shape, decorative/alt handling; SVG path-based with tokens for sizing.
  - Language Selector: composed with Flag + Dropdown patterns, listbox ARIA, `<select>` fallback.
  - Updated `COMPONENT_MANIFEST.yml` to mark both as completed.
2025-11-28: Added molecules `search-bar.md` and `pagination.md`:
  - Search Bar: role=search, input type=search with icon and submit button; inline/block layouts; sizes.
  - Pagination: nav/ul pattern with aria-current, prev/next rel, compact variant.
  - Updated `COMPONENT_MANIFEST.yml` to mark both as completed.
2025-11-28: Normalized locale codes (Option C):
  - Flag atom now accepts BCP 47 tags (`locale`) and maps to ISO 3166-1 for assets; `code` remains supported and takes precedence.
  - Language Selector passes `locale` to Flag and documents dual code support.
  - Added manifest note under `atoms.flag` describing Option C.
2025-11-28: Added `molecules/menu-item.md` and `organisms/main-menu.md`:
  - Menu Item: link with icon/active/has-children states; caret indicator.
  - Main Menu: recursive items, submenu toggle buttons, responsive collapsible nav.
  - Updated `COMPONENT_MANIFEST.yml` to mark both as completed.
2025-11-28: Added JS behavior docs and data hooks:
  - Dropdown, Language Selector: `data-*` hooks with minimal toggle scripts (aria-expanded/hidden).
  - Main Menu: mobile toggle and submenu disclosure with `data-*` hooks.
2025-11-28: Added molecule `molecules/tooltip.md`:
  - SDC-style API, Twig, SCSS, and minimal JS behavior (`data-tooltip` hooks; `aria-expanded` and positioning guidance).
  - Updated `COMPONENT_MANIFEST.yml` to mark `molecules.tooltip` as completed.
2025-11-28: Added molecule `molecules/accordion.md`:
  - Accessible accordion with `button[aria-expanded]` + `role=region`, `singleOpen` option, BEM structure, tokens, Twig, SCSS, and minimal JS (`data-accordion` hooks).
  - Updated `COMPONENT_MANIFEST.yml` to mark `molecules.accordion` as completed.
2025-11-28: Added molecule `molecules/tabs.md`:
  - Accessible tablist/tab/tabpanel with keyboard navigation, horizontal/vertical orientations, underline/boxed/pill variants, and optional auto/manual activation.
  - Updated `COMPONENT_MANIFEST.yml` to mark `molecules.tabs` as completed.
2025-11-28: Added molecule `molecules/modal.md`:
  - Accessible dialog with `<dialog>` element, focus trap, backdrop dismiss, size variants (small/medium/large/fullscreen), and minimal JS behavior.
  - Updated `COMPONENT_MANIFEST.yml` to mark `molecules.modal` as completed.
2025-11-28: Added molecule `molecules/toast.md`:
  - Temporary notification with role=status/alert, auto-dismiss timer, position variants, semantic colors (info/success/warning/error), and ToastManager JS class for runtime notifications.
  - Updated `COMPONENT_MANIFEST.yml` to mark `molecules.toast` as completed.
2025-11-28: Added atom `atoms/badge.md`:
  - Compact status/date/label/count indicator with semantic variants, size/shape options (rounded/square/pill), icon support, and clickable state.
  - Updated `COMPONENT_MANIFEST.yml` to mark `atoms.badge` as completed.
2025-11-28: Added atoms `toggle.md`, `avatar.md`, `eyebrow.md`, `spinner.md`:
  - Toggle: On/off switch with role=switch, sizes (xs/sm/md/lg), checked/disabled states, optional internal labels.
  - Avatar: User representation with image/initials/icon fallback, shapes (circle/square/rounded), sizes (xs-xl), status badges (online/offline/busy).
  - Eyebrow: Contextual label/kicker with variants (primary/secondary/accent/neutral), decorative line/dot options, uppercase/bold styles.
  - Spinner: Loading indicator with variants (circular/dots/bars), sizes (xs-xl), colors, role=status, and CSS animations.
  - Updated `COMPONENT_MANIFEST.yml` to mark all four atoms as completed.
2025-11-28: Added atoms `divider.md`, `progress-bar.md`:
  - Divider: Horizontal/vertical separator with styles (solid/dashed/dotted), thickness, colors, spacing, optional text/icon center.
  - Progress Bar: Linear/circular progress indicator with role=progressbar, determinate/indeterminate states, striped animation, colors, sizes.
  - Updated `COMPONENT_MANIFEST.yml` to mark both atoms as completed.
## Open Questions
[QUESTION]
  A) Un seul `ps-card` avec `variant` (product/news/publication/solution/study/push)
  B) Des sous-composants: `ps-card-product`, `ps-card-news`, etc.
  C) Un organism `ps-card-grid` gérant normalisation et mapping des metas
  - Cohérence: ✅ Meilleure unification
  - Maintenance: ✅ Moins de duplications
  - Drupal: ✅ Plus simple à exposer via un seul SDC avec `enum` de variants
