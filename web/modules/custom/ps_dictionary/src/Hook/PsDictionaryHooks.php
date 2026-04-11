<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Hook;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_dictionary\Entity\DictionaryEntryInterface;
use Drupal\ps_dictionary\Entity\DictionaryTypeInterface;

/**
 * Hook implementations for ps_dictionary.
 */
class PsDictionaryHooks {

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme(): array {
    return [
      'dictionary_entry_detail' => [
        'variables' => [
          'entry' => NULL,
        ],
        'template' => 'dictionary-entry-detail',
      ],
    ];
  }

  /**
   * Invalidates cache when a dictionary type or entry is saved.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity being saved.
   */
  #[Hook('entity_insert')]
  #[Hook('entity_update')]
  public function clearCacheOnChange(EntityInterface $entity): void {
    if ($entity instanceof DictionaryTypeInterface) {
      \Drupal::service('ps_dictionary.manager')->clearCache($entity->id());
      $this->notifyDictionaryChange('updated', $entity->label());
    }
    elseif ($entity instanceof DictionaryEntryInterface) {
      \Drupal::service('ps_dictionary.manager')->clearCache($entity->getType());
      $this->notifyDictionaryChange('updated', $entity->label());
    }
  }

  /**
   * Invalidates cache when a dictionary entry is deleted.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity being deleted.
   */
  #[Hook('entity_delete')]
  public function clearCacheOnDelete(EntityInterface $entity): void {
    if ($entity instanceof DictionaryEntryInterface) {
      \Drupal::service('ps_dictionary.manager')->clearCache($entity->getType());
      $this->notifyDictionaryChange('deleted', $entity->label());
    }
  }

  /**
   * Send notification when dictionary changes.
   *
   * @param string $action
   *   The action (updated, deleted, etc).
   * @param string $label
   *   The dictionary or entry label.
   */
  private function notifyDictionaryChange(string $action, string $label): void {
    // Try to use ps.notification service if available.
    try {
      $notification_service = \Drupal::service('ps.notification');
      if ($notification_service && method_exists($notification_service, 'send')) {
        $message = sprintf('Dictionary item "%s" was %s', $label, $action);
        // Send as log notification (no recipient needed).
        $notification_service->send(
          'admin@property-search.local',
          'Dictionary Updated',
          $message,
          ['channel' => 'log']
        );
      }
    }
    catch (\Throwable $e) {
      // Silently fail - notifications are not critical and should not block operations.
      // Log at debug level only to avoid noise.
      if (\Drupal::hasService('logger.factory')) {
        \Drupal::logger('ps_dictionary')->debug(
          'Failed to send dictionary notification: @error',
          ['@error' => $e->getMessage()]
        );
      }
    }
  }

  /**
   * Implements hook_system_breadcrumb_alter().
   *
   * @param \Drupal\Core\Breadcrumb\Breadcrumb $breadcrumb
   *   The breadcrumb object.
   * @param mixed $route_match
   *   The route match.
   * @param array<string, mixed> $context
   *   The breadcrumb context.
   */
  #[Hook('system_breadcrumb_alter')]
  public function systemBreadcrumbAlter(Breadcrumb &$breadcrumb, mixed $route_match, array $context): void {
    $route_name = $route_match->getRouteName();

    // On entry edit/delete/view pages, add type name then entry list.
    if (str_contains($route_name, 'entity.ps_dictionary_entry.')) {
      $entry = $route_match->getParameter('ps_dictionary_entry');
      if ($entry && method_exists($entry, 'getType')) {
        $type_id = $entry->getType();

        // Load the type entity to get its label.
        $type_storage = \Drupal::entityTypeManager()->getStorage('ps_dictionary_type');
        $type = $type_storage->load($type_id);

        if ($type) {
          // Get existing links.
          $links = $breadcrumb->getLinks();
          if (!empty($links)) {
            // Remove the last link (current page).
            $last_link = array_pop($links);

            // Add Dictionary Types link.
            $types_collection_url = Url::fromRoute('entity.ps_dictionary_type.collection');
            $types_collection_link = Link::fromTextAndUrl($this->t('Dictionary Types'), $types_collection_url);
            $links[] = $types_collection_link;

            // Add type label link (to entry list).
            $type_url = Url::fromRoute('entity.ps_dictionary_type.entries', [
              'ps_dictionary_type' => $type_id,
            ]);
            $type_link = Link::fromTextAndUrl($type->label(), $type_url);
            $links[] = $type_link;

            // Add back the current page link.
            $links[] = $last_link;

            // Rebuild breadcrumb.
            $new_breadcrumb = new Breadcrumb();
            foreach ($links as $link) {
              $new_breadcrumb->addLink($link);
            }
            $new_breadcrumb->addCacheContexts($breadcrumb->getCacheContexts());
            $new_breadcrumb->addCacheTags($breadcrumb->getCacheTags());
            $new_breadcrumb->mergeCacheMaxAge($breadcrumb->getCacheMaxAge());

            // Replace the breadcrumb.
            $breadcrumb = $new_breadcrumb;
          }
        }
      }
    }
  }

