SHELL := /usr/bin/env bash
.DEFAULT_GOAL := help

SRC_DIR := $(CURDIR)
SCRIPTS_CLI := $(SRC_DIR)/scripts/main.sh
COUNTRY ?= com

.PHONY: \
	help build build-composer build-npm verify install reinstall install-from-conf import demo deploy drush-cr \
	index-solr export-solr rbac-sync rbac-export create-test-users \
	drush drush-uli drush-status drush-cex rebuild-permissions composer-install composer-update npm-install npm-audit \
	seed-site-configs export-all-configs translations-fetch translations-fetch

# Build flags — use make variables (GNU make treats --foo as its own options):
#   make build PRODUCTION=1
#   make build-composer PRODUCTION=1 NO_CACHE=1
# Legacy: make build -- --production
BUILD_SCRIPT_OPTS := $(filter-out build build-composer build-npm composer-install,$(MAKECMDGOALS))
ifeq ($(PRODUCTION),1)
BUILD_SCRIPT_OPTS += --production
endif
ifeq ($(NO_CACHE),1)
BUILD_SCRIPT_OPTS += --no-cache
endif
ifeq ($(KEEP_NPM),1)
BUILD_SCRIPT_OPTS += --keep-npm
endif

help:
	@echo "Property Search — src/ (deployable artefact)"
	@echo ""
	@echo "  make build [OPTIONS]                  — Composer + NPM (dev: full build)"
	@echo "  make build-composer [OPTIONS]         — Composer only (Jenkins stage 1)"
	@echo "  make build-npm [OPTIONS]                — NPM/themes only (Jenkins stage 2)"
	@echo "  make composer-install                   — alias for build-composer"
	@echo "  OPTIONS: PRODUCTION=1 NO_CACHE=1 KEEP_NPM=1  (or: make build -- --production)"
	@echo "  make verify"
	@echo "  make install [country...] | reinstall [country...]"
	@echo "  make install-from-conf [country...]  — CMI from config/sites/{code}"
	@echo "  make seed-site-configs               — seed sites from legacy config/sync"
	@echo "  make export-all-configs [country...] — drush cex per country"
	@echo "  make import [country] | demo [country]"
	@echo "  make deploy [country...]"
	@echo "  make drush-cr [country...] | rebuild-permissions [country...]"
	@echo "  make index-solr [country] | export-solr [country]"
	@echo "  make rbac-sync [country] | rbac-export [country]"
	@echo "  make create-test-users [country]"
	@echo "  make translations-fetch [country] [lang...]  — dev: populate translations/contrib"
	@echo "  make drush [country] <args...>   (default COUNTRY=$(COUNTRY))"
	@echo "  make drush-uli | drush-status | drush-cex"
	@echo "  make npm-install | npm-audit"
	@echo ""
	@echo "Drush runs on the host. Requires vendor/ (make build-composer / make build)."

build-composer:
	bash "$(SCRIPTS_CLI)" tools build --composer-only $(BUILD_SCRIPT_OPTS)

build-npm:
	bash "$(SCRIPTS_CLI)" tools build --npm-only $(BUILD_SCRIPT_OPTS)

build:
	bash "$(SCRIPTS_CLI)" tools build $(BUILD_SCRIPT_OPTS)

verify:
	bash "$(SCRIPTS_CLI)" tools check

composer-install: build-composer
	@:

composer-update:
	cd "$(SRC_DIR)" && COMPOSER_PROCESS_TIMEOUT=2000 composer update --no-interaction --prefer-dist

npm-install:
	cd "$(SRC_DIR)" && npm ci --no-audit --no-fund
	cd "$(SRC_DIR)/web/themes/custom/ui_suite_bnp" && npm ci --no-audit --no-fund
	cd "$(SRC_DIR)/web/themes/custom/ps_theme" && npm ci --no-audit --no-fund

npm-audit:
	bash "$(SCRIPTS_CLI)" tools npm-audit

install:
	bash "$(SCRIPTS_CLI)" drupal install $(filter-out install,$(MAKECMDGOALS))

reinstall:
	bash "$(SCRIPTS_CLI)" drupal install --force $(filter-out reinstall install,$(MAKECMDGOALS))

install-from-conf:
	bash "$(SCRIPTS_CLI)" drupal install-from-conf $(filter-out install-from-conf,$(MAKECMDGOALS))

reinstall-from-conf:
	bash "$(SCRIPTS_CLI)" drupal install-from-conf --force $(filter-out reinstall-from-conf install-from-conf,$(MAKECMDGOALS))

seed-site-configs:
	bash "$(SRC_DIR)/../scripts/multisite/seed-site-configs.sh"

export-all-configs:
	bash "$(SCRIPTS_CLI)" drupal export-all-configs $(filter-out export-all-configs,$(MAKECMDGOALS))

import:
	bash "$(SCRIPTS_CLI)" drupal import $(filter-out import,$(MAKECMDGOALS))

demo:
	bash "$(SCRIPTS_CLI)" drupal demo $(filter-out demo,$(MAKECMDGOALS))

deploy:
	bash "$(SCRIPTS_CLI)" drupal deploy $(filter-out deploy,$(MAKECMDGOALS))

drush-cr:
	bash "$(SCRIPTS_CLI)" drupal cache-clear $(filter-out drush-cr,$(MAKECMDGOALS))

index-solr:
	bash "$(SCRIPTS_CLI)" drupal index-solr $(filter-out index-solr,$(MAKECMDGOALS))

export-solr:
	bash "$(SCRIPTS_CLI)" drupal export-solr $(filter-out export-solr,$(MAKECMDGOALS))

rbac-sync:
	bash "$(SCRIPTS_CLI)" drupal rbac-sync $(filter-out rbac-sync,$(MAKECMDGOALS))

rbac-export:
	bash "$(SCRIPTS_CLI)" drupal rbac-sync --export $(filter-out rbac-export,$(MAKECMDGOALS))

create-test-users:
	bash "$(SCRIPTS_CLI)" drupal create-test-users $(filter-out create-test-users,$(MAKECMDGOALS))

translations-fetch:
	bash "$(SCRIPTS_CLI)" drupal translations-fetch $(filter-out translations-fetch,$(MAKECMDGOALS))

drush:
	bash "$(SCRIPTS_CLI)" drupal drush $(filter-out drush,$(MAKECMDGOALS))

drush-uli:
	bash "$(SCRIPTS_CLI)" drupal drush $(COUNTRY) uli

drush-status:
	bash "$(SCRIPTS_CLI)" drupal drush $(COUNTRY) status --fields=bootstrap,db-status,drupal-version,drush-version

drush-cex:
	bash "$(SCRIPTS_CLI)" drupal drush $(COUNTRY) cex -y

rebuild-permissions:
	bash "$(SCRIPTS_CLI)" drupal rebuild-permissions $(filter-out rebuild-permissions,$(MAKECMDGOALS))

%:
	@:
