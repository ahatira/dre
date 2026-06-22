#!/bin/bash
# guide-installation.sh - Complete installation guide for PS Project

clear

cat << 'EOF'
================================================================================
                    PS PROJECT - GUIDE D'INSTALLATION
================================================================================

PRÉREQUIS
---------
✓ Docker Engine 24.x+
✓ Docker Compose v2
✓ GNU Make
✓ Node.js 20.x+ (pour les builds frontend)
✓ PHP 8.3+ CLI (pour Drush sur l'hôte)

--------------------------------------------------------------------------------

ÉTAPE 1 : Configuration de l'environnement
------------------------------------------
1. Cloner le repository
2. Configurer les variables d'environnement :
   $ make env

3. Configurer le système d'exploitation :
   $ make os-setup

   → Détecte automatiquement Linux/Windows
   → Applique les fichiers Docker optimisés

--------------------------------------------------------------------------------

ÉTAPE 2 : Démarrage des services Docker
---------------------------------------
$ make up                    # Démarrer les conteneurs
$ make ps                    # Vérifier l'état
$ make logs                  # Voir les logs nginx

--------------------------------------------------------------------------------

ÉTAPE 3 : Installation Drupal
-----------------------------
$ cd src
$ composer install           # Dépendances PHP
$ npm install                # Dépendances frontend

--------------------------------------------------------------------------------

ÉTAPE 4 : Configuration multisite
---------------------------------
$ make generate-multisite    # Génère les configs pays
$ make verify-multisite      # Vérifie la configuration

--------------------------------------------------------------------------------

ÉTAPE 5 : Build frontend
------------------------
$ cd src
$ npm run gulp-prod          # Build CSS/JS

--------------------------------------------------------------------------------

ÉTAPE 6 : Test et validation
-----------------------------
URLs de test :
  • International : http://com.localhost:8080
  • France        : http://fr.localhost:8083
  • Admin FR      : http://admin.fr.localhost:8083
  • Solr          : http://localhost:8983
  • Mailpit       : http://localhost:8025

Se connecter en admin :
  $ make drush-uli           # Login admin (com)
  $ make drush fr uli        # Login admin (France)

--------------------------------------------------------------------------------

COMMANDES MAKE DISPONIBLES
--------------------------
Docker & Services :
  make up                    Démarrer les conteneurs
  make down                Arrêter les conteneurs
  make restart             Redémarrer
  make ps                  Statut des conteneurs
  make logs                Logs nginx
  make rebuild             Rebuild image PHP
  make env                 Setup environnement
  make os-setup            Configurer fichiers OS
  make fix-permissions     Corriger permissions
  make init-solr-cores     Initialiser cores Solr

Multisite :
  make generate-multisite    Générer configs pays
  make verify-multisite      Vérifier la configuration
  make seed-site-configs     Seed configs depuis countries.yml

Drush (sur l'hôte) :
  make drush-cr              Rebuild cache
  make drush-uli             Login admin
  make drush fr uli          Login admin France
  make drush-cex             Export config
  make drush-status          Statut site

Frontend :
  make composer-install      Installer dépendances PHP
  make npm-install           Installer dépendances JS
  make build                 Build frontend

--------------------------------------------------------------------------------

FICHIERS DE CONFIGURATION PAR OS
--------------------------------
Linux (Ubuntu/Debian) :
  • docker/docker-compose.linux.yml → docker/docker-compose.yml
  • docker/php/zz-performance.linux.ini → docker/php/zz-performance.ini

  Optimisations :
    - consistency: cached (I/O optimisé Linux)
    - opcache.revalidate_freq=0 (hot-reload rapide)
    - opcache.validate_timestamps=1 (dev)

Windows :
  • docker/docker-compose.windows.yml → docker/docker-compose.yml
  • docker/php/zz-performance.windows.ini → docker/php/zz-performance.ini

  Optimisations :
    - consistency: delegated (I/O optimisé Windows)
    - opcache.revalidate_freq=2
    - opcache.validate_timestamps=1 (dev)

--------------------------------------------------------------------------------

DÉPANNAGE
---------
Problème : Permission EACCES sur config/sync
Solution : $ make fix-permissions

Problème : Drush ne voit pas la base de données
Solution : Vérifier src/.env et redémarrer Docker

Problème : Solr cores manquants
Solution : $ make index-solr

Problème : Hot-reload PHP lent
Solution : Vérifier opcache.revalidate_freq=0 dans ini

Problème : Site ne répond pas
Solution : $ make restart && make drush-cr

--------------------------------------------------------------------------------

RÉSUMÉ RAPIDE
-------------
$ make env
$ make os-setup
$ make up
$ cd src && composer install && npm install
$ make generate-multisite
$ cd src && npm run gulp-prod
$ make drush-uli

================================================================================
EOF
