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
 * Config Split entities are read from src/config/env/splits (versioned).
 */
final class SiteLanguageNegotiationInstaller {

  /**
   * File storage for versioned Config Split entity YAML.
   */
  private readonly FileStorage $splitStorage;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly StorageInterface $configStorage,
    private readonly LoggerInterface $logger,
  ) {
    $this->splitStorage = new FileStorage(DRUPAL_ROOT . '/../config/env/splits');
  }

  /**
   * Imports the language split entity and applies language.negotiation.
   */
  public function applyForCountry(string $country): void {
    $country = strtolower(trim($country));
    if ($country === '') {
      throw new \InvalidArgumentException('Country code is required.');
    }

    $this->importSplitEntity('language_' . $country);
    $this->importSplitEntity('local');
    $this->applyLanguageNegotiation($country);
  }

  /**
   * Copies a Config Split entity from config/env/splits into active storage.
   */
  private function importSplitEntity(string $splitId): void {
    $configName = 'config_split.config_split.' . $splitId;
    if ($this->configStorage->exists($configName)) {
      return;
    }

    if (!$this->splitStorage->exists($configName)) {
      throw new \RuntimeException(sprintf('Missing split config: %s', $configName));
    }

    $data = $this->splitStorage->read($configName);
    if (!is_array($data)) {
      throw new \RuntimeException(sprintf('Invalid split config: %s', $configName));
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
