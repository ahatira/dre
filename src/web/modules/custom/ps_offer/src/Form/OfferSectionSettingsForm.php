<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_core\Form\IconAutocompleteHelperTrait;
use Drupal\ps_core\Service\OfferSectionRegistry;

/**
 * Configures offer detail section titles and icons.
 */
final class OfferSectionSettingsForm extends ConfigFormBase {

  use IconAutocompleteHelperTrait;

  public function __construct(
    ConfigFactoryInterface $config_factory,
    private readonly OfferSectionRegistry $sectionRegistry,
  ) {
    parent::__construct($config_factory);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(\Symfony\Component\DependencyInjection\ContainerInterface $container): static {
    return new static(
      $container->get('config.factory'),
      $container->get('ps_core.section_registry'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_core.offer_section_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_offer_section_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_core.offer_section_settings');

    $form['intro'] = [
      '#markup' => '<p>' . $this->t('Configure headings and icons for offer detail page sections. Sections are registered by modules; only installed sections appear below.') . '</p>',
    ];

    $form['sections'] = [
      '#type' => 'details',
      '#title' => $this->t('Section headings'),
      '#open' => TRUE,
    ];

    foreach ($this->sectionRegistry->getPlugins() as $section_id => $plugin) {
      $stored = (array) ($config->get("sections.$section_id") ?? []);
      $default_icon = $plugin->getDefaultIcon();

      $form['sections'][$section_id] = [
        '#type' => 'fieldset',
        '#title' => $plugin->getAdminLabel(),
        '#tree' => TRUE,
      ];

      $form['sections'][$section_id]['label'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Section title'),
        '#default_value' => (string) ($stored['label'] ?? $plugin->getDefaultLabel()),
        '#maxlength' => 255,
        '#required' => TRUE,
      ];

      if ($default_icon !== '') {
        $form['sections'][$section_id]['icon'] = $this->buildIconPickerElement(
          $this->t('Section icon'),
          $this->getIconDefault($stored['icon'] ?? NULL, $default_icon),
          [
            'description' => $this->t('UI Icon shown next to the section title on offer detail pages.'),
            'required' => TRUE,
          ],
        );
      }
      else {
        $form['sections'][$section_id]['icon'] = [
          '#type' => 'value',
          '#value' => '',
        ];
      }
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    $sections = (array) $form_state->getValue('sections');
    foreach ($this->sectionRegistry->getPlugins() as $section_id => $plugin) {
      $values = (array) ($sections[$section_id] ?? []);
      $default_icon = $plugin->getDefaultIcon();
      if ($default_icon === '') {
        continue;
      }

      $form_state->setValue(
        ['sections', $section_id, 'icon'],
        $this->extractIconId($values['icon'] ?? NULL, $default_icon),
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $editable = $this->configFactory->getEditable('ps_core.offer_section_settings');
    $sections = [];

    foreach ((array) $form_state->getValue('sections') as $section_id => $values) {
      if (!is_array($values)) {
        continue;
      }

      $plugin = $this->sectionRegistry->getPlugin((string) $section_id);
      if ($plugin === NULL) {
        continue;
      }

      $icon = trim((string) ($values['icon'] ?? ''));
      if ($icon === '' && $plugin->getDefaultIcon() !== '') {
        $icon = $plugin->getDefaultIcon();
      }

      $sections[$section_id] = [
        'label' => trim((string) ($values['label'] ?? $plugin->getDefaultLabel())),
        'icon' => $icon,
      ];
    }

    $editable->set('sections', $sections)->save();
    parent::submitForm($form, $form_state);
  }

}
