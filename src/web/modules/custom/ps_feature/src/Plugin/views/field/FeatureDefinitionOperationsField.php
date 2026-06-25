<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\views\field;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\views\Attribute\ViewsField;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Renders edit/delete operations for feature definitions.
 */
#[ViewsField('ps_feature_definition_operations')]
final class FeatureDefinitionOperationsField extends FieldPluginBase {

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
  public function query(): void {
    // Operations are resolved at render time from the loaded entity.
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $definition = $this->getDefinition($values);
    if ($definition === NULL) {
      return '';
    }

    $cacheability = new CacheableMetadata();
    $operations = $this->entityTypeManager
      ->getListBuilder('fb_feature_definition')
      ->getOperations($definition, $cacheability);

    $build = [
      '#type' => 'operations',
      '#links' => $operations,
      '#attached' => [
        'library' => ['core/drupal.dialog.ajax'],
      ],
    ];
    $cacheability->applyTo($build);
    return $build;
  }

}
