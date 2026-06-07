<?php

declare(strict_types=1);

namespace Drupal\ps_core_test_offer_section\Plugin\OfferSection;

use Drupal\ps_core\Plugin\OfferSection\OfferSectionBase;

/**
 * Test section with icon for kernel/unit coverage.
 *
 * @OfferSection(
 *   id = "test_icon",
 *   admin_label = @Translation("Test icon section"),
 *   label = @Translation("Test icon default"),
 *   default_icon = "bnp_custom:test-icon",
 *   weight = 10,
 * )
 */
final class IconOfferSection extends OfferSectionBase {}
