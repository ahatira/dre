/**
 * Feature Builder - Drupal behavior entry point.
 *
 * Architecture note:
 * - PHP renders a mount container plus a hidden state input.
 * - JS takes ownership of rendering inside the mount.
 * - Form submission still relies on Drupal Form API through the hidden input.
 *
 * Why this file matters:
 * - It is the only integration point with Drupal behaviors/once.
 * - It wires all runtime services (state, catalogue, renderer, sortable, sync).
 * - It enforces the bridge contract between drupalSettings and the UI runtime.
 */
(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.featureBuilder = {
    /**
     * Attach lifecycle called by Drupal on initial load and AJAX refresh.
     *
     * Constraints:
     * - Must be idempotent (handled via `once`).
     * - Must support multiple widget instances on the same form.
     */
    attach(context, settings) {
      const fbSettings = (settings && settings.featureBuilder) || {};

      once('feature-builder', '.fb-widget-mount', context).forEach(function (mount) {
        // Resolve field name from mount id.
        // Contract enforced by PHP widget: id="fb-{fieldName}-{entityId}".
        const mountId = mount.getAttribute('id') || '';
        // We intentionally resolve from settings keys to avoid brittle regex parsing.
        const fieldName = Object.keys(fbSettings).find(fn => mountId.startsWith('fb-' + fn + '-'));
        if (!fieldName) {
          console.warn('[FeatureBuilder] Unable to find settings for', mountId);
          return;
        }

        const config = fbSettings[fieldName];
        if (!config) return;

        // Hidden field rendered by PHP and used as JS<->Drupal synchronization channel.
        const fieldId = 'fb-state-' + fieldName;
        const hiddenField = document.getElementById(fieldId) || mount.querySelector('.fb-state-field');

        // Move hidden field outside mount before renderer.innerHTML writes.
        // Without this workaround the hidden field is removed from DOM and Form API receives nothing.
        // This is a critical Drupal integration edge case.
        if (hiddenField && hiddenField.parentNode === mount) {
          mount.after(hiddenField);
        }

        // Initialize runtime services.
        // Separation of concerns:
        // - state: source of truth
        // - catalogue: immutable feature definitions
        // - renderer: DOM projection of current state
        // - sortable: DnD interaction layer
        // - sync: state serialization to hidden input
        const state = new window.FeatureBuilderStateManager(config.initialState || { features: [] });
        const catalogue = new window.FeatureBuilderCatalogueService(config.catalogue || { groups: [], definitions: [] });
        const renderer = new window.FeatureBuilderRenderer(mount, state, catalogue);
        const sortable = new window.FeatureBuilderSortableController(mount, state);
        const sync = new window.FeatureBuilderHiddenFieldSync(hiddenField, state);

        // Initial render pipeline.
        renderer.render();
        sortable.attach();
        sync.attach();

        // React to state changes.
        // Performance strategy:
        // - payload-only updates avoid full re-render in renderer.update().
        // - sortable reattach only for structural changes.
        state.subscribe(function (newState, changeType) {
          renderer.update(newState, changeType);
          if (changeType !== 'payload') {
            // Reattach sortable after structural DOM updates.
            // setTimeout ensures DOM has been committed before Sortable scans containers.
            setTimeout(function () { sortable.reattach(); }, 0);
          }
        });
      });
    },
  };

})(Drupal, once);
