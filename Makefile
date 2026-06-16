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
XML_SAMPLE := data/xml/bnppre_sample_50_per_type.xml
XML_TARGET := src/web/sites/$(PS_COUNTRY)/files/crm/offers.xml

.PHONY: \
	help up down restart ps logs rebuild \
	composer-install composer-update npm-install env bootstrap \
	provision-databases provision-site-files solr-init \
	install reinstall demo post-install index-solr deploy verify cleanup \
	rbac-sync rbac-export create-test-users rbac-sec-e2e \
	drush-status drush-cr drush-uli drush-cex fix-permissions modules-list theme-admin db-reset \
	dictionary-import generate-sample-xml xml-stage-sample \
	import-crm import-sample-xml import-status import-reset import-rollback

help:
	@echo "Cibles disponibles:"
	@echo "  make up                - Demarrer services Docker"
	@echo "  make env               - Generate src/.env from .env.dist (USER_UID substitution)"
	@echo "  make provision-databases - Create PostgreSQL DBs from src/.env"
	@echo "  make provision-site-files - Create per-country public/private dirs"
	@echo "  make down              - Arreter services Docker"
	@echo "  make restart           - Redemarrer services Docker"
	@echo "  make ps                - Etat des conteneurs"
	@echo "  make logs              - Logs nginx"
	@echo "  make rebuild           - Reconstruire l'image PHP"
	@echo "  make install           - Installation multisite (tous les pays par defaut)"
	@echo "  make install com       - Installation d'un pays (com, fr, be, es, ie, it, lu, nl, pl)"
	@echo "  make install com fr      - Installation de plusieurs pays"
	@echo "  make install --minimal fr - Coquille seule (sans demo/offres/Solr)"
	@echo "  make reinstall com     - Reinstallation forcee d'un pays"
	@echo "  make post-install      - Demo + offres sample + ps_search/ps_seo + Solr (apres install --minimal)"
	@echo "  make verify-country fr shell - Verifier coquille sans demo (pays + mode)"
	@echo "  make verify-country es demo    - Verifier site avec contenu demo"
	@echo "  make rbac-sync         - Importer les roles BNP avec permissions PS (post-install)"
	@echo "  make rbac-export       - Exporter les roles actifs vers bnp_admin/config/rbac/"
	@echo "  make create-test-users - Creer un compte test par role BNP"
	@echo "  make rbac-sec-e2e      - E2E SEC + CTX-ADM (RBAC recette)"
	@echo "  make demo              - Contenu demo (menus, homepage, mega-menu CMI)"
	@echo "  make import-sample-xml - Import offres sample (migrate CRM XML)"
	@echo "  make index-solr        - Indexer les offres dans Solr"
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

bootstrap: env up composer-install

env:
	bash "$(SRC_DIR)/scripts/tools/setup-env.sh"

provision-databases:
	bash "$(SRC_DIR)/scripts/drupal/provision-databases.sh"

provision-site-files:
	bash "$(SRC_DIR)/scripts/drupal/provision-site-files.sh"

solr-init:
	chmod +x "$(PROJECT_ROOT)/docker/solr/init-cores.sh"
	"$(PROJECT_ROOT)/docker/solr/init-cores.sh"

install:
	bash "$(SCRIPTS_CLI)" drupal install $(filter-out install,$(MAKECMDGOALS))

reinstall:
	bash "$(SCRIPTS_CLI)" drupal install --force $(filter-out reinstall install,$(MAKECMDGOALS))

post-install:
	bash "$(SCRIPTS_CLI)" drupal post-install

rbac-sync:
	bash "$(SCRIPTS_CLI)" drupal rbac-sync

rbac-export:
	bash "$(SCRIPTS_CLI)" drupal rbac-sync --export

create-test-users:
	bash "$(SCRIPTS_CLI)" drupal create-test-users

rbac-sec-e2e:
	cd "$(SRC_DIR)" && composer test:rbac-sec-e2e

demo:
	bash "$(SCRIPTS_CLI)" drupal demo

verify-country:
	bash "$(SCRIPTS_CLI)" drupal verify-country $(filter-out verify-country,$(MAKECMDGOALS))

index-solr:
	bash "$(SCRIPTS_CLI)" drupal index-solr

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

drush-cex:
	$(call drush,cex -y)

fix-permissions:
	@echo "Correction des droits (uid $$(id -u) via www-data dans le conteneur)..."
	docker exec -i $(PHP_CONTAINER) chown -R www-data:www-data \
		/var/www/html/config/sync \
		/var/www/html/web/modules/custom \
		/var/www/html/web/themes/custom
	@echo "Termine. Exemple: ls -la src/config/sync/views.view.ps_news.yml"

modules-list:
	$(call drush,pml --status=enabled --type=module --no-core --format=list)

theme-admin:
	$(call drush,cget system.theme admin)

db-reset:
	docker exec -i "$(PG_CONTAINER)" sh -lc "psql -U drupal -d postgres -c \"DROP DATABASE IF EXISTS drupal;\" && psql -U drupal -d postgres -c \"CREATE DATABASE drupal;\""

dictionary-import:
	$(call drush,ps:dictionary:import -y)

generate-sample-xml:
	bash "$(SCRIPTS_CLI)" tools generate-sample-xml $(PS_COUNTRY)

xml-stage-sample:
	bash "$(SCRIPTS_CLI)" drupal stage-sample-xml $(PS_COUNTRY)

import-crm:
	$(call drush,en -y migrate migrate_plus migrate_tools ps_migrate)
	$(call drush,migrate:import ps_offer_from_xml --update --execute-dependencies -y)
	$(call drush,migrate:import ps_offer_translations_from_xml --update -y)

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

# Allow: make install --minimal
%:
	@:
