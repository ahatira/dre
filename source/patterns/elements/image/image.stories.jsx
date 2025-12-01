import { faker } from '@faker-js/faker';
import imageTwig from './image.twig';
import imageData from './image.yml';

/**
 * Image component stories
 * Responsive image with lazy loading, srcset/sizes, object-fit and border-radius.
 */
export default {
  title: 'Elements/Image',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Responsive image with lazy loading, srcset/sizes support, object-fit and border-radius options. For figures with captions, see Figure component.',
      },
    },
  },
  argTypes: {
    // Content
    src: {
      control: 'text',
      description: 'Image source URL',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    alt: {
      control: 'text',
      description: 'Alternative text for accessibility',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    width: {
      control: 'number',
      description: 'Intrinsic width in pixels (prevents CLS)',
      table: {
        category: 'Content',
        type: { summary: 'integer' },
      },
    },
    height: {
      control: 'number',
      description: 'Intrinsic height in pixels (prevents CLS)',
      table: {
        category: 'Content',
        type: { summary: 'integer' },
      },
    },
    srcset: {
      control: 'object',
      description:
        'Array of responsive image sources (e.g., ["/img-400.jpg 400w", "/img-800.jpg 800w"])',
      table: {
        category: 'Content',
        type: { summary: 'array<string>' },
      },
    },
    sizes: {
      control: 'text',
      description: 'Sizes attribute for responsive images (e.g., "(min-width: 768px) 50vw, 100vw")',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },

    // Appearance
    fit: {
      control: 'select',
      options: ['none', 'cover', 'contain', 'fill', 'scale-down'],
      description: 'Object-fit behavior',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'none' },
      },
    },
    rounded: {
      control: 'select',
      options: ['none', 'sm', 'md', 'lg', 'full'],
      description: 'Border-radius style',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'none' },
      },
    },

    // Behavior
    loading: {
      control: 'select',
      options: ['lazy', 'eager'],
      description: 'Loading strategy (lazy = load when near viewport, eager = immediate)',
      table: {
        category: 'Behavior',
        type: { summary: 'string' },
        defaultValue: { summary: 'lazy' },
      },
    },
    decoding: {
      control: 'select',
      options: ['auto', 'async', 'sync'],
      description: 'Decoding hint for browser',
      table: {
        category: 'Behavior',
        type: { summary: 'string' },
        defaultValue: { summary: 'auto' },
      },
    },
  },
  args: {
    ...imageData,
  },
};

/**
 * Default image - responsive with lazy loading
 */
export const Default = {
  render: (args) => imageTwig(args),
  args: {
    src: faker.image.urlLoremFlickr({ category: 'building', width: 800, height: 450 }),
    alt: `${faker.company.buzzAdjective()} commercial building in ${faker.location.city()}`,
    width: 800,
    height: 450,
  },
};

/**
 * All object-fit variants
 */
export const AllObjectFit = {
  render: () => `
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; max-width: 1200px;">
      <div>
        <p style="margin-bottom: 0.5rem; font-weight: 500;">none (default)</p>
        <div style="width: 200px; height: 200px; border: 1px dashed #ccc; overflow: hidden; display: flex; align-items: center; justify-content: center;">
          ${imageTwig({
            src: faker.image.urlLoremFlickr({ category: 'building', width: 400, height: 300 }),
            alt: `Office building entrance in ${faker.location.city()}`,
            width: 400,
            height: 300,
            fit: 'none',
          })}
        </div>
      </div>
      
      <div>
        <p style="margin-bottom: 0.5rem; font-weight: 500;">cover</p>
        <div style="width: 200px; height: 200px; border: 1px dashed #ccc; overflow: hidden;">
          ${imageTwig({
            src: faker.image.urlLoremFlickr({ category: 'apartment', width: 400, height: 300 }),
            alt: `Modern apartment balcony overlooking ${faker.location.city()}`,
            width: 400,
            height: 300,
            fit: 'cover',
          })}
        </div>
      </div>
      
      <div>
        <p style="margin-bottom: 0.5rem; font-weight: 500;">contain</p>
        <div style="width: 200px; height: 200px; border: 1px dashed #ccc; overflow: hidden; display: flex; align-items: center; justify-content: center;">
          ${imageTwig({
            src: faker.image.urlLoremFlickr({ category: 'architecture', width: 400, height: 300 }),
            alt: 'Residential complex architectural design',
            width: 400,
            height: 300,
            fit: 'contain',
          })}
        </div>
      </div>
      
      <div>
        <p style="margin-bottom: 0.5rem; font-weight: 500;">fill</p>
        <div style="width: 200px; height: 200px; border: 1px dashed #ccc; overflow: hidden;">
          ${imageTwig({
            src: faker.image.urlLoremFlickr({ category: 'blueprint', width: 400, height: 300 }),
            alt: 'Commercial real estate floor plan layout',
            width: 400,
            height: 300,
            fit: 'fill',
          })}
        </div>
      </div>
      
      <div>
        <p style="margin-bottom: 0.5rem; font-weight: 500;">scale-down</p>
        <div style="width: 200px; height: 200px; border: 1px dashed #ccc; overflow: hidden; display: flex; align-items: center; justify-content: center;">
          ${imageTwig({
            src: faker.image.urlLoremFlickr({ category: 'logo', width: 100, height: 100 }),
            alt: `${faker.company.name()} Real Estate logo`,
            width: 100,
            height: 100,
            fit: 'scale-down',
          })}
        </div>
      </div>
    </div>
  `,
};

