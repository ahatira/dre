(function (Drupal, once) {
  'use strict';

  /**
   * @return {boolean}
   */
  function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  /**
   * Reads the sticky site header offset used by compare table CSS.
   *
   * @param {HTMLElement} page
   *
   * @return {number}
   */
  function getChromeOffset(page) {
    const raw = window.getComputedStyle(page).getPropertyValue('--ps-compare-chrome-offset').trim();
    const parsed = parseFloat(raw);
    return Number.isFinite(parsed) ? parsed : 0;
  }

  /**
   * Horizontal scroll + sticky compact header (CodyHouse-style).
   *
   * @param {HTMLElement} control
   *   Wrapper with head pin, scroll container and navigation buttons.
   */
  function initTableScroll(control) {
    const page = control.closest('[data-ps-compare-page]');
    const scrollEl = control.querySelector('[data-ps-compare-table-scroll]');
    const headScrollEl = control.querySelector('[data-ps-compare-table-head-scroll]');
    const headTable = control.querySelector('[data-ps-compare-table-head-table]');
    const table = control.querySelector('[data-ps-compare-table-body]')
      || control.querySelector('[data-ps-compare-table]');
    const sentinel = control.querySelector('[data-ps-compare-table-sentinel]');
    const prev = control.querySelector('[data-ps-compare-table-prev]');
    const next = control.querySelector('[data-ps-compare-table-next]');
    if (!scrollEl || !table || !prev || !next) {
      return;
    }

    let syncingScroll = false;

    const syncHeadScroll = (scrollLeft) => {
      if (!headScrollEl || syncingScroll) {
        return;
      }
      syncingScroll = true;
      headScrollEl.scrollLeft = scrollLeft;
      syncingScroll = false;
    };

    const syncTableLayout = () => {
      if (!headTable || !table || !page) {
        return;
      }

      const headRow = headTable.querySelector('tr');
      const bodyRow = table.querySelector('.ps-compare-table__row');
      if (!headRow || !bodyRow) {
        return;
      }

      const styles = window.getComputedStyle(page);
      const labelWidth = parseFloat(styles.getPropertyValue('--ps-compare-table-label-width')) || 220;
      const columnWidth = parseFloat(styles.getPropertyValue('--ps-compare-table-column-width')) || 280;
      const columnCount = Math.max(0, headRow.children.length - 1);
      const totalWidth = labelWidth + (columnWidth * columnCount);
      const tableWidth = `${totalWidth}px`;
      const headCols = headTable.querySelectorAll('col');
      const bodyCols = table.querySelectorAll('col');

      headTable.style.width = tableWidth;
      table.style.width = tableWidth;

      Array.from(headRow.children).forEach((headCell, index) => {
        const width = index === 0 ? labelWidth : columnWidth;
        const widthPx = `${width}px`;

        if (headCols[index] instanceof HTMLElement) {
          headCols[index].style.width = widthPx;
        }
        if (bodyCols[index] instanceof HTMLElement) {
          bodyCols[index].style.width = widthPx;
        }
        if (headCell instanceof HTMLElement) {
          headCell.style.width = widthPx;
          headCell.style.minWidth = widthPx;
          headCell.style.maxWidth = widthPx;
        }
      });
    };

    const getStep = () => {
      const column = table.querySelector('.ps-compare-table__column')
        || table.querySelector('.ps-compare-table__cell');
      if (!column) {
        return Math.max(scrollEl.clientWidth * 0.75, 240);
      }
      return column.getBoundingClientRect().width;
    };

    const updateScrollState = () => {
      syncTableLayout();
      const maxScroll = scrollEl.scrollWidth - scrollEl.clientWidth;
      const scrollLeft = scrollEl.scrollLeft;
      const atStart = scrollLeft <= 1;
      const atEnd = scrollLeft >= maxScroll - 1;
      const scrollable = maxScroll > 1;

      control.classList.toggle('is-scrollable', scrollable);
      control.classList.toggle('is-scrolled-start', atStart);
      control.classList.toggle('is-scrolled-end', atEnd);

      prev.disabled = !scrollable || atStart;
      next.disabled = !scrollable || atEnd;
    };

    const scrollByStep = (direction) => {
      scrollEl.scrollBy({
        left: direction * getStep(),
        behavior: prefersReducedMotion() ? 'auto' : 'smooth',
      });
    };

    const onBodyScroll = () => {
      syncHeadScroll(scrollEl.scrollLeft);
      updateScrollState();
    };

    prev.addEventListener('click', (event) => {
      event.preventDefault();
      scrollByStep(-1);
    });

    next.addEventListener('click', (event) => {
      event.preventDefault();
      scrollByStep(1);
    });

    scrollEl.addEventListener('scroll', onBodyScroll, { passive: true });
    window.addEventListener('resize', updateScrollState, { passive: true });

    if (typeof ResizeObserver !== 'undefined') {
      const observer = new ResizeObserver(updateScrollState);
      observer.observe(scrollEl);
      observer.observe(table);
    }

    if (sentinel && page && typeof IntersectionObserver !== 'undefined') {
      const compactObserver = new IntersectionObserver(([entry]) => {
        control.classList.toggle('is-head-compact', !entry.isIntersecting);
        syncTableLayout();
      }, {
        root: null,
        threshold: 0,
        rootMargin: `-${getChromeOffset(page)}px 0px 0px 0px`,
      });
      compactObserver.observe(sentinel);

      window.addEventListener('resize', () => {
        compactObserver.disconnect();
        compactObserver.observe(sentinel);
      }, { passive: true });
    }

    syncTableLayout();
    syncHeadScroll(scrollEl.scrollLeft);
    updateScrollState();
  }

  Drupal.behaviors.psCompareTableScroll = {
    attach(context) {
      once('ps-compare-table-scroll', '[data-ps-compare-table-control]', context).forEach((control) => {
        initTableScroll(control);
      });
    },
  };
})(Drupal, once);
