<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Ensures the Layout Builder overrides field is translatable.
 */
final class HomepageLayoutFieldConfigurer {

  public function __construct(
    private readonly EntityFieldManagerInterface $entityFieldManager,
  ) {}

  /**
   * Marks layout_builder__layout as translatable on page nodes.
   */
  public function ensureTranslatable(): bool {
    $fieldDefinitions = $this->entityFieldManager->getFieldDefinitions('node', 'page');
    if (!isset($fieldDefinitions['layout_builder__layout'])) {
      return FALSE;
    }

    $storage = FieldStorageConfig::load('node.layout_builder__layout');
    if (!$storage instanceof FieldStorageConfig) {
      return FALSE;
    }

    $field = FieldConfig::load('node.page.layout_builder__layout');
    if (!$field instanceof FieldConfig) {
      return FALSE;
    }

    $updated = FALSE;
    if (!$storage->isTranslatable()) {
      $storage->setTranslatable(TRUE);
      $storage->save();
      $updated = TRUE;
    }

    if (!$field->isTranslatable()) {
      $field->setTranslatable(TRUE);
      $field->save();
      $updated = TRUE;
    }

    if ($updated) {
      $this->entityFieldManager->clearCachedFieldDefinitions();
    }

    return $updated;
  }

}
