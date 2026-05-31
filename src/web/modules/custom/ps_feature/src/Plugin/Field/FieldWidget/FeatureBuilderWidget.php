<?php

namespace Drupal\ps_feature\Plugin\Field\FieldWidget;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'feature_builder' widget.
 */
#[FieldWidget(
  id: 'feature_builder',
  label: new TranslatableMarkup('Feature Builder (JS)'),
  field_types: ['feature'],
)]
class FeatureBuilderWidget extends WidgetBase {

  /**
   * @var \Drupal\ps_feature\Service\FeatureBuilderStateBuilder
   */
  protected $stateBuilder;

  /**
   * @var \Drupal\ps_feature\Service\FeatureCatalogueBuilder
   */
  protected $catalogueBuilder;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->stateBuilder = $container->get('ps_feature.state_builder');
    $instance->catalogueBuilder = $container->get('ps_feature.catalogue_builder');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function supportsMultipleValues(): bool {
    return TRUE;
  }

  /**
   * Bypasses the Drupal table (field_multiple_value_form) and returns
   * our container directly without any table markup.
   *
   * {@inheritdoc}
   */
  public function form(FieldItemListInterface $items, array &$form, FormStateInterface $form_state, $get_delta = NULL): array {
    $field_name = $this->fieldDefinition->getName();
    $parents = $form['#parents'];

    // Initialise le widget state (requis par les internals Drupal).
    if (!static::getWidgetState($parents, $field_name, $form_state)) {
      static::setWidgetState($parents, $field_name, $form_state, [
        'items_count' => count($items),
        'array_parents' => [],
      ]);
    }

    $element = $this->formElement($items, 0, [], $form, $form_state);
    $element['#field_name'] = $field_name;
    $element['#field_parents'] = $parents;
    $element['#after_build'][] = [static::class, 'afterBuild'];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $field_name = $this->fieldDefinition->getName();
    $entity = $form_state->getFormObject()->getEntity();
    // For new (unsaved) entities, id() returns NULL — use uuid() as stable fallback.
    $entity_key = $entity->id() ?? $entity->uuid();
    $widget_id = 'fb-' . $field_name . '-' . $entity_key;

    $initial_state = $this->resolveInitialState($items, $form_state, $field_name);
    $catalogue = $this->catalogueBuilder->buildForEntity($entity);

    return [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['fb-widget-mount'],
        'id' => $widget_id,
      ],
      '#attached' => [
        'library' => ['ps_feature/feature-builder'],
        'drupalSettings' => [
          'featureBuilder' => [
            $field_name => [
              'widgetId' => $widget_id,
              'fieldName' => $field_name,
              'initialState' => $initial_state,
              'catalogue' => $catalogue,
            ],
          ],
        ],
      ],
      'state' => [
        '#type' => 'hidden',
        '#name' => "fb_state_{$field_name}",
        '#value' => json_encode($initial_state),
        '#attributes' => [
          'id' => "fb-state-{$field_name}",
          'class' => ['fb-state-field'],
        ],
      ],
    ];
  }

  /**
   * Bypass Drupal's tree-based form value extraction.
   *
   * WidgetBase::extractFormValues() looks for the field at
   * $form_state->getValues()[$field_name], which doesn't exist in our
   * custom layout. We read the JSON directly from getUserInput() instead.
   *
   * {@inheritdoc}
   */
  public function extractFormValues(FieldItemListInterface $items, array $form, FormStateInterface $form_state): void {
    $field_name = $this->fieldDefinition->getName();
    $state_json = $form_state->getUserInput()["fb_state_{$field_name}"] ?? '{}';

    // Guard: enforce a reasonable payload size limit (512KB) to prevent DoS.
    if (strlen($state_json) > 524288) {
      return;
    }

    $state = json_decode($state_json, TRUE);

    // Guard: invalid JSON or unexpected structure.
    if (!is_array($state) || !isset($state['features']) || !is_array($state['features'])) {
      return;
    }

    // Load valid definition IDs to prevent saving arbitrary feature IDs.
    $valid_ids = $this->loadValidDefinitionIds();

    $values = [];
    foreach ($state['features'] as $feature) {
      // Each entry must have a non-empty id string.
      if (empty($feature['id']) || !is_string($feature['id'])) {
        continue;
      }
      // Reject feature IDs that don't correspond to a known config entity.
      if (!in_array($feature['id'], $valid_ids, TRUE)) {
        continue;
      }
      $payload = is_array($feature['payload'] ?? NULL) ? $feature['payload'] : [];
      $values[] = [
        'feature_definition_id' => $feature['id'],
        'payload' => json_encode($payload),
      ];
    }

    $items->setValue($values);
    $items->filterEmptyItems();
  }

  /**
   * Loads the IDs of all active fb_feature_definition config entities.
   *
   * @return string[]
   */
  protected function loadValidDefinitionIds(): array {
    $ids = $this->entityTypeManager
      ->getStorage('fb_feature_definition')
      ->getQuery()
      ->condition('status', TRUE)
      ->accessCheck(FALSE)
      ->execute();
    return array_values($ids);
  }

  /**
   * Resolves the initial widget state.
   *
   * During form rebuilds (validation errors), Drupal may not have updated
   * field items yet. In that case we must restore the user-entered JSON from
   * the hidden field to avoid losing in-progress edits.
   */
  protected function resolveInitialState(FieldItemListInterface $items, FormStateInterface $form_state, string $field_name): array {
    $user_input = $form_state->getUserInput();
    $raw_state = $user_input["fb_state_{$field_name}"] ?? NULL;

    if (is_string($raw_state) && $raw_state !== '' && strlen($raw_state) <= 524288) {
      $decoded = json_decode($raw_state, TRUE);
      if (is_array($decoded) && isset($decoded['features']) && is_array($decoded['features'])) {
        return $decoded;
      }
    }

    return $this->stateBuilder->buildFromItems($items);
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    // Non utilisé : extractFormValues() écrit directement sur $items.
    return $values;
  }

}
