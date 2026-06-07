/**
 * @file
 * Client-side sorting for the offer surface division table.
 */
(function (Drupal, once) {
  'use strict';

  /**
   * Parses a cell sort value for comparison.
   *
   * @param {string} rawValue
   *   Raw data-sort-value attribute.
   * @param {string} sortType
   *   Sort type: text or number.
   *
   * @return {number|string}
   *   Comparable value.
   */
  function parseSortValue(rawValue, sortType) {
    if (sortType === 'number') {
      const parsed = parseFloat(rawValue);
      return Number.isFinite(parsed) ? parsed : 0;
    }

    return (rawValue || '').toLocaleLowerCase();
  }

  /**
   * Sorts table rows in place.
   *
   * @param {HTMLTableElement} table
   *   Table element.
   * @param {string} columnKey
   *   Column key to sort by.
   * @param {string} direction
   *   asc or desc.
   */
  function sortTable(table, columnKey, direction) {
    const tbody = table.tBodies[0];
    if (!tbody) {
      return;
    }

    const headerButtons = table.querySelectorAll('.ps-surface-division-table__sort');
    let columnIndex = -1;
    headerButtons.forEach((button, index) => {
      if (button.dataset.sortKey === columnKey) {
        columnIndex = index;
      }
    });

    if (columnIndex < 0) {
      return;
    }

    const sortType = headerButtons[columnIndex].dataset.sortType || 'text';
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const multiplier = direction === 'desc' ? -1 : 1;

    rows.sort((rowA, rowB) => {
      const cellA = rowA.cells[columnIndex];
      const cellB = rowB.cells[columnIndex];
      const valueA = parseSortValue(cellA?.dataset.sortValue ?? cellA?.textContent ?? '', sortType);
      const valueB = parseSortValue(cellB?.dataset.sortValue ?? cellB?.textContent ?? '', sortType);

      if (valueA < valueB) {
        return -1 * multiplier;
      }
      if (valueA > valueB) {
        return 1 * multiplier;
      }
      return 0;
    });

    rows.forEach((row) => tbody.appendChild(row));
  }

  /**
   * Updates active sort button state and ARIA labels.
   *
   * @param {HTMLTableElement} table
   *   Table element.
   * @param {string} columnKey
   *   Active column key.
   * @param {string} direction
   *   Active direction.
   */
  function updateSortState(table, columnKey, direction) {
    table.dataset.sortColumn = columnKey;
    table.dataset.sortDirection = direction;

    table.querySelectorAll('.ps-surface-division-table__sort').forEach((button) => {
      const isActive = button.dataset.sortKey === columnKey;
      button.classList.toggle('is-sort-active', isActive);
      button.classList.toggle('is-sort-asc', isActive && direction === 'asc');
      button.classList.toggle('is-sort-desc', isActive && direction === 'desc');
    });
  }

  Drupal.behaviors.psSurfaceDivisionTable = {
    attach(context) {
      once('ps-surface-division-table', '.ps-surface-division-table', context).forEach((table) => {
        const columnKey = table.dataset.sortColumn || 'lot';
        const direction = table.dataset.sortDirection || 'asc';
        sortTable(table, columnKey, direction);
        updateSortState(table, columnKey, direction);

        table.querySelectorAll('.ps-surface-division-table__sort').forEach((button) => {
          button.addEventListener('click', () => {
            const key = button.dataset.sortKey;
            if (!key) {
              return;
            }

            let nextDirection = 'asc';
            if (table.dataset.sortColumn === key && table.dataset.sortDirection === 'asc') {
              nextDirection = 'desc';
            }

            sortTable(table, key, nextDirection);
            updateSortState(table, key, nextDirection);
          });
        });
      });
    },
  };
})(Drupal, once);
