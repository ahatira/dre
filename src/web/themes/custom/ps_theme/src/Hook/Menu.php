<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Hook;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;

/**
 * Menu preprocess — language-specific URIs and Property Search header menus.
 */
final class Menu {

  /**
   * @var array<string, array<string, string>>|null
   */
  private static ?array $externalUris = NULL;

  public function __construct(
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_menu')]
  public function preprocessMenu(array &$variables): void {
    if (($variables['menu_name'] ?? '') === 'main') {
      $this->preprocessMainMegaMenu($variables);
    }

    if (empty($variables['items'])) {
      return;
    }

    if (($variables['menu_name'] ?? '') === 'account') {
      $this->localizeAccountMenu($variables['items']);
    }

    if (($variables['menu_name'] ?? '') === 'ps_header_actions') {
      $this->applyHeaderActionLinkAttributes($variables['items']);
      $this->filterHeaderActionItems($variables['items']);
      $variables['#attached']['library'][] = 'core/drupal.dialog.ajax';
    }

    $this->applyExternalUris($variables['items']);
  }

  /**
   * Advanced Mega Menu on main — PS theme assets instead of module defaults.
   *
   * @param array<string, mixed> $variables
   */
  private function preprocessMainMegaMenu(array &$variables): void {
    $enabled = \Drupal::config('advanced_mega_menu.settings')->get('enabled_menus') ?? [];
    if (!in_array('main', $enabled, TRUE)) {
      return;
    }

    $instance = (string) (\Drupal::request()->attributes->get('ps_mega_menu_instance') ?? 'main');
    $variables['ps_menu_instance'] = $instance;
    $variables['menu_nav_id'] = 'ps-main-nav-' . $instance;

    $variables['#attached']['library'][] = 'ps_theme/header';
    $variables['attributes']['class'][] = 'ps-mega-menu-nav';

    if (!empty($variables['#attached']['library'])) {
      $variables['#attached']['library'] = array_values(array_filter(
        $variables['#attached']['library'],
        static fn (string $library): bool => $library !== 'advanced_mega_menu/advanced_mega_menu',
      ));
    }
  }

  /**
   * Ensures Menu Link Attributes classes are on each header action link.
   *
   * @param array<int, array<string, mixed>> $items
   */
  private function applyHeaderActionLinkAttributes(array &$items): void {
    foreach ($items as &$item) {
      if ($item['url'] instanceof Url) {
        $options = $item['url']->getOptions();
        foreach ($options['attributes'] ?? [] as $name => $value) {
          if ($name === 'class') {
            $classes = is_array($value) ? $value : explode(' ', (string) $value);
            $item['attributes']->addClass($classes);
          }
          else {
            $item['attributes']->setAttribute($name, $value);
          }
        }
      }

      if (!empty($item['below'])) {
        $this->applyHeaderActionLinkAttributes($item['below']);
      }
    }
  }

  /**
   * Hides login for authenticated users in the header actions menu.
   *
   * @param array<int, array<string, mixed>> $items
   */
  private function filterHeaderActionItems(array &$items): void {
    $isAuthenticated = \Drupal::currentUser()->isAuthenticated();

    foreach ($items as $key => &$item) {
      $classes = $item['attributes']['class'] ?? [];
      if (!is_array($classes)) {
        $classes = [$classes];
      }

      if ($isAuthenticated && in_array('ps-header-actions__btn--login', $classes, TRUE)) {
        unset($items[$key]);
        continue;
      }

      if (!empty($item['below'])) {
        $this->filterHeaderActionItems($item['below']);
      }
    }
  }

  /**
   * @param array<int, array<string, mixed>> $items
   */
  private function localizeAccountMenu(array &$items): void {
    if ($this->languageManager->getCurrentLanguage()->getId() !== 'fr') {
      return;
    }

    foreach ($items as &$item) {
      $routeName = $item['url'] instanceof Url ? $item['url']->getRouteName() : NULL;
      if ($routeName === 'user.login') {
        $item['title'] = 'Se connecter';
      }
    }
  }

  /**
   * @param array<int, array<string, mixed>> $items
   */
  private function applyExternalUris(array &$items): void {
    $map = $this->externalUriMap();
    $langcode = $this->languageManager->getCurrentLanguage()->getId();

    foreach ($items as &$item) {
      $originalLink = $item['original_link'] ?? NULL;
      if (!is_object($originalLink) || !method_exists($originalLink, 'getPluginId')) {
        if (!empty($item['below'])) {
          $this->applyExternalUris($item['below']);
        }
        continue;
      }

      $pluginId = $originalLink->getPluginId();
      if (str_starts_with($pluginId, 'menu_link_content:')) {
        $uuid = substr($pluginId, strlen('menu_link_content:'));
      }
      else {
        $uuid = '';
      }

      if ($uuid !== '' && isset($map[$uuid][$langcode]) && $item['url'] instanceof Url) {
        $item['url'] = Url::fromUri($map[$uuid][$langcode], $item['url']->getOptions());
      }

      if (!empty($item['below'])) {
        $this->applyExternalUris($item['below']);
      }
    }
  }

  /**
   * @return array<string, array<string, string>>
   */
  private function externalUriMap(): array {
    if (self::$externalUris !== NULL) {
      return self::$externalUris;
    }

    $paths = [
      DRUPAL_ROOT . '/modules/custom/ps_demo/config/stellar_menu_uris.yml',
      \Drupal::service('extension.path.resolver')->getPath('theme', 'ps_theme') . '/config/menu/stellar_menu_uris.yml',
    ];

    foreach ($paths as $path) {
      if (!is_readable($path)) {
        continue;
      }
      /** @var array{external_uris?: array<string, array<string, string>>} $data */
      $data = Yaml::decode((string) file_get_contents($path));
      self::$externalUris = $data['external_uris'] ?? [];
      return self::$externalUris;
    }

    self::$externalUris = [];
    return self::$externalUris;
  }

}
