# Offer Module Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- **Core Offer Node Type**: New `offer` content type for property offers
- **Base Fields** (8 core fields):
  - `external_id` — CRM unique identifier
  - `reference` — Internal reference number
  - `property_type_code` — Property type code (dict reference)
  - `transaction_type_codes` — Transaction type codes (array)
  - `address` — Property address
  - `postal_code` — Postal code
  - `city_label` — City name
  - `description` — Property description (text_long)

- **OfferManager Service**: Core service for CRUD operations
  - `createOffer()` — Create unsaved offer node
  - `getOfferByExternalId()` — Load by CRM ID
  - `publishOffer()` — Publish offer node
  - `archiveOffer()` — Archive offer node

- **OOP Hooks** (Drupal 11.1+ style):
  - `OfferHooks::nodePresave()` — Pre-save logic
  - `OfferHooks::nodeInsert()` — New offer creation
  - `OfferHooks::nodeUpdate()` — Offer updates
  - `OfferHooks::nodeDelete()` — Offer deletion

- **Event Subscribers**:
  - `OfferEventSubscriber` — Entity presave event handling

- **Admin Interfaces**:
  - `/admin/content/offers` — Offer collection management
  - `/admin/ps/config/offers` — Offer settings

- **Drush Commands**:
  - `ps:offer-list` — List all offers
  - `ps:offer-show {id}` — Display offer details

- **Permissions**:
  - `create offer content`
  - `edit own/any offer content`
  - `delete own/any offer content`
  - `administer offer content`

- **Configuration**:
  - Offer settings (divisible_default, auto_publish, results_per_page)
  - Form display configuration
  - View display configuration

- **Tests**:
  - Unit tests for OfferManager service
  - Kernel tests for module installation
  - Field creation verification tests

### Changed

### Deprecated

### Removed

### Fixed

### Security

## [1.0.0] — 2026-01-15

### Added

- Initial module setup and structure
- Foundation service architecture
- Documentation and README
