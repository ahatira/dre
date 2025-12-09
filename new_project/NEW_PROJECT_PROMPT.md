# Prompt de relance ultra-concis (48h)

Objectif: Générer automatiquement un nouveau workspace de thème Drupal 11 (SDC natif) + Storybook + Vite, prêt à coder 70 composants, en 48h chrono. Reprends le branding PS Theme: couleurs sémantiques, fonts BNP Paribas Sans/Open Sans, tokens de taille/espacement, focus visible. **Architecture obligatoire: Atomic Design (elements → components/molecules → collections/organisms → layouts/templates → pages).**

## Instructions à exécuter
1) Créer un repo vierge `ps-theme-v2` (Node 18+, npm) avec arbo minimale:
```
ps-theme-v2/
  components/
    atoms/                # Atoms SDC (button, badge, input, icon, avatar...)
    molecules/            # Molecules SDC (form-field, card, dropdown, tabs...)
    organisms/            # Organisms SDC (header, footer, listing...)
    templates/            # Templates SDC (one-column, two-column, hero...)
    pages/                # Pages SDC (home, listing, detail...)
  source/
    tokens/               # Design tokens CSS (colors, spacing, typography, shadows, media)
    icons/                # SVG source files
    fonts/                # BNP Paribas Sans, Open Sans
  .storybook/             # Storybook HTML + Vite
  .github/
    instructions/         # Instructions files (CSS, components, accessibility, workflows)
  dist/                   # build output
  package.json
  vite.config.js
  tsconfig.json
  biome.json
  ps.info.yml
  ps.component.yml
  ps.libraries.yml
```

2) Stack & outils
- Drupal 11 + SDC (Single Directory Components)
- Twig (HTML edition) + Storybook 9 (tags: ['autodocs'] obligatoires)
- Vite + PostCSS (nesting) + TypeScript (strict, noEmit for checks)
- Biome pour lint/format, Vitest pour tests unitaires JS
- @faker-js/faker pour fixtures stories
- Atomic Design respecté: nomenclature `atoms/`, `molecules/`, `organisms/`, `templates/`, `pages/` + titres Storybook `Atoms/Button`, `Molecules/Form Field`, `Organisms/Header`, `Templates/Two Column`, `Pages/Home`.
- Naming conventions: kebab-case pour fichiers/dossiers (`form-field/`, `two-column.twig`), PascalCase pour composants Storybook, BEM pour classes CSS (`ps-form-field__label`, `ps-button--primary`).

3) Tokens & branding (CSS custom properties)
- Couleurs sémantiques: `--primary` (#00915A), `--secondary` (#A12B66), `--success` (#198754), `--danger` (#EB3636), `--warning` (#FBBF24), `--info` (#2563EB), `--gold` (#D1AE6E), `--light`, `--dark`. Inclure variantes `-hover`, `-active`, `-text`, `-border`, `-subtle`, `-bg-subtle`, `-border-subtle`, `-text-emphasis`.
- Typo: BNP Paribas Sans (fallback system-ui), Open Sans; échelles `--font-size-xs` à `--font-size-6xl`, poids 300-800.
- Espacements: `--size-1` (4px) à `--size-32` (128px), base 8px.
- Radius: `--radius-none` à `--radius-full`.
- Ombres: 6 niveaux + focus.
- Animations: durées 75–1000ms, easing standard.
4) SDC: exemple `components/atoms/button/`
```
button.component.yml   # props schema (text req, type=primary|secondary|danger|success, size=sm|md|lg, disabled, icon, href, attributes)
button.twig            # markup Twig sans arrow/functions
button.css             # nesting 3 niveaux max, tokens only, focus-visible obligatoire
button.js              # Drupal behaviors + once()
button.stories.jsx     # tags:['autodocs'], argTypes courts, title: 'Atoms/Button'
```

