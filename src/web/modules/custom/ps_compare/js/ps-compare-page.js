(function (Drupal, once) {
  'use strict';

  /**
   * Sets sticky header offset below the site header.
   */
  const updateCompareLayout = (page) => {
    const siteHeader = document.querySelector('[data-ps-site-header]');
    const headerHeight = siteHeader ? Math.ceil(siteHeader.getBoundingClientRect().height) : 0;
    page.style.setProperty('--ps-compare-chrome-offset', `${headerHeight}px`);
  };

  Drupal.behaviors.psComparePageLayout = {
    attach(context) {
      once('ps-compare-page-layout', '[data-ps-compare-page]', context).forEach((page) => {
        const refresh = () => updateCompareLayout(page);
        refresh();
        window.addEventListener('resize', refresh, { passive: true });
        window.addEventListener('scroll', refresh, { passive: true });
      });
    },
  };

  /**
   * Bootstrap popover for compact price info in compare cells.
   */
  Drupal.behaviors.psCompareBudgetInfo = {
    attach(context) {
      once('ps-compare-budget-info', '[data-ps-compare-budget-info]', context).forEach((trigger) => {
        const content = trigger.getAttribute('data-bs-content');
        if (!content || typeof bootstrap === 'undefined' || !bootstrap.Popover) {
          return;
        }

        const container = context.closest('[data-ps-compare-modal]')
          ? context.querySelector('.modal-body')
          : 'body';

        // eslint-disable-next-line no-new
        new bootstrap.Popover(trigger, {
          container,
          content,
          html: true,
          placement: 'top',
          trigger: 'focus',
          sanitize: false,
        });
      });
    },
  };

  document.addEventListener('psCompare:changed', (event) => {
    if (document.querySelector('[data-ps-compare-modal-body] .ps-compare-page')) {
      return;
    }

    if (!document.querySelector('.ps-compare-page')) {
      return;
    }

    const detail = event.detail || {};
    if (detail.isCompared === false || (typeof detail.count === 'number' && detail.count < 2)) {
      window.location.reload();
    }
  });
})(Drupal, once);
