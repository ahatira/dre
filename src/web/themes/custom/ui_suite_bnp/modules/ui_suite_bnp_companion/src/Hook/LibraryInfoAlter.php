<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp_companion\Hook;

use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hook implementation.
 */
class LibraryInfoAlter {

  public function __construct(
    protected ModuleExtensionList $moduleExtensionList,
  ) {}

  /**
   * Implements hook_library_info_alter().
   *
   * Override libraries regarding companion module changes.
   */
  #[Hook('library_info_alter')]
  public function alter(array &$libraries, string $extension): void {
    $modulePath = $this->moduleExtensionList->getPath('ui_suite_bnp_companion');
    switch ($extension) {
      case 'ui_suite_bnp':
        $oldPath = 'assets/js/layout-builder/layout-builder.js';
        if (isset($libraries['drupal.layout_builder_block_filter']['js'][$oldPath])) {
          $newPath = '/' . $modulePath . '/assets/js/layout-builder/layout-builder.js';
          $libraries['drupal.layout_builder_block_filter']['js'][$newPath] = $libraries['drupal.layout_builder_block_filter']['js'][$oldPath];
          unset($libraries['drupal.layout_builder_block_filter']['js'][$oldPath]);
        }
        break;

      case 'section_library':
        $oldPath = 'js/section-library.js';
        if (isset($libraries['section_library']['js'][$oldPath])) {
          $newPath = '/' . $modulePath . '/assets/js/section-library/section-library.js';
          $libraries['section_library']['js'][$newPath] = $libraries['section_library']['js'][$oldPath];
          unset($libraries['section_library']['js'][$oldPath]);
        }
        break;

      default:
        break;
    }
  }

}
