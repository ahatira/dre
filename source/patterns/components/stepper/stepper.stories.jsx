import markup from './stepper.twig';
import data from './stepper.yml';

const settings = {
  title: 'Components/Stepper',
  tags: ['autodocs'],
  argTypes: {
    steps: {
      description: 'Array of step objects with label, completed, active',
      table: { category: 'Content' },
    },
    orientation: {
      control: { type: 'select' },
      options: ['horizontal', 'vertical'],
      description: 'Step indicator orientation',
      table: { category: 'Display' },
    },
  },
};

export const Default = {
  name: 'Horizontal',
  render: (args) => markup(args),
  args: Object.assign({}, data, { orientation: 'horizontal' }),
};

export const Vertical = {
  name: 'Vertical',
  render: (args) => markup(args),
  args: Object.assign({}, data, {
    orientation: 'vertical',
    steps: [
      { label: 'Review', completed: true, active: false },
      { label: 'Confirm', completed: false, active: true },
      { label: 'Submit', completed: false, active: false },
    ],
  }),
};

export default settings;
