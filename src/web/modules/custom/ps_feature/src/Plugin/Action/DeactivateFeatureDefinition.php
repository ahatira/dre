<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Action\Attribute\Action;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_feature\Entity\FeatureDefinition;

/**
 * Deactivates a feature definition.
 */
#[Action(
  id: 'feature_definition_deactivate_action',
  label: new TranslatableMarkup('Deactivate feature definition'),
  type: 'fb_feature_definition',
)]
final class DeactivateFeatureDefinition extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL): void {
    if ($entity instanceof FeatureDefinition) {
      $entity->set('status', FALSE)->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, ?AccountInterface $account = NULL, $return_as_object = FALSE) {
    assert($object instanceof FeatureDefinition);
    return $object->access('update', $account, $return_as_object);
  }

}
