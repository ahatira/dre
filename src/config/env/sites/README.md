# Per-country CMI overrides (Config Split `site_{code}`).

Country matrix: `../../../../scripts/multisite/countries.yml` → `web/sites/countries.yml`.

| Path | Role |
|------|------|
| `../splits/config_split.config_split.site_{code}.yml` | Split entity (generated) |
| `{code}/` | Override YAML for this country |

## Generated split contents

Each `site_{code}` split owns:

**`complete_list`** (from manifest `languages`):

- `language.entity.{langcode}` for each enabled language

**`partial_list`** (shared across countries):

- `language.negotiation`
- `system.site`
- `ps_homepage.homepage`
- `ps_homepage.settings`

Additional per-country YAML (Search API, LB structure, language overrides) will be
filled by `make export-all-configs` (phase 3).

## Bootstrap

- Split activation: `web/sites/default/settings.bootstrap.php`
- Greenfield / shell install: `ps_core.site_config_split_installer`
- Language config overrides from modules: `ps_import_active_language_config_overrides`

Regenerate split entities: `make generate-multisite` (repo root).
