SHELL := /usr/bin/env bash
.DEFAULT_GOAL := help

SRC_DIR := $(CURDIR)
SCRIPTS_CLI := $(SRC_DIR)/scripts/main.sh
COUNTRY ?= com

.PHONY: \
	help build verify install reinstall import demo deploy drush-cr \
	index-solr export-solr rbac-sync rbac-export create-test-users \
	drush drush-uli drush-status drush-cex composer-install composer-update npm-install

help:
	@echo "Property Search — src/ (deployable artefact)"
	@echo ""
	@echo "  make build [--production via scripts/main.sh tools build --production]"
	@echo "  make verify"
	@echo "  make install [country...] | reinstall [country...]"
	@echo "  make import [country] | demo [country]"
	@echo "  make deploy [country...]"
	@echo "  make drush-cr [country...]"
	@echo "  make index-solr [country] | export-solr [country]"
	@echo "  make rbac-sync [country] | rbac-export [country]"
	@echo "  make create-test-users [country]"
	@echo "  make drush [country] <args...>   (default COUNTRY=$(COUNTRY))"
	@echo "  make drush-uli | drush-status | drush-cex"
	@echo ""
	@echo "Drush runs on the host. Requires vendor/ (make build / composer-install)."

build:
	bash "$(SCRIPTS_CLI)" tools build $(filter-out build,$(MAKECMDGOALS))

verify:
	bash "$(SCRIPTS_CLI)" tools check

composer-install:
	cd "$(SRC_DIR)" && COMPOSER_PROCESS_TIMEOUT=2000 composer install --no-interaction --prefer-dist

composer-update:
	cd "$(SRC_DIR)" && COMPOSER_PROCESS_TIMEOUT=2000 composer update --no-interaction --prefer-dist

npm-install:
	cd "$(SRC_DIR)" && npm install

install:
	bash "$(SCRIPTS_CLI)" drupal install $(filter-out install,$(MAKECMDGOALS))

reinstall:
	bash "$(SCRIPTS_CLI)" drupal install --force $(filter-out reinstall install,$(MAKECMDGOALS))

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

drush:
	bash "$(SCRIPTS_CLI)" drupal drush $(filter-out drush,$(MAKECMDGOALS))

drush-uli:
	bash "$(SCRIPTS_CLI)" drupal drush $(COUNTRY) uli

drush-status:
	bash "$(SCRIPTS_CLI)" drupal drush $(COUNTRY) status --fields=bootstrap,db-status,drupal-version,drush-version

drush-cex:
	bash "$(SCRIPTS_CLI)" drupal drush $(COUNTRY) cex -y

%:
	@:
