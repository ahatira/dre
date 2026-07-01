(function (Drupal, once) {
  'use strict';

  const MAX_DATES = 5;
  const MIN_DATES = 1;
  const WRAPPER_SELECTOR = '.ps-schedule-visit-availabilities';
  const CONTINUE_SELECTOR = '.webform-button--next, [data-drupal-selector="edit-wizard-next"]';

  /**
   * Resolves the flatpickr-bound input inside the availability wrapper.
   */
  function findFlatpickrInput(wrapper) {
    return wrapper.querySelector('input[flatpickr-name="availabilities"]')
      || wrapper.querySelector('input[type="text"]');
  }

  /**
   * Finds the wizard Continue button for the schedule visit form.
   */
  function findContinueButton(input) {
    const form = input.closest('form');
    if (!form) {
      return null;
    }

    return form.querySelector(CONTINUE_SELECTOR);
  }

  /**
   * Enables or disables Continue based on selected date count.
   */
  function syncContinueButton(continueButton, selectedCount) {
    if (!continueButton) {
      return;
    }

    const hasSelection = selectedCount >= MIN_DATES;
    continueButton.disabled = !hasSelection;
    continueButton.setAttribute('aria-disabled', hasSelection ? 'false' : 'true');
    continueButton.classList.toggle('is-disabled', !hasSelection);
  }

  Drupal.behaviors.psScheduleVisitAvailabilities = {
    attach(context) {
      once('ps-schedule-visit-availabilities', WRAPPER_SELECTOR, context).forEach((wrapper) => {
        const input = findFlatpickrInput(wrapper);
        if (!input) {
          return;
        }

        const continueButton = findContinueButton(input);
        syncContinueButton(continueButton, 0);

        const applyCalendar = () => {
          const instance = input._flatpickr;
          if (!instance) {
            window.requestAnimationFrame(applyCalendar);
            return;
          }

          syncContinueButton(continueButton, instance.selectedDates.length);

          instance.set('onChange', (selectedDates, dateStr, fp) => {
            if (selectedDates.length > MAX_DATES) {
              selectedDates.pop();
              fp.setDate(selectedDates, false);
            }

            syncContinueButton(continueButton, selectedDates.length);
          });
        };

        applyCalendar();
      });
    },
  };
})(Drupal, once);
