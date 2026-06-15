<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Service;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Loads promo card preset configuration.
 */
final class PresetRepository {

  /**
   * Constructs a PresetRepository.
   */
  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Returns all preset definitions keyed by ID.
   *
   * @return array<string, array<string, mixed>>
   */
  public function getAll(): array {
    $presets = [];
    foreach ($this->configFactory->listAll('views_promo_card.preset.') as $name) {
      $config = $this->configFactory->get($name)->get();
      if (!is_array($config) || empty($config['id'])) {
        continue;
      }
      $presets[(string) $config['id']] = $config;
    }
    return $presets;
  }

  /**
   * Loads a single preset by ID.
   */
  public function get(string $preset_id): ?array {
    $config = $this->configFactory->get('views_promo_card.preset.' . $preset_id);
    if ($config->isNew()) {
      return NULL;
    }
    $data = $config->get();
    return is_array($data) ? $data : NULL;
  }

}
