<?php

namespace Drupal\ps_feature\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ps_feature\Entity\FeatureGroup;
use Drupal\ps_feature\FeatureDefinitionByGroupListBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for Feature Definition by Group listing.
 */
class FeatureDefinitionByGroupController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static();
  }

  /**
   * Lists Feature Definitions for a specific group.
   *
   * @param \Drupal\ps_feature\Entity\FeatureGroup $feature_group
   *   The feature group.
   *
   * @return array
   *   A render array.
   */
  public function listByGroup(FeatureGroup $feature_group): array {
    $entity_type = $this->entityTypeManager()->getDefinition('fb_feature_definition');
    
    /** @var \Drupal\ps_feature\FeatureDefinitionByGroupListBuilder $list_builder */
    $list_builder = $this->entityTypeManager()
      ->getListBuilder('fb_feature_definition');
    
    // If it's not our custom builder, create one manually.
    if (!$list_builder instanceof FeatureDefinitionByGroupListBuilder) {
      $list_builder = new FeatureDefinitionByGroupListBuilder(
        $entity_type,
        $this->entityTypeManager()->getStorage('fb_feature_definition')
      );
      $list_builder = FeatureDefinitionByGroupListBuilder::createInstance(
        \Drupal::getContainer(),
        $entity_type
      );
    }
    
    $list_builder->setGroupId($feature_group->id());
    
    return $list_builder->render();
  }

  /**
   * Gets the title for the page.
   *
   * @param \Drupal\ps_feature\Entity\FeatureGroup $feature_group
   *   The feature group.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The page title.
   */
  public function getTitle(FeatureGroup $feature_group) {
    return $this->t('Manage definitions: @group', [
      '@group' => $feature_group->label(),
    ]);
  }

}
