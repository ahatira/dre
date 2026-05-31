/**
 * @file
 * ps_context — Dynamic rules engine for the offer node form.
 *
 * Reads rules from drupalSettings.psContext.rules (serialized from
 * ps_context_rule config entities by OfferMatrixRules.php) and evaluates
 * them client-side on every controlling field change.
 *
 * Architecture:
 *   1. Collect all elements that any rule might affect.
 *   2. Reset them to their default state (visible, optional, enabled).
 *   3. Apply all active rules in weight order (server already sorted them).
 *
 * Supported action types:
 *   show_tab / hide_tab       — horizontal-tab group visibility
 *   show_field / hide_field   — field wrapper visibility
 *   set_required / set_optional — HTML required attribute
 *   disable_field             — disabled attribute
 *   set_default               — set field value when condition becomes true
 *   hide_surface_delta        — hide a specific delta row of field_surfaces
 */
(function ($, Drupal, drupalSettings, once) {
  'use strict';

  // -------------------------------------------------------------------------
  // Selectors for fields that can act as conditions (driving fields).
  // -------------------------------------------------------------------------
  const FIELD_SELECTORS = {
    field_asset_type: 'select[name="field_asset_type"]',
    field_operation_type: 'select[name="field_operation_type"]',
    field_divisible: 'input[name="field_divisible[value]"]',
  };

  // -------------------------------------------------------------------------
  // Behavior entry point.
  // -------------------------------------------------------------------------
  Drupal.behaviors.psContextOfferForm = {
    attach(context) {
      once('ps-context-offer', 'body', context).forEach(() => {
        const rules = (drupalSettings.psContext && drupalSettings.psContext.rules) || [];

        if (!rules.length) {
          return;
        }

        // Collect all controlling field selectors used across all rules.
        const controllingSelectors = new Set();
        rules.forEach((rule) => {
          (rule.conditions || []).forEach((c) => {
            const sel = FIELD_SELECTORS[c.field_name];
            if (sel) {
              controllingSelectors.add(sel);
            }
          });
        });

        // Listen for changes on all driving fields.
        controllingSelectors.forEach((sel) => {
          $(sel).on('change.psContext', applyAllRules);
        });

        // Apply immediately on page load.
        applyAllRules();

        // ---------------------------------------------------------------
        // Core evaluation loop.
        // ---------------------------------------------------------------

        function applyAllRules() {
          // Step 1 — collect every element that any rule might touch.
          const affectedTabs = new Set();
          const affectedFields = new Set();
          const affectedRequired = new Set();
          const affectedDisabled = new Set();
          const affectedSurfaceDeltas = new Set();

          rules.forEach((rule) => {
            (rule.actions || []).forEach((action) => {
              switch (action.action_type) {
                case 'show_tab':
                case 'hide_tab':
                  affectedTabs.add(action.target);
                  break;
                case 'show_field':
                case 'hide_field':
                  affectedFields.add(action.target);
                  break;
                case 'set_required':
                case 'set_optional':
                  affectedRequired.add(action.target);
                  break;
                case 'disable_field':
                  affectedDisabled.add(action.target);
                  break;
                case 'hide_surface_delta':
                  affectedSurfaceDeltas.add(action.value);
                  break;
              }
            });
          });

          // Step 2 — reset all affected elements to their default state.
          affectedTabs.forEach((tab) => toggleTab(tab, true));
          affectedFields.forEach((field) => setFieldVisible(field, true));
          affectedRequired.forEach((field) => setFieldRequired(field, false));
          affectedDisabled.forEach((field) => setFieldDisabled(field, false));
          affectedSurfaceDeltas.forEach((delta) => toggleSurfaceRow(parseInt(delta, 10), true));

          // Step 3 — apply every rule whose conditions match (weight order,
          // last applied wins when two rules conflict on the same element).
          rules.forEach((rule) => {
            if (evaluateRule(rule)) {
              (rule.actions || []).forEach(applyAction);
            }
          });

          // Keep tab panes aligned with tab visibility/active state.
          syncTabPanels();
        }

        // ---------------------------------------------------------------
        // Rule / condition evaluation.
        // ---------------------------------------------------------------

        function evaluateRule(rule) {
          const conditions = rule.conditions || [];
          if (!conditions.length) {
            return true; // Unconditional rule — always fires.
          }
          const results = conditions.map(evaluateCondition);
          return rule.conditions_logic === 'OR'
            ? results.some(Boolean)
            : results.every(Boolean);
        }

        function evaluateCondition(condition) {
          const val = getFieldValue(condition.field_name);
          const expected = condition.value || '';
          switch (condition.operator) {
            case 'equals':
              return val === expected;
            case 'not_equals':
              return val !== expected;
            case 'empty':
              return val === '' || val === null || val === undefined;
            case 'filled':
              return val !== '' && val !== null && val !== undefined;
            case 'contains':
              return typeof val === 'string' && val.includes(expected);
            default:
              return false;
          }
        }

        function getFieldValue(fieldName) {
          const sel = FIELD_SELECTORS[fieldName];
          if (!sel) {
            return '';
          }
          const $el = $(sel);
          if ($el.is(':checkbox')) {
            return $el.is(':checked') ? '1' : '0';
          }
          const val = $el.val();
          // Drupal's empty option for select widgets is often '_none'.
          return (val === '_none' || val === null || val === undefined) ? '' : val;
        }

        // ---------------------------------------------------------------
        // Action dispatch.
        // ---------------------------------------------------------------

        function applyAction(action) {
          switch (action.action_type) {
            case 'show_tab':
              toggleTab(action.target, true);
              break;
            case 'hide_tab':
              toggleTab(action.target, false);
              break;
            case 'show_field':
              setFieldVisible(action.target, true);
              break;
            case 'hide_field':
              setFieldVisible(action.target, false);
              break;
            case 'set_required':
              setFieldRequired(action.target, true);
              break;
            case 'set_optional':
              setFieldRequired(action.target, false);
              break;
            case 'disable_field':
              setFieldDisabled(action.target, true);
              break;
            case 'set_default':
              setFieldDefault(action.target, action.value);
              break;
            case 'hide_surface_delta':
              toggleSurfaceRow(parseInt(action.value, 10), false);
              break;
          }
        }

        // ---------------------------------------------------------------
        // DOM helpers — tabs.
        // ---------------------------------------------------------------

        /**
         * Shows or hides a horizontal-tab group by its panel DOM id.
         *
         * @param {string}  panelId  Tab panel id, e.g. 'group_surface' (without edit- prefix).
         * @param {boolean} visible
         */
        function toggleTab(panelId, visible) {
          // Drupal renders panel ids as 'edit-group-xxx', href="#edit-group-xxx".
          const domId = 'edit-' + panelId.replace(/_/g, '-');
          const $link = $('a[href="#' + domId + '"]');
          if (!$link.length) {
            return;
          }
          const $li = $link.closest('li');
          if (visible) {
            $li.show();
          }
          else {
            // Move focus to Identification tab if this tab is currently active.
            const isActive =
              $li.hasClass('ui-tabs-active') ||
              $li.hasClass('is-active') ||
              $li.hasClass('horizontal-tab-button-selected');
            if (isActive) {
              $('a[href="#edit-group-identification"]').trigger('click');
            }
            $li.hide();
          }
        }

        /**
         * Synchronizes tab panels with their corresponding tab menu state.
         * Ensures only the active visible tab pane is shown.
         */
        function syncTabPanels() {
          $('a[href^="#edit-group-"]').each(function () {
            const $link = $(this);
            const href = $link.attr('href');
            if (!href) {
              return;
            }
            const $li = $link.closest('li');
            const $panel = $(href);
            if (!$panel.length) {
              return;
            }

            const isTabVisible = $li.is(':visible');
            const isActive =
              $li.hasClass('ui-tabs-active') ||
              $li.hasClass('is-active') ||
              $li.hasClass('horizontal-tab-button-selected') ||
              $li.hasClass('selected');

            if (isTabVisible && isActive) {
              $panel.show();
            }
            else {
              $panel.hide();
            }
          });
        }

        // ---------------------------------------------------------------
        // DOM helpers — fields.
        // ---------------------------------------------------------------

        /**
         * Shows or hides a field wrapper element.
         *
         * @param {string}  fieldName  Field machine name, e.g. 'field_budget_period'.
         * @param {boolean} visible
         */
        function setFieldVisible(fieldName, visible) {
          // Field wrappers have data-drupal-selector="edit-{field-name}-wrapper"
          // or a matching class. Use the most reliable selector.
          const selector = '[data-drupal-selector="edit-' + fieldName.replace(/_/g, '-') + '-wrapper"]';
          const $el = $(selector);
          if (!$el.length) {
            return;
          }
          if (visible) {
            $el.show();
          }
          else {
            $el.hide();
          }
        }

        /**
         * Adds or removes the HTML5 required attribute from a field's inputs.
         *
         * @param {string}  fieldName
         * @param {boolean} required
         */
        function setFieldRequired(fieldName, required) {
          const selector = '[data-drupal-selector="edit-' + fieldName.replace(/_/g, '-') + '-wrapper"]';
          const $wrapper = $(selector);
          if (!$wrapper.length) {
            return;
          }
          $wrapper.find('input, select, textarea').each(function () {
            $(this).prop('required', required);
          });
          // Toggle Drupal's 'required' marker on the label.
          if (required) {
            $wrapper.find('label').addClass('js-form-required form-required');
          }
          else {
            $wrapper.find('label').removeClass('js-form-required form-required');
          }
        }

        /**
         * Enables or disables a field's inputs.
         *
         * @param {string}  fieldName
         * @param {boolean} disabled
         */
        function setFieldDisabled(fieldName, disabled) {
          const selector = '[data-drupal-selector="edit-' + fieldName.replace(/_/g, '-') + '-wrapper"]';
          const $wrapper = $(selector);
          if (!$wrapper.length) {
            return;
          }
          $wrapper.find('input, select, textarea').prop('disabled', disabled);
        }

        /**
         * Sets a field's value when the driving condition first becomes true
         * and the field is currently empty.
         *
         * @param {string} fieldName
         * @param {string} value
         */
        function setFieldDefault(fieldName, value) {
          const selector = '[data-drupal-selector="edit-' + fieldName.replace(/_/g, '-') + '-wrapper"]';
          const $wrapper = $(selector);
          if (!$wrapper.length || value === '') {
            return;
          }
          const $input = $wrapper.find('input:not([type="hidden"]), select, textarea').first();
          const currentVal = $input.length ? $input.val() : null;
          if ($input.length && (currentVal === '' || currentVal === '_none')) {
            $input.val(value);
          }
        }

        // ---------------------------------------------------------------
        // DOM helpers — surface delta rows.
        // ---------------------------------------------------------------

        /**
         * Shows or hides a specific delta row of the field_surfaces table widget.
         *
         * Delta 0 = TOTAL (always visible by default).
         * Delta 1 = DISPO.
         * Delta 2 = ETREF.
         *
         * @param {number}  delta
         * @param {boolean} visible
         */
        function toggleSurfaceRow(delta, visible) {
          const $row = $(
            '[data-drupal-selector="edit-field-surfaces-' + delta + '"]'
          ).closest('tr');
          if (!$row.length) {
            return;
          }
          if (visible) {
            $row.show();
          }
          else {
            $row.hide();
          }
        }

      }); // end once
    },
  };

})(jQuery, Drupal, drupalSettings, once);
