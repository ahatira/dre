# Scripts Toolbox

Enterprise script toolbox for PS Project.

## Architecture

- `main.sh`: central CLI entrypoint.
- `_core/`: shared shell modules (logging, errors, docker, drush, db, validation).
- `drupal/`: Drupal workflows (install, deploy, import, config, qa, maintenance).
- `tools/`: developer productivity workflows (quality, lint, tests, debug, profiling).
- `composer/`: composer wrappers (audit, validate, update, autoload, cleanup).
- `generate/`: XML/fixture generation workflows for migration and QA datasets.

Prototype commands:

- `generate-xml`: minimal XML fixtures generator.
- `bnppre-offers`: real BNPPRE XML from public offer pages with built-in business-rule validation.

Generic dataset generation quick usage:

```bash
# Default fixture XML (1 offer)
bash src/scripts/main.sh generate generate-xml

# Generate 10 fixture offers
bash src/scripts/main.sh generate generate-xml tmp/generated-xml --count 10
```

BNPPRE generation quick usage:

```bash
# Default run (3 URLs) + automatic validation
bash src/scripts/main.sh generate bnppre-offers

# VEN live sample profile (7 validated OVBUR pages) + automatic validation
bash src/scripts/main.sh generate bnppre-offers tmp/generated-bnppre-xml/bnppre-ven-batch.xml --ven-sample

# Skip validation explicitly
bash src/scripts/main.sh generate bnppre-offers --no-validate
```

BNPPRE extraction modes:

- Extract all offers from sitemap:

```bash
python3 src/scripts/generate/generate-bnppre-offers.py --mode all --limit 100 --output tmp/generated-bnppre-xml/bnppre-all.xml
```

- Extract all offers for one asset type:

```bash
python3 src/scripts/generate/generate-bnppre-offers.py --mode asset --asset bureau --limit 100 --output tmp/generated-bnppre-xml/bnppre-bureau.xml
```

- Extract with asset + operation filters and capped volume:

```bash
python3 src/scripts/generate/generate-bnppre-offers.py --mode asset-operation --asset bureau --operation VEN --limit 25 --output tmp/generated-bnppre-xml/bnppre-bureau-ven.xml
```

BNPPRE translation workflows:

- Direct translation during generation (external translator command):

```bash
python3 src/scripts/generate/generate-bnppre-offers.py --translate-en direct --translator-command "my_translator_cli" --output tmp/generated-bnppre-xml/bnppre-en-direct.xml
```

- Deferred translation (generate backlog now, inject English later):

```bash
# Step 1: generate FR + backlog template
python3 src/scripts/generate/generate-bnppre-offers.py --translate-en deferred --output tmp/generated-bnppre-xml/bnppre.xml

# Step 2: fill generated *.translations.todo.json, then re-run with --translations-input
python3 src/scripts/generate/generate-bnppre-offers.py --translate-en deferred --translations-input tmp/generated-bnppre-xml/bnppre.translations.done.json --output tmp/generated-bnppre-xml/bnppre-en.xml
```

## Conventions

- Strict mode: `set -Eeuo pipefail`
- Shared bootstrap: `source scripts/_core/_source.sh`
- Uniform logs via `_core/logger.sh`
- Centralized error handling via `_core/errors.sh`
- Docker and Drush access via `_core/docker.sh` and `_core/drush.sh`

## Usage

```bash
# Installation
bash src/scripts/main.sh drupal install

# CRM import
bash src/scripts/main.sh drupal import-crm

# CRM import (high-volume mode: batches + integrated monitoring)
BATCH_SIZE=200 MONITOR_INTERVAL=20 bash src/scripts/main.sh drupal import-crm-bulk

# Solr hard reset + Search API reindex
bash src/scripts/main.sh drupal solr-hard-reset

# Full QA regression
bash src/scripts/main.sh drupal qa

# Composer validation
bash src/scripts/main.sh composer validate

# Cleanup generated files
bash src/scripts/main.sh drupal cleanup
```

## Advanced Diagnostics

Diagnostics can be enabled at runtime for critical workflows:

```bash
# Structured diagnostics context + timed steps
PS_DIAG=1 bash src/scripts/main.sh drupal install

# Full shell trace to file
PS_TRACE=1 PS_TRACE_FILE=/tmp/ps-install-trace.log bash src/scripts/main.sh drupal install --force

# Combined diagnostics and trace for import/deploy
PS_DIAG=1 PS_TRACE=1 bash src/scripts/main.sh drupal import-crm
PS_DIAG=1 PS_TRACE=1 BATCH_SIZE=200 MONITOR_INTERVAL=20 bash src/scripts/main.sh drupal import-crm-bulk
PS_DIAG=1 PS_TRACE=1 bash src/scripts/main.sh drupal deploy
```

Bulk import tuning:
- `BATCH_SIZE` (default `200`): number of `BUSINESS_ID` imported per `ps_offer_from_xml` batch.
- `MONITOR_INTERVAL` (default `20`): seconds between monitor snapshots (CPU/RAM/process) while a migration command runs.
- `XML_SOURCE_PATH` (default `data/xml/bnppre_all_fr.xml`): source XML used for the bulk run.
- `IMPORT_XML_DICTIONARIES` (default `0`): set to `1` to force XML dictionary migrations before batch import.
- `KILL_EXISTING_IMPORTS` (default `0`): set to `1` to terminate existing `migrate:import` processes before bulk run start.
- `SKIP_STATIC_PREIMPORTS` (default `0`): set to `1` to skip groups/definitions/agents/media/divisions phase when resuming an already-populated environment.
- `SKIP_AGENT_IMPORTS` (default `0`): set to `1` to skip avatar+agent pre-imports for search-focused fast runs.
- `SKIP_MEDIA_IMPORTS` (default `0`): set to `1` to skip file+media pre-imports for search/filter validation runs.
- `SKIP_DIVISION_IMPORTS` (default `0`): set to `1` to skip divisions pre-import when not required.
- `MAX_BATCHES` (default `0`): set to a small value (for example `2`) to stop after N offer batches and estimate total duration.

Built-in behavior:
- `timing`: every critical step logs START/END with elapsed seconds.
- `retry`: network-sensitive commands are retried with controlled backoff.
- `trace`: optional xtrace stream to `PS_TRACE_FILE`.

## Docker or Local Execution

Drush execution now supports automatic or manual runtime routing:

```bash
# Auto mode (default): uses Docker only if available and PHP container is running.
PS_EXEC_MODE=auto bash src/scripts/main.sh drupal cache

# Force Docker mode.
PS_EXEC_MODE=docker bash src/scripts/main.sh drupal cache

# Force local mode (runs from src/vendor/bin/drush).
PS_EXEC_MODE=local bash src/scripts/main.sh drupal cache
```

Allowed values:
- `PS_EXEC_MODE=auto` (default)
- `PS_EXEC_MODE=docker`
- `PS_EXEC_MODE=local`

## Compatibility

Legacy scripts under `src/script/drupal` remain available as wrappers.
Use `src/scripts/*` as the canonical location for all new automations.
