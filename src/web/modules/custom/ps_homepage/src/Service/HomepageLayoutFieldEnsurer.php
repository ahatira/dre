<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\field\FieldConfigInterface;
use Drupal\layout_builder\Plugin\SectionStorage\OverridesSectionStorage;

/**
 * Ensures the page layout override field stays translatable with layout_builder_at.
 */
final class HomepageLayoutFieldEnsurer {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly EntityFieldManagerInterface $entityFieldManager,
    private readonly ModuleHandlerInterface $moduleHandler,
  ) {}

  public function ensurePageLayoutFieldTranslatable(): void {
    if (!$this->moduleHandler->moduleExists('layout_builder_at')) {
      return;
    }

    $field = $this->entityTypeManager->getStorage('field_config')->load(
      'node.page.' . OverridesSectionStorage::FIELD_NAME,
    );
    if (!$field instanceof FieldConfigInterface || $field->isTranslatable()) {
      return;
    }

    $field->setTranslatable(TRUE);
    $field->save();
    $this->entityFieldManager->clearCachedFieldDefinitions();
  }

}
