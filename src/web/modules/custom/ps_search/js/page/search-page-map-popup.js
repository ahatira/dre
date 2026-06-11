(function (Drupal, once) {
  'use strict';

  const PANEL_ANIM_MS = 280;

  /**
   * Whether the desktop floating map panel is used (vs mobile card).
   *
   * @param {HTMLElement} root
   *   Search view root.
   *
   * @return {boolean}
   */
  function usesFloatPanel(root) {
    return window.matchMedia('(min-width: 992px)').matches
      && !Drupal.psSearchMap.isListVisible(root);
  }

  Drupal.behaviors.psSearchPageMapPopup = {
    attach(context) {
      once('ps-search-map-popup', '.ps-search-view', context).forEach(function (root) {
        const popup = root.querySelector('.js-ps-search-map-popup');
        const popupContent = root.querySelector('.js-ps-search-map-popup-content');
        const popupCloseBtn = root.querySelector('.js-ps-search-map-popup-close');
        const floatPanel = root.querySelector('.js-ps-search-map-float-panel');
        const floatContent = root.querySelector('.js-ps-search-map-float-content');
        let openNid = null;
        let floatCloseTimer = null;

        /**
         * Clones an offer card for display in a panel.
         *
         * @param {HTMLElement} card
         *   Source offer card from the results list.
         *
         * @return {HTMLElement}
         *   Cloned card element.
         */
        function cloneOfferCard(card) {
          const clone = card.cloneNode(true);
          clone.classList.add('ps-offer-search-card--float-panel');
          clone.classList.remove('is-map-sync-active');
          clone.removeAttribute('id');
          return clone;
        }

        /**
         * Copies interactive slots (favorite lazy builder, etc.) from source card.
         *
         * @param {HTMLElement} clone
         *   Cloned card.
         * @param {HTMLElement} source
         *   Source card from the list.
         */
        function syncInteractiveSlots(clone, source) {
          const cloneFavorite = clone.querySelector('.ps-offer-search-card__action--favorite');
          const sourceFavorite = source.querySelector('.ps-offer-search-card__action--favorite');
          if (cloneFavorite && sourceFavorite && sourceFavorite.innerHTML.trim()) {
            cloneFavorite.innerHTML = sourceFavorite.innerHTML;
          }
        }

        /**
         * Mounts a cloned offer card and re-initializes front behaviors.
         *
         * @param {HTMLElement} card
         *   Source offer card.
         * @param {HTMLElement} container
         *   Target container element.
         *
         * @return {HTMLElement}
         *   Mounted clone.
         */
        function mountOfferCard(card, container) {
          const clone = cloneOfferCard(card);
          syncInteractiveSlots(clone, card);

          if (Drupal.psOfferSearchCard?.resetOnceAttributes) {
            Drupal.psOfferSearchCard.resetOnceAttributes(clone);
          }

          container.textContent = '';
          container.appendChild(clone);

          if (Drupal.psOfferSearchCard?.initCard) {
            Drupal.psOfferSearchCard.initCard(clone);
          }
          if (typeof Drupal.attachBehaviors === 'function') {
            Drupal.attachBehaviors(clone);
          }

          return clone;
        }

        /**
         * Closes the mobile floating offer card.
         */
        function closeFloatingPopup() {
          if (!popup || !popupContent) {
            return;
          }
          popup.hidden = true;
          popupContent.textContent = '';
        }

        /**
         * Hides the desktop floating map panel with slide/fade animation.
         *
         * @param {boolean} clearContent
         *   Whether to clear panel content after the animation.
         */
        function closeFloatPanel(clearContent) {
          if (!floatPanel) {
            return;
          }

          window.clearTimeout(floatCloseTimer);

          if (floatPanel.hidden || !floatPanel.classList.contains('is-open')) {
            floatPanel.hidden = true;
            floatPanel.classList.remove('is-open', 'is-closing');
            root.classList.remove('ps-search-view--float-panel-open');
            if (clearContent !== false && floatContent) {
              floatContent.textContent = '';
            }
            return;
          }

          floatPanel.classList.remove('is-open');
          floatPanel.classList.add('is-closing');
          root.classList.remove('ps-search-view--float-panel-open');

          floatCloseTimer = window.setTimeout(function () {
            floatPanel.hidden = true;
            floatPanel.classList.remove('is-closing');
            if (clearContent !== false && floatContent) {
              floatContent.textContent = '';
            }
          }, PANEL_ANIM_MS);
        }

        /**
         * Hides all offer panels and clears marker selection.
         */
        function closeAllPanels() {
          closeFloatingPopup();
          closeFloatPanel(true);
          openNid = null;
          root.dispatchEvent(new CustomEvent('ps-search-map-marker-clear'));
        }

        /**
         * Shows offer in the mobile floating card over the map.
         *
         * @param {HTMLElement} card
         *   Source offer card.
         * @param {string} nid
         *   Offer node id.
         */
        function openFloatingPopup(card, nid) {
          if (!popup || !popupContent) {
            return;
          }

          if (openNid === nid && !popup.hidden) {
            closeAllPanels();
            return;
          }

          popupContent.textContent = '';
          mountOfferCard(card, popupContent);
          popup.hidden = false;
          openNid = nid;

          root.dispatchEvent(new CustomEvent('ps-search-map-marker-select', {
            detail: { nid },
          }));
        }

        /**
         * Shows offer in the desktop floating map panel.
         *
         * @param {HTMLElement} card
         *   Source offer card.
         * @param {string} nid
         *   Offer node id.
         * @param {object} [options]
         *   Panel open options.
         * @param {boolean} [options.allowToggleClose]
         *   Close when clicking the same marker again.
         */
        function openFloatPanel(card, nid, options) {
          if (!floatContent || !floatPanel) {
            return;
          }

          const allowToggleClose = !options || options.allowToggleClose !== false;

          if (allowToggleClose && openNid === nid && !floatPanel.hidden && floatPanel.classList.contains('is-open')) {
            closeAllPanels();
            return;
          }

          window.clearTimeout(floatCloseTimer);
          closeFloatingPopup();
          mountOfferCard(card, floatContent);

          floatPanel.hidden = false;
          floatPanel.classList.remove('is-closing');
          floatPanel.classList.remove('is-open');
          root.classList.add('ps-search-view--float-panel-open');
          openNid = nid;

          requestAnimationFrame(function () {
            requestAnimationFrame(function () {
              floatPanel.classList.add('is-open');
            });
          });

          root.dispatchEvent(new CustomEvent('ps-search-map-marker-select', {
            detail: { nid },
          }));
        }

        /**
         * Opens the float panel for the currently pinned offer, if any.
         */
        function openFloatPanelForSelection() {
          if (!usesFloatPanel(root)) {
            return;
          }

          const nid = Drupal.psSearchMap.getSelectedOfferId(root);
          if (!nid) {
            return;
          }

          const card = root.querySelector(`.ps-offer-search-card[data-offer-id="${nid}"]`);
          if (card) {
            openFloatPanel(card, nid, { allowToggleClose: false });
            return;
          }

          if (!Drupal.psSearchPage?.fetchOfferCard) {
            return;
          }

          Drupal.psSearchPage.fetchOfferCard(String(nid)).then(function (fetchedCard) {
            if (fetchedCard) {
              openFloatPanel(fetchedCard, nid, { allowToggleClose: false });
            }
          });
        }

        /**
         * Opens the appropriate offer panel for the current layout.
         *
         * @param {string} nid
         *   Offer node id.
         */
        function openOfferPanel(nid) {
          const card = root.querySelector(`.ps-offer-search-card[data-offer-id="${nid}"]`);
          if (card) {
            if (usesFloatPanel(root)) {
              openFloatPanel(card, nid);
              return;
            }
            openFloatingPopup(card, nid);
            return;
          }

          if (!Drupal.psSearchPage?.fetchOfferCard) {
            closeAllPanels();
            return;
          }

          Drupal.psSearchPage.fetchOfferCard(String(nid)).then(function (fetchedCard) {
            if (!fetchedCard) {
              closeAllPanels();
              return;
            }

            if (usesFloatPanel(root)) {
              openFloatPanel(fetchedCard, nid);
              return;
            }

            openFloatingPopup(fetchedCard, nid);
          });
        }

        /**
         * Binds marker click handlers for offer panels.
         *
         * @param {object} mapData
         *   PS map data bucket.
         */
        function bindMarkers(mapData) {
          Object.keys(mapData.markersByNid || {}).forEach(function (nid) {
            const marker = mapData.markersByNid[nid];
            if (marker.__psSearchPopupBound) {
              return;
            }

            marker.__psSearchPopupBound = true;

            const clickListener = mapData.oms ? 'spider_click' : 'click';
            google.maps.event.addListener(marker, clickListener, function (event) {
              if (event && typeof event.stop === 'function') {
                event.stop();
              }
              Drupal.psSearchMap.closeMapInfoWindow(mapData);

              if (Drupal.psSearchMap.isListVisible(root)) {
                root.dispatchEvent(new CustomEvent('ps-search-map-marker-select', {
                  detail: { nid },
                }));
                return;
              }

              openOfferPanel(String(nid));
            });
          });
        }

        if (popupCloseBtn) {
          popupCloseBtn.addEventListener('click', closeAllPanels);
        }

        Drupal.psSearchMap.whenMapShellReady(root, function (mapData) {
          const map = mapData.map || mapData.google_map;
          if (map) {
            google.maps.event.addListener(map, 'click', function () {
              if (!Drupal.psSearchMap.isListVisible(root)) {
                closeAllPanels();
              }
            });
          }
        });

        root.addEventListener('ps-search-map-markers-loaded', function (event) {
          bindMarkers(event.detail.mapData);
        });

        document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape') {
            closeAllPanels();
          }
        });

        root.addEventListener('ps-search-list-shown', function () {
          closeFloatPanel(true);
          openNid = null;
        });

        root.addEventListener('ps-search-map-mode', function () {
          window.setTimeout(openFloatPanelForSelection, 50);
        });

        function closePanelsOnResultsReload() {
          closeAllPanels();
        }

        root.addEventListener('ps-search-zone-reloaded', closePanelsOnResultsReload);
        root.addEventListener('ps-search-filters-applied', closePanelsOnResultsReload);
      });
    },
  };
}(Drupal, once));
