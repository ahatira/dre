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
          'Semantic typographic headings (h1-h6) with consistent styling and alignment options. Use proper hierarchy for SEO and accessibility.',
      },
    },
  },
  argTypes: {
    text: {
      control: 'text',
      description: 'Heading text content',
      table: {
        category: 'content',
        type: { summary: 'string', required: true },
      },
    },
    level: {
      control: { type: 'select' },
      options: ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
      description: 'Semantic heading level',
      table: {
        category: 'structure',
        defaultValue: { summary: 'h2' },
      },
    },
    align: {
      control: { type: 'inline-radio' },
      options: ['left', 'center', 'right'],
      description: 'Text alignment',
      table: {
        category: 'appearance',
        defaultValue: { summary: 'left' },
      },
    },
    visuallyHidden: {
      control: 'boolean',
      description: 'Hide visually but keep for screen readers',
      table: {
        category: 'accessibility',
        defaultValue: { summary: false },
      },
    },
  },
};

// Heading levels
export const H1 = {
  args: { level: 'h1', text: 'H1 - Main Page Title (48px)' },
};

export const H2 = {
  args: { level: 'h2', text: 'H2 - Section Title (36px)' },
};

export const H3 = {
  args: { level: 'h3', text: 'H3 - Subsection Title (28px)' },
};

export const H4 = {
  args: { level: 'h4', text: 'H4 - Content Block Title (24px)' },
};

export const H5 = {
  args: { level: 'h5', text: 'H5 - Small Heading (20px)' },
};

export const H6 = {
  args: { level: 'h6', text: 'H6 - Micro Title (16px uppercase)' },
};

// Alignment variations
export const AlignmentVariants = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-6);">
      ${component({ level: 'h2', text: 'Left Aligned (default)', align: 'left' })}
      ${component({ level: 'h2', text: 'Center Aligned', align: 'center' })}
      ${component({ level: 'h2', text: 'Right Aligned', align: 'right' })}
    </div>
  `,
};

// Complete hierarchy showcase
export const CompleteHierarchy = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-4);">
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
    docs: {
      description: {
        story: 'This heading is hidden visually but accessible to screen readers for better navigation structure.',
      },
    },
  },
};

// Real-world examples
export const RealWorldExamples = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-8);">
      ${component({ level: 'h1', text: 'Nos biens immobiliers', align: 'center' })}
      <p style="text-align:center; margin-top: calc(var(--size-6) * -1); color: var(--gray-600);">
        Découvrez notre sélection de propriétés d'exception
      </p>
      
      ${component({ level: 'h2', text: 'Bureaux à Paris' })}
      <p style="color: var(--gray-700);">
        Large choix d'espaces professionnels dans les meilleurs quartiers de la capitale.
      </p>
      
      ${component({ level: 'h3', text: 'Quartier La Défense' })}
      <p style="color: var(--gray-700);">
        Bureaux modernes avec vue panoramique.
      </p>
    </div>
  `,
};
