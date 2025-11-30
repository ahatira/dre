import spinnerTwig from './spinner.twig';
import data from './spinner.yml';

export default {
  title: 'Elements/Spinner',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Animated loading indicator for asynchronous states.

**Key Features:**
- 3 variants: circular (rotating SVG, default), dots (3 bouncing dots), bars (3 stretching bars)
- 5 sizes: xs (16px), sm (24px), md (32px, default), lg (48px), xl (64px)
- 8 colors: default (gray), primary (BNP green), secondary (pink), success, info, warning, danger, white (for dark backgrounds)
- Accessibility: role="status" and aria-live="polite" for screen reader announcements
- Centered option: absolute positioning with transform for perfect centering

**Usage:**
- Use circular for general loading states
- Use dots for subtle, less intrusive loading
- Use bars for alternative visual effect
- Size xs for buttons, sm/md for inline content, lg/xl for page loading
- Always provide descriptive text for screen readers
- Use semantic colors for contextual feedback

**Accessibility:**
- role="status" announces loading state to screen readers
- aria-live="polite" ensures non-intrusive announcements
- Visually hidden text provides context for screen reader users
- No user interaction required (not focusable)`,
      },
    },
  },
  argTypes: {
    // Appearance
    variant: {
      description: 'Spinner animation type (circular: rotating SVG, dots: bouncing, bars: stretching)',
      control: { type: 'select' },
      options: ['circular', 'dots', 'bars'],
      table: {
        category: 'Appearance',
        type: { summary: 'circular | dots | bars' },
        defaultValue: { summary: 'circular' },
      },
    },
    size: {
      description: 'Spinner size (xs: 16px, sm: 24px, md: 32px, lg: 48px, xl: 64px)',
      control: { type: 'select' },
      options: ['xs', 'sm', 'md', 'lg', 'xl'],
      table: {
        category: 'Appearance',
        type: { summary: 'xs | sm | md | lg | xl' },
        defaultValue: { summary: 'md' },
      },
    },
    color: {
      description: 'Spinner color (semantic colors from design tokens, use white for dark backgrounds)',
      control: { type: 'select' },
      options: ['default', 'primary', 'secondary', 'success', 'info', 'warning', 'danger', 'white'],
      table: {
        category: 'Appearance',
        type: { summary: 'default | primary | secondary | success | info | warning | danger | white' },
        defaultValue: { summary: 'default' },
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
      description: 'Screen reader text announced to users (e.g., "Loading content...", "Processing data...")',
      control: { type: 'text' },
      table: {
        category: 'Accessibility',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'Loading...' },
      },
    },
  },
  args: { ...data },
};

export const Default = {
  render: (args) => spinnerTwig(args),
  args: { ...data },
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

export const AllSizes = {
  render: () => `
    <div style="display: flex; gap: 3rem; align-items: flex-end; padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ size: 'xs' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 12px; color: var(--gray-700);">XS</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">16px</p>
      </div>
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ size: 'sm' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 12px; color: var(--gray-700);">SM</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">24px</p>
      </div>
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ size: 'md' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 12px; color: var(--gray-700);">MD</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">32px · Default</p>
      </div>
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ size: 'lg' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 12px; color: var(--gray-700);">LG</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">48px</p>
      </div>
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ size: 'xl' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 12px; color: var(--gray-700);">XL</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">64px</p>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          '5 sizes available: XS (16px) for buttons, SM (24px) for inline, MD (32px) default, LG (48px) for centered areas, XL (64px) for full page loading.',
      },
    },
  },
};

