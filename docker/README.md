# Docker Setup for Drupal Project

## Stack locale

La stack Docker locale fournit uniquement les services d'execution et d'infrastructure :

1. **nginx**
2. **php 8.3-fpm**
3. **PostgreSQL 17**
4. **Memcache 1.6**
5. **Solr 9**
6. **Mailpit**
7. **Node container** optionnel pour execution npm dans Docker

## Workflow recommande sur Windows

Sur Windows, le workflow valide pour ce projet est **hybride** :

1. **Composer sur l'hote**
2. **npm sur l'hote**
3. **Docker pour les services runtime uniquement**

Cette approche evite les timeouts et les problemes d'extraction observes avec Composer dans un volume monte depuis Windows.

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

## URLs utiles

- Drupal front : <http://localhost:8080>
- Drupal admin : <http://localhost:8080/admin>
- Solr : <http://localhost:8983>
- Mailpit : <http://localhost:8025>

## Emails locaux (Mailpit)

Les webforms (`ps_form`) envoient via **Symfony Mailer** vers Mailpit en local.
La config SMTP est dans `src/web/sites/default/settings.local.php` (host `mailpit`, port `1025`).

Apres une install fraiche :

```bash
docker exec -i ps_php sh -lc 'cd /var/www/html && vendor/bin/drush en -y symfony_mailer mailer_override'
make drush-cr
```

Verifier les emails : <http://localhost:8025>

## Validation rapide

### Anonyme

```bash
curl -I http://localhost:8080/admin
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
