import carouselTwig from './carousel.twig';
import carouselData from './carousel.yml';

// Helper to generate SVG data URIs (avoids external network requests)
const createPlaceholderSVG = (width, height, bgColor, text) => {
  const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${width}" height="${height}"><rect fill="${bgColor}" width="${width}" height="${height}"/><text fill="#FFF" font-family="sans-serif" font-size="32" x="50%" y="50%" text-anchor="middle" dominant-baseline="middle">${text}</text></svg>`;
  return `data:image/svg+xml;base64,${btoa(svg)}`;
};

const settings = {
  title: 'Components/Carousel',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Responsive carousel component built with Swiper.js (v12). Supports multiple variants (images, cards), navigation controls, pagination, keyboard navigation, touch/swipe gestures, and optional toolbar for multi-media galleries.',
      },
    },
  },
  argTypes: {
    // Content
    slides: {
      description:
        'Array of slide objects. Each slide must have an `id` and content (image or card HTML).',
      control: { type: 'object' },
      table: {
        category: 'Content',
        type: { summary: 'array' },
      },
    },
    toolbar: {
      description:
        'Toolbar configuration for multi-media navigation (e.g., photos, 3D visits, plans). Auto-displayed if items array is provided.',
      control: { type: 'object' },
      table: {
        category: 'Content',
        type: { summary: 'object' },
        defaultValue: { summary: '{}' },
      },
    },

    // Appearance
    variant: {
      description:
        'Display variant: `images` (default, single slide) or `cards` (multiple cards with external buttons and gradients)',
      control: { type: 'select' },
      options: ['images', 'cards'],
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'images' },
      },
    },
    fit: {
      description:
        'Image object-fit mode: `cover` (default, fills area) or `contain` (fits within area with letterboxing). Only applies to images variant.',
      control: { type: 'select' },
      options: ['cover', 'contain'],
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'cover' },
      },
    },

    // Behavior
    loop: {
      description:
        'Enable infinite loop navigation (requires at least 2× slidesPerView slides for cards variant)',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    autoHeight: {
      description: 'Automatically adjust carousel height based on active slide content',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    withPagination: {
      description: 'Show pagination bullets below carousel',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: true },
      },
    },

    // Accessibility
    ariaLabel: {
      description: 'Accessible label for the carousel region',
      control: { type: 'text' },
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: 'Carousel' },
      },
    },
  },
};

// Default story with interactive controls
export const Default = {
  render: (args) => carouselTwig(args),
  args: { ...carouselData },
};

