<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\EventSubscriber;

use Drupal\Core\Config\ConfigEvents;
use Drupal\Core\Config\ConfigImporterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber for dictionary cache invalidation.
 *
 * Listens to config import/export events and clears dictionary cache
 * when dictionary types or entries are modified.
 */
final class DictionaryEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      ConfigEvents::SAVE => 'onConfigSave',
      ConfigEvents::DELETE => 'onConfigDelete',
      ConfigEvents::RENAME => 'onConfigRename',
    ];
  }

  /**
   * Clears cache when dictionary config is saved.
   *
   * @param \Drupal\Core\Config\ConfigImporterEvent $event
   *   The config event.
   */
  public function onConfigSave(ConfigImporterEvent $event): void {
    $this->clearCacheForDictionaryConfig($event);
  }

  /**
   * Clears cache when dictionary config is deleted.
   *
   * @param \Drupal\Core\Config\ConfigImporterEvent $event
   *   The config event.
   */
  public function onConfigDelete(ConfigImporterEvent $event): void {
    $this->clearCacheForDictionaryConfig($event);
  }

  /**
   * Clears cache when dictionary config is renamed.
   *
   * @param \Drupal\Core\Config\ConfigImporterEvent $event
   *   The config event.
   */
  public function onConfigRename(ConfigImporterEvent $event): void {
    $this->clearCacheForDictionaryConfig($event);
  }

  /**
   * Clear cache for dictionary config changes.
   *
   * @param \Drupal\Core\Config\ConfigImporterEvent $event
   *   The config event.
   */
  private function clearCacheForDictionaryConfig(ConfigImporterEvent $event): void {
    $config_name = $event->getConfigName();

    // Check if this is a dictionary type or entry config.
    if (str_starts_with($config_name, 'ps_dictionary.type.') ||
        str_starts_with($config_name, 'ps_dictionary.entry.')) {
      // Extract dictionary type from config name.
      // ps_dictionary.type.property_type.yml → property_type
      // ps_dictionary.entry.property_type.ACT.yml → property_type
      $parts = explode('.', $config_name);

      if (count($parts) >= 3) {
        $type_id = $parts[2];
        $manager = \Drupal::service('ps_dictionary.manager');
        if ($manager) {
          $manager->clearCache($type_id);
        }
      }
    }
  }

}
