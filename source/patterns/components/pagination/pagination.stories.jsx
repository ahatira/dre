import markup from './pagination.twig';
import data from './pagination.yml';

const settings = {
  title: 'Components/Pagination',
  tags: ['autodocs'],
  argTypes: {
    current_page: {
      control: { type: 'number', min: 1, max: 10 },
      description: 'Current active page number',
      table: { category: 'State' },
    },
    total_pages: {
      control: { type: 'number', min: 1, max: 20 },
      description: 'Total number of pages available',
      table: { category: 'Configuration' },
    },
    has_previous: {
      control: 'boolean',
      description: 'Show previous page button',
      table: { category: 'State' },
    },
    has_next: {
      control: 'boolean',
      description: 'Show next page button',
      table: { category: 'State' },
    },
  },
};

export const Default = {
  name: 'Pagination',
  render: (args) => markup(args),
  args: data,
};

export const FirstPage = {
  name: 'First Page (No Previous)',
  render: (args) => markup(args),
  args: Object.assign({}, data, {
    current_page: 1,
    has_previous: false,
    has_next: true,
  }),
};

export const LastPage = {
  name: 'Last Page (No Next)',
  render: (args) => markup(args),
  args: Object.assign({}, data, {
    current_page: 5,
    has_previous: true,
    has_next: false,
  }),
};

export default settings;
