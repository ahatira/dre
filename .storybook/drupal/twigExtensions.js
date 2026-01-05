/**
 * Twig Extensions for Drupal Compatibility in Storybook
 *
 * Provides additional Drupal-specific Twig functions for use in Storybook.
 * Note: Most Drupal functions and filters are already provided by twig-drupal-filters.
 * This module adds/extends functions that are missing or need improvements.
 *
 * Already provided by twig-drupal-filters:
 * - Functions: link(), attach_library(), active_theme()
 * - Filters: t(), trans(), placeholder(), without(), clean_class(), clean_id(), render(), path(), url(), format_date()
 */

import Twig from 'twig';

/**
 * create_attribute() function
 * Creates a Drupal Attribute object for building HTML attributes with chainable methods.
 * This is a Drupal 8+ function that simulates the Drupal Attribute class.
 *
 * @param {object} attributes - Optional initial attributes
 * @returns {object} Attribute-like object with chainable methods
 */
export const create_attribute = Twig.extendFunction('create_attribute', function (attributes) {
  // Return an Attribute-like object
  const attr = attributes || {};
  const classes = [];

  return {
    addClass: function (newClasses) {
      if (Array.isArray(newClasses)) {
        classes.push(...newClasses.filter((c) => c));
      } else if (newClasses) {
        classes.push(newClasses);
      }
      return this;
    },
    removeClass: function (classToRemove) {
      const index = classes.indexOf(classToRemove);
      if (index > -1) {
        classes.splice(index, 1);
      }
      return this;
    },
    setAttribute: function (name, value) {
      attr[name] = value;
      return this;
    },
    removeAttribute: function (name) {
      delete attr[name];
      return this;
    },
    toString: function () {
      let result = '';

      // Add classes
      if (classes.length > 0) {
        result += ` class="${classes.join(' ')}"`;
      }

      // Add other attributes
      Object.keys(attr).forEach((key) => {
        if (key !== 'class') {
          result += ` ${key}="${attr[key]}"`;
        }
      });

      return result;
    },
  };
});

/**
 * filter filter
 * Filters elements of an array using a callback or filters out falsy values.
 * When called without arguments, it removes falsy values (null, false, 0, '', etc).
 *
 * @param {array} array - Array to filter
 * @param {function|string} callback - Optional callback function or filter name
 * @returns {array} Filtered array
 */
export const filter = Twig.extendFilter('filter', function (array, callback) {
  if (!Array.isArray(array)) {
    return array;
  }

  // If no callback provided, filter out falsy values
  if (!callback) {
    return array.filter((item) => item != null && item !== '' && item !== false && item !== 0);
  }

  // If callback is a function, use it
  if (typeof callback === 'function') {
    return array.filter(callback);
  }

  // If callback is a string (filter name), use it as a method
  if (typeof callback === 'string') {
    return array.filter((item) => {
      // Support common filter methods
      if (callback === 'empty') {
        return !item;
      }
      if (callback === 'length') {
        return item && item.length > 0;
      }
      return item[callback];
    });
  }

  return array;
});

/**
 * map filter
 * Maps array elements through a callback function.
 *
 * @param {array} array - Array to map
 * @param {function|string} callback - Callback function or attribute name
 * @returns {array} Mapped array
 */
export const map = Twig.extendFilter('map', function (array, callback) {
  if (!Array.isArray(array)) {
    return array;
  }

  // If callback is a string, treat it as a property accessor
  if (typeof callback === 'string') {
    return array.map((item) => {
      return typeof item === 'object' ? item[callback] : item;
    });
  }

  // If callback is a function, use it
  if (typeof callback === 'function') {
    return array.map(callback);
  }

  return array;
});

/**
 * join filter
 * Joins array elements with a separator.
 *
 * @param {array} array - Array to join
 * @param {string} glue - Separator string
 * @returns {string} Joined string
 */
export const join = Twig.extendFilter('join', function (array, glue) {
  if (!Array.isArray(array)) {
    return String(array);
  }

  glue = glue || '';
  return array.join(glue);
});

/**
 * keys filter
 * Returns the keys of an array or object.
 *
 * @param {array|object} data - Array or object
 * @returns {array} Array of keys
 */
export const keys = Twig.extendFilter('keys', function (data) {
  if (typeof data !== 'object' || data === null) {
    return [];
  }

  return Object.keys(data);
});

/**
 * values filter
 * Returns the values of an array or object.
 *
 * @param {array|object} data - Array or object
 * @returns {array} Array of values
 */
export const values = Twig.extendFilter('values', function (data) {
  if (!Array.isArray(data) && typeof data !== 'object') {
    return [];
  }

  if (Array.isArray(data)) {
    return data;
  }

  return Object.values(data);
});

// Export for convenience
export default {
  create_attribute,
  filter,
  map,
  join,
  keys,
  values,
};
