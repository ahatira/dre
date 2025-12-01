/**
 * Carousel Behavior (Swiper wrapper)
 * Manages carousel initialization and lifecycle using Swiper.js
 * @see https://swiperjs.com/
 */

import Swiper from 'swiper';
import { A11y, Keyboard, Navigation, Pagination, Thumbs } from 'swiper/modules';

/**
 * Wrapper class for Swiper carousel
 */
class PsCarouselWrapper {
  constructor(element) {
    this.element = element;
    this.swiper = null;
  }

  /**
   * Initialize Swiper instance
   */
  init() {
    if (this.swiper) {
      return;
    }

    // Read configuration from data attributes
    const loop = this.element.classList.contains('ps-carousel--loop');
    const autoHeight = this.element.classList.contains('ps-carousel--auto-height');
    const isCards = this.element.classList.contains('ps-carousel--cards');
    const thumbsId = this.element.dataset.carouselThumbs; // ID of thumbs carousel
    const isThumbsCarousel = this.element.dataset.carouselRole === 'thumbs';

    // Find navigation and pagination elements within this carousel
    const nextButton = this.element.querySelector('[data-carousel-next]');
    const prevButton = this.element.querySelector('[data-carousel-prev]');
    const pagination = this.element.querySelector('.ps-carousel__pagination');

    // Find toolbar items for media group navigation
    this.toolbarItems = this.element.querySelectorAll('[data-toolbar-item]');

    // Build configuration with DOM elements (not selectors)
    const config = {
      modules: [Navigation, Pagination, Keyboard, A11y, Thumbs],
      slidesPerView: isCards ? 4 : isThumbsCarousel ? 5 : 1,
      spaceBetween: isCards ? 16 : isThumbsCarousel ? 8 : 0,
      speed: 300,
      loop: isThumbsCarousel ? false : loop, // Disable loop for thumbs carousel
      autoHeight,
      watchSlidesProgress: isThumbsCarousel, // Required for thumbs

      // Navigation - Must use DOM elements (not selectors)
      navigation: {
        nextEl: nextButton,
        prevEl: prevButton,
        disabledClass: 'is-disabled',
      },

      // Keyboard control
      keyboard: {
        enabled: true,
        onlyInViewport: true,
      },

      // Accessibility
      a11y: {
        enabled: true,
        prevSlideMessage: 'Previous slide',
        nextSlideMessage: 'Next slide',
        firstSlideMessage: 'This is the first slide',
        lastSlideMessage: 'This is the last slide',
        paginationBulletMessage: 'Go to slide {{index}}',
      },

      // Callbacks
      on: {
        init: this.onInit.bind(this),
        slideChange: this.onSlideChange.bind(this),
      },
    };

    // Add pagination only if element exists
    if (pagination) {
      config.pagination = {
        el: pagination,
        clickable: true,
        bulletClass: 'swiper-pagination-bullet',
        bulletActiveClass: 'swiper-pagination-bullet-active',
      };
    }

    // Add thumbs synchronization if thumbsId is specified
    if (thumbsId) {
      const thumbsElement = document.getElementById(thumbsId);
      // Always configure thumbs module, even if thumbs element doesn't exist yet
      config.thumbs = {
        swiper: thumbsElement?.swiper || null,
      };

      if (!thumbsElement?.swiper) {
        // Store thumbsId for later binding (thumbs carousel might not be initialized yet)
        this.pendingThumbsId = thumbsId;
      }
    }

    // Initialize Swiper
    this.swiper = new Swiper(this.element, config);

    // If this is a thumbs carousel, try to bind any pending main carousels
    if (this.element.dataset.carouselRole === 'thumbs') {
      this.bindPendingMainCarousels();
    }

    // Setup toolbar navigation
    this.setupToolbarNavigation();
  }

  /**
   * Bind pending main carousels that are waiting for this thumbs carousel
   */
  bindPendingMainCarousels() {
    const thumbsId = this.element.id;
    document.querySelectorAll(`[data-carousel-thumbs="${thumbsId}"]`).forEach((mainEl) => {
      if (mainEl.psCarouselWrapper?.pendingThumbsId) {
        // Update thumbs swiper reference and reinitialize
        const mainSwiper = mainEl.psCarouselWrapper.swiper;
        if (mainSwiper.params.thumbs) {
          mainSwiper.params.thumbs.swiper = this.swiper;
          mainSwiper.thumbs.init();
          mainSwiper.thumbs.update();
          delete mainEl.psCarouselWrapper.pendingThumbsId;
        }
      }
    });
  }