**Nesting CSS 3 niveaux max**: `.ps-button { .ps-button__icon { svg { } } }` = OK. Plus profond = interdit. Exceptions: pseudo-classes (`:hover`, `:focus-visible`), pseudo-éléments (`::before`), états (`&.is-active`), modifiers (`&--primary`).ton.stories.jsx     # tags:['autodocs'], argTypes courts
5) Configs clés
- `ps.component.yml`: registre SDC (button, badge, card, modal, form-field, tabs, table, pagination, dropdown, search-bar, toast, tooltip, skeleton...).
- `ps.libraries.yml`: global CSS `dist/css/styles.css`, vendors JS `dist/js/vendors.js`, un entry par behavior.
- `ps.info.yml`: base theme false, librairie `ps/global`, namespaces: `atoms`, `molecules`, `organisms`, `templates`, `pages` -> `components/*/`.
- `vite.config.js`: namespaces Twig (atoms, molecules, organisms, templates, pages, tokens), build entries auto (styles.css + tous JS composants), output dist/css|js, plugin yaml + svg-sprite.
- `tsconfig.json`: strict, moduleResolution bundler, paths `@atoms/*`, `@molecules/*`, `@organisms/*`, `@tokens/*`, include components/**, source/**.
- `biome.json`: linting rules (no-console warnings, consistent naming), formatting (indent 2, quotes single, trailing commas).

6) Assets sources (fichiers à fournir)

**Structure `source/assets/`** :
```
source/assets/
  icons-source/        # SVG sources (avant build sprite)
    generic/           # Icons génériques (check, close, arrow, etc.)
    social-media/      # Facebook, Twitter, LinkedIn, etc.
    tools/             # Search, filter, share, favorite, etc.
  fonts/               # Fichiers @font-face
    BNPPSans/
      BNPPSans-Regular.woff2
      BNPPSans-Bold.woff2
    OpenSans/
      OpenSans-Regular.woff2
      OpenSans-Bold.woff2
  images/              # Images projet (logos, backgrounds, placeholders)
    logo-bnpp.svg
    placeholder.jpg
    hero-bg-1.jpg
  flags/               # Drapeaux pays (pour sélecteur langue)
    fr.svg
    en.svg
    es.svg
```

**Build process icons** :
- Script `scripts/build-icons.mjs` compile `source/assets/icons-source/*.svg`
- Génère `source/assets/icons/icons-sprite.svg` (sprite avec #icon-{name})
- Génère `source/props/icons-generated.css` (mappings data-icon)
- Commande : `npm run build:icons`

**Utilisation icons** :
```twig
{# ✅ CORRECT - Pas de préfixe icon- #}
<span data-icon="check"></span>
{% include '@atoms/icon/icon.twig' with { icon: 'search', size: 'md' } only %}
```

**Fonts loading** (`source/props/font-face.css`) :
```css
@font-face {
  font-family: 'BNP Paribas Sans';
  src: url('/assets/fonts/BNPPSans/BNPPSans-Regular.woff2') format('woff2');
  font-weight: 400;
  font-display: swap;
}
```

7) Scripts npm (package.json)
- `dev`: concurrently vite --watch + storybook dev
- `build`: lint:check + type-check + icons:build + vite build + storybook build
- `lint:check|fix`: biome check/format source components
- `type-check`: tsc --noEmit
- `icons:build|watch`: script svg -> sprite/webfont
- `components:generate`: scaffolder SDC
- `test`: vitest
7) Instructions files à créer (`.github/instructions/`)
- `css.instructions.md`: nesting 3 niveaux max, tokens only, BEM naming (`.ps-block__element--modifier`), pseudo-classes/elements autorisées, cascade order (base → elements → modifiers → states → responsive).
- `components.instructions.md`: structure 5 fichiers (.component.yml, .twig, .css, .js, .stories.jsx), props schema mandatory, kebab-case naming, Twig sans arrow/map/filter, data-icon sans préfixe.
- `accessibility.instructions.md`: focus-visible obligatoire, aria-* attributes, roles sémantiques, contrast ratios WCAG AA, keyboard navigation, disabled states, skip links.
- `atomic-design.instructions.md`: 5 niveaux stricts (atoms → molecules → organisms → templates → pages), pas de saut de niveau, composition rules, Storybook titles mapping.
- `workflows.instructions.md`: component generation steps, git commit format, code review checklist, build validation.

8) Règles qualité incontournables
- Pas de valeurs hardcodées (toujours tokens CSS `var(--token-name)`)
- Pas d'arrow functions ni `.map/.filter/.includes()` dans Twig (incompatible Drupal)
- Focus-visible sur tous les interactifs (outline + box-shadow)

9) Instructions détaillées (fichiers `.github/instructions/`)

**CRITIQUE** : Consulter ces fichiers AVANT toute création de composant.

**5 fichiers obligatoires** :
1. **`css.instructions.md`** : Tokens obligatoires, nesting max 3 niveaux, couleurs sémantiques, focus-visible, BEM strict
2. **`components.instructions.md`** : Structure 5 fichiers, Twig sans arrow functions, YAML schema SDC, Storybook tags: ['autodocs'], README complet
3. **`accessibility.instructions.md`** : WCAG 2.2 AA minimum, contraste, ARIA, keyboard navigation, screen readers
4. **`atomic-design.instructions.md`** : Hiérarchie 5 niveaux (atoms → molecules → organisms → templates → pages), règles composition, validation dépendances
5. **`workflows.instructions.md`** : Workflow création composant (étapes 1-11), audit conformité 100%, git commit format, génération en masse

**Utilisation** :
```bash
# AVANT de créer un composant, lire :
cat .github/instructions/components.instructions.md
cat .github/instructions/atomic-design.instructions.md
# Pour CSS : cat .github/instructions/css.instructions.md
# Pour A11y : cat .github/instructions/accessibility.instructions.md
# Pour workflow : cat .github/instructions/workflows.instructions.md
```

**Règle d'or** : Instructions = SINGLE SOURCE OF TRUTH.

10) Composants génériques prioritaires (à générer AVANT le design spécifique)

**ATOMS (16 composants de base)** :
- `button` (primary, secondary, danger, success, sizes, icons, disabled)
- `badge` (couleurs sémantiques, pill variant)
- `input` (text, email, password, number, tel, disabled, error)
- `select` (options, multiple, disabled, error)
- `textarea` (rows, disabled, error)
- `checkbox` (checked, disabled, indeterminate)
- `radio` (checked, disabled, grouping)
- `toggle` (switch on/off, disabled, sizes)
- `icon` (140+ icons, sizes, colors)
- `avatar` (sizes, initials fallback, image)
- `link` (internal, external, disabled)
- `label` (required indicator, for attribute)
- `heading` (h1-h6, styles)
- `text` (paragraph, sizes, weights)
- `divider` (horizontal, vertical, with text)
- `spinner` (loading, sizes, colors)

**MOLECULES (15 composants composés)** :
- `form-field` (label + input/select/textarea + hint + error)
- `card` (header, body, footer, image, actions)
- `dropdown` (trigger + menu, positions, nested)
- `tabs` (horizontal/vertical, active state, disabled)
- `accordion` (expand/collapse, multiple, icons)
- `breadcrumb` (home, separators, current page)
- `pagination` (pages, prev/next, ellipsis, sizes)
- `table` (sortable, striped, hover, responsive)
- `modal` (overlay, close, sizes, scrollable)
- `toast` (success/error/warning/info, auto-dismiss, positions)
- `tooltip` (top/bottom/left/right, arrow)
- `alert` (dismissible, icons, couleurs sémantiques)
- `progress-bar` (percentage, striped, animated)
- `search-bar` (input + button, suggestions)
- `tag-list` (removable, colors, max-display)

**ORGANISMS (8 composants complexes)** :
- `header` (logo, nav, search, user menu, mobile toggle)
- `footer` (columns, links, social, copyright)
- `navigation` (menu multi-niveau, dropdowns, active states)
- `listing` (cards grid, filters, sort, pagination)
- `hero` (image, title, CTA, overlay)
- `article-preview` (image, title, excerpt, meta, CTA)
- `sidebar` (filters, categories, tags)
- `contact-form` (fields, validation, submit)
- **`property-card`** (image carousel, badges, price, location, surface, CTA, share, favorite)
- **`consultant-card`** (avatar, name, phone, contact CTA, schedule visit CTA)
- **`search-hero`** (hero background + centered search form overlay)
- **`filter-bar`** (multiple dropdowns, location autocomplete, surface range, price range, "More filters" button)
- **`map-view`** (interactive map, markers, clusters, price tags, toggle list/map)

**TEMPLATES (4 layouts)** :
- `one-column` (header, main, footer)
- `two-column` (sidebar left/right, main)
- `three-column` (sidebars both sides)
- `hero-layout` (hero section + content)

**PAGES (4 exemples)** :
- `home` (hero + listings + CTA)
- `listing` (filters + cards grid + pagination)
- `detail` (article content + sidebar)
- `contact` (form + info)

**Total: 47 composants génériques** (couvre 90% des besoins UI standards)

**Spécificités Real Estate à prévoir** :
- Lightbox fullscreen avec thumbnails strip
- Map integration (Leaflet/Google Maps) avec markers personnalisés, clusters, price bubbles
- Property filters avancés (location autocomplete, range sliders surface/price)
- Carousel photos multi-images avec navigation
- Consultant contact forms (modal + validation inline)
- Favorites system (heart icon + state)
- Share buttons (social + copy link)
- Tags/badges contextuels ("Exclusivity", "Already viewed", etc.)
- Price formatting avec unités (€ HT/HC/m²/an)
- Surface formatting (m², floor notation)
- Location display (address + city + postal code)
- Responsive tables (equipments, services, surfaces)

10) Plan 48h itératif (génération composant par composant)
- **H0–H4**: bootstrap repo + configs + tokens + fonts + icons pipeline + **créer les 5 instructions files** (`.github/instructions/*.md`)
- **H4–H8**: Atoms 1-8 (Button → Toggle) – itératif avec validation à chaque composant
- **H8–H12**: Atoms 9-16 (Icon → Spinner) – validation continue
- **H12–H18**: Molecules 1-8 (Form Field → Table) – test composition atoms
- **H18–H24**: Molecules 9-15 (Modal → Tag List) – validation UI/UX
- **H24–H30**: Organisms 1-4 (Header → Listing) – composition molecules/atoms
- **H30–H36**: Organisms 5-8 (Hero → Contact Form) + Templates 1-4
- **H36–H42**: Pages 1-4 (Home → Contact) + Storybook passes
- **H42–H46**: QA a11y + lint + type-check + build validation
- **H46–H48**: Buffer risques + packaging livrable + README final

**Approche itérative** : Générer 1 composant → valider (build + stories + a11y) → commit → suivant. Pas de batch, contrôle qualité continu.

11) Livrable attendu
- Repo prêt à builder: `npm run build` OK, `npm run dev` OK
- **47 composants SDC génériques** livrés (16 atoms + 15 molecules + 8 organisms + 4 templates + 4 pages) avec stories complètes
- Tokens conformes au branding PS Theme
- **5 instructions files** dans `.github/instructions/` (CSS, components, accessibility, atomic-design, workflows)
- **Assets sources** préparés dans `source/assets/` (icons-source, fonts, images, flags)
- Documentation complète: README avec commandes + structure + naming conventions + règles qualité + liste composants
- Storybook déployable avec tous les composants génériques documentés

12) Commande finale pour lancer
```
npm install && npm run build && npm run dev
```

## Méthodologie de génération

**IMPORTANT**: Générer les composants **un par un, de manière itérative**, dans l'ordre atoms → molecules → organisms → templates → pages. À chaque composant :
1. **Consulter instructions** : `.github/instructions/components.instructions.md` + `atomic-design.instructions.md`
2. Générer les 5 fichiers (.component.yml, .twig, .css, .js, .stories.jsx)
3. Valider build (`npm run build`)
4. Vérifier Storybook (stories s'affichent, autodocs OK)
5. Tester a11y basique (focus-visible, ARIA, keyboard)
6. Commit git avec message structuré (`feat(atoms): add button component`)
7. Passer au suivant

**Objectif final**: 47 composants génériques prêts à l'emploi, permettant de se concentrer uniquement sur les composants spécifiques au projet (immobilier BNP Paribas, voir `REAL_ESTATE_COMPONENTS_PROMPT.md`) sans refaire les bases.

Fin du prompt. Génère le workspace selon ces instructions, composant par composant, avec validation continue.
