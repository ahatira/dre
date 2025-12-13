import imageTwig from './image.twig';
import imageData from './image.yml';

export default {
  title: 'Elements/Image',
  tags: ['autodocs'],
  render: (args) => imageTwig(args),
  args: imageData,
  parameters: {
    docs: {
      description: {
        component:
          'Responsive image with lazy loading, srcset/sizes, object-fit and border-radius. For figures with captions, see the Figure component.',
      },
    },
  },
  argTypes: {
    src: {
      control: 'text',
      description: 'Image URL',
      table: { category: 'Content', type: { summary: 'string' } },
    },
    alt: {
      control: 'text',
      description: 'Alternative text for accessibility (required)',
      table: { category: 'Content', type: { summary: 'string' } },
    },
    width: {
      control: 'number',
      description: 'Intrinsic width in pixels (prevents CLS)',
      table: { category: 'Content', type: { summary: 'integer' } },
    },
    height: {
      control: 'number',
      description: 'Intrinsic height in pixels (prevents CLS)',
      table: { category: 'Content', type: { summary: 'integer' } },
    },
    srcset: {
      control: 'object',
      description: 'Array of responsive sources (e.g., ["/img-400.jpg 400w", "/img-800.jpg 800w"])',
      table: { category: 'Content', type: { summary: 'array<string>' } },
    },
    sizes: {
      control: 'text',
      description: 'Sizes attribute for responsive images (e.g., "(min-width: 768px) 50vw, 100vw")',
      table: { category: 'Content', type: { summary: 'string' } },
    },
    fit: {
      control: 'select',
      options: ['none', 'cover', 'contain', 'fill', 'scale-down'],
      description: 'object-fit behavior',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'none' },
      },
    },
    rounded: {
      control: 'select',
      options: ['none', 'sm', 'md', 'lg', 'full'],
      description: 'border-radius style',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'none' },
      },
    },
    loading: {
      control: 'select',
      options: ['lazy', 'eager'],
      description: 'Loading strategy (lazy = on scroll, eager = immediate)',
      table: {
        category: 'Behavior',
        type: { summary: 'string' },
        defaultValue: { summary: 'lazy' },
      },
    },
    decoding: {
      control: 'select',
      options: ['auto', 'async', 'sync'],
      description: 'Decoding hint for the browser',
      table: {
        category: 'Behavior',
        type: { summary: 'string' },
        defaultValue: { summary: 'auto' },
      },
    },
  },
};

/**
 * Default responsive image with lazy loading
 */
export const Default = {
  args: {
    ...imageData,
    src: '/source/assets/images/16-9.jpg',
    alt: 'Modern commercial building with glass facade',
  },
};

/**
 * All object-fit modes
 * Comparison of behaviors to adapt image to container
 */
export const ObjectFits = {
  render: () => {
    const fits = ['none', 'cover', 'contain', 'fill', 'scale-down'];
    const items = fits.map((fit) => {
      return `
        <div style="text-align: center;">
          <div style="width: 150px; height: 150px; border: 1px solid var(--gray-300); margin-bottom: var(--size-2); overflow: hidden;">
            <img 
              class="ps-image${fit !== 'none' ? ` ps-image--fit-${fit}` : ''}" 
              src="/source/assets/images/16-9.jpg"
              alt="Image with object-fit: ${fit}"
            />
          </div>
          <code style="font-size: var(--font-size-1); color: var(--gray-600);">${fit}</code>
        </div>
      `;
    });

    return `
      <div style="display: flex; flex-wrap: wrap; gap: var(--size-4); max-width: 800px;">
        ${items.join('')}
      </div>
    `;
  },
};

/**
 * All border-radius variants
 * Comparison of available rounding styles
 */
export const BorderRadius = {
  render: () => {
    const rounded = ['none', 'sm', 'md', 'lg', 'full'];
    const items = rounded.map((radius) => {
      const classes = radius !== 'none' ? `ps-image ps-image--rounded-${radius}` : 'ps-image';
      return `
        <div style="text-align: center;">
          <div style="width: 150px; height: 150px; margin-bottom: var(--size-2);">
            <img 
              class="${classes}" 
              src="/source/assets/images/1-1.jpg"
              alt="Image with border-radius: ${radius}"
            />
          </div>
          <code style="font-size: var(--font-size-1); color: var(--gray-600);">${radius}</code>
        </div>
      `;
    });

    return `
      <div style="display: flex; flex-wrap: wrap; gap: var(--size-4); max-width: 800px;">
        ${items.join('')}
      </div>
    `;
  },
};

/**
 * Real Estate context examples
 */
export const RealEstateContext = {
  render: () => {
    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-8);">
        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Property Card Image (16:9, cover, rounded-md)</h4>
          <div style="width: 350px; height: 200px; overflow: hidden; border-radius: var(--radius-4);">
            <img 
              class="ps-image ps-image--fit-cover ps-image--rounded-md" 
              src="/source/assets/images/16-9.jpg"
              alt="Modern office building exterior"
              width="350"
              height="200"
            />
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Hero Banner (21:9, cover, no radius)</h4>
          <div style="width: 100%; max-width: 800px; height: 300px; overflow: hidden;">
            <img 
              class="ps-image ps-image--fit-cover" 
              src="/source/assets/images/16-9.jpg"
              alt="Luxury residential complex panoramic view"
              width="800"
              height="300"
            />
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Agent Avatar (1:1, cover, rounded-full)</h4>
          <div style="display: flex; gap: var(--size-4);">
            <div style="width: 80px; height: 80px; overflow: hidden; border-radius: var(--radius-round);">
              <img 
                class="ps-image ps-image--fit-cover ps-image--rounded-full" 
                src="/source/assets/images/1-1.jpg"
                alt="Real estate agent profile photo"
                width="80"
                height="80"
              />
            </div>
            <div style="width: 120px; height: 120px; overflow: hidden; border-radius: var(--radius-round);">
              <img 
                class="ps-image ps-image--fit-cover ps-image--rounded-full" 
                src="/source/assets/images/1-1.jpg"
                alt="Real estate consultant profile photo"
                width="120"
                height="120"
              />
            </div>
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Gallery Thumbnails (4:3, cover, rounded-sm)</h4>
          <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
            ${[1, 2, 3, 4]
              .map(
                () => `
              <div style="width: 120px; height: 90px; overflow: hidden; border-radius: var(--radius-2);">
                <img 
                  class="ps-image ps-image--fit-cover ps-image--rounded-sm" 
                  src="/source/assets/images/16-9.jpg"
                  alt="Property gallery thumbnail"
                  width="120"
                  height="90"
                />
              </div>
            `
              )
              .join('')}
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Logo / Partner (contain, rounded-sm)</h4>
          <div style="width: 180px; height: 80px; background: var(--gray-50); border: 1px solid var(--border-light); border-radius: var(--radius-2); overflow: hidden; display: flex; align-items: center; justify-content: center; padding: var(--size-3);">
            <img 
              class="ps-image ps-image--fit-contain ps-image--rounded-sm" 
              src="/source/assets/images/1-1.jpg"
              alt="Partner company logo"
              width="180"
              height="80"
            />
          </div>
        </div>
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Common image usage patterns in real estate context: property cards (16:9 cover), hero banners (21:9 cover), agent avatars (1:1 rounded-full), gallery thumbnails (4:3), and logos (contain).',
      },
    },
  },
};
