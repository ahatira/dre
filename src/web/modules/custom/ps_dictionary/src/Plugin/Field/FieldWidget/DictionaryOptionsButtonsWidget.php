<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsButtonsWidget;

/**
 * Plugin implementation of the 'ps_dictionary_options_buttons' widget.
 *
 * @FieldWidget(
 *   id = "ps_dictionary_options_buttons",
 *   label = @Translation("Check boxes/radio buttons"),
 *   field_types = {
 *     "ps_dictionary"
 *   },
 *   multiple_values = TRUE
 * )
 */
final class DictionaryOptionsButtonsWidget extends OptionsButtonsWidget {
}
