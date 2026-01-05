/**
 * Search Block Stories
 * Layout block combining search trigger button and expandable form
 */

import searchBlockTemplate from './block-search.twig';
import searchBlockData from './block-search.yml';

// Import dependencies
import '../../../../components/search-form/search-form.css';
import './block-search.css';

export default {
  title: 'Collections/Blocks/Header/Search Block',
  tags: ['autodocs'],
  argTypes: {
    button_label: {
      name: 'Button Label',
      description: 'Text label for search button (hidden on desktop, visible on mobile)',
      control: 'text',
      table: {
        category: 'Content',
        defaultValue: { summary: 'Search' },
      },
    },
    show_form: {
      name: 'Show Form Initially',
      description: 'Display search form on page load (for testing, usually toggled by JavaScript)',
      control: 'boolean',
      table: {
        category: 'State',
        defaultValue: { summary: false },
      },
    },
    search_form_props: {
      name: 'Search Form Props',
      description:
        'Props passed to search-form component (placeholder, action, method, input_name)',
      control: 'object',
      table: {
        category: 'Components',
        defaultValue: {
          summary: '{ placeholder, action, method, input_name }',
        },
      },
    },
  },
};

/**
 * Default: Search block with button (form hidden)
 */
export const Default = {
  args: {
    ...searchBlockData,
  },
};

/**
 * Form Open: Search block with expanded form (for testing)
 */
export const FormOpen = {
  args: {
    ...searchBlockData,
    show_form: true,
  },
  parameters: {
    docs: {
      description: {
        story:
          'Search form expanded state (for visual testing). In production, form opens on button click via JavaScript.',
      },
    },
  },
};

/**
 * Mobile View: Demonstrates responsive label visibility
 */
export const MobileView = {
  args: {
    ...searchBlockData,
  },
  parameters: {
    viewport: {
      defaultViewport: 'mobile1',
    },
    docs: {
      description: {
        story:
          'Mobile viewport showing "Search" label next to icon. On desktop (>768px), only icon is visible.',
      },
    },
  },
};

/**
 * Real Estate Context: Search in header
 */
export const InHeader = {
  render: (args) => {
    return `
      <div style="background: var(--white); border-bottom: 1px solid var(--border-light); padding: var(--size-4) var(--size-6);">
        <div style="display: flex; justify-content: space-between; align-items: center; max-width: var(--size-max-site-width); margin-inline: auto;">
          <div style="font-size: var(--font-size-5); font-weight: var(--font-weight-700); color: var(--primary);">
            BNP Paribas Real Estate
          </div>
          <div style="display: flex; gap: var(--size-4); align-items: center;">
            <span style="color: var(--text-secondary); font-size: var(--font-size-3);">Find a property</span>
            ${searchBlockTemplate(args)}
          </div>
        </div>
      </div>
    `;
  },
  args: {
    ...searchBlockData,
  },
  parameters: {
    docs: {
      description: {
        story: 'Search block integrated in header navigation with "Find a property" link.',
      },
    },
  },
};

// Default story template
Default.render = (args) => searchBlockTemplate(args);
FormOpen.render = (args) => searchBlockTemplate(args);
MobileView.render = (args) => searchBlockTemplate(args);
