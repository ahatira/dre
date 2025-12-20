/**
 * @file
 * Language Selector behavior.
 *
 * Provides accessible dropdown behavior for language selection.
 * Progressive enhancement: works with native <select> fallback if JS disabled.
 *
 * @see https://www.drupal.org/docs/drupal-apis/javascript-api/javascript-api-overview
 */

(function (Drupal, once) {
  'use strict';

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
    attach: function (context, settings) {
      const elements = once('ps-language-selector', '.ps-language-selector', context);

      elements.forEach(function (element) {
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
        let currentIndex = options.findIndex(function (opt) {
          return opt.getAttribute('aria-selected') === 'true';
        });

        if (currentIndex === -1) {
          currentIndex = 0;
        }

        // Store cleanup functions
        const cleanupFunctions = [];

        // Toggle dropdown
        function toggle() {
          const isExpanded = button.getAttribute('aria-expanded') === 'true';
          if (isExpanded) {
            close();
          } else {
            open();
          }
        }

        function open() {
          list.hidden = false;
          button.setAttribute('aria-expanded', 'true');
          if (options[currentIndex]) {
            options[currentIndex].focus();
          }
        }

        function close() {
          list.hidden = true;
          button.setAttribute('aria-expanded', 'false');
        }

        function selectOption(index) {
          // Update ARIA selected state
          options.forEach(function (opt, i) {
            opt.setAttribute('aria-selected', i === index ? 'true' : 'false');
          });

          currentIndex = index;

          // Update button label
          const selectedOption = options[index];
          const labelElement = button.querySelector('.ps-language-selector__label');
          const flagElement = button.querySelector('.ps-flag');

          if (labelElement) {
            const optionLabel = selectedOption.querySelector('.ps-language-selector__label');
            if (optionLabel) {
              labelElement.textContent = optionLabel.textContent.trim();
            }
          }

          // Update button flag
          if (flagElement) {
            const optionFlag = selectedOption.querySelector('.ps-flag');
            if (optionFlag) {
              flagElement.replaceWith(optionFlag.cloneNode(true));
            }
          }

          // Update native select
          const value = selectedOption.dataset.value;
          if (nativeSelect && value) {
            nativeSelect.value = value;

            // Trigger change event
            const changeEvent = new Event('change', { bubbles: true });
            nativeSelect.dispatchEvent(changeEvent);
          }

          // Handle URL navigation
          const url = selectedOption.dataset.url;
          if (url) {
            const navigationEvent = new CustomEvent('ps-language-selector:navigate', {
              detail: { url: url, value: value, option: selectedOption },
              cancelable: true
            });
            element.dispatchEvent(navigationEvent);

            if (!navigationEvent.defaultPrevented) {
              window.location.href = url;
            }
          }
        }

        function focusOption(index) {
          if (options[index]) {
            options[index].focus();
          }
        }

        function navigateOptions(direction) {
          let newIndex = currentIndex + direction;

          // Wrap around
          if (newIndex < 0) {
            newIndex = options.length - 1;
          } else if (newIndex >= options.length) {
            newIndex = 0;
          }

          currentIndex = newIndex;
          focusOption(newIndex);
        }

        function navigateByLetter(letter) {
          const lowerLetter = letter.toLowerCase();
          let foundIndex = -1;

          // Search from current index + 1
          for (let i = currentIndex + 1; i < options.length; i++) {
            const text = options[i].textContent.trim().toLowerCase();
            if (text.startsWith(lowerLetter)) {
              foundIndex = i;
              break;
            }
          }

          // If not found, search from beginning
          if (foundIndex === -1) {
            for (let i = 0; i <= currentIndex; i++) {
              const text = options[i].textContent.trim().toLowerCase();
              if (text.startsWith(lowerLetter)) {
                foundIndex = i;
                break;
              }
            }
          }

          if (foundIndex !== -1) {
            currentIndex = foundIndex;
            focusOption(foundIndex);
          }
        }

        // Event: Button click
        function handleButtonClick(e) {
          e.preventDefault();
          e.stopPropagation();
          toggle();
        }

        // Event: Button keydown
        function handleButtonKeydown(e) {
          const isExpanded = button.getAttribute('aria-expanded') === 'true';

          if (!isExpanded && ['Enter', ' ', 'ArrowDown', 'ArrowUp'].indexOf(e.key) !== -1) {
            e.preventDefault();
            open();
          }
        }

        // Event: List keydown
        function handleListKeydown(e) {
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

            case 'End':
              e.preventDefault();
              const lastIndex = options.length - 1;
              focusOption(lastIndex);
              currentIndex = lastIndex;
              break;

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
        }

        // Event: Escape key
        function handleEscape(e) {
          if (e.key === 'Escape') {
            const isExpanded = button.getAttribute('aria-expanded') === 'true';
            if (isExpanded) {
              e.preventDefault();
              e.stopPropagation();
              close();
              button.focus();
            }
          }
        }

        // Event: Outside click
        function handleOutsideClick(e) {
          const isExpanded = button.getAttribute('aria-expanded') === 'true';
          if (isExpanded && !element.contains(e.target)) {
            close();
          }
        }

        // Attach event listeners
        button.addEventListener('click', handleButtonClick);
        button.addEventListener('keydown', handleButtonKeydown);
        list.addEventListener('keydown', handleListKeydown);
        element.addEventListener('keydown', handleEscape);
        document.addEventListener('click', handleOutsideClick, { capture: true });

        // Store cleanup function
        cleanupFunctions.push(function () {
          button.removeEventListener('click', handleButtonClick);
          button.removeEventListener('keydown', handleButtonKeydown);
          list.removeEventListener('keydown', handleListKeydown);
          element.removeEventListener('keydown', handleEscape);
          document.removeEventListener('click', handleOutsideClick, { capture: true });
        });

        // Attach option click handlers
        options.forEach(function (option, index) {
          function handleOptionClick(e) {
            e.stopPropagation();
            selectOption(index);
            close();
          }

          option.addEventListener('click', handleOptionClick);

          cleanupFunctions.push(function () {
            option.removeEventListener('click', handleOptionClick);
          });
        });

        // Store cleanup functions on element for detach
        element.psLanguageSelectorCleanup = cleanupFunctions;
      });
    },

    detach: function (context, settings, trigger) {
      if (trigger === 'unload') {
        const elements = once.remove('ps-language-selector', '.ps-language-selector', context);

        elements.forEach(function (element) {
          if (element.psLanguageSelectorCleanup) {
            element.psLanguageSelectorCleanup.forEach(function (cleanup) {
              cleanup();
            });
            delete element.psLanguageSelectorCleanup;
          }
        });
      }
    }
  };

})(Drupal, once);
