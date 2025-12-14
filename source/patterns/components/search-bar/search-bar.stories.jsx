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
    size: {
      control: 'select',
      options: ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'],
      description: 'Size variant',
      table: { category: 'Appearance' },
    },
  },
};

export const Default = {
  name: 'Empty',
  render: (args) => markup(args),
  args: Object.assign({}, data, { search_text: '', has_suggestions: false }),
};

export const WithSuggestions = {
  name: 'With Suggestions',
  render: (args) => markup(args),
  args: Object.assign({}, data, { search_text: 'Paris', has_suggestions: true }),
};

// Size Variants
export const Sizes = {
  render: () => {
    const sizes = ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'];
    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-6); max-width: 600px;">
        ${sizes
          .map(
            (size) => `
          <div>
            <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600); text-transform: uppercase;">${size}</label>
            ${markup({ ...data, size, placeholder: `Search (${size.toUpperCase()})...`, has_suggestions: false })}
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
};

export default settings;
