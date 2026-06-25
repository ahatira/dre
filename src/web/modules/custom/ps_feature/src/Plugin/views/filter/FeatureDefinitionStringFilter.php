<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\views\filter;

use Drupal\views\Attribute\ViewsFilter;

/**
 * String filter for feature definition entity fields.
 */
#[ViewsFilter('ps_feature_definition_string')]
final class FeatureDefinitionStringFilter extends FeatureDefinitionStringFilterBase {
}
