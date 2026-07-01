<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Attribute;

use Drupal\Component\Plugin\Attribute\Plugin;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines a FeatureType attribute for plugin discovery.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class FeatureType extends Plugin {

  /**
   * Constructs a FeatureType attribute.
   *
   * @param string $id
   *   The plugin ID.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|null $label
   *   The human-readable name of the feature type.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|null $description
   *   A brief description of the feature type.
   * @param array<string, mixed> $payload_schema
   *   The JSON schema for the payload.
   * @param class-string|null $deriver
   *   (optional) The deriver class.
   */
  public function __construct(
    string $id,
    public readonly ?TranslatableMarkup $label = NULL,
    public readonly ?TranslatableMarkup $description = NULL,
    public readonly array $payload_schema = [],
    ?string $deriver = NULL,
  ) {
    parent::__construct($id, $deriver);
  }

}
