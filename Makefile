SHELL := /usr/bin/env bash
.DEFAULT_GOAL := help

PROJECT_ROOT := $(CURDIR)
COMPOSE_FILE := $(PROJECT_ROOT)/docker/docker-compose.yml
SRC_DIR := $(PROJECT_ROOT)/src
SCRIPTS_CLI := $(SRC_DIR)/scripts/main.sh

PHP_CONTAINER := ps_php
PG_CONTAINER := ps_postgres

define drush
docker exec -i $(PHP_CONTAINER) sh -lc 'cd /var/www/html && vendor/bin/drush $(1)'
endef

XML_SAMPLE := data/xml/bnppre_sample_50_per_type.xml
XML_TARGET := src/web/sites/default/files/crm/offers.xml

.PHONY: \
	help up down restart ps logs rebuild \
	composer-install composer-update npm-install bootstrap \
	install reinstall deploy verify cleanup \
	drush-status drush-cr drush-uli modules-list theme-admin db-reset \
	dictionary-import xml-stage-sample \
	import-crm import-sample-xml import-status import-reset import-rollback

help:
	@echo "Cibles disponibles:"
	@echo "  make up                - Demarrer services Docker"
	@echo "  make down              - Arreter services Docker"
	@echo "  make restart           - Redemarrer services Docker"
	@echo "  make ps                - Etat des conteneurs"
	@echo "  make logs              - Logs nginx"
	@echo "  make rebuild           - Reconstruire l'image PHP"
	@echo "  make install           - Installation Drupal"
	@echo "  make reinstall         - Reinstallation Drupal (--force)"
	@echo "  make deploy            - Workflow deploiement Drupal"
	@echo "  make verify            - Verifier build/dependances scripts"
	@echo "  make dictionary-import - Import des dictionnaires"
	@echo "  make xml-stage-sample  - Copier XML sample vers la source migrate"
	@echo "  make import-sample-xml - Import XML sample (pipeline migrate)"
	@echo "  make import-crm        - Import offers CRM and execute migrate dependencies"
	@echo "  make import-status     - Statut des migrations"
	@echo "  make import-reset      - Reset status migrations"
	@echo "  make import-rollback   - Rollback migrations"

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
	@echo "Reconstruction de l'image PHP..."
	docker compose -f "$(COMPOSE_FILE)" build --no-cache php
	@echo "Image reconstruite. Lancez 'make restart'"

composer-install:
	cd "$(SRC_DIR)" && COMPOSER_PROCESS_TIMEOUT=2000 composer install --no-interaction --prefer-dist

composer-update:
	cd "$(SRC_DIR)" && COMPOSER_PROCESS_TIMEOUT=2000 composer update --no-interaction --prefer-dist

npm-install:
	cd "$(SRC_DIR)" && npm install

bootstrap: up composer-install

install:
	bash "$(SCRIPTS_CLI)" drupal install

reinstall:
	bash "$(SCRIPTS_CLI)" drupal install --force

deploy:
	bash "$(SCRIPTS_CLI)" drupal deploy

verify:
	bash "$(SCRIPTS_CLI)" tools check

cleanup:
	bash "$(SCRIPTS_CLI)" drupal cache-clear

drush-status:
	$(call drush,status --fields=bootstrap,db-status,drupal-version,drush-version)

drush-cr:
	bash "$(SCRIPTS_CLI)" drupal cache-clear

drush-uli:
	$(call drush,uli)

modules-list:
	$(call drush,pml --status=enabled --type=module --no-core --format=list)

theme-admin:
	$(call drush,cget system.theme admin)

db-reset:
	docker exec -i "$(PG_CONTAINER)" sh -lc "psql -U drupal -d postgres -c \"DROP DATABASE IF EXISTS drupal;\" && psql -U drupal -d postgres -c \"CREATE DATABASE drupal;\""

dictionary-import:
	$(call drush,ps:dictionary:import -y)

xml-stage-sample:
	@test -f "$(XML_SAMPLE)" || (echo "Fichier introuvable: $(XML_SAMPLE)" && exit 1)
	cp "$(XML_SAMPLE)" "$(XML_TARGET)"
	@echo "XML source staged: $(XML_TARGET)"

import-crm:
	$(call drush,pm:install migrate migrate_plus migrate_tools ps_migrate -y)
	$(call drush,migrate:import ps_offer_from_xml --update --execute-dependencies -y)

import-sample-xml: xml-stage-sample dictionary-import import-crm

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
