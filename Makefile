SHELL := /usr/bin/env bash
.DEFAULT_GOAL := help

PROJECT_ROOT := $(CURDIR)
DRUSH := $(PROJECT_ROOT)/vendor/bin/drush
COMPOSER := composer
THEME_DIR := $(PROJECT_ROOT)/web/themes/custom/ui_suite_bnppre

.PHONY: help install install-site build-theme build-css theme-check theme-npm-install \
	drush-cr drush-status drush-uli drush-updb drush-cim drush-cex \
	composer-install composer-update composer-require composer-remove composer-validate \
	default-content-export default-content-export-references \
	default-content-export-module default-content-export-module-references \
	default-content-export-main-menu

help:
	@echo "Available targets:"
	@echo "  install                 - Install dependencies + npm + build theme"
	@echo "  install-site            - Run scripts/install.sh (fresh Drupal install + modules + themes)"
	@echo "  build-theme             - Build all theme assets"
	@echo "  build-css               - Build only theme CSS"
	@echo "  theme-check             - Run theme metadata/icons checks"
	@echo "  drush-cr                - Rebuild Drupal cache"
	@echo "  drush-status            - Show Drupal status"
	@echo "  drush-uli               - Generate one-time login link"
	@echo "  drush-updb              - Run database updates"
	@echo "  drush-cim               - Import config"
	@echo "  drush-cex               - Export config"
	@echo "  default-content-export ENTITY_TYPE= ENTITY_ID= [OUT=]"
	@echo "                         - Export one entity with default_content"
	@echo "  default-content-export-references ENTITY_TYPE= [ENTITY_ID=] [FOLDER=]"
	@echo "                         - Export one/all entities and references"
	@echo "  default-content-export-module MODULE="
	@echo "                         - Export entities listed in <module>.info.yml"
	@echo "  default-content-export-module-references MODULE="
	@echo "                         - Export entities+references listed in <module>.info.yml"
	@echo "  default-content-export-main-menu"
	@echo "                         - Build/update main menu tree and export in ps_default_content"
	@echo "  composer-install        - Install PHP dependencies"
	@echo "  composer-update         - Update PHP dependencies"
	@echo "  composer-require PKG=   - Require a composer package"
	@echo "  composer-remove PKG=    - Remove a composer package"
	@echo "  composer-validate       - Validate composer.json"

install: composer-install theme-npm-install build-theme

install-site:
	@bash scripts/install.sh

theme-npm-install:
	@cd "$(THEME_DIR)" && npm install

build-theme:
	@cd "$(THEME_DIR)" && npm run build

build-css:
	@cd "$(THEME_DIR)" && npm run build:css

theme-check:
	@cd "$(THEME_DIR)" && npm run build:theme-yaml:check && npm run build:bnppre-icons:check

drush-cr:
	@"$(DRUSH)" cr -y

drush-status:
	@"$(DRUSH)" status

drush-uli:
	@"$(DRUSH)" uli

drush-updb:
	@"$(DRUSH)" updb -y

drush-cim:
	@"$(DRUSH)" cim -y

drush-cex:
	@"$(DRUSH)" cex -y

composer-install:
	@"$(COMPOSER)" install

composer-update:
	@"$(COMPOSER)" update

composer-require:
	@test -n "$(PKG)" || (echo "Usage: make composer-require PKG=vendor/package" && exit 1)
	@"$(COMPOSER)" require "$(PKG)"

composer-remove:
	@test -n "$(PKG)" || (echo "Usage: make composer-remove PKG=vendor/package" && exit 1)
	@"$(COMPOSER)" remove "$(PKG)"

composer-validate:
	@"$(COMPOSER)" validate

default-content-export:
	@test -n "$(ENTITY_TYPE)" || (echo "Usage: make default-content-export ENTITY_TYPE=<entity_type> ENTITY_ID=<id> [OUT=<file.yml>]" && exit 1)
	@test -n "$(ENTITY_ID)" || (echo "Usage: make default-content-export ENTITY_TYPE=<entity_type> ENTITY_ID=<id> [OUT=<file.yml>]" && exit 1)
	@if [ -n "$(OUT)" ]; then \
		"$(DRUSH)" default-content:export "$(ENTITY_TYPE)" "$(ENTITY_ID)" --file="$(OUT)"; \
	else \
		"$(DRUSH)" default-content:export "$(ENTITY_TYPE)" "$(ENTITY_ID)"; \
	fi

default-content-export-references:
	@test -n "$(ENTITY_TYPE)" || (echo "Usage: make default-content-export-references ENTITY_TYPE=<entity_type> [ENTITY_ID=<id>] [FOLDER=<path>]" && exit 1)
	@if [ -n "$(ENTITY_ID)" ] && [ -n "$(FOLDER)" ]; then \
		"$(DRUSH)" default-content:export-references "$(ENTITY_TYPE)" "$(ENTITY_ID)" --folder="$(FOLDER)"; \
	elif [ -n "$(ENTITY_ID)" ]; then \
		"$(DRUSH)" default-content:export-references "$(ENTITY_TYPE)" "$(ENTITY_ID)"; \
	elif [ -n "$(FOLDER)" ]; then \
		"$(DRUSH)" default-content:export-references "$(ENTITY_TYPE)" --folder="$(FOLDER)"; \
	else \
		"$(DRUSH)" default-content:export-references "$(ENTITY_TYPE)"; \
	fi

default-content-export-module:
	@test -n "$(MODULE)" || (echo "Usage: make default-content-export-module MODULE=<module_name>" && exit 1)
	@"$(DRUSH)" default-content:export-module "$(MODULE)"

default-content-export-module-references:
	@test -n "$(MODULE)" || (echo "Usage: make default-content-export-module-references MODULE=<module_name>" && exit 1)
	@"$(DRUSH)" default-content:export-module-with-references "$(MODULE)"

default-content-export-main-menu:
	@bash scripts/export-main-menu-default-content.sh
