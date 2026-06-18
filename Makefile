SHELL := /usr/bin/env bash
.DEFAULT_GOAL := help

PROJECT_ROOT := $(CURDIR)
COMPOSE_FILE := $(PROJECT_ROOT)/docker/docker-compose.yml
SRC_DIR := $(PROJECT_ROOT)/src
SRC_MAKE := $(MAKE) -C "$(SRC_DIR)"
COUNTRY ?= com

.PHONY: \
	help up down restart ps logs rebuild \
	env bootstrap generate-multisite verify-multisite fix-permissions init-solr-cores \
	build verify install reinstall import demo deploy drush-cr index-solr export-solr \
	rbac-sync rbac-export create-test-users \
	drush drush-uli drush-status drush-cex rbac-sec-e2e \
	composer-install composer-update npm-install

help:
	@echo "PS Project — dev environment (repo root)"
	@echo ""
	@echo "Docker (local dev only):"
	@echo "  make up | down | restart | ps | logs | rebuild"
	@echo "  make env | fix-permissions | init-solr-cores"
	@echo ""
	@echo "Multisite (repo root → syncs into src/):"
	@echo "  make generate-multisite | verify-multisite"
	@echo ""
	@echo "Project commands (delegate to src/Makefile, Drush on host):"
	@echo "  make bootstrap          = env + up + generate-multisite + build"
	@echo "  make build | verify | install | deploy | drush-cr | …"
	@echo ""
	@echo "See src/Makefile and README.md for full project command list."

# --- Docker (dev only) ---

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

env:
	bash "$(PROJECT_ROOT)/scripts/docker/env.sh"

fix-permissions:
	bash "$(PROJECT_ROOT)/scripts/docker/fix-permissions.sh"

init-solr-cores:
	bash "$(PROJECT_ROOT)/scripts/docker/index-solr.sh"

# --- Multisite (repo root) ---

generate-multisite:
	bash "$(PROJECT_ROOT)/scripts/multisite/generate.sh"

verify-multisite:
	bash "$(PROJECT_ROOT)/scripts/multisite/verify.sh"

bootstrap: env up generate-multisite build

# --- Delegate to src/ ---

build:
	$(SRC_MAKE) build $(filter-out build,$(MAKECMDGOALS))

verify:
	$(SRC_MAKE) verify

composer-install:
	$(SRC_MAKE) composer-install

composer-update:
	$(SRC_MAKE) composer-update

npm-install:
	$(SRC_MAKE) npm-install

install:
	$(SRC_MAKE) install $(filter-out install,$(MAKECMDGOALS))

reinstall:
	$(SRC_MAKE) reinstall $(filter-out reinstall install,$(MAKECMDGOALS))

import:
	$(SRC_MAKE) import $(filter-out import,$(MAKECMDGOALS))

demo:
	$(SRC_MAKE) demo $(filter-out demo,$(MAKECMDGOALS))

deploy:
	$(SRC_MAKE) deploy $(filter-out deploy,$(MAKECMDGOALS))

drush-cr:
	$(SRC_MAKE) drush-cr $(filter-out drush-cr,$(MAKECMDGOALS))

index-solr: init-solr-cores
	$(SRC_MAKE) index-solr $(filter-out index-solr,$(MAKECMDGOALS))

export-solr:
	$(SRC_MAKE) export-solr $(filter-out export-solr,$(MAKECMDGOALS))

rbac-sync:
	$(SRC_MAKE) rbac-sync $(filter-out rbac-sync,$(MAKECMDGOALS))

rbac-export:
	$(SRC_MAKE) rbac-export $(filter-out rbac-export,$(MAKECMDGOALS))

create-test-users:
	$(SRC_MAKE) create-test-users $(filter-out create-test-users,$(MAKECMDGOALS))

drush:
	$(SRC_MAKE) drush $(filter-out drush,$(MAKECMDGOALS))

drush-uli:
	$(SRC_MAKE) drush-uli COUNTRY=$(COUNTRY)

drush-status:
	$(SRC_MAKE) drush-status COUNTRY=$(COUNTRY)

drush-cex:
	$(SRC_MAKE) drush-cex COUNTRY=$(COUNTRY)

rbac-sec-e2e:
	bash "$(SRC_DIR)/web/modules/custom/bnp_admin/tests/e2e_rbac_sec_ctx.sh"

%:
	@:
