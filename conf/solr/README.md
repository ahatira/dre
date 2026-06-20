# Solr configuration (Property Search)

One **complete Solr core directory** per country site, named after `SOLR_CORE_*` in `src/.env`.

## Layout

```
conf/solr/
├── cores.yml                              # Country → core name manifest
├── local_ps_core_com_db/
│   ├── core.properties
│   └── conf/                              # schema.xml, solrconfig.xml, …
├── local_ps_core_be_db/
│   └── …
└── …
```

Each `{core_name}/` folder is ready to deploy as a standalone Solr core instance in production.

## Export (from Drupal)

Requires an installed site and Search API Solr:

```bash
make export-solr              # finalize + export (default country: com)
make export-solr fr           # export using FR site bootstrap
make export-solr --finalize-all
```

Direct CLI:

```bash
bash src/scripts/main.sh drupal export-solr
bash src/scripts/main.sh drupal export-solr --skip-finalize
```

Uses `drush search-api-solr:get-server-config ps_solr`, then copies the config into every core directory listed in `cores.yml`.

## Local Docker

`docker-compose.yml` mounts `conf/solr/` at `/opt/solr/conf-export`.

Create cores after stack is up:

```bash
bash docker/solr/init-cores.sh
# or: make index-solr com  (also runs core init)
```

Each core is created with: `solr create -c {core_name} -d /opt/solr/conf-export/{core_name}`.

## Production

1. Run `make export-solr` and commit `conf/solr/{core_name}/` for each site.
2. Copy each `{core_name}/` directory to the Solr server (or mount via your orchestrator).
3. Create or reload the core using that instance directory.
4. Configure the Drupal connector: `drush @ps.{code} config:set search_api.server.ps_solr backend_config.connector_config.core {core_name} -y` (see `docs/MULTISITE_OPS.md` § Infrastructure).

Reload Solr after config updates when cores already exist.