  /**
   * Setup toolbar item click handlers for media group navigation
   */
  setupToolbarNavigation() {
    if (!this.toolbarItems || this.toolbarItems.length === 0) {
      return;
    }

    this.toolbarItems.forEach((item) => {
      item.addEventListener('click', () => {
        const slideIndex = parseInt(item.dataset.slideIndex, 10);
        if (!Number.isNaN(slideIndex) && this.swiper) {
          this.swiper.slideTo(slideIndex);
        }
      });
    });

    // Update active state on init
    this.updateToolbarActiveState();
  }

  /**
   * Update toolbar active state based on current slide
   */
  updateToolbarActiveState() {
    if (!this.toolbarItems || this.toolbarItems.length === 0 || !this.swiper) {
      return;
    }

    const currentIndex = this.swiper.realIndex;

    // Find which toolbar item corresponds to current slide
    let activeItem = null;
    this.toolbarItems.forEach((item) => {
      const slideIndex = parseInt(item.dataset.slideIndex, 10);
      const nextItem = item.nextElementSibling?.nextElementSibling; // Skip divider
      const nextSlideIndex = nextItem ? parseInt(nextItem.dataset.slideIndex, 10) : Infinity;

      // Check if current slide is in this item's range
      if (currentIndex >= slideIndex && currentIndex < nextSlideIndex) {
        activeItem = item;
      }
    });

    // Update active states
    this.toolbarItems.forEach((item) => {
      if (item === activeItem) {
        item.setAttribute('data-active', 'true');
      } else {
        item.removeAttribute('data-active');
      }
    });
  }

  /**
   * Swiper init callback
   */
  onInit() {
    // Custom logic after Swiper initialization
    console.log('[Carousel] Initialized');
  }

  /**
   * Slide change callback
   */
  onSlideChange() {
    if (!this.swiper) {
      return;
    }

    const { activeIndex, slides } = this.swiper;
    const currentSlide = slides[activeIndex];

    if (currentSlide) {
      // Update ARIA attributes
      currentSlide.setAttribute('aria-current', 'true');

      // Remove from other slides
      slides.forEach((slide, index) => {
        if (index !== activeIndex) {
          slide.removeAttribute('aria-current');
        }
      });
    }

    // Update toolbar active state
    this.updateToolbarActiveState();
  }

  /**
   * Destroy Swiper instance
   */
  destroy() {
    if (this.swiper) {
      this.swiper.destroy(true, true);
      this.swiper = null;
    }
  }
}

/**
 * Drupal behavior for carousel
 */
if (typeof Drupal !== 'undefined') {
  Drupal.behaviors.psCarousel = {
    attach(context) {
      const carousels = context.querySelectorAll('[data-carousel]');

      // Initialize thumbs carousels first
      const thumbsCarousels = [];
      const mainCarousels = [];

      carousels.forEach((element) => {
        if (element.dataset.carouselRole === 'thumbs') {
          thumbsCarousels.push(element);
        } else {
          mainCarousels.push(element);
        }
      });

      // Init thumbs first
      thumbsCarousels.forEach((element) => {
        if (typeof once !== 'undefined') {
          once('ps-carousel', element).forEach((el) => {
            const wrapper = new PsCarouselWrapper(el);
            wrapper.init();
            el.psCarouselWrapper = wrapper;
          });
        } else {
          if (!element.dataset.carouselInitialized) {
            const wrapper = new PsCarouselWrapper(element);
            wrapper.init();
            element.dataset.carouselInitialized = 'true';
            element.psCarouselWrapper = wrapper;
          }
        }
      });

      // Then init main carousels
      mainCarousels.forEach((element) => {
        if (typeof once !== 'undefined') {
          once('ps-carousel', element).forEach((el) => {
            const wrapper = new PsCarouselWrapper(el);
            wrapper.init();
            el.psCarouselWrapper = wrapper;
          });
        } else {
          if (!element.dataset.carouselInitialized) {
            const wrapper = new PsCarouselWrapper(element);
            wrapper.init();
            element.dataset.carouselInitialized = 'true';
            element.psCarouselWrapper = wrapper;
          }
        }
      });
    },

    detach(context, _settings, trigger) {
      if (trigger === 'unload') {
        const carousels = context.querySelectorAll('[data-carousel]');
        carousels.forEach((element) => {
          if (element.psCarouselWrapper) {
            element.psCarouselWrapper.destroy();
            delete element.psCarouselWrapper;
          }
        });
      }
    },
  };
}

// Standalone initialization for non-Drupal contexts (Storybook)
if (typeof Drupal === 'undefined') {
  document.addEventListener('DOMContentLoaded', () => {
    const carousels = document.querySelectorAll('[data-carousel]');
    carousels.forEach((element) => {
      const wrapper = new PsCarouselWrapper(element);
      wrapper.init();
    });
  });
}
