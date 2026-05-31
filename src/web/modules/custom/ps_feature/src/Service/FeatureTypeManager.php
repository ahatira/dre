<?php

namespace Drupal\ps_feature\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\ps_feature\Annotation\FeatureType;
use Drupal\ps_feature\Plugin\FeatureTypeInterface;

/**
 * Feature Type plugin manager.
 */
class FeatureTypeManager extends DefaultPluginManager {

  /**
   * Constructs a FeatureTypeManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(
    \Traversable $namespaces,
    CacheBackendInterface $cache_backend,
    ModuleHandlerInterface $module_handler
  ) {
    parent::__construct(
      'Plugin/FeatureType',
      $namespaces,
      $module_handler,
      FeatureTypeInterface::class,
      FeatureType::class
    );

    $this->alterInfo('ps_feature_type_info');
    $this->setCacheBackend($cache_backend, 'ps_feature_type_plugins');
  }

  /**
   * Gets a plugin instance.
   *
   * @param string $type_id
   *   The plugin ID.
   *
   * @return \Drupal\ps_feature\Plugin\FeatureTypeInterface
   *   The plugin instance.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function getPlugin(string $type_id): FeatureTypeInterface {
    return $this->createInstance($type_id);
  }

  /**
   * Validates a payload for a given type.
   *
   * @param string $type_id
   *   The feature type ID.
   * @param array $payload
   *   The payload to validate.
   *
   * @return array
   *   Array of error messages. Empty if valid.
   */
  public function validate(string $type_id, array $payload): array {
    try {
      $plugin = $this->getPlugin($type_id);
      return $plugin->validate($payload);
    }
    catch (\Exception $e) {
      return ["Invalid feature type: {$type_id}"];
    }
  }

  /**
   * Normalizes a payload for a given type.
   *
   * @param string $type_id
   *   The feature type ID.
   * @param array $payload
   *   The payload to normalize.
   *
   * @return array
   *   The normalized payload.
   */
  public function normalize(string $type_id, array $payload): array {
    try {
      $plugin = $this->getPlugin($type_id);
      return $plugin->normalize($payload);
    }
    catch (\Exception $e) {
      return $payload;
    }
  }

  /**
   * Gets all available feature types.
   *
   * @return array
   *   Keyed array of type_id => label.
   */
  public function getAllTypes(): array {
    $types = [];
    foreach ($this->getDefinitions() as $plugin_id => $definition) {
      $types[$plugin_id] = (string) $definition['label'];
    }
    return $types;
  }

}
