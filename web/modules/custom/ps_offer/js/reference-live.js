/**
 * @file
 * Live reference preview for offer node forms.
 *
 * Computes a reference preview immediately in JavaScript from the configured
 * segment definitions. No AJAX call is made. The "auto" counter segment
 * (assigned on save via a database sequence) displays "?" placeholders.
 *
 * Supported segment types:
 *   - static : fixed static_value
 *   - custom : map field value through custom_map table
 *   - start  : substring of field value from a 1-based start index
 *   - date   : format a date field using YY/YYYY/MM/YYMM/YYMMDD key
 *   - auto   : server-side counter → rendered as "?????" placeholder
 */
(function (Drupal, drupalSettings, once) {
  'use strict';

  // ---------------------------------------------------------------------------
  // Utilities
  // ---------------------------------------------------------------------------

  /**
   * Uppercases and strips non-alphanumeric characters, matching PHP fitToLength.
   *
   * @param {string} value
   * @return {string}
   */
  function sanitize(value) {
    return String(value).toUpperCase().replace(/[^A-Z0-9]/g, '');
  }

  /**
   * Pads with 'X' or truncates a sanitized string to an exact length.
   *
   * @param {string} value
   * @param {number} length
   * @return {string}
   */
  function fitToLength(value, length) {
    value = sanitize(value);
    if (value.length < length) {
      value = value.padEnd(length, 'X');
    }
    return value.substring(0, length);
  }

  /**
   * Formats a raw field value (ISO date string or Unix timestamp in seconds)
   * using the same keys as OfferReferenceBuilder::DATE_FORMATS.
   *
   * @param {string} rawValue
   * @param {string} formatKey  YY | YYYY | MM | YYMM | YYMMDD
   * @return {string}
   */
  function formatDate(rawValue, formatKey) {
    let d;

    rawValue = String(rawValue).trim();
    if (/^\d{10}$/.test(rawValue)) {
      // Unix timestamp (seconds) — e.g. Scheduler's publish_on field.
      d = new Date(parseInt(rawValue, 10) * 1000);
    } else if (rawValue !== '') {
      d = new Date(rawValue);
    }

    if (!d || isNaN(d.getTime())) {
      d = new Date();
    }

    const yy   = d.getFullYear().toString().slice(-2);
    const yyyy = d.getFullYear().toString();
    const mm   = String(d.getMonth() + 1).padStart(2, '0');
    const dd   = String(d.getDate()).padStart(2, '0');

    switch (formatKey) {
      case 'YYYY':   return yyyy;
      case 'MM':     return mm;
      case 'YYMM':   return yy + mm;
      case 'YYMMDD': return yy + mm + dd;
      default:       return yy; // 'YY' is the default
    }
  }

  /**
   * Reads the current value of a Drupal form field by its machine name.
   * Tries several common Drupal widget name patterns.
   *
   * @param {HTMLFormElement} form
   * @param {string} fieldName  e.g. "field_transaction_types" or "publish_on"
   * @return {string}
   */
  function getFieldValue(form, fieldName) {
    if (!fieldName) {
      return '';
    }

    const patterns = [
      '[name="' + fieldName + '"]',
      '[name="' + fieldName + '[0][value]"]',
      '[name="' + fieldName + '[0][target_id]"]',
    ];

    for (const sel of patterns) {
      const el = form.querySelector(sel);
      if (!el) {
        continue;
      }
      if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked) {
        continue;
      }
      const val = el.value.trim();
      if (val !== '') {
        return val;
      }
    }

    // Fallback: any element whose name starts with fieldName[
    const fallback = form.querySelector('[name^="' + fieldName + '["]');
    if (fallback && fallback.value.trim() !== '') {
      return fallback.value.trim();
    }

    return '';
  }

  // ---------------------------------------------------------------------------
  // Core computation
  // ---------------------------------------------------------------------------

  /**
   * Computes the preview reference from current form field values.
   *
   * Returns a partial string where "auto" counter segments are represented
   * by '?' characters (e.g. "OVBUR26?????").
   *
   * @param {HTMLFormElement} form
   * @param {Array<Object>} segments  drupalSettings.psOfferReference.segments
   * @return {string}
   */
  function computeReference(form, segments) {
    const parts = [];

    for (const seg of segments) {
      const type        = seg.type || 'custom';
      const length      = Math.max(1, seg.length || 1);
      const sourceField = seg.sourceField || '';
      const options     = seg.options || {};

      let value;

      switch (type) {
        case 'static':
          value = fitToLength(options.staticValue || '', length);
          break;

        case 'custom': {
          const raw    = sanitize(getFieldValue(form, sourceField));
          const map    = options.customMap || {};
          const mapped = map[raw] || (raw.charAt(0)) || 'X';
          value = fitToLength(mapped, length);
          break;
        }

        case 'start': {
          const raw      = sanitize(getFieldValue(form, sourceField));
          const startIdx = Math.max(1, options.startIndex || 1) - 1;
          value = fitToLength(raw.substring(startIdx), length);
          break;
        }

        case 'date':
          value = fitToLength(
            formatDate(getFieldValue(form, sourceField), options.dateFormat || 'YY'),
            length
          );
          break;

        case 'auto':
          // Counter is assigned server-side on save; show placeholder.
          value = '?'.repeat(length);
          break;

        default: {
          const raw = sanitize(getFieldValue(form, sourceField));
          value = fitToLength(raw, length);
        }
      }

      parts.push(value);
    }

    return parts.join('');
  }

  // ---------------------------------------------------------------------------
  // Drupal behavior
  // ---------------------------------------------------------------------------

  Drupal.behaviors.psOfferReferenceLive = {
    attach: function attach(context) {
      const settings = drupalSettings.psOfferReference || {};
      const segments = Array.isArray(settings.segments) ? settings.segments : [];

      if (segments.length === 0) {
        return;
      }

      once(
        'ps-offer-reference-live',
        'form.node-offer-form, form.node-offer-edit-form',
        context
      ).forEach(function (form) {
        const referenceInput = form.querySelector('[name="field_reference[0][value]"]');
        if (!referenceInput) {
          return;
        }

        // Existing non-empty references are locked (also set readonly in PHP).
        if (referenceInput.value.trim() !== '') {
          referenceInput.readOnly = true;
          return;
        }

        // Track whether the current value is managed by this live preview.
        let isAutoManagedValue = true;

        // Derive watched fields from segment definitions (deduplicated).
        const watchedFields = [
          ...new Set(
            segments
              .filter(function (seg) { return seg.sourceField; })
              .map(function (seg) { return seg.sourceField; })
          ),
        ];

        /**
         * Recomputes and displays the preview reference.
         * Does nothing when a manual value was entered by the editor.
         */
        function refreshReference() {
          if (!isAutoManagedValue && referenceInput.value.trim() !== '') {
            return;
          }
          referenceInput.value = computeReference(form, segments);
          isAutoManagedValue = true;
        }

        // If editor types in the reference field, stop live overwrites.
        referenceInput.addEventListener('input', function () {
          isAutoManagedValue = referenceInput.value.trim() === '';
        });

        // Attach change/input listeners to every watched source field element.
        watchedFields.forEach(function (fieldName) {
          const selectors = [
            '[name="' + fieldName + '"]',
            '[name="' + fieldName + '[0][value]"]',
            '[name="' + fieldName + '[0][target_id]"]',
            '[name^="' + fieldName + '["]',
          ];

          selectors.forEach(function (sel) {
            form.querySelectorAll(sel).forEach(function (el) {
              el.addEventListener('change', refreshReference);
              if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA' || el.tagName === 'SELECT') {
                el.addEventListener('input', refreshReference);
              }
            });
          });
        });

        // Compute an initial preview if source fields already have values.
        refreshReference();
      });
    },
  };

}(Drupal, drupalSettings, once));
