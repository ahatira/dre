<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\views\field;

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\views\Attribute\ViewsField;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Renders a linked feature definition label.
 */
#[ViewsField('ps_feature_definition_label')]
final class FeatureDefinitionLabelField extends FieldPluginBase {

  use FeatureDefinitionFieldTrait;

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $definition = $this->getDefinition($values);
    if ($definition === NULL) {
      return '';
    }
    return Link::fromTextAndUrl($definition->label(), Url::fromRoute('entity.fb_feature_definition.edit_form', [
      'fb_feature_definition' => $definition->id(),
    ]))->toString();
  }

}
