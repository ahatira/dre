<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp\Hook;

use Drupal\Core\Cache\CacheCollectorInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Add theme settings.
 */
class ThemeSettings {

  use StringTranslationTrait;

  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    #[Autowire(service: 'library.discovery')]
    protected CacheCollectorInterface $libraryDiscovery,
  ) {}

  /**
   * Implements hook_form_system_theme_settings_alter().
   */
  #[Hook('form_system_theme_settings_alter')]
  public function alter(array &$form, FormStateInterface $formState): void {
    if (isset($form['config_key']['#value']) && \is_string($form['config_key']['#value'])) {
      $config_key = $form['config_key']['#value'];
    }
    else {
      return;
    }

    $form['ui_suite_bnp'] = [
      '#type' => 'details',
      '#title' => $this->t('Framework CSS'),
      '#open' => TRUE,
    ];

    $form['ui_suite_bnp']['container'] = [
      '#type' => 'select',
      '#title' => $this->t('Page container'),
      '#description' => $this->t('Select an option for <a href=":url">Bootstrap containers</a>.', [
        ':url' => 'https://getbootstrap.com/docs/5.3/layout/containers/',
      ]),
      '#options' => [
        'container' => $this->t('Container'),
        'container-sm' => $this->t('Container small'),
        'container-md' => $this->t('Container medium'),
        'container-lg' => $this->t('Container large'),
        'container-xl' => $this->t('Container x-large'),
        'container-xxl' => $this->t('Container xx-large'),
        'container-fluid' => $this->t('Container fluid'),
      ],
      '#config_target' => $config_key . ':container',
    ];

    $form['ui_suite_bnp']['library'] = [
      '#type' => 'details',
      '#title' => $this->t('Library'),
      '#tree' => TRUE,
    ];
    $form['ui_suite_bnp']['library']['js_loading'] = [
      '#type' => 'select',
      '#title' => $this->t('JavaScript'),
      '#description' => $this->t('If left empty, it is assumed that you have custom code or sub-theme altering or extending ui_suite_bnp/framework library to load JS your own way. Runtime CDN is intentionally not supported.'),
      '#options' => [
        'ui_suite_bnp/framework_js' => $this->t('Local'),
      ],
      '#empty_value' => '',
      '#config_target' => $config_key . ':library.js_loading',
      '#after_build' => [
        [static::class, 'filterLibraries'],
      ],
    ];
    $form['ui_suite_bnp']['library']['css_loading'] = [
      '#type' => 'select',
      '#title' => $this->t('Stylesheet'),
      '#description' => $this->t('If left empty, it is assumed that you have custom code or sub-theme altering or extending ui_suite_bnp/framework library to load CSS your own way.'),
      '#options' => [
        'ui_suite_bnp/framework_css_bnpp' => $this->t('BNP Paribas'),
        'ui_suite_bnp/framework_css_re' => $this->t('Real Estate (global)'),
      ],
      '#empty_value' => '',
      '#config_target' => $config_key . ':library.css_loading',
      '#after_build' => [
        [static::class, 'filterLibraries'],
      ],
    ];

    $form['#submit'][] = static::class . ':submitForm';
  }

  /**
   * Submit callback.
   *
   * @param array $form
   *   The form structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    /** @var string $config_key */
    $config_key = $form_state->getValue('config_key');
    $theme_config = $this->configFactory->get($config_key);

    $checked_keys = [
      'library.js_loading' => ['library', 'js_loading'],
      'library.css_loading' => ['library', 'css_loading'],
    ];

    foreach ($checked_keys as $config_path => $form_path) {
      if ($theme_config->get($config_path) != $form_state->getValue($form_path)) {
        $this->libraryDiscovery->clear();
        return;
      }
    }
  }

  /**
   * Remove invalid libraries from select options.
   *
   * @param array{"#options": array} $element
   *   The form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The modified element.
   */
  public static function filterLibraries(array $element, FormStateInterface $form_state): array {
    $element['#options'] = static::clearLibraries($element['#options']);
    return $element;
  }

  /**
   * Filter invalid libraries.
   *
   * @param array $options
   *   The options to filter.
   *
   * @return array
   *   The changed options.
   */
  protected static function clearLibraries(array $options): array {
    /** @var \Drupal\Core\Asset\LibraryDiscoveryInterface $library_discovery */
    $library_discovery = \Drupal::service('library.discovery');
    foreach ($options as $option => $value) {
      if (\is_array($value)) {
        $options[$option] = static::clearLibraries($value);
        if (empty($options[$option])) {
          unset($options[$option]);
        }
        continue;
      }

      $library_name = \explode('/', $option);
      if (!isset($library_name[1])) {
        continue;
      }
      $library = $library_discovery->getLibraryByName($library_name[0], $library_name[1]);
      // @phpstan-ignore-next-line
      if ($library && !static::isLibraryValid($library)) {
        unset($options[$option]);
      }
    }

    return $options;
  }

  /**
   * Check if library files are accessible.
   *
   * @param array{js?: array{array{type: string, data: string}}, css?: array{array{type: string, data: string}}, dependencies?: string[]} $library
   *   A library definition.
   *
   * @return bool
   *   TRUE if the library files are accessible.
   *
   * @see https://www.drupal.org/project/drupal/issues/2231385
   */
  protected static function isLibraryValid(array $library): bool {
    if (\array_key_exists('js', $library)) {
      foreach ($library['js'] as $js) {
        if ($js['type'] == 'file') {
          if (!\file_exists(\Drupal::root() . '/' . $js['data'])) {
            return FALSE;
          }
        }
      }
    }

    if (\array_key_exists('css', $library)) {
      foreach ($library['css'] as $css) {
        if ($css['type'] == 'file') {
          if (!\file_exists(\Drupal::root() . '/' . $css['data'])) {
            return FALSE;
          }
        }
      }
    }

    if (\array_key_exists('dependencies', $library)) {
      foreach ($library['dependencies'] as $dependency) {
        $parts = \explode('/', $dependency, (int) 2);
        $dependencyLibrary = \Drupal::service('library.discovery')->getLibraryByName($parts[0], $parts[1]);
        // @phpstan-ignore-next-line
        if ($dependencyLibrary && !static::isLibraryValid($dependencyLibrary)) {
          return FALSE;
        }
      }
    }

    return TRUE;
  }

}
