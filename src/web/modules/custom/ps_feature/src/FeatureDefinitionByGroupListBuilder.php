<?php

namespace Drupal\ps_feature;

use Drupal\Core\Config\Entity\DraggableListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\ps_feature\Service\FeatureTypeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a draggable listing of Feature Definitions filtered by group.
 */
class FeatureDefinitionByGroupListBuilder extends DraggableListBuilder {

  /**
   * The feature type manager.
   *
   * @var \Drupal\ps_feature\Service\FeatureTypeManager
   */
  protected FeatureTypeManager $featureTypeManager;

  /**
   * The feature group storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected EntityStorageInterface $groupStorage;

  /**
   * The group ID to filter by.
   *
   * @var string|null
   */
  protected ?string $groupId = NULL;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type): static {
    $instance = parent::createInstance($container, $entity_type);
    $instance->featureTypeManager = $container->get('ps_feature.type_manager');
    $instance->groupStorage = $container->get('entity_type.manager')->getStorage('fb_feature_group');
    return $instance;
  }

  /**
   * Sets the group ID filter.
   *
   * @param string $group_id
   *   The group ID to filter by.
   */
  public function setGroupId(string $group_id): void {
    $this->groupId = $group_id;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntityIds(): array {
    $query = $this->getStorage()->getQuery()
      ->accessCheck(TRUE)
      ->sort($this->entityType->getKey('weight'), 'ASC')
      ->sort($this->entityType->getKey('id'), 'ASC');

    // Filter by group if set.
    if ($this->groupId) {
      $query->condition('group', $this->groupId);
    }

    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'feature_definition_by_group_admin_overview_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['label'] = $this->t('Feature name');
    $header['type_driver'] = $this->t('Data type');
    $header['asset_types'] = $this->t('Required asset types');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\ps_feature\Entity\FeatureDefinition $entity */
    $row['label'] = $entity->label();

    // Display type driver label.
    $types = $this->featureTypeManager->getAllTypes();
    $type_label = $types[$entity->getTypeDriver()] ?? $entity->getTypeDriver();
    $row['type_driver'] = [
      '#plain_text' => $type_label,
    ];

    // Display asset types as badges.
    $required_asset_types = $entity->getRequiredAssetTypes();
    if (empty($required_asset_types)) {
      $row['asset_types'] = [
        '#markup' => '<span class="badge badge--info">' . $this->t('All types') . '</span>',
      ];
    }
    else {
      $badges = [];
      foreach ($required_asset_types as $code) {
        $badges[] = '<span class="badge badge--warning">' . $this->getAssetTypeLabel($code) . '</span>';
      }
      $row['asset_types'] = [
        '#markup' => implode(' ', $badges),
      ];
    }

    // Add parent's weight field and operations.
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    $build = parent::render();

    // Add breadcrumb info about current group.
    if ($this->groupId) {
      $group = $this->groupStorage->load($this->groupId);
      if ($group) {
        $build['group_info'] = [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('Manage order of feature definitions for the group: <strong>@group</strong>', [
            '@group' => $group->label(),
          ]),
          '#weight' => -10,
        ];

        // Add link back to groups list.
        $build['back_link'] = [
          '#type' => 'link',
          '#title' => $this->t('Back to groups'),
          '#url' => Url::fromRoute('entity.fb_feature_group.collection'),
          '#attributes' => ['class' => ['button', 'button--small']],
          '#weight' => -9,
        ];
      }
    }

    $build['help'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('Drag rows to reorder features in this group.'),
      '#attributes' => ['class' => ['description']],
      '#weight' => -8,
    ];

    if (empty($this->load())) {
      $build['empty'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('No definitions in this group.'),
        '#weight' => 0,
      ];
    }

    $build['#attached']['library'][] = 'ps_feature/admin';

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    parent::submitForm($form, $form_state);

    $this->messenger()->addStatus($this->t('The order of features has been updated.'));
  }

  /**
   * Gets the human-readable label for an asset type.
   *
   * @param string $code
   *   The asset type code.
   *
   * @return string
   *   The label.
   */
  protected function getAssetTypeLabel(string $code): string {
    // TODO: Load from actual asset type config when available.
    $labels = [
      'BUR' => 'Office',
      'COW' => 'Coworking',
      'LDG' => 'Local',
    ];
    return $labels[$code] ?? $code;
  }

}
