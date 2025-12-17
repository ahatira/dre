/**
 * Table Component
 *
 * Interactive table with client-side sorting functionality.
 * Supports numeric and text sorting with ascending/descending order.
 *
 * @class PsTable
 */
export class PsTable {
  constructor(root, options = {}) {
    this.root = root;
    this.options = { ...PsTable.defaults, ...options };
    this.controllers = [];
    this.initialized = false;
  }

  static defaults = {
    sortable: true,
    locale: 'fr',
  };

  /**
   * Initialize the table component
   */
  init() {
    if (this.initialized) {
      return;
    }
    this.initialized = true;

    const ac = new AbortController();
    this.controllers.push(ac);

    // Attach click listeners to sortable headers
    const sortButtons = this.root.querySelectorAll('.ps-table__sort-button');
    sortButtons.forEach((button) => {
      button.addEventListener(
        'click',
        (e) => {
          e.preventDefault();
          this.handleSort(e.currentTarget);
        },
        { signal: ac.signal }
      );

      // Keyboard support (Enter/Space)
      button.addEventListener(
        'keydown',
        (e) => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            this.handleSort(e.currentTarget);
          }
        },
        { signal: ac.signal }
      );
    });
  }

  /**
   * Handle sort button click/keyboard activation
   * @param {HTMLElement} button - The sort button element
   */
  handleSort(button) {
    const header = button.closest('.ps-table__header');
    const columnIndex = this.getColumnIndex(header);
    const currentOrder = this.getCurrentOrder(header);

    // Toggle order: null → asc → desc → asc
    const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';

    // Reset all column sort states
    this.root.querySelectorAll('.ps-table__header').forEach((h) => {
      h.classList.remove('ps-table__header--sorted-asc', 'ps-table__header--sorted-desc');
      h.removeAttribute('aria-sort');
    });

    // Apply new sort state to current column
    header.classList.add(`ps-table__header--sorted-${newOrder}`);
    header.setAttribute('aria-sort', newOrder === 'asc' ? 'ascending' : 'descending');

    // Sort the table rows
    this.sortRows(columnIndex, newOrder, header.classList.contains('ps-table__header--numeric'));
  }

  /**
   * Get the column index of a header cell
   * @param {HTMLElement} header - The header cell
   * @returns {number} Column index
   */
  getColumnIndex(header) {
    return Array.from(header.parentElement.children).indexOf(header);
  }

  /**
   * Get current sort order of a header
   * @param {HTMLElement} header - The header cell
   * @returns {string|null} 'asc', 'desc', or null
   */
  getCurrentOrder(header) {
    if (header.classList.contains('ps-table__header--sorted-asc')) {
      return 'asc';
    }
    if (header.classList.contains('ps-table__header--sorted-desc')) {
      return 'desc';
    }
    return null;
  }

  /**
   * Sort table rows by column
   * @param {number} columnIndex - Index of the column to sort
   * @param {string} order - 'asc' or 'desc'
   * @param {boolean} isNumeric - Whether to use numeric sorting
   */
  sortRows(columnIndex, order, isNumeric = false) {
    const tbody = this.root.querySelector('.ps-table__body');
    if (!tbody) {
      return;
    }

    const rows = Array.from(tbody.querySelectorAll('.ps-table__row'));

    rows.sort((a, b) => {
      const aCell = a.children[columnIndex]?.textContent.trim() || '';
      const bCell = b.children[columnIndex]?.textContent.trim() || '';

      let comparison = 0;

      if (isNumeric) {
        // Numeric comparison - extract numbers from strings (e.g., "207 m²" → 207)
        const aNum = parseFloat(aCell.replace(/[^\d.,-]/g, '').replace(',', '.'));
        const bNum = parseFloat(bCell.replace(/[^\d.,-]/g, '').replace(',', '.'));

        if (!Number.isNaN(aNum) && !Number.isNaN(bNum)) {
          comparison = aNum - bNum;
        } else {
          // Fallback to string comparison if not valid numbers
          comparison = aCell.localeCompare(bCell, this.options.locale, { numeric: true });
        }
      } else {
        // String comparison with locale support
        comparison = aCell.localeCompare(bCell, this.options.locale, {
          sensitivity: 'base',
          numeric: true, // Handle embedded numbers (e.g., "Lot 1" vs "Lot 10")
        });
      }

      return order === 'asc' ? comparison : -comparison;
    });

    // Re-append sorted rows (maintains DOM order without destroying elements)
    rows.forEach((row) => tbody.appendChild(row));
  }

  /**
   * Destroy the component and cleanup listeners
   */
  destroy() {
    this.controllers.forEach((c) => c.abort());
    this.controllers = [];
    this.initialized = false;
  }
}

/**
 * Drupal Behavior for Table Component
 */
((Drupal, once) => {
  Drupal.behaviors.psTable = {
    attach(context, settings) {
      const globalConfig = settings.psTheme?.components?.table || {};

      once('psTable', '.ps-table', context).forEach((root) => {
        // Skip if already initialized
        if (root.__psInstance) {
          return;
        }

        // Only initialize tables with sortable headers
        const hasSortableHeaders = root.querySelector('.ps-table__header--sortable');
        if (!hasSortableHeaders) {
          return;
        }

        // Initialize table instance
        const instance = new PsTable(root, globalConfig);
        instance.init();

        // Store instance reference for cleanup
        root.__psInstance = instance;
      });
    },

    detach(context, _settings, trigger) {
      // Only cleanup on full unload
      if (trigger !== 'unload') {
        return;
      }

      context.querySelectorAll('.ps-table').forEach((root) => {
        if (root.__psInstance) {
          root.__psInstance.destroy();
          root.__psInstance = null;
        }
      });
    },
  };
})(Drupal, once);
