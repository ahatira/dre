<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Component\Utility\SortArray;
use Drupal\Core\Access\AccessResultAllowed;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Prepare local task link for nav component.
 */
class PreprocessMenuLocalTasks {

  /**
   * The possible local task types.
   *
   * @var string[]
   */
  public array $localTaskTypes = [
    'primary',
    'secondary',
  ];

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_menu_local_tasks')]
  public function preprocess(array &$variables): void {
    // Prepare structure for normalization.
    foreach ($this->localTaskTypes as $type) {
      $preparedLinks = [];
      $menuLocalTasks = $variables[$type];
      // @phpstan-ignore-next-line
      \uasort($menuLocalTasks, [SortArray::class, 'sortByWeightProperty']);

      /** @var array{"#access"?: \Drupal\Core\Access\AccessResultInterface, "#active": bool,"#link": array{url: \Drupal\Core\Url, localized_options: array, title: string}} $menuLocalTask */
      foreach ($menuLocalTasks as $menuLocalTask) {
        // Access check.
        if (isset($menuLocalTask['#access']) && !$menuLocalTask['#access'] instanceof AccessResultAllowed) {
          continue;
        }

        if ($menuLocalTask['#active']) {
          $menuLocalTask['#link']['url']->mergeOptions([
            'attributes' => [
              'class' => [
                'active',
              ],
            ],
          ]);
        }
        $preparedLinks[] = [
          'link' => [
            '#url' => $menuLocalTask['#link']['url'],
            '#options' => $menuLocalTask['#link']['localized_options'],
          ],
          'title' => $menuLocalTask['#link']['title'],
        ];
      }
      $variables['preprocessed_items_' . $type] = $preparedLinks;
    }
  }

}
