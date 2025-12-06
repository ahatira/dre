import markup from './search-bar.twig';
import data from './search-bar.yml';

const settings = {
  title: 'Components/Search Bar',
  tags: ['autodocs'],
  argTypes: {
    placeholder: {
      control: 'text',
      description: 'Placeholder text for input',
      table: { category: 'Content' },
    },
    search_text: {
      control: 'text',
      description: 'Current search text',
      table: { category: 'State' },
    },
    has_suggestions: {
      control: 'boolean',
      description: 'Show suggestions dropdown',
      table: { category: 'Configuration' },
    },
    show_icon: {
      control: 'boolean',
      description: 'Display search icon',
      table: { category: 'Configuration' },
    },
  },
};

export const Default = {
  name: 'Empty',
  render: (args) => markup(args),
  args: { ...data, search_text: '', has_suggestions: false },
};

export const WithSuggestions = {
  name: 'With Suggestions',
  render: (args) => markup(args),
  args: { ...data, search_text: 'Paris', has_suggestions: true },
};

export default settings;
