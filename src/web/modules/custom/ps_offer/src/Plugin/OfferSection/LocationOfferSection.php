<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\OfferSection;

use Drupal\ps_core\Plugin\OfferSection\OfferSectionBase;

/**
 * Location section on offer detail pages.
 *
 * @OfferSection(
 *   id = "location",
 *   admin_label = @Translation("Location"),
 *   label = @Translation("Location"),
 *   default_icon = "bnp_custom:pin-map",
 *   weight = 30,
 * )
 */
final class LocationOfferSection extends OfferSectionBase {}
