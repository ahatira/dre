import markup from './tag-list.twig';
import data from './tag-list.yml';

const settings = {
  title: 'Components/Tag List',
  tags: ['autodocs'],
  argTypes: {
    tags: {
      description: 'Array of tag objects with label, removable, selected',
      table: { category: 'Content' },
    },
  },
};

export const Default = {
  name: 'With Checkboxes',
  render: (args) => markup(args),
  args: data,
};

export const ManyTags = {
  name: 'Many Tags',
  render: (args) => markup(args),
  args: {
    tags: [
      { label: 'Paris', removable: true, selected: true },
      { label: 'London', removable: true, selected: false },
      { label: 'Berlin', removable: true, selected: true },
      { label: 'Amsterdam', removable: true, selected: false },
      { label: 'Brussels', removable: true, selected: true },
    ],
  },
};

export default settings;
