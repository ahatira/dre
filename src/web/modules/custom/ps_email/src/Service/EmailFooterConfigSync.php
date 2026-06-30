<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\FileStorage;
use Symfony\Component\Yaml\Yaml;

/**
 * Imports ps_email.footer from per-country CMI (config/env/sites/{code}/).
 */
final class EmailFooterConfigSync {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EmailFooterCountryResolver $countryResolver,
  ) {}

  /**
   * Applies ps_email.footer YAML for a country into active storage.
   *
   * @return bool
   *   TRUE when config was imported.
   */
  public function syncFromCmi(?string $country = NULL): bool {
    $country = strtolower(trim($country ?? $this->countryResolver->resolveCountryCode()));
    $folder = DRUPAL_ROOT . '/../config/env/sites/' . $country;
    $storage = new FileStorage($folder);
    $configName = 'ps_email.footer';

    if (!$storage->exists($configName)) {
      throw new \RuntimeException(sprintf('Missing CMI file: config/env/sites/%s/%s.yml', $country, $configName));
    }

    $data = $storage->read($configName);
    if (!is_array($data)) {
      throw new \RuntimeException(sprintf('Invalid CMI file: config/env/sites/%s/%s.yml', $country, $configName));
    }

    unset($data['_comment']);
    $this->configFactory->getEditable($configName)->setData($data)->save(TRUE);

    return TRUE;
  }

  /**
   * Returns parsed footer config from CMI without saving.
   *
   * @return array<string, mixed>
   *   Footer config data.
   */
  public function readFromCmi(?string $country = NULL): array {
    $country = strtolower(trim($country ?? $this->countryResolver->resolveCountryCode()));
    $path = DRUPAL_ROOT . '/../config/env/sites/' . $country . '/ps_email.footer.yml';
    if (!is_readable($path)) {
      throw new \RuntimeException(sprintf('Missing CMI file: %s', $path));
    }

    $data = Yaml::parseFile($path);
    if (!is_array($data)) {
      throw new \RuntimeException(sprintf('Invalid CMI file: %s', $path));
    }

    return $data;
  }

}
