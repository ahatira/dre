<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Plugin\Field\FieldWidget;

use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'ps_dictionary_autocomplete' widget.
 *
 * @FieldWidget(
 *   id = "ps_dictionary_autocomplete",
 *   label = @Translation("Autocomplete"),
 *   field_types = {
 *     "ps_dictionary"
 *   }
 * )
 */
class DictionaryItemWidget extends WidgetBase {

  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $value = isset($items[$delta]->value) ? $items[$delta]->value : '';
    $dictionary_type = $this->getFieldSetting('dictionary_type') ?? '';

    $autocompleteRoute = [];
    if ($dictionary_type !== '') {
      $autocompleteRoute['#autocomplete_route_name'] = 'ps_dictionary.autocomplete';
      $autocompleteRoute['#autocomplete_route_parameters'] = ['dictionary_type' => $dictionary_type];
    }

    $element['value'] = [
      '#type' => 'textfield',
      '#title' => $this->fieldDefinition->getLabel(),
      '#default_value' => $value,
      '#maxlength' => 16,
      '#required' => $element['#required'] ?? FALSE,
      '#description' => $dictionary_type === '' ? t('Configure a dictionary type on the field settings to enable autocomplete.') : NULL,
    ];
    $element['value'] += $autocompleteRoute;

    return $element;
  }
}
