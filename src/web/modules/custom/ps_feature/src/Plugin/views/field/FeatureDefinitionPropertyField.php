<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\views\field;

use Drupal\views\Attribute\ViewsField;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Renders a scalar property from a feature definition entity.
 */
#[ViewsField('ps_feature_definition_property')]
final class FeatureDefinitionPropertyField extends FieldPluginBase {

  use FeatureDefinitionFieldTrait;

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values): string {
    $definition = $this->getDefinition($values);
    if ($definition === NULL) {
      return '';
    }

    $property = $this->field;
    $value = match ($property) {
      'id' => $definition->id(),
      'label' => $definition->label(),
      'code' => $definition->get('code') ?? '',
      'weight' => (string) $definition->getWeight(),
      'status' => $definition->status()
        ? (string) $this->t('Active')
        : (string) $this->t('Inactive'),
      'type_locked' => $definition->isTypeLocked()
        ? (string) $this->t('Yes')
        : (string) $this->t('No'),
      'expose_as_filter' => $definition->isExposeAsFilter()
        ? (string) $this->t('Exposed')
        : (string) $this->t('Hidden'),
      default => (string) ($definition->get($property) ?? ''),
    };

    return $value;
  }

}