// Offer Carousel - Property detail page with toolbar (multi-media navigation)
export const OfferCarousel = {
  render: () =>
    carouselTwig({
      slides: [
        // Photos (index 0-12)
        {
          id: 'photo-1',
          type: 'image',
          image: {
            src: 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1200&h=800&fit=crop',
            alt: 'Exterior view of classic Parisian building',
          },
        },
        {
          id: 'photo-2',
          type: 'image',
          image: {
            src: 'https://images.unsplash.com/photo-1502672260066-6bc2614179d7?w=1200&h=800&fit=crop',
            alt: 'Living room interior',
          },
        },
        {
          id: 'photo-3',
          type: 'image',
          image: {
            src: 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=1200&h=800&fit=crop',
            alt: 'Kitchen view',
          },
        },
        {
          id: 'photo-4',
          type: 'image',
          image: {
            src: 'https://images.unsplash.com/photo-1540518614846-7eded433c457?w=1200&h=800&fit=crop',
            alt: 'Bedroom view',
          },
        },
        {
          id: 'photo-5',
          type: 'image',
          image: {
            src: 'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?w=1200&h=800&fit=crop',
            alt: 'Bathroom view',
          },
        },
        {
          id: 'photo-6',
          type: 'image',
          image: {
            src: 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1200&h=800&fit=crop',
            alt: 'Balcony view',
          },
        },
        {
          id: 'photo-7',
          type: 'image',
          image: {
            src: 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?w=1200&h=800&fit=crop',
            alt: 'Dining area',
          },
        },
        {
          id: 'photo-8',
          type: 'image',
          image: {
            src: 'https://images.unsplash.com/photo-1600566752355-35792bedcfea?w=1200&h=800&fit=crop',
            alt: 'Home office',
          },
        },
        {
          id: 'photo-9',
          type: 'image',
          image: {
            src: 'https://images.unsplash.com/photo-1600573472591-ee6b68d14c68?w=1200&h=800&fit=crop',
            alt: 'Second bedroom',
          },
        },
        {
          id: 'photo-10',
          type: 'image',
          image: {
            src: 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?w=1200&h=800&fit=crop',
            alt: 'Guest bathroom',
          },
        },
        {
          id: 'photo-11',
          type: 'image',
          image: {
            src: 'https://images.unsplash.com/photo-1600607687644-c7171b42498f?w=1200&h=800&fit=crop',
            alt: 'Storage space',
          },
        },
        {
          id: 'photo-12',
          type: 'image',
          image: {
            src: 'https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?w=1200&h=800&fit=crop',
            alt: 'Building entrance',
          },
        },
        {
          id: 'photo-13',
          type: 'image',
          image: {
            src: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1200&h=800&fit=crop',
            alt: 'Parking area',
          },
        },
        // 3D Visits (index 13-15)
        {
          id: '3d-1',
          type: '3d-visit',
          image: {
            src: createPlaceholderSVG(1200, 800, '#0B5FFF', '3D Visit 1 - Click to explore'),
            alt: '3D virtual tour - Main floor',
          },
        },
        {
          id: '3d-2',
          type: '3d-visit',
          image: {
            src: createPlaceholderSVG(1200, 800, '#0B5FFF', '3D Visit 2 - Click to explore'),
            alt: '3D virtual tour - Upper floor',
          },
        },
        {
          id: '3d-3',
          type: '3d-visit',
          image: {
            src: createPlaceholderSVG(1200, 800, '#0B5FFF', '3D Visit 3 - Click to explore'),
            alt: '3D virtual tour - Basement',
          },
        },
        // Plans (index 16-21)
        {
          id: 'plan-1',
          type: 'plan',
          image: {
            src: createPlaceholderSVG(1200, 800, '#E0388C', 'Floor Plan - Ground Floor'),
            alt: 'Architectural plan - Ground floor',
          },
        },
        {
          id: 'plan-2',
          type: 'plan',
          image: {
            src: createPlaceholderSVG(1200, 800, '#E0388C', 'Floor Plan - First Floor'),
            alt: 'Architectural plan - First floor',
          },
        },
        {
          id: 'plan-3',
          type: 'plan',
          image: {
            src: createPlaceholderSVG(1200, 800, '#E0388C', 'Floor Plan - Second Floor'),
            alt: 'Architectural plan - Second floor',
          },
        },
        {
          id: 'plan-4',
          type: 'plan',
          image: {
            src: createPlaceholderSVG(1200, 800, '#E0388C', 'Floor Plan - Attic'),
            alt: 'Architectural plan - Attic',
          },
        },
        {
          id: 'plan-5',
          type: 'plan',
          image: {
            src: createPlaceholderSVG(1200, 800, '#E0388C', 'Floor Plan - Basement'),
            alt: 'Architectural plan - Basement',
          },
        },
        {
          id: 'plan-6',
          type: 'plan',
          image: {
            src: createPlaceholderSVG(1200, 800, '#E0388C', 'Site Plan'),
            alt: 'Site plan with parking',
          },
        },
        // Brochure (index 22)
        {
          id: 'brochure-1',
          type: 'brochure',
          image: {
            src: createPlaceholderSVG(
              1200,
              800,
              '#00915A',
              'Property Brochure - Click to download'
            ),
            alt: 'Downloadable property brochure',
          },
        },
      ],
      withPagination: false,
      toolbar: {
        items: [
          { type: 'photos', label: '13 photos', icon: 'picture', slideIndex: 0 },
          { type: '3d-visit', label: '3 visites 3D', icon: 'select-area-map', slideIndex: 13 },
          { type: 'plan', label: '6 plans', icon: 'last-articles', slideIndex: 16 },
          { type: 'brochure', label: '1 brochure', icon: 'map', slideIndex: 22 },
        ],
      },
      ariaLabel: 'Property media gallery with photos, 3D visits, plans and brochure',
    }),
};

