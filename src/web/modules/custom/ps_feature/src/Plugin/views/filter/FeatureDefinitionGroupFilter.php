<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\views\filter;

use Drupal\views\Attribute\ViewsFilter;

/**
 * Filter feature definitions by group.
 */
#[ViewsFilter('ps_feature_definition_group')]
final class FeatureDefinitionGroupFilter extends FeatureDefinitionInOperatorFilterBase {

  /**
   * {@inheritdoc}
   */
  protected function buildValueOptions(): array {
    $options = [];
    $groups = $this->entityTypeManager->getStorage('fb_feature_group')->loadMultiple();
    uasort($groups, static fn($a, $b) => $a->getWeight() <=> $b->getWeight());
    foreach ($groups as $group) {
      if (!$group->status()) {
        continue;
      }
      $options[$group->id()] = $group->label();
    }
    return $options;
  }

}
