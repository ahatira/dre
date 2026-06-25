<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_core\Form\IconAutocompleteHelperTrait;
use Drupal\ps_core\Service\OfferSectionRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configures offer detail section titles and icons.
 */
final class OfferSectionSettingsForm extends ConfigFormBase {

  use IconAutocompleteHelperTrait;

  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typed_config_manager,
    private readonly OfferSectionRegistry $sectionRegistry,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($config_factory, $typed_config_manager);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('ps_core.section_registry'),
      $container->get('entity_type.manager'),
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
      '#tree' => TRUE,
    ];

    foreach ($this->sectionRegistry->getPlugins() as $section_id => $plugin) {
      $stored = (array) ($config->get("sections.$section_id") ?? []);

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

      $form['sections'][$section_id]['icon'] = $this->buildIconPickerElement(
        $this->t('Section icon'),
        trim((string) ($stored['icon'] ?? '')),
        [
          'description' => $this->t('Optional. Leave empty to hide the icon on offer detail pages.'),
          'required' => FALSE,
        ],
      );

      if ($section_id === 'location') {
        $form['sections'][$section_id]['transport_group'] = [
          '#type' => 'select',
          '#title' => $this->t('Transport feature group'),
          '#description' => $this->t('Features from this group are shown under the transport line in the location section.'),
          '#options' => $this->getFeatureGroupOptions(),
          '#default_value' => (string) ($stored['transport_group'] ?? 'equipements'),
          '#required' => TRUE,
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
      $form_state->setValue(
        ['sections', $section_id, 'icon'],
        $this->extractIconId($values['icon'] ?? NULL, ''),
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

      $sections[$section_id] = [
        'label' => trim((string) ($values['label'] ?? $plugin->getDefaultLabel())),
        'icon' => $icon,
      ];

      if ($section_id === 'location') {
        $sections[$section_id]['transport_group'] = trim((string) ($values['transport_group'] ?? 'equipements'));
      }
    }

    $editable->set('sections', $sections)->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * Returns feature group options for the location transport selector.
   *
   * @return array<string, string>
   *   Options keyed by group ID.
   */
  private function getFeatureGroupOptions(): array {
    $options = [];
    $groups = $this->entityTypeManager->getStorage('fb_feature_group')->loadMultiple();
    usort($groups, static function ($a, $b): int {
      $a_weight = method_exists($a, 'getWeight') ? $a->getWeight() : 0;
      $b_weight = method_exists($b, 'getWeight') ? $b->getWeight() : 0;
      if ($a_weight === $b_weight) {
        return strcasecmp((string) $a->label(), (string) $b->label());
      }
      return $a_weight <=> $b_weight;
    });

    foreach ($groups as $group) {
      $options[$group->id()] = $group->label();
    }

    return $options;
  }

}
