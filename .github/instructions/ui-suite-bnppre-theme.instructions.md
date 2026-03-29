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

- Build strategy: `web/themes/custom/ui_suite_bnppre/docs/build-assets-strategy.md`
- Icons contract: `web/themes/custom/ui_suite_bnppre/docs/bnppre-icons-contract.md`
- Libraries overrides quick reference: `web/themes/custom/ui_suite_bnppre/docs/libraries-override-quick-reference.md`
- Libraries overrides audit: `web/themes/custom/ui_suite_bnppre/docs/audit-libraries-override.md`
- CSS variants governance: `web/themes/custom/ui_suite_bnppre/docs/css-variants-governance.md`
- Theme roadmap: `web/themes/custom/ui_suite_bnppre/REFACTO.md`
