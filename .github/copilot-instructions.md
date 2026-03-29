# Project Guidelines

## Architecture
- This is a Drupal 11 recommended-project layout with docroot in `web/`.
- Most project-specific implementation lives in `web/themes/custom/ui_suite_bnppre/`.
- `web/modules/custom/` is currently empty; prioritize theme-level changes unless a module is explicitly needed.
- Theme PHP follows Drupal 11 OOP hook patterns in `web/themes/custom/ui_suite_bnppre/src/Hook/`.
- Treat `web/core/` as managed code. If core behavior must be patched (for example Windows icon-path handling in `IconFinder.php`), keep the change minimal and document why.

## Build And Test
- Install PHP dependencies from repo root: `composer install`.
- Theme asset workflow (run in `web/themes/custom/ui_suite_bnppre/`):
  - `npm install`
  - `npm run build` for full asset build
  - `npm run build:css` for SCSS-only updates
  - `npm run build:bnppre-icons:check` to validate icon conventions
  - `npm run build:theme-yaml:check` to validate theme metadata sync
- After theme, library, or config-affecting changes, clear Drupal caches: `vendor/bin/drush cr`.
- No repository-wide automated test command is defined at root; run targeted checks relevant to changed files.

## Conventions
- Do not edit generated assets directly (`assets/css/**`, generated component CSS). Edit SCSS or source files, then rebuild.
- Use dependency injection patterns in PHP classes; avoid adding new direct service-locator usage in business logic.
- Keep Bootstrap and library overrides deliberate and documented, because this theme overrides multiple core/contrib libraries.
- For icon work, follow the documented normalization and naming contract before committing SVG updates.

## Reference Docs
- Build and generated-file rules: `web/themes/custom/ui_suite_bnppre/docs/build-assets-strategy.md`
- Icon contract and validation rules: `web/themes/custom/ui_suite_bnppre/docs/bnppre-icons-contract.md`
- Libraries override quick reference: `web/themes/custom/ui_suite_bnppre/docs/libraries-override-quick-reference.md`
- Full override audit: `web/themes/custom/ui_suite_bnppre/docs/audit-libraries-override.md`
- CSS variants governance: `web/themes/custom/ui_suite_bnppre/docs/css-variants-governance.md`
- Theme refactor status and roadmap: `web/themes/custom/ui_suite_bnppre/REFACTO.md`
- Bootstrap icons copy workflow in this project: `web/libraries/README.md`

## Environment Notes
- Local Windows/WAMP setup may use relaxed SSL verification in `web/sites/default/settings.local.php`.
- Keep environment-specific behavior local-only and out of production configuration.
