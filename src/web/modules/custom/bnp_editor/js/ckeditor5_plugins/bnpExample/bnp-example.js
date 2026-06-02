/**
 * @file
 * Example CKEditor 5 plugin for BNP platform.
 *
 * This is a placeholder demonstrating the structure for custom CKEditor 5 plugins.
 * Replace with actual plugin implementation as needed.
 */

(function () {
  'use strict';

  /**
   * Example BNP CKEditor 5 plugin.
   *
   * @example
   * // This plugin would be configured in editor.editor.*.yml:
   * // plugins:
   * //   bnp_editor_example:
   * //     enabled: true
   * //     example_setting: "value"
   */
  class BnpExample {
    constructor(editor) {
      this.editor = editor;
      console.log('BNP Example Plugin initialized');
    }

    init() {
      const editor = this.editor;
      console.log('BNP Example Plugin: init called', editor.config);
    }
  }

  // Export for CKEditor 5 plugin system.
  if (typeof module !== 'undefined' && module.exports) {
    module.exports = { BnpExample };
  }

  // Register with global scope for Drupal integration.
  if (typeof window !== 'undefined') {
    window.BnpExample = BnpExample;
  }

})();
