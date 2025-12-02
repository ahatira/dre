import accordionTwig from './accordion.twig';
import data from './accordion.yml';

const settings = {
  title: 'Components/Accordion',
  tags: ['autodocs'],
  render: (args) => accordionTwig(args),
  args: data.args || data,
  parameters: {
    docs: {
      description: {
        component:
          'Collapsible disclosure list with bordered separators, optional flush layout, and accessible ARIA controls.',
      },
    },
  },
  argTypes: {
    items: {
      description: 'Array of accordion sections (title, content, id?, open?)',
      table: {
        category: 'Content',
        type: { summary: 'Array<{ id?, title, content, open? }>' },
      },
    },
    singleOpen: {
      description: 'When true, only one section can be expanded at a time',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'true' },
      },
    },
    flush: {
      description: 'Remove vertical padding for compact layout',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    headingLevel: {
      description: 'Semantic heading level for accessibility',
      control: { type: 'select' },
      options: ['h2', 'h3', 'h4', 'h5'],
      table: {
        category: 'Accessibility',
        type: { summary: 'h2 | h3 | h4 | h5' },
        defaultValue: { summary: 'h3' },
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
        ${accordionTwig({ ...baseData, singleOpen: true })}
      </div>
      <div>
        <h3 style="margin-bottom: var(--size-4);">Multiple Open Mode</h3>
        ${accordionTwig({
          ...baseData,
          singleOpen: false,
          items: baseData.items?.map((it, i) => ({ ...it, open: i < 2 })) || [],
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
        ${accordionTwig({ ...baseData, flush: true })}
      </div>
    </div>
  `;
  },
};

export const AccessibilityShowcase = {
  name: 'Heading Levels',
  render: () => {
    const baseData = data.args || data;
    const singleItem = { ...baseData, items: [baseData.items?.[0] || {}] };
    return `
    <div style="display:flex; flex-direction:column; gap: var(--size-8);">
      <div>
        <h2 style="margin-bottom: var(--size-4);">Heading Level h2 (for h3 accordion)</h2>
        ${accordionTwig({ ...singleItem, headingLevel: 'h3' })}
      </div>
      <div>
        <h2 style="margin-bottom: var(--size-4);">Heading Level h3 (for h4 accordion)</h2>
        ${accordionTwig({ ...singleItem, headingLevel: 'h4' })}
      </div>
      <div>
        <h2 style="margin-bottom: var(--size-4);">Heading Level h4 (for h5 accordion)</h2>
        ${accordionTwig({ ...singleItem, headingLevel: 'h5' })}
      </div>
    </div>
  `;
  },
};

export default settings;
