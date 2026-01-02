import blockSearch from './block-search.twig';
import blockSearchData from './block-search.yml';
import './block-search.css';

export default {
  title: 'Layouts/Blocks/Search',
  tags: ['autodocs'],
  argTypes: {
    plugin_id: {
      control: 'text',
      description: 'Drupal block plugin ID',
      table: { category: 'Drupal', defaultValue: { summary: 'block_search' } },
    },
    configuration: {
      control: 'object',
      description: 'Block configuration (expects provider at minimum)',
      table: { category: 'Drupal', defaultValue: { summary: '{ provider: "search" }' } },
    },
    label: {
      control: 'text',
      description: 'Block title (empty by default)',
      table: { category: 'Drupal', defaultValue: { summary: '' } },
    },
    button_label: {
      control: 'text',
      description: 'Search button label (shown on mobile)',
      table: { category: 'Button', defaultValue: { summary: 'Search' } },
    },
    icon: {
      control: 'text',
      description: 'Icon name (without prefix)',
      table: { category: 'Button', defaultValue: { summary: 'search' } },
    },
  },
};

export const Default = {
  render(args) {
    return blockSearch(args);
  },
  args: blockSearchData,
};

export const WithCustomLabel = {
  render(args) {
    return blockSearch(args);
  },
  args: {
    ...blockSearchData,
    button_label: 'Find properties',
  },
};