// Modal Carousel - Lightbox/fullscreen mode with main carousel + thumbs (from screenshot)
export const ModalCarousel = {
  render: () => {
    const slides = [
      {
        id: 'modal1',
        image: {
          src: 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1600&h=1200&fit=crop',
          alt: 'Building exterior - large view',
        },
      },
      {
        id: 'modal2',
        image: {
          src: 'https://images.unsplash.com/photo-1502672260066-6bc2614179d7?w=1600&h=1200&fit=crop',
          alt: 'Living room - large view',
        },
      },
      {
        id: 'modal3',
        image: {
          src: 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=1600&h=1200&fit=crop',
          alt: 'Kitchen - large view',
        },
      },
      {
        id: 'modal4',
        image: {
          src: 'https://images.unsplash.com/photo-1540518614846-7eded433c457?w=1600&h=1200&fit=crop',
          alt: 'Bedroom - large view',
        },
      },
      {
        id: 'modal5',
        image: {
          src: 'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?w=1600&h=1200&fit=crop',
          alt: 'Bathroom - large view',
        },
      },
    ];

    // Main carousel (large images)
    const mainCarousel = carouselTwig({
      slides,
      ariaLabel: 'Property photos - fullscreen gallery',
      withPagination: false,
      attributes: 'data-carousel-thumbs="carousel-thumbs-modal"',
    });

    // Thumbs carousel (miniatures)
    const thumbsCarousel = carouselTwig({
      slides,
      ariaLabel: 'Photo thumbnails',
      withPagination: false,
      attributes: 'data-carousel-role="thumbs" id="carousel-thumbs-modal"',
    });

    return `
      <div style="background: #fff; display: flex; flex-direction: column; gap: 16px; padding: 16px; height: 100vh; box-sizing: border-box;">
        <!-- Main carousel (auto-fit within viewport) -->
        <div style="width: 100%; flex: 1 1 auto; min-height: 0; display: flex; align-items: center; justify-content: center; background: #fff;">
          <style>
            /* Ensure swiper takes available height */
            [data-carousel-thumbs].swiper,
            [data-carousel-thumbs] .ps-carousel__track,
            [data-carousel-thumbs] .ps-carousel__slide {
              height: 100% !important;
            }
            [data-carousel-thumbs] .ps-carousel__image {
              max-width: 100%;
              max-height: 100%;
              object-fit: contain;
            }
          </style>
          ${mainCarousel}
        </div>
        
        <!-- Thumbs carousel -->
        <div style="height: 120px; width: 100%; flex: 0 0 120px;">
          <style>
            /* Thumbs carousel specific styles */
            [data-carousel-role="thumbs"] {
              height: 120px !important;
            }
            [data-carousel-role="thumbs"] .swiper-slide {
              opacity: 0.4;
              cursor: pointer;
              transition: opacity 150ms ease;
              height: 120px;
            }
            [data-carousel-role="thumbs"] .swiper-slide:hover {
              opacity: 0.7;
            }
            [data-carousel-role="thumbs"] .swiper-slide-thumb-active {
              opacity: 1 !important;
            }
            [data-carousel-role="thumbs"] .ps-carousel__image {
              height: 120px;
              width: 100%;
              object-fit: cover;
            }
            [data-carousel-role="thumbs"].swiper {
              --swiper-navigation-size: 24px;
            }
            [data-carousel-role="thumbs"] .ps-carousel__button {
              width: 32px;
              height: 32px;
            }
            
            /* Modal carousel full height */
            [data-carousel-thumbs] .ps-carousel__slide {
              display: flex;
              align-items: center;
              justify-content: center;
            }
            [data-carousel-thumbs] .ps-carousel__image {
              max-width: 100%;
              max-height: 100%;
              object-fit: contain;
            }
          </style>
          ${thumbsCarousel}
        </div>
      </div>
    `;
  },
};

