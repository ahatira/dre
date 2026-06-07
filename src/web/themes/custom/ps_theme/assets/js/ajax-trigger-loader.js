/**
 * @file
 * Overlays AJAX loaders on triggers without changing their size.
 */

(($, Drupal) => {
  const LOADING_CLASS = 'is-ajax-loading';
  const OVERLAY_CLASS = 'ps-ajax-trigger__overlay';
  const LABEL_CLASS = 'ps-ajax-trigger__label';
  const VALUE_DATA_KEY = 'psAjaxInputValue';

  /**
   * Whether the element should use the overlay loader.
   *
   * @param {Element} element
   *   The AJAX trigger element.
   *
   * @return {boolean}
   *   TRUE for use-ajax links and form action buttons.
   */
  const isOverlayTrigger = (element) => {
    if (!(element instanceof Element)) {
      return false;
    }

    if (element.classList.contains('use-ajax')) {
      return true;
    }

    const tag = element.tagName;
    if (tag === 'BUTTON') {
      return true;
    }

    return tag === 'INPUT' && ['submit', 'button'].includes(element.getAttribute('type') ?? '');
  };

  /**
   * Wraps direct text nodes so they can be hidden during loading.
   *
   * @param {jQuery} $element
   *   The trigger element.
   */
  const wrapTriggerLabel = ($element) => {
    const element = $element.get(0);
    if (!element || element.querySelector(`.${LABEL_CLASS}`)) {
      return;
    }

    const textNodes = [];
    element.childNodes.forEach((node) => {
      if (node.nodeType === Node.TEXT_NODE && node.textContent.trim()) {
        textNodes.push(node);
      }
    });

    if (!textNodes.length) {
      return;
    }

    const $label = $(`<span class="${LABEL_CLASS}"></span>`);
    textNodes.forEach((node) => {
      $label.append(node);
    });
    $element.prepend($label);
  };

  /**
   * Hides native input values while keeping button dimensions.
   *
   * @param {jQuery} $element
   *   The trigger element.
   */
  const hideInputValue = ($element) => {
    const element = $element.get(0);
    if (!(element instanceof HTMLInputElement)) {
      return;
    }

    if (element.value) {
      element.dataset[VALUE_DATA_KEY] = element.value;
      element.value = '';
    }
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
    wrapTriggerLabel($element);
    hideInputValue($element);

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

    const element = $element.get(0);
    if (element instanceof HTMLInputElement && element.dataset[VALUE_DATA_KEY]) {
      element.value = element.dataset[VALUE_DATA_KEY];
      delete element.dataset[VALUE_DATA_KEY];
    }

    ajax.progress.overlayLoader = false;
  };

  const parentBeforeSend = Drupal.Ajax.prototype.beforeSend;
  // eslint-disable-next-line func-names
  Drupal.Ajax.prototype.beforeSend = function (xmlhttprequest, options) {
    parentBeforeSend.apply(this, [xmlhttprequest, options]);

    if (isOverlayTrigger(this.element)) {
      // Core disables all AJAX triggers; keep <a.use-ajax> clickable semantics.
      $(this.element).prop('disabled', false).removeAttr('disabled');
    }
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