  /**
   * Implements hook_form_alter().
   */
  #[Hook('form_alter')]
  public function formAlter(array &$form, FormStateInterface $form_state, string $form_id): void {
    if ($form_id === 'ps_dictionary_entry_form' || $form_id === 'ps_dictionary_type_form') {
      $form['#prefix'] = $this->renderLocalTasks();
    }
  }

  /**
   * Render local tasks for current route.
   */
  private function renderLocalTasks(): string {
    $route_match = \Drupal::routeMatch();
    $local_task_manager = \Drupal::service('plugin.manager.menu.local_task');

    try {
      $tasks = $local_task_manager->getLocalTasks($route_match->getRouteName(), 0);
      if (empty($tasks)) {
        return '';
      }

      $markup = '<ul class="tabs primary" role="tablist">';
      foreach ($tasks['tabs'] ?? [] as $task) {
        $markup .= '<li role="presentation"><a href="#" class="tabs__link">' . htmlspecialchars($task->getTitle()) . '</a></li>';
      }
      $markup .= '</ul>';

      return $markup;
    }
    catch (\Exception $e) {
      return '';
    }
  }

  /**
   * Implements hook_views_data().
   *
   * Exposes ps_dictionary fields to Views with custom filter handler.
   */
  #[Hook('views_data')]
  public function viewsData(): array {
    $data = [];

    // Get all field storages of type ps_dictionary.
    $field_storages = \Drupal::entityTypeManager()
      ->getStorage('field_storage_config')
      ->loadByProperties(['type' => 'ps_dictionary']);

    foreach ($field_storages as $field_storage) {
      $field_name = $field_storage->getName();
      $entity_type = $field_storage->getTargetEntityTypeId();
      $dictionary_type = $field_storage->getSetting('dictionary_type');

      // Get the base table for this entity type.
      $entity_type_definition = \Drupal::entityTypeManager()->getDefinition($entity_type);
      $base_table = $entity_type_definition->getDataTable() ?: $entity_type_definition->getBaseTable();

      if (!$base_table) {
        continue;
      }

      $table_name = $entity_type . '__' . $field_name;

      // Override the filter handler for this field.
      if (isset($data[$table_name][$field_name . '_value']['filter'])) {
        $data[$table_name][$field_name . '_value']['filter']['id'] = 'ps_dictionary_filter';
        $data[$table_name][$field_name . '_value']['filter']['dictionary_type'] = $dictionary_type;
      }
      else {
        // If the field table doesn't exist yet in Views data,
        // we'll add it when Views processes field data.
        // This is a fallback to ensure our filter is registered.
        $data[$table_name][$field_name . '_value']['filter'] = [
          'id' => 'ps_dictionary_filter',
          'dictionary_type' => $dictionary_type,
          'field_name' => $field_name . '_value',
          'title' => new TranslatableMarkup('@field_name (dictionary)', ['@field_name' => $field_name]),
          'help' => new TranslatableMarkup('Filter by dictionary values.'),
        ];
      }
    }

    return $data;
  }

  /**
   * Implements hook_views_data_alter().
   *
   * Alters Views data to use ps_dictionary filter for ps_dictionary fields.
   */
  #[Hook('views_data_alter')]
  public function viewsDataAlter(array &$data): void {
    // Get all field storages of type ps_dictionary.
    $field_storages = \Drupal::entityTypeManager()
      ->getStorage('field_storage_config')
      ->loadByProperties(['type' => 'ps_dictionary']);

    foreach ($field_storages as $field_storage) {
      $field_name = $field_storage->getName();
      $entity_type = $field_storage->getTargetEntityTypeId();
      $dictionary_type = $field_storage->getSetting('dictionary_type');

      // The field data table.
      $table_name = $entity_type . '__' . $field_name;

      // Override the filter plugin for the value column.
      if (isset($data[$table_name][$field_name . '_value'])) {
        $data[$table_name][$field_name . '_value']['filter']['id'] = 'ps_dictionary_filter';
        $data[$table_name][$field_name . '_value']['filter']['dictionary_type'] = $dictionary_type;
      }
    }
  }

  /**
   * Translate a string.
   *
   * @param string $string
   *   The string to translate.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The translated string.
   */
  private function t(string $string): TranslatableMarkup {
    return new TranslatableMarkup($string);
  }

}
