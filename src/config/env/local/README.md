# Config Split « local » — dev-only overrides.

Enabled when `APP_ENV=dev` (`settings.bootstrap.php`).

| Config | Purpose |
|--------|---------|
| `antibot.settings` | Hub recette — no Antibot on webform submission forms (`complete_list`) |
| `webform.settings`, `webform.webform.*` | Hub recette — Antibot / Honeypot off (`partial_list`) |

**Première utilisation / après modification des YAML ici :**

```bash
cd src
vendor/bin/drush @ps.com config-split:import local -y
vendor/bin/drush @ps.com cr
```

Pour réactiver les protections en local : retirer les entrées du split ou `config-split:deactivate local`, puis `make drush fr cex -y` depuis la baseline si besoin.

Mail, Solr, Memcache : `docs/MULTISITE_OPS.md` § Infrastructure.
