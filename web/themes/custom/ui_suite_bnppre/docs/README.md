# ui_suite_bnppre — Developer Documentation

> **Drupal 11** · **Bootstrap 5.3.3** · **UI Suite** (Patterns + Styles + Icons) · **Starterkit-enabled**

Custom standalone theme for BNP Paribas Real Estate. No base theme — all Bootstrap integration is self-contained.

---

## Quick Start

```bash
# 1. From project root — install PHP dependencies
composer install

# 2. From theme directory — install Node dependencies + build all assets
cd web/themes/custom/ui_suite_bnppre
npm install
npm run build

# 3. From project root — enable theme and clear caches
vendor/bin/drush theme:enable ui_suite_bnppre
vendor/bin/drush config:set system.theme default ui_suite_bnppre
vendor/bin/drush cr
```

---

## Tech Stack at a Glance

| Layer | Technology | Version |
|-------|-----------|---------|
| CMS | Drupal | ^11.3 |
| CSS framework | Bootstrap | 5.3.3 |
| Component system | UI Suite (Patterns + Styles) | Latest |
| Icon system | UI Icons + custom BNPPRE pack | 132 icons |
| CSS pre-processor | Sass (Dart Sass) | 1.x |
| Build orchestrator | Gulp | 4.x |
| PHP hooks | Drupal 11 OOP `#[Hook]` | 26 classes |
| Template engine | Twig | 3.x |

---

## Documentation Index

| Document | What it covers |
|----------|---------------|
| [architecture.md](architecture.md) | Theme design, regions, dependencies, CSS variants, starterkit |
| [development-workflow.md](development-workflow.md) | Build chain, SCSS layers, commands, watch mode, troubleshooting |
| [php-hooks.md](php-hooks.md) | OOP hook classes inventory, DI patterns, extension guide |
| [templates.md](templates.md) | All 84 templates, suggestion hierarchy, override patterns |
| [components.md](components.md) | UI Patterns component system, slots/props, component list, how to create |
| [offer-components-variants.md](offer-components-variants.md) | Offer-detail SDC contract: variants, strict props, optional slots, integration notes |
| [libraries.md](libraries.md) | Library system, dynamic loading, full override audit + risk table |
| [icons.md](icons.md) | BNPPRE icon pack: categories, naming contract, SVG rules, build pipeline |

---

## Repository Structure

```
ui_suite_bnppre/
├── assets/
│   ├── css/          ← GENERATED — do not edit
│   ├── fonts/        ← BNPPSans, OpenSans (woff2)
│   ├── icons/        ← Custom BNPPRE SVG pack (132 icons, 12 categories)
│   ├── images/       ← Static images
│   ├── js/           ← Custom JavaScript (source, not compiled)
│   └── scss/         ← SCSS source — edit here
├── components/       ← UI Patterns components (Twig + YAML + stories)
├── docs/             ← Developer documentation (this folder)
├── scripts/          ← Node.js build scripts
├── src/
│   └── Hook/         ← PHP OOP hook classes (26 files)
├── starterkits/      ← Starterkit for generating child themes
├── templates/        ← Drupal Twig templates (84 files)
├── gulpfile.js       ← Build pipeline configuration
├── package.json      ← Node dependencies + build scripts
├── ui_suite_bnppre.info.yml       ← Theme definition + regions + library overrides
├── ui_suite_bnppre.libraries.yml  ← Library definitions
├── ui_suite_bnppre.icons.yml      ← UI Icons pack definition
└── ui_suite_bnppre.ui_styles.yml  ← UI Styles definitions
```

---

## Key Development Rules

1. **Never edit generated CSS** in `assets/css/` or `components/**/styles/*.css`. Edit `.scss` sources and rebuild.
2. **Use dependency injection** in Hook classes — no `\Drupal::service()` in business logic.
3. **Library overrides** must be deliberate and documented in [libraries.md](libraries.md).
4. **Check after core updates** — two JS files shadow core security patches (see [libraries.md § High-Risk](libraries.md#high-risk-overrides)).
5. **Clear Drupal cache** after any `.info.yml`, `.libraries.yml`, or PHP hook change: `vendor/bin/drush cr`.
