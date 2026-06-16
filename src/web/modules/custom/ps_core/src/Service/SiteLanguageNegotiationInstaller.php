<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\FileStorage;
use Drupal\Core\Config\StorageInterface;
use Psr\Log\LoggerInterface;

/**
 * Applies per-country language.negotiation for multisite greenfield installs.
 *
 * Replaces shell partial config:import from src/config/env/languages/{country}.
 * Config Split entities remain in sync CMI; negotiation YAML in config/env.
 */
final class SiteLanguageNegotiationInstaller {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly StorageInterface $configStorage,
    private readonly StorageInterface $syncStorage,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Imports the language split entity and applies language.negotiation.
   */
  public function applyForCountry(string $country): void {
    $country = strtolower(trim($country));
    if ($country === '') {
      throw new \InvalidArgumentException('Country code is required.');
    }

    $this->importLanguageSplitEntity($country);
    $this->applyLanguageNegotiation($country);
  }

  /**
   * Copies the per-country language Config Split entity from sync storage.
   */
  private function importLanguageSplitEntity(string $country): void {
    $configName = 'config_split.config_split.language_' . $country;
    if ($this->configStorage->exists($configName)) {
      return;
    }

    if (!$this->syncStorage->exists($configName)) {
      throw new \RuntimeException(sprintf('Missing sync config: %s', $configName));
    }

    $data = $this->syncStorage->read($configName);
    if (!is_array($data)) {
      throw new \RuntimeException(sprintf('Invalid sync config: %s', $configName));
    }

    $this->configStorage->write($configName, $data);
    $this->logger->notice('ps_core: imported config split entity {name}.', [
      'name' => $configName,
    ]);
  }

  /**
   * Applies language.negotiation from config/env/languages/{country}.
   */
  private function applyLanguageNegotiation(string $country): void {
    $source = DRUPAL_ROOT . '/../config/env/languages/' . $country;
    if (!is_dir($source)) {
      throw new \RuntimeException(sprintf('Missing language negotiation directory: %s', $source));
    }

    $fileStorage = new FileStorage($source);
    $data = $fileStorage->read('language.negotiation');
    if (!is_array($data)) {
      throw new \RuntimeException(sprintf('Missing language.negotiation in %s', $source));
    }

    $this->configFactory->getEditable('language.negotiation')->setData($data)->save(TRUE);
    $this->logger->notice('ps_core: applied language.negotiation for country {country}.', [
      'country' => $country,
    ]);
  }

}
