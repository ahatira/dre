<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Utility;

use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ThemeHandlerInterface;

/**
 * Defines a theme object.
 */
class Theme {

  /**
   * The theme machine name.
   *
   * @var string
   */
  protected string $name;

  /**
   * An array of installed themes.
   *
   * @var array
   */
  protected array $themes;

  /**
   * The current theme info.
   *
   * @var array
   */
  protected array $info;

  public function __construct(
    protected Extension $theme,
    protected ThemeHandlerInterface $themeHandler,
  ) {
    $this->name = $theme->getName();
    $this->themes = $this->themeHandler->listInfo();
    $this->info = $this->themes[$this->name]->info ?? [];
  }

  /**
   * Serialization method.
   */
  public function __sleep() {
    // Only store the theme name.
    return ['name'];
  }

  /**
   * Unserialize method.
   */
  public function __wakeup() {
    $theme_handler = Bootstrap::getThemeHandler();
    $theme = $theme_handler->getTheme($this->name);
    $this->__construct($theme, $theme_handler);
  }

  /**
   * Returns the theme machine name.
   *
   * @return string
   *   Theme machine name.
   */
  public function __toString() {
    return $this->getName();
  }

  /**
   * Retrieves an individual item from a theme's cache in the database.
   *
   * @param string $name
   *   The name of the item to retrieve from the theme cache.
   * @param array $context
   *   Optional. An array of additional context to use for retrieving the
   *   cached storage.
   * @param mixed $default
   *   Optional. The default value to use if $name does not exist.
   *
   * @return mixed|\Drupal\ui_suite_bnppre\Utility\StorageItem
   *   The cached value for $name.
   */
  public function getCache($name, array $context = [], $default = []) {
    static $cache = [];

    // Prepend the theme name as the first context item, followed by cache name.
    \array_unshift($context, $name);
    \array_unshift($context, $this->getName());

    // Join context together with ":" and use it as the name.
    $name = \implode(':', $context);

    if (!isset($cache[$name])) {
      $storage = self::getStorage();
      $value = $storage->get($name);
      if (!isset($value)) {
        $value = \is_array($default) ? new StorageItem($default, $storage) : $default;
        $storage->set($name, $value);
      }
      $cache[$name] = $value;
    }

    return $cache[$name];
  }

  /**
   * Retrieves the theme info.
   *
   * @param string $property
   *   A specific property entry from the theme's info array to return.
   *
   * @return mixed
   *   The entire theme info or a specific item if $property was passed.
   */
  public function getInfo($property = NULL) {
    if (isset($property)) {
      return $this->info[$property] ?? NULL;
    }
    return $this->info;
  }

  /**
   * Returns the machine name of the theme.
   *
   * @return string
   *   The machine name of the theme.
   */
  public function getName() {
    return $this->theme->getName();
  }

  /**
   * Retrieves the theme's cache from the database.
   *
   * @return \Drupal\ui_suite_bnppre\Utility\Storage
   *   The cache object.
   */
  public function getStorage() {
    /** @var \Drupal\ui_suite_bnppre\Utility\Storage[] $cache */
    static $cache = [];
    $theme = $this->getName();
    if (!isset($cache[$theme])) {
      $cache[$theme] = new Storage($theme);
    }
    return $cache[$theme];
  }

}
