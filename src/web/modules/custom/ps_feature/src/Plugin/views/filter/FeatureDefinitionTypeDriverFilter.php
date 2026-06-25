<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\views\filter;

use Drupal\ps_feature\Service\FeatureTypeManager;
use Drupal\views\Attribute\ViewsFilter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Filter feature definitions by data type driver.
 */
#[ViewsFilter('ps_feature_definition_type_driver')]
final class FeatureDefinitionTypeDriverFilter extends FeatureDefinitionInOperatorFilterBase {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    $entityTypeManager,
    private readonly FeatureTypeManager $featureTypeManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entityTypeManager);
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
      $container->get('ps_feature.type_manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function buildValueOptions(): array {
    return $this->featureTypeManager->getAllTypes();
  }

}
