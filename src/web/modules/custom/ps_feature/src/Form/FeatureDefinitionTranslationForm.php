<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Form;

use Drupal\config_translation\Form\ConfigTranslationFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Custom configuration translation form for feature definitions.
 */
class FeatureDefinitionTranslationForm extends ConfigTranslationFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_feature_definition_translation_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?RouteMatchInterface $route_match = NULL, ?string $plugin_id = NULL, ?string $langcode = NULL): array {
    // Initialize mapper and language from parameters.
    if ($plugin_id) {
      /** @var \Drupal\config_translation\ConfigMapperManagerInterface $mapper_manager */
      $mapper_manager = \Drupal::service('plugin.manager.config_translation.mapper');
      $this->mapper = $mapper_manager->createInstance($plugin_id);
      $this->mapper->populateFromRouteMatch($route_match);
    }
    
    if ($langcode) {
      $this->language = \Drupal::languageManager()->getLanguage($langcode);
    }
    
    /** @var \Drupal\config_translation\ConfigMapperInterface $mapper */
    $mapper = $this->mapper;
    $language = $this->language;
    
    // Get the config names.
    $config_names = $mapper->getConfigNames();
    if (empty($config_names)) {
      return $form;
    }
    
    $config_name = reset($config_names);
    
    // Load source config (original, without language overrides).
    // Use config.storage to get the raw config without any overrides.
    $source_data = \Drupal::service('config.storage')->read($config_name);
    $source_config = new \Drupal\Core\Config\Config($config_name, \Drupal::service('config.storage'), \Drupal::service('event_dispatcher'), \Drupal::service('config.typed'));
    $source_config->initWithData($source_data);
    
    // Load translation config.
    $translation_config = \Drupal::service('language.config_factory_override')
      ->getOverride($language->getId(), $config_name);
    
    // Build custom form fields for all translatable properties.
    $form['translation'] = [
      '#tree' => TRUE,
    ];
    
    // Label field (already handled by parent, but we customize it).
    $form['translation']['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#default_value' => $translation_config->get('label') ?? $source_config->get('label'),
      '#required' => TRUE,
      '#description' => $this->t('Original: @value', [
        '@value' => $source_config->get('label'),
      ]),
    ];
    
    // Description field.
    $source_description = $source_config->get('description') ?? '';
    $form['translation']['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $translation_config->get('description') ?? $source_description,
      '#rows' => 3,
      '#description' => $source_description 
        ? $this->t('Original: @value', ['@value' => $source_description])
        : $this->t('No description in the original.'),
    ];
    
    // Payload defaults nested fields.
    $type_driver = $source_config->get('type_driver');
    $source_payload = $source_config->get('payload_defaults') ?? [];
    $translation_payload = $translation_config->get('payload_defaults') ?? [];
    
    // Unit field (for numeric and range types).
    if (in_array($type_driver, ['numeric', 'range']) && isset($source_payload['unit'])) {
      $form['translation']['payload_defaults']['unit'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Unit'),
        '#default_value' => $translation_payload['unit'] ?? $source_payload['unit'],
        '#size' => 20,
        '#description' => $this->t('Original: @value', [
          '@value' => $source_payload['unit'],
        ]),
      ];
    }
    
    // Options field (for list type).
    if ($type_driver === 'list' && isset($source_payload['options']) && is_array($source_payload['options'])) {
      $form['translation']['payload_defaults']['options'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('List options'),
        '#description' => $this->t('Translate the labels for each option. Keys should remain unchanged.'),
        '#tree' => TRUE,
      ];
      
      foreach ($source_payload['options'] as $key => $label) {
        $form['translation']['payload_defaults']['options'][$key] = [
          '#type' => 'textfield',
          '#title' => $this->t('Option: @key', ['@key' => $key]),
          '#default_value' => $translation_payload['options'][$key] ?? $label,
          '#description' => $this->t('Original: @value', ['@value' => $label]),
        ];
      }
    }
    
    // Add submit button.
    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save translation'),
      '#button_type' => 'primary',
    ];
    
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    /** @var \Drupal\config_translation\ConfigMapperInterface $mapper */
    $mapper = $this->mapper;
    $language = $this->language;
    
    // Get the config names.
    $config_names = $mapper->getConfigNames();
    if (empty($config_names)) {
      return;
    }
    
    $config_name = reset($config_names);
    
    // Load translation config.
    $translation_config = \Drupal::service('language.config_factory_override')
      ->getOverride($language->getId(), $config_name);
    
    // Get translation values.
    $translation_values = $form_state->getValue('translation') ?? [];
    
    // Save label.
    if (isset($translation_values['label'])) {
      $translation_config->set('label', $translation_values['label']);
    }
    
    // Save description.
    if (isset($translation_values['description'])) {
      $translation_config->set('description', $translation_values['description']);
    }
    
    // Save payload_defaults nested fields.
    if (isset($translation_values['payload_defaults'])) {
      $payload_translation = $translation_values['payload_defaults'];
      
      // Save unit.
      if (isset($payload_translation['unit'])) {
        $translation_config->set('payload_defaults.unit', $payload_translation['unit']);
      }
      
      // Save options.
      if (isset($payload_translation['options']) && is_array($payload_translation['options'])) {
        foreach ($payload_translation['options'] as $key => $label) {
          $translation_config->set("payload_defaults.options.$key", $label);
        }
      }
    }
    
    // Save the translation.
    $translation_config->save();
    
    // Show success message.
    $this->messenger()->addStatus($this->t('Successfully saved @language translation.', [
      '@language' => $language->getName(),
    ]));
    
    // Redirect back to translation overview.
    $form_state->setRedirect(
      $mapper->getOverviewRouteName(),
      $mapper->getOverviewRouteParameters()
    );
  }

}
