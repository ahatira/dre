# Per-country language.negotiation overrides (Config Split `language_{code}`).

Imported at install/post-install via `ps_core.site_language_negotiation_installer`
(`SiteLanguageNegotiationInstaller`) and enabled in
`settings.bootstrap.php` (`config_split.config_split.language_{code}`).

| Site | Default | Prefixes (non-default lang first path segment) |
|------|---------|--------------------------------------------------|
| com  | en      | `/fr/` |
| fr   | fr      | `/en/` (en hidden on front switcher) |
| be   | fr      | `/en/`, `/nl/` |
| es   | es      | `/en/` |
| ie   | en      | — |
| it   | it      | `/en/` |
| lu   | fr      | `/en/` |
| pl   | pl      | `/en/` |
| nl   | nl      | `/en/` |

Luxembourg (`lu`): FR default, EN via `/en/` (same pattern as `fr`).
