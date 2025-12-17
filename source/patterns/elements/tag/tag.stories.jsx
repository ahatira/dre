import markup from './tag.twig';
import data from './tag.yml';

const settings = {
  title: 'Elements/Tag',
  tags: ['autodocs'],
  argTypes: {
    label: {
      description: 'Tag text content',
      control: 'text',
      table: { category: 'Content' },
    },
    variant: {
      description: 'Style variant: filled (solid background) or outline (border only)',
      control: 'select',
      options: ['filled', 'outline'],
      table: { category: 'Variants', defaultValue: { summary: 'filled' } },
    },
    color: {
      description:
        'Color theme (semantic) - Only affects filled variant. Outline is always black per maquette.',
      control: 'select',
      options: ['neutral', 'primary', 'secondary', 'success', 'danger', 'warning', 'info', 'gold'],
      table: { category: 'Variants', defaultValue: { summary: 'neutral' } },
    },
    size: {
      description: 'Tag size',
      control: 'select',
      options: ['sm', 'md', 'lg'],
      table: { category: 'Variants', defaultValue: { summary: 'md' } },
    },
    removable: {
      description: 'Show close icon (X) for removal',
      control: 'boolean',
      table: { category: 'Options', defaultValue: { summary: 'false' } },
    },
    iconStart: {
      description: 'Position close icon at start (for search input tags)',
      control: 'boolean',
      table: { category: 'Options', defaultValue: { summary: 'false' } },
    },
    selected: {
      description: 'Selected/active state',
      control: 'boolean',
      table: { category: 'States', defaultValue: { summary: 'false' } },
    },
    url: {
      description: 'Optional link URL (renders as <a> instead of <button>)',
      control: 'text',
      table: { category: 'Options' },
    },
  },
  parameters: {
    docs: {
      description: {
        component:
          'Tag atom - Interactive chip/badge for filtering, categorization, and selection. Can be used standalone or composed into Tag List. Per maquette: Filled variant uses semantic colors with white text/icon, Outline variant always uses black border and text regardless of color parameter.',
      },
    },
  },
};

export default settings;

// ============================================
// DEFAULT STORY
// ============================================
export const Default = {
  render: (args) => markup(args),
  args: data,
};

// ============================================
// STYLE VARIANTS (per maquette)
// ============================================
export const StyleVariants = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 2rem;">
      <div>
        <h3 style="margin-bottom: 0.5rem; font-size: 0.875rem; color: #6b7280;">Filled (Solid background - per maquette left box)</h3>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
          ${markup({ label: 'TAG LABEL', variant: 'filled', color: 'primary', removable: false })}
          ${markup({ label: 'TAG LABEL', variant: 'filled', color: 'primary', removable: true })}
        </div>
      </div>
      <div>
        <h3 style="margin-bottom: 0.5rem; font-size: 0.875rem; color: #6b7280;">Outline (Black border only - per maquette right box)</h3>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
          ${markup({ label: 'Coworking to let', variant: 'outline', removable: true, iconStart: true })}
          ${markup({ label: 'Coworking to let', variant: 'outline', removable: true, iconStart: false })}
        </div>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Two style variants per maquette: Filled (colored background + white text) and Outline (white background + black border + black text). Color parameter only affects filled variant.',
      },
    },
  },
};

// ============================================
// COLOR VARIANTS (filled)
// ============================================
export const ColorsFilled = {
  render: () => `
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
      ${['neutral', 'primary', 'secondary', 'success', 'danger', 'warning', 'info', 'gold']
        .map((color) => markup({ label: color, variant: 'filled', color, removable: true }))
        .join('')}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story: 'All semantic color variants with filled style (solid background).',
      },
    },
  },
};

// ============================================
// OUTLINE VARIANT (always black per maquette)
// ============================================
export const ColorsOutline = {
  render: () => `
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
      ${['Paris 8e', 'Bureaux', 'Coworking to let', 'Disponible', 'Climatisation']
        .map((label) => markup({ label, variant: 'outline', removable: true }))
        .join('')}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Outline variant always uses black border and text per maquette (right box). Color parameter has no effect on outline style.',
      },
    },
  },
};

// ============================================
// SIZE VARIANTS
// ============================================
export const Sizes = {
  render: () => `
    <div style="display: flex; align-items: center; gap: 1rem;">
      ${markup({ label: 'Small', size: 'sm', variant: 'filled', color: 'primary', removable: true })}
      ${markup({ label: 'Medium', size: 'md', variant: 'filled', color: 'primary', removable: true })}
      ${markup({ label: 'Large', size: 'lg', variant: 'filled', color: 'primary', removable: true })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story: 'Three size variants: sm (compact), md (default), lg (prominent).',
      },
    },
  },
};

// ============================================
// REMOVABLE (icon positions)
// ============================================
export const RemovableVariants = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
      <div>
        <h3 style="margin-bottom: 0.5rem; font-size: 0.875rem; color: #6b7280;">Icon at end (default - per maquette)</h3>
        <div style="display: flex; gap: 0.5rem;">
          ${markup({ label: 'Paris 8e', variant: 'filled', color: 'primary', removable: true, iconStart: false })}
          ${markup({ label: 'Bureaux', variant: 'outline', color: 'neutral', removable: true, iconStart: false })}
        </div>
      </div>
      <div>
        <h3 style="margin-bottom: 0.5rem; font-size: 0.875rem; color: #6b7280;">Icon at start (search input variant - per maquette top)</h3>
        <div style="display: flex; gap: 0.5rem;">
          ${markup({ label: 'Madrid', variant: 'outline', color: 'neutral', removable: true, iconStart: true })}
          ${markup({ label: 'Coworking to let', variant: 'outline', color: 'neutral', removable: true, iconStart: true })}
        </div>
      </div>
      <div>
        <h3 style="margin-bottom: 0.5rem; font-size: 0.875rem; color: #6b7280;">Not removable</h3>
        <div style="display: flex; gap: 0.5rem;">
          ${markup({ label: 'Static Tag', variant: 'filled', color: 'neutral', removable: false })}
          ${markup({ label: 'Category', variant: 'outline', color: 'primary', removable: false })}
        </div>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Removable tags with close icon. Icon can be positioned at end (default) or start (search input variant per maquette).',
      },
    },
  },
};

// ============================================
// SELECTED STATE
// ============================================
export const SelectedState = {
  render: () => `
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
      ${markup({ label: 'Not selected', variant: 'filled', color: 'primary', selected: false, removable: true })}
      ${markup({ label: 'Selected', variant: 'filled', color: 'primary', selected: true, removable: true })}
      ${markup({ label: 'Not selected', variant: 'outline', color: 'primary', selected: false, removable: true })}
      ${markup({ label: 'Selected', variant: 'outline', color: 'primary', selected: true, removable: true })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story: 'Selected state for filter tags (darker background, bolder text).',
      },
    },
  },
};

// ============================================
// AS LINK
// ============================================
export const AsLink = {
  render: () => `
    <div style="display: flex; gap: 0.5rem;">
      ${markup({ label: 'Bureaux', variant: 'filled', color: 'primary', url: '/bureaux' })}
      ${markup({ label: 'Commerces', variant: 'outline', color: 'neutral', url: '/commerces', removable: true })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story: 'Tags as clickable links (<a>) instead of buttons. Useful for category navigation.',
      },
    },
  },
};
