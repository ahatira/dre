<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\OfferSection;

use Drupal\ps_core\Plugin\OfferSection\OfferSectionBase;

/**
 * Description section on offer detail pages.
 *
 * @OfferSection(
 *   id = "description",
 *   admin_label = @Translation("Description"),
 *   label = @Translation("Description"),
 *   default_icon = "",
 *   weight = 5,
 * )
 */
final class DescriptionOfferSection extends OfferSectionBase {}
