/**
 * @file
 * Feature Widget Builder - Drag and Drop JavaScript Controller
 *
 * Vanilla JavaScript implementation of a visual form builder for features.
 * Provides drag-and-drop functionality, dynamic configuration panels,
 * field preview rendering, and data persistence.
 *
 * Architecture:
 * - FeatureWidgetBuilder: Main controller class
 * - DragDropManager: Handles all drag-and-drop interactions
 * - ConfigurationManager: Manages field configuration panels
 * - DataManager: Handles data persistence and synchronization
 * - FilterManager: Manages sidebar search/filter functionality
 *
 * @see ps-feature-widget-builder.html.twig
 * @see widget-builder.css
 */

(function (Drupal, once) {
  'use strict';

  /**
   * Main Feature Widget Builder Controller
   *
   * Orchestrates all sub-managers and provides the public API
   * for the drag-and-drop form builder.
   */
  class FeatureWidgetBuilder {
    constructor(element) {
      this.element = element;
      this.fieldName = element.dataset.fieldName;

      // Sub-components
      this.dropZone = element.querySelector('.ps-feature-builder__drop-zone');
      this.itemsContainer = element.querySelector('.ps-feature-builder__items');
      this.sidebar = element.querySelector('.ps-feature-builder__sidebar');
      this.dataInput = element.querySelector('.ps-feature-builder__data');

      // State
      this.items = new Map(); // itemId -> itemData
      this.nextItemId = 1;

      // Undo history (circular buffer of last 10 actions)
      this.history = [];
      this.maxHistory = 10;

      // Toast container
      this.toastContainer = this.createToastContainer();

      // Initialize managers
      this.dragDropManager = new DragDropManager(this);
      this.configManager = new ConfigurationManager(this);
      this.dataManager = new DataManager(this);
      this.filterManager = new FilterManager(this);

      this.init();
    }

    /**
     * Initialize the widget builder
     */
    init() {
      this.loadExistingData();
      this.attachEventListeners();
      this.attachKeyboardShortcuts();
      this.updateDropZoneState();
      this.updateStats();

      // Debug: log dictionary types discovered in sidebar.
      try {
        this.sidebar
          .querySelectorAll('.ps-feature-builder__feature-type')
          .forEach((el) => {
            if (el.dataset.featureType === 'dictionary') {
              // eslint-disable-next-line no-console
              console.debug(
                '[FeatureBuilder] Sidebar feature dictionary type',
                el.dataset.featureId,
                el.dataset.featureDictionaryType
              );
            }
          });
      } catch (e) {
        // eslint-disable-next-line no-console
        console.warn('[FeatureBuilder] Dictionary debug failed', e);
      }

      console.log('[FeatureBuilder] Initialized for field:', this.fieldName);
    }

    /**
     * Load existing feature data from hidden input
     */
    loadExistingData() {
      try {
        const jsonData = this.dataInput.value;
        if (jsonData) {
          const existingData = JSON.parse(jsonData);
          existingData.forEach((itemData) => {
            this.addFeatureItem(itemData);
          });
        }
      } catch (error) {
        console.error('[FeatureBuilder] Error loading existing data:', error);
      }
    }

    /**
     * Attach global event listeners
     */
    attachEventListeners() {
      // Clear all button
      const clearAllBtn = this.element.querySelector(
        '.ps-feature-builder__clear-all'
      );
      if (clearAllBtn) {
        clearAllBtn.addEventListener('click', () => this.clearAllItems());
      }

      // Undo button
      const undoBtn = this.element.querySelector('.ps-feature-builder__undo');
      if (undoBtn) {
        undoBtn.addEventListener('click', () => this.undo());
      }

      // Apply compact mode by default
      this.element.classList.add('ps-feature-builder--compact');

      // Toggle entire builder collapse/expand from stats bar
      const statsToggle = this.element.querySelector(
        '.ps-feature-builder__stats-toggle'
      );
      if (statsToggle) {
        statsToggle.addEventListener('click', () => {
          const isCollapsed = this.element.classList.toggle(
            'ps-feature-builder--collapsed'
          );
          statsToggle.setAttribute('aria-expanded', String(!isCollapsed));
        });
      }

      // Collapse/Expand all
      const collapseAllBtn = this.element.querySelector(
        '.ps-feature-builder__collapse-all'
      );
      if (collapseAllBtn) {
        collapseAllBtn.addEventListener('click', () => {
          this.itemsContainer
            .querySelectorAll('.ps-feature-item')
            .forEach((el) => el.classList.add('ps-feature-item--collapsed'));
        });
      }
      const expandAllBtn = this.element.querySelector(
        '.ps-feature-builder__expand-all'
      );
      if (expandAllBtn) {
        expandAllBtn.addEventListener('click', () => {
          this.itemsContainer
            .querySelectorAll('.ps-feature-item')
            .forEach((el) => el.classList.remove('ps-feature-item--collapsed'));
        });
      }

      // Filter added items
      const addedSearch = this.element.querySelector(
        '.ps-feature-builder__added-search-input'
      );
      if (addedSearch) {
        addedSearch.addEventListener('input', () => {
          this.filterAdded(addedSearch.value || '');
        });
      }

      // Click on feature type in sidebar (alternative to drag-and-drop)
      this.sidebar
        .querySelectorAll('.ps-feature-builder__feature-type')
        .forEach((featureEl) => {
          featureEl.addEventListener('click', (e) => {
            // Prevent if already dragging
            if (
              featureEl.classList.contains(
                'ps-feature-builder__feature-type--dragging'
              )
            ) {
              return;
            }
            this.addFeatureFromSidebar(featureEl);
          });
        });
    }

    /**
     * Filter features already added (left column) by label
     * @param {string} query
     */
    filterAdded(query) {
      const q = (query || '').toLowerCase().trim();
      this.itemsContainer
        .querySelectorAll('.ps-feature-item')
        .forEach((itemEl) => {
          const label =
            itemEl.querySelector('.ps-feature-item__label')?.textContent || '';
          const match = !q || label.toLowerCase().includes(q);
          itemEl.style.display = match ? '' : 'none';
        });
    }

    /**
     * Add a feature item from sidebar click
     *
     * @param {HTMLElement} featureEl - The feature type element from sidebar
     */
    addFeatureFromSidebar(featureEl) {
      const featureData = {
        feature_id: featureEl.dataset.featureId,
        feature_label: featureEl.dataset.featureLabel,
        feature_type: featureEl.dataset.featureType,
        feature_dictionary_type: featureEl.dataset.featureDictionaryType || '',
        feature_dictionary_options: (() => {
          try {
            if (featureEl.dataset.dictionaryOptions) {
              return JSON.parse(featureEl.dataset.dictionaryOptions);
            }
          } catch (e) {
            console.warn(
              '[FeatureBuilder] Failed parsing dictionary options',
              e
            );
          }
          return {};
        })(),
        feature_required: featureEl.dataset.featureRequired === '1',
        feature_unit: featureEl.dataset.featureUnit || '',
        feature_description: featureEl.dataset.featureDescription || '',
        config: {},
      };

      this.addFeatureItem(featureData);
    }

    /**
     * Add a feature item to the builder
     *
     * @param {Object} featureData - Feature configuration data
     * @returns {string} The generated item ID
     */
    addFeatureItem(featureData) {
      const itemId = featureData.id || `item-${this.nextItemId++}`;
      featureData.id = itemId;

      // Check if already exists
      if (this.items.has(itemId)) {
        console.warn('[FeatureBuilder] Item already exists:', itemId);
        return itemId;
      }

      // Clone template and populate
      const template = document.getElementById('ps-feature-item-template');
      const itemElement = template.content
        .cloneNode(true)
        .querySelector('.ps-feature-item');

      itemElement.dataset.itemId = itemId;
      itemElement.dataset.featureId = featureData.feature_id;
      itemElement.querySelector('.ps-feature-item__label').textContent =
        featureData.feature_label;

      // Store in state
      this.items.set(itemId, {
        data: featureData,
        element: itemElement,
      });

      // Append to container
      this.itemsContainer.appendChild(itemElement);

      // Attach item-specific event listeners
      this.attachItemListeners(itemElement, itemId);

      // Initialize configuration if data exists
      if (featureData.config && Object.keys(featureData.config).length > 0) {
        this.configManager.populateConfig(itemElement, featureData);
        this.updatePreview(itemElement, featureData);
      }

      this.updateDropZoneState();
      this.updateStats();
      this.dataManager.save();

      // Track in history
      this.addToHistory({
        type: 'add',
        itemId: itemId,
        data: JSON.parse(JSON.stringify(featureData)),
      });

      // Show toast notification
      this.showToast(`Feature "${featureData.feature_label}" added`, 'success');

      return itemId;
    }

    /**
     * Attach event listeners to a feature item
     *
     * @param {HTMLElement} itemElement - The item DOM element
     * @param {string} itemId - The item ID
     */
    attachItemListeners(itemElement, itemId) {
      // Configure button
      const configBtn = itemElement.querySelector(
        '.ps-feature-item__configure'
      );
      configBtn.addEventListener('click', () => {
        this.configManager.openConfig(itemElement, this.items.get(itemId).data);
      });

      // Remove button
      const removeBtn = itemElement.querySelector('.ps-feature-item__remove');
      removeBtn.addEventListener('click', () => {
        this.removeItem(itemId);
      });

      // Configuration save
      const saveBtn = itemElement.querySelector(
        '.ps-feature-item__config-save'
      );
      saveBtn.addEventListener('click', () => {
        this.configManager.saveConfig(itemElement, itemId);
      });

      // Configuration cancel
      const cancelBtn = itemElement.querySelector(
        '.ps-feature-item__config-cancel'
      );
      cancelBtn.addEventListener('click', () => {
        this.configManager.closeConfig(itemElement);
      });

      // Make draggable
      this.dragDropManager.makeDraggable(itemElement);
    }

    /**
     * Remove an item from the builder
     *
     * @param {string} itemId - The item ID to remove
     */
    removeItem(itemId) {
      const item = this.items.get(itemId);
      if (!item) {
        return;
      }

      // Confirm removal
      if (!confirm(Drupal.t('Remove this feature?'))) {
        return;
      }

      // Store for undo
      const featureData = JSON.parse(JSON.stringify(item.data));

      // Remove from DOM
      item.element.remove();

      // Remove from state
      this.items.delete(itemId);

      this.updateDropZoneState();
      this.updateStats();
      this.dataManager.save();

      // Track in history
      this.addToHistory({
        type: 'remove',
        itemId: itemId,
        data: featureData,
      });

      // Show toast notification
      this.showToast(`Feature "${featureData.feature_label}" removed`, 'info');
    }

    /**
     * Clear all items
     */
    clearAllItems() {
      if (this.items.size === 0) {
        return;
      }

      if (!confirm(Drupal.t('Remove all features? This cannot be undone.'))) {
        return;
      }

      this.items.forEach((item) => {
        item.element.remove();
      });

      this.items.clear();
      this.updateDropZoneState();
      this.updateStats();
      this.dataManager.save();
    }

    /**
     * Update the drop zone visual state
     */
    updateDropZoneState() {
      if (this.items.size > 0) {
        this.dropZone.classList.add('ps-feature-builder__drop-zone--has-items');
      } else {
        this.dropZone.classList.remove(
          'ps-feature-builder__drop-zone--has-items'
        );
      }
    }

    /**
     * Update stats bar with current counts
     */
    updateStats() {
      const statsBar = this.element.querySelector('.ps-feature-builder__stats');
      if (!statsBar) {
        return;
      }

      const currentCount = this.items.size;

      // Get total available features from sidebar
      const allFeatures = this.sidebar.querySelectorAll(
        '.ps-feature-builder__feature-type:not(.ps-feature-builder__feature-type--hidden)'
      );
      const totalCount = allFeatures.length;

      // Update counter with animation
      const counterValue = statsBar.querySelector('[data-stat="added"]');
      if (counterValue) {
        const oldValue = parseInt(counterValue.textContent) || 0;

        // Animate if value changed
        if (oldValue !== currentCount) {
          counterValue.classList.add('stat-pulse');
          setTimeout(() => counterValue.classList.remove('stat-pulse'), 500);
        }

        counterValue.textContent = currentCount;
      }

      // Update total
      const totalValue = statsBar.querySelector('[data-stat="total"]');
      if (totalValue) {
        totalValue.textContent = totalCount;
      }

      // Update last modified timestamp
      const timestamp = statsBar.querySelector('[data-stat="modified"]');
      if (timestamp) {
        const now = new Date();
        const timeString = now.toLocaleTimeString('fr-FR', {
          hour: '2-digit',
          minute: '2-digit',
        });
        timestamp.textContent = timeString;
      }
    }

    /**
     * Add action to history (circular buffer)
     *
     * @param {Object} action - Action object {type, itemId, data}
     */
    addToHistory(action) {
      this.history.push(action);

      // Keep only last N actions
      if (this.history.length > this.maxHistory) {
        this.history.shift();
      }

      // Enable undo button
      const undoBtn = this.element.querySelector('.ps-feature-builder__undo');
      if (undoBtn) {
        undoBtn.disabled = false;
      }
    }

    /**
     * Undo last action
     */
    undo() {
      if (this.history.length === 0) {
        return;
      }

      const lastAction = this.history.pop();

      if (lastAction.type === 'add') {
        // Undo add = remove item
        const item = this.items.get(lastAction.itemId);
        if (item) {
          item.element.remove();
          this.items.delete(lastAction.itemId);
          this.showToast('Action undone', 'info');
        }
      } else if (lastAction.type === 'remove') {
        // Undo remove = add item back
        this.addFeatureItem(lastAction.data);
        this.showToast('Action undone', 'info');
      }

      // Disable undo button if no more history
      if (this.history.length === 0) {
        const undoBtn = this.element.querySelector('.ps-feature-builder__undo');
        if (undoBtn) {
          undoBtn.disabled = true;
        }
      }

      this.updateDropZoneState();
      this.updateStats();
      this.dataManager.save();
    }

    /**
     * Create toast notification container
     *
     * @returns {HTMLElement} Toast container
     */
    createToastContainer() {
      let container = this.element.querySelector(
        '.ps-feature-builder__toast-container'
      );

      if (!container) {
        container = document.createElement('div');
        container.className = 'ps-feature-builder__toast-container';
        this.element.appendChild(container);
      }

      return container;
    }

    /**
     * Show toast notification
     *
     * @param {string} message - Toast message
     * @param {string} type - Toast type (success, info, warning, error)
     */
    showToast(message, type = 'info') {
      const toast = document.createElement('div');
      toast.className = `ps-feature-builder__toast ps-feature-builder__toast--${type}`;

      // Icon based on type
      let icon = '';
      switch (type) {
        case 'success':
          icon =
            '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>';
          break;

        case 'info':
          icon =
            '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>';
          break;

        case 'warning':
          icon =
            '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
          break;

        case 'error':
          icon =
            '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
          break;
      }

      toast.innerHTML = `${icon}<span>${message}</span>`;

      this.toastContainer.appendChild(toast);

      // Trigger animation
      setTimeout(
        () => toast.classList.add('ps-feature-builder__toast--show'),
        10
      );

      // Auto dismiss after 2 seconds
      setTimeout(() => {
        toast.classList.remove('ps-feature-builder__toast--show');
        setTimeout(() => toast.remove(), 300);
      }, 2000);
    }

    /**
     * Attach keyboard shortcuts
     */
    attachKeyboardShortcuts() {
      document.addEventListener('keydown', (e) => {
        // Ignore if typing in input
        if (
          e.target.tagName === 'INPUT' ||
          e.target.tagName === 'TEXTAREA' ||
          e.target.tagName === 'SELECT'
        ) {
          // Except Escape to clear search
          if (
            e.key === 'Escape' &&
            e.target === this.filterManager.searchInput
          ) {
            this.filterManager.searchInput.value = '';
            this.filterManager.filter('');
            e.target.blur();
            return;
          }
          return;
        }

        // Ctrl+K: Focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
          e.preventDefault();
          if (this.filterManager.searchInput) {
            this.filterManager.searchInput.focus();
          }
        }

        // Ctrl+Z: Undo
        if ((e.ctrlKey || e.metaKey) && e.key === 'z') {
          e.preventDefault();
          this.undo();
        }
      });
    }

    /**
     * Update item preview based on configuration
     *
     * @param {HTMLElement} itemElement - The item DOM element
     * @param {Object} featureData - Feature data with configuration
     */
    updatePreview(itemElement, featureData) {
      const previewContent = itemElement.querySelector(
        '.ps-feature-item__preview-content'
      );
      const config = featureData.config || {};
      const type = featureData.feature_type;

      let html = '';

      switch (type) {
        case 'flag':
          html = `<em>${Drupal.t('Present (TRUE)')}</em>`;
          break;

        case 'yesno':
          html = config.value_boolean
            ? `<strong>${Drupal.t('Yes')}</strong>`
            : `<strong>${Drupal.t('No')}</strong>`;
          break;

        case 'numeric':
          if (
            config.value_numeric !== undefined &&
            config.value_numeric !== ''
          ) {
            html = ` < strong > ${config.value_numeric} < / strong > `;
            if (featureData.feature_unit) {
              html += ` ${featureData.feature_unit}`;
            }
          }
          break;

        case 'range':
          if (
            config.value_range_min !== undefined ||
            config.value_range_max !== undefined
          ) {
            const min =
              config.value_range_min !== '' ? config.value_range_min : '?';
            const max =
              config.value_range_max !== '' ? config.value_range_max : '?';
            html = `<strong>${min}</strong> – <strong>${max}</strong>`;
            if (featureData.feature_unit) {
              html += ` ${featureData.feature_unit}`;
            }
          }
          break;

        case 'string':
          if (config.value_string) {
            html = `"${this.escapeHtml(config.value_string)}"`;
          }
          break;

        case 'dictionary':
          if (config.value_string) {
            html = `${Drupal.t('Selected')}: <strong>${this.escapeHtml(
              config.value_string
            )}</strong>`;
          }
          break;
      }

      // Add complement if present
      if (config.complement && config.complement.trim() !== '') {
        if (html) {
          html += ' ';
        }
        html += `<span class="ps-feature-complement">(${this.escapeHtml(
          config.complement
        )})</span>`;
      }

      previewContent.innerHTML = html || '';
    }

    /**
     * Escape HTML for safe rendering
     *
     * @param {string} text - Text to escape
     * @returns {string} Escaped text
     */
    escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }
  }

  /**
   * Drag and Drop Manager
   *
   * Handles all drag-and-drop interactions including:
   * - Dragging from sidebar
   * - Reordering items
   * - Visual feedback
   */
  class DragDropManager {
    constructor(builder) {
      this.builder = builder;
      this.draggedElement = null;
      this.draggedData = null;
      this.init();
    }

    /**
     * Initialize drag and drop
     */
    init() {
      // Make sidebar features draggable
      this.builder.sidebar
        .querySelectorAll('.ps-feature-builder__feature-type')
        .forEach((featureEl) => {
          featureEl.draggable = true;

          featureEl.addEventListener('dragstart', (e) => {
            this.draggedData = {
              feature_id: featureEl.dataset.featureId,
              feature_label: featureEl.dataset.featureLabel,
              feature_type: featureEl.dataset.featureType,
              feature_dictionary_type:
                featureEl.dataset.featureDictionaryType || '',
              feature_dictionary_options: (() => {
                try {
                  if (featureEl.dataset.dictionaryOptions) {
                    return JSON.parse(featureEl.dataset.dictionaryOptions);
                  }
                } catch (err) {
                  console.warn(
                    '[DragDrop] Failed parsing dictionary options',
                    err
                  );
                }
                return {};
              })(),
              feature_required: featureEl.dataset.featureRequired === '1',
              feature_unit: featureEl.dataset.featureUnit || '',
              feature_description: featureEl.dataset.featureDescription || '',
              config: {},
            };

            e.dataTransfer.effectAllowed = 'copy';
            e.dataTransfer.setData('text/plain', featureEl.dataset.featureId);
            featureEl.classList.add(
              'ps-feature-builder__feature-type--dragging'
            );
          });

          featureEl.addEventListener('dragend', () => {
            featureEl.classList.remove(
              'ps-feature-builder__feature-type--dragging'
            );
            this.draggedData = null;
          });
        });

      // Setup drop zones
      this.setupDropZone(this.builder.dropZone);
      this.setupDropZone(this.builder.itemsContainer);
    }

    /**
     * Setup a drop zone
     *
     * @param {HTMLElement} element - Drop zone element
     */
    setupDropZone(element) {
      element.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'copy'; // Only copy from sidebar, no reordering

        // Visual feedback
        if (
          !element.classList.contains(
            'ps-feature-builder__drop-zone--drag-over'
          )
        ) {
          element.classList.add('ps-feature-builder__drop-zone--drag-over');
        }

        // Reordering disabled - no repositioning of dragged elements
      });

      element.addEventListener('dragleave', (e) => {
        if (e.target === element) {
          element.classList.remove('ps-feature-builder__drop-zone--drag-over');
        }
      });

      element.addEventListener('drop', (e) => {
        e.preventDefault();
        element.classList.remove('ps-feature-builder__drop-zone--drag-over');

        if (this.draggedData) {
          // Adding from sidebar
          this.builder.addFeatureItem(this.draggedData);
        }
        // Reordering disabled - draggedElement handling removed
      });
    }

    /**
     * Make an item draggable for reordering
     * DISABLED: Reordering via drag-drop is disabled for added features
     *
     * @param {HTMLElement} itemElement - Item element
     */
    makeDraggable(itemElement) {
      // Reordering disabled - items stay in addition order
      return;
    }

    /**
     * Get the element to insert dragged item before
     *
     * @param {HTMLElement} container - Container element
     * @param {number} y - Mouse Y position
     * @returns {HTMLElement|null} Element to insert before
     */
    getDragAfterElement(container, y) {
      const draggableElements = [
        ...container.querySelectorAll(
          '.ps-feature-item:not(.ps-feature-item--dragging)'
        ),
      ];

      return draggableElements.reduce(
        (closest, child) => {
          const box = child.getBoundingClientRect();
          const offset = y - box.top - box.height / 2;

          if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
          } else {
            return closest;
          }
        },
        { offset: Number.NEGATIVE_INFINITY }
      ).element;
    }
  }

  /**
   * Configuration Manager
   *
   * Manages configuration panels for feature items including:
   * - Opening/closing configuration UI
   * - Populating configuration fields
   * - Saving configuration data
   * - Rendering appropriate fields based on feature type
   */
  class ConfigurationManager {
    constructor(builder) {
      this.builder = builder;
    }

    /**
     * Open configuration panel for an item
     *
     * @param {HTMLElement} itemElement - The item DOM element
     * @param {Object} featureData - Feature data
     */
    openConfig(itemElement, featureData) {
      const configPanel = itemElement.querySelector('.ps-feature-item__config');
      const configContent = itemElement.querySelector(
        '.ps-feature-item__config-content'
      );

      // Close any other open configs
      this.builder.itemsContainer
        .querySelectorAll('.ps-feature-item__config')
        .forEach((panel) => {
          if (panel !== configPanel) {
            panel.hidden = true;
          }
        });

      // Generate configuration fields
      this.renderConfigFields(configContent, featureData);

      // Show panel
      configPanel.hidden = false;
    }

    /**
     * Close configuration panel
     *
     * @param {HTMLElement} itemElement - The item DOM element
     */
    closeConfig(itemElement) {
      const configPanel = itemElement.querySelector('.ps-feature-item__config');
      configPanel.hidden = true;
    }

    /**
     * Render configuration fields based on feature type
     *
     * @param {HTMLElement} container - Container for config fields
     * @param {Object} featureData - Feature data
     */
    renderConfigFields(container, featureData) {
      const type = featureData.feature_type;
      const templateId = `ps-feature-config-${type}-template`;
      const template = document.getElementById(templateId);

      if (!template) {
        container.innerHTML = `<p>${Drupal.t(
          'No configuration available for type: @type',
          { '@type': type }
        )}</p>`;
        return;
      }

      // Clone template
      const configFields = template.content.cloneNode(true);

      // Special handling for dictionary: populate options first
      if (type === 'dictionary') {
        this.populateDictionaryOptions(configFields, featureData);
      }

      // Populate with existing values (after options are present)
      this.populateFieldValues(configFields, featureData);

      // Clear and insert
      container.innerHTML = '';
      container.appendChild(configFields);
    }

    /**
     * Populate dictionary select options for dictionary-type features
     *
     * @param {DocumentFragment} fields
     * @param {Object} featureData
     */
    populateDictionaryOptions(fields, featureData) {
      const select = fields.querySelector('select[name="value_dictionary"]');
      if (!select) {
        return;
      }

      let dictType = featureData.feature_dictionary_type || '';

      // If missing, try to recover it from the sidebar item.
      if (!dictType && featureData.feature_id) {
        const sidebarEl = this.builder.element.querySelector(
          `.ps - feature - builder__sidebar .ps - feature - builder__feature - type[data - feature - id = "${featureData.feature_id}"]`
        );
        if (sidebarEl && sidebarEl.dataset.featureDictionaryType) {
          dictType = sidebarEl.dataset.featureDictionaryType;
          featureData.feature_dictionary_type = dictType;
        }
      }

      const settings =
        window.drupalSettings && drupalSettings.ps_features
          ? drupalSettings.ps_features
          : {};
      const dictionaries = settings.dictionaries || {};
      let options = {};

      // Prefer server-injected options stored in featureData.
      if (
        featureData.feature_dictionary_options &&
        Object.keys(featureData.feature_dictionary_options).length
      ) {
        options = featureData.feature_dictionary_options;
      } else if (dictType && dictionaries[dictType]) {
        options = dictionaries[dictType];
      } else {
        // As a last resort, try to read data-dictionary-options from the sidebar element.
        const sidebarEl = this.builder.element.querySelector(
          `.ps - feature - builder__sidebar .ps - feature - builder__feature - type[data - feature - id = "${featureData.feature_id}"]`
        );
        if (sidebarEl && sidebarEl.dataset.dictionaryOptions) {
          try {
            options = JSON.parse(sidebarEl.dataset.dictionaryOptions) || {};
          } catch (e) {
            // eslint-disable-next-line no-console
            console.warn(
              '[FeatureBuilder] Failed to parse sidebar dictionary options',
              e
            );
          }
        }
      }

      if (!dictType) {
        select.innerHTML = `<option value="">${Drupal.t(
          'No dictionary type configured'
        )}</option>`;
        // eslint-disable-next-line no-console
        console.debug(
          '[FeatureBuilder] populateDictionaryOptions: missing dictType for',
          featureData.feature_id
        );
        return;
      }

      // Reset options
      select.innerHTML = '';

      // Placeholder
      const placeholder = document.createElement('option');
      placeholder.value = '';
      placeholder.textContent = Drupal.t('- Select -');
      select.appendChild(placeholder);

      // Add options
      Object.keys(options).forEach((code) => {
        const opt = document.createElement('option');
        opt.value = code;
        opt.textContent = options[code];
        select.appendChild(opt);
      });

      // Fallback: if only placeholder present, inform user.
      if (select.options.length <= 1) {
        const infoOpt = document.createElement('option');
        infoOpt.value = '';
        infoOpt.textContent = Drupal.t('No entries defined');
        select.appendChild(infoOpt);
      }
      // Debug
      // eslint-disable-next-line no-console
      console.debug(
        '[FeatureBuilder] populateDictionaryOptions',
        dictType,
        'count=',
        select.options.length - 1
      );
    }

    /**
     * Populate configuration fields with existing values
     *
     * @param {DocumentFragment} fields - Configuration fields
     * @param {Object} featureData - Feature data
     */
    populateFieldValues(fields, featureData) {
      const config = featureData.config || {};

      // Set readonly unit field if present
      const unitInput = fields.querySelector('input[name="unit"]');
      if (unitInput && featureData.feature_unit) {
        unitInput.value = featureData.feature_unit;
      }

      // Populate based on config data
      Object.keys(config).forEach((key) => {
        const input = fields.querySelector(`[name = "${key}"]`);
        if (input) {
          if (input.type === 'checkbox') {
            input.checked = config[key];
          } else {
            input.value = config[key];
          }
        }
      });
    }

    /**
     * Populate existing configuration (for loaded items)
     *
     * @param {HTMLElement} itemElement - The item DOM element
     * @param {Object} featureData - Feature data with config
     */
    populateConfig(itemElement, featureData) {
      // Render configuration fields for this feature type.
      const configContent = itemElement.querySelector(
        '.ps-feature-item__config-content'
      );
      if (!configContent) {
        return;
      }

      this.renderConfigFields(configContent, featureData);

      // Pre-fill config values if they exist.
      if (featureData.config && Object.keys(featureData.config).length > 0) {
        const inputs = configContent.querySelectorAll(
          '.ps-feature-config-input'
        );

        // Map template input names to config keys.
        const nameMapping = {
          default_value: 'value_boolean',
          value_string: 'value_string',
          value_numeric: 'value_numeric',
          value_min: 'value_range_min',
          value_max: 'value_range_max',
          value_dictionary: 'value_string', // Dictionary stores in value_string.
        };

        inputs.forEach((input) => {
          const inputName = input.name;
          if (!inputName || inputName === 'unit') {
            return; // Skip readonly unit fields.
          }

          const configKey = nameMapping[inputName] || inputName;

          if (!(configKey in featureData.config)) {
            return;
          }

          const value = featureData.config[configKey];

          if (input.type === 'checkbox') {
            input.checked = !!value;
          } else if (input.type === 'number') {
            input.value = value !== null && value !== '' ? value : '';
          } else if (input.tagName === 'SELECT') {
            // For selects, set value after options are populated.
            input.value = value !== null ? value : '';
          } else {
            input.value = value !== null ? value : '';
          }
        });
      }
    }

    /**
     * Save configuration from panel to item data
     *
     * @param {HTMLElement} itemElement - The item DOM element
     * @param {string} itemId - The item ID
     */
    saveConfig(itemElement, itemId) {
      const item = this.builder.items.get(itemId);
      if (!item) {
        return;
      }

      const configContent = itemElement.querySelector(
        '.ps-feature-item__config-content'
      );
      const inputs = configContent.querySelectorAll('.ps-feature-config-input');

      const config = {};

      // Map template input names to config storage keys.
      const saveMapping = {
        default_value: 'value_boolean',
        value_string: 'value_string',
        value_numeric: 'value_numeric',
        value_min: 'value_range_min',
        value_max: 'value_range_max',
        value_dictionary: 'value_string',
        complement: 'complement',
      };

      inputs.forEach((input) => {
        const name = input.name;
        if (!name || input.readOnly) {
          return;
        }

        const configKey = saveMapping[name] || name;

        if (input.type === 'checkbox') {
          config[configKey] = input.checked;
        } else if (input.type === 'number') {
          config[configKey] =
            input.value !== '' ? parseFloat(input.value) : null;
        } else {
          config[configKey] = input.value || null;
        }
      });

      // Update item data
      item.data.config = config;

      // Update preview
      this.builder.updatePreview(itemElement, item.data);

      // Close config panel
      this.closeConfig(itemElement);

      // Save to hidden input
      this.builder.dataManager.save();
    }
  }

  /**
   * Data Manager
   *
   * Handles persistence of feature data to the hidden form input.
   * Serializes the current state and keeps it in sync with the UI.
   */
  class DataManager {
    constructor(builder) {
      this.builder = builder;
    }

    /**
     * Save current state to hidden input
     */
    save() {
      const data = [];

      // Iterate through items in DOM order (to preserve drag-drop order)
      this.builder.itemsContainer
        .querySelectorAll('.ps-feature-item')
        .forEach((itemElement) => {
          const itemId = itemElement.dataset.itemId;
          const item = this.builder.items.get(itemId);

          if (item) {
            data.push(item.data);
          }
        });

      this.builder.dataInput.value = JSON.stringify(data);

      console.log('[DataManager] Saved', data.length, 'items');
    }
  }

  /**
   * Filter Manager
   *
   * Manages search/filter functionality in the sidebar.
   * Filters feature types and groups based on text input.
   */
  class FilterManager {
    constructor(builder) {
      this.builder = builder;
      this.searchInput = builder.sidebar.querySelector(
        '.ps-feature-builder__search-input'
      );
      this.clearBtn = builder.sidebar.querySelector(
        '.ps-feature-builder__search-clear'
      );

      this.init();
    }

    /**
     * Initialize filter functionality
     */
    init() {
      if (!this.searchInput) {
        return;
      }

      // Search input
      this.searchInput.addEventListener('input', () => {
        this.filter(this.searchInput.value);
      });

      // Clear button
      if (this.clearBtn) {
        this.clearBtn.addEventListener('click', () => {
          this.searchInput.value = '';
          this.filter('');
          this.searchInput.focus();
        });
      }

      // Group toggle buttons
      this.builder.sidebar
        .querySelectorAll('.ps-feature-builder__group-toggle')
        .forEach((toggleBtn) => {
          toggleBtn.addEventListener('click', () => {
            const group = toggleBtn.closest('.ps-feature-builder__group');
            const expanded = group.dataset.expanded === 'true';
            group.dataset.expanded = !expanded;
          });
        });
    }

    /**
     * Filter features by search query with fuzzy matching and highlighting
     *
     * @param {string} query - Search query
     */
    filter(query) {
      const normalizedQuery = query.toLowerCase().trim();

      if (!normalizedQuery) {
        // Show all and remove highlights
        this.builder.sidebar
          .querySelectorAll('.ps-feature-builder__feature-type')
          .forEach((el) => {
            el.classList.remove('ps-feature-builder__feature-type--hidden');
            this.removeHighlight(el);
          });
        this.builder.sidebar
          .querySelectorAll('.ps-feature-builder__group')
          .forEach((el) => {
            el.classList.remove('ps-feature-builder__group--hidden');
          });
        return;
      }

      // Filter features with fuzzy matching
      this.builder.sidebar
        .querySelectorAll('.ps-feature-builder__feature-type')
        .forEach((featureEl) => {
          const label = featureEl.dataset.featureLabel;
          const description = featureEl.dataset.featureDescription || '';
          const type = featureEl.dataset.featureType || '';

          const labelLower = label.toLowerCase();
          const descLower = description.toLowerCase();
          const typeLower = type.toLowerCase();

          // Check for matches
          const labelMatches = this.fuzzyMatch(labelLower, normalizedQuery);
          const descMatches = descLower.includes(normalizedQuery);
          const typeMatches = typeLower.includes(normalizedQuery);

          const matches = labelMatches || descMatches || typeMatches;

          if (matches) {
            featureEl.classList.remove(
              'ps-feature-builder__feature-type--hidden'
            );

            // Highlight matched text in label
            if (labelMatches) {
              this.highlightText(featureEl, label, normalizedQuery);
            } else {
              this.removeHighlight(featureEl);
            }
          } else {
            featureEl.classList.add('ps-feature-builder__feature-type--hidden');
            this.removeHighlight(featureEl);
          }
        });

      // Hide empty groups
      this.builder.sidebar
        .querySelectorAll('.ps-feature-builder__group')
        .forEach((groupEl) => {
          const visibleFeatures = groupEl.querySelectorAll(
            '.ps-feature-builder__feature-type:not(.ps-feature-builder__feature-type--hidden)'
          );

          if (visibleFeatures.length === 0) {
            groupEl.classList.add('ps-feature-builder__group--hidden');
          } else {
            groupEl.classList.remove('ps-feature-builder__group--hidden');
          }
        });
    }

    /**
     * Fuzzy match - allows characters in order but not necessarily consecutive
     *
     * @param {string} text - Text to search in
     * @param {string} query - Query to search for
     * @returns {boolean} True if matches
     */
    fuzzyMatch(text, query) {
      let textIndex = 0;
      let queryIndex = 0;

      while (textIndex < text.length && queryIndex < query.length) {
        if (text[textIndex] === query[queryIndex]) {
          queryIndex++;
        }
        textIndex++;
      }

      return queryIndex === query.length;
    }

    /**
     * Highlight matched text in feature label
     *
     * @param {HTMLElement} featureEl - Feature element
     * @param {string} text - Original text
     * @param {string} query - Query to highlight
     */
    highlightText(featureEl, text, query) {
      const labelEl = featureEl.querySelector(
        '.ps-feature-builder__feature-label'
      );
      if (!labelEl) {
        return;
      }

      // Store original if not already stored
      if (!labelEl.dataset.originalText) {
        labelEl.dataset.originalText = text;
      }

      // Simple substring highlight (can be enhanced to fuzzy)
      const textLower = text.toLowerCase();
      const index = textLower.indexOf(query);

      if (index !== -1) {
        const before = text.substring(0, index);
        const match = text.substring(index, index + query.length);
        const after = text.substring(index + query.length);

        labelEl.innerHTML = `${before} < mark class = "ps-feature-highlight" > ${match} < / mark > ${after}`;
      }
    }

    /**
     * Remove highlight from feature label
     *
     * @param {HTMLElement} featureEl - Feature element
     */
    removeHighlight(featureEl) {
      const labelEl = featureEl.querySelector(
        '.ps-feature-builder__feature-label'
      );
      if (!labelEl) {
        return;
      }

      if (labelEl.dataset.originalText) {
        labelEl.textContent = labelEl.dataset.originalText;
      }
    }
  }

  /**
   * Drupal behavior for initializing feature widget builders
   */
  Drupal.behaviors.psFeatureWidgetBuilder = {
    attach: function (context, settings) {
      once('ps-feature-builder', '.ps-feature-builder', context).forEach(
        (element) => {
          // Find Drupal's multi-value wrapper
          const fieldWrapper = element.closest('[id$="-add-more-wrapper"]');

          if (fieldWrapper) {
            // Move widget outside Drupal's wrapper
            fieldWrapper.parentNode.insertBefore(
              element,
              fieldWrapper.nextSibling
            );

            // Hide Drupal's wrapper (contains table, buttons, etc.)
            fieldWrapper.style.display = 'none';

            console.log('[FeatureBuilder] Widget moved outside Drupal wrapper');
          }

          // Initialize builder
          const builder = new FeatureWidgetBuilder(element);

          // Store instance on element for potential external access
          element.featureBuilder = builder;
        }
      );
    },
  };
})(Drupal, once);
