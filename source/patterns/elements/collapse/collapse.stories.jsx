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
      description: 'Raw HTML content for panel (backward-compat)',
      control: 'text',
      table: { category: 'Content' },
    },
    text: {
      name: 'Text (atomic)',
      description: 'Text content rendered via text atom',
      control: 'text',
      table: { category: 'Content' },
    },
    text_format: {
      name: 'Text Format',
      description: 'Format variant for text atom',
      control: 'select',
      options: ['body-medium', 'body-small', 'body-large'],
      table: { category: 'Content' },
    },

    // Appearance
    variant: {
      name: 'Variant',
      description: 'Visual style variant',
      control: 'select',
      options: [
        'default',
        'primary',
        'secondary',
        'success',
        'warning',
        'danger',
        'info',
        'dark',
        'light',
      ],
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

export const ComposedWithAtom = {
  args: {
    id: 'collapse-composed',
    title: 'Real Estate Listing Features',
    text: 'This luxury property offers 3 bedrooms, 2 bathrooms, modern kitchen with granite countertops, hardwood flooring throughout, and a private balcony overlooking the city skyline.',
    text_format: 'body-medium',
  },
  render: (args) => collapseTwig(args),
};

export const MultipleItems = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 0;">
      ${collapseTwig({
        id: 'property-1',
        title: 'Downtown Apartment',
        text: '2-bedroom apartment in prime downtown location. Walk to restaurants, shops, and public transit. Recently renovated with modern finishes.',
        expanded: false,
      })}
      ${collapseTwig({
        id: 'property-2',
        title: 'Suburban House',
        text: '4-bedroom family home with large backyard and attached garage. Quiet neighborhood with excellent schools nearby.',
        expanded: true,
      })}
      ${collapseTwig({
        id: 'property-3',
        title: 'Waterfront Condo',
        text: 'Luxury condo with stunning ocean views. Resort-style amenities including pool, gym, and 24/7 concierge service.',
        expanded: false,
      })}
    </div>
  `,
};

export const SemanticVariants = {
  name: 'Semantic Variants',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin-bottom: var(--size-3); font-size: var(--font-size-2);">Default</h3>
        ${collapseTwig({
          id: 'variant-default',
          title: 'Property Financing Options',
          text: 'We offer flexible financing solutions including conventional mortgages, FHA loans, and investment property financing. Our team will guide you through the pre-approval process.',
          variant: 'default',
          expanded: false,
        })}
      </div>
      <div>
        <h3 style="margin-bottom: var(--size-3); font-size: var(--font-size-2);">Primary</h3>
        ${collapseTwig({
          id: 'variant-primary',
          title: 'Featured Property Details',
          text: 'This premium commercial space features modern amenities, high-speed fiber internet, 24/7 security, and panoramic city views. Perfect for executive offices or tech startups.',
          variant: 'primary',
          expanded: true,
        })}
      </div>
      <div>
        <h3 style="margin-bottom: var(--size-3); font-size: var(--font-size-2);">Secondary</h3>
        ${collapseTwig({
          id: 'variant-secondary',
          title: 'Additional Property Information',
          text: 'Built in 2022, this LEED-certified building offers energy-efficient systems, ample parking, and convenient access to public transportation and major highways.',
          variant: 'secondary',
          expanded: false,
        })}
      </div>
      <div>
        <h3 style="margin-bottom: var(--size-3); font-size: var(--font-size-2);">Success</h3>
        ${collapseTwig({
          id: 'variant-success',
          title: 'Property Available Now',
          text: 'Immediate occupancy available! This move-in ready office space has been freshly renovated and is ready for your business. Schedule a viewing today.',
          variant: 'success',
          expanded: false,
        })}
      </div>
      <div>
        <h3 style="margin-bottom: var(--size-3); font-size: var(--font-size-2);">Warning</h3>
        ${collapseTwig({
          id: 'variant-warning',
          title: 'Limited Availability',
          text: 'Only 3 units remaining in this high-demand building. Act fast to secure your preferred space before they are gone. Contact our leasing team immediately.',
          variant: 'warning',
          expanded: false,
        })}
      </div>
      <div>
        <h3 style="margin-bottom: var(--size-3); font-size: var(--font-size-2);">Danger</h3>
        ${collapseTwig({
          id: 'variant-danger',
          title: 'Property Inspection Required',
          text: 'This property requires mandatory structural inspection before closing. Foundation issues have been identified and must be addressed by qualified contractors.',
          variant: 'danger',
          expanded: false,
        })}
      </div>
      <div>
        <h3 style="margin-bottom: var(--size-3); font-size: var(--font-size-2);">Info</h3>
        ${collapseTwig({
          id: 'variant-info',
          title: 'Market Insights & Trends',
          text: 'The commercial real estate market in this district has seen 12% year-over-year growth. Average lease rates are €45/sqm, with strong demand for Class A office space.',
          variant: 'info',
          expanded: false,
        })}
      </div>
      <div>
        <h3 style="margin-bottom: var(--size-3); font-size: var(--font-size-2);">Dark</h3>
        ${collapseTwig({
          id: 'variant-dark',
          title: 'Exclusive Listing',
          text: 'Private sale opportunity for discerning investors. This off-market property offers exceptional ROI potential with established tenants and premium location.',
          variant: 'dark',
          expanded: false,
        })}
      </div>
      <div>
        <h3 style="margin-bottom: var(--size-3); font-size: var(--font-size-2);">Light</h3>
        ${collapseTwig({
          id: 'variant-light',
          title: 'General Property FAQ',
          text: 'Have questions about our properties? Our FAQ covers common inquiries about lease terms, maintenance responsibilities, parking policies, and move-in procedures.',
          variant: 'light',
          expanded: false,
        })}
      </div>
    </div>
  `,
};
