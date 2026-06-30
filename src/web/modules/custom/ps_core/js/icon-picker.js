/**
 * @file
 * Replaces the icon picker text input with Select / Remove icon controls.
 */
((Drupal, once) => {
  const formatIconId = (value) => {
    if (!value || !value.includes(':')) {
      return '';
    }
    return value.split(':').pop().replace(/-/g, ' ');
  };

  const updateButtonLabel = (input, button) => {
    button.textContent = input.value
      ? Drupal.t('Change icon')
      : Drupal.t('Select icon');
  };

  const clearPreview = (container) => {
    const previewIcon = container.querySelector('.ui-icons-preview-icon');
    if (previewIcon) {
      previewIcon.innerHTML = '';
    }
  };

  Drupal.behaviors.psIconPicker = {
    attach(context) {
      once('ps-icon-picker', '.ps-icon-picker', context).forEach((container) => {
        const input = container.querySelector('input.form-icon-dialog');
        if (!input || container.querySelector('.ps-icon-picker__button')) {
          return;
        }

        const inputWrapper = container.querySelector('.ui-icons-input-wrapper');
        const preview = container.querySelector('.ui-icons-preview');
        const selectWrapper = input.closest('.ui-icons-select') ?? container;

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'button ps-icon-picker__button';

        const clearButton = document.createElement('button');
        clearButton.type = 'button';
        clearButton.className = 'button ps-icon-picker__clear';
        clearButton.textContent = Drupal.t('Remove icon');

        const meta = document.createElement('span');
        meta.className = 'ps-icon-picker__id';
        meta.setAttribute('aria-live', 'polite');

        const syncState = () => {
          updateButtonLabel(input, button);
          const label = formatIconId(input.value);
          meta.textContent = label;
          meta.hidden = label === '';
          clearButton.hidden = input.value === '';
        };

        button.addEventListener('click', (event) => {
          event.preventDefault();
          input.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        });

        clearButton.addEventListener('click', (event) => {
          event.preventDefault();
          input.value = '';
          clearPreview(container);
          input.dispatchEvent(new Event('input', { bubbles: true }));
          input.dispatchEvent(new Event('change', { bubbles: true }));
          syncState();
        });

        input.addEventListener('change', syncState);
        input.addEventListener('input', syncState);

        if (inputWrapper && preview) {
          const control = document.createElement('div');
          control.className = 'ps-icon-picker__control';
          control.append(preview, button, clearButton, meta);
          inputWrapper.insertBefore(control, selectWrapper);
        }
        else {
          selectWrapper.insertBefore(button, selectWrapper.firstChild);
          selectWrapper.insertBefore(clearButton, button.nextSibling);
          selectWrapper.insertBefore(meta, input.parentElement);
        }

        const description = selectWrapper.querySelector('.form-item__description');
        if (description) {
          description.classList.add('visually-hidden');
        }

        syncState();
      });
    },
  };
})(Drupal, once);
