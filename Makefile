SHELL := /usr/bin/env bash
.DEFAULT_GOAL := help

PROJECT_ROOT := $(CURDIR)
DRUSH := $(PROJECT_ROOT)/vendor/bin/drush
COMPOSER := composer
THEME_DIR := $(PROJECT_ROOT)/web/themes/custom/ui_suite_bnppre

.PHONY: help install install-site build-theme build-css theme-check theme-npm-install \
	drush-cr drush-status drush-uli drush-updb drush-cim drush-cex \
	composer-install composer-update composer-require composer-remove composer-validate

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
