import markup from './stepper.twig';
import data from './stepper.yml';

const settings = {
  title: 'Components/Stepper',
  tags: ['autodocs'],
  argTypes: {
    steps: {
      control: { type: 'object' },
      description: 'Array of step objects with label, completed, active properties',
      table: { category: 'Content' },
    },
    orientation: {
      control: { type: 'select' },
      options: ['horizontal', 'vertical'],
      description: 'Orientation of step indicator',
      table: {
        category: 'Display',
        defaultValue: { summary: 'horizontal' },
      },
    },
    attributes: {
      control: false,
      description: 'Additional HTML attributes (Drupal attributes object)',
      table: { category: 'Attributes' },
    },
  },
};

export const Default = {
  name: '↔ Default (Horizontal)',
  render: (args) => markup(args),
  args: data,
};

export const PropertySearchWizard = {
  name: '🏢 Property Search Wizard',
  render: (args) => markup(args),
  args: {
    orientation: 'horizontal',
    steps: [
      { label: 'Type de bien', completed: true, active: false },
      { label: 'Localisation', completed: true, active: false },
      { label: 'Budget', completed: true, active: false },
      { label: 'Caractéristiques', completed: false, active: true },
      { label: 'Confirmation', completed: false, active: false },
    ],
  },
};

export const Vertical = {
  name: '↕ Vertical Orientation',
  render: (args) => markup(args),
  args: {
    orientation: 'vertical',
    steps: [
      { label: 'Informations personnelles', completed: true, active: false },
      { label: 'Coordonnées', completed: false, active: true },
      { label: 'Préférences', completed: false, active: false },
    ],
  },
};

export const AllStates = {
  name: '🎨 All States',
  render: (args) => markup(args),
  args: {
    orientation: 'horizontal',
    steps: [
      { label: 'Completed', completed: true, active: false },
      { label: 'Active', completed: false, active: true },
      { label: 'Pending', completed: false, active: false },
      { label: 'Pending', completed: false, active: false },
    ],
  },
};

export default settings;
