/**
 * @file
 * Defines ajax throbber theme functions.
 */

(($, window, Drupal) => {
  /**
   * Determines whether the current element is a modal dialog action button.
   *
   * @param {jQuery} $element
   *   The candidate element.
   *
   * @return {boolean}
   *   TRUE when the element is a jQuery UI dialog button.
   */
  Drupal.Ajax.prototype.isDialogActionButton = function ($element) {
    return (
      $element.closest('#drupal-modal, .modal').length > 0 &&
      $element.is('button.js-form-submit, button[type="submit"]')
    );
  };

  /**
   * Finds the visible action button rendered in the dialog footer.
   *
   * Dialog auto-buttons hide the original submit button and render a visible
   * footer button that proxies the click event. We need to update that visible
   * button to show progress feedback.
   *
   * @param {jQuery} $element
   *   The original submit button element.
   *
   * @return {jQuery}
   *   The visible footer button when available, otherwise the original element.
   */
  Drupal.Ajax.prototype.resolveDialogActionButton = function ($element) {
    const $modal = $element.closest('.modal');
    if (!$modal.length) {
      return $element;
    }

    const $footerButtons = $modal.find(
      '.modal-footer .ui-dialog-buttonset button:visible',
    );
    if (!$footerButtons.length) {
      return $element;
    }

    const originalLabel = ($element.text() || $element.val() || '').trim();
    if (originalLabel) {
      const $matchingButton = $footerButtons.filter((index, button) => {
        return $(button).text().trim() === originalLabel;
      });

      if ($matchingButton.length) {
        return $matchingButton.first();
      }
    }

    return $footerButtons.first();
  };

  /**
   * Toggles a state class when a dialog displays a confirmation message.
   *
   * @param {jQuery} $element
   *   The Ajax triggering element.
   */
  Drupal.Ajax.prototype.updateDialogConfirmationState = function ($element) {
    const $modal = $element.closest('.modal');
    if (!$modal.length) {
      return;
    }

    const hasConfirmation =
      $modal.find(
        '.webform-confirmation, .webform-confirmation__message, .webform-confirmation-modal, .messages--status, .alert-success',
      ).length > 0;

    $modal
      .find('.modal-dialog')
      .toggleClass('has-webform-confirmation', hasConfirmation);

    if (hasConfirmation) {
      this.ensureConfirmationAnimatedIcon($modal);
    }
  };

  /**
   * Ensures confirmation messages render an inline SVG icon for stroke animation.
   *
   * @param {jQuery} $modal
   *   The active modal container.
   */
  Drupal.Ajax.prototype.ensureConfirmationAnimatedIcon = function ($modal) {
    const iconMarkup = `
      <span class="ps-confirmation-icon" aria-hidden="true">
        <svg class="ps-confirmation-icon__svg" viewBox="0 0 64 64" focusable="false">
          <circle class="ps-confirmation-icon__circle" cx="32" cy="32" r="30"></circle>
          <path class="ps-confirmation-icon__check" d="M18 33.5 27.5 43 46 24"></path>
        </svg>
      </span>
    `;

    $modal
      .find(
        '.webform-confirmation, .webform-confirmation__message, .webform-submission-form .alert-success, .webform-submission-form .messages--status',
      )
      .each((index, element) => {
        const $target = $(element);
        if (
          $target.is('.webform-confirmation__message') &&
          $target.closest('.webform-confirmation').length
        ) {
          return;
        }

        if ($target.children('.ps-confirmation-icon').length) {
          return;
        }

        $target.prepend(iconMarkup);
      });
  };

  /**
   * Synchronizes confirmation classes and SVG icons for visible modals.
   */
  Drupal.Ajax.prototype.syncOpenModalConfirmationState = function () {
    $('.modal:visible').each((index, element) => {
      this.updateDialogConfirmationState($(element));
    });
  };

  /**
   * Replaces a modal submit button label with a spinner.
   *
   * @param {jQuery} $element
   *   The submit button element.
   */
  Drupal.Ajax.prototype.startDialogActionButtonProgress = function ($element) {
    const progressMessage = Drupal.t('Loading...');
    if ($element.data('ajaxOriginalLabel') === undefined) {
      $element.data('ajaxOriginalLabel', $element.html());
    }

    $element
      .addClass('is-loading')
      .attr('aria-busy', 'true')
      .prop('disabled', true)
      .html(`<span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span class="visually-hidden">${progressMessage}</span>`);
  };

  /**
   * Restores a modal submit button label after an AJAX request.
   *
   * @param {jQuery} $element
   *   The submit button element.
   */
  Drupal.Ajax.prototype.stopDialogActionButtonProgress = function ($element) {
    const originalLabel = $element.data('ajaxOriginalLabel');
    if (originalLabel !== undefined) {
      $element.html(originalLabel).removeData('ajaxOriginalLabel');
    }

    $element.removeClass('is-loading').removeAttr('aria-busy').prop('disabled', false);
  };

  /**
   * Attempts to find the closest icon progress indicator.
   *
   * @param {jQuery|Element} element
   *   A DOM element.
   *
   * @return {jQuery}
   *   A jQuery object.
   */
  // eslint-disable-next-line func-names
  Drupal.Ajax.prototype.findIcon = function (element) {
    return $(element)
      .closest('.form-item')
      .find('.ajax-progress .bi-arrow-repeat');
  };

  /**
   * Starts the spinning of the icon progress indicator.
   *
   * @param {jQuery|Element} element
   *   A DOM element.
   * @param {string} [message]
   *   An optional message to display (tooltip) for the progress.
   *
   * @return {jQuery}
   *   A jQuery object.
   */
  // eslint-disable-next-line func-names
  Drupal.Ajax.prototype.iconStart = function (element, message) {
    const $icon = this.findIcon(element);
    if ($icon[0]) {
      $icon.addClass('icon-spin');
      $icon.parent().addClass('text-primary');

      // Append a message for screen readers.
      if (message) {
        $icon
          .parent()
          .append(`<div class="visually-hidden message">${message}</div>`);
      }
    }
    return $icon;
  };

  /**
   * Stop the spinning of a icon progress indicator.
   *
   * @param {jQuery|Element} element
   *   A DOM element.
   */
  // eslint-disable-next-line func-names
  Drupal.Ajax.prototype.iconStop = function (element) {
    const $icon = this.findIcon(element);
    if ($icon[0]) {
      $icon.removeClass('icon-spin');
      $icon.parent().removeClass('text-primary');
    }
  };

  /**
   * Sets the throbber progress indicator.
   */
  // eslint-disable-next-line func-names
  Drupal.Ajax.prototype.setProgressIndicatorThrobber = function () {
    const $element = $(this.element);

    if (this.isDialogActionButton($element)) {
      const $dialogActionButton = this.resolveDialogActionButton($element);
      this.startDialogActionButtonProgress($dialogActionButton);
      this.progress.element = $dialogActionButton;
      this.progress.dialogActionButton = true;
      return;
    }

    // Find an existing icon progress indicator.
    const $icon = this.iconStart($element, this.progress.message);
    if ($icon[0]) {
      this.progress.element = $icon.parent();
      this.progress.icon = true;
      return;
    }

    // Otherwise, add a throbber after the element.
    if (!this.progress.element) {
      this.progress.element = $(
        Drupal.theme('ajaxProgressThrobber', this.progress.message),
      );
    }
    if (this.progress.message) {
      this.progress.element.after(
        `<div class="message">${this.progress.message}</div>`,
      );
    }

    // If element is an input DOM element type (not :input), append after.
    if (this.element.tagName === 'INPUT') {
      $element.after(this.progress.element);
    }
    // Otherwise append the throbber inside the element.
    else {
      if ($element.is('.ps-card-agent__cta')) {
        $element.addClass('is-loading').attr('aria-busy', 'true');
      }
      $element.append(this.progress.element);
    }
  };

  /**
   * Handler for the form redirection completion.
   *
   * @param {Array.<Drupal.AjaxCommands~commandDefinition>} response
   * @param {number} status
   */
  const success = Drupal.Ajax.prototype.success;
  // eslint-disable-next-line func-names
  Drupal.Ajax.prototype.success = function (response, status) {
    const $element = $(this.element);

    if (this.progress.element) {
      if (this.progress.dialogActionButton) {
        this.stopDialogActionButtonProgress(this.progress.element);
        this.progress.dialogActionButton = false;
        this.progress.element = false;
      }

      if (this.progress.element) {
        // Remove any message set.
        this.progress.element.parent().find('.message').remove();

        // Stop an icon throbber.
        if (this.progress.icon) {
          this.iconStop(this.progress.element);
          // If there is an icon, after cleaning the element, set it to false
          // to avoid the parent method to delete it.
          this.progress.element = false;
        }
        // Remove the progress element.
        else {
          this.progress.element.remove();
        }
      }
    }

    if ($element.is('.ps-card-agent__cta')) {
      $element.removeClass('is-loading').removeAttr('aria-busy');
    }

    // Invoke the original success handler.
    const result = success.apply(this, [response, status]);

    this.syncOpenModalConfirmationState();
    window.setTimeout(() => this.syncOpenModalConfirmationState(), 100);
    window.setTimeout(() => this.syncOpenModalConfirmationState(), 300);
    window.setTimeout(() => this.syncOpenModalConfirmationState(), 700);

    if ($element.is('.ps-card-agent__cta')) {
      $('.modal .modal-dialog').removeClass('has-webform-confirmation');
    }

    return result;
  };

  const error = Drupal.Ajax.prototype.error;
  // eslint-disable-next-line func-names
  Drupal.Ajax.prototype.error = function (xmlhttprequest, uri, customMessage) {
    const $element = $(this.element);

    if (this.progress && this.progress.dialogActionButton) {
      this.stopDialogActionButtonProgress(this.progress.element);
      this.progress.dialogActionButton = false;
      this.progress.element = false;
    }

    if (this.isDialogActionButton($element)) {
      this.updateDialogConfirmationState($element);
    }

    return error.apply(this, [xmlhttprequest, uri, customMessage]);
  };

  Drupal.behaviors.psModalConfirmationIcon = {
    attach() {
      Drupal.Ajax.prototype.syncOpenModalConfirmationState.call(
        Drupal.Ajax.prototype,
      );
    },
  };

  /**
   * An animated progress throbber and container element for AJAX operations.
   *
   * @param {string} [message]
   *   (optional) The message shown on the UI.
   * @return {string}
   *   The HTML markup for the throbber.
   */
  Drupal.theme.ajaxProgressThrobber = (message) => {
    // Build markup without adding extra white space since it affects rendering.
    const messageMarkup =
      typeof message === 'string'
        ? Drupal.theme('ajaxProgressMessage', message)
        : '';

    if (messageMarkup === '') {
      const defaultMessage = Drupal.t('Loading...');
      return `<div class="ajax-progress ajax-progress-throbber">
        <div class="spinner-border spinner-border-sm" role="status">
          <span class="visually-hidden">${defaultMessage}</span>
        </div>
      </div>`;
    }

    return `<div class="ajax-progress ajax-progress-throbber">
      <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
      ${messageMarkup}
    </div>`;
  };

  /**
   * Formats text accompanying the AJAX progress throbber.
   *
   * @param {string} message
   *   The message shown on the UI.
   * @return {string}
   *   The HTML markup for the throbber.
   */
  Drupal.theme.ajaxProgressMessage = (message) => {
    return `<span role="status">${message}</span>`;
  };
})(jQuery, this, Drupal);
