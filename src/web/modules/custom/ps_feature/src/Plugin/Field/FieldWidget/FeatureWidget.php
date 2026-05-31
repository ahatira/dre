<?php

namespace Drupal\ps_feature\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_feature\Service\FeatureTypeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'feature_default' widget.
 */
#[FieldWidget(
  id: 'feature_default',
  label: new TranslatableMarkup('Feature selector'),
  field_types: ['feature'],
)]
class FeatureWidget extends WidgetBase {

  /**
   * The feature type plugin manager.
   *
   * @var \Drupal\ps_feature\Service\FeatureTypeManager
   */
  protected FeatureTypeManager $featureTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->featureTypeManager = $container->get('plugin.manager.feature_type');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $item = $items[$delta];
    $field_name = $this->fieldDefinition->getName();
    
    // Get current values.
    $current_feature_id = $item->feature_definition_id ?? '';
    $current_payload = $item->getPayloadArray();

    // Container for the field.
    $element += [
      '#type' => 'fieldset',
      '#title' => $this->t('Feature'),
    ];

    // Feature definition selector.
    $feature_options = $this->getFeatureOptions();
    
    $element['feature_definition_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Feature'),
      '#options' => ['' => $this->t('- Select a feature -')] + $feature_options,
      '#default_value' => $current_feature_id,
      '#required' => $element['#required'] ?? FALSE,
      '#ajax' => [
        'callback' => [$this, 'ajaxRefreshPayloadFields'],
        'wrapper' => "feature-payload-wrapper-{$field_name}-{$delta}",
        'event' => 'change',
      ],
    ];

    // Payload fields container.
    $element['payload_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => "feature-payload-wrapper-{$field_name}-{$delta}"],
    ];

    // Get selected feature (from AJAX or default).
    $selected_feature_id = $form_state->getValue([
      $field_name,
      $delta,
      'feature_definition_id',
    ]) ?? $current_feature_id;

    if (!empty($selected_feature_id)) {
      $feature_definition = \Drupal::entityTypeManager()
        ->getStorage('fb_feature_definition')
        ->load($selected_feature_id);

      if ($feature_definition) {
        $type = $feature_definition->getTypeDriver();
        $plugin = $this->featureTypeManager->createInstance($type);
        
        // Get payload fields from the plugin.
        $payload_fields = $plugin->buildPayloadForm($current_payload);
        
        foreach ($payload_fields as $key => $field_element) {
          $element['payload_wrapper'][$key] = $field_element;
        }
      }
    }

    // Hidden field to store the JSON payload.
    $element['payload'] = [
      '#type' => 'hidden',
      '#default_value' => json_encode($current_payload),
    ];

    return $element;
  }

  /**
   * AJAX callback to refresh payload fields.
   */
  public function ajaxRefreshPayloadFields(array &$form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    
    // Navigate up to find the payload_wrapper.
    $element_parents = array_slice($triggering_element['#array_parents'], 0, -1);
    $element_parents[] = 'payload_wrapper';
    
    $element = $form;
    foreach ($element_parents as $parent) {
      $element = $element[$parent];
    }
    
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    $return = [];
    
    foreach ($values as $delta => $value) {
      if (empty($value['feature_definition_id'])) {
        continue;
      }

      // Build payload array from payload_wrapper fields.
      $payload = [];
      if (isset($value['payload_wrapper']) && is_array($value['payload_wrapper'])) {
        $payload = $value['payload_wrapper'];
        
        // Special handling for 'list' type with codes_text.
        if (isset($payload['codes_text'])) {
          $codes_text = $payload['codes_text'];
          $codes = array_filter(array_map('trim', explode("\n", $codes_text)));
          $payload['codes'] = $codes;
          unset($payload['codes_text']);
        }
      }

      $return[$delta] = [
        'feature_definition_id' => $value['feature_definition_id'],
        'payload' => json_encode($payload),
      ];
    }

    return $return;
  }

  /**
   * Gets available feature options based on field settings.
   *
   * @return array
   *   Array of feature options keyed by feature ID.
   */
  protected function getFeatureOptions(): array {
    $storage = \Drupal::entityTypeManager()->getStorage('fb_feature_definition');
    
    $allowed_groups = array_filter($this->getFieldSetting('allowed_groups') ?: []);
    $allowed_features = array_filter($this->getFieldSetting('allowed_features') ?: []);

    // Build query.
    $query = $storage->getQuery()
      ->accessCheck(FALSE)
      ->sort('label');

    if (!empty($allowed_groups)) {
      $query->condition('group', $allowed_groups, 'IN');
    }

    if (!empty($allowed_features)) {
      $query->condition('id', $allowed_features, 'IN');
    }

    $ids = $query->execute();
    
    if (empty($ids)) {
      return [];
    }

    $features = $storage->loadMultiple($ids);
    $options = [];
    
    foreach ($features as $feature) {
      $group_label = '';
      $group = \Drupal::entityTypeManager()
        ->getStorage('fb_feature_group')
        ->load($feature->getGroup());
      
      if ($group) {
        $group_label = $group->label();
      }
      
      $options[$feature->id()] = $feature->label() . ' (' . $group_label . ')';
    }

    return $options;
  }

}
