# SCSS Component Migration Strategy

## Current state (ui_suite_bnppre)

- Theme SCSS is currently centralized in `assets/scss/components/*.scss`.
- This is the dominant, stable pattern in the repository.
- Build entrypoints are `assets/scss/styles.scss` and `assets/scss/styles-ps.scss`.

## Recommendation

Short answer: keep the current global pattern as the default for now.

Reasons:

- It matches the existing architecture and team habits.
- It avoids introducing two competing styling models at once.
- It keeps bundle ordering predictable across both CSS variants.

## Target convention (progressive)

Use a hybrid model during transition:

- Existing components: keep styles in `assets/scss/components/*.scss`.
- New or heavily refactored SDC components: allow colocated source in `components/<component>/styles/<component>.scss` when rules are strictly component-scoped.
- Shared foundations (tokens, typography, generic helpers): keep in global `assets/scss/components/*.scss`.

## Migration rules

- Scope first: colocate only component-specific selectors (`.ps-card-agent*`, etc.).
- No generated edits: never edit generated CSS files.
- One source of truth per selector: no duplicated rules between global and local SCSS.
- Keep Bootstrap-first discipline: prefer Bootstrap utilities/structure before custom rules.
- Validate each move with:
  - `npm run build:css`
  - `vendor/bin/drush cr`

## Suggested rollout

- Pilot 1 or 2 components with clear boundaries.
- Review readability, maintainability, and output CSS regressions.
- Adopt rule for new components only when pilot is validated.
- Migrate existing components progressively when touched by feature/fix work.

## Decision for card_agent

- Keep `card_agent` in global SCSS for now (`assets/scss/components/_card-agent.scss`) to stay aligned with repository baseline.
- Revisit colocated SCSS after pilot approval.
