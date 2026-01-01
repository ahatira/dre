import block from './block.twig';
import blockData from './block.yml';
import './block.css';

export default {
  title: 'Layouts/Block',
  tags: ['autodocs'],
  argTypes: {
    plugin_id: {
      control: 'text',
      description: 'Machine name of the block implementation',
      table: { category: 'Drupal', defaultValue: { summary: 'custom_placeholder' } },
    },
    configuration: {
      control: 'object',
      description: 'Block configuration (expects provider at minimum)',
      table: { category: 'Drupal', defaultValue: { summary: '{ provider: "custom" }' } },
    },
    label: {
      control: 'text',
      description: 'Block title (hidden when empty)',
      table: { category: 'Content', defaultValue: { summary: 'Block title placeholder' } },
    },
    content: {
      control: 'text',
      description: 'Rendered block content (HTML)',
      table: { category: 'Content', type: { summary: 'string (HTML)' } },
    },
  },
};

export const Default = {
  render: (args) => block(args),
  args: {
    ...blockData,
  },
};
