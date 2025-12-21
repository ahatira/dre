/**
 * @file
 * Language Selector behavior.
 *
 * Provides accessible dropdown behavior for language selection.
 * Progressive enhancement: works with native <select> fallback if JS disabled.
 *
 * @see https://www.drupal.org/docs/drupal-apis/javascript-api/javascript-api-overview
 */

((Drupal, once) => {
  /**
   * Language Selector behavior.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches language selector behavior to .ps-language-selector elements.
   * @prop {Drupal~behaviorDetach} detach
   *   Detaches language selector behavior on unload.
   */
  Drupal.behaviors.psLanguageSelector = {
    attach: (context, _settings) => {
      const elements = once('ps-language-selector', '.ps-language-selector', context);

      elements.forEach((element) => {
        // DOM references
        const button = element.querySelector('.ps-language-selector__button');
        const list = element.querySelector('.ps-language-selector__list');
        const nativeSelect = element.querySelector('.ps-language-selector__native');

        if (!button || !list) {
          return;
        }

        // Get enabled options
        const options = Array.from(
          list.querySelectorAll('.ps-language-selector__option:not([aria-disabled="true"])')
        );

        // Current selected index
        let currentIndex = options.findIndex((opt) => opt.getAttribute('aria-selected') === 'true');

        if (currentIndex === -1) {
          currentIndex = 0;
        }

        // Store cleanup functions
        const cleanupFunctions = [];

        // Helpers
        const updateSelectedState = (index) => {
          options.forEach((opt, i) => {
            opt.setAttribute('aria-selected', i === index ? 'true' : 'false');
          });
          currentIndex = index;
        };

        const updateButtonDisplay = (selectedOption) => {
          const labelElement = button.querySelector('.ps-language-selector__label');
          const flagElement = button.querySelector('.ps-flag');
          const optionLabel = selectedOption.querySelector('.ps-language-selector__label');
          const optionFlag = selectedOption.querySelector('.ps-flag');

          const optionLabelText = optionLabel ? optionLabel.textContent.trim() : '';

          if (labelElement && optionLabelText) {
            labelElement.textContent = optionLabelText;
          }

          if (flagElement && optionFlag) {
            flagElement.replaceWith(optionFlag.cloneNode(true));
          }
        };

        const updateNativeSelectValue = (selectedOption) => {
          const value = selectedOption.dataset.value;
          if (nativeSelect && value) {
            nativeSelect.value = value;
            const changeEvent = new Event('change', { bubbles: true });
            nativeSelect.dispatchEvent(changeEvent);
          }
          return value;
        };

        const navigateToUrl = (selectedOption, value) => {
          const url = selectedOption.dataset.url;
          if (url) {
            const navigationEvent = new CustomEvent('ps-language-selector:navigate', {
              detail: { url: url, value: value, option: selectedOption },
              cancelable: true,
            });
            element.dispatchEvent(navigationEvent);

            if (!navigationEvent.defaultPrevented) {
              window.location.href = url;
            }
          }
        };

        const selectOption = (index) => {
          updateSelectedState(index);
          const selectedOption = options[index];
          updateButtonDisplay(selectedOption);
          const value = updateNativeSelectValue(selectedOption);
          navigateToUrl(selectedOption, value);
        };

        // Toggle dropdown
        const toggle = () => {
          const isExpanded = button.getAttribute('aria-expanded') === 'true';
          if (isExpanded) {
            close();
          } else {
            open();
          }
        };

        const open = () => {
          list.hidden = false;
          button.setAttribute('aria-expanded', 'true');
          if (options[currentIndex]) {
            options[currentIndex].focus();
          }
        };

        const close = () => {
          list.hidden = true;
          button.setAttribute('aria-expanded', 'false');
        };

        const focusOption = (index) => {
          if (options[index]) {
            options[index].focus();
          }
        };

        const navigateOptions = (direction) => {
          let newIndex = currentIndex + direction;

          // Wrap around
          if (newIndex < 0) {
            newIndex = options.length - 1;
          } else if (newIndex >= options.length) {
            newIndex = 0;
          }

          currentIndex = newIndex;
          focusOption(newIndex);
        };

        const navigateByLetter = (letter) => {
          const lowerLetter = letter.toLowerCase();
          const total = options.length;
          for (let step = 1; step <= total; step++) {
            const idx = (currentIndex + step) % total;
            const text = options[idx].textContent.trim().toLowerCase();
            if (text.startsWith(lowerLetter)) {
              currentIndex = idx;
              focusOption(idx);
              break;
            }
          }
        };

        // Event: Button click
        const handleButtonClick = (e) => {
          e.preventDefault();
          e.stopPropagation();
          toggle();
        };

        // Event: Button keydown
        const handleButtonKeydown = (e) => {
          const isExpanded = button.getAttribute('aria-expanded') === 'true';

          if (!isExpanded && ['Enter', ' ', 'ArrowDown', 'ArrowUp'].indexOf(e.key) !== -1) {
            e.preventDefault();
            open();
          }
        };

        // Event: List keydown
        const handleListKeydown = (e) => {
          switch (e.key) {
            case 'ArrowDown':
              e.preventDefault();
              navigateOptions(1);
              break;

            case 'ArrowUp':
              e.preventDefault();
              navigateOptions(-1);
              break;

            case 'Home':
              e.preventDefault();
              focusOption(0);
              currentIndex = 0;
              break;

            case 'End': {
              e.preventDefault();
              const lastIndex = options.length - 1;
              focusOption(lastIndex);
              currentIndex = lastIndex;
              break;
            }

            case 'Enter':
            case ' ':
              e.preventDefault();
              selectOption(currentIndex);
              close();
              button.focus();
              break;

            default:
              // Letter key navigation
              if (e.key.length === 1 && /[a-z]/i.test(e.key)) {
                navigateByLetter(e.key);
              }
              break;
          }
        };

        // Event: Escape key
        const handleEscape = (e) => {
          if (e.key === 'Escape') {
            const isExpanded = button.getAttribute('aria-expanded') === 'true';
            if (isExpanded) {
              e.preventDefault();
              e.stopPropagation();
              close();
              button.focus();
            }
          }
        };

        // Event: Outside click
        const handleOutsideClick = (e) => {
          const isExpanded = button.getAttribute('aria-expanded') === 'true';
          if (isExpanded && !element.contains(e.target)) {
            close();
          }
        };

        // Attach event listeners
        button.addEventListener('click', handleButtonClick);
        button.addEventListener('keydown', handleButtonKeydown);
        list.addEventListener('keydown', handleListKeydown);
        element.addEventListener('keydown', handleEscape);
        document.addEventListener('click', handleOutsideClick, { capture: true });

        // Store cleanup function
        cleanupFunctions.push(() => {
          button.removeEventListener('click', handleButtonClick);
          button.removeEventListener('keydown', handleButtonKeydown);
          list.removeEventListener('keydown', handleListKeydown);
          element.removeEventListener('keydown', handleEscape);
          document.removeEventListener('click', handleOutsideClick, { capture: true });
        });

        // Attach option click handlers
        options.forEach((option, index) => {
          const handleOptionClick = (e) => {
            e.stopPropagation();
            selectOption(index);
            close();
          };

          option.addEventListener('click', handleOptionClick);

          cleanupFunctions.push(() => {
            option.removeEventListener('click', handleOptionClick);
          });
        });

        // Store cleanup functions on element for detach
        element.psLanguageSelectorCleanup = cleanupFunctions;
      });
    },

    detach: (context, _settings, trigger) => {
      if (trigger === 'unload') {
        const elements = once.remove('ps-language-selector', '.ps-language-selector', context);

        elements.forEach((element) => {
          if (element.psLanguageSelectorCleanup) {
            element.psLanguageSelectorCleanup.forEach((cleanup) => {
              cleanup();
            });
            delete element.psLanguageSelectorCleanup;
          }
        });
      }
    },
  };
})(Drupal, once);
