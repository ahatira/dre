# ps_offer — Real Estate Offer Module

Drupal 11 module for managing real estate offers with comprehensive property data, pricing, features, diagnostics, and agent information.

## Overview

`ps_offer` provides a **Node type "offer"** bundled with 15+ specialized fields for representing real estate offers. Each offer aggregates:

- **Property metadata** (type, transaction type, location)
- **Pricing** (via `ps_price` field type)
- **Features/Equipment** (via `ps_features`)
- **Building diagnostics** (via `ps_diagnostic`)
- **Subdivisions/Surfaces** (via `ps_division`)
- **Agent/Contact info** (references ps_agent)
- **Media** (photos, plans, videos)

## Architecture

### Node Type: "Offer"

**Machine Name**: `offer`
**Label**: Offer
**Description**: Real estate offer with prices, features, diagnostics, and location

### Base Fields (15+)

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `title` | text | ✅ | Address + property type label |
| `external_id` | string | ❌ | CRM system identifier (readonly) |
| `reference` | string | ❌ | Internal reference code |
| `property_type_code` | string | ✅ | Property type (ACT, BUR) from ps_dictionary |
| `transaction_type_codes` | text | ✅ | Transaction types (LOC) from ps_dictionary |
| `address` | text | ✅ | Full address |
| `postal_code` | string | ✅ | Postal/ZIP code |
| `city_label` | string | ✅ | City name |
| `geofield` | geofield | ❌ | Geographic coordinates |
| `description` | text_long | ❌ | Detailed offer description |
| `prices` | ps_price[] | ✅ | Rental/sale prices (value, currency, unit, period) |
| `features` | ps_feature_value[] | ❌ | Equipment, services, building condition |
| `diagnostics` | ps_diagnostic[] | ❌ | Energy ratings (DPE, GES) |
| `divisions` | entity_reference[] | ❌ | References to ps_division (floors/lots) |
| `media_photos` | media_reference[] | ❌ | Property photos |
| `media_plans` | media_reference[] | ❌ | Property plans/blueprints |
| `media_videos` | media_reference[] | ❌ | Property videos |
| `agents` | entity_reference[] | ✅ | References to ps_agent (main contact) |
| `status` | boolean | ✅ | Published/Archived toggle |
| Auto: `created`, `changed` | timestamp | 🤖 | Timestamps |

## Dependencies

- **Drupal Core**: node, text, options, field, user, media, geofield
- **ps** — Foundation services
- **ps_dictionary** — Business code validation (property_type, transaction_type)
- **ps_agent** — Agent entity references
- **ps_price** — Price field type & pricing logic
- **ps_features** — Equipment/features field type
- **ps_diagnostic** — Diagnostics field type (DPE, GES)
- **ps_division** — Subdivisions/lots entity
- **ps_surface** — Surface field type
- **search_api** — Offer indexing

## Services

### OfferManager

Core service for CRUD operations and business logic.

```php
$manager = \Drupal::service('ps_offer.manager');

// Create offer
$offer = $manager->createOffer(['title' => 'Office Space']);

// Load by external ID
$offer = $manager->getOfferByExternalId('CRM-12345');

// Publish/Archive
$manager->publishOffer($offer);
$manager->archiveOffer($offer);
```

## Usage Examples

### Create an Offer

```php
use Drupal\node\Entity\Node;

$offer = Node::create([
  'type' => 'offer',
  'title' => 'Office Space at Edificio ARA',
  'property_type_code' => 'BUR',
  'transaction_type_codes' => 'LOC',
  'address' => '37 Cl. Hermanos García Noblejas',
  'postal_code' => '28037',
  'city_label' => 'Madrid',
  'prices' => [
    [
      'value' => 20000,
      'currency' => 'EUR',
      'unit' => 'cm2',
      'period' => 'ann',
    ],
  ],
  'agents' => [1], // Reference to agent node
  'status' => 1,
]);
$offer->save();
```

### Query Offers

```php
$storage = \Drupal::entityTypeManager()->getStorage('node');
$offers = $storage->loadByProperties([
  'type' => 'offer',
  'status' => 1,
]);
```

## Configuration

### Admin Routes

- `/admin/content/offers` — Offer list view
- `/admin/content/offers/{id}/edit` — Edit offer
- `/admin/content/offers/add` — Create offer

### Search API

Offers are indexed via Search API (Solr) with:
- Full text search on title, description, address
- Faceting on property_type, transaction_type, price ranges, surface ranges
- Filtering on status, location

## Events

(Future) PSR-14 events dispatched:
- `OfferPublishedEvent` — After offer publication
- `OfferArchivedEvent` — After offer archival

## Testing

```bash
# Unit tests
vendor/bin/phpunit web/modules/custom/ps_offer/tests/src/Unit

# Kernel tests
vendor/bin/phpunit web/modules/custom/ps_offer/tests/src/Kernel
```

## Installation

```bash
# Enable module
drush en ps_offer -y

# Clear cache
drush cr

# Create initial offer (if fixtures available)
drush migrate:import ps_offer
```

---

**Module**: ps_offer
**Type**: Feature
**Layer**: Business
**Version**: 1.0.0
**Drupal**: 11.3.2+
**PHP**: 8.3+
