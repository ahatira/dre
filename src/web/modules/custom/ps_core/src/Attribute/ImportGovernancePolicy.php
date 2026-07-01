<?php

declare(strict_types=1);

namespace Drupal\ps_core\Attribute;

use Drupal\Component\Plugin\Attribute\Plugin;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines an ImportGovernancePolicy attribute for plugin discovery.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class ImportGovernancePolicy extends Plugin {

  /**
   * Constructs an ImportGovernancePolicy attribute.
   *
   * @param string $id
   *   The plugin ID.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|null $admin_label
   *   Admin label shown in the governance hub.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|null $description
   *   Short description for the governance hub.
   * @param string $settings_route
   *   Route name of the domain governance settings form.
   * @param int $weight
   *   Sort weight in the governance hub.
   * @param class-string|null $deriver
   *   (optional) The deriver class.
   */
  public function __construct(
    string $id,
    public readonly ?TranslatableMarkup $admin_label = NULL,
    public readonly ?TranslatableMarkup $description = NULL,
    public readonly string $settings_route = '',
    public readonly int $weight = 0,
    ?string $deriver = NULL,
  ) {
    parent::__construct($id, $deriver);
  }

}
