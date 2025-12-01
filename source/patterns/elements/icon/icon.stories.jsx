import iconTwig from './icon.twig';
import defaultData from './icon.yml';

export default {
  title: 'Elements/Icon',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Icon component using font glyphs via CSS pseudo-element. Minimal markup, token-based sizes and colors.',
      },
    },
  },
  argTypes: {
    name: {
      control: 'text',
      description: 'Unique icon name (no "icon-" prefix)',
      table: { category: 'Content' },
    },
    size: {
      control: { type: 'select' },
      options: [16, 20, 24, 32],
      description: 'Icon size in px (mapped to tokens)',
      table: { category: 'Appearance' },
    },
    colorVariant: {
      control: { type: 'select' },
      options: ['dark-grey', 'light-grey', 'green', 'white'],
      description: 'Semantic color variant',
      table: { category: 'Appearance' },
    },
    state: {
      control: { type: 'select' },
      options: ['default', 'disabled', 'hover', 'selected'],
      description: 'Visual state modifier',
      table: { category: 'Behavior' },
    },
    color: {
      control: 'text',
      description: 'Custom CSS color to override variant',
      table: { category: 'Appearance' },
    },
    ariaLabel: {
      control: 'text',
      description: 'Accessibility label for informative icons',
      table: { category: 'Accessibility' },
    },
  },
};

const render = (args) => iconTwig(args);

export const Default = {
  args: { ...defaultData, name: 'arrow-right' },
  render,
};

export const Sizes = {
  args: { ...defaultData, name: 'arrow-right' },
  render: (args) => `
    ${iconTwig({ ...args, size: 16 })}
    ${iconTwig({ ...args, size: 20 })}
    ${iconTwig({ ...args, size: 24 })}
    ${iconTwig({ ...args, size: 32 })}
  `,
};

export const Colors = {
  args: { ...defaultData, name: 'facebook' },
  render: (args) => `
    ${iconTwig({ ...args, colorVariant: 'dark-grey' })}
    ${iconTwig({ ...args, colorVariant: 'light-grey' })}
    ${iconTwig({ ...args, colorVariant: 'green' })}
    ${iconTwig({ ...args, colorVariant: 'white' })}
  `,
};

export const States = {
  args: { ...defaultData, name: 'fav-filled', colorVariant: 'green' },
  render: (args) => `
    ${iconTwig({ ...args, state: 'default' })}
    ${iconTwig({ ...args, state: 'disabled' })}
    ${iconTwig({ ...args, state: 'hover' })}
    ${iconTwig({ ...args, state: 'selected' })}
  `,
};

export const AllVariants = {
  args: { ...defaultData, name: 'search' },
  render: (args) => `
    <div>
      <div>${Sizes.render({ ...args })}</div>
      <div>${Colors.render({ ...args })}</div>
      <div>${States.render({ ...args })}</div>
    </div>
  `,
};
