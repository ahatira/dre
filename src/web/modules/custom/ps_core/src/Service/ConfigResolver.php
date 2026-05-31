<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Config\ConfigFactoryInterface;

final class ConfigResolver {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  public function get(string $configName, string $key, mixed $default = NULL): mixed {
    $value = $this->configFactory->get($configName)->get($key);
    return $value ?? $default;
  }

}
