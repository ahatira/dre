# Config Split — local dev only

Per-country configuration now lives in **`config/sites/{code}/`** (full CMI per site).

This directory keeps only the **`local`** split for development overrides (`config/env/local/`).

Legacy **`site_{code}`** split entities are deprecated. Regenerate scaffolding with `make generate-multisite` (no longer creates site splits).

See `config/sites/README.md`.
