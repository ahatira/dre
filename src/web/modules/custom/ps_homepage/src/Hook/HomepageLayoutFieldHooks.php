<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Hook;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\field\FieldConfigInterface;
use Drupal\layout_builder\Plugin\SectionStorage\OverridesSectionStorage;

/**
 * Keeps the page layout field translatable for layout_builder_at asymmetric LB.
 */
final class HomepageLayoutFieldHooks {

  public function __construct(
    private readonly ModuleHandlerInterface $moduleHandler,
  ) {}

  /**
   * Implements hook_field_config_presave().
   *
   * Core Layout Builder marks the override field non-translatable when enabled;
   * layout_builder_at only forces translatable storage. Both must be true so
   * each node translation stores its own layout rows.
   */
  #[Hook('field_config_presave')]
  public function fieldConfigPresave(FieldConfigInterface $field_config): void {
    if (!$this->moduleHandler->moduleExists('layout_builder_at')) {
      return;
    }

    if ($field_config->getTargetEntityTypeId() !== 'node'
      || $field_config->getTargetBundle() !== 'page'
      || $field_config->getName() !== OverridesSectionStorage::FIELD_NAME) {
      return;
    }

    $field_config->setTranslatable(TRUE);
  }

}
