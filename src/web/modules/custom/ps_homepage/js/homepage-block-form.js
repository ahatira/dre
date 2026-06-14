/**
 * @file
 * Homepage block form helpers for Layout Builder off-canvas.
 */
(function (Drupal, once) {
  'use strict';

  /**
   * Ensures Media Library dialogs render above the off-canvas tray.
   */
  Drupal.behaviors.psHomepageBlockFormMediaDialog = {
    attach(context) {
      once('ps-homepage-media-dialog', 'body', context).forEach(() => {
        if (!window.jQuery || !window.jQuery.ui || !window.jQuery.ui.dialog) {
          return;
        }

        const originalOpen = window.jQuery.ui.dialog.prototype.open;
        window.jQuery.ui.dialog.prototype.open = function psHomepageDialogOpen() {
          const result = originalOpen.apply(this, arguments);
          const $element = this.element || this.uiDialog;
          if ($element && $element.hasClass('media-library-widget-modal')) {
            const widget = this;
            window.setTimeout(() => {
              if (widget.uiDialog) {
                widget.uiDialog.css('z-index', 1305);
              }
            }, 0);
          }
          return result;
        };
      });
    },
  };
})(Drupal, once);
