import accordionTwig from './accordion.twig';
import data from './accordion.yml';

const settings = {
  title: 'Collections/Accordion',
  tags: ['autodocs'],
  render: (args) => accordionTwig(args),
  args: data.args || data,
  parameters: {
    docs: {
      description: {
        component:
          'Orchestrates multiple Collapse elements with optional single-open coordination. Collection-level component managing group behavior.',
      },
    },
  },
  argTypes: {
    items: {
      description: 'Array of collapse items. Each item: { id?, title, content?, expanded? }',
      table: {
        category: 'Content',
        type: { summary: 'Array<{ id?, title, content?, expanded? }>' },
      },
    },
    single_open: {
      description: 'When true, only one section can be expanded at a time',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'true' },
      },
    },
    variant: {
      description: 'Visual style variant',
      control: { type: 'select' },
      options: [
        'default',
        'flush',
        'primary',
        'secondary',
        'success',
        'warning',
        'danger',
        'info',
        'dark',
        'light',
      ],
      table: {
        category: 'Appearance',
        type: {
          summary:
            'default | flush | primary | secondary | success | warning | danger | info | dark | light',
        },
        defaultValue: { summary: 'default' },
      },
    },
    attributes: {
      description: 'Drupal attributes object for root element',
      table: {
        category: 'Layout',
        type: { summary: 'Drupal.Attribute' },
      },
    },
  },
};

export const Default = {
  render: (args) => accordionTwig(args),
  args: data.args || data,
};

export const BehaviorShowcase = {
  name: 'Behaviors',
  render: () => {
    const baseData = data.args || data;
    return `
    <div style="display:flex; flex-direction:column; gap: var(--size-8);">
      <div>
        <h3 style="margin-bottom: var(--size-4);">Single Open Mode (Default)</h3>
        ${accordionTwig({ ...baseData, single_open: true })}
      </div>
      <div>
        <h3 style="margin-bottom: var(--size-4);">Multiple Open Mode</h3>
        ${accordionTwig({
          ...baseData,
          single_open: false,
          items: baseData.items?.map((it, i) => ({ ...it, expanded: i < 2 })) || [],
        })}
      </div>
    </div>
  `;
  },
};

export const LayoutShowcase = {
  name: 'Layout Variants',
  render: () => {
    const baseData = data.args || data;
    return `
    <div style="display:flex; flex-direction:column; gap: var(--size-8);">
      <div>
        <h3 style="margin-bottom: var(--size-4);">Default Padding</h3>
        ${accordionTwig({ ...baseData })}
      </div>
      <div>
        <h3 style="margin-bottom: var(--size-4);">Flush (Compact)</h3>
        ${accordionTwig({ ...baseData, variant: 'flush' })}
      </div>
      <div>
        <h3 style="margin-bottom: var(--size-4);">Semantic Primary</h3>
        ${accordionTwig({ ...baseData, variant: 'primary' })}
      </div>
    </div>
  `;
  },
};

export const ComposedWithAtoms = {
  name: 'Composed with Atoms (New Pattern)',
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-4);">
      <h3 style="margin-bottom: var(--size-2);">Using raw HTML content</h3>
        ${accordionTwig({
          items: [
            {
              title: 'Property Details',
              content:
                '<p>This 2,500 sqft commercial space features modern amenities, ample parking, and is located in a prime downtown area with excellent accessibility.</p>',
              expanded: true,
            },
            {
              title: 'Pricing & Terms',
              content:
                '<p>Monthly lease rate of €3,200 includes property management and maintenance. Flexible lease terms available from 12 to 60 months.</p>',
            },
            {
              title: 'Contact Information',
              content:
                '<p>For viewings and inquiries, please contact our commercial real estate team at +33 1 23 45 67 89 or email commercial@bnpparibas-realestate.com.</p>',
            },
          ],
        })}
    </div>
  `,
};

export default settings;
