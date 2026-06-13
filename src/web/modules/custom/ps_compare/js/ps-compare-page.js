(function (Drupal, once) {
  'use strict';

  /**
   * Sets sticky header offset below the site header.
   */
  let layoutFrameId = 0;
  const updateCompareLayout = (page) => {
    if (layoutFrameId) {
      cancelAnimationFrame(layoutFrameId);
    }
    layoutFrameId = requestAnimationFrame(() => {
      layoutFrameId = 0;
      const siteHeader = document.querySelector('[data-ps-site-header]');
      const headerHeight = siteHeader ? Math.ceil(siteHeader.getBoundingClientRect().height) : 0;
      page.style.setProperty('--ps-compare-chrome-offset', `${headerHeight}px`);
    });
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
   * Resolves the Bootstrap popover container for compare table price info.
   *
   * When modal content is injected, attachBehaviors() receives the modal body
   * element itself — not the modal root — so we must not query for a nested
   * .modal-body (that returns null and breaks Bootstrap 5 config validation).
   */
  const resolvePopoverContainer = (trigger, context) => {
    const modal = trigger.closest('[data-ps-compare-modal]');
    if (!modal) {
      return document.body;
    }

    if (context instanceof Element && context.matches('[data-ps-compare-modal-body], .modal-body')) {
      return context;
    }

    return modal.querySelector('[data-ps-compare-modal-body]')
      || modal.querySelector('.modal-body')
      || modal;
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

        const container = resolvePopoverContainer(trigger, context);

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
      const detail = event.detail || {};
      if (detail.restored) {
        const minItems = (drupalSettings.psCompare || {}).minItems || 2;
        if (typeof detail.count === 'number' && detail.count >= minItems) {
          Drupal.psComparePanel?.loadCompareModalContent?.();
        }
        return;
      }

      if (detail.isCompared === false) {
        const minItems = (drupalSettings.psCompare || {}).minItems || 2;
        const count = typeof detail.count === 'number' ? detail.count : 0;
        if (count >= minItems && Drupal.psCompareUndo?.shouldDeferReload?.()) {
          return;
        }
        if (count >= minItems) {
          Drupal.psComparePanel?.loadCompareModalContent?.();
          return;
        }
      }
      return;
    }

    if (!document.querySelector('.ps-compare-page')) {
      return;
    }

    const detail = event.detail || {};
    if (detail.restored) {
      const minItems = (drupalSettings.psCompare || {}).minItems || 2;
      if (typeof detail.count === 'number' && detail.count >= minItems) {
        Drupal.psCompareUndo?.refreshComparePage?.();
      }
      else {
        window.location.reload();
      }
      return;
    }

    if (detail.isCompared === false || (typeof detail.count === 'number' && detail.count < 2)) {
      const minItems = (drupalSettings.psCompare || {}).minItems || 2;
      const count = typeof detail.count === 'number' ? detail.count : 0;

      if (count >= minItems && Drupal.psCompareUndo?.shouldDeferReload?.()) {
        return;
      }

      if (count >= minItems) {
        Drupal.psCompareUndo?.refreshComparePage?.();
        return;
      }

      if (Drupal.psCompareUndo?.shouldDeferReload?.()) {
        Drupal.psCompareUndo.scheduleDeferredReload(() => {
          window.location.reload();
        });
        return;
      }
      window.location.reload();
    }
  });
})(Drupal, once);
