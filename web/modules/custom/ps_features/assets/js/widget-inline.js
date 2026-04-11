/**
 * @file
 * Inline widget behavior for ps_feature_value field type.
 *
 * Provides enhanced UX for compact inline widget with auto-hide/show
 * of irrelevant fields and keyboard shortcuts.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Behavior for ps_feature_value inline widget.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.psFeatureValueInlineWidget = {
    attach: function (context) {
      // Use Drupal's once API (Drupal 9.2+).
      once('ps-feature-value-inline', '.ps-feature-value-inline', context).forEach(function (element) {
        const $widget = $(element);
        const $featureSelect = $widget.find('.feature-select');

        // Hide all value fields initially if no feature selected.
        const updateFieldVisibility = function () {
          const hasFeature = $featureSelect.val() !== '';
          $widget.find('.feature-value, .feature-range-min, .feature-range-max, .range-separator').toggle(hasFeature);
        };

        updateFieldVisibility();
        $featureSelect.on('change', updateFieldVisibility);

        // Range validation.
        const $rangeMin = $widget.find('.feature-range-min');
        const $rangeMax = $widget.find('.feature-range-max');

        if ($rangeMin.length && $rangeMax.length) {
          const validateRange = function () {
            const min = parseFloat($rangeMin.val());
            const max = parseFloat($rangeMax.val());

            if (!isNaN(min) && !isNaN(max) && min > max) {
              $rangeMax.addClass('error');
            } else {
              $rangeMax.removeClass('error');
            }
          };

          $rangeMin.on('input', validateRange);
          $rangeMax.on('input', validateRange);
        }

        // Auto-focus value field after feature selection.
        $featureSelect.on('change', function () {
          const $valueField = $widget.find('.feature-value').filter(':visible').first();
          if ($valueField.length) {
            $valueField.focus();
          }
        });

        // Keyboard shortcut: Alt+Enter to add another item.
        $widget.find('input, select').on('keydown', function (e) {
          if (e.altKey && e.key === 'Enter') {
            e.preventDefault();
            const $addButton = $widget.closest('.field-multiple-table, .field--widget-ps-feature-value-inline').find('.field-add-more-submit');
            if ($addButton.length) {
              $addButton.click();
            }
          }
        });
      });
    }
  };

})(jQuery, Drupal);
