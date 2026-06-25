<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\views\filter;

use Drupal\ps_feature\Service\FeatureDefinitionSource;
use Drupal\views\Attribute\ViewsFilter;

/**
 * Filter feature definitions by catalogue source.
 */
#[ViewsFilter('ps_feature_definition_source')]
final class FeatureDefinitionSourceFilter extends FeatureDefinitionInOperatorFilterBase {

  /**
   * {@inheritdoc}
   */
  protected function buildValueOptions(): array {
    return [
      FeatureDefinitionSource::BO => (string) $this->t('Back office'),
      FeatureDefinitionSource::XML => (string) $this->t('CRM XML'),
      FeatureDefinitionSource::LEGACY => (string) $this->t('Legacy'),
    ];
  }

}
