/**
 * Feature Builder - Renderer.
 *
 * Responsibilities:
 * - Project state into DOM.
 * - Handle UI interactions (catalogue open/search/add, remove, group collapse).
 * - Host payload editor instances and route payload changes to state manager.
 *
 * Rendering strategy:
 * - Full body re-render for structural changes (add/remove/reorder).
 * - Lightweight updates for payload-only changes.
 */
(function () {
  'use strict';

  /**
   * Resolve payload editor class by feature type.
   *
   * Unknown types intentionally fallback to BasePayloadEditor to keep the UI
   * operational even when a dedicated JS editor is missing.
   */
  function createEditor(definition, payload, onChange) {
    const Editors = window.FeatureBuilderEditors;
    const EditorClass = Editors[definition.type] || Editors.BasePayloadEditor;
    return new EditorClass(definition, payload, onChange);
  }

  /**
   * Icon mapping by feature type.
   */
  function typeIcon(type) {
    const map = {
      flag: 'bi-toggle-on',
      yes_no: 'bi-check-square',
      numeric: 'bi-123',
      range: 'bi-arrows-expand-vertical',
      text: 'bi-pencil',
      list: 'bi-list-check',
      date: 'bi-calendar-event',
      dictionary: 'bi-book',
    };
    return map[type] || 'bi-puzzle';
  }

  /**
   * DOM renderer/controller for the Feature Builder widget.
   */
  class FeatureBuilderRenderer {
    /**
     * @param {HTMLElement} mount - L'élément racine du widget
     * @param {FeatureBuilderStateManager} state
     * @param {CatalogueService} catalogue
     */
    constructor(mount, state, catalogue) {
      this.mount = mount;
      this.state = state;
      this.catalogue = catalogue;
      this._catalogueOpen = false;
      this._searchQuery = '';
      this._editorInstances = {}; // featureId -> editor instance
      this._docClickHandler = null; // tracked to prevent duplicate global listeners
    }

    /**
     * Initial full render.
     */
    render() {
      this.mount.innerHTML = this._buildWidget();
      this._bindStaticEvents();
      this._renderFeatures();
    }

    /**
     * Update lifecycle called by state manager subscription.
     *
     * `payload` updates are optimized to avoid structural DOM churn.
     */
    update(state, changeType = 'structure') {
      if (changeType === 'payload') {
        this._updateCount(state.features.length);
        return;
      }

      this._renderFeatures();
      this._updateCount(state.features.length);
      this._updateCatalogueItems(state.features.map(f => f.id));
    }

    /**
     * Build static widget shell.
     */
    _buildWidget() {
      const count = this.state.getFeatures().length;
      const addedIds = this.state.getFeatures().map(f => f.id);
      return `
<div class="fb-widget">
  <div class="fb-widget-header">
    <div>
      <strong><i class="bi bi-puzzle-fill" style="color:#009b77"></i> ${Drupal.t('Feature Builder')}</strong>
      <span class="fb-widget-count">${Drupal.formatPlural(count, '@count feature filled in', '@count features filled in')}</span>
    </div>
    <div style="position:relative">
      <button type="button" class="fb-btn fb-btn-primary" id="fb-add-btn">
        <i class="bi bi-plus-lg"></i> ${Drupal.t('Add a feature')}
      </button>
      <div class="fb-catalogue-menu" id="fb-catalogue-menu" style="display:none;right:0;top:calc(100% + 4px)">
        <div class="fb-catalogue-search-wrap">
          <input type="text" class="fb-catalogue-search" id="fb-catalogue-search" placeholder="${Drupal.t('Search for a feature…')}">
        </div>
        <div class="fb-catalogue-list" id="fb-catalogue-list">
          ${this._buildCatalogueList(addedIds)}
        </div>
      </div>
    </div>
  </div>
  <div class="fb-widget-body" id="fb-widget-body">
    <div class="fb-widget-empty"><i class="bi bi-puzzle"></i>${Drupal.t('No features yet. Click Add to get started.')}</div>
  </div>
</div>`;
    }

    /**
     * Build grouped catalogue menu markup.
     */
    _buildCatalogueList(addedIds) {
      const grouped = this.catalogue.groupedDefinitions();
      if (!grouped.length) {
        return '<div class="fb-catalogue-no-results">' + Drupal.t('Empty catalogue.') + '</div>';
      }
      let html = '';
      grouped.forEach(({ group, definitions }) => {
        html += `<div class="fb-catalogue-group-header">${this._esc(group.label)}</div>`;
        definitions.forEach(def => {
          const added = addedIds.includes(def.id) ? 'is-added' : '';
          html += `
<div class="fb-catalogue-item ${added}" data-def-id="${this._esc(def.id)}" data-label="${this._esc(def.label)}" data-type="${this._esc(def.type)}">
  <span>${added ? '<i class="bi bi-check-circle-fill" style="color:#009b77;margin-right:4px"></i>' : ''} ${this._esc(def.label)}</span>
  <span class="badge-type type-${this._esc(def.type)}">${this._esc(def.type)}</span>
</div>`;
        });
      });
      return html;
    }

    /**
     * Build grouped feature blocks and mount payload editors.
     *
     * Important:
     * - This method recreates body DOM on structural changes.
     * - Event bindings and sortable instances must be reattached afterwards.
     */
    _renderFeatures() {
      const body = this.mount.querySelector('#fb-widget-body');
      if (!body) return;
      const features = this.state.getFeatures();

      if (!features.length) {
        body.innerHTML = '<div class="fb-widget-empty"><i class="bi bi-puzzle"></i>' + Drupal.t('No features yet. Click Add to get started.') + '</div>';
        this._editorInstances = {};
        return;
      }

      // Group features by configured catalogue groups, with `_other` fallback.
      const grouped = this._groupFeatures(features);
      let html = '';
      grouped.forEach(({ groupLabel, groupId, items }) => {
        const collapseId = 'fb-grp-' + groupId;
        html += `
<div class="fb-group-block" id="${collapseId}">
  <div class="fb-group-header" data-group="${this._esc(groupId)}">
    <div class="fb-group-title">
      <i class="bi bi-collection"></i>
      ${this._esc(groupLabel)}
      <span class="fb-group-count">${items.length}</span>
    </div>
    <i class="bi bi-chevron-up fb-group-chevron"></i>
  </div>
  <div class="fb-items" id="fb-items-${this._esc(groupId)}" data-fb-group="${this._esc(groupId)}">
    ${items.map(f => this._buildFeatureItemShell(f)).join('')}
  </div>
</div>`;
      });
      body.innerHTML = html;

      // Mount payload editors after shell insertion.
      features.forEach(f => this._mountEditor(f));

      // Rebind listeners for freshly rendered DOM nodes.
      this._bindGroupToggle();
      this._bindRemoveButtons();
    }

    /**
     * Build HTML shell for one feature item (payload editor mounted separately).
     */
    _buildFeatureItemShell(feature) {
      const def = this.catalogue.getById(feature.id);
      const type = feature.type || (def && def.type) || 'unknown';
      return `
<div class="fb-item" data-feature-id="${this._esc(feature.id)}">
  <div class="drag-handle"><i class="bi bi-grip-vertical"></i></div>
  <div class="fb-item-label">
    <i class="bi ${typeIcon(type)}"></i>
    ${this._esc(feature.label || (def && def.label) || feature.id)}
    <span class="badge-type type-${this._esc(type)}">${this._esc(type)}</span>
  </div>
  <div class="fb-item-payload" id="fb-payload-${this._esc(feature.id)}"></div>
  <div class="fb-item-actions">
    <button type="button" class="fb-btn fb-btn-danger fb-remove" data-feature-id="${this._esc(feature.id)}" title="${Drupal.t('Remove')}">
      <i class="bi bi-trash"></i>
    </button>
  </div>
</div>`;
    }

    /**
     * Mount payload editor for a feature.
     */
    _mountEditor(feature) {
      const container = this.mount.querySelector('#fb-payload-' + feature.id);
      if (!container) return;
      // Safety fallback when catalogue is stale/missing definition entry.
      const def = this.catalogue.getById(feature.id) || { id: feature.id, type: feature.type, label: feature.label, payload_defaults: {}, options: [] };
      const editor = createEditor(def, feature.payload, (newPayload) => {
        this.state.updatePayload(feature.id, newPayload);
      });
      this._editorInstances[feature.id] = editor;
      container.innerHTML = editor.render();
      editor.bindEvents(container);
    }

    /**
     * Group active features by known catalogue groups.
     *
     * Ordering policy:
     * - known groups in catalogue order,
     * - then unknown groups.
     */
    _groupFeatures(features) {
      const groups = this.catalogue.getGroups();
      const byGroup = {};
      features.forEach(f => {
        const gid = f.group || '_other';
        if (!byGroup[gid]) byGroup[gid] = [];
        byGroup[gid].push(f);
      });
      const result = [];
      // Known groups first.
      groups.forEach(g => {
        if (byGroup[g.id]) {
          result.push({ groupId: g.id, groupLabel: g.label, items: byGroup[g.id] });
        }
      });
      // Unknown groups (or missing configuration) remain visible at the end.
      Object.keys(byGroup).forEach(gid => {
        if (!groups.some(g => g.id === gid)) {
      result.push({ groupId: gid, groupLabel: gid === '_other' ? Drupal.t('Other') : gid, items: byGroup[gid] });
        }
      });
      return result;
    }

    _bindStaticEvents() {
      const addBtn = this.mount.querySelector('#fb-add-btn');
      const menu = this.mount.querySelector('#fb-catalogue-menu');
      const search = this.mount.querySelector('#fb-catalogue-search');
      const list = this.mount.querySelector('#fb-catalogue-list');

      addBtn && addBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        this._catalogueOpen = !this._catalogueOpen;
        menu.style.display = this._catalogueOpen ? 'flex' : 'none';
        if (this._catalogueOpen) {
          this._refreshCatalogue();
          search && search.focus();
        }
      });

      search && search.addEventListener('input', () => {
        this._searchQuery = search.value;
        this._filterCatalogue(search.value);
      });

      list && list.addEventListener('click', (e) => {
        const item = e.target.closest('.fb-catalogue-item');
        if (!item || item.classList.contains('is-added')) return;
        const defId = item.dataset.defId;
        const def = this.catalogue.getById(defId);
        if (def) this.state.addFeature(def);
        this._closeCatalogue();
      });

      // Close catalogue on outside click.
      // We keep a single document-level listener to avoid leaks across re-renders.
      if (this._docClickHandler) {
        document.removeEventListener('click', this._docClickHandler);
      }
      this._docClickHandler = (e) => {
        if (this._catalogueOpen && !this.mount.querySelector('#fb-add-btn').contains(e.target) && !menu.contains(e.target)) {
          this._closeCatalogue();
        }
      };
      document.addEventListener('click', this._docClickHandler);
    }

    _bindGroupToggle() {
      // Group blocks are collapsible for large feature sets.
      this.mount.querySelectorAll('.fb-group-header').forEach(header => {
        header.addEventListener('click', () => {
          const block = header.closest('.fb-group-block');
          block && block.classList.toggle('collapsed');
        });
      });
    }

    _bindRemoveButtons() {
      // Remove actions are rebound after each structural re-render.
      this.mount.querySelectorAll('.fb-remove').forEach(btn => {
        btn.addEventListener('click', () => {
          const fid = btn.dataset.featureId;
          if (fid) this.state.removeFeature(fid);
        });
      });
    }

    _closeCatalogue() {
      this._catalogueOpen = false;
      const menu = this.mount.querySelector('#fb-catalogue-menu');
      if (menu) menu.style.display = 'none';
    }

    _refreshCatalogue() {
      const addedIds = this.state.getFeatures().map(f => f.id);
      const list = this.mount.querySelector('#fb-catalogue-list');
      if (list) list.innerHTML = this._buildCatalogueList(addedIds);
    }

    _filterCatalogue(query) {
      // Client-side filtering avoids extra network calls and keeps UI responsive.
      const q = query.toLowerCase().trim();
      const items = this.mount.querySelectorAll('.fb-catalogue-item');
      let hasAny = false;
      items.forEach(item => {
        const label = (item.dataset.label || '').toLowerCase();
        const matches = !q || label.includes(q);
        item.classList.toggle('is-hidden', !matches);
        if (matches) hasAny = true;
      });
      const noRes = this.mount.querySelector('.fb-catalogue-no-results');
      if (!hasAny && !noRes) {
        const list = this.mount.querySelector('#fb-catalogue-list');
        if (list) list.insertAdjacentHTML('beforeend', '<div class="fb-catalogue-no-results">' + Drupal.t('No features found.') + '</div>');
      } else if (hasAny && noRes) {
        noRes.remove();
      }
    }

    _updateCatalogueItems(addedIds) {
      // Mark already-added definitions to prevent accidental duplicates.
      this.mount.querySelectorAll('.fb-catalogue-item').forEach(item => {
        const isAdded = addedIds.includes(item.dataset.defId);
        item.classList.toggle('is-added', isAdded);
      });
    }

    _updateCount(n) {
      const el = this.mount.querySelector('.fb-widget-count');
      if (el) el.textContent = Drupal.formatPlural(n, '@count feature filled in', '@count features filled in');
    }

    _esc(str) {
      return String(str ?? '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
  }

  window.FeatureBuilderRenderer = FeatureBuilderRenderer;
})();
