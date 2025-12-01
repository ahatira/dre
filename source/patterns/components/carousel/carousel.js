/**
 * Carousel Behavior (Swiper wrapper)
 * Manages carousel initialization and lifecycle using Swiper.js
 * @see https://swiperjs.com/
 */

import Swiper from 'swiper';
import { A11y, Keyboard, Navigation, Pagination } from 'swiper/modules';

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

    // Find navigation and pagination elements within this carousel
    const nextButton = this.element.querySelector('[data-carousel-next]');
    const prevButton = this.element.querySelector('[data-carousel-prev]');
    const pagination = this.element.querySelector('.ps-carousel__pagination');

    // Find toolbar items for media group navigation
    this.toolbarItems = this.element.querySelectorAll('[data-toolbar-item]');

    // Build configuration with DOM elements (not selectors)
    const config = {
      modules: [Navigation, Pagination, Keyboard, A11y],
      slidesPerView: 1,
      spaceBetween: 0,
      speed: 300,
      loop,
      autoHeight,

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

    // Initialize Swiper
    this.swiper = new Swiper(this.element, config);

    // Setup toolbar navigation
    this.setupToolbarNavigation();
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
        if (!isNaN(slideIndex) && this.swiper) {
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
      const nextSlideIndex = nextItem
        ? parseInt(nextItem.dataset.slideIndex, 10)
        : Infinity;

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

      carousels.forEach((element) => {
        // Use once() to prevent re-initialization
        if (typeof once !== 'undefined') {
          once('ps-carousel', element).forEach((el) => {
            const wrapper = new PsCarouselWrapper(el);
            wrapper.init();

            // Store reference for cleanup
            el.psCarouselWrapper = wrapper;
          });
        } else {
          // Fallback without once()
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
