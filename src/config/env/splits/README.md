# Config Split entities (versioned)

YAML definitions for Config Split (`config_split.config_split.*`).

| File | Split id | Folder override |
|------|----------|-----------------|
| `config_split.config_split.local.yml` | `local` | `../config/env/local` |
| `config_split.config_split.language_{code}.yml` | `language_{code}` | `../config/env/languages/{code}` |

Imported into active config at greenfield install via
`ps_core.site_language_negotiation_installer` (`SiteLanguageNegotiationInstaller`).

Activation (status) is controlled in `web/sites/default/settings.bootstrap.php`:
- `local` when `APP_ENV=dev`
- `language_{ps_country_code}` for each multisite

`src/config/sync/` remains gitignored (full CMI export); split **entities** live here so
CI and fresh clones do not depend on sync.
