# PS Compare

Property comparison for the Property Search platform (Drupal 11).

## User flows

1. **Search** — add offers via compare buttons on cards.
2. **Comparator panel** — floating widget on search; CTA opens the comparison modal.
3. **Comparison modal / page** — table at `/compare` or modal on search (`/api/compare/modal`).
4. **Share** — “Send my selection” opens a Bootstrap offcanvas with webform `compare_share` (`/api/compare/share-offcanvas`).
5. **Email** — webform submission triggers `CompareEmailSender`.

## Architecture

| Layer | Responsibility |
|-------|----------------|
| `CompareManager` | Cookie (anonymous) + DB (authenticated) list |
| `ComparePageBuilder` | Table sections, columns, toolbar |
| `ComparePanelBuilder` | Search widget + modal shell |
| `CompareWebformHooks` | Webform `compare_share` alter + email on submit |
| `CompareShareOffcanvasController` | Offcanvas fragment (preferred share UI) |

## Configuration

- **Module settings**: `/admin/ps/config/compare`
- **Consultant footer** (phone/hours in share offcanvas): `/admin/ps/config/core` → urgency help block
- **Webform**: `compare_share` (provisioned by `ps_form`)
- **Budget “From” prefix**: `ps_offer.settings` → `budget_from_prefix`

## API routes

| Route | Method | Purpose |
|-------|--------|---------|
| `/api/compare/toggle/{type}/{id}` | POST | Add/remove offer |
| `/api/compare/modal` | GET | Modal table HTML |
| `/api/compare/share-offcanvas` | GET | Share webform offcanvas |
| `/api/compare/share-modal` | GET/POST | **Deprecated** — legacy Form API share |

## Frontend libraries

- `compare-toggle` — toggle buttons, toast, undo
- `compare-panel` — search widget + modal loader
- `compare-page` — full page table, gallery, scroll
- `compare-share-offcanvas` — share offcanvas opener

## Tests

```bash
bash src/web/modules/custom/ps_compare/tests/b2b_compare_full.sh
```

Individual suites: `b2b_compare_search.sh`, `b2b_compare_page.sh`, `b2b_compare_share.sh`, `b2b_compare_undo.sh`.

## Deprecated

- `CompareShareModalController` / `/api/compare/share-modal` — use offcanvas + webform
- `CompareDisplaySettings::emailShare()` — use `shareButton()`
- `js/ps-compare-share.js` — removed; use `ps-compare-share-offcanvas.js`
