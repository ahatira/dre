<?php

namespace Drupal\ps_feature\Plugin;

/**
 * Interface for Feature Type plugins.
 */
interface FeatureTypeInterface {

  /**
   * Validates the payload structure.
   *
   * @param array $payload
   *   The payload to validate.
   *
   * @return array
   *   Array of error messages. Empty if valid.
   */
  public function validate(array $payload): array;

  /**
   * Normalizes the payload.
   *
   * Ensures correct data types and cleans up the structure.
   *
   * @param array $payload
   *   The payload to normalize.
   *
   * @return array
   *   The normalized payload.
   */
  public function normalize(array $payload): array;

  /**
   * Gets the plugin ID.
   *
   * @return string
   *   The plugin ID.
   */
  public function getPluginId(): string;

  /**
   * Gets the plugin label.
   *
   * @return string
   *   The plugin label.
   */
  public function getLabel(): string;

  /**
   * Gets the plugin description.
   *
   * @return string
   *   The plugin description.
   */
  public function getDescription(): string;

  /**
   * Builds form elements for editing the payload.
   *
   * @param array $current_payload
   *   The current payload values (empty array for new items).
   *
   * @return array
   *   Form API elements for the payload.
   */
  public function buildPayloadForm(array $current_payload = []): array;

  /**
   * Formats the payload for display.
   *
   * @param array $payload
   *   The payload to format.
   *
   * @return string
   *   The formatted string for display.
   */
  public function format(array $payload): string;

}
