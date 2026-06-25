<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\views\field;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\views\Attribute\ViewsField;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Displays the human-readable feature group label.
 */
#[ViewsField('ps_feature_definition_group')]
final class FeatureDefinitionGroupField extends FieldPluginBase {

  use FeatureDefinitionFieldTrait;

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values): string {
    $definition = $this->getDefinition($values);
    if ($definition === NULL) {
      return '';
    }
    $group = $this->entityTypeManager->getStorage('fb_feature_group')->load($definition->getGroup());
    return $group ? $group->label() : $definition->getGroup();
  }

}
