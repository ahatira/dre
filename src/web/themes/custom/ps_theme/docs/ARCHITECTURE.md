# Architecture front Property Search

> Décision validée : dual-theme `ui_suite_bnp` (générique) + `ps_theme` (projet).

## Principe de séparation

**Règle :** si un composant peut servir à d'autres sites BNPPRE → `ui_suite_bnp`.  
S'il encode le métier immobilier PS (offre, recherche, agent, favoris) → `ps_theme`.

```
ui_suite_bnp (base theme)
├── Bootstrap 5 + UI Patterns + UI Styles
├── framework_css_re / framework_css_bnpp
├── SDC génériques : card, navbar, breadcrumb, modal…
├── ui_suite_bnp_companion (Layout Builder, Section Library)
└── starterkits (monolithique + split)

ps_theme (sub-theme, starterkit split)
├── Hérite librairies + SDC parent
├── Overrides Sass par composant Bootstrap (split)
├── SDC métier : offer-card, search-hero, transaction-toggle…
├── Templates Drupal : node--offer--*, views-view--ps-search-offers
└── Tokens Stellar PS (surcouche _custom-variables.scss)
```

## Stack technique

| Couche | Technologie |
|---|---|
| CMS | Drupal 11.3+, Layout Builder |
| Composants | Single Directory Components (SDC) + UI Patterns |
| Styles | Sass split (starterkit), tokens `--re-*` du parent |
| JS | Drupal behaviors (minimal, filtres/carte à venir) |
| Build | Gulp local (`npm run gulp-prod`) |

## Phases d'intégration maquette

| Phase | Objectif | Statut |
|---|---|---|
| **A** | **Shell global** — header, footer, blocs de base, logo, assets Figma | 🟡 en cours |
| 0 | Matrice Figma → composants (`COMPONENT_MATRIX.md`) | ✅ |
| 1 | Squelette `ps_theme` (starterkit split) | ✅ |
| 2 | Tokens Stellar (overrides sur `framework_css_re`) | ✅ |
| 3 | SDC génériques manquants dans le parent (minimal) | ⬜ |
| 4 | SDC métier dans `ps_theme` | ✅ offer-card, search-hero, transaction-toggle |
| 5 | Pages métier une par une (recherche, fiche, homepage) | ⬜ après Phase A |
| 6 | Layout Builder homepage + Section Library | ⬜ |

### Phase A — Shell global (Config-First)

1. Block layout en config (`ps_theme/config/install/`)
2. Contenu + copy démo : `ps_demo/export/content/` + `ps_demo.homepage` config
3. Structure démo (mega-menu, multilingue) : `src/config/demo/` (partial CMI)
4. `page.html.twig` / `html.html.twig` stables — pas de markup page métier
5. Assets Figma dans `assets/images/` — voir [`docs/ASSETS.md`](ASSETS.md)
6. Header / footer ISO maquette PS + responsive
7. Validation visuelle avant toute page métier

### Règle Config-First

- **Config** : blocs, menus, view modes, displays, LB sections
- **Code** : SDC (briques), SCSS (ISO), utilitaires PHP fins
- **Interdit** : composition de page en preprocess/Twig override

### Règle d'or — minimal custom

Avant d'écrire du code custom (bloc PHP, service, hook), **utiliser ce qui existe** :
blocs core (`system_branding_block`, `system_menu_block`, `search_form_block`, `language_block`),
menus configurables, Layout Builder, templates Twig/SDC.

Le custom ne couvre que le gap UX que le core ne peut pas exprimer seul (ex. panneau recherche
header, styles menu actions).

### Régions du shell PS (`ps_theme`)

**Couche 1 — Header PS** (SDC `site-header`, assemblé via `Page::prepareSiteHeaderSlots()`)

| Région | Blocs attendus |
|---|---|
| `branding` | Site branding (core) |
| `shortcut` | Language switcher |
| `navigation` | Main navigation (mega-menu) |
| `actions` | Search, CTA, account, favorites |

