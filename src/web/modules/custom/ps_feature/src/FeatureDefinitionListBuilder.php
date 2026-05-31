<?php

namespace Drupal\ps_feature;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\ps_feature\Service\FeatureTypeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a listing of Feature Definitions.
 */
class FeatureDefinitionListBuilder extends ConfigEntityListBuilder {

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
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type): static {
    $instance = parent::createInstance($container, $entity_type);
    $instance->featureTypeManager = $container->get('ps_feature.type_manager');
    $instance->groupStorage = $container->get('entity_type.manager')->getStorage('fb_feature_group');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['label'] = $this->t('Feature name');
    $header['id'] = $this->t('Identifier');
    $header['group'] = $this->t('Group');
    $header['type_driver'] = $this->t('Data type');
    $header['asset_types'] = $this->t('Required asset types');
    $header['weight'] = $this->t('Weight');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\ps_feature\Entity\FeatureDefinition $entity */
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    
    // Display group label instead of ID.
    $group = $this->groupStorage->load($entity->getGroup());
    $row['group'] = $group ? $group->label() : $entity->getGroup();
    
    // Display type driver label.
    $types = $this->featureTypeManager->getAllTypes();
    $row['type_driver'] = $types[$entity->getTypeDriver()] ?? $entity->getTypeDriver();
    
    // Display asset types as badges.
    $required_asset_types = $entity->getRequiredAssetTypes();
    if (empty($required_asset_types)) {
      $row['asset_types'] = new FormattableMarkup('<span class="badge badge--info">@text</span>', [
        '@text' => $this->t('All types'),
      ]);
    }
    else {
      $badges = [];
      foreach ($required_asset_types as $code) {
        $badges[] = '<span class="badge badge--warning">' . $this->getAssetTypeLabel($code) . '</span>';
      }
      $row['asset_types'] = new FormattableMarkup(implode(' ', $badges), []);
    }
    
    $row['weight'] = $entity->getWeight();
    
    // Add parent's operations.
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
      '#value' => $this->t('Feature definitions represent the catalogue of all available features used to describe offers (e.g., Total surface, Parking included, Air conditioning). Each definition belongs to a group and has a specific data type.'),
      '#weight' => -10,
    ];
    
    if (empty($this->load())) {
      $build['empty'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('No feature definitions found. Create one to start enriching your offers.'),
        '#weight' => 10,
      ];
    }
    
    // Add CSS for badges.
    $build['#attached']['library'][] = 'ps_feature/admin';
    
    return $build;
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
