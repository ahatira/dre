import breadcrumbTwig from './breadcrumb.twig';
import breadcrumbData from './breadcrumb.yml';

const settings = {
  title: 'Components/Breadcrumb',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Breadcrumb shows the page hierarchy with semantic, accessible markup. Improves navigation and SEO with clear current-page indication.',
      },
    },
  },
  argTypes: {
    items: {
      description: 'Array of breadcrumb items with label and optional url',
      control: { type: 'object' },
      table: {
        category: 'Content',
        type: { summary: 'array<{label: string, url?: string}>' },
      },
    },
  },
};

export const Default = {
  render: (args) => breadcrumbTwig(args),
  args: { ...breadcrumbData },
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
        { label: 'Office Buildings', url: '/real-estate/commercial/offices' },
        { label: 'Paris', url: '/real-estate/commercial/offices/paris' },
        { label: '8th District', url: '/real-estate/commercial/offices/paris/8th' },
        { label: 'Champs-Élysées Premium Office Space' },
      ],
    }),
};

export const Residential = {
  render: () =>
    breadcrumbTwig({
      items: [
        { label: 'Home', url: '/' },
        { label: 'Residential Properties', url: '/residential' },
        { label: 'Paris 15e Apartments', url: '/residential/paris-15' },
        { label: 'Modern 3-Bedroom Apartment' },
      ],
    }),
};

export default settings;
