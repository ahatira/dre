(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.psOfferDetail = {
    attach(context) {
      once('ps-offer-read-more', '.ps-offer-description--collapsible', context).forEach((wrapper) => {
        const content = wrapper.querySelector('.ps-offer-description__content');
        const toggle = wrapper.querySelector('[data-ps-read-more]');
        if (!content || !toggle) {
          return;
        }
        if (content.scrollHeight <= content.clientHeight + 1) {
          toggle.hidden = true;
          return;
        }
        toggle.addEventListener('click', () => {
          const expanded = wrapper.classList.toggle('is-expanded');
          toggle.textContent = expanded ? Drupal.t('See less') : Drupal.t('See more');
        });
      });
    },
  };
})(Drupal, once);
