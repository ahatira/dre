SHELL := /usr/bin/env bash
.DEFAULT_GOAL := help

PROJECT_ROOT := $(CURDIR)
COMPOSE_FILE := $(PROJECT_ROOT)/docker/docker-compose.yml
SRC_DIR := $(PROJECT_ROOT)/src
SCRIPTS_CLI := $(SRC_DIR)/scripts/main.sh

COUNTRY ?= com

.PHONY: \
	help up down restart ps logs rebuild \
	env bootstrap build verify composer-install composer-update npm-install fix-permissions \
	install reinstall import demo deploy drush-cr index-solr \
	rbac-sync rbac-export create-test-users \
	drush drush-uli drush-status drush-cex rbac-sec-e2e

help:
	@echo "PS Project — Makefile wraps src/scripts/main.sh"
	@echo ""
	@echo "Docker:"
	@echo "  make up | down | restart | ps | logs | rebuild"
	@echo ""
	@echo "Tools (scripts/tools/):"
	@echo "  make env | build | verify | bootstrap"
	@echo "  make composer-install | composer-update | npm-install"
	@echo "  make fix-permissions"
	@echo ""
	@echo "Drupal (scripts/drupal/):"
	@echo "  make install [country...]      - Greenfield multisite shell"
	@echo "  make reinstall [country...]    - Force reinstall"
	@echo "  make import [country]          - CRM migrate + Solr"
	@echo "  make demo [country]            - Demo content"
	@echo "  make deploy                    - cim + updb + cr (all countries)"
	@echo "  make drush-cr                  - Cache rebuild (all countries)"
	@echo "  make index-solr [country]      - Reindex Search API offers"
	@echo "  make rbac-sync [country]       - Import BNP RBAC roles"
	@echo "  make rbac-export [country]     - Export RBAC roles to YAML"
	@echo "  make create-test-users [country]"
	@echo ""
	@echo "Drush (@ps.{country}, default COUNTRY=$(COUNTRY)):"
	@echo "  make drush [country] <args...>   e.g. make drush fr uli"
	@echo "  make drush-uli | drush-status | drush-cex   (override: COUNTRY=fr)"
	@echo ""
	@echo "Tests:"
	@echo "  make rbac-sec-e2e"

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

fix-permissions:
	bash "$(SCRIPTS_CLI)" tools fix-permissions

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

drush-cr:
	bash "$(SCRIPTS_CLI)" drupal cache-clear

index-solr:
	bash "$(SCRIPTS_CLI)" drupal index-solr $(filter-out index-solr,$(MAKECMDGOALS))

rbac-sync:
	bash "$(SCRIPTS_CLI)" drupal rbac-sync $(filter-out rbac-sync,$(MAKECMDGOALS))

rbac-export:
	bash "$(SCRIPTS_CLI)" drupal rbac-sync --export $(filter-out rbac-export,$(MAKECMDGOALS))

create-test-users:
	bash "$(SCRIPTS_CLI)" drupal create-test-users $(filter-out create-test-users,$(MAKECMDGOALS))

drush:
	bash "$(SCRIPTS_CLI)" drupal drush $(filter-out drush,$(MAKECMDGOALS))

drush-uli:
	bash "$(SCRIPTS_CLI)" drupal drush $(COUNTRY) uli

drush-status:
	bash "$(SCRIPTS_CLI)" drupal drush $(COUNTRY) status --fields=bootstrap,db-status,drupal-version,drush-version

drush-cex:
	bash "$(SCRIPTS_CLI)" drupal drush $(COUNTRY) cex -y

rbac-sec-e2e:
	bash "$(SRC_DIR)/web/modules/custom/bnp_admin/tests/e2e_rbac_sec_ctx.sh"

%:
	@:
