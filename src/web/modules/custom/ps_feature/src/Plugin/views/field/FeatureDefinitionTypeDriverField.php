<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\views\field;

use Drupal\ps_feature\Service\FeatureTypeManager;
use Drupal\views\Attribute\ViewsField;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Displays the human-readable feature type driver label.
 */
#[ViewsField('ps_feature_definition_type_driver')]
final class FeatureDefinitionTypeDriverField extends FieldPluginBase {

  use FeatureDefinitionFieldTrait;

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly FeatureTypeManager $featureTypeManager,
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
      $container->get('ps_feature.type_manager'),
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
    $types = $this->featureTypeManager->getAllTypes();
    $driver = $definition->getTypeDriver();
    return (string) ($types[$driver] ?? $driver);
  }

}
