/**
 * Diagnostics Collection (Organism)
 *
 * Section displaying multiple diagnostic indicators (energy, emissions, etc.)
 * with optional labels/images (LEED badges, etc.) at the bottom.
 * Composes Diagnostic components in a grid layout.
 */

import iconsRegistry from '../../documentation/icons-registry.json';
import diagnosticsTemplate from './diagnostics.twig';
import data from './diagnostics.yml';

const settings = {
  title: 'Collections/Diagnostics',
  tags: ['autodocs'],
  parameters: {
    layout: 'padded',
    docs: {
      description: {
        component:
          'Organism composing multiple Diagnostic components in a grid. Displays energy and emissions indicators with optional certification labels. Supports dimmed state for unavailable data.',
      },
    },
  },
  render: (args) => diagnosticsTemplate(args),
  args: data,
  argTypes: {
    title: {
      description: 'Section title (e.g., "Energy")',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: data.title },
      },
    },
    icon: {
      control: 'select',
      options: iconsRegistry.names,
      description: 'Icon name for data-icon attribute (without icon- prefix)',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: data.icon },
      },
    },
    diagnostics: {
      description: 'Array of diagnostic data objects (each with label, icon, unit, value, classes, etc.)',
      table: {
        category: 'Content',
        type: { summary: 'array' },
        defaultValue: { summary: '2 diagnostics (energy + emissions)' },
      },
    },
    labels: {
      description: 'Array of label/image objects (src, alt, optional url for certification badges)',
      table: {
        category: 'Content',
        type: { summary: 'array' },
        defaultValue: { summary: 'LEED Platinum badge' },
      },
    },
  },
};

export default settings;

export const Default = {
  name: 'Default',
  args: data,
};

export const WithoutLabels = {
  name: 'Without Labels',
  args: {
    ...data,
    labels: [],
  },
};

export const SingleDiagnostic = {
  name: 'Single Diagnostic',
  args: {
    ...data,
    diagnostics: [data.diagnostics[0]],
  },
};

export const BothDimmed = {
  name: 'Both Dimmed (No Data)',
  args: {
    ...data,
    diagnostics: data.diagnostics.map(diag => ({
      ...diag,
      value: null,
      is_dimmed: true,
    })),
  },
};
