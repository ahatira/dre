import markup from './skeleton.twig';
import data from './skeleton.yml';

const settings = {
  title: 'Components/Skeleton',
  tags: ['autodocs'],
  argTypes: {
    type: {
      control: { type: 'select' },
      options: ['text', 'card', 'table'],
      description: 'Skeleton type/shape',
      table: { category: 'Display' },
    },
    lines: {
      control: { type: 'number', min: 1, max: 5 },
      description: 'Number of placeholder lines',
      table: { category: 'Content' },
    },
    height: {
      control: { type: 'select' },
      options: ['small', 'medium', 'large'],
      description: 'Height of each line',
      table: { category: 'Display' },
    },
    show_avatar: {
      control: 'boolean',
      description: 'Show avatar placeholder',
      table: { category: 'Content' },
    },
  },
};

export const Default = {
  name: 'Text',
  render: (args) => markup(args),
  args: { ...data, type: 'text', lines: 3, show_avatar: false },
};

export const WithAvatar = {
  name: 'With Avatar',
  render: (args) => markup(args),
  args: { ...data, type: 'text', lines: 3, show_avatar: true },
};

export default settings;
