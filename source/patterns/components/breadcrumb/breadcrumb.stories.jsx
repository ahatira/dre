import breadcrumbTwig from './breadcrumb.twig';
import breadcrumbData from './breadcrumb.yml';

const settings = {
  title: 'Components/Breadcrumb',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Navigation trail showing page hierarchy in site structure. Enhances SEO and UX with semantic markup, accessible states, and keyboard navigation support.',
      },
    },
  },
  argTypes: {
    items: {
      description: 'Array of breadcrumb items with label, optional url, and optional icon',
      control: { type: 'object' },
      table: {
        category: 'Content',
        type: { summary: 'array<{label: string, url?: string, icon?: string}>' },
      },
    },
    compact: {
      description: 'Enable compact spacing (reduced font size and gaps)',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    truncate: {
      description: 'Enable CSS text truncation for long labels',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
  },
};

export const Default = {
  render: (args) => breadcrumbTwig(args),
  args: { ...breadcrumbData },
};

export const WithIcons = {
  render: () =>
    breadcrumbTwig({
      items: [
        { label: 'Home', url: '/', icon: 'home' },
        { label: 'Locations', url: '/locations', icon: 'map' },
        { label: 'Paris 15e', url: '/locations/paris-15', icon: 'building' },
        { label: 'Family Apartment' },
      ],
    }),
};

export const Compact = {
  render: () =>
    breadcrumbTwig({
      items: [
        { label: 'Home', url: '/' },
        { label: 'Products', url: '/products' },
        { label: 'Electronics', url: '/products/electronics' },
        { label: 'Smartphones' },
      ],
      compact: true,
    }),
};

export const Truncated = {
  render: () =>
    breadcrumbTwig({
      items: [
        { label: 'Home', url: '/' },
        { label: 'Very Long Category Name That Should Be Truncated', url: '/category' },
        { label: 'Another Extremely Long Subcategory Name', url: '/category/subcategory' },
        { label: 'Final Item with Very Long Name' },
      ],
      truncate: true,
    }),
};

export const Simple = {
  render: () =>
    breadcrumbTwig({
      items: [{ label: 'Home', url: '/' }, { label: 'Current Page' }],
    }),
};

export const Deep = {
  render: () =>
    breadcrumbTwig({
      items: [
        { label: 'Home', url: '/' },
        { label: 'Real Estate', url: '/real-estate' },
        { label: 'Commercial', url: '/real-estate/commercial' },
        { label: 'Offices', url: '/real-estate/commercial/offices' },
        { label: 'Paris', url: '/real-estate/commercial/offices/paris' },
        { label: '8th District', url: '/real-estate/commercial/offices/paris/8th' },
        { label: 'Champs-Élysées Building' },
      ],
    }),
};

export const ShowcaseVariants = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">Standard</h3>
        ${breadcrumbTwig({
          items: [
            { label: 'Home', url: '/' },
            { label: 'Locations', url: '/locations' },
            { label: 'Paris 15e' },
          ],
        })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">Compact</h3>
        ${breadcrumbTwig({
          items: [
            { label: 'Home', url: '/' },
            { label: 'Locations', url: '/locations' },
            { label: 'Paris 15e' },
          ],
          compact: true,
        })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">With Icons</h3>
        ${breadcrumbTwig({
          items: [
            { label: 'Home', url: '/', icon: 'home' },
            { label: 'Products', url: '/products', icon: 'grid' },
            { label: 'Laptop' },
          ],
        })}
      </div>
      <div style="max-width: 400px; border: 1px solid var(--gray-200); padding: var(--size-4); border-radius: var(--radius-2);">
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">Truncated (narrow container)</h3>
        ${breadcrumbTwig({
          items: [
            { label: 'Home', url: '/' },
            { label: 'Long Category Name', url: '/category' },
            { label: 'Very Long Subcategory Name' },
          ],
          truncate: true,
        })}
      </div>
    </div>
  `,
};

export default settings;
