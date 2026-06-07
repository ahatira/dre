<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Plugin\OfferSection;

use Drupal\ps_core\Plugin\OfferSection\OfferSectionBase;

/**
 * Energy and diagnostics section on offer detail pages.
 *
 * @OfferSection(
 *   id = "energy",
 *   admin_label = @Translation("Energy & diagnostics"),
 *   label = @Translation("Energy & diagnostics"),
 *   default_icon = "bnp_custom:energy-cons",
 *   weight = 20,
 * )
 */
final class EnergyOfferSection extends OfferSectionBase {}
