<?php

declare(strict_types=1);

namespace Drupal\ps_core\Plugin\OfferSection;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Offer detail section plugin — provides default heading label and icon.
 */
interface OfferSectionInterface extends PluginInspectionInterface {

  /**
   * Returns the default translatable section heading.
   */
  public function getDefaultLabel(): string;

  /**
   * Returns the default UI Icon pack:id value.
   */
  public function getDefaultIcon(): string;

  /**
   * Returns the admin-facing section name.
   */
  public function getAdminLabel(): string;

}
