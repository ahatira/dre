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
          'Semantic heading component (h1–h6) with token-based typography, colors, weights, and alignment. Supports optional icon and visually hidden mode for accessibility.',
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
    icon: {
      control: 'text',
      description: 'Optional icon class (e.g., icon-pin-map, icon-calendar)',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '' },
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
    iconPosition: {
      control: { type: 'inline-radio' },
      options: ['left', 'right'],
      description: 'Icon position relative to text',
      table: {
        category: 'Appearance',
        type: { summary: 'left | right' },
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
    <div style="display:flex; flex-direction:column; gap: var(--size-3);">
      ${component({ level: 'h1', text: 'H1 - Main Page Title (48px)' })}
      ${component({ level: 'h2', text: 'H2 - Section Title (36px)' })}
      ${component({ level: 'h3', text: 'H3 - Subsection Title (28px)' })}
      ${component({ level: 'h4', text: 'H4 - Content Block Title (24px)' })}
      ${component({ level: 'h5', text: 'H5 - Small Heading (20px)' })}
      ${component({ level: 'h6', text: 'H6 - Micro Title (16px)' })}
    </div>
  `,
};

export const AllColors = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-4);">
      ${component({ text: 'Default Heading (gray)', color: 'default' })}
      ${component({ text: 'Primary Heading (green)', color: 'primary' })}
      ${component({ text: 'Secondary Heading (purple)', color: 'secondary' })}
      ${component({ text: 'Success Heading', color: 'success' })}
      ${component({ text: 'Warning Heading', color: 'warning' })}
      ${component({ text: 'Danger Heading', color: 'danger' })}
      ${component({ text: 'Info Heading', color: 'info' })}
    </div>
  `,
};

export const AllWeights = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-3);">
      ${component({ text: 'Light Weight (300)', weight: 'light', level: 'h2' })}
      ${component({ text: 'Regular Weight (400)', weight: 'regular', level: 'h2' })}
      ${component({ text: 'Bold Weight (700 - default)', weight: 'bold', level: 'h2' })}
      ${component({ text: 'Extra Weight (800)', weight: 'extra', level: 'h2' })}
    </div>
  `,
};

export const AllAlignments = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-4);">
      ${component({ text: 'Left Aligned (default)', align: 'left', level: 'h2' })}
      ${component({ text: 'Center Aligned', align: 'center', level: 'h2' })}
      ${component({ text: 'Right Aligned', align: 'right', level: 'h2' })}
    </div>
  `,
};
export const WithIcons = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-4);">
      ${component({ level: 'h2', text: 'Location', icon: 'icon-pin-map' })}
      ${component({ level: 'h3', text: 'Upcoming Events', icon: 'icon-calendar' })}
      ${component({ level: 'h4', text: 'View Details', icon: 'icon-arrow-right', iconPosition: 'right' })}
      ${component({ level: 'h2', text: 'Our Offices', icon: 'icon-offices', align: 'center' })}
    </div>
  `,
};

export const UseCases = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Page Structure</h3>
        ${component({ level: 'h1', text: 'Main Page Title', color: 'primary' })}
        ${component({ level: 'h2', text: 'Section Heading' })}
        ${component({ level: 'h3', text: 'Subsection Title', weight: 'regular' })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">With Icons</h3>
        ${component({ level: 'h2', text: 'Property Search', icon: 'icon-search', color: 'primary' })}
        ${component({ level: 'h3', text: 'Contact Information', icon: 'icon-phone' })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Semantic Colors</h3>
        ${component({ level: 'h3', text: 'Success Message', color: 'success', icon: 'icon-check' })}
        ${component({ level: 'h3', text: 'Warning Notice', color: 'warning', icon: 'icon-infos' })}
      </div>
    </div>
  `,
};