/**
 * All border-radius variants
 */
export const AllRounded = {
  render: () => `
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; max-width: 1000px;">
      <div>
        <p style="margin-bottom: 0.5rem; font-weight: 500;">none (default)</p>
        ${imageTwig({
          src: faker.image.urlLoremFlickr({ category: 'skyscraper', width: 200, height: 200 }),
          alt: `${faker.location.city()} downtown office tower`,
          width: 200,
          height: 200,
          rounded: 'none',
        })}
      </div>
      
      <div>
        <p style="margin-bottom: 0.5rem; font-weight: 500;">sm (4px)</p>
        ${imageTwig({
          src: faker.image.urlLoremFlickr({ category: 'apartment', width: 200, height: 200 }),
          alt: `Modern apartment building in ${faker.location.city()}`,
          width: 200,
          height: 200,
          rounded: 'sm',
        })}
      </div>
      
      <div>
        <p style="margin-bottom: 0.5rem; font-weight: 500;">md (8px)</p>
        ${imageTwig({
          src: faker.image.urlLoremFlickr({ category: 'penthouse', width: 200, height: 200 }),
          alt: 'Luxury penthouse terrace with panoramic view',
          width: 200,
          height: 200,
          rounded: 'md',
        })}
      </div>
      
      <div>
        <p style="margin-bottom: 0.5rem; font-weight: 500;">lg (16px)</p>
        ${imageTwig({
          src: faker.image.urlLoremFlickr({ category: 'garden', width: 200, height: 200 }),
          alt: 'Residential complex landscaped courtyard',
          width: 200,
          height: 200,
          rounded: 'lg',
        })}
      </div>
      
      <div>
        <p style="margin-bottom: 0.5rem; font-weight: 500;">full (circle)</p>
        ${imageTwig({
          src: faker.image.avatar(),
          alt: `${faker.person.fullName()}, Real Estate Consultant`,
          width: 200,
          height: 200,
          rounded: 'full',
        })}
      </div>
    </div>
  `,
};

/**
 * Responsive image with srcset/sizes
 */
export const ResponsiveWithSrcset = {
  render: (args) => imageTwig(args),
  args: {
    src: faker.image.urlLoremFlickr({ category: 'apartment', width: 800, height: 450 }),
    alt: `Luxury apartment complex in ${faker.location.city()}`,
    width: 800,
    height: 450,
    srcset: [
      `${faker.image.urlLoremFlickr({ category: 'apartment', width: 400, height: 225 })} 400w`,
      `${faker.image.urlLoremFlickr({ category: 'apartment', width: 800, height: 450 })} 800w`,
      `${faker.image.urlLoremFlickr({ category: 'apartment', width: 1200, height: 675 })} 1200w`,
    ],
    sizes: '(min-width: 1024px) 960px, 100vw',
  },
};

/**
 * Avatar use case - circular image with cover
 */
export const AvatarUseCase = {
  render: (args) => imageTwig(args),
  args: {
    src: faker.image.avatar(),
    alt: `${faker.person.fullName()}, Senior Real Estate Agent`,
    width: 200,
    height: 200,
    fit: 'cover',
    rounded: 'full',
  },
};

/**
 * Decorative image (empty alt)
 */
export const DecorativeImage = {
  render: (args) => imageTwig(args),
  args: {
    src: faker.image.urlLoremFlickr({ category: 'abstract', width: 100, height: 100 }),
    alt: '',
    width: 100,
    height: 100,
  },
};

/**
 * Card thumbnail use case
 */
export const CardThumbnailUseCase = {
  render: (args) => {
    const city = faker.location.city();
    const price = faker.commerce.price({ min: 500000, max: 3000000, dec: 0, symbol: '€' });
    const bedrooms = faker.number.int({ min: 2, max: 5 });

    return `
      <div style="width: 300px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
        ${imageTwig(args)}
        <div style="padding: 1rem;">
          <h3 style="margin: 0 0 0.5rem; font-size: 1.125rem;">Luxury Penthouse</h3>
          <p style="margin: 0; color: #666; font-size: 0.875rem;">${city} • ${bedrooms} bedrooms • ${price}</p>
        </div>
      </div>
    `;
  },
  args: {
    src: faker.image.urlLoremFlickr({ category: 'penthouse', width: 300, height: 200 }),
    alt: `Luxury penthouse living room with panoramic ${faker.location.city()} views`,
    width: 300,
    height: 200,
    fit: 'cover',
    rounded: 'none',
  },
};
