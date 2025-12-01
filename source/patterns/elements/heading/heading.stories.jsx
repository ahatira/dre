import component from './heading.twig';
import data from './heading.yml';
import './heading.css';

export default {
  title: 'Elements/Heading',
  render: (args) => component(args),
  args: data,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Semantic heading component (h1–h6) with component-scoped CSS variables for typography, colors, weights, and alignment. Supports visually hidden mode for accessibility.',
      },
    },
  },
  argTypes: {
    // Content
    text: {
      control: 'text',
      description: 'Heading text content',
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'Heading text' },
      },
    },
    // Structure
    level: {
      control: { type: 'select' },
      options: ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
      description:
        'Semantic heading level (h1: 48px, h2: 36px, h3: 28px, h4: 24px, h5: 20px, h6: 16px)',
      table: {
        category: 'Structure',
        type: { summary: 'h1 | h2 | h3 | h4 | h5 | h6' },
        defaultValue: { summary: 'h1' },
      },
    },
    // Appearance
    color: {
      control: { type: 'select' },
      options: ['default', 'primary', 'secondary', 'success', 'warning', 'danger', 'info'],
      description: 'Semantic color variant',
      table: {
        category: 'Appearance',
        type: { summary: 'default | primary | secondary | success | warning | danger | info' },
        defaultValue: { summary: 'default' },
      },
    },
    weight: {
      control: { type: 'select' },
      options: ['light', 'regular', 'bold', 'extra'],
      description: 'Font weight variant (light: 300, regular: 400, bold: 700, extra: 800)',
      table: {
        category: 'Appearance',
        type: { summary: 'light | regular | bold | extra' },
        defaultValue: { summary: 'bold' },
      },
    },
    align: {
      control: { type: 'inline-radio' },
      options: ['left', 'center', 'right'],
      description: 'Text alignment',
      table: {
        category: 'Appearance',
        type: { summary: 'left | center | right' },
        defaultValue: { summary: 'left' },
      },
    },
    // Accessibility
    visuallyHidden: {
      control: 'boolean',
      description: 'Hide visually but keep for screen readers (accessibility)',
      table: {
        category: 'Accessibility',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
  },
};

export const Default = {
  render: (args) => component(args),
  args: { ...data },
};

export const AllLevels = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      ${component({ level: 'h1', text: 'H1 - Main Page Title' })}
      ${component({ level: 'h2', text: 'H2 - Section Title' })}
      ${component({ level: 'h3', text: 'H3 - Subsection Title' })}
      ${component({ level: 'h4', text: 'H4 - Content Block Title' })}
      ${component({ level: 'h5', text: 'H5 - Small Heading' })}
      ${component({ level: 'h6', text: 'H6 - Micro Title' })}
    </div>
  `,
};

export const AllColors = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-5);">
      ${component({ level: 'h2', text: 'Default Heading', color: 'default' })}
      ${component({ level: 'h2', text: 'Primary Heading', color: 'primary' })}
      ${component({ level: 'h2', text: 'Secondary Heading', color: 'secondary' })}
      ${component({ level: 'h2', text: 'Success Heading', color: 'success' })}
      ${component({ level: 'h2', text: 'Warning Heading', color: 'warning' })}
      ${component({ level: 'h2', text: 'Danger Heading', color: 'danger' })}
      ${component({ level: 'h2', text: 'Info Heading', color: 'info' })}
    </div>
  `,
};

export const AllWeights = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-5);">
      ${component({ level: 'h2', text: 'Light Weight Heading', weight: 'light' })}
      ${component({ level: 'h2', text: 'Regular Weight Heading', weight: 'regular' })}
      ${component({ level: 'h2', text: 'Bold Weight Heading', weight: 'bold' })}
      ${component({ level: 'h2', text: 'Extra Weight Heading', weight: 'extra' })}
    </div>
  `,
};

export const AllAlignments = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-5);">
      ${component({ level: 'h2', text: 'Left Aligned Heading', align: 'left' })}
      ${component({ level: 'h2', text: 'Center Aligned Heading', align: 'center' })}
      ${component({ level: 'h2', text: 'Right Aligned Heading', align: 'right' })}
    </div>
  `,
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-10);">
      <section>
        <h3 style="margin: 0 0 var(--size-5) 0; font-size: var(--font-size-3); font-weight: var(--font-weight-600); color: var(--gray-700);">Page Structure Example</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-4);">
          ${component({ level: 'h1', text: 'Real Estate Services', color: 'primary' })}
          ${component({ level: 'h2', text: 'Our Properties' })}
          ${component({ level: 'h3', text: 'Luxury Apartments', weight: 'regular' })}
          ${component({ level: 'h4', text: 'Property Features' })}
        </div>
      </section>
      
      <section>
        <h3 style="margin: 0 0 var(--size-5) 0; font-size: var(--font-size-3); font-weight: var(--font-weight-600); color: var(--gray-700);">Semantic Status Messages</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-4);">
          ${component({ level: 'h3', text: 'Operation Successful', color: 'success' })}
          ${component({ level: 'h3', text: 'Important Notice', color: 'warning' })}
          ${component({ level: 'h3', text: 'Critical Error', color: 'danger' })}
          ${component({ level: 'h3', text: 'Information', color: 'info' })}
        </div>
      </section>
      
      <section>
        <h3 style="margin: 0 0 var(--size-5) 0; font-size: var(--font-size-3); font-weight: var(--font-weight-600); color: var(--gray-700);">Combined Variants</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-4);">
          ${component({ level: 'h2', text: 'Primary Bold Title', color: 'primary', weight: 'bold' })}
          ${component({ level: 'h2', text: 'Secondary Light Title', color: 'secondary', weight: 'light' })}
          ${component({ level: 'h2', text: 'Centered Success Title', color: 'success', align: 'center' })}
          ${component({ level: 'h3', text: 'Extra Weight Info Title', color: 'info', weight: 'extra' })}
        </div>
      </section>
    </div>
  `,
};
