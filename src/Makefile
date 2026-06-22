SHELL := /usr/bin/env bash
.DEFAULT_GOAL := help

# Paths for host (docker compose file location)
PROJECT_ROOT_HOST := $(CURDIR)/..
# Paths for container (working directory inside container)
PROJECT_ROOT_CONTAINER := /var/www
SRC_DIR_CONTAINER := /var/www/html

SCRIPTS_CLI := $(SRC_DIR_CONTAINER)/scripts/main.sh
COUNTRY ?= com

.PHONY: \
	help build verify install reinstall install-from-conf import demo deploy drush-cr \
	index-solr export-solr rbac-sync rbac-export create-test-users \
	drush drush-uli drush-status drush-cex rebuild-permissions composer-install composer-update npm-install npm-audit \
	seed-site-configs export-all-configs translations-fetch translations-fetch

help:
	@echo "Property Search — src/ (deployable artefact)"
	@echo ""
	@echo "  make build [--production via scripts/main.sh tools build --production]"
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
	@echo "All commands run in Docker container (ps_php). Requires container running."

build:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh tools build $(filter-out build,$(MAKECMDGOALS))"

verify:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh tools check"

composer-install:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php sh -c 'cd "$(SRC_DIR_CONTAINER)" && COMPOSER_PROCESS_TIMEOUT=2000 composer install --no-interaction --prefer-dist'

composer-update:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php sh -c 'cd "$(SRC_DIR_CONTAINER)" && COMPOSER_PROCESS_TIMEOUT=2000 composer update --no-interaction --prefer-dist'

npm-install:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php sh -c 'cd "$(SRC_DIR_CONTAINER)" && npm ci --no-audit --no-fund'
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php sh -c 'cd "$(SRC_DIR_CONTAINER)/web/themes/custom/ui_suite_bnp" && npm ci --no-audit --no-fund'
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php sh -c 'cd "$(SRC_DIR_CONTAINER)/web/themes/custom/ps_theme" && npm ci --no-audit --no-fund'

npm-audit:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php sh -c 'cd "$(SRC_DIR_CONTAINER)" && npm audit'

install:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal install $(filter-out install,$(MAKECMDGOALS))"

reinstall:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal install --force $(filter-out reinstall install,$(MAKECMDGOALS))"

install-from-conf:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal install-from-conf $(filter-out install-from-conf,$(MAKECMDGOALS))"

reinstall-from-conf:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal install-from-conf --force $(filter-out reinstall-from-conf install-from-conf,$(MAKECMDGOALS))"

seed-site-configs:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/multisite/seed-site-configs.sh"

export-all-configs:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal export-all-configs $(filter-out export-all-configs,$(MAKECMDGOALS))"

import:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal import $(filter-out import,$(MAKECMDGOALS))"

demo:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal demo $(filter-out demo,$(MAKECMDGOALS))"

deploy:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal deploy $(filter-out deploy,$(MAKECMDGOALS))"

drush-cr:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal cache-clear $(filter-out drush-cr,$(MAKECMDGOALS))"

index-solr:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal index-solr $(filter-out index-solr,$(MAKECMDGOALS))"

export-solr:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal export-solr $(filter-out export-solr,$(MAKECMDGOALS))"

rbac-sync:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal rbac-sync $(filter-out rbac-sync,$(MAKECMDGOALS))"

rbac-export:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal rbac-sync --export $(filter-out rbac-export,$(MAKECMDGOALS))"

create-test-users:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal create-test-users $(filter-out create-test-users,$(MAKECMDGOALS))"

translations-fetch:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal translations-fetch $(filter-out translations-fetch,$(MAKECMDGOALS))"

drush:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal drush $(filter-out drush,$(MAKECMDGOALS))"

drush-uli:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal drush $(COUNTRY) uli"

drush-status:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal drush $(COUNTRY) status --fields=bootstrap,db-status,drupal-version,drush-version"

drush-cex:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal drush $(COUNTRY) cex -y"

rebuild-permissions:
	docker compose -f "$(PROJECT_ROOT_HOST)/docker/docker-compose.yml" exec -T php bash -c "bash scripts/main.sh drupal rebuild-permissions $(filter-out rebuild-permissions,$(MAKECMDGOALS))"

%:
	@:
