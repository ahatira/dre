# Docker Setup for Drupal Project

## Stack locale

La stack Docker locale fournit uniquement les services d'execution et d'infrastructure :

1. **nginx**
2. **php 8.3-fpm**
3. **PostgreSQL 17**
4. **Memcache 1.6**
5. **Solr 9**
6. **Mailpit**

Composer, npm et Drush s'exécutent sur l'hôte WSL — pas dans Docker.

## Commandes principales

### Demarrer les services

```bash
docker compose -f docker/docker-compose.yml up -d
```

### Arreter les services

```bash
docker compose -f docker/docker-compose.yml down
```

### Installer les dependances PHP sur l'hote

```bash
cd src
composer install
```

### Installer Drush sur l'hote

```bash
cd src
composer require --dev drush/drush:^13
```

### Installer les dependances frontend sur l'hote

```bash
cd src
npm install
```

## Installation Drupal minimale

Une fois Composer termine sur l'hote :

```bash
docker compose -f docker/docker-compose.yml up -d
docker compose -f docker/docker-compose.yml exec -T postgres sh -lc 'psql -U drupal -d postgres -c "DROP DATABASE IF EXISTS drupal;" && psql -U drupal -d postgres -c "CREATE DATABASE drupal;"'
docker compose -f docker/docker-compose.yml exec -T php sh -lc 'cp /var/www/html/web/sites/default/default.settings.php /var/www/html/web/sites/default/settings.php && mkdir -p /var/www/html/web/sites/default/files && chmod 664 /var/www/html/web/sites/default/settings.php && chmod 775 /var/www/html/web/sites/default/files'
docker compose -f docker/docker-compose.yml exec -T php sh -lc 'vendor/bin/drush site:install minimal --db-url=pgsql://drupal:drupal@postgres:5432/drupal --account-name=admin --account-pass=admin --account-mail=admin@example.com --site-name="PS Project" -y'
```

## URLs utiles (multisite dev)

- International : <http://com.localhost:8080>
- France : <http://fr.localhost:8083>
- Admin FR : <http://admin.fr.localhost:8083>
- Solr : <http://localhost:8983>
- Mailpit : <http://localhost:8025>
- Multisite debug : <http://fr.localhost:8083/multisite-debug.php> (tout vhost local)

## Emails locaux (Mailpit)

Les webforms (`ps_form`) envoient via **Symfony Mailer** vers Mailpit en local.

### `settings.local.php` (dev)

Fichier **gitignoré**, conservé entre les `make reinstall`. Template versionné :

```bash
cp src/web/sites/default/settings.local.example.php src/web/sites/default/settings.local.php
```

Contenu typique (voir le template) :

- **Mailpit** — `MAILPIT_HOST` / `MAILPIT_PORT` depuis `src/.env`

Le connecteur **Solr** (`SOLR_*`, `SOLR_CORE_{CODE}`) est appliqué dans `settings.bootstrap.php` pour tous les environnements — pas besoin de `settings.local.php` pour Solr. En prod/staging, `backend_config` est exclu du CMI via `config_ignore`.

Après copie ou modification :

```bash
make drush-cr
# ou: cd src && vendor/bin/drush @ps.com en -y symfony_mailer mailer_override
```

Vérifier les emails : <http://localhost:8025>

## Validation rapide

### Anonyme

```bash
curl -I -H "Host: fr.localhost" http://127.0.0.1:8083/admin
```

Un **403 Forbidden** sur `/admin` en anonyme est normal.

### Authentifie

Se connecter avec le compte cree pendant l'installation, puis verifier l'acces a `/admin`.

## Optimisations Windows

Le projet applique des optimisations Docker/PHP pour limiter la latence I/O sous Windows:

- Montage bind en mode `delegated` pour `php` et `nginx`
- `tmpfs` sur `/tmp` dans les conteneurs web
- Tuning PHP local via `docker/php/zz-performance.ini` (OPcache + realpath cache)

Appliquer/reappliquer ces optimisations:

```bash
docker compose -f docker/docker-compose.yml up -d --build php nginx
```

### Limite connue

Ces optimisations ameliorent partiellement les requetes web, mais les commandes CLI Drupal dans le conteneur (Drush/PHPUnit) restent souvent lentes sur bind mount Windows.

## Permissions WSL (EACCES sur config/sync)

L'image PHP aligne `www-data` sur uid/gid **1000** (utilisateur WSL). Si Drush est lance **sans** `-u www-data`, les fichiers CMI exportes (`drush cex`) sont crees en `root:root` et VS Code ne peut plus les editer (`EACCES`).

**Prevention** — toujours utiliser Drush sur l'hôte :

```bash
make drush-cex          # ou make drush-cr, make drush-uli
```

**Reparation** apres un export root :

```bash
make fix-permissions
```

Ne pas appliquer sur `web/sites/default/files` (runtime Drupal) sauf problemes upload/cache.
