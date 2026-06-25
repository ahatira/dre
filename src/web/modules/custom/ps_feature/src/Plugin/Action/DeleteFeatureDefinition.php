<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Action\Attribute\Action;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_feature\Entity\FeatureDefinition;

/**
 * Deletes a feature definition.
 */
#[Action(
  id: 'feature_definition_delete_action',
  label: new TranslatableMarkup('Delete feature definition'),
  type: 'fb_feature_definition',
)]
final class DeleteFeatureDefinition extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL): void {
    if ($entity instanceof FeatureDefinition) {
      $entity->delete();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, ?AccountInterface $account = NULL, $return_as_object = FALSE) {
    assert($object instanceof FeatureDefinition);
    return $object->access('delete', $account, $return_as_object);
  }

}
