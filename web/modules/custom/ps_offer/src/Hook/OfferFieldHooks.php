<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Hook;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hooks for ps_offer field creation and management.
 */
final class OfferFieldHooks {

  /**
   * Implements hook_entity_bundle_field_info().
   *
   * Provides field definitions for the offer node type.
   */
  #[Hook('entity_bundle_field_info')]
  public function entityBundleFieldInfo(EntityTypeInterface $entityType, string $bundle): array {
    if ($entityType->id() === 'node' && $bundle === 'offer') {
      // The offer bundle is fully shipped through config/install.
    }

    return [];
  }

  /**
   * Keeps backward compatibility for any legacy installer call paths.
   */
  public static function createOfferFields(): void {
    if (function_exists('ps_offer_ensure_offer_structure')) {
      ps_offer_ensure_offer_structure();
    }
  }

}
