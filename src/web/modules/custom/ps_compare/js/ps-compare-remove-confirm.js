/**
 * @file
 * Confirms property removal from the compare table before toggling off.
 */
(function (Drupal, once) {
  'use strict';

  const CONFIRMED_FLAG = 'psCompareRemoveConfirmed';
  let pendingButton = null;

  const getPopoverContainer = (button) => {
    const modal = button.closest('[data-ps-compare-modal]');
    if (modal) {
      return modal.querySelector('[data-ps-compare-modal-body]') || modal;
    }
    return document.body;
  };

  const buildConfirmContent = (button) => {
    const title = button.closest('.ps-compare-table__column-header')
      ?.querySelector('.ps-compare-table__column-title')
      ?.textContent
      ?.trim();

    const heading = title
      ? Drupal.t('Remove “@title” from comparison?', { '@title': title })
      : Drupal.t('Remove this property from comparison?');

    return `
      <div class="ps-compare-remove-confirm">
        <p class="ps-compare-remove-confirm__text mb-2">${Drupal.checkPlain(heading)}</p>
        <div class="ps-compare-remove-confirm__actions d-flex gap-2">
          <button type="button" class="btn btn-sm btn-outline-secondary" data-ps-compare-remove-cancel>${Drupal.t('Cancel')}</button>
          <button type="button" class="btn btn-sm btn-danger" data-ps-compare-remove-accept>${Drupal.t('Remove')}</button>
        </div>
      </div>`;
  };

  const hidePopover = (button) => {
    if (typeof bootstrap === 'undefined' || !bootstrap.Popover) {
      return;
    }
    bootstrap.Popover.getInstance(button)?.hide();
    pendingButton = null;
  };

  const confirmRemoval = (button) => {
    hidePopover(button);
    button.dataset[CONFIRMED_FLAG] = 'true';
    button.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));
  };

  const showConfirmPopover = (button) => {
    if (typeof bootstrap === 'undefined' || !bootstrap.Popover) {
      if (window.confirm(Drupal.t('Remove this property from comparison?'))) {
        confirmRemoval(button);
      }
      return;
    }

    pendingButton = button;
    let popover = bootstrap.Popover.getInstance(button);
    if (!popover) {
      popover = new bootstrap.Popover(button, {
        container: getPopoverContainer(button),
        content: buildConfirmContent(button),
        customClass: 'ps-compare-remove-confirm-popover',
        html: true,
        placement: 'bottom',
        sanitize: false,
        trigger: 'manual',
      });
    }
    else {
      popover.setContent({ '.popover-body': buildConfirmContent(button) });
    }

    popover.show();
  };

  Drupal.behaviors.psCompareRemoveConfirm = {
    attach(context) {
      once('ps-compare-remove-confirm-delegate', 'body', context).forEach(() => {
        document.addEventListener('click', (event) => {
          if (!pendingButton) {
            return;
          }

          const accept = event.target.closest('[data-ps-compare-remove-accept]');
          const cancel = event.target.closest('[data-ps-compare-remove-cancel]');
          if (!accept && !cancel) {
            return;
          }

          event.preventDefault();
          event.stopPropagation();

          if (accept) {
            confirmRemoval(pendingButton);
            return;
          }

          hidePopover(pendingButton);
        });
      });

      once('ps-compare-remove-confirm', '[data-ps-compare-remove-confirm][data-ps-compare-toggle]', context)
        .forEach((button) => {
          button.addEventListener('click', (event) => {
            if (!button.classList.contains('is-active')) {
              return;
            }
            if (button.dataset[CONFIRMED_FLAG] === 'true') {
              delete button.dataset[CONFIRMED_FLAG];
              return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();
            showConfirmPopover(button);
          }, true);
        });
    },
  };
})(Drupal, once);
