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
          'Semantic HTML figure element with overlay captions positioned in 4 corners. Features dark semi-transparent background, copyright icon support, and smooth animations (fade, slide). Perfect for real estate property showcases with attribution.',
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
      description: 'Overlay caption text with dark background (optional).',
      table: {
        category: 'Caption',
        type: { summary: 'string' },
      },
    },
    caption_position: {
      control: { type: 'select' },
      options: ['bottom-left', 'bottom-right', 'top-left', 'top-right'],
      description: 'Caption overlay position in corner (4 positions).',
      table: {
        category: 'Caption',
        type: { summary: 'string' },
        defaultValue: { summary: 'bottom-left' },
      },
    },
    show_icon: {
      control: 'boolean',
      description: 'Show an icon before caption text (default: true).',
      table: {
        category: 'Caption',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'true' },
      },
    },
    icon: {
      control: 'text',
      description: 'Icon name (without prefix), e.g., "copyright", "camera", "info".',
      table: {
        category: 'Caption',
        type: { summary: 'string' },
        defaultValue: { summary: 'copyright' },
      },
    },
    caption_animation: {
      control: { type: 'select' },
      options: ['none', 'fade', 'slide-up', 'slide-down', 'slide-left', 'slide-right'],
      description: 'Caption reveal animation on hover/focus.',
      table: {
        category: 'Caption',
        type: { summary: 'string' },
        defaultValue: { summary: 'fade' },
      },
    },
  },
};

/**
 * Default: Overlay caption bottom-left with fade animation + copyright
 */
export const Default = {
  args: data,
};

/**
 * Without Caption: Image only (no overlay)
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
 * All Positions: Showcase 4 corner positions
 */
export const AllPositions = {
  render: () => `
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--size-6);">
      ${figureTemplate({
        src: '/source/assets/images/16-9.jpg',
        alt: 'Property - Top Left',
        width: 600,
        height: 338,
        caption: 'Top Left Caption',
        caption_position: 'top-left',
        caption_animation: 'fade',
      })}
      ${figureTemplate({
        src: '/source/assets/images/16-9.jpg',
        alt: 'Property - Top Right',
        width: 600,
        height: 338,
        caption: 'Top Right Caption',
        caption_position: 'top-right',
        caption_animation: 'fade',
      })}
      ${figureTemplate({
        src: '/source/assets/images/16-9.jpg',
        alt: 'Property - Bottom Left',
        width: 600,
        height: 338,
        caption: 'Bottom Left Caption',
        caption_position: 'bottom-left',
        caption_animation: 'fade',
      })}
      ${figureTemplate({
        src: '/source/assets/images/16-9.jpg',
        alt: 'Property - Bottom Right',
        width: 600,
        height: 338,
        caption: 'Bottom Right Caption',
        caption_position: 'bottom-right',
        caption_animation: 'fade',
      })}
    </div>
  `,
};

/**
 * Copyright Icon: Display copyright symbol before caption
 */
export const WithIcon = {
  args: {
    src: '/source/assets/images/16-9.jpg',
    alt: 'Commercial property with copyright',
    width: 1200,
    height: 675,
    caption: 'BNP Paribas Real Estate 2025',
    caption_position: 'bottom-right',
    show_icon: true,
    icon: 'copyright',
    caption_animation: 'fade',
    loading: 'lazy',
  },
};

/**
 * Slide Up Animation: Caption slides from bottom
 */
export const AnimationSlideUp = {
  args: {
    src: '/source/assets/images/16-9.jpg',
    alt: 'Property with slide animation',
    width: 1200,
    height: 675,
    caption: 'Premium commercial development',
    caption_position: 'bottom-left',
    caption_animation: 'slide-up',
    loading: 'lazy',
  },
};

/**
 * Slide Right Animation: Caption slides from left
 */
export const AnimationSlideRight = {
  args: {
    src: '/source/assets/images/16-9.jpg',
    alt: 'Property with slide right animation',
    width: 1200,
    height: 675,
    caption: 'Modern office space',
    caption_position: 'top-left',
    caption_animation: 'slide-right',
    loading: 'lazy',
  },
};

/**
 * No Animation: Caption always visible
 */
export const NoAnimation = {
  args: {
    src: '/source/assets/images/16-9.jpg',
    alt: 'Property with static caption',
    width: 1200,
    height: 675,
    caption: 'Always visible caption',
    caption_position: 'bottom-left',
    caption_animation: 'none',
    loading: 'lazy',
  },
};
