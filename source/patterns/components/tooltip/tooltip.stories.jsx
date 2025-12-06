import markup from './tooltip.twig';
import data from './tooltip.yml';

const settings = {
  title: 'Components/Tooltip',
  tags: ['autodocs'],
  argTypes: {
    text: {
      control: 'text',
      description: 'Tooltip content text',
      table: { category: 'Content' },
    },
    position: {
      control: { type: 'select' },
      options: ['top', 'bottom', 'left', 'right'],
      description: 'Tooltip position relative to trigger',
      table: { category: 'Display' },
    },
    trigger_text: {
      control: 'text',
      description: 'Trigger button text',
      table: { category: 'Content' },
    },
  },
};

export const TopPosition = {
  name: 'Top',
  render: (args) => markup(args),
  args: { ...data, position: 'top', text: 'Contact the agent for more info' },
};

export const BottomPosition = {
  name: 'Bottom',
  render: (args) => markup(args),
  args: { ...data, position: 'bottom', text: 'Swipe to see more photos' },
};

export const RightPosition = {
  name: 'Right',
  render: (args) => markup(args),
  args: { ...data, position: 'right', text: 'Click to expand details' },
};

export default settings;
