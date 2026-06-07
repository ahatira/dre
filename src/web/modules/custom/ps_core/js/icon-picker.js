/**
 * @file
 * Replaces the icon picker text input with a Select icon button.
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

        const meta = document.createElement('span');
        meta.className = 'ps-icon-picker__id';
        meta.setAttribute('aria-live', 'polite');

        const syncState = () => {
          updateButtonLabel(input, button);
          const label = formatIconId(input.value);
          meta.textContent = label;
          meta.hidden = label === '';
        };

        button.addEventListener('click', (event) => {
          event.preventDefault();
          input.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        });

        input.addEventListener('change', syncState);
        input.addEventListener('input', syncState);

        if (inputWrapper && preview) {
          const control = document.createElement('div');
          control.className = 'ps-icon-picker__control';
          control.append(preview, button, meta);
          inputWrapper.insertBefore(control, selectWrapper);
        }
        else {
          selectWrapper.insertBefore(button, selectWrapper.firstChild);
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
