import collapseTwig from './collapse.twig';
import collapseData from './collapse.yml';

export default {
  title: 'Elements/Collapse',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Collapsible disclosure element with trigger and expandable panel. Single-item behavior for show/hide content.',
      },
    },
  },
  argTypes: {
    // Content
    id: {
      name: 'ID',
      description: 'Unique identifier for panel/trigger linkage (required for ARIA)',
      control: 'text',
      table: { category: 'Content' },
    },
    title: {
      name: 'Title',
      description: 'Trigger button text',
      control: 'text',
      table: { category: 'Content' },
    },
    content: {
      name: 'Content (raw)',
      description: 'Raw HTML content for panel',
      control: 'text',
      table: { category: 'Content' },
    },

    // Appearance
    variant: {
      name: 'Variant',
      description: 'Visual style variant',
      control: 'select',
      options: ['primary', 'secondary', 'success', 'warning', 'danger', 'info', 'dark', 'light'],
      table: { category: 'Appearance' },
    },

    // Behavior
    expanded: {
      name: 'Expanded',
      description: 'Initial expanded state',
      control: 'boolean',
      table: { category: 'Behavior' },
    },

    // Accessibility
    trigger_tag: {
      name: 'Trigger Tag',
      description: 'HTML tag for trigger element (default: button)',
      control: 'select',
      options: ['button', 'h3', 'h4'],
      table: { category: 'Accessibility' },
    },
    attributes: {
      name: 'Attributes',
      description:
        'Additional HTML attributes (ARIA, data-*, extra classes) for Drupal integration.',
      control: 'object',
      table: { category: 'Accessibility' },
    },
  },
};

export const Default = {
  args: collapseData,
  render: (args) => collapseTwig(args),
};

export const Expanded = {
  args: {
    ...collapseData,
    expanded: true,
  },
  render: (args) => collapseTwig(args),
};

export const BasicContent = {
  args: {
    id: 'collapse-basic',
    title: 'Real Estate Listing Features',
    content:
      'This luxury property offers 3 bedrooms, 2 bathrooms, modern kitchen with granite countertops, hardwood flooring throughout, and a private balcony overlooking the city skyline.',
  },
  render: (args) => collapseTwig(args),
};

export const Variants = {
  name: 'Variants',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 0;">
      ${collapseTwig({
        id: 'variant-default',
        title: '[Default] Property Financing Options',
        content:
          'We offer flexible financing solutions including conventional mortgages, FHA loans, and investment property financing. Our team will guide you through the pre-approval process.',
        variant: 'default',
        expanded: false,
      })}
      ${collapseTwig({
        id: 'variant-primary',
        title: '[Primary] Featured Property Details',
        content:
          'This premium commercial space features modern amenities, high-speed fiber internet, 24/7 security, and panoramic city views. Perfect for executive offices or tech startups.',
        variant: 'primary',
        expanded: true,
      })}
      ${collapseTwig({
        id: 'variant-secondary',
        title: '[Secondary] Additional Property Information',
        content:
          'Built in 2022, this LEED-certified building offers energy-efficient systems, ample parking, and convenient access to public transportation and major highways.',
        variant: 'secondary',
        expanded: false,
      })}
      ${collapseTwig({
        id: 'variant-success',
        title: '[Success] Property Available Now',
        content:
          'Immediate occupancy available! This move-in ready office space has been freshly renovated and is ready for your business. Schedule a viewing today.',
        variant: 'success',
        expanded: false,
      })}
      ${collapseTwig({
        id: 'variant-warning',
        title: '[Warning] Limited Availability',
        content:
          'Only 3 units remaining in this high-demand building. Act fast to secure your preferred space before they are gone. Contact our leasing team immediately.',
        variant: 'warning',
        expanded: false,
      })}
      ${collapseTwig({
        id: 'variant-danger',
        title: '[Danger] Property Inspection Required',
        content:
          'This property requires mandatory structural inspection before closing. Foundation issues have been identified and must be addressed by qualified contractors.',
        variant: 'danger',
        expanded: false,
      })}
      ${collapseTwig({
        id: 'variant-info',
        title: '[Info] Market Insights & Trends',
        content:
          'The commercial real estate market in this district has seen 12% year-over-year growth. Average lease rates are €45/sqm, with strong demand for Class A office space.',
        variant: 'info',
        expanded: false,
      })}
      ${collapseTwig({
        id: 'variant-dark',
        title: '[Dark] Exclusive Listing',
        content:
          'Private sale opportunity for discerning investors. This off-market property offers exceptional ROI potential with established tenants and premium location.',
        variant: 'dark',
        expanded: false,
      })}
      ${collapseTwig({
        id: 'variant-light',
        title: '[Light] General Property FAQ',
        content:
          'Have questions about our properties? Our FAQ covers common inquiries about lease terms, maintenance responsibilities, parking policies, and move-in procedures.',
        variant: 'light',
        expanded: false,
      })}
    </div>
  `,
};
