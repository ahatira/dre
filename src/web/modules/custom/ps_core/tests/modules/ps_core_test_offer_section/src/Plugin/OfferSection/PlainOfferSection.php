<?php

declare(strict_types=1);

namespace Drupal\ps_core_test_offer_section\Plugin\OfferSection;

use Drupal\ps_core\Plugin\OfferSection\OfferSectionBase;

/**
 * Test section without icon for kernel/unit coverage.
 *
 * @OfferSection(
 *   id = "test_plain",
 *   admin_label = @Translation("Test plain section"),
 *   label = @Translation("Test plain default"),
 *   default_icon = "",
 *   weight = 5,
 * )
 */
final class PlainOfferSection extends OfferSectionBase {}
