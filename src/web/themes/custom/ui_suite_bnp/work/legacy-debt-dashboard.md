# Legacy Styles Debt Dashboard

Date: 2026-04-28 (updated: 2026-04-29)
Owner: Front team
Scope: Theme style sources under work/styles/scss

## Objective

Remove the transitional legacy source area and keep only canonical folders under work/styles/scss.

## Target Date

2026-06-30

## Status Summary

- Legacy SCSS files moved to canonical folders: 21/21 (done)
- Legacy SCSS files remaining: 0
- Runtime library paths migrated: already non-legacy (no change required)
- Build guard `lint:no-legacy` in package.json: in place (build fails if SCSS reappears)
- Legacy source dirs deleted: work/styles/scss/legacy/ (done)
- Legacy compiled artifacts deleted: assets/css/legacy/ (done)
- All debt fully resolved — target date met early.

## Remaining Debt

None. Debt fully closed.

## Risks

- If developers reintroduce SCSS in legacy path, `npm run lint:no-legacy` (called first by `npm run build`) will fail fast with an actionable error message.
- Admin media library screen: not validated on com (module route unavailable at audit time). Validate when enabling media_library on com or on a site where it is active.

## Validation Checklist

- [x] npm run build (lint:no-legacy + css + icons) succeeds
- [x] Admin UI icons page is visually correct
- [ ] Admin media library screen is visually correct (module/route unavailable on com at audit time)
- [x] Admin layout builder screen is visually correct
- [x] No new SCSS committed under work/styles/scss/legacy
- [x] work/styles/scss/legacy/ directory deleted
- [x] assets/css/legacy/ stale artifacts deleted
