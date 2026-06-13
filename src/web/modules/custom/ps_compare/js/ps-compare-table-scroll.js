(function (Drupal, once) {
  'use strict';

  /**
   * @return {boolean}
   */
  function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  /**
   * Horizontal scroll + column navigation (CodyHouse-style).
   *
   * @param {HTMLElement} control
   *   Wrapper with scroll container and navigation buttons.
   */
  function initTableScroll(control) {
    const page = control.closest('[data-ps-compare-page]');
    const scrollEl = control.querySelector('[data-ps-compare-table-scroll]');
    const table = control.querySelector('[data-ps-compare-table]');
    const prev = control.querySelector('[data-ps-compare-table-prev]');
    const next = control.querySelector('[data-ps-compare-table-next]');
    if (!scrollEl || !table || !prev || !next) {
      return;
    }

    const syncTableLayout = () => {
      if (!table || !page) {
        return;
      }

      const photosRow = table.querySelector('.ps-compare-table__row--photos');
      if (!photosRow) {
        return;
      }

      const styles = window.getComputedStyle(page);
      const labelWidth = parseFloat(styles.getPropertyValue('--ps-compare-table-label-width')) || 220;
      const columnWidth = parseFloat(styles.getPropertyValue('--ps-compare-table-column-width')) || 280;
      const columnCount = Math.max(0, photosRow.children.length - 1);
      const totalWidth = labelWidth + (columnWidth * columnCount);
      const tableWidth = `${totalWidth}px`;
      const cols = table.querySelectorAll('col');

      table.style.width = tableWidth;

      Array.from(photosRow.children).forEach((cell, index) => {
        const width = index === 0 ? labelWidth : columnWidth;
        const widthPx = `${width}px`;

        if (cols[index] instanceof HTMLElement) {
          cols[index].style.width = widthPx;
        }
        if (cell instanceof HTMLElement) {
          cell.style.width = widthPx;
          cell.style.minWidth = widthPx;
          cell.style.maxWidth = widthPx;
        }
      });
    };

    const getStep = () => {
      const column = table.querySelector('.ps-compare-table__cell--photos')
        || table.querySelector('.ps-compare-table__cell');
      if (!column) {
        return Math.max(scrollEl.clientWidth * 0.75, 240);
      }
      return column.getBoundingClientRect().width;
    };

    let layoutFrameId = 0;
    const scheduleScrollStateUpdate = () => {
      if (layoutFrameId) {
        cancelAnimationFrame(layoutFrameId);
      }
      layoutFrameId = requestAnimationFrame(() => {
        layoutFrameId = 0;
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
      });
    };

    const updateScrollState = () => {
      scheduleScrollStateUpdate();
    };

    const scrollByStep = (direction) => {
      scrollEl.scrollBy({
        left: direction * getStep(),
        behavior: prefersReducedMotion() ? 'auto' : 'smooth',
      });
    };

    prev.addEventListener('click', (event) => {
      event.preventDefault();
      scrollByStep(-1);
    });

    next.addEventListener('click', (event) => {
      event.preventDefault();
      scrollByStep(1);
    });

    scrollEl.addEventListener('scroll', updateScrollState, { passive: true });
    window.addEventListener('resize', updateScrollState, { passive: true });

    if (typeof ResizeObserver !== 'undefined') {
      const observer = new ResizeObserver(updateScrollState);
      observer.observe(scrollEl);
      observer.observe(table);
    }

    syncTableLayout();
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
