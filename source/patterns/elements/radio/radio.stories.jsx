import radioTwig from './radio.twig';
import data from './radio.yml';

export default {
  title: 'Elements/Radio',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: 'Individual radio with label. Use within Radios group or standalone.',
      },
    },
  },
  argTypes: {
    name: {
      description: 'Input name attribute (group identifier)',
      control: 'text',
      table: { category: 'Content' },
    },
    value: {
      description: 'Input value attribute',
      control: 'text',
      table: { category: 'Content' },
    },
    id: {
      description: 'Input ID for label association',
      control: 'text',
      table: { category: 'Content' },
    },
    label: {
      description: 'Label text',
      control: 'text',
      table: { category: 'Content' },
    },
    checked: {
      description: 'Whether radio is checked',
      control: 'boolean',
      table: { category: 'Behavior' },
    },
    disabled: {
      description: 'Whether radio is disabled',
      control: 'boolean',
      table: { category: 'Behavior' },
    },
  },
  render: (args) => radioTwig(args),
};

export const Default = {
  args: data,
};

export const Checked = {
  args: {
    ...data,
    checked: true,
  },
};

export const Disabled = {
  args: {
    ...data,
    disabled: true,
  },
};
