# ps_offer Module Structure

This document describes the directory and file structure of the ps_offer module.

## Directory Layout

```
ps_offer/
├── config/
│   ├── install/                          # Installation configuration
│   │   ├── core.entity_form_display.node.offer.default.yml
│   │   ├── core.entity_view_display.node.offer.default.yml
│   │   ├── field.field.node.offer.external_id.yml
│   │   ├── field.storage.node.external_id.yml
│   │   ├── node.type.offer.yml
│   │   ├── ps_offer.settings.yml
│   │   └── README_FIELDS.txt
│   ├── optional/                         # Optional configurations
│   └── schema/                           # Configuration schema
│       └── ps_offer.schema.yml
├── src/
│   ├── Commands/                         # Drush commands
│   │   └── OfferCommands.php
│   ├── EventSubscriber/                  # Event subscribers
│   │   └── OfferEventSubscriber.php
│   ├── Form/                             # Forms (entity, settings)
│   │   ├── OfferSettingsForm.php
│   │   └── (future: OfferForm.php)
│   ├── Hook/                             # OOP hooks (Drupal 11.1+)
│   │   ├── OfferFieldHooks.php
│   │   └── OfferHooks.php
│   ├── Service/                          # Business services
│   │   ├── OfferManager.php
│   │   └── OfferManagerInterface.php
│   └── (future: Entity/, Plugin/)
├── templates/                            # Twig templates (future)
├── tests/
│   └── src/
│       ├── Kernel/                       # Kernel tests
│       │   └── OfferModuleTest.php
│       ├── Unit/                         # Unit tests
│       │   └── OfferManagerTest.php
│       └── Functional/                   # Functional tests (future)
├── .gitignore
├── CHANGELOG.md
├── ps_offer.help.yml
├── ps_offer.info.yml                     # Module metadata
├── ps_offer.links.menu.yml               # Menu links
├── ps_offer.module                       # Module hooks (minimal)
├── ps_offer.permissions.yml              # Permissions
├── ps_offer.routing.yml                  # Routes
├── ps_offer.services.yml                 # Service definitions
└── README.md                             # Module documentation
```

## File Descriptions

### Configuration Files

- **node.type.offer.yml** — Define the offer node type with display settings
- **field.storage.node.external_id.yml** — Field storage for external_id
- **field.field.node.offer.external_id.yml** — Field instance configuration
- **core.entity_form_display.node.offer.default.yml** — Default form display settings
- **core.entity_view_display.node.offer.default.yml** — Default view display settings
- **ps_offer.settings.yml** — Default module settings

### Module Files

- **ps_offer.info.yml** — Module metadata, version, dependencies
- **ps_offer.module** — Hooks (minimal, procedural)
- **ps_offer.routing.yml** — URL routes and controllers
- **ps_offer.permissions.yml** — Custom permissions
- **ps_offer.links.menu.yml** — Admin menu links
- **ps_offer.services.yml** — Service definitions and DI

### Source Code

#### Commands/
- **OfferCommands.php** — Drush integration
  - `ps:offer-list` — List all offers
  - `ps:offer-show {id}` — Display offer details

#### EventSubscriber/
- **OfferEventSubscriber.php** — Entity lifecycle event handling

#### Form/
- **OfferSettingsForm.php** — Module settings configuration form

#### Hook/
- **OfferFieldHooks.php** — Field creation hooks (install time)
- **OfferHooks.php** — OOP hooks for offer nodes (node_presave, node_insert, etc.)

#### Service/
- **OfferManager.php** — Service class for CRUD operations
- **OfferManagerInterface.php** — Service interface contract

### Tests

- **tests/src/Unit/OfferManagerTest.php** — Unit tests for OfferManager
- **tests/src/Kernel/OfferModuleTest.php** — Integration tests

### Documentation

- **README.md** — Complete module documentation
- **CHANGELOG.md** — Version history and changes
- **config/install/README_FIELDS.txt** — Future field creation notes

## Dependencies

### Required Modules
- ps (Foundation)
- ps_dictionary (Dictionary management)
- ps_agent (Agent entities)
- ps_price (Price field type)
- ps_features (Feature field type)
- ps_diagnostic (Diagnostic field type)
- ps_division (Division entity)
- ps_surface (Surface field type)
- search_api (Search integration)

### Core Modules
- node (Node entities)
- field (Field API)
- text (Text fields)
- options (Select/radio options)
- user (User accounts)
- media (Media entities)
- geofield (Geo location fields)

## Key Concepts

### Offer Node Type
- Machine name: `offer`
- Label: "Offer"
- Basis: Content entity (via Node module)
- Base fields: 8 core fields + 7 custom fields (prices, features, diagnostics, etc.)

### Service Layer
- **OfferManager** — Main service for offer CRUD
- Injected via container: `$this->container->get('ps_offer.manager')`

### OOP Hooks (Drupal 11.1+)
- Hooks declared via `#[Hook(...)]` attributes
- Auto-discovered and injected
- No procedural hook declarations needed

### Configuration
- Module settings at `/admin/ps/config/offers`
- Offer admin list at `/admin/content/offers`
- Form display configurable at `/admin/structure/types/manage/offer/form-display`
- View display configurable at `/admin/structure/types/manage/offer/display`

## Installation

```bash
# Enable module
drush en ps_offer -y

# Clear cache
drush cr

# Run tests (optional)
drush test:run --group=ps_offer
```

## Future Enhancements

- [ ] Additional custom fields (media, pricing, features)
- [ ] Views integration and default views
- [ ] Search API integration
- [ ] Import integration with ps_import
- [ ] Custom reporting and analytics
- [ ] API endpoints for external systems
- [ ] Workflow states (draft, pending, published, archived)
- [ ] Revision support
- [ ] Translation support
