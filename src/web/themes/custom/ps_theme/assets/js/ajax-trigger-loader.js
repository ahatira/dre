/**
 * @file
 * Overlays AJAX loaders on buttons/links without changing their size.
 */

(($, Drupal) => {
  const LOADING_CLASS = 'is-ajax-loading';
  const OVERLAY_CLASS = 'ps-ajax-trigger__overlay';

  /**
   * Whether the element should use the overlay loader.
   *
   * @param {Element} element
   *   The AJAX trigger element.
   *
   * @return {boolean}
   *   TRUE for standalone links and buttons.
   */
  const isOverlayTrigger = (element) => {
    const tag = element.tagName;
    return tag === 'A' || tag === 'BUTTON';
  };

  /**
   * Shows a centered overlay loader on the trigger.
   *
   * @param {jQuery} $element
   *   The trigger element.
   * @param {string} [message]
   *   Optional progress message.
   */
  const startOverlayLoader = ($element, message) => {
    let $overlay = $element.children(`.${OVERLAY_CLASS}`).first();
    if (!$overlay.length) {
      $overlay = $(`<span class="${OVERLAY_CLASS}" aria-hidden="true"></span>`);
      $element.append($overlay);
    }

    $overlay.html(Drupal.theme('ajaxProgressThrobber', message));
    $element.addClass(LOADING_CLASS).attr('aria-busy', 'true');
  };

  /**
   * Removes the overlay loader and restores visible trigger content.
   *
   * @param {Drupal.Ajax} ajax
   *   The AJAX object.
   */
  const stopOverlayLoader = (ajax) => {
    const $element = $(ajax.element);

    if (!$element.hasClass(LOADING_CLASS)) {
      return;
    }

    $element.removeClass(LOADING_CLASS).removeAttr('aria-busy');
    $element.children(`.${OVERLAY_CLASS}`).remove();
    ajax.progress.overlayLoader = false;
  };

  const parentSetThrobber = Drupal.Ajax.prototype.setProgressIndicatorThrobber;
  // eslint-disable-next-line func-names
  Drupal.Ajax.prototype.setProgressIndicatorThrobber = function () {
    if (isOverlayTrigger(this.element)) {
      startOverlayLoader($(this.element), this.progress.message);
      this.progress.overlayLoader = true;
      this.progress.element = null;
      return;
    }

    parentSetThrobber.apply(this);
  };

  const parentSuccess = Drupal.Ajax.prototype.success;
  // eslint-disable-next-line func-names
  Drupal.Ajax.prototype.success = function (response, status) {
    const overlayLoader = this.progress.overlayLoader;

    return parentSuccess.apply(this, [response, status]).then(() => {
      if (overlayLoader) {
        stopOverlayLoader(this);
      }
    });
  };

  const parentError = Drupal.Ajax.prototype.error;
  // eslint-disable-next-line func-names
  Drupal.Ajax.prototype.error = function (xmlhttprequest, uri, customMessage) {
    if (this.progress.overlayLoader) {
      stopOverlayLoader(this);
    }

    return parentError.apply(this, [xmlhttprequest, uri, customMessage]);
  };
})(jQuery, Drupal);
