/**
 * Tabs (Molecule)
 *
 * Navigation component to organize related content within tab panels.
 * Supports semantic variants, pill modifier, and optional icons.
 */

import tabsTemplate from './tabs.twig';
import data from './tabs.yml';
import './tabs.js';

const Meta = {
  title: 'Components/Tabs',
  parameters: {
    layout: 'centered',
  },
  tags: ['autodocs'],
  render: (args) => tabsTemplate(args),
  argTypes: {
    label: {
      name: 'Label',
      description: 'Accessible label applied to the tablist.',
      control: 'text',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: 'Property tabs' },
      },
    },
    variant: {
      name: 'Variant',
      description: 'Semantic color variant.',
      control: 'select',
      options: [
        'neutral',
        'primary',
        'success',
        'danger',
        'warning',
        'info',
        'gold',
        'light',
        'dark',
      ],
      table: {
        category: 'Styling',
        type: { summary: 'string' },
        defaultValue: { summary: 'neutral' },
      },
    },
    pill: {
      name: 'Pill',
      description: 'Toggle the pill presentation.',
      control: 'boolean',
      table: {
        category: 'Styling',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    icon: {
      name: 'Default Icon',
      description: 'Fallback icon applied to tabs missing explicit icon.',
      control: 'text',
      table: {
        category: 'Content',
        type: { summary: 'string|null' },
        defaultValue: { summary: 'null' },
      },
    },
    tabs: {
      name: 'Tabs',
      description: 'Array of tab objects with id, label, content, active, disabled, and icon.',
      control: 'object',
      table: {
        category: 'Content',
        type: { summary: 'array' },
        defaultValue: { summary: '[]' },
      },
    },
  },
};

export default Meta;

export const Default = {
  name: 'Default (Primary)',
  args: data,
};

export const Neutral = {
  args: {
    ...data,
    variant: 'neutral',
    tabs: data.tabs.map((tab, index) => ({
      ...tab,
      active: index === 0,
    })),
  },
};

export const SuccessWithIcons = {
  args: {
    label: 'Validation steps',
    variant: 'success',
    icon: 'check',
    tabs: [
      {
        id: 'tab-validation-docs',
        label: 'Documents',
        content: '<p>All legal documentation validated.</p>',
        active: true,
      },
      {
        id: 'tab-validation-finance',
        label: 'Finance',
        content: '<p>Financing plan approved.</p>',
      },
      {
        id: 'tab-validation-compliance',
        label: 'Compliance',
        content: '<p>Compliance review completed.</p>',
      },
    ],
  },
};

export const PillTabs = {
  args: {
    label: 'Acquisition journey',
    variant: 'primary',
    pill: true,
    tabs: [
      {
        id: 'tab-pillar-1',
        label: 'Explore',
        content: '<p>Discover curated property selections.</p>',
        active: true,
      },
      {
        id: 'tab-pillar-2',
        label: 'Compare',
        content: '<p>Review performance indicators and services.</p>',
      },
      {
        id: 'tab-pillar-3',
        label: 'Decide',
        content: '<p>Plan visits and finalize negotiations.</p>',
      },
    ],
  },
};

export const WarningState = {
  args: {
    label: 'Due diligence',
    variant: 'warning',
    tabs: [
      {
        id: 'tab-risk',
        label: 'Risks',
        icon: 'info',
        content: '<p>Identify critical risks requiring mitigation.</p>',
        active: true,
      },
      {
        id: 'tab-dependencies',
        label: 'Dependencies',
        content: '<p>Outline project dependencies and blockers.</p>',
      },
      {
        id: 'tab-contingency',
        label: 'Contingency',
        content: '<p>Mitigation actions prepared for each scenario.</p>',
      },
    ],
  },
};

export const DarkVariant = {
  args: {
    label: 'Night mode tabs',
    variant: 'dark',
    tabs: [
      {
        id: 'tab-highlights',
        label: 'Highlights',
        content: '<p>Key differentiators for premium offices.</p>',
        active: true,
      },
      {
        id: 'tab-services',
        label: 'Services',
        content: '<p>Concierge, catering, and mobility services.</p>',
      },
    ],
  },
};

export const ManualActivation = {
  name: 'Manual Activation',
  args: {
    ...data,
    label: 'Manual activation example',
    variant: 'primary',
    pill: false,
    // Manual activation: requires Enter/Space or click to activate after focus roving
    attributes: {
      'data-activation': 'manual',
    },
    tabs: data.tabs.map((tab, index) => ({
      ...tab,
      active: index === 0,
    })),
  },
};

export const VerticalOrientation = {
  name: 'Vertical Orientation',
  args: {
    label: 'Vertical tabs',
    variant: 'neutral',
    pill: false,
    attributes: {
      'data-orientation': 'vertical',
    },
    tabs: [
      {
        id: 'tab-vertical-1',
        label: 'Overview',
        content: '<p>Summary and KPIs.</p>',
        active: true,
      },
      {
        id: 'tab-vertical-2',
        label: 'Details',
        content: '<p>Specifications and terms.</p>',
      },
      {
        id: 'tab-vertical-3',
        label: 'Contacts',
        content: '<p>Team and stakeholders.</p>',
      },
    ],
  },
};
