---
description: "Use when working on Drupal theme files in ui_suite_bnppre (Twig, SCSS, theme hooks, libraries, icons, or theme build scripts)."
name: "UI Suite BNP PRE Theme Guidelines"
applyTo: "web/themes/custom/ui_suite_bnppre/**"
---

# UI Suite BNP PRE Theme Guidelines

- Treat this path as source-of-truth for project customization: `web/themes/custom/ui_suite_bnppre/`.
- Follow Drupal 11 OOP theme hook patterns in `src/Hook/`; keep business logic out of templates.
- Do not edit generated assets directly (`assets/css/**`, generated component CSS). Update SCSS or source files and rebuild.

## Build Workflow

- Run theme commands from `web/themes/custom/ui_suite_bnppre/`.
- Install dependencies with `npm install`.
- Use `npm run build` for full asset pipeline, or `npm run build:css` for SCSS-only changes.
- For icon updates, run `npm run build:bnppre-icons:check`.
- For theme metadata consistency, run `npm run build:theme-yaml:check`.
- After theme/library/config-impacting changes, clear caches with `vendor/bin/drush cr` from repo root.

## Theme Conventions

- Prefer dependency injection in PHP classes; avoid introducing new direct service-locator usage in business logic.
- Keep library overrides deliberate and documented because this theme overrides multiple core/contrib libraries.
- For icon changes, follow naming and normalization contract before committing SVG updates.

## Reference Docs

- Developer docs index: `web/themes/custom/ui_suite_bnppre/docs/README.md`
- Architecture (regions, dependencies, CSS variants): `web/themes/custom/ui_suite_bnppre/docs/architecture.md`
- Build workflow and SCSS layers: `web/themes/custom/ui_suite_bnppre/docs/development-workflow.md`
- PHP Hook classes and DI patterns: `web/themes/custom/ui_suite_bnppre/docs/php-hooks.md`
- Twig templates inventory: `web/themes/custom/ui_suite_bnppre/docs/templates.md`
- UI Patterns component system: `web/themes/custom/ui_suite_bnppre/docs/components.md`
- Library system and override audit: `web/themes/custom/ui_suite_bnppre/docs/libraries.md`
- BNPPRE icon pack contract: `web/themes/custom/ui_suite_bnppre/docs/icons.md`
- Theme roadmap: `web/themes/custom/ui_suite_bnppre/REFACTO.md`
