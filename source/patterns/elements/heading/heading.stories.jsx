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
          'Semantic typographic headings (h1–h6) with color and weight variants. Minimal markup: base <h1> by default.',
      },
    },
  },
  argTypes: {
    text: {
      control: 'text',
      description: 'Heading text content',
      table: { category: 'content', type: { summary: 'string', required: true } },
    },
    level: {
      control: { type: 'select' },
      options: ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
      description: 'Semantic heading level',
      table: { category: 'structure', defaultValue: { summary: 'h1' } },
    },
    align: {
      control: { type: 'inline-radio' },
      options: ['left', 'center', 'right'],
      description: 'Text alignment',
      table: { category: 'appearance', defaultValue: { summary: 'left' } },
    },
    color: {
      control: { type: 'select' },
      options: ['default', 'primary', 'secondary', 'success', 'warning', 'danger', 'info'],
      description: 'Semantic color variant',
      table: { category: 'appearance', defaultValue: { summary: 'default' } },
    },
    weight: {
      control: { type: 'select' },
      options: ['light', 'regular', 'bold', 'extra'],
      description: 'Font weight variant',
      table: { category: 'appearance', defaultValue: { summary: 'bold' } },
    },
    visuallyHidden: {
      control: 'boolean',
      description: 'Hide visually but keep for screen readers',
      table: { category: 'accessibility', defaultValue: { summary: false } },
    },
    icon: {
      control: 'text',
      description: 'Icon class (e.g., icon-pin-map)',
      table: { category: 'content', type: { summary: 'string' } },
    },
    iconPosition: {
      control: { type: 'inline-radio' },
      options: ['left', 'right'],
      description: 'Icon position relative to text',
      table: { category: 'appearance', defaultValue: { summary: 'left' } },
    },
  },
};

// Heading levels (base is h1)
export const H1 = { args: { level: 'h1', text: 'H1 - Main Page Title' } };
export const H2 = { args: { level: 'h2', text: 'H2 - Section Title' } };
export const H3 = { args: { level: 'h3', text: 'H3 - Subsection Title' } };
export const H4 = { args: { level: 'h4', text: 'H4 - Content Block Title' } };
export const H5 = { args: { level: 'h5', text: 'H5 - Small Heading' } };
export const H6 = { args: { level: 'h6', text: 'H6 - Micro Title' } };

// Color variants
export const ColorVariants = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-4);">
      ${component({ text: 'Primary Heading', color: 'primary' })}
      ${component({ text: 'Secondary Heading', color: 'secondary' })}
      ${component({ text: 'Success Heading', color: 'success' })}
      ${component({ text: 'Warning Heading', color: 'warning' })}
      ${component({ text: 'Danger Heading', color: 'danger' })}
      ${component({ text: 'Info Heading', color: 'info' })}
    </div>
  `,
};

// Weight variants
export const WeightVariants = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-3);">
      ${component({ text: 'Light Weight', weight: 'light' })}
      ${component({ text: 'Regular Weight', weight: 'regular' })}
      ${component({ text: 'Bold Weight (default)', weight: 'bold' })}
      ${component({ text: 'Extra Weight', weight: 'extra' })}
    </div>
  `,
};

// Alignment variations
export const AlignmentVariants = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-4);">
      ${component({ text: 'Left Aligned (default)', align: 'left' })}
      ${component({ text: 'Center Aligned', align: 'center' })}
      ${component({ text: 'Right Aligned', align: 'right' })}
    </div>
  `,
};

// Complete hierarchy showcase
export const CompleteHierarchy = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-3);">
      ${component({ level: 'h1', text: 'H1 - Main Page Title' })}
      ${component({ level: 'h2', text: 'H2 - Section Title' })}
      ${component({ level: 'h3', text: 'H3 - Subsection Title' })}
      ${component({ level: 'h4', text: 'H4 - Content Block Title' })}
      ${component({ level: 'h5', text: 'H5 - Small Heading' })}
      ${component({ level: 'h6', text: 'H6 - Micro Title' })}
    </div>
  `,
};

// Visually hidden (accessibility)
export const VisuallyHidden = {
  args: { level: 'h2', text: 'Screen reader only heading', visuallyHidden: true },
  parameters: {
    docs: { description: { story: 'Hidden visually but available to assistive tech.' } },
  },
};

// With icons
export const WithIcons = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-4);">
      ${component({ level: 'h2', text: 'Localisation', icon: 'icon-pin-map' })}
      ${component({ level: 'h3', text: 'Événements à venir', icon: 'icon-calendar' })}
      ${component({ level: 'h4', text: 'Voir les détails', icon: 'icon-arrow-right', iconPosition: 'right' })}
      ${component({ level: 'h2', text: 'Nos bureaux', icon: 'icon-offices', align: 'center' })}
    </div>
  `,
};

// All variants combined showcase
export const AllVariants = {
  render: () => `
    <div style="display:grid; gap: var(--size-6);">
      <div>${component({ text: 'Primary Bold H1', color: 'primary', weight: 'bold', level: 'h1' })}</div>
      <div>${component({ text: 'Secondary Regular H2', color: 'secondary', weight: 'regular', level: 'h2' })}</div>
      <div>${component({ text: 'Success Light H3', color: 'success', weight: 'light', level: 'h3' })}</div>
      <div>${component({ text: 'Warning Extra H4', color: 'warning', weight: 'extra', level: 'h4' })}</div>
      <div>${component({ text: 'Danger Bold H5', color: 'danger', weight: 'bold', level: 'h5' })}</div>
      <div>${component({ text: 'Info Regular H6', color: 'info', weight: 'regular', level: 'h6' })}</div>
    </div>
  `,
};
