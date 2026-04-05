<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Cache\CacheCollectorInterface;
use Drupal\Core\Asset\LibraryDiscoveryInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Add theme settings.
 */
class ThemeSettings
{
    use StringTranslationTrait;

    public function __construct(
        protected ConfigFactoryInterface $configFactory,
        #[Autowire(service: 'library.discovery')]
        protected CacheCollectorInterface&LibraryDiscoveryInterface $libraryDiscovery,
    ) {
    }

  /**
   * Implements hook_form_system_theme_settings_alter().
   */
    #[Hook('form_system_theme_settings_alter')]
    public function alter(array &$form, FormStateInterface $formState): void
    {
        if (isset($form['config_key']['#value']) && \is_string($form['config_key']['#value'])) {
            $config_key = $form['config_key']['#value'];
        } else {
            return;
        }

        $form['ui_suite_bnppre'] = [
        '#type' => 'details',
        '#title' => $this->t('UI Suite BNP PRE'),
        '#open' => true,
        ];

        $form['ui_suite_bnppre']['container'] = [
        '#type' => 'select',
        '#title' => $this->t('Page container'),
        '#description' => $this->t('Select an option for <a href=":url">UI Suite BNP PRE containers</a>.', [
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

        $form['ui_suite_bnppre']['footer_container'] = [
        '#type' => 'select',
        '#title' => $this->t('Footer container'),
        '#description' => $this->t('Select the container class used by the footer component.'),
        '#options' => [
        'container' => $this->t('Container'),
        'container-sm' => $this->t('Container small'),
        'container-md' => $this->t('Container medium'),
        'container-lg' => $this->t('Container large'),
        'container-xl' => $this->t('Container x-large'),
        'container-xxl' => $this->t('Container xx-large'),
        'container-fluid' => $this->t('Container fluid'),
        ],
        '#config_target' => $config_key . ':footer_container',
        ];

        $form['ui_suite_bnppre']['language_switcher'] = [
        '#type' => 'details',
        '#title' => $this->t('Language switcher'),
        '#open' => false,
        '#tree' => true,
        ];

        $form['ui_suite_bnppre']['language_switcher']['display_mode'] = [
        '#type' => 'select',
        '#title' => $this->t('Language label format'),
        '#description' => $this->t('Choose how each language label is displayed in the header switcher.'),
        '#options' => [
        'code_lower' => $this->t('Code (lowercase)'),
        'code_upper' => $this->t('Code (uppercase)'),
        'code_capitalize' => $this->t('Code (Capitalize)'),
        'name_native' => $this->t('Native name'),
        'name_translated' => $this->t('Translated name'),
        ],
        '#config_target' => $config_key . ':language_switcher.display_mode',
        ];

        $form['ui_suite_bnppre']['language_switcher']['show_icons'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Display language icons when available'),
        '#description' => $this->t(
            'Icons are shown only when the Language Icons module is enabled and provides icon markup.',
        ),
        '#config_target' => $config_key . ':language_switcher.show_icons',
        ];

        $form['ui_suite_bnppre']['library'] = [
        '#type' => 'details',
        '#title' => $this->t('Library'),
        '#tree' => true,
        ];
        $form['ui_suite_bnppre']['library']['js_loading'] = [
        '#type' => 'select',
        '#title' => $this->t('JavaScript'),
        '#description' => $this->t('If left empty, it is assumed that you have custom code or sub-theme altering or extending ui_suite_bnppre/framework library to load JS your own way.'),
        '#options' => [
        'ui_suite_bnppre/framework_js' => $this->t('Local'),
        ],
        '#empty_value' => '',
        '#config_target' => $config_key . ':library.js_loading',
        '#after_build' => [
        [$this, 'filterLibraries'],
        ],
        ];
        $form['ui_suite_bnppre']['library']['css_loading'] = [
        '#type' => 'select',
        '#title' => $this->t('Stylesheet'),
        '#description' => $this->t('If left empty, it is assumed that you have custom code or sub-theme altering or extending ui_suite_bnppre/framework library to load CSS your own way.')
        . '<br>'
        . $this->t('Select the Realestate stylesheet variant to load.'),
        '#options' => [
        'ui_suite_bnppre/framework_css_bnppre' => $this->t('Realestate'),
        'ui_suite_bnppre/framework_css_bnppre_ps' => $this->t('Property Search'),
        ],
        '#empty_value' => '',
        '#config_target' => $config_key . ':library.css_loading',
        '#after_build' => [
        [$this, 'filterLibraries'],
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
    public function submitForm(array &$form, FormStateInterface $form_state): void
    {
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
    public function filterLibraries(array $element, FormStateInterface $form_state): array
    {
        $element['#options'] = $this->clearLibraries($element['#options']);
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
    protected function clearLibraries(array $options): array
    {
        foreach ($options as $option => $value) {
            if (\is_array($value)) {
                $options[$option] = $this->clearLibraries($value);
                if (empty($options[$option])) {
                    unset($options[$option]);
                }
                continue;
            }

            $library_name = \explode('/', $option);
            if (!isset($library_name[1])) {
                continue;
            }
            $library = $this->libraryDiscovery->getLibraryByName($library_name[0], $library_name[1]);
          // @phpstan-ignore-next-line
            if ($library && !$this->isLibraryValid($library)) {
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
    protected function isLibraryValid(array $library): bool
    {
        if (\array_key_exists('js', $library)) {
            foreach ($library['js'] as $js) {
                if ($js['type'] == 'file') {
                    if (!\file_exists(DRUPAL_ROOT . '/' . $js['data'])) {
                        return false;
                    }
                }
            }
        }

        if (\array_key_exists('css', $library)) {
            foreach ($library['css'] as $css) {
                if ($css['type'] == 'file') {
                    if (!\file_exists(DRUPAL_ROOT . '/' . $css['data'])) {
                        return false;
                    }
                }
            }
        }

        if (\array_key_exists('dependencies', $library)) {
            foreach ($library['dependencies'] as $dependency) {
                $parts = \explode('/', $dependency, (int) 2);
                $dependencyLibrary = $this->libraryDiscovery->getLibraryByName($parts[0], $parts[1]);
              // @phpstan-ignore-next-line
                if ($dependencyLibrary && !$this->isLibraryValid($dependencyLibrary)) {
                    return false;
                }
            }
        }

        return true;
    }
}
