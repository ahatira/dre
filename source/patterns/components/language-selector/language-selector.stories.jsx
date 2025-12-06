import markup from './language-selector.twig';
import data from './language-selector.yml';

const settings = {
  title: 'Components/Language Selector',
  tags: ['autodocs'],
  argTypes: {
    languages: {
      control: { type: 'object' },
      description: 'Array of language options',
    },
    current: {
      control: { type: 'text' },
      description: 'Current language code',
    },
  },
};

export const Default = {
  name: 'Language Selector',
  render: (args) => markup(args),
  args: { ...data },
};

export default settings;
