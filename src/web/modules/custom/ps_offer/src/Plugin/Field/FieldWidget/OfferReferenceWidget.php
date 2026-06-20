<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\Service\OfferReferenceManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @FieldWidget(
 *   id = "ps_offer_reference_widget",
 *   label = @Translation("Offer reference (auto/manual)"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
final class OfferReferenceWidget extends WidgetBase {

  public static function defaultSettings(): array {
    return [
      'toggle_button_text' => 'Auto',
      'preview_manual_message' => 'Auto generation is off. Enter a custom reference value.',
    ] + parent::defaultSettings();
  }

  public function __construct(
    string $plugin_id,
    mixed $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    private readonly OfferReferenceManagerInterface $referenceManager,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('Drupal\\ps_offer\\Service\\OfferReferenceManagerInterface'),
    );
  }

  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $current_value = isset($items[$delta]->value) ? (string) $items[$delta]->value : '';
    $entity = $items->getEntity();

    $values = $form_state->getValues();
    $submitted_mode = $values['field_reference_auto'][0]['value'] ?? $values['field_reference_auto']['value'] ?? NULL;
    $auto_enabled = $this->isAutoModeEnabled($submitted_mode);
    if ($submitted_mode === NULL && $entity instanceof NodeInterface) {
      if ($entity->hasField('field_reference_auto')) {
        $auto_enabled = $this->isAutoModeEnabled($entity->get('field_reference_auto')->value ?? NULL);
      }
    }

    $context = $this->referenceManager->buildContextFromFormValues($values);
    if ($context['field_operation_type'] === '' || $context['field_asset_type'] === '') {
      $user_input = $form_state->getUserInput();
      if (is_array($user_input)) {
        $input_context = $this->referenceManager->buildContextFromFormValues($user_input);
        if ($context['field_operation_type'] === '' && $input_context['field_operation_type'] !== '') {
          $context['field_operation_type'] = $input_context['field_operation_type'];
        }
        if ($context['field_asset_type'] === '' && $input_context['field_asset_type'] !== '') {
          $context['field_asset_type'] = $input_context['field_asset_type'];
        }
      }
    }
    if (($context['field_operation_type'] === '' || $context['field_asset_type'] === '') && $entity instanceof NodeInterface) {
      $node_context = $this->referenceManager->buildContextFromNode($entity);
      if ($context['field_operation_type'] === '') {
        $context['field_operation_type'] = $node_context['field_operation_type'];
      }
      if ($context['field_asset_type'] === '') {
        $context['field_asset_type'] = $node_context['field_asset_type'];
      }
    }
    $preview = $this->referenceManager->generateForBundle('offer', $context);

    $element['#attached']['library'][] = 'ps_offer/reference_widget';

    $mode_selector = ':input[name="field_reference_auto[0][value]"]';

    $element['row'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['ps-offer-reference-row'],
      ],
      '#weight' => 0,
    ];

    $element['row']['value'] = [
      '#type' => 'textfield',
      '#title' => $this->fieldDefinition->getLabel(),
      '#default_value' => $current_value,
      '#parents' => [$items->getName(), $delta, 'value'],
      '#size' => 15,
      '#maxlength' => 128,
      // Auto mode generates the value on presave; do not block submit on empty input.
      '#required' => $this->fieldDefinition->isRequired() && !$auto_enabled,
      '#disabled' => $auto_enabled,
      '#states' => [
        'disabled' => [
          $mode_selector => ['checked' => TRUE],
        ],
        'required' => [
          $mode_selector => ['checked' => FALSE],
        ],
        'optional' => [
          $mode_selector => ['checked' => TRUE],
        ],
      ],
      '#attributes' => [
        'class' => ['ps-offer-reference-input'],
      ],
    ];

    $element['row']['auto_toggle'] = [
      '#type' => 'checkbox',
      '#title' => $this->t((string) $this->getSetting('toggle_button_text')),
      '#default_value' => $auto_enabled ? 1 : 0,
      '#return_value' => 1,
      '#parents' => ['field_reference_auto', 0, 'value'],
      '#wrapper_attributes' => [
        'class' => ['ps-offer-reference-toggle-item'],
      ],
      '#attributes' => [
        'class' => ['ps-offer-reference-auto-toggle'],
      ],
      '#weight' => 10,
    ];

    $element['help'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['ps-offer-reference-help'],
      ],
      '#weight' => 1,
    ];

    $element['help']['text'] = [
      '#markup' => '<div class="form-item__description">' . Html::escape((string) $this->t('Enable Auto to generate the value from the active pattern, or disable it to edit manually.')) . '</div>',
    ];

    $element['preview'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['ps-offer-reference-preview', $auto_enabled ? 'is-auto' : 'is-manual'],
      ],
      '#weight' => 5,
    ];

    $element['preview']['title'] = [
      '#markup' => '<strong>' . Html::escape((string) $this->t('Reference preview')) . '</strong>',
    ];

    $element['preview']['auto'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => [
          $mode_selector => ['checked' => TRUE],
        ],
      ],
    ];

    $element['preview']['auto']['value'] = [
      '#markup' => $preview !== ''
        ? '<code class="ps-offer-reference-preview-code">' . Html::escape($preview) . '</code>'
        : '<span class="ps-offer-reference-preview-hint">' . Html::escape((string) $this->t('No preview available yet. Fill operation type and asset type.')) . '</span>',
    ];

    $element['preview']['manual'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => [
          $mode_selector => ['checked' => FALSE],
        ],
      ],
    ];

    $element['preview']['manual']['value'] = [
      '#markup' => '<span class="ps-offer-reference-preview-hint">' . Html::escape((string) $this->t((string) $this->getSetting('preview_manual_message'))) . '</span>',
    ];

    return $element;
  }

  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    $submitted_mode = $form_state->getValue(['field_reference_auto', 0, 'value']);
    if ($this->isAutoModeEnabled($submitted_mode)) {
      $current = trim((string) ($values[0]['value'] ?? ''));
      if ($current === '') {
        $context = $this->referenceManager->buildContextFromFormValues($form_state->getValues());
        $generated = $this->referenceManager->generateForBundle('offer', $context);
        if ($generated !== '') {
          $values[0]['value'] = $generated;
        }
      }
    }

    return parent::massageFormValues($values, $form, $form_state);
  }

  private function isAutoModeEnabled(mixed $rawMode): bool {
    if (!is_scalar($rawMode)) {
      return TRUE;
    }

    $normalized = mb_strtolower(trim((string) $rawMode));
    if ($normalized === '') {
      return TRUE;
    }

    return in_array($normalized, ['1', 'true', 'auto', 'on', 'yes'], TRUE);
  }

  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $elements = parent::settingsForm($form, $form_state);

    $elements['toggle_button_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Toggle button text'),
      '#default_value' => $this->getSetting('toggle_button_text'),
      '#maxlength' => 64,
      '#required' => TRUE,
    ];

    $elements['preview_manual_message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Manual preview message'),
      '#default_value' => $this->getSetting('preview_manual_message'),
      '#maxlength' => 255,
      '#required' => TRUE,
    ];

    return $elements;
  }

  public function settingsSummary(): array {
    $summary = parent::settingsSummary();
    $summary[] = $this->t('Toggle button text: @label', ['@label' => (string) $this->getSetting('toggle_button_text')]);
    return $summary;
  }

}