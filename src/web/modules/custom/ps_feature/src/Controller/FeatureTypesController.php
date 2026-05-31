<?php

namespace Drupal\ps_feature\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ps_feature\Service\FeatureTypeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for displaying available feature types.
 */
class FeatureTypesController extends ControllerBase {

  /**
   * The feature type manager.
   *
   * @var \Drupal\ps_feature\Service\FeatureTypeManager
   */
  protected FeatureTypeManager $featureTypeManager;

  /**
   * Constructs a new FeatureTypesController.
   *
   * @param \Drupal\ps_feature\Service\FeatureTypeManager $feature_type_manager
   *   The feature type manager.
   */
  public function __construct(FeatureTypeManager $feature_type_manager) {
    $this->featureTypeManager = $feature_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_feature.type_manager')
    );
  }

  /**
   * Displays the list of available feature types.
   *
   * @return array
   *   A render array.
   */
  public function listTypes(): array {
    // Dynamically load all types from the plugin manager.
    $definitions = $this->featureTypeManager->getDefinitions();

    $rows = [];
    foreach ($definitions as $type_id => $definition) {
      $label = (string) $definition['label'];
      $description = (string) ($definition['description'] ?? '');

      $rows[] = [
        'type' => [
          'data' => [
            '#markup' => '<strong>' . $label . '</strong><br><code>' . $type_id . '</code>',
          ],
        ],
        'description' => $description,
      ];
    }

    $build = [];

    $build['description'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('Feature types are defined by developers and determine how data is stored and displayed. This list is read-only.'),
      '#attributes' => [
        'class' => ['description'],
      ],
    ];

    $build['table'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Type'),
        $this->t('Description and usage'),
      ],
      '#rows' => $rows,
      '#empty' => $this->t('No feature type defined.'),
      '#attributes' => [
        'class' => ['feature-types-list'],
      ],
    ];

    $build['note'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('<em>Technical note</em>: these types are implemented through the plugin system in <code>src/Plugin/FeatureType/</code>.'),
      '#attributes' => [
        'class' => ['help-text'],
      ],
    ];

    return $build;
  }

}