export const AllColors = {
  render: () => `
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <div style="text-align: center; padding: 1rem; background: white; border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'default' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Default</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">Neutral gray</p>
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
      <div style="text-align: center; padding: 1rem; background: var(--gray-800); border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'white' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: white;">White</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-400);">For dark backgrounds</p>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'All semantic colors available: Default (neutral gray), Primary (BNP green), Secondary (pink accent), Success/Info/Warning/Danger for contextual states, and White for dark backgrounds.',
      },
    },
  },
};

export const Centered = {
  render: () => `
    <div style="position: relative; height: 200px; border: 2px dashed var(--gray-300); border-radius: var(--radius-2); background: var(--gray-50);">
      <p style="margin: 1rem; font-size: 14px; color: var(--gray-600); font-weight: 500;">The spinner is centered vertically and horizontally in this container</p>
      ${spinnerTwig({ centered: true, size: 'lg', color: 'primary' })}
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

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 2.5rem; padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      
      <!-- Buttons with different colors -->
      <div>
        <p style="margin: 0 0 1rem; font-weight: 600; font-size: 15px; color: var(--gray-800);">Loading buttons</p>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
          <button class="ps-button ps-button--primary" disabled style="display: inline-flex; align-items: center; gap: var(--size-2);">
            ${spinnerTwig({ size: 'xs', color: 'white' })}
            Saving...
          </button>
          <button class="ps-button ps-button--secondary" disabled style="display: inline-flex; align-items: center; gap: var(--size-2);">
            ${spinnerTwig({ size: 'xs', color: 'white' })}
            Processing...
          </button>
          <button class="ps-button ps-button--success" disabled style="display: inline-flex; align-items: center; gap: var(--size-2);">
            ${spinnerTwig({ size: 'xs', color: 'white' })}
            Validating...
          </button>
          <button class="ps-button ps-button--danger" disabled style="display: inline-flex; align-items: center; gap: var(--size-2);">
            ${spinnerTwig({ size: 'xs', color: 'white' })}
            Deleting...
          </button>
        </div>
      </div>

      <!-- Centered page loading -->
      <div>
        <p style="margin: 0 0 1rem; font-weight: 600; font-size: 15px; color: var(--gray-800);">Page loading</p>
        <div style="position: relative; height: 180px; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200);">
          ${spinnerTwig({ centered: true, size: 'xl', color: 'primary', text: 'Loading page content...' })}
        </div>
      </div>

      <!-- Inline with text -->
      <div>
        <p style="margin: 0 0 1rem; font-weight: 600; font-size: 15px; color: var(--gray-800);">Inline loading</p>
        <div style="display: flex; flex-direction: column; gap: 0.75rem; background: white; padding: 1.5rem; border-radius: var(--radius-2); border: 1px solid var(--gray-200);">
          <p style="display: flex; align-items: center; gap: var(--size-2); margin: 0; font-size: 14px;">
            ${spinnerTwig({ size: 'sm', color: 'default' })}
            Loading data...
          </p>
          <p style="display: flex; align-items: center; gap: var(--size-2); margin: 0; font-size: 14px;">
            ${spinnerTwig({ size: 'sm', color: 'info' })}
            Synchronizing...
          </p>
          <p style="display: flex; align-items: center; gap: var(--size-2); margin: 0; font-size: 14px;">
            ${spinnerTwig({ size: 'sm', color: 'warning', variant: 'dots' })}
            Processing files...
          </p>
        </div>
      </div>

      <!-- Semantic contexts -->
      <div>
        <p style="margin: 0 0 1rem; font-weight: 600; font-size: 15px; color: var(--gray-800);">Semantic contexts</p>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
          <div style="padding: 1.5rem; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200); text-align: center;">
            ${spinnerTwig({ variant: 'circular', size: 'md', color: 'success' })}
            <p style="margin: 0.75rem 0 0; font-size: 13px; color: var(--gray-700);">Validating...</p>
          </div>
          <div style="padding: 1.5rem; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200); text-align: center;">
            ${spinnerTwig({ variant: 'dots', size: 'md', color: 'info' })}
            <p style="margin: 0.75rem 0 0; font-size: 13px; color: var(--gray-700);">Information...</p>
          </div>
          <div style="padding: 1.5rem; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200); text-align: center;">
            ${spinnerTwig({ variant: 'bars', size: 'md', color: 'warning' })}
            <p style="margin: 0.75rem 0 0; font-size: 13px; color: var(--gray-700);">Warning...</p>
          </div>
          <div style="padding: 1.5rem; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200); text-align: center;">
            ${spinnerTwig({ variant: 'circular', size: 'md', color: 'danger' })}
            <p style="margin: 0.75rem 0 0; font-size: 13px; color: var(--gray-700);">Deleting...</p>
          </div>
        </div>
      </div>

      <!-- On dark background -->
      <div>
        <p style="margin: 0 0 1rem; font-weight: 600; font-size: 15px; color: var(--gray-800);">On dark background</p>
        <div style="padding: 2rem; background: var(--gray-800); border-radius: var(--radius-2); display: flex; gap: 2rem; align-items: center; justify-content: center;">
          ${spinnerTwig({ size: 'lg', color: 'white' })}
          <p style="color: white; margin: 0; font-size: 14px;">Loading...</p>
        </div>
      </div>

    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          "Real-world usage examples of the spinner in different contexts: loading buttons (xs size), centered pages (xl size), inline text (sm/md size), and semantic contexts (success, info, warning, danger).",
      },
    },
  },
};
