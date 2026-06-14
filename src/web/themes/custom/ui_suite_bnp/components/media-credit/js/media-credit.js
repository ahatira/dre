((Drupal, once) => {
  Drupal.behaviors.ui_suite_bnp_media_credit = {
    attach(context) {
      once('credit-tooltip', '.credit-tooltip', context).forEach((tooltip) => {
        const open = () => {
          tooltip.classList.add('is-open');
          tooltip.setAttribute('aria-expanded', 'true');
          window.setTimeout(() => {
            tooltip.classList.add('is-done');
          }, 500);
        };

        const close = () => {
          tooltip.classList.remove('is-open', 'is-done');
          tooltip.setAttribute('aria-expanded', 'false');
        };

        const toggle = () => {
          if (tooltip.classList.contains('is-open')) {
            close();
          }
          else {
            open();
          }
        };

        tooltip.addEventListener('click', (event) => {
          event.preventDefault();
          event.stopPropagation();
          toggle();
        });

        tooltip.addEventListener('keydown', (event) => {
          if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            toggle();
          }
          if (event.key === 'Escape') {
            close();
          }
        });
      });
    },
  };
})(Drupal, once);
