SHELL := /usr/bin/env bash
.DEFAULT_GOAL := help

PROJECT_ROOT := $(CURDIR)
COMPOSE_FILE := $(PROJECT_ROOT)/docker/docker-compose.yml
SRC_DIR := $(PROJECT_ROOT)/src
SCRIPTS_CLI := $(SRC_DIR)/scripts/main.sh

PHP_CONTAINER := ps_php
PG_CONTAINER := ps_postgres

define drush
docker exec -u www-data -i $(PHP_CONTAINER) sh -lc 'cd /var/www/html && vendor/bin/drush $(1)'
endef

PS_COUNTRY ?= com

.PHONY: \
	help up down restart ps logs rebuild \
	composer-install composer-update npm-install env bootstrap \
	build verify install reinstall import demo deploy \
	rbac-sync rbac-export create-test-users rbac-sec-e2e \
	drush-status drush-cr drush-uli drush-cex fix-permissions modules-list theme-admin db-reset \
	dictionary-import import-crm import-status import-reset import-rollback

help:
	@echo "Docker:"
	@echo "  make up / down / restart / ps / logs / rebuild"
	@echo ""
	@echo "Dependencies:"
	@echo "  make env              - Generate src/.env (dev)"
	@echo "  make build            - composer + npm + libs"
	@echo "  make verify           - CI gate (vendor + libraries)"
	@echo "  make composer-install"
	@echo ""
	@echo "Drupal scripts (src/scripts/main.sh):"
	@echo "  make install [country]   - Shell greenfield (default: all countries)"
	@echo "  make reinstall [country] - Force reinstall"
	@echo "  make import [country]    - CRM sample + Solr (default: com)"
	@echo "  make demo [country]      - Demo content (default: com)"
	@echo "  make deploy              - cim + updb + cr (all countries)"
	@echo "  make drush-cr            - Cache rebuild all countries"
	@echo "  make rbac-sync           - Import BNP roles (not in install)"
	@echo "  make create-test-users   - QA test accounts"
	@echo ""
	@echo "Drush shortcuts (docker, single default URI):"
	@echo "  make drush-status / drush-uli / drush-cex"
	@echo "  make dictionary-import / import-crm / import-status"

up:
	docker compose -f "$(COMPOSE_FILE)" up -d

down:
	docker compose -f "$(COMPOSE_FILE)" down

restart: down up

ps:
	docker compose -f "$(COMPOSE_FILE)" ps

logs:
	docker compose -f "$(COMPOSE_FILE)" logs -f nginx

rebuild:
	docker compose -f "$(COMPOSE_FILE)" build --no-cache php
	@echo "Image rebuilt. Run: make restart"

composer-install:
	cd "$(SRC_DIR)" && COMPOSER_PROCESS_TIMEOUT=2000 composer install --no-interaction --prefer-dist

composer-update:
	cd "$(SRC_DIR)" && COMPOSER_PROCESS_TIMEOUT=2000 composer update --no-interaction --prefer-dist

npm-install:
	cd "$(SRC_DIR)" && npm install

bootstrap: env up build

env:
	bash "$(SCRIPTS_CLI)" tools env

build:
	bash "$(SCRIPTS_CLI)" tools build

verify:
	bash "$(SCRIPTS_CLI)" tools check

install:
	bash "$(SCRIPTS_CLI)" drupal install $(filter-out install,$(MAKECMDGOALS))

reinstall:
	bash "$(SCRIPTS_CLI)" drupal install --force $(filter-out reinstall install,$(MAKECMDGOALS))

import:
	bash "$(SCRIPTS_CLI)" drupal import $(filter-out import,$(MAKECMDGOALS))

demo:
	bash "$(SCRIPTS_CLI)" drupal demo $(filter-out demo,$(MAKECMDGOALS))

deploy:
	bash "$(SCRIPTS_CLI)" drupal deploy

rbac-sync:
	bash "$(SCRIPTS_CLI)" drupal rbac-sync $(filter-out rbac-sync,$(MAKECMDGOALS))

rbac-export:
	bash "$(SCRIPTS_CLI)" drupal rbac-sync --export $(filter-out rbac-export,$(MAKECMDGOALS))

create-test-users:
	bash "$(SCRIPTS_CLI)" drupal create-test-users $(filter-out create-test-users,$(MAKECMDGOALS))

rbac-sec-e2e:
	cd "$(SRC_DIR)" && composer test:rbac-sec-e2e

drush-cr:
	bash "$(SCRIPTS_CLI)" drupal cache-clear

drush-status:
	$(call drush,status --fields=bootstrap,db-status,drupal-version,drush-version)

drush-uli:
	$(call drush,uli)

drush-cex:
	$(call drush,cex -y)

fix-permissions:
	docker exec -i $(PHP_CONTAINER) chown -R www-data:www-data \
		/var/www/html/config/sync \
		/var/www/html/web/modules/custom \
		/var/www/html/web/themes/custom

modules-list:
	$(call drush,pml --status=enabled --type=module --no-core --format=list)

theme-admin:
	$(call drush,cget system.theme admin)

db-reset:
	docker exec -i "$(PG_CONTAINER)" sh -lc "psql -U drupal -d postgres -c \"DROP DATABASE IF EXISTS drupal;\" && psql -U drupal -d postgres -c \"CREATE DATABASE drupal;\""

dictionary-import:
	$(call drush,ps:dictionary:import -y)

import-crm:
	$(call drush,en -y migrate migrate_plus migrate_tools ps_migrate)
	$(call drush,migrate:import ps_offer_from_xml --update --execute-dependencies -y)
	$(call drush,migrate:import ps_offer_translations_from_xml --update -y)

import-status:
	$(call drush,migrate:status)

import-reset:
	$(call drush,migrate:reset-status ps_feature_groups_from_xml) || true
	$(call drush,migrate:reset-status ps_feature_definitions_from_xml) || true
	$(call drush,migrate:reset-status ps_agent_avatar_file_from_xml) || true
	$(call drush,migrate:reset-status ps_agent_from_xml) || true
	$(call drush,migrate:reset-status ps_file_from_xml) || true
	$(call drush,migrate:reset-status ps_media_from_xml) || true
	$(call drush,migrate:reset-status ps_media_virtual_tour_from_xml) || true
	$(call drush,migrate:reset-status ps_offer_from_xml) || true
	$(call drush,migrate:reset-status ps_offer_translations_from_xml) || true
	$(call drush,migrate:reset-status ps_surface_division_from_xml) || true

import-rollback:
	$(call drush,migrate:rollback ps_offer_translations_from_xml) || true
	$(call drush,migrate:rollback ps_surface_division_from_xml) || true
	$(call drush,migrate:rollback ps_offer_from_xml) || true
	$(call drush,migrate:rollback ps_media_virtual_tour_from_xml) || true
	$(call drush,migrate:rollback ps_media_from_xml) || true
	$(call drush,migrate:rollback ps_file_from_xml) || true
	$(call drush,migrate:rollback ps_agent_from_xml) || true
	$(call drush,migrate:rollback ps_agent_avatar_file_from_xml) || true
	$(call drush,migrate:rollback ps_feature_definitions_from_xml) || true
	$(call drush,migrate:rollback ps_feature_groups_from_xml) || true

%:
	@:
