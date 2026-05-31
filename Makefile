SHELL := /usr/bin/env bash
.DEFAULT_GOAL := help

PROJECT_ROOT := $(CURDIR)
COMPOSE_FILE := $(PROJECT_ROOT)/docker/docker-compose.yml
COMPOSE_WSL_FILE := $(PROJECT_ROOT)/docker/docker-compose.wsl.yml
SRC_DIR := $(PROJECT_ROOT)/src
PHP_CONTAINER := ps_php
PG_CONTAINER := ps_postgres

DRUSH := docker exec -i $(PHP_CONTAINER) sh -lc 'cd /var/www/html && vendor/bin/drush'

.PHONY: help up down restart ps logs install bootstrap reinstall composer-install composer-update npm-install drush-status drush-cr drush-uli modules-list theme-admin db-reset up-wsl down-wsl ps-wsl verify-wsl geocoder-set-key cleanup rebuild import-crm import-status import-reset import-rollback

help:
	@echo "Cibles disponibles:"
	@echo "  make up              - Demarrer services Docker"
	@echo "  make down            - Arreter services Docker"
	@echo "  make restart         - Redemarrer services Docker"
	@echo "  make ps              - Etat des conteneurs"
	@echo "  make logs            - Logs nginx"
	@echo "  make install         - Installation initiale Drupal"
	@echo "  make reinstall       - Reinstallation forcee Drupal"
	@echo "  make composer-install- Composer install sur l'hote"
	@echo "  make composer-update - Composer update sur l'hote"
	@echo "  make npm-install     - npm install sur l'hote"
	@echo "  make drush-status    - Drush status"
	@echo "  make drush-cr        - Cache rebuild"
	@echo "  make drush-uli       - One-time login URL"
	@echo "  make modules-list    - Liste modules actives"
	@echo "  make theme-admin     - Theme admin courant"
	@echo "  make db-reset        - Drop/Create DB drupal"
	@echo "  make up-wsl          - Demarrer stack WSL (port 8081 par defaut)"
	@echo "  make down-wsl        - Arreter stack WSL"
	@echo "  make ps-wsl          - Etat stack WSL"
	@echo "  make verify-wsl      - Verifications post-migration WSL"
	@echo "  make geocoder-set-key KEY=<api_key> - Configurer la cle Google Maps API pour le geocoding"
	@echo "  make cleanup         - Nettoyer fichiers Drupal obsoletes (config/sync temporaires, caches)"
	@echo "  make rebuild         - Reconstruire l'image PHP (apres modification Dockerfile)"
	@echo "  make import-crm      - Import complet CRM (agents, offres, divisions, media, dictionnaire)"
	@echo "  make import-status   - Statut des migrations CRM"
	@echo "  make import-reset    - Reinitialiser toutes les migrations CRM"
	@echo "  make import-rollback - Rollback de toutes les migrations CRM"

up:
	docker compose -f "$(COMPOSE_FILE)" up -d

down:
	docker compose -f "$(COMPOSE_FILE)" down

restart: down up

ps:
	docker compose -f "$(COMPOSE_FILE)" ps

logs:
	docker compose -f "$(COMPOSE_FILE)" logs -f nginx

composer-install:
	cd "$(SRC_DIR)" && COMPOSER_PROCESS_TIMEOUT=2000 composer install --no-interaction --prefer-dist

composer-update:
	cd "$(SRC_DIR)" && COMPOSER_PROCESS_TIMEOUT=2000 composer update --no-interaction --prefer-dist

npm-install:
	cd "$(SRC_DIR)" && npm install

bootstrap: up composer-install

install:
	bash "$(SRC_DIR)/scripts/drupal/install.sh"

reinstall:
	bash "$(SRC_DIR)/scripts/drupal/install.sh" --force

drush-status:
	$(DRUSH) status --fields=bootstrap,db-status,drupal-version,drush-version'

drush-cr:
	$(DRUSH) cr'

drush-uli:
	$(DRUSH) uli'

modules-list:
	$(DRUSH) pml --status=enabled --type=module --no-core --format=list'

theme-admin:
	$(DRUSH) cget system.theme admin'

db-reset:
	docker exec -i "$(PG_CONTAINER)" sh -lc "psql -U drupal -d postgres -c \"DROP DATABASE IF EXISTS drupal;\" && psql -U drupal -d postgres -c \"CREATE DATABASE drupal;\""

up-wsl:
	docker compose -f "$(COMPOSE_FILE)" -f "$(COMPOSE_WSL_FILE)" up -d --build php nginx

down-wsl:
	docker compose -f "$(COMPOSE_FILE)" -f "$(COMPOSE_WSL_FILE)" down

ps-wsl:
	docker compose -f "$(COMPOSE_FILE)" -f "$(COMPOSE_WSL_FILE)" ps

verify-wsl:
	bash "$(SRC_DIR)/scripts/drupal/verify.sh"

geocoder-set-key:
	@test -n "$(KEY)" || (echo "Usage: make geocoder-set-key KEY=<votre_cle_api_google_maps>" && exit 1)
	$(DRUSH) config:set geocoder.geocoder_provider.google_maps id google_maps --yes
	$(DRUSH) config:set geocoder.geocoder_provider.google_maps label "Google Maps" --yes
	$(DRUSH) config:set geocoder.geocoder_provider.google_maps plugin googlemaps --yes
	$(DRUSH) config:set geocoder.geocoder_provider.google_maps configuration.apiKey "$(KEY)" --yes
	$(DRUSH) cr

cleanup:
	@echo "🧹 Nettoyage des fichiers Drupal obsolètes..."
	@bash "$(SRC_DIR)/scripts/drupal/cleanup.sh"

rebuild:
	@echo "🔧 Reconstruction de l'image PHP avec nouveau UID/GID..."
	docker compose -f "$(COMPOSE_FILE)" build --no-cache php
	@echo "✅ Image reconstruite. Redémarrez avec 'make restart'"

import-crm:
	@bash "$(SRC_DIR)/scripts/drupal/import-crm.sh"

import-status:
	@echo "📊 Statut des migrations CRM:"
	@$(DRUSH) migrate:status

import-reset:
	@echo "🔄 Réinitialisation de toutes les migrations CRM..."
	@$(DRUSH) migrate:reset-status ps_agent_avatar_file_from_xml || true
	@$(DRUSH) migrate:reset-status ps_agent_from_xml || true
	@$(DRUSH) migrate:reset-status ps_file_from_xml || true
	@$(DRUSH) migrate:reset-status ps_media_from_xml || true
	@$(DRUSH) migrate:reset-status ps_offer_from_xml || true
	@$(DRUSH) migrate:reset-status ps_surface_division_from_xml || true
	@echo "✅ Migrations réinitialisées"

import-rollback:
	@echo "⏮️  Rollback de toutes les migrations CRM..."
	@$(DRUSH) migrate:rollback ps_offer_from_xml || true
	@$(DRUSH) migrate:rollback ps_surface_division_from_xml || true
	@$(DRUSH) migrate:rollback ps_media_from_xml || true
	@$(DRUSH) migrate:rollback ps_file_from_xml || true
	@$(DRUSH) migrate:rollback ps_agent_from_xml || true
	@$(DRUSH) migrate:rollback ps_agent_avatar_file_from_xml || true
	@echo "✅ Rollback terminé"
