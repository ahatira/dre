import wizardStepsTwig from './wizard-steps.twig';
import wizardStepsData from './wizard-steps.yml';

export default {
  title: 'Components/Wizard Steps',
  tags: ['autodocs'],
  args: wizardStepsData,
  argTypes: {
    steps: {
      description: 'Array of step objects with label, completed, active properties',
      control: { type: 'object' },
      table: { category: 'Content' },
    },
    orientation: {
      description: 'Orientation of step indicator',
      control: { type: 'select' },
      options: ['horizontal', 'vertical'],
      table: {
        category: 'Display',
        defaultValue: { summary: 'horizontal' },
      },
    },
    attributes: {
      description: 'Additional HTML attributes (Drupal attributes object)',
      control: false,
      table: { category: 'Attributes' },
    },
  },
};

// Default story
export const Default = {
  name: 'Default (Horizontal)',
  render: (args) => wizardStepsTwig(args),
  args: wizardStepsData,
};

// Property Search Wizard
export const PropertySearchWizard = {
  name: 'Property Search Wizard',
  render: (args) => wizardStepsTwig(args),
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

// Vertical Orientation
export const Vertical = {
  name: 'Vertical Orientation',
  render: (args) => wizardStepsTwig(args),
  args: {
    orientation: 'vertical',
    steps: [
      { label: 'Informations personnelles', completed: true, active: false },
      { label: 'Coordonnées', completed: false, active: true },
      { label: 'Préférences', completed: false, active: false },
    ],
  },
};

// All States
export const AllStates = {
  name: 'All States',
  render: (args) => wizardStepsTwig(args),
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

// Listing Submission Process
export const ListingSubmission = {
  name: 'Listing Submission Process',
  render: (args) => wizardStepsTwig(args),
  args: {
    orientation: 'horizontal',
    steps: [
      { label: 'Informations', completed: true, active: false },
      { label: 'Photos', completed: true, active: false },
      { label: 'Tarification', completed: false, active: true },
      { label: 'Publication', completed: false, active: false },
    ],
  },
};

// Account Setup
export const AccountSetup = {
  name: 'Account Setup (Vertical)',
  render: (args) => wizardStepsTwig(args),
  args: {
    orientation: 'vertical',
    steps: [
      { label: 'Créer votre compte', completed: true, active: false },
      { label: 'Vérifier votre email', completed: true, active: false },
      { label: 'Compléter votre profil', completed: false, active: true },
      { label: 'Choisir vos préférences', completed: false, active: false },
    ],
  },
};
