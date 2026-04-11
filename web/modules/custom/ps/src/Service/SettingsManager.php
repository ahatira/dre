<?php

declare(strict_types=1);

namespace Drupal\ps\Service;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Manages PropertySearch settings with dot notation.
 */
final class SettingsManager implements SettingsManagerInterface {

  /**
   * Constructor.
   */
  public function __construct(
    protected readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function get(string $key, mixed $default = NULL): mixed {
    $config = $this->configFactory->get('ps.settings');
    $parts = explode('.', $key);
    $value = $config->getRawData();

    foreach ($parts as $part) {
      if (is_array($value) && isset($value[$part])) {
        $value = $value[$part];
      }
      else {
        return $default;
      }
    }

    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function set(string $key, mixed $value): void {
    $config = $this->configFactory->getEditable('ps.settings');
    $parts = explode('.', $key);
    $lastKey = array_pop($parts);

    $data = $config->getRawData();
    $reference = &$data;

    foreach ($parts as $part) {
      if (!isset($reference[$part])) {
        $reference[$part] = [];
      }
      $reference = &$reference[$part];
    }

    $reference[$lastKey] = $value;
    $config->setData($data)->save();
  }

  /**
   * {@inheritdoc}
   */
  public function getAll(): array {
    return $this->configFactory->get('ps.settings')->getRawData();
  }

}
