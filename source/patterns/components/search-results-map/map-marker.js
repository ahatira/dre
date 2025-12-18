/**
 * Map Marker Theming (Drupal.theme)
 * - Themable marker generation for search results map
 * - Supports: price markers, NC markers, selected state, clusters
 * - Override via Drupal.theme.mapMarker() in custom modules
 */

((Drupal) => {
  /**
   * Default marker theme implementation
   * @param {Object} data - Marker data
   * @param {string} data.id - Result ID
   * @param {number|null} data.price - Price or null for NC
   * @param {string} data.currency - Currency code (EUR, USD, etc.)
   * @param {boolean} data.selected - Is this result selected?
   * @param {string} data.type - Marker type: 'price', 'nc', 'cluster'
   * @param {number} data.count - For clusters: number of items
   * @returns {HTMLElement} - Marker DOM element
   */
  Drupal.theme.mapMarker = function (data) {
    const { id, price, currency = 'EUR', selected = false, type = 'price', count } = data;

    // Cluster marker
    if (type === 'cluster') {
      const cluster = document.createElement('div');
      cluster.className = 'ps-map__cluster';
      cluster.textContent = String(count || 0);
      cluster.dataset.type = 'cluster';
      return cluster;
    }

    // Standard price/NC marker
    const wrapper = document.createElement('div');
    const isNC = price === null || typeof price === 'undefined';

    // Build class list
    const classes = ['ps-map__marker'];
    if (isNC) {
      classes.push('ps-map__marker--nc');
    }
    if (selected) {
      classes.push('ps-map__marker--selected');
    }
    wrapper.className = classes.join(' ');
    wrapper.dataset.id = id;
    wrapper.dataset.type = isNC ? 'nc' : 'price';

    // Badge
    const badge = document.createElement('div');
    badge.className = 'ps-map__marker__badge';
    badge.textContent = isNC ? 'NC' : `${price} ${this.getCurrencySymbol(currency)}`;

    // Circle pin
    const circle = document.createElement('div');
    circle.className = 'ps-map__marker__circle';

    wrapper.appendChild(badge);
    wrapper.appendChild(circle);

    return wrapper;
  };

  /**
   * Get currency symbol
   * @param {string} currency - Currency code
   * @returns {string} - Symbol
   */
  Drupal.theme.getCurrencySymbol = (currency) => {
    const symbols = {
      EUR: '€',
      USD: '$',
      GBP: '£',
      CHF: 'CHF',
      JPY: '¥',
    };
    return symbols[currency] || currency;
  };

  /**
   * Marker anchor computation (for AdvancedMarkerElement)
   * @param {HTMLElement} wrapper - Marker wrapper
   * @param {HTMLElement} badge - Badge element
   * @param {HTMLElement} circle - Circle element
   * @returns {{x:number,y:number}} - Anchor point
   */
  Drupal.theme.computeMarkerAnchor = (wrapper, badge, circle) => {
    let anchorX = 36;
    let anchorY = 44;

    try {
      const attached = document.contains(wrapper);
      if (!attached) {
        wrapper.style.position = 'absolute';
        wrapper.style.visibility = 'hidden';
        wrapper.style.left = '-10000px';
        wrapper.style.top = '0';
        document.body.appendChild(wrapper);
      }

      const badgeRect = badge.getBoundingClientRect();
      const beforeStyles = getComputedStyle(badge, '::before');
      const notchHeight = parseFloat(beforeStyles.borderTopWidth) || 0;
      const circleRect = circle.getBoundingClientRect();
      const circleStyles = getComputedStyle(circle);
      const circleBottom = Math.abs(parseFloat(circleStyles.bottom) || 0);

      anchorX = badgeRect.width / 2;
      anchorY = badgeRect.height + notchHeight + circleBottom + circleRect.height / 2;

      if (!attached) {
        document.body.removeChild(wrapper);
      }
    } catch (_e) {
      // Keep defaults
    }

    return { x: anchorX, y: anchorY };
  };
})(Drupal);
