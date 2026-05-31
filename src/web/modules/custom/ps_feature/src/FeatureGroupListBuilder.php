<?php

namespace Drupal\ps_feature;

use Drupal\Core\Config\Entity\DraggableListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a draggable listing of Feature Groups.
 */
class FeatureGroupListBuilder extends DraggableListBuilder {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'feature_group_admin_overview_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['label'] = $this->t('Group name');
    $header['asset_types'] = $this->t('Asset types');
    $header['manage_definitions'] = $this->t('Manage definitions');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\ps_feature\Entity\FeatureGroup $entity */
    // Build label first (will be shown with drag handle).
    $row['label'] = $entity->label();
    
    // Display asset type badges.
    $asset_types = $entity->getAssetTypes();
    if (empty($asset_types)) {
      $row['asset_types'] = [
        '#markup' => '<span class="badge badge--info">' . $this->t('All types') . '</span>',
      ];
    }
    else {
      $badges = [];
      foreach ($asset_types as $code) {
        $badges[] = '<span class="badge badge--primary">' . $this->getAssetTypeLabel($code) . '</span>';
      }
      $row['asset_types'] = [
        '#markup' => implode(' ', $badges),
      ];
    }
    
    // Add link to manage definitions for this group.
    $row['manage_definitions'] = [
      '#type' => 'link',
      '#title' => $this->t('Manage order'),
      '#url' => Url::fromRoute('entity.fb_feature_definition.collection_by_group', [
        'feature_group' => $entity->id(),
      ]),
      '#attributes' => [
        'class' => ['button', 'button--small', 'button--action'],
      ],
    ];
    
    // Add parent's weight field and operations.
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    $build = parent::render();
    
    // Add helpful description for non-technical users.
    $build['description'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('Feature groups organize offer features into logical categories (e.g., Amenities, Technical features). Each group can be associated with one or more asset types.'),
      '#weight' => -10,
    ];
    
    $build['help'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('Drag rows to reorder groups.'),
      '#attributes' => ['class' => ['description']],
      '#weight' => -9,
    ];
    
    if (empty($this->load())) {
      $build['empty'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('No feature groups defined. Create one to start organizing your features.'),
        '#weight' => 10,
      ];
    }
    
    // Add CSS for badges.
    $build['#attached']['library'][] = 'ps_feature/admin';
    
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    parent::submitForm($form, $form_state);
    
    $this->messenger()->addStatus($this->t('Group order has been updated.'));
  }

  /**
   * Gets the human-readable label for an asset type code.
   *
   * @param string $code
   *   The asset type code.
   *
   * @return string
   *   The translated label.
   */
  protected function getAssetTypeLabel(string $code): string {
    $labels = [
      'BUR' => $this->t('Office'),
      'COW' => $this->t('Coworking'),
      'ENT' => $this->t('Warehouse/Logistics'),
      'ACT' => $this->t('Activity unit'),
      'COM' => $this->t('Retail unit'),
      'TER' => $this->t('Land'),
    ];
    
    return $labels[$code] ?? $code;
  }

}
