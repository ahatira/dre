<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp\Utility;

use Drupal\Core\Extension\ThemeHandlerInterface;

/**
 * The primary class for the Drupal Bootstrap base theme.
 *
 * Provides many helper methods.
 */
class Bootstrap {

  /**
   * Tag used to invalidate caches.
   *
   * @var string
   */
  public const CACHE_TAG = 'theme_registry';

  /**
   * Matches a Bootstrap class based on a string value.
   *
   * @param string|array $value
   *   The string to match against to determine the class. Passed by reference
   *   in case it is a render array that needs to be rendered and typecast.
   * @param string $default
   *   The default class to return if no match is found.
   *
   * @return string
   *   The Bootstrap class matched against the value of $haystack or $default
   *   if no match could be made.
   */
  public static function cssClassFromString(&$value, $default = '') {
    /** @var string|null $lang */
    static $lang;
    if (!isset($lang)) {
      $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
    }

    $theme = static::getTheme();
    /** @var \Drupal\ui_suite_bnp\Utility\StorageItem $texts */
    $texts = $theme->getCache('cssClassFromString', [$lang]);

    // Ensure it's a string value that was passed.
    // @phpstan-ignore-next-line
    $string = static::toString($value);

    if ($texts->isEmpty()) {
      $data = [
        // Text that match these specific strings are checked first.
        'matches' => [
          // Primary class.
          \t('Download feature')->render() => 'primary',

          // Success class.
          \t('Add effect')->render() => 'success',
          \t('Add and configure')->render() => 'success',
          \t('Save configuration')->render() => 'success',
          \t('Install and set as default')->render() => 'success',

          // Info class.
          \t('Save and add')->render() => 'info',
          \t('Add another item')->render() => 'info',
          \t('Update style')->render() => 'info',

          // Outline danger class.
          \t('Discard changes')->render() => 'outline-danger',
          \t('Revert to defaults')->render() => 'outline-danger',
        ],

        // Text containing these words anywhere in the string are checked last.
        'contains' => [
          // Primary class.
          \t('Confirm')->render() => 'primary',
          \t('Filter')->render() => 'primary',
          \t('Log in')->render() => 'primary',
          \t('Search')->render() => 'primary',
          \t('Settings')->render() => 'primary',
          \t('Submit')->render() => 'primary',
          \t('Upload')->render() => 'primary',

          // Secondary class.
          \t('Apply')->render() => 'secondary',
          \t('Update')->render() => 'secondary',

          // Success class.
          \t('Add')->render() => 'success',
          \t('Create')->render() => 'success',
          \t('Install')->render() => 'success',
          \t('Save')->render() => 'success',
          \t('Write')->render() => 'success',

          // Danger class.
          \t('Delete')->render() => 'danger',
          \t('Remove')->render() => 'danger',
          \t('Reset')->render() => 'danger',
          \t('Uninstall')->render() => 'danger',

          // Warning class.
          \t('Export')->render() => 'warning',
          \t('Import')->render() => 'warning',
          \t('Rebuild')->render() => 'warning',
          \t('Restore')->render() => 'warning',
        ],
      ];

      // Allow sub-themes to alter this array of patterns.
      /** @var \Drupal\Core\Theme\ThemeManagerInterface $theme_manager */
      $theme_manager = \Drupal::service('theme.manager');
      $theme_manager->alter('ui_suite_bnp_colorize_text', $data);

      /** @var array $data */
      $texts->setMultiple($data);
    }

    // Iterate over the array.
    /** @var array $strings */
    foreach ($texts as $pattern => $strings) {
      /** @var string $text */
      /** @var string $class */
      foreach ($strings as $text => $class) {
        switch ($pattern) {
          case 'matches':
            if ($string === $text) {
              return $class;
            }
            break;

          case 'contains':
            if (\str_contains(\mb_strtolower($string), \mb_strtolower($text))) {
              return $class;
            }
            break;
        }
      }
    }

    // Return the default if nothing was matched.
    return $default;
  }

