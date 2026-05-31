<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Hook;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Hook implementations for local tasks.
 */
final class LocalTaskAlter implements ContainerInjectionInterface {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static();
  }

  /**
   * Implements hook_menu_local_tasks_alter().
   */
  #[Hook('menu_local_tasks_alter')]
  public function menuLocalTasksAlter(array &$data, string $route_name): void {
    // Simplify translation tab titles for DictionaryType entity.
    if (isset($data['tabs'][0]['config_translation.local_tasks:entity.ps_dictionary_type.config_translation_overview'])) {
      $data['tabs'][0]['config_translation.local_tasks:entity.ps_dictionary_type.config_translation_overview']['#link']['title'] = new TranslatableMarkup('Translate');
    }

    // Simplify translation tab titles for DictionaryEntry entity.
    if (isset($data['tabs'][0]['config_translation.local_tasks:entity.ps_dictionary_entry.config_translation_overview'])) {
      $data['tabs'][0]['config_translation.local_tasks:entity.ps_dictionary_entry.config_translation_overview']['#link']['title'] = new TranslatableMarkup('Translate');
    }
  }

}
