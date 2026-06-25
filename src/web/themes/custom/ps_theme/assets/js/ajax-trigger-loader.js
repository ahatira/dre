/**
 * @file
 * Overlays AJAX loaders on triggers without changing their size.
 */

(($, Drupal) => {
  const LOADING_CLASS = 'is-ajax-loading';
  const LOADING_KEEP_LABEL_CLASS = 'ps-ajax-loading--keep-label';
  const OVERLAY_CLASS = 'ps-ajax-trigger__overlay';
  const LABEL_CLASS = 'ps-ajax-trigger__label';
  const VALUE_DATA_KEY = 'psAjaxInputValue';

  /**
   * Whether the element is the Views load-more pager link.
   *
   * @param {Element|null|undefined} element
   *   Trigger element.
   *
   * @return {boolean}
   *   TRUE for search list load-more links.
   */
  const isLoadMorePagerLink = (element) => (
    element instanceof Element
    && element.matches('.ps-search-load-more a[href], .pager--load-more a[href]')
  );

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

    if (element.matches('.ps-search-load-more a[href], .pager--load-more a[href]')) {
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

    // Preserve sibling order (e.g. icon span before label text).
    const insertBefore = textNodes[0].nextSibling;
    const $label = $(`<span class="${LABEL_CLASS}"></span>`);
    textNodes.forEach((node) => {
      $label.append(node);
    });

    if (insertBefore) {
      element.insertBefore($label[0], insertBefore);
    }
    else {
      element.appendChild($label[0]);
    }
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

    const element = $element.get(0);
    const loadMoreLink = isLoadMorePagerLink(element);
    let overlayMessage = message;

    if (loadMoreLink) {
      const labelText = $element.find(`.${LABEL_CLASS}`).text().trim()
        || $element.text().trim();
      if (labelText) {
        $element.attr('aria-label', labelText);
      }
      // Spinner only in place of label; keep-label keeps the link layout until
      // views_load_more replaces the pager.
      overlayMessage = null;
      $element.addClass(LOADING_KEEP_LABEL_CLASS);
    }

    let $overlay = $element.children(`.${OVERLAY_CLASS}`).first();
    if (!$overlay.length) {
      $overlay = $(`<span class="${OVERLAY_CLASS}" aria-hidden="true"></span>`);
      $element.append($overlay);
    }

    $overlay.html(Drupal.theme('ajaxProgressThrobber', overlayMessage));
    $element.addClass(LOADING_CLASS).attr('aria-busy', 'true');
    if ($element.is('a')) {
      $element.attr('aria-disabled', 'true');
    }
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

    $element.removeClass(LOADING_CLASS).removeClass(LOADING_KEEP_LABEL_CLASS).removeAttr('aria-busy');
    if ($element.is('a')) {
      $element.removeAttr('aria-disabled');
      if (isLoadMorePagerLink($element.get(0))) {
        $element.removeAttr('aria-label');
      }
    }
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
      const message = isLoadMorePagerLink(this.element) ? null : this.progress.message;
      startOverlayLoader($(this.element), message);
      this.progress.overlayLoader = true;
      this.progress.element = null;
      return;
    }

    parentSetThrobber.apply(this);
  };

  const parentSetFullscreen = Drupal.Ajax.prototype.setProgressIndicatorFullscreen;
  // eslint-disable-next-line func-names
  Drupal.Ajax.prototype.setProgressIndicatorFullscreen = function () {
    if (isOverlayTrigger(this.element)) {
      const message = isLoadMorePagerLink(this.element) ? null : this.progress.message;
      startOverlayLoader($(this.element), message);
      this.progress.overlayLoader = true;
      this.progress.element = null;
      return;
    }

    parentSetFullscreen.apply(this);
  };

  const parentSuccess = Drupal.Ajax.prototype.success;
  // eslint-disable-next-line func-names
  Drupal.Ajax.prototype.success = function (response, status) {
    const overlayLoader = this.progress.overlayLoader;
    const loadMoreTrigger = overlayLoader && isLoadMorePagerLink(this.element);

    return parentSuccess.apply(this, [response, status]).then(() => {
      if (!overlayLoader) {
        return;
      }

      // Load-more: keep spinner until views_load_more replaces the pager (see
      // search-page-load-more.js → views_load_more.new_content).
      if (loadMoreTrigger) {
        return;
      }

      stopOverlayLoader(this);
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
