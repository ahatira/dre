<?php

declare(strict_types=1);

namespace Drupal\ps_email\Plugin\EmailFooterBlock;

/**
 * Defines an email footer block plugin.
 */
interface EmailFooterBlockInterface {

  /**
   * Returns the admin label.
   */
  public function label(): string;

  /**
   * Renders table-safe HTML for the email footer zone.
   *
   * @param array<string, mixed> $settings
   *   Component settings from footer layout config.
   * @param string|null $langcode
   *   Optional language code.
   */
  public function build(array $settings, ?string $langcode = NULL): string;

}
