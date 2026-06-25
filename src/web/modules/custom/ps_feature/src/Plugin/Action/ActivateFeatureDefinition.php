<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Action\Attribute\Action;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_feature\Entity\FeatureDefinition;

/**
 * Activates a feature definition.
 */
#[Action(
  id: 'feature_definition_activate_action',
  label: new TranslatableMarkup('Activate feature definition'),
  type: 'fb_feature_definition',
)]
final class ActivateFeatureDefinition extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL): void {
    if ($entity instanceof FeatureDefinition) {
      $entity->set('status', TRUE)->save();
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