**Couche 2 — Cadre de page** (variable selon le type de page)

| Région | Blocs attendus | Visibilité |
|---|---|---|
| `breadcrumb` | Breadcrumbs (core) | Public — sauf `<front>`, `/user/*` |
| `highlighted` | Status messages (core) | Tous |
| `sidebar_first` | Menu `ps_account` (Mon compte, Favoris) | `/user/*` connecté |
| `content` | Contenu principal (LB, nodes) | Tous |
| `sidebar_second` | *(réservé — modules métier ultérieurs)* | Selon besoin page |
| `footer_top` | Pre-footer SEO (menu) | Quasi toutes pages |
| `footer` / `footer_bottom` | Footer PS (contact, legal…) | Toutes pages |

**Couche 3 — Chrome éditeur** (connecté, `access toolbar`)

| Région | Blocs attendus |
|---|---|
| `editor_tools` | Page title (hors homepage), Tabs, Primary admin actions |
| `help` | Help (core) |

Rendu conditionnel dans `page.html.twig` : `editor_tools` et `help` visibles uniquement quand
`ps_show_editor_tools` est vrai (permission `access toolbar`). `editor_tools` est aussi masqué sur
la page d'accueil (`is_front`), même pour les éditeurs connectés. Classes HTML :
`ps-visitor-view` (visiteur) / `ps-editor-preview` (éditeur).

## Conventions

### SDC métier (`ps_theme/components/`)

```
offer-card/
├── offer-card.component.yml
├── offer-card.twig
└── styles/
    └── offer-card.scss   → compilé en offer-card.css par Gulp
```

- Props typées (title, url, image, badges, kpis).
- Pas de logique métier PHP dans le thème : données injectées via preprocess ou Layout Builder.
- Réutiliser les SDC parent (`card`, `badge`, `button`) en composition Twig quand possible.

### Templates Drupal

| Template | Usage |
|---|---|
| `node--offer--teaser.html.twig` | Liste recherche, carrousels |
| `node--offer--full.html.twig` | Fiche détail offre PS |
| `views-view--ps-search-offers.html.twig` | Page recherche (`/find-property`) |
| `block--ps-favorite*.html.twig` | Compteur favoris header |

### Layout Builder

Homepage PS = sections LB configurables :

1. **Hero recherche** — bloc custom ou SDC `search-hero` + formulaire (module filtres à venir)
2. **Univers métier** — tuiles asset type (bureaux, logistique…)
3. **Offres à la une** — View `ps_search_offers` (display teaser)
4. **Contenu éditorial** — blocs WYSIWYG standard

Sections réutilisables via **Section Library** (`ui_suite_bnp_companion`).

### CSS

- Tokens globaux : hérités de `ui_suite_bnp` (`--re-color-*`, `--re-font-*`).
- Overrides PS : `assets/scss/_custom-variables.scss`, `_stellar-tokens.scss`.
- Styles composant : isolés dans `components/*/styles/` (starterkit split).

## Dépendances modules PS

| Module | Rôle front |
|---|---|
| `ps_offer` | Node offer, view modes, champs affichés |
| `ps_search` | Vue Solr `/find-property`, SEO URLs |
| `ps_favorite` | Favoris header + card_favorite |
| `ps_feature` | Badges caractéristiques sur cards |
| `ps_agent` | Bloc contact agent fiche |
| `ps_media` | Formatters galerie / documents |

Modules front **absents** (bloquants parité maquette) : `ps_search_filters`, `ps_location_autocomplete`.

## Références

- Maquette : [BNP PRE Stellar (Figma)](https://www.figma.com/proto/rrA1dlYnJMzcXlwOZ5iuuw/BNP-PRE-Stellar---Livrable-client?node-id=48-7862)
- Parent : `ui_suite_bnp/README.md`
- Modules : `src/web/modules/custom/ps_*/README.md`
