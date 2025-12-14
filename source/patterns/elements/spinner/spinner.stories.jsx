import spinnerTwig from './spinner.twig';
import data from './spinner.yml';

export default {
  title: 'Elements/Spinner',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Animated loading indicator for asynchronous states.
Supports 3 animation variants (circular, dots, bars), 3 sizes, 9 semantic colors, centering, and screen-reader text.`,
      },
    },
  },
  argTypes: {
    // Appearance
    variant: {
      description:
        'Spinner animation type (circular: rotating SVG, dots: bouncing, bars: stretching)',
      control: { type: 'select' },
      options: ['circular', 'dots', 'bars'],
      table: {
        category: 'Appearance',
        type: { summary: 'circular | dots | bars' },
        defaultValue: { summary: 'circular' },
      },
    },
    size: {
      description: 'Spinner size (small: 24px, medium: 32px, large: 48px)',
      control: { type: 'select' },
      options: ['small', 'medium', 'large'],
      table: {
        category: 'Appearance',
        type: { summary: 'small | medium | large' },
        defaultValue: { summary: 'medium' },
      },
    },
    color: {
      description:
        'Spinner semantic color (neutral: gray default, primary: BNP green, secondary: pink, gold, success, info, warning, danger, light, dark)',
      control: { type: 'select' },
      options: [
        'neutral',
        'primary',
        'secondary',
        'gold',
        'success',
        'info',
        'warning',
        'danger',
        'light',
        'dark',
      ],
      table: {
        category: 'Appearance',
        type: {
          summary:
            'neutral | primary | secondary | gold | success | info | warning | danger | light | dark',
        },
        defaultValue: { summary: 'neutral' },
      },
    },
    // Layout
    centered: {
      description: 'Center spinner in parent container (parent must have position: relative)',
      control: { type: 'boolean' },
      table: {
        category: 'Layout',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    // Accessibility
    text: {
      description:
        'Screen reader text announced to users (e.g., "Loading content...", "Processing data...")',
      control: { type: 'text' },
      table: {
        category: 'Accessibility',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'Loading...' },
      },
    },
  },
  args: data,
};

export const Default = {
  render: (args) => spinnerTwig(args),
  args: data,
};

// === Grouped Showcases ===

export const AllVariants = {
  render: () => `
    <div style="display: flex; gap: 3rem; align-items: center; padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <div style="text-align: center;">
        <div style="margin-bottom: 1rem;">${spinnerTwig({ variant: 'circular', size: 'lg' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 14px; color: var(--gray-700);">Circular</p>
        <p style="margin: 0.25rem 0 0; font-size: 12px; color: var(--gray-500);">Rotating SVG</p>
      </div>
      <div style="text-align: center;">
        <div style="margin-bottom: 1rem;">${spinnerTwig({ variant: 'dots', size: 'lg' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 14px; color: var(--gray-700);">Dots</p>
        <p style="margin: 0.25rem 0 0; font-size: 12px; color: var(--gray-500);">3 bouncing dots</p>
      </div>
      <div style="text-align: center;">
        <div style="margin-bottom: 1rem;">${spinnerTwig({ variant: 'bars', size: 'lg' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 14px; color: var(--gray-700);">Bars</p>
        <p style="margin: 0.25rem 0 0; font-size: 12px; color: var(--gray-500);">3 stretching bars</p>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'The three spinner variants available. Circular is recommended by default, Dots for a more subtle effect, and Bars for a visual alternative.',
      },
    },
  },
};

export const Sizes = {
  render: () => `
    <div style="display: flex; gap: 3rem; align-items: flex-end; padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ size: 'small' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 12px; color: var(--gray-700);">Small</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">24px</p>
      </div>
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ size: 'medium' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 12px; color: var(--gray-700);">Medium</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">32px · Default</p>
      </div>
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ size: 'large' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 12px; color: var(--gray-700);">Large</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">48px</p>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          '3 sizes available: Small (24px) for inline/button use, Medium (32px) default for general loading, Large (48px) for centered areas and page loading.',
      },
    },
  },
};

export const SemanticColors = {
  render: () => `
    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1.5rem; padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <div style="text-align: center; padding: 1rem; background: white; border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'neutral' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Neutral</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">Default gray</p>
      </div>
      <div style="text-align: center; padding: 1rem; background: white; border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'primary' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Primary</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">BNP green</p>
      </div>
      <div style="text-align: center; padding: 1rem; background: white; border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'secondary' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Secondary</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">Pink accent</p>
      </div>
      <div style="text-align: center; padding: 1rem; background: white; border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'gold' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Gold</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">Premium</p>
      </div>
      <div style="text-align: center; padding: 1rem; background: white; border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'success' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Success</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">Green success</p>
      </div>
      <div style="text-align: center; padding: 1rem; background: white; border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'info' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Info</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">Blue info</p>
      </div>
      <div style="text-align: center; padding: 1rem; background: white; border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'warning' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Warning</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">Yellow alert</p>
      </div>
      <div style="text-align: center; padding: 1rem; background: white; border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'danger' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Danger</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">Red error</p>
      </div>
      <div style="text-align: center; padding: 1rem; background: white; border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'light' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Light</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">Neutral light</p>
      </div>
      <div style="text-align: center; padding: 1rem; background: var(--gray-800); border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'dark' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: white;">Dark</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-400);">Neutral dark</p>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          '9 semantic color variants: Neutral (default gray), Primary (BNP green), Secondary (pink), Gold (premium), Success/Info/Warning/Danger for contextual states, Light/Dark for backgrounds.',
      },
    },
  },
};

export const Centered = {
  render: () => `
    <div style="position: relative; height: 200px; border: 2px dashed var(--gray-300); border-radius: var(--radius-2); background: var(--gray-50);">
      <p style="margin: 1rem; font-size: 14px; color: var(--gray-600); font-weight: 500;">The spinner is centered vertically and horizontally in this container</p>
      ${spinnerTwig({ centered: true, size: 'large', color: 'primary' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'The `centered: true` modifier positions the spinner absolutely with transform translate for perfect centering. The parent container must have `position: relative`.',
      },
    },
  },
};

export const LoadingButtons = {
  render: () => `
    <div style="display: flex; gap: 1rem; flex-wrap: wrap; padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <button class="ps-button ps-button--primary" disabled style="display: inline-flex; align-items: center; gap: var(--size-2);">
        ${spinnerTwig({ size: 'small', color: 'light' })}
        Saving...
      </button>
      <button class="ps-button ps-button--secondary" disabled style="display: inline-flex; align-items: center; gap: var(--size-2);">
        ${spinnerTwig({ size: 'small', color: 'light' })}
        Processing...
      </button>
      <button class="ps-button ps-button--success" disabled style="display: inline-flex; align-items: center; gap: var(--size-2);">
        ${spinnerTwig({ size: 'small', color: 'light' })}
        Validating...
      </button>
      <button class="ps-button ps-button--danger" disabled style="display: inline-flex; align-items: center; gap: var(--size-2);">
        ${spinnerTwig({ size: 'small', color: 'light' })}
        Deleting...
      </button>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Loading buttons use Small size with Light color for visibility on colored backgrounds. Buttons are disabled during loading.',
      },
    },
  },
};

export const PageLoading = {
  render: () => `
    <div style="position: relative; height: 240px; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200);">
      ${spinnerTwig({ centered: true, size: 'large', color: 'primary', text: 'Loading page content...' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Page loading uses Large size with Primary color, centered in container. Ideal for full-page or card content loading.',
      },
    },
  },
};
