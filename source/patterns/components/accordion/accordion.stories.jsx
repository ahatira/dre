import accordionTwig from './accordion.twig';
import data from './accordion.yml';
import './accordion.js';

const settings = {
  title: 'Components/Accordion',
  tags: ['autodocs'],
  render: (args) => accordionTwig(args),
  args: data.args || data,
  parameters: {
    docs: {
      description: {
        component:
          'Simplified pixel-perfect accordion: default separators, optional flush, aria-expanded controls region panels. Tokens only.',
      },
    },
  },
  argTypes: {
    items: {
      description: 'Sections to render (title, content, id?, open?)',
      table: {
        category: 'Content',
        type: { summary: 'Array<{ id?, title, content, open? }>' },
      },
    },
    singleOpen: {
      description: 'When true, only one section can be expanded',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'true' },
      },
    },
    flush: {
      description: 'Remove horizontal padding for dense lists',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    headingLevel: {
      description: 'Heading level for item headers',
      control: { type: 'select' },
      options: ['h2', 'h3', 'h4', 'h5'],
      table: {
        category: 'Accessibility',
        type: { summary: 'h2 | h3 | h4 | h5' },
        defaultValue: { summary: 'h3' },
      },
    },
  },
};

export const Default = {
  render: (args) => accordionTwig(args),
  args: data.args || data,
};

export const Flush = {
  render: () => accordionTwig({ ...(data.args || data), flush: true }),
};

export const MultipleOpen = {
  render: () => {
    const baseData = data.args || data;
    return accordionTwig({
      ...baseData,
      singleOpen: false,
      items: baseData.items?.map((it, i) => ({ ...it, open: i === 0 })) || [],
    });
  },
};

export const HeadingLevelH4 = {
  render: () => accordionTwig({ ...(data.args || data), headingLevel: 'h4' }),
};

export const AllVariants = {
  render: () => {
    const baseData = data.args || data;
    return `
    <div style="display:flex; flex-direction:column; gap: var(--ps-spacing-5);">
      ${accordionTwig({ ...baseData })}
      ${accordionTwig({ ...baseData, flush: true })}
      ${accordionTwig({ ...baseData, singleOpen: false })}
      ${accordionTwig({ ...baseData, headingLevel: 'h4' })}
    </div>
  `;
  },
};

export default settings;
