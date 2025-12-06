import markup from './table.twig';
import data from './table.yml';

const settings = {
  title: 'Components/Table',
  tags: ['autodocs'],
  argTypes: {
    headers: {
      description: 'Array of column header labels',
      table: { category: 'Content' },
    },
    rows: {
      description: 'Array of row arrays (cells)',
      table: { category: 'Content' },
    },
  },
};

export const Default = {
  name: 'Data Table',
  render: (args) => markup(args),
  args: { ...data },
};

export const Empty = {
  name: 'Empty State',
  render: (args) => markup(args),
  args: {
    ...data,
    rows: [],
  },
};

export default settings;
