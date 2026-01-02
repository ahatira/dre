import searchFormTwig from '../../../components/search-form/search-form.twig';
import searchFormData from '../../../components/search-form/search-form.yml';
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
  parameters: {
    docs: {
      description: {
        story:
          'Search trigger button with integrated search form. Form is hidden by default and toggles when button is clicked.',
      },
    },
  },
};

export const WithCustomLabel = {
  render(args) {
    return blockSearch(args);
  },
  args: {
    ...blockSearchData,
    button_label: 'Find properties',
  },
  parameters: {
    docs: {
      description: {
        story: 'Search button with custom label. Form remains integrated and toggle-enabled.',
      },
    },
  },
};

/**
 * Complete integration with search form
 * Demonstrates how the block works with search-form component in a real header
 */
export const WithSearchForm = {
  render(args) {
    const buttonHtml = blockSearch(args);
    const formHtml = searchFormTwig(args.form || {});

    // Create interactive demo with JavaScript
    return `
      <style>
        .storybook-search-demo {
          width: 100%;
          background: var(--white);
          border-bottom: 1px solid var(--border-light);
        }

        .storybook-header-nav {
          display: flex;
          align-items: center;
          justify-content: space-between;
          max-width: 1200px;
          margin: 0 auto;
          padding: var(--size-4) var(--size-6);
          gap: var(--size-4);
        }

        .storybook-nav-left {
          display: flex;
          gap: var(--size-6);
          align-items: center;
        }

        .storybook-nav-right {
          display: flex;
          gap: var(--size-3);
          align-items: center;
        }

        .storybook-nav-item {
          padding: var(--size-2) var(--size-4);
          background: transparent;
          border: none;
          cursor: pointer;
          font-weight: 500;
          color: var(--text-primary);
          transition: color var(--duration-2) var(--ease-out);
        }

        .storybook-nav-item:hover {
          color: var(--primary);
        }
      </style>

      <div class="storybook-search-demo">
        <div class="storybook-header-nav">
          <div class="storybook-nav-left">
            <button class="storybook-nav-item">Find a property</button>
            <button class="storybook-nav-item">About us</button>
            <button class="storybook-nav-item">Solutions</button>
          </div>
          <div class="storybook-nav-right">
            <button class="storybook-nav-item">User Account</button>
            ${buttonHtml}
          </div>
        </div>
      </div>

      ${formHtml}

      <script>
        (function() {
          // Simple interactive demo for Storybook (non-Drupal version)
          const trigger = document.querySelector('.ps-search-trigger');
          const searchForm = document.querySelector('[data-search-form]');

          if (trigger && searchForm) {
            trigger.addEventListener('click', (e) => {
              e.preventDefault();
              searchForm.classList.toggle('ps-search-form--open');
              const input = searchForm.querySelector('[data-search-input]');
              if (input && searchForm.classList.contains('ps-search-form--open')) {
                input.focus();
              }
            });

            // Close button
            const closeBtn = searchForm.querySelector('[data-search-close]');
            if (closeBtn) {
              closeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                searchForm.classList.remove('ps-search-form--open');
              });
            }

            // ESC key
            document.addEventListener('keydown', (e) => {
              if (e.key === 'Escape' && searchForm.classList.contains('ps-search-form--open')) {
                e.preventDefault();
                searchForm.classList.remove('ps-search-form--open');
              }
            });
          }
        })();
      </script>
    `;
  },
  args: {
    ...blockSearchData,
    form: searchFormData,
  },
  parameters: {
    layout: 'fullscreen',
    docs: {
      description: {
        story:
          'Complete integration showing the search button in a header-like layout with the search form below. Click the search button to toggle the form. Press ESC or click the close button to hide it.',
      },
    },
  },
};
