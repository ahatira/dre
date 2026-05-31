<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsSelectWidget;

/**
 * Plugin implementation of the 'ps_dictionary_options_select' widget.
 *
 * @FieldWidget(
 *   id = "ps_dictionary_options_select",
 *   label = @Translation("Select list"),
 *   field_types = {
 *     "ps_dictionary"
 *   },
 *   multiple_values = TRUE
 * )
 */
final class DictionaryOptionsSelectWidget extends OptionsSelectWidget {
}
