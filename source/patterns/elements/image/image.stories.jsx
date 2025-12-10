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
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: var(--size-4);">
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
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: var(--size-4);">
        ${items.join('')}
      </div>
    `;
  },
};
