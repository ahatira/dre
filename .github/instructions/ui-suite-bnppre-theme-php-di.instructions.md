---
description: "Use when editing PHP in ui_suite_bnppre Hook and Utility classes to enforce dependency injection and avoid service-locator usage."
name: "UI Suite BNP PRE PHP DI Guidelines"
applyTo:
  - "web/themes/custom/ui_suite_bnppre/src/Hook/**/*.php"
  - "web/themes/custom/ui_suite_bnppre/src/Utility/**/*.php"
---

# UI Suite BNP PRE PHP DI Guidelines

- Prefer constructor dependency injection for services used by class business logic.
- Keep direct `\Drupal::service()` and other `\Drupal::*` static calls out of business logic.
- If a static call is unavoidable, isolate it behind a dedicated protected helper method so usage remains centralized and reviewable.
- In hook classes, keep methods focused on orchestration and delegate reusable logic to utilities/services.
- When adding a dependency, keep naming explicit and align with existing class property conventions.
- Before adding new PHP logic, verify whether existing configuration can satisfy the requirement; prefer configuration to custom code whenever possible.
- If custom code is required, keep logic minimal and remove obsolete branches/options as part of the same change.

## Preferred Pattern

- Inject services through constructors and use typed properties.
- Keep helper methods small and single-purpose.
- Add or update imports instead of using fully-qualified names inline repeatedly.

## Validation Checklist

- No new direct service-locator calls in method bodies handling business decisions.
- New dependencies are injected and testable.
- Existing centralized helper methods are reused instead of duplicating access patterns.
- Any DI trade-off is documented briefly in code comments when applicable.

## References

- Theme roadmap and architecture notes: `web/themes/custom/ui_suite_bnppre/REFACTO.md`
- Theme-level conventions: `.github/instructions/ui-suite-bnppre-theme.instructions.md`
