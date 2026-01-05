import diagnosticTwig from './diagnostic.twig';
import diagnosticData from './diagnostic.yml';
import './diagnostic.css';

export default {
  title: 'Components/Diagnostic',
  tags: ['autodocs'],
  render: (args) => diagnosticTwig(args),
  argTypes: {
    label: {
      description: 'Title displayed above the bar',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: diagnosticData.label },
      },
    },
    icon: {
      description: 'Icon used in the title (without icon- prefix)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: diagnosticData.icon },
      },
    },
    unit: {
      description: 'Unit appended to the value in the legend',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: diagnosticData.unit },
      },
    },
    value: {
      description: 'Numeric value displayed when class is available',
      control: { type: 'number' },
      table: {
        category: 'Data',
        type: { summary: 'number|null' },
        defaultValue: { summary: diagnosticData.value },
      },
    },
    valid_from: {
      description: 'Validity start date',
      control: { type: 'text' },
      table: {
        category: 'Meta',
        type: { summary: 'string|null' },
        defaultValue: { summary: 'null' },
      },
    },
    valid_to: {
      description: 'Validity end date',
      control: { type: 'text' },
      table: {
        category: 'Meta',
        type: { summary: 'string|null' },
        defaultValue: { summary: 'null' },
      },
    },
    classes: {
      description: 'Map of classes (a-g) with label, color, range_max',
      control: { type: 'object' },
      table: {
        category: 'Data',
        type: { summary: 'Record<string, {label, color, range_max}>' },
        defaultValue: { summary: '7 energy classes' },
      },
    },
    is_dimmed: {
      description: 'Dim bar opacity for empty state',
      control: { type: 'boolean' },
      table: {
        category: 'State',
        type: { summary: 'boolean' },
        defaultValue: { summary: diagnosticData.is_dimmed },
      },
    },
    dim_opacity: {
      description: 'Opacity percentage (10-90) when dimmed',
      control: { type: 'number' },
      table: {
        category: 'State',
        type: { summary: 'number' },
        defaultValue: { summary: diagnosticData.dim_opacity },
      },
    },
    empty_message: {
      description: 'Message shown when no value or dimmed',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: diagnosticData.empty_message },
      },
    },
  },
};

export const Default = {
  name: 'Default (Class D)',
  args: diagnosticData,
};

export const UnknownClass = {
  name: 'Unknown / Not Provided',
  args: {
    ...diagnosticData,
    value: null,
  },
};

export const ClassB = {
  name: 'Class B with Value',
  args: {
    ...diagnosticData,
    value: 85,
  },
};

export const Dimmed = {
  name: 'Dimmed State',
  args: {
    ...diagnosticData,
    value: null,
    is_dimmed: true,
  },
};

export const MissingValueMessage = {
  name: 'Missing Value with Message',
  args: {
    ...diagnosticData,
    value: null,
    is_dimmed: false,
  },
};
