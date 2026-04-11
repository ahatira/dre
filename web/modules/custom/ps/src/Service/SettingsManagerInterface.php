<?php

declare(strict_types=1);

namespace Drupal\ps\Service;

/**
 * Interface for settings management with dot notation.
 */
interface SettingsManagerInterface {

  /**
   * Get a setting value by key with dot notation support.
   *
   * @param string $key
   *   The setting key (e.g., 'validation.strictMode').
   * @param mixed $default
   *   Default value if not found.
   *
   * @return mixed
   *   The setting value.
   */
  public function get(string $key, mixed $default = NULL): mixed;

  /**
   * Set a setting value.
   *
   * @param string $key
   *   The setting key.
   * @param mixed $value
   *   The value to set.
   *
   * @return void
   *   This method does not return a value.
   */
  public function set(string $key, mixed $value): void;

  /**
   * Get all settings.
   *
   * @return array<string, mixed>
   *   Array of all settings.
   */
  public function getAll(): array;

}
