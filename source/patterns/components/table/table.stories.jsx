import markup from './table.twig';
import data from './table.yml';

const settings = {
  title: 'Components/Table',
  tags: ['autodocs'],
  argTypes: {
    caption: {
      description: 'Table caption for accessibility and context',
      control: 'text',
      table: { category: 'Content' },
    },
    headers: {
      description: 'Column headers array with metadata (key, label, sortable, numeric, sticky)',
      control: 'object',
      table: { category: 'Content' },
    },
    rows: {
      description: 'Data rows array with cells, id, selected, and disabled states',
      control: 'object',
      table: { category: 'Content' },
    },
    variant: {
      description: 'Color variant for table header and borders (semantic colors)',
      control: 'select',
      options: [
        'default',
        'neutral',
        'primary',
        'secondary',
        'success',
        'danger',
        'warning',
        'info',
        'light',
        'dark',
        'gold',
      ],
      table: { category: 'Variants', defaultValue: { summary: 'default' } },
    },
    striped: {
      description: 'Alternate row background colors for improved readability',
      control: 'boolean',
      table: { category: 'Variants', defaultValue: { summary: 'false' } },
    },
    hover: {
      description: 'Show hover effect on rows for better interactivity',
      control: 'boolean',
      table: { category: 'Variants', defaultValue: { summary: 'true' } },
    },
    bordered: {
      description: 'Add borders to all cells for clear separation',
      control: 'boolean',
      table: { category: 'Variants', defaultValue: { summary: 'false' } },
    },
    compact: {
      description: 'Reduce padding for denser data display',
      control: 'boolean',
      table: { category: 'Variants', defaultValue: { summary: 'false' } },
    },
    responsive: {
      description: 'Enable horizontal scroll on mobile (incompatible with stacked)',
      control: 'boolean',
      table: { category: 'Responsive', defaultValue: { summary: 'true' } },
    },
    stacked: {
      description: 'Transform into card-like layout on mobile (incompatible with responsive)',
      control: 'boolean',
      table: { category: 'Responsive', defaultValue: { summary: 'false' } },
    },
  },
  parameters: {
    docs: {
      description: {
        component:
          'Data table molecule for displaying structured real estate property information. Supports sorting, selection, color variants, zebra striping, hover effects, borders, compact spacing, and responsive layouts. Perfect for property listings, market data, and surface tables.',
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
// COLOR VARIANTS
// ============================================
export const ColorVariants = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 2rem;">
      ${['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'gold']
        .map(
          (variant) => `
        <div>
          <h3 style="margin-bottom: 0.5rem; font-size: 0.875rem; color: #6b7280;">Variant: ${variant}</h3>
          ${markup({ ...data, variant, caption: `Table with ${variant} variant` })}
        </div>
      `
        )
        .join('')}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Color variants override header background and border colors with semantic theme colors.',
      },
    },
  },
};

// ============================================
// LAYOUT VARIANTS
// ============================================
export const Striped = {
  render: (args) => markup(args),
  args: { ...data, striped: true, hover: false },
};

export const Bordered = {
  render: (args) => markup(args),
  args: { ...data, bordered: true },
};

export const Compact = {
  render: (args) => markup(args),
  args: { ...data, compact: true },
};

export const Combined = {
  render: (args) => markup(args),
  args: { ...data, striped: true, bordered: true, compact: true, variant: 'primary' },
};

// ============================================
// SORTABLE TABLE
// ============================================
export const Sortable = {
  render: (args) => markup(args),
  args: {
    caption: 'Q4 2024 - Statistiques immobilières régionales',
    headers: [
      { key: 'region', label: 'Région', sortable: true, numeric: false },
      { key: 'transactions', label: 'Transactions', sortable: true, numeric: true },
      { key: 'avgPrice', label: 'Prix moyen/m²', sortable: true, numeric: true },
      { key: 'growth', label: 'Croissance', sortable: true, numeric: true },
    ],
    rows: [
      { id: 'r1', cells: ['Île-de-France', '482', '€8,450', '+12.5%'] },
      { id: 'r2', cells: ['Auvergne-Rhône-Alpes', '287', '€6,200', '+8.2%'] },
      { id: 'r3', cells: ["Provence-Alpes-Côte d'Azur", '198', '€7,100', '+3.1%'] },
      { id: 'r4', cells: ['Hauts-de-France', '156', '€4,850', '-2.4%'] },
    ],
    striped: true,
    hover: true,
  },
  parameters: {
    docs: {
      description: {
        story:
          'Sortable table with numeric and text columns. Click headers to sort (JavaScript required).',
      },
    },
  },
};

// ============================================
// ROW STATES
// ============================================
export const RowStates = {
  render: (args) => markup(args),
  args: {
    caption: 'Table avec états de lignes',
    headers: data.headers,
    rows: [
      { ...data.rows[0], selected: false, disabled: false },
      { ...data.rows[1], selected: true, disabled: false },
      { ...data.rows[2], selected: false, disabled: true },
      { ...data.rows[3], selected: false, disabled: false },
    ],
    hover: true,
  },
  parameters: {
    docs: {
      description: {
        story: 'Demonstrates selected and disabled row states with visual feedback.',
      },
    },
  },
};

// ============================================
// RESPONSIVE VARIANTS
// ============================================
export const ResponsiveScroll = {
  render: (args) => markup(args),
  args: { ...data, responsive: true, stacked: false },
  parameters: {
    viewport: { defaultViewport: 'mobile1' },
    docs: {
      description: {
        story: 'Horizontal scroll on mobile to preserve tabular layout. Best for wide tables.',
      },
    },
  },
};

export const StackedMobile = {
  render: (args) => markup(args),
  args: { ...data, responsive: false, stacked: true },
  parameters: {
    viewport: { defaultViewport: 'mobile1' },
    docs: {
      description: {
        story: 'Card-like stacked layout on mobile with data labels. Best for narrow tables.',
      },
    },
  },
};

// ============================================
// EMPTY STATE
// ============================================
export const Empty = {
  render: (args) => markup(args),
  args: {
    caption: 'Aucune donnée disponible',
    headers: data.headers,
    rows: [],
  },
};