// Teaser Carousel - Property listing preview (from screenshot 3 - single property)
export const TeaserCarousel = {
  render: () => `
    <div style="max-width: 400px;">
      ${carouselTwig({
        slides: [
          {
            id: 'teaser1',
            image: {
              src: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&h=600&fit=crop',
              alt: 'Modern building exterior',
            },
          },
          {
            id: 'teaser2',
            image: {
              src: 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800&h=600&fit=crop',
              alt: 'Building facade',
            },
          },
          {
            id: 'teaser3',
            image: {
              src: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&h=600&fit=crop',
              alt: 'Property view',
            },
          },
        ],
        ariaLabel: 'Property preview images',
      })}
    </div>
  `,
};

// Cards Carousel - Property listing cards (from screenshot 4 - multiple properties)
export const CardsCarousel = {
  render: () => {
    const carouselHtml = carouselTwig({
      slides: [
        {
          id: 'card1',
          card: `
            <div style="position: relative;">
              <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=400&h=300&fit=crop" alt="Property 1" style="width: 100%; height: 200px; object-fit: cover;" />
              <button style="position: absolute; top: 8px; right: 8px; width: 48px; height: 48px; background: white; border: none; border-radius: 0; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 24px; color: #A22B66;">♥</span>
              </button>
              <div style="padding: 16px;">
                <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #6A7078;">Price • m²</div>
                <h4 style="margin: 0 0 8px; font-size: 16px; font-weight: 600;">Product title</h4>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; color: #6A7078;">
                  <span style="font-size: 20px;">📍</span>
                  <span>Location</span>
                </div>
                <a href="#" style="color: #00915A; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 500;">
                  View the property →
                </a>
              </div>
            </div>
          `,
        },
        {
          id: 'card2',
          card: `
            <div style="position: relative;">
              <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=400&h=300&fit=crop" alt="Property 2" style="width: 100%; height: 200px; object-fit: cover;" />
              <button style="position: absolute; top: 8px; right: 8px; width: 48px; height: 48px; background: white; border: none; border-radius: 0; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 24px; color: #A22B66;">♥</span>
              </button>
              <div style="padding: 16px;">
                <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #6A7078;">Price • m²</div>
                <h4 style="margin: 0 0 8px; font-size: 16px; font-weight: 600;">Product title</h4>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; color: #6A7078;">
                  <span style="font-size: 20px;">📍</span>
                  <span>Location</span>
                </div>
                <a href="#" style="color: #00915A; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 500;">
                  View the property →
                </a>
              </div>
            </div>
          `,
        },
        {
          id: 'card3',
          card: `
            <div style="position: relative;">
              <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&h=300&fit=crop" alt="Property 3" style="width: 100%; height: 200px; object-fit: cover;" />
              <button style="position: absolute; top: 8px; right: 8px; width: 48px; height: 48px; background: white; border: none; border-radius: 0; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 24px; color: #A22B66;">♥</span>
              </button>
              <div style="padding: 16px;">
                <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #6A7078;">Price • m²</div>
                <h4 style="margin: 0 0 8px; font-size: 16px; font-weight: 600;">Product title</h4>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; color: #6A7078;">
                  <span style="font-size: 20px;">📍</span>
                  <span>Location</span>
                </div>
                <a href="#" style="color: #00915A; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 500;">
                  View the property →
                </a>
              </div>
            </div>
          `,
        },
        {
          id: 'card4',
          card: `
            <div style="position: relative;">
              <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=400&h=300&fit=crop" alt="Property 4" style="width: 100%; height: 200px; object-fit: cover;" />
              <button style="position: absolute; top: 8px; right: 8px; width: 48px; height: 48px; background: white; border: none; border-radius: 0; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 24px; color: #A22B66;">♥</span>
              </button>
              <div style="padding: 16px;">
                <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #6A7078;">Price • m²</div>
                <h4 style="margin: 0 0 8px; font-size: 16px; font-weight: 600;">Product title</h4>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; color: #6A7078;">
                  <span style="font-size: 20px;">📍</span>
                  <span>Location</span>
                </div>
                <a href="#" style="color: #00915A; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 500;">
                  View the property →
                </a>
              </div>
            </div>
          `,
        },
        {
          id: 'card5',
          card: `
            <div style="position: relative;">
              <img src="https://images.unsplash.com/photo-1502672260066-6bc2614179d7?w=400&h=300&fit=crop" alt="Property 5" style="width: 100%; height: 200px; object-fit: cover;" />
              <button style="position: absolute; top: 8px; right: 8px; width: 48px; height: 48px; background: white; border: none; border-radius: 0; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 24px; color: #A22B66;">♥</span>
              </button>
              <div style="padding: 16px;">
                <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #6A7078;">Price • m²</div>
                <h4 style="margin: 0 0 8px; font-size: 16px; font-weight: 600;">Product title</h4>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; color: #6A7078;">
                  <span style="font-size: 20px;">📍</span>
                  <span>Location</span>
                </div>
                <a href="#" style="color: #00915A; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 500;">
                  View the property →
                </a>
              </div>
            </div>
          `,
        },
        {
          id: 'card6',
          card: `
            <div style="position: relative;">
              <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400&h=300&fit=crop" alt="Property 6" style="width: 100%; height: 200px; object-fit: cover;" />
              <button style="position: absolute; top: 8px; right: 8px; width: 48px; height: 48px; background: white; border: none; border-radius: 0; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 24px; color: #A22B66;">♥</span>
              </button>
              <div style="padding: 16px;">
                <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #6A7078;">Price • m²</div>
                <h4 style="margin: 0 0 8px; font-size: 16px; font-weight: 600;">Product title</h4>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; color: #6A7078;">
                  <span style="font-size: 20px;">📍</span>
                  <span>Location</span>
                </div>
                <a href="#" style="color: #00915A; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 500;">
                  View the property →
                </a>
              </div>
            </div>
          `,
        },
        {
          id: 'card7',
          card: `
            <div style="position: relative;">
              <img src="https://images.unsplash.com/photo-1540518614846-7eded433c457?w=400&h=300&fit=crop" alt="Property 7" style="width: 100%; height: 200px; object-fit: cover;" />
              <button style="position: absolute; top: 8px; right: 8px; width: 48px; height: 48px; background: white; border: none; border-radius: 0; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 24px; color: #A22B66;">♥</span>
              </button>
              <div style="padding: 16px;">
                <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #6A7078;">Price • m²</div>
                <h4 style="margin: 0 0 8px; font-size: 16px; font-weight: 600;">Product title</h4>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; color: #6A7078;">
                  <span style="font-size: 20px;">📍</span>
                  <span>Location</span>
                </div>
                <a href="#" style="color: #00915A; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 500;">
                  View the property →
                </a>
              </div>
            </div>
          `,
        },
        {
          id: 'card8',
          card: `
            <div style="position: relative;">
              <img src="https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?w=400&h=300&fit=crop" alt="Property 8" style="width: 100%; height: 200px; object-fit: cover;" />
              <button style="position: absolute; top: 8px; right: 8px; width: 48px; height: 48px; background: white; border: none; border-radius: 0; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 24px; color: #A22B66;">♥</span>
              </button>
              <div style="padding: 16px;">
                <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #6A7078;">Price • m²</div>
                <h4 style="margin: 0 0 8px; font-size: 16px; font-weight: 600;">Product title</h4>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; color: #6A7078;">
                  <span style="font-size: 20px;">📍</span>
                  <span>Location</span>
                </div>
                <a href="#" style="color: #00915A; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 500;">
                  View the property →
                </a>
              </div>
            </div>
          `,
        },
      ],
      variant: 'cards',
      loop: true,
      withPagination: false,
      ariaLabel: 'Property listings carousel',
    });

    return carouselHtml;
  },
};

export default settings;