  /**
   * Matches an icon based on a string value.
   *
   * Default to Bootstrap icons but can be altered.
   *
   * @param string|\Stringable $value
   *   The string to match against to determine the icon. Passed by reference
   *   in case it is a render array that needs to be rendered and typecast.
   * @param array $settings
   *   The icon pack settings if needed to force a specific usage.
   * @param array $default
   *   The default icon render array to return if no match is found.
   *
   * @return array
   *   The icon render array matched against the value of $haystack or
   *   $default if no match could be made.
   */
  public static function iconFromString(&$value, array $settings = [], array $default = []) {
    /** @var string|null $lang */
    static $lang;
    if (!isset($lang)) {
      $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
    }

    $theme = static::getTheme();
    /** @var \Drupal\ui_suite_bnp\Utility\StorageItem $texts */
    $texts = $theme->getCache('iconFromString', [$lang]);

    // Ensure it's a string value that was passed.
    // @phpstan-ignore-next-line
    $string = static::toString($value);

    if ($texts->isEmpty()) {
      $iconInfos = [
        'packId' => 'bootstrap',
        'settings' => $settings + [
          'size' => '1em',
        ],
      ];

      $data = [
        // Text that match these specific strings are checked first.
        'matches' => [],

        // Text containing these words anywhere in the string are checked last.
        'contains' => [
          \t('Configure')->render() => $iconInfos + ['iconId' => 'gear-fill'],
          \t('Manage')->render() => $iconInfos + ['iconId' => 'gear-fill'],
          \t('Settings')->render() => $iconInfos + ['iconId' => 'gear-fill'],
          \t('Download')->render() => $iconInfos + ['iconId' => 'download'],
          \t('Export')->render() => $iconInfos + ['iconId' => 'box-arrow-up-right'],
          \t('Filter')->render() => $iconInfos + ['iconId' => 'funnel-fill'],
          \t('Import')->render() => $iconInfos + ['iconId' => 'box-arrow-in-down-right'],
          \t('Save')->render() => $iconInfos + ['iconId' => 'check-lg'],
          \t('Update')->render() => $iconInfos + ['iconId' => 'check-lg'],
          \t('Edit')->render() => $iconInfos + ['iconId' => 'pencil-fill'],
          \t('Uninstall')->render() => $iconInfos + ['iconId' => 'trash'],
          \t('Install')->render() => $iconInfos + ['iconId' => 'plus-lg'],
          \t('Write')->render() => $iconInfos + ['iconId' => 'plus-lg'],
          \t('Cancel')->render() => $iconInfos + ['iconId' => 'x-lg'],
          \t('Delete')->render() => $iconInfos + ['iconId' => 'trash'],
          \t('Discard')->render() => $iconInfos + ['iconId' => 'trash'],
          \t('Remove')->render() => $iconInfos + ['iconId' => 'trash'],
          \t('Reset')->render() => $iconInfos + ['iconId' => 'trash'],
          \t('Revert')->render() => $iconInfos + ['iconId' => 'arrow-counterclockwise'],
          \t('Search')->render() => $iconInfos + ['iconId' => 'search'],
          \t('Upload')->render() => $iconInfos + ['iconId' => 'upload'],
          \t('Preview')->render() => $iconInfos + ['iconId' => 'eye-fill'],
          \t('Log in')->render() => $iconInfos + ['iconId' => 'box-arrow-in-right'],
          \t('Add')->render() => $iconInfos + ['iconId' => 'plus-lg'],
          \t('Cart')->render() => $iconInfos + ['iconId' => 'cart4'],
        ],
      ];

      // Allow sub-themes to alter this array of patterns.
      /** @var \Drupal\Core\Theme\ThemeManagerInterface $theme_manager */
      $theme_manager = \Drupal::service('theme.manager');
      $theme_manager->alter('ui_suite_bnp_iconize_text', $data);

      /** @var array $data */
      $texts->setMultiple($data);
    }

    // Iterate over the array.
    /** @var array $strings */
    foreach ($texts as $pattern => $strings) {
      /** @var string $text */
      /** @var array{iconId: string, packId: string, settings: array} $icon */
      foreach ($strings as $text => $icon) {
        switch ($pattern) {
          case 'matches':
            if ($string === $text) {
              return static::icon($icon['iconId'], $icon['packId'], $icon['settings']);
            }
            break;

          case 'contains':
            if (\str_contains(\mb_strtolower($string), \mb_strtolower($text))) {
              return static::icon($icon['iconId'], $icon['packId'], $icon['settings']);
            }
            break;
        }
      }
    }

    // Return a default icon if nothing was matched.
    return $default;
  }

  /**
   * Returns an icon render array.
   *
   * @param string $iconId
   *   The icon ID.
   * @param string $packId
   *   The icon pack ID.
   * @param array $settings
   *   (Optional) The icon settings.
   *
   * @return array
   *   The icon render array.
   */
  public static function icon(string $iconId, string $packId = 'bootstrap', array $settings = []): array {
    // @todo put size in method default value?
    return [
      '#type' => 'icon',
      '#pack_id' => $packId,
      '#icon_id' => $iconId,
      '#settings' => $settings + [
        'size' => '1em',
      ],
    ];
  }

  /**
   * Retrieves a theme instance of \Drupal\ui_suite_bnp.
   *
   * @param string|\Drupal\ui_suite_bnp\Utility\Theme $name
   *   The machine name of a theme. If omitted, the active theme will be used.
   * @param \Drupal\Core\Extension\ThemeHandlerInterface|null $theme_handler
   *   The theme handler object.
   *
   * @return \Drupal\ui_suite_bnp\Utility\Theme
   *   A theme object.
   */
  public static function getTheme($name = NULL, ?ThemeHandlerInterface $theme_handler = NULL) {
    // Immediately return if theme passed is already instantiated.
    if ($name instanceof Theme) {
      return $name;
    }

    /** @var \Drupal\ui_suite_bnp\Utility\Theme[] $themes */
    static $themes = [];

    // Retrieve the active theme.
    // Do not statically cache this as the active theme may change.
    if (!isset($name)) {
      $name = \Drupal::theme()->getActiveTheme()->getName();
    }

    if (!isset($theme_handler)) {
      $theme_handler = self::getThemeHandler();
    }

    if (!isset($themes[$name])) {
      $themes[$name] = new Theme($theme_handler->getTheme($name), $theme_handler);
    }

    return $themes[$name];
  }

  /**
   * Retrieves the theme handler instance.
   *
   * @return \Drupal\Core\Extension\ThemeHandlerInterface
   *   The theme handler instance.
   */
  public static function getThemeHandler() {
    static $theme_handler;
    if (!isset($theme_handler)) {
      $theme_handler = \Drupal::service('theme_handler');
    }
    /** @var \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler */
    return $theme_handler;
  }

  /**
   * Ensures a value is typecast to a string, rendering an array if necessary.
   *
   * @param string|array|object $value
   *   The value to typecast, passed by reference.
   *
   * @return string
   *   The typecast string value.
   */
  public static function toString(&$value) {
    if (\is_string($value)) {
      return $value;
    }
    if ($value instanceof \Stringable) {
      return $value->__toString();
    }
    if (Element::isRenderArray($value)) {
      return (string) Element::create($value)->renderPlain();
    }

    return '';
  }

}
