import imageTwig from './image.twig';
import data from './image.yml';

const settings = {
  title: 'Elements/Image',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Responsive image component with lazy loading, srcset/sizes support, optional aspect ratios, and border radius variants. Supports object-fit (cover/contain) and various rounded corner options including full circle for avatars.',
      },
    },
  },
  argTypes: {
    src: {
      description: 'Image source URL',
      control: { type: 'text' },
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    alt: {
      description: 'Alternative text for accessibility (required)',
      control: { type: 'text' },
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    width: {
      description: 'Intrinsic width in pixels',
      control: { type: 'number' },
      table: {
        type: { summary: 'number' },
        defaultValue: { summary: 'undefined' },
      },
    },
    height: {
      description: 'Intrinsic height in pixels',
      control: { type: 'number' },
      table: {
        type: { summary: 'number' },
        defaultValue: { summary: 'undefined' },
      },
    },
    srcset: {
      description: 'Array of srcset strings (e.g., ["/img-400.jpg 400w","/img-800.jpg 800w"])',
      control: { type: 'object' },
      table: {
        type: { summary: 'array<string>' },
        defaultValue: { summary: '[]' },
      },
    },
    sizes: {
      description: 'Sizes attribute (e.g., "(min-width: 768px) 50vw, 100vw")',
      control: { type: 'text' },
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    loading: {
      description: 'Loading strategy',
      control: { type: 'select' },
      options: ['lazy', 'eager'],
      table: {
        type: { summary: 'lazy | eager' },
        defaultValue: { summary: 'lazy' },
      },
    },
    decoding: {
      description: 'Decoding strategy',
      control: { type: 'select' },
      options: ['auto', 'async', 'sync'],
      table: {
        type: { summary: 'auto | async | sync' },
        defaultValue: { summary: 'auto' },
      },
    },
    fit: {
      description: 'Object-fit CSS property value',
      control: { type: 'select' },
      options: ['cover', 'contain'],
      table: {
        type: { summary: 'cover | contain' },
        defaultValue: { summary: 'cover' },
      },
    },
    rounded: {
      description: 'Border radius variant',
      control: { type: 'select' },
      options: ['none', 'sm', 'md', 'lg', 'full'],
      table: {
        type: { summary: 'none | sm | md | lg | full' },
        defaultValue: { summary: 'none' },
      },
    },
    ratio: {
      description: 'Aspect ratio variant using padding technique',
      control: { type: 'select' },
      options: ['none', '16x9', '1x1', '4x3'],
      table: {
        type: { summary: 'none | 16x9 | 1x1 | 4x3' },
        defaultValue: { summary: 'none' },
      },
    },
  },
};

export const Default = {
  render: (args) => imageTwig(args),
  args: { ...data },
};

export const WithRatio16x9 = {
  render: () =>
    imageTwig({
      src: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1600&h=900&fit=crop',
      alt: 'Modern glass office building in 16:9 ratio',
      width: 1600,
      height: 900,
      ratio: '16x9',
    }),
};

export const WithRatio1x1 = {
  render: () =>
    imageTwig({
      src: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&h=800&fit=crop',
      alt: 'Apartment building in 1:1 ratio',
      width: 800,
      height: 800,
      ratio: '1x1',
    }),
};

export const WithRatio4x3 = {
  render: () =>
    imageTwig({
      src: 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=800&h=600&fit=crop',
      alt: 'Residential building in 4:3 ratio',
      width: 800,
      height: 600,
      ratio: '4x3',
    }),
};

export const RoundedSmall = {
  render: () =>
    imageTwig({
      src: 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=400&h=300&fit=crop',
      alt: 'Commercial building with small rounded corners',
      width: 400,
      height: 300,
      rounded: 'sm',
    }),
};

export const RoundedMedium = {
  render: () =>
    imageTwig({
      src: 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=400&h=300&fit=crop',
      alt: 'Luxury apartment with medium rounded corners',
      width: 400,
      height: 300,
      rounded: 'md',
    }),
};

export const RoundedLarge = {
  render: () =>
    imageTwig({
      src: 'https://images.unsplash.com/photo-1516156008625-3a9d6067fab5?w=400&h=300&fit=crop',
      alt: 'Modern house with large rounded corners',
      width: 400,
      height: 300,
      rounded: 'lg',
    }),
};

export const RoundedFull = {
  render: () =>
    imageTwig({
      src: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=200&h=200&fit=crop&crop=faces',
      alt: 'Real estate agent avatar',
      width: 200,
      height: 200,
      rounded: 'full',
      ratio: '1x1',
    }),
};

export const FitContain = {
  render: () =>
    imageTwig({
      src: 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=400&h=600&fit=crop',
      alt: 'Tall skyscraper with contain fit',
      width: 400,
      height: 600,
      fit: 'contain',
      ratio: '16x9',
    }),
};

export const AllRatios = {
  render: () => `
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--size-4);">
      <div>
        <h4 style="margin-bottom: var(--size-2); font-size: var(--size-4);">No Ratio (Auto)</h4>
        ${imageTwig({
          src: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800&h=450&fit=crop',
          alt: 'Office building auto height',
          width: 800,
          height: 450,
          ratio: 'none',
        })}
      </div>
      <div>
        <h4 style="margin-bottom: var(--size-2); font-size: var(--size-4);">16:9 Ratio</h4>
        ${imageTwig({
          src: 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=1600&h=900&fit=crop',
          alt: '16:9 residential complex',
          width: 1600,
          height: 900,
          ratio: '16x9',
        })}
      </div>
      <div>
        <h4 style="margin-bottom: var(--size-2); font-size: var(--size-4);">1:1 Ratio</h4>
        ${imageTwig({
          src: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&h=800&fit=crop',
          alt: '1:1 apartment tower',
          width: 800,
          height: 800,
          ratio: '1x1',
        })}
      </div>
      <div>
        <h4 style="margin-bottom: var(--size-2); font-size: var(--size-4);">4:3 Ratio</h4>
        ${imageTwig({
          src: 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800&h=600&fit=crop',
          alt: '4:3 commercial property',
          width: 800,
          height: 600,
          ratio: '4x3',
        })}
      </div>
    </div>
  `,
};

export const AllRounded = {
  render: () => `
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--size-4);">
      <div>
        <h4 style="margin-bottom: var(--size-2); font-size: var(--size-4); text-align: center;">None</h4>
        ${imageTwig({
          src: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=300&h=200&fit=crop',
          alt: 'Office building, no rounded corners',
          width: 300,
          height: 200,
          rounded: 'none',
        })}
      </div>
      <div>
        <h4 style="margin-bottom: var(--size-2); font-size: var(--size-4); text-align: center;">Small</h4>
        ${imageTwig({
          src: 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=300&h=200&fit=crop',
          alt: 'Commercial building, small rounded',
          width: 300,
          height: 200,
          rounded: 'sm',
        })}
      </div>
      <div>
        <h4 style="margin-bottom: var(--size-2); font-size: var(--size-4); text-align: center;">Medium</h4>
        ${imageTwig({
          src: 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=300&h=200&fit=crop',
          alt: 'Luxury apartment, medium rounded',
          width: 300,
          height: 200,
          rounded: 'md',
        })}
      </div>
      <div>
        <h4 style="margin-bottom: var(--size-2); font-size: var(--size-4); text-align: center;">Large</h4>
        ${imageTwig({
          src: 'https://images.unsplash.com/photo-1516156008625-3a9d6067fab5?w=300&h=200&fit=crop',
          alt: 'Modern house, large rounded',
          width: 300,
          height: 200,
          rounded: 'lg',
        })}
      </div>
      <div>
        <h4 style="margin-bottom: var(--size-2); font-size: var(--size-4); text-align: center;">Full (Avatar)</h4>
        <div style="max-width: 150px; margin: 0 auto;">
          ${imageTwig({
            src: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=200&h=200&fit=crop&crop=faces',
            alt: 'Real estate agent avatar',
            width: 200,
            height: 200,
            rounded: 'full',
            ratio: '1x1',
          })}
        </div>
      </div>
    </div>
  `,
};

export const ObjectFit = {
  render: () => `
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--size-4);">
      <div>
        <h4 style="margin-bottom: var(--size-2); font-size: var(--size-4);">Cover (default)</h4>
        ${imageTwig({
          src: 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=400&h=600&fit=crop',
          alt: 'Tall skyscraper with cover fit',
          width: 400,
          height: 600,
          fit: 'cover',
          ratio: '16x9',
        })}
      </div>
      <div>
        <h4 style="margin-bottom: var(--size-2); font-size: var(--size-4);">Contain</h4>
        ${imageTwig({
          src: 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=400&h=600&fit=crop',
          alt: 'Tall skyscraper with contain fit',
          width: 400,
          height: 600,
          fit: 'contain',
          ratio: '16x9',
        })}
      </div>
    </div>
  `,
};

export const WithSrcset = {
  render: () =>
    imageTwig({
      src: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800&h=450&fit=crop',
      alt: 'Responsive office building with srcset',
      width: 800,
      height: 450,
      srcset: [
        'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=400&h=225&fit=crop 400w',
        'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800&h=450&fit=crop 800w',
        'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1200&h=675&fit=crop 1200w',
      ],
      sizes: '(min-width: 1024px) 960px, 100vw',
      rounded: 'md',
    }),
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin-bottom: var(--size-3); font-size: var(--size-5);">Hero Banner (16:9)</h3>
        ${imageTwig({
          src: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1600&h=900&fit=crop',
          alt: 'Modern office building hero banner',
          width: 1600,
          height: 900,
          ratio: '16x9',
          rounded: 'md',
        })}
      </div>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--size-4);">
        <div>
          <h3 style="margin-bottom: var(--size-3); font-size: var(--size-5);">Card Image (4:3)</h3>
          ${imageTwig({
            src: 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=800&h=600&fit=crop',
            alt: 'Residential property card thumbnail',
            width: 800,
            height: 600,
            ratio: '4x3',
            rounded: 'md',
          })}
        </div>
        <div>
          <h3 style="margin-bottom: var(--size-3); font-size: var(--size-4);">Profile Avatar</h3>
          <div style="max-width: 120px;">
            ${imageTwig({
              src: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=200&h=200&fit=crop&crop=faces',
              alt: 'Real estate agent profile',
              width: 200,
              height: 200,
              ratio: '1x1',
              rounded: 'full',
            })}
          </div>
        </div>
      </div>
    </div>
  `,
};

export default settings;
