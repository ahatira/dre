/**
 * Figure (Molecule)
 *
 * Semantic figure element with optional caption for responsive, accessible images.
 * Composes Image atom for enhanced real estate property visualization.
 */

import figureTemplate from './figure.twig';
import data from './figure.yml';
import './figure.css';

export default {
  title: 'Components/Figure',
  tags: ['autodocs'],
  parameters: {
    layout: 'padded',
    docs: {
      description: {
        component:
          'Semantic HTML figure element for displaying images with optional captions. Composes the Image atom for responsive, lazy-loaded images with accessibility. Ideal for real estate property showcases, before/after images, and contextual imagery.',
      },
    },
  },
  render: (args) => figureTemplate(args),
  argTypes: {
    // Image properties
    src: {
      control: 'text',
      description: 'Image source URL. Passed directly to Image atom.',
      table: {
        category: 'Image',
        type: { summary: 'string' },
      },
    },
    alt: {
      control: 'text',
      description: 'Accessible alternative text for the image (required). Passed to Image atom.',
      table: {
        category: 'Image',
        type: { summary: 'string' },
      },
    },
    width: {
      control: 'number',
      description: 'Intrinsic image width in pixels. Helps prevent layout shift.',
      table: {
        category: 'Image',
        type: { summary: 'int' },
      },
    },
    height: {
      control: 'number',
      description: 'Intrinsic image height in pixels. Helps prevent layout shift.',
      table: {
        category: 'Image',
        type: { summary: 'int' },
      },
    },
    srcset: {
      control: 'object',
      description:
        'Array of responsive image sources (e.g., ["/img-400.jpg 400w", "/img-800.jpg 800w"]). Passed to Image atom.',
      table: {
        category: 'Image',
        type: { summary: 'array<string>' },
      },
    },
    sizes: {
      control: 'text',
      description:
        'Sizes attribute for responsive images (e.g., "(min-width: 768px) 50vw, 100vw"). Passed to Image atom.',
      table: {
        category: 'Image',
        type: { summary: 'string' },
      },
    },
    loading: {
      control: { type: 'select' },
      options: ['lazy', 'eager'],
      description:
        'Image loading strategy: lazy (deferred) or eager (immediate). Passed to Image atom.',
      table: {
        category: 'Image',
        type: { summary: 'string' },
        defaultValue: { summary: 'lazy' },
      },
    },
    decoding: {
      control: { type: 'select' },
      options: ['auto', 'async', 'sync'],
      description: 'Image decoding hint for browser optimization. Passed to Image atom.',
      table: {
        category: 'Image',
        type: { summary: 'string' },
        defaultValue: { summary: 'auto' },
      },
    },
    fit: {
      control: { type: 'select' },
      options: ['none', 'cover', 'contain', 'fill', 'scale-down'],
      description: 'Object-fit behavior for image scaling. Passed to Image atom.',
      table: {
        category: 'Image',
        type: { summary: 'string' },
        defaultValue: { summary: 'none' },
      },
    },
    rounded: {
      control: { type: 'select' },
      options: ['none', 'sm', 'md', 'lg', 'full'],
      description: 'Image border-radius styling. Passed to Image atom.',
      table: {
        category: 'Image',
        type: { summary: 'string' },
        defaultValue: { summary: 'none' },
      },
    },
    // Caption properties
    caption: {
      control: 'text',
      description: 'Figure caption text (optional). Supports Markdown links.',
      table: {
        category: 'Caption',
        type: { summary: 'string' },
      },
    },
    caption_position: {
      control: { type: 'select' },
      options: ['bottom', 'top'],
      description: 'Caption display position relative to image.',
      table: {
        category: 'Caption',
        type: { summary: 'string' },
        defaultValue: { summary: 'bottom' },
      },
    },
  },
};

/**
 * Default: Image with bottom caption
 */
export const Default = {
  args: data,
};

/**
 * Without Caption: Image only (caption empty)
 */
export const WithoutCaption = {
  args: {
    src: '/source/assets/images/16-9.jpg',
    alt: 'Premium commercial real estate property',
    width: 1200,
    height: 675,
    caption: '',
    loading: 'lazy',
  },
};

/**
 * Top Caption: Caption positioned above image
 */
export const TopCaption = {
  args: {
    src: '/source/assets/images/16-9.jpg',
    alt: 'Modern office building with sustainable design',
    width: 1200,
    height: 675,
    caption: 'Eco-certified commercial complex in urban center',
    caption_position: 'top',
    loading: 'lazy',
  },
};

/**
 * Rounded Corners: Image with border-radius modifier
 */
export const RoundedImage = {
  args: {
    src: '/source/assets/images/16-9.jpg',
    alt: 'Commercial property with architectural details',
    width: 1200,
    height: 675,
    caption: 'Professional property showcase with refined styling',
    rounded: 'md',
    loading: 'lazy',
  },
};

/**
 * Object-Fit Cover: Image scales and crops to fill container
 */
export const ObjectFitCover = {
  args: {
    src: '/source/assets/images/16-9.jpg',
    alt: 'Office space with modern amenities',
    width: 1200,
    height: 675,
    caption: 'Prime location commercial real estate',
    fit: 'cover',
    loading: 'lazy',
  },
};

/**
 * Responsive with Srcset: Multiple image sizes for different viewports
 */
export const ResponsiveImage = {
  args: {
    src: '/source/assets/images/16-9.jpg',
    alt: 'Responsive commercial property showcase',
    width: 1200,
    height: 675,
    srcset: [
      '/source/assets/images/16-9-sm.jpg 640w',
      '/source/assets/images/16-9-md.jpg 1024w',
      '/source/assets/images/16-9.jpg 1200w',
    ],
    sizes: '(min-width: 768px) 80vw, 100vw',
    caption: 'Optimized for all device sizes',
    loading: 'lazy',
  },
};
