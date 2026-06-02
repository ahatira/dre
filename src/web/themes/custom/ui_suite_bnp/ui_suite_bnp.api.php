<?php

/**
 * @file
 * List of available procedural hook and alter APIs for use in your sub-theme.
 */

declare(strict_types=1);

/**
 * @addtogroup plugins_alter
 *
 * @{
 */

/**
 * Allows sub-themes to alter the array used for colorizing text.
 *
 * @param array $texts
 *   An associative array containing the text and classes to be matched, passed
 *   by reference.
 *
 * @see \Drupal\ui_suite_bnp\Utility\Bootstrap::cssClassFromString()
 */
function hook_ui_suite_bnp_colorize_text_alter(array &$texts): void {
  // This matches the exact string: "My Unique Button Text".
  // Note: the t() function in D8 returns a TranslatableMarkup object.
  // It must be rendered to a string before it can be added as an array key.
  $texts['matches'][\t('My Unique Button Text')->render()] = 'primary';

  // This would also match the string above, however the class returned would
  // also be the one above; "matches" takes precedence over "contains".
  $texts['contains'][\t('Unique')->render()] = 'notice';

  // Remove matching for strings that contain "apply":
  unset($texts['contains'][\t('Apply')->render()]);

  // Change the class that matches "Rebuild" (originally "warning"):
  $texts['contains'][\t('Rebuild')->render()] = 'success';
}

/**
 * Allows sub-themes to alter the array used for associating an icon with text.
 *
 * @param array $texts
 *   An associative array containing the text and icons to be matched, passed
 *   by reference.
 *
 * @see \Drupal\ui_suite_bnp\Utility\Bootstrap::iconFromString()
 */
function hook_ui_suite_bnp_iconize_text_alter(array &$texts): void {
  // This matches the exact string: "My Unique Button Text".
  // Note: the t() function in D8 returns a TranslatableMarkup object.
  // It must be rendered to a string before it can be added as an array key.
  $texts['matches'][\t('My Unique Button Text')->render()] = [
    'packId' => 'bootstrap',
    'iconId' => 'heart-fill',
    'settings' => [
      'size' => '1em',
    ],
  ];

  // This would also match the string above, however the class returned would
  // also be the one above; "matches" takes precedence over "contains".
  $texts['contains'][\t('Unique')->render()] = [
    'packId' => 'bootstrap',
    'iconId' => 'megaphone-fill',
    'settings' => [
      'size' => '1em',
    ],
  ];

  // Remove matching for strings that contain "filter":
  unset($texts['contains'][\t('Filter')->render()]);

  // Change the icon that matches "Upload" (originally "upload"):
  $texts['contains'][\t('Upload')->render()] = [
    'packId' => 'bootstrap',
    'iconId' => 'check',
    'settings' => [
      'size' => '1em',
    ],
  ];
}

/**
 * @} End of "addtogroup".
 */
