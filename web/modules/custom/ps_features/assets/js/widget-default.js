/**
 * @file
 * Default widget behavior for ps_feature_value field type.
 *
 * Provides dynamic field visibility, validation, and enhanced UX for the
 * default feature value widget with client-side field generation.
 */

(function ($, Drupal, drupalSettings, once) {

  'use strict';

  /**
   * Behavior for ps_feature_value default widget.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.psFeatureValueDefaultWidget = {
    attach: function (context, settings) {
      const featuresData = (drupalSettings && drupalSettings.psFeatures) ? drupalSettings.psFeatures : {};
      const features = featuresData.features || {};
      const dictionaries = featuresData.dictionaries || {};
      const widgetSettings = featuresData.settings || {};

      // High-level attach log.
      // eslint-disable-next-line no-console
      console.log('[PS Features] attach()', {
        context: context,
        featuresCount: Object.keys(features).length,
        dictionaryTypes: Object.keys(dictionaries),
        widgetSettings: widgetSettings
      });

      // Debug: log data to console.
      console.log('PS Features Widget - Data loaded:', {
        features: features,
        dictionaries: dictionaries,
        widgetSettings: widgetSettings
      });

      // Process each widget. Target the Drupal field wrapper class as primary,
      // and fall back to the custom class if present.
      const widgets = once(
        'ps-feature-value-default',
        '.field--widget-ps-feature-value-default, .ps-feature-value-default',
        context
      );
      // eslint-disable-next-line no-console
      console.log('[PS Features] widgets found:', widgets.length);
      widgets.forEach(function (element) {
        // eslint-disable-next-line no-console
        console.log('[PS Features] init widget element', element);

        const $widget = $(element);
        // Feature select: prefer explicit class, fallback to name suffix.
        let $featureSelect = $widget.find('.feature-select');
        if ($featureSelect.length === 0) {
          $featureSelect = $widget.find('select[name$="[feature_id]"]');
        }
        const $valueFieldsContainer = $widget.find('.value-fields-container');
        const $valueType = $widget.find('.field-value-type');
        const $dictionaryType = $widget.find('.field-dictionary-type');

        // eslint-disable-next-line no-console
        console.log('[PS Features] element refs:', {
          featureSelect: $featureSelect.length,
          valueFieldsContainer: $valueFieldsContainer.length,
          valueType: $valueType.length,
          dictionaryType: $dictionaryType.length
        });

        // Field wrappers.
        const $flagWrapper = $widget.find('.field-wrapper-flag');
        const $yesnoWrapper = $widget.find('.field-wrapper-yesno');
        const $booleanWrapper = $widget.find('.field-wrapper-boolean');
        const $stringWrapper = $widget.find('.field-wrapper-string');
        const $dictionaryWrapper = $widget.find('.field-wrapper-dictionary');
        const $numericWrapper = $widget.find('.field-wrapper-numeric');
        const $rangeWrapper = $widget.find('.field-wrapper-range');
        const $description = $widget.find('.feature-description');
        const $unitSuffix = $widget.find('.unit-suffix');
        const $complementWrapper = $widget.find('.field-wrapper-complement');
        const $complementInput = $widget.find('.field-complement');

        // Field inputs.
        const $booleanInput = $widget.find('#edit-value-fields-value-boolean, .field-boolean');
        const $stringInput = $widget.find('.field-string');
        const $dictionarySelect = $widget.find('.field-dictionary');
        const $numericInput = $widget.find('.field-numeric');
        const $rangeMin = $widget.find('.field-range-min');
        const $rangeMax = $widget.find('.field-range-max');

        /**
         * Update visible fields based on selected feature.
         */
        function updateFields() {
          const featureId = $featureSelect.val();
          // eslint-disable-next-line no-console
          console.log('[PS Features] updateFields()', { featureId: featureId, exists: !!features[featureId] });

          // Hide all fields first.
          $flagWrapper.hide();
          $yesnoWrapper.hide();
          $booleanWrapper.hide();
          $stringWrapper.hide();
          $dictionaryWrapper.hide();
          $numericWrapper.hide();
          $rangeWrapper.hide();
          $description.hide();
          $unitSuffix.hide();
          $complementWrapper.hide();
          $valueFieldsContainer.removeClass('has-feature-selected');

          if (!featureId || !features[featureId]) {
            $valueType.val('');
            $dictionaryType.val('');
            // eslint-disable-next-line no-console
            console.log('[PS Features] no feature selected — hide all');
            return;
          }

          const feature = features[featureId];
          const valueType = feature.value_type;

          // eslint-disable-next-line no-console
          console.log('[PS Features] selected feature', {
            id: featureId,
            label: feature.label,
            valueType: valueType,
            dictionaryType: feature.dictionary_type,
            unit: feature.unit
          });

          // Update hidden fields.
          $valueType.val(valueType);
          $dictionaryType.val(feature.dictionary_type || '');

          // Show description if enabled.
          if (widgetSettings.showDescription && feature.description) {
            $description.html(feature.description).show();
            // eslint-disable-next-line no-console
            console.log('[PS Features] show description');
          }

          $valueFieldsContainer.addClass('has-feature-selected');
          // Complement is available for all features when one is selected.
          $complementWrapper.show();
          if (widgetSettings.placeholder_text) {
            $complementInput.attr('placeholder', widgetSettings.placeholder_text);
          }

          // Show appropriate field based on value type.
          // eslint-disable-next-line no-console
          console.log('[PS Features] show fields for type:', valueType);

          switch (valueType) {
            case 'flag':
              // Flag: presence means TRUE, just show the message.
              $flagWrapper.show();
              // eslint-disable-next-line no-console
              console.log('[PS Features] show flag message (presence = TRUE)');
              break;

            case 'yesno':
            case 'boolean':
              // Yesno: show checkbox for yes/no choice.
              $yesnoWrapper.show();
              // eslint-disable-next-line no-console
              console.log('[PS Features] show yesno checkbox');
              break;

            case 'string':
              $stringWrapper.show();
              if (widgetSettings.placeholder_text) {
                $stringInput.attr('placeholder', widgetSettings.placeholder_text);
              }
              // eslint-disable-next-line no-console
              console.log('[PS Features] show string');
              break;

            case 'dictionary':
              // Populate dictionary options.
              if (feature.dictionary_type && dictionaries[feature.dictionary_type]) {
                $dictionarySelect.empty();
                $dictionarySelect.append($('<option>').val('').text(Drupal.t('- Select -')));

                $.each(dictionaries[feature.dictionary_type], function (value, label) {
                  $dictionarySelect.append($('<option>').val(value).text(label));
                });

                // Copy value from string field to dictionary select.
                const currentValue = $stringInput.val();
                if (currentValue) {
                  $dictionarySelect.val(currentValue);
                }

                $dictionaryWrapper.show();
                // eslint-disable-next-line no-console
                console.log('[PS Features] show dictionary with options:', $dictionarySelect.find('option').length);
              }
              break;

            case 'numeric':
              $numericWrapper.show();
              if (widgetSettings.placeholder_text) {
                $numericInput.attr('placeholder', widgetSettings.placeholder_text);
              }

              // Apply validation rules.
              if (feature.validation_rules) {
                if (feature.validation_rules.min !== undefined) {
                  $numericInput.attr('min', feature.validation_rules.min);
                }
                if (feature.validation_rules.max !== undefined) {
                  $numericInput.attr('max', feature.validation_rules.max);
                }
              }

              // Show unit.
              if (feature.unit) {
                $unitSuffix.text(feature.unit).show();
                $numericInput.after($unitSuffix);
              }
              // eslint-disable-next-line no-console
              console.log('[PS Features] show numeric');
              break;

            case 'range':
              $rangeWrapper.show();

              // Show unit.
              if (feature.unit) {
                $unitSuffix.text(feature.unit).show();
                $rangeMax.after($unitSuffix);
              }
              // eslint-disable-next-line no-console
              console.log('[PS Features] show range');
              break;
          }
        }

        // Update fields on feature selection change.
        // Bind change with robust delegation in case of AJAX-added rows.
        $widget.on('change', $featureSelect.selector || 'select[name$="[feature_id]"]', function () {
          // eslint-disable-next-line no-console
          console.log('[PS Features] feature change', $(this).val());
          // When delegated, refresh $featureSelect value by closest lookup.
          const $currentWidget = $(this).closest('.field--widget-ps-feature-value-default, .ps-feature-value-default');
          if ($currentWidget.length) {
            $featureSelect = $currentWidget.find('.feature-select');
            if ($featureSelect.length === 0) {
              $featureSelect = $currentWidget.find('select[name$="[feature_id]"]');
            }
          }
          updateFields();

          // Auto-focus first visible input.
          setTimeout(function () {
            const $firstInput = $valueFieldsContainer.find('input:visible, select:visible').first();
            if ($firstInput.length) {
              $firstInput.focus();
            }
          }, 100);
        });

        // Sync dictionary select value back to string field (for form submission).
        $dictionarySelect.on('change', function () {
          // eslint-disable-next-line no-console
          console.log('[PS Features] dictionary change', $(this).val());
          $stringInput.val($(this).val());
        });

        // Range validation.
        function validateRange() {
          const min = parseFloat($rangeMin.val());
          const max = parseFloat($rangeMax.val());

          if (!isNaN(min) && !isNaN(max) && min > max) {
            $rangeMax.addClass('error');
            $rangeMax.attr('title', Drupal.t('Maximum must be greater than minimum'));
          }
          else {
            $rangeMax.removeClass('error');
            $rangeMax.removeAttr('title');
          }
          // eslint-disable-next-line no-console
          console.log('[PS Features] validate range', { min: $rangeMin.val(), max: $rangeMax.val() });
        }

        $rangeMin.on('input change', validateRange);
        $rangeMax.on('input change', validateRange);

        // Initial state: show fields if feature already selected.
        // Initial draw
        updateFields();
      });
    }
  };

})(jQuery, Drupal, drupalSettings, once);
