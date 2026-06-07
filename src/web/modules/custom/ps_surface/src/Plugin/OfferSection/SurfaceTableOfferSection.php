<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Plugin\OfferSection;

use Drupal\ps_core\Plugin\OfferSection\OfferSectionBase;

/**
 * Surface division table section on offer detail pages.
 *
 * @OfferSection(
 *   id = "surface_table",
 *   admin_label = @Translation("Surface table"),
 *   label = @Translation("Surface table"),
 *   default_icon = "bnp_custom:surface",
 *   weight = 25,
 * )
 */
final class SurfaceTableOfferSection extends OfferSectionBase {}
