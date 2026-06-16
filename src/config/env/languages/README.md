# Per-country language.negotiation overrides (Config Split `language_{code}`).

Imported at install/post-install via `ps_apply_site_language_negotiation`
(`config:import --partial --source=../config/env/languages/{code}`) and enabled in
`settings.bootstrap.php` (`config_split.config_split.language_{code}`).

| Site | Default | Prefixes (non-default lang first path segment) |
|------|---------|--------------------------------------------------|
| com  | en      | `/fr/` |
| fr   | fr      | `/en/` (en hidden on front switcher) |
| be   | fr      | `/en/`, `/nl/` |
| es   | es      | `/en/` |
| ie   | en      | — |
| it   | it      | `/en/` |
| lu   | fr      | `/en/`, `/lb/` |
| pl   | pl      | `/en/` |
| nl   | nl      | `/en/` |

Luxembourg: client alias `lu` → Drupal langcode `lb`.
