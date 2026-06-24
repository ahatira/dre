<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\FileStorage;
use Drupal\Core\Config\StorageInterface;
use Psr\Log\LoggerInterface;

/**
 * Bootstraps versioned Config Split entities and per-country site overrides.
 *
 * Reads split definitions from src/config/env/splits and override YAML from
 * src/config/env/sites/{country}. Used during shell install until full CMI
 * import (install-from-conf) is available.
 */
final class SiteConfigSplitInstaller {

  /**
   * Partial config overrides stored under config/env/sites/{country}/.
   *
   * @var string[]
   */
  private const PARTIAL_CONFIG_NAMES = [
    'field.field.node.offer.field_address',
  ];

  /**
   * Install YAML used as merge base for partial overrides.
   *
   * @var array<string, string>
   */
  private const PARTIAL_CONFIG_BASE = [
    'field.field.node.offer.field_address' => '../web/modules/custom/ps_offer/config/install/field.field.node.offer.field_address.yml',
  ];

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
   * Imports split entities and applies per-country config from env/sites.
   */
  public function applyForCountry(string $country): void {
    $this->importSplitEntitiesForCountry($country);
    $this->applySiteConfigFromFolder($country);
  }

  /**
   * Imports Config Split entity definitions only (no folder overrides).
   */
  public function importSplitEntitiesForCountry(string $country): void {
    $country = strtolower(trim($country));
    if ($country === '') {
      throw new \InvalidArgumentException('Country code is required.');
    }

    $this->importSplitEntity('site_' . $country);
    $this->importSplitEntity('local');
  }

  /**
   * Applies YAML overrides from config/env/sites/{country}/.
   */
  public function applySiteConfigFromFolder(string $country): void {
    $country = strtolower(trim($country));
    if ($country === '') {
      throw new \InvalidArgumentException('Country code is required.');
    }

    $source = DRUPAL_ROOT . '/../config/env/sites/' . $country;
    if (!is_dir($source)) {
      throw new \RuntimeException(sprintf('Missing site config directory: %s', $source));
    }

    $fileStorage = new FileStorage($source);
    $imported = 0;
    foreach ($fileStorage->listAll() as $configName) {
      $data = $fileStorage->read($configName);
      if (!is_array($data)) {
        continue;
      }
      unset($data['_comment']);
      $data = $this->resolveConfigData($configName, $data);
      $this->configFactory->getEditable($configName)->setData($data)->save(TRUE);
      $imported++;
    }

    $this->logger->notice('ps_core: applied {count} site config item(s) for country {country}.', [
      'count' => $imported,
      'country' => $country,
    ]);
  }

  /**
   * Resolves partial overrides against active or install config.
   *
   * @param array<string, mixed> $override
   *   Override data from config/env/sites.
   *
   * @return array<string, mixed>
   *   Full config data ready for import.
   */
  private function resolveConfigData(string $configName, array $override): array {
    if (!in_array($configName, self::PARTIAL_CONFIG_NAMES, TRUE)) {
      return $override;
    }

    $base = $this->configFactory->get($configName)->getRawData();
    if ($base === [] && isset(self::PARTIAL_CONFIG_BASE[$configName])) {
      $basePath = DRUPAL_ROOT . '/' . self::PARTIAL_CONFIG_BASE[$configName];
      if (is_file($basePath)) {
        $parsed = \Symfony\Component\Yaml\Yaml::parseFile($basePath);
        $base = is_array($parsed) ? $parsed : [];
      }
    }

    return array_replace_recursive($base, $override);
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

}
