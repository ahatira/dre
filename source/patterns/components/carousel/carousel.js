/**
 * Carousel Behavior - Swiper.js Integration
 *
 * Responsive carousel with navigation, pagination, keyboard, touch, and accessibility.
 * Uses Swiper.js v12 (https://swiperjs.com/) for robust carousel functionality.
 *
 * Features:
 * - Navigation: Prev/next buttons with disabled states
 * - Pagination: Clickable bullets synchronized with active slide
 * - Keyboard: Arrow keys, Home/End navigation
 * - Touch: Smooth swipe gestures with momentum scrolling
 * - Accessibility: ARIA announcements and live regions
 * - Loop: Optional infinite scrolling
 * - Auto Height: Adapts height to active slide
 * - Toolbar: Multi-media navigation (photos, 3D visits, plans)
 */

((Drupal, once) => {
  /**
   * Build pagination configuration
   * @param {HTMLElement|null} pagination - Pagination element
   * @returns {Object|boolean} Pagination config or false
   */
  function buildPaginationConfig(pagination) {
    if (!pagination) {
      return false;
    }
    return {
      el: pagination,
      clickable: true,
      bulletClass: 'swiper-pagination-bullet',
      bulletActiveClass: 'swiper-pagination-bullet-active',
    };
  }

  /**
   * Build breakpoints configuration for cards layout
   * @param {boolean} isCards - Is cards layout
   * @param {boolean} isThumbs - Is thumbs layout
   * @returns {Object} Breakpoints config
   */
  function buildBreakpoints(isCards, isThumbs) {
    if (isCards) {
      return {
        320: { slidesPerView: 1, spaceBetween: 28 },
        768: { slidesPerView: 2, spaceBetween: 28 },
        1024: { slidesPerView: 3, spaceBetween: 28 },
        1200: { slidesPerView: 4, spaceBetween: 28 },
        1600: { slidesPerView: 5, spaceBetween: 28 },
      };
    }
    if (isThumbs) {
      return {
        320: { slidesPerView: 4, spaceBetween: 8 },
        640: { slidesPerView: 5, spaceBetween: 8 },
        1024: { slidesPerView: 6, spaceBetween: 8 },
      };
    }
    return {};
  }

  /**
   * Build Swiper configuration object
   * @param {Object} options - Configuration options
   * @returns {Object} Swiper configuration
   */
  function buildSwiperConfig(options) {
    const { hasLoop, hasAutoHeight, isCards, isThumbs, prevButton, nextButton, pagination } =
      options;

    return {
      // Core settings
      loop: isThumbs ? false : hasLoop,
      autoHeight: hasAutoHeight,
      speed: 300,
      spaceBetween: isCards ? 28 : isThumbs ? 8 : 0,
      slidesPerView: isCards ? 1 : isThumbs ? 5 : 1,
      centeredSlides: !isCards && !isThumbs,
      watchOverflow: true,
      watchSlidesProgress: !!isThumbs,
      freeMode: !!isThumbs,
      slideToClickedSlide: !!isThumbs,

      // Navigation module
      navigation: {
        prevEl: prevButton,
        nextEl: nextButton,
        disabledClass: 'is-disabled',
      },

      // Pagination module
      pagination: buildPaginationConfig(pagination),

      // Keyboard module
      keyboard: {
        enabled: true,
        onlyInViewport: true,
      },

      // Accessibility module
      a11y: {
        enabled: true,
        prevSlideMessage: 'Previous slide',
        nextSlideMessage: 'Next slide',
        firstSlideMessage: 'This is the first slide',
        lastSlideMessage: 'This is the last slide',
        paginationBulletMessage: 'Go to slide {{index}}',
      },

      // Breakpoints
      breakpoints: buildBreakpoints(isCards, isThumbs),
    };
  }

  /**
   * Setup thumbs synchronization
   * @param {HTMLElement} carousel - Carousel element
   * @param {Object} config - Swiper configuration
   * @param {string|null} thumbsTargetId - ID of thumbs carousel
   */
  function setupThumbsSync(carousel, config, thumbsTargetId) {
    if (!thumbsTargetId) {
      return;
    }

    const thumbsEl = document.getElementById(thumbsTargetId);
    if (thumbsEl?.swiperInstance) {
      config.thumbs = { swiper: thumbsEl.swiperInstance };
    } else {
      config.thumbs = { swiper: null };
      carousel.__pendingThumbsTargetId = thumbsTargetId;
    }
  }

  /**
   * Initialize thumbs module on a swiper instance
   * @param {Swiper} swiperInstance - The swiper instance
   */
  function initializeThumbsModule(swiperInstance) {
    if (!swiperInstance.thumbs) {
      return;
    }
    if (typeof swiperInstance.thumbs.init === 'function') {
      swiperInstance.thumbs.init();
    }
    if (typeof swiperInstance.thumbs.update === 'function') {
      swiperInstance.thumbs.update();
    }
  }

  /**
   * Wire a main carousel to an initialized thumbs swiper
   * @param {HTMLElement} main - Main carousel element
   * @param {Swiper} thumbsSwiper - Thumbs swiper instance
   * @param {string} id - Thumbs carousel ID
   */
  function bindMainToThumbs(main, thumbsSwiper, id) {
    if (!main.swiperInstance || main.__pendingThumbsTargetId !== id) {
      return;
    }

    main.swiperInstance.params.thumbs = main.swiperInstance.params.thumbs || {};
    main.swiperInstance.params.thumbs.swiper = thumbsSwiper;

    initializeThumbsModule(main.swiperInstance);
    wireManualSync(main.swiperInstance, thumbsSwiper);
    delete main.__pendingThumbsTargetId;
  }

  /**
   * Bind main carousels waiting for this thumbs carousel
   * @param {HTMLElement} carousel - Thumbs carousel element
   * @param {Swiper} swiper - Swiper instance
   */
  function bindPendingMainCarousels(carousel, swiper) {
    const id = carousel.id;
    if (!id) {
      return;
    }

    document.querySelectorAll(`[data-carousel-thumbs="${id}"]`).forEach((main) => {
      bindMainToThumbs(main, swiper, id);
    });
  }

  /**
   * Update toolbar active state based on current slide index
   * @param {Array<HTMLElement>} toolbarItems - Toolbar item elements
   * @param {Swiper} swiper - Swiper instance
   */
  function updateToolbarActiveState(toolbarItems, swiper) {
    const activeIndex = swiper.realIndex;

    toolbarItems.forEach((item, itemIndex) => {
      const itemStartIndex = Number.parseInt(item.dataset.slideIndex, 10);
      const nextItem = toolbarItems[itemIndex + 1];
      const itemEndIndex = nextItem
        ? Number.parseInt(nextItem.dataset.slideIndex, 10) - 1
        : swiper.slides.length - 1;

      const isActive = activeIndex >= itemStartIndex && activeIndex <= itemEndIndex;
      item.dataset.active = isActive ? 'true' : 'false';
      item.setAttribute('aria-selected', isActive ? 'true' : 'false');
      item.tabIndex = isActive ? 0 : -1;
    });
  }

  /**
   * Get next focus index for horizontal navigation
   * @param {string} key - Key pressed
   * @param {number} index - Current item index
   * @param {number} totalItems - Total items
   * @returns {number} Next focus index or -1
   */
  function getNextHorizontalIndex(key, index, totalItems) {
    const lastIndex = totalItems - 1;
    if (key === 'ArrowRight' || key === 'Right') {
      return index === lastIndex ? 0 : index + 1;
    }
    if (key === 'ArrowLeft' || key === 'Left') {
      return index === 0 ? lastIndex : index - 1;
    }
    return -1;
  }

  /**
   * Get next focus index for toolbar navigation
   * @param {string} key - Key pressed
   * @param {number} index - Current item index
   * @param {number} totalItems - Total items
   * @returns {number} Next focus index or -1 if no navigation
   */
  function getNextFocusIndex(key, index, totalItems) {
    const hIdx = getNextHorizontalIndex(key, index, totalItems);
    if (hIdx >= 0) {
      return hIdx;
    }
    const lastIndex = totalItems - 1;
    if (key === 'Home') {
      return 0;
    }
    if (key === 'End') {
      return lastIndex;
    }
    return -1;
  }

  /**
   * Handle toolbar keydown events (navigation and activation)
   * @param {Event} e - Keyboard event
   * @param {HTMLElement} item - Toolbar item
   * @param {number} index - Current item index
   * @param {number} totalItems - Total number of items
   * @param {Array<HTMLElement>} toolbarItems - All toolbar items
   * @param {Swiper} swiper - Swiper instance
   */
  function handleToolbarKeydown(e, item, index, totalItems, toolbarItems, swiper) {
    const key = e.key;
    const nextFocusIdx = getNextFocusIndex(key, index, totalItems);

    if (nextFocusIdx >= 0) {
      const target = toolbarItems[nextFocusIdx];
      if (target) {
        target.focus();
      }
      e.preventDefault();
      e.stopPropagation();
      return;
    }

    // Handle activation (Enter or Space)
    if (key === 'Enter' || key === ' ') {
      const slideIndex = Number.parseInt(item.dataset.slideIndex, 10);
      if (!Number.isNaN(slideIndex)) {
        swiper.slideTo(slideIndex);
      }
      e.preventDefault();
      e.stopPropagation();
    }
  }

  /**
   * Setup toolbar navigation and active state tracking
   * @param {Swiper} swiper - Swiper instance
   * @param {Array<HTMLElement>} toolbarItems - Toolbar item elements
   */
  function setupToolbarNavigation(swiper, toolbarItems) {
    if (toolbarItems.length === 0) {
      return;
    }

    // Click handlers
    toolbarItems.forEach((item) => {
      item.addEventListener('click', () => {
        const slideIndex = Number.parseInt(item.dataset.slideIndex, 10);
        if (!Number.isNaN(slideIndex)) {
          swiper.slideTo(slideIndex);
        }
      });
    });

    // Active state update on slide change
    const updateActiveState = () => {
      updateToolbarActiveState(toolbarItems, swiper);
    };

    swiper.on('slideChange', updateActiveState);
    updateActiveState(); // Set initial state

    // Keyboard interactions for toolbar items (Tabs pattern)
    toolbarItems.forEach((item, index) => {
      item.addEventListener('keydown', (e) => {
        handleToolbarKeydown(e, item, index, toolbarItems.length, toolbarItems, swiper);
      });
    });
  }

  /**
   * Setup ARIA current management on active slide
   * @param {Swiper} swiper - Swiper instance
   */
  function setupA11yAriaCurrent(swiper) {
    const update = () => {
      if (!swiper || !swiper.slides || swiper.slides.length === 0) {
        return;
      }
      const activeIndex = swiper.activeIndex ?? swiper.realIndex ?? 0;
      swiper.slides.forEach((slide, index) => {
        if (index === activeIndex) {
          slide.setAttribute('aria-current', 'true');
        } else {
          slide.removeAttribute('aria-current');
        }
      });
    };
    swiper.on('init', update);
    swiper.on('slideChange', update);
    update();
  }

  /**
   * Update thumb active class based on main swiper index
   * @param {Swiper} mainSwiper - Main swiper instance
   * @param {Swiper} thumbsSwiper - Thumbs swiper instance
   */
  function updateThumbsActiveClass(mainSwiper, thumbsSwiper) {
    const real = mainSwiper.realIndex ?? mainSwiper.activeIndex ?? 0;
    thumbsSwiper.slideTo(real);

    if (!thumbsSwiper.slides?.length) {
      return;
    }

    thumbsSwiper.slides.forEach((slide, i) => {
      if (i === real) {
        slide.classList.add('swiper-slide-thumb-active');
      } else {
        slide.classList.remove('swiper-slide-thumb-active');
      }
    });
  }

  /**
   * Wire manual synchronization between main and thumbs swipers
   * as a fallback in case the Thumbs module is not ready yet.
   * @param {Swiper} mainSwiper
   * @param {Swiper} thumbsSwiper
   */
  function wireManualSync(mainSwiper, thumbsSwiper) {
    if (!mainSwiper || !thumbsSwiper) {
      return;
    }
    if (mainSwiper.__syncedWith === thumbsSwiper) {
      return; // already wired
    }
    mainSwiper.__syncedWith = thumbsSwiper;

    // Clicking a thumb drives the main swiper
    thumbsSwiper.on('tap', () => {
      const idx = thumbsSwiper.clickedIndex;
      if (typeof idx === 'number' && idx >= 0) {
        mainSwiper.slideTo(idx);
      }
    });

    // Main slide changes update the thumbs position and active class
    const updateThumbActive = () => {
      updateThumbsActiveClass(mainSwiper, thumbsSwiper);
    };
    mainSwiper.on('slideChange', updateThumbActive);
    updateThumbActive();
  }

  /**
   * Get configuration from carousel element attributes and classes
   * @param {HTMLElement} carousel - Carousel element
   * @returns {Object} Configuration object
   */
  function getCarouselConfig(carousel) {
    return {
      hasLoop: carousel.classList.contains('ps-carousel--loop'),
      hasAutoHeight: carousel.classList.contains('ps-carousel--auto-height'),
      isCards: carousel.classList.contains('ps-carousel--cards'),
      isThumbs: carousel.getAttribute('data-carousel-role') === 'thumbs',
      thumbsTargetId: carousel.getAttribute('data-carousel-thumbs'),
      attrSlidesPerView: carousel.getAttribute('data-slides-per-view'),
      attrSpaceBetween: carousel.getAttribute('data-space-between'),
    };
  }

  /**
   * Find DOM elements within carousel
   * @param {HTMLElement} carousel - Carousel element
   * @returns {Object} DOM elements
   */
  function getCarouselElements(carousel) {
    return {
      prevButton: carousel.querySelector('[data-carousel-prev]'),
      nextButton: carousel.querySelector('[data-carousel-next]'),
      pagination: carousel.querySelector('.ps-carousel__pagination'),
      toolbarItems: Array.from(carousel.querySelectorAll('[data-toolbar-item]')),
    };
  }

  /**
   * Apply attribute-based overrides to swiper config
   * @param {Object} config - Swiper config
   * @param {Object} carouselConfig - Carousel config
   */
  function applyConfigOverrides(config, carouselConfig) {
    const { isThumbs, attrSlidesPerView, attrSpaceBetween } = carouselConfig;

    if (!isThumbs) {
      return;
    }

    if (attrSlidesPerView) {
      config.slidesPerView = attrSlidesPerView === 'auto' ? 'auto' : Number(attrSlidesPerView);
      if (config.breakpoints) {
        config.breakpoints = {};
      }
    }

    if (attrSpaceBetween) {
      const sb = Number(attrSpaceBetween);
      if (!Number.isNaN(sb)) {
        config.spaceBetween = sb;
      }
    }
  }

  /**
   * Initialize Swiper instance for a carousel element
   * @param {HTMLElement} carousel - Carousel container (.ps-carousel)
   * @returns {Swiper|null} Swiper instance or null if initialization fails
   */
  function initCarousel(carousel) {
    // Check if Swiper is available
    if (typeof Swiper === 'undefined') {
      console.error('Carousel: Swiper.js is not loaded. Install via npm: npm install swiper');
      return null;
    }

    // Get configuration from element
    const carouselConfig = getCarouselConfig(carousel);
    const elements = getCarouselElements(carousel);

    // Build Swiper configuration
    const config = buildSwiperConfig({
      hasLoop: carouselConfig.hasLoop,
      hasAutoHeight: carouselConfig.hasAutoHeight,
      isCards: carouselConfig.isCards,
      isThumbs: carouselConfig.isThumbs,
      prevButton: elements.prevButton,
      nextButton: elements.nextButton,
      pagination: elements.pagination,
    });

    // Apply attribute-based overrides
    applyConfigOverrides(config, carouselConfig);

    // Setup thumbs synchronization
    setupThumbsSync(carousel, config, carouselConfig.thumbsTargetId);

    // Initialize Swiper
    const swiper = new Swiper(carousel, config);

    // Bind pending main carousels if this is a thumbs carousel
    if (carouselConfig.isThumbs) {
      bindPendingMainCarousels(carousel, swiper);
    }

    // Setup toolbar navigation
    setupToolbarNavigation(swiper, elements.toolbarItems);

    // Setup ARIA current attribute updates
    setupA11yAriaCurrent(swiper);

    // If thumbs already exist, wire manual sync as a safety net
    if (carouselConfig.thumbsTargetId) {
      const thumbsElNow = document.getElementById(carouselConfig.thumbsTargetId);
      if (thumbsElNow?.swiperInstance) {
        wireManualSync(swiper, thumbsElNow.swiperInstance);
      }
    }

    return swiper;
  }
  /**
   * Drupal behavior for carousel initialization
   */
  Drupal.behaviors.psCarousel = {
    attach(context) {
      const carousels = once('ps-carousel', '[data-carousel]', context);

      carousels.forEach((carousel) => {
        const swiperInstance = initCarousel(carousel);

        // Store instance on element for external access
        if (swiperInstance) {
          carousel.swiperInstance = swiperInstance;
        }
      });
    },

    detach(context, _settings, trigger) {
      if (trigger === 'unload') {
        const carousels = context.querySelectorAll('[data-carousel]');

        carousels.forEach((carousel) => {
          if (carousel.swiperInstance) {
            carousel.swiperInstance.destroy(true, true);
            delete carousel.swiperInstance;
          }
        });
      }
    },
  };

  /**
   * Standalone initialization for non-Drupal contexts (Storybook)
   */
  if (typeof Drupal === 'undefined' || !Drupal.behaviors) {
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('[data-carousel]').forEach((carousel) => {
        if (!carousel.swiperInstance) {
          const swiperInstance = initCarousel(carousel);
          if (swiperInstance) {
            carousel.swiperInstance = swiperInstance;
          }
        }
      });
    });
  }
})(typeof Drupal !== 'undefined' ? Drupal : {}, typeof once !== 'undefined' ? once : () => []);
