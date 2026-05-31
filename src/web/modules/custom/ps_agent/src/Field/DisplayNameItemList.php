<?php

namespace Drupal\ps_agent\Field;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;

/**
 * Computed field for Agent display name (first_name + last_name).
 */
class DisplayNameItemList extends FieldItemList {

  use ComputedItemListTrait;

  /**
   * {@inheritdoc}
   */
  protected function computeValue() {
    /** @var \Drupal\ps_agent\Entity\Agent $entity */
    $entity = $this->getEntity();

    $first_name = $entity->get('first_name')->value ?? '';
    $last_name = $entity->get('last_name')->value ?? '';

    // Concatenate with a space.
    $display_name = trim($first_name . ' ' . $last_name);

    $this->list[0] = $this->createItem(0, $display_name);
  }

}
