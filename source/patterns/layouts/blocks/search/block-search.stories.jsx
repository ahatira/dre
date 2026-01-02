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
      description: 'Block configuration',
      table: { category: 'Drupal', defaultValue: { summary: '{ provider: "search" }' } },
    },
    label: {
      control: 'text',
      description: 'Block title (optional)',
      table: { category: 'Drupal', defaultValue: { summary: '' } },
    },
    button_label: {
      control: 'text',
      description: 'Search button label (visible on mobile)',
      table: { category: 'Content', defaultValue: { summary: 'Search' } },
    },
    icon: {
      control: 'text',
      description: 'Icon name (without icon- prefix)',
      table: { category: 'Content', defaultValue: { summary: 'search' } },
    },
  },
};

/**
 * Default: Search block in realistic header context
 * Shows the search trigger button integrated in a header with navigation menu.
 * Click the button to open the search form component.
 */
export const Default = {
  render(args) {
    const buttonHtml = blockSearch(args);
    const formHtml = searchFormTwig(searchFormData);

    return `
      <style>
        .demo-header {
          width: 100%;
          background: var(--white);
          border-bottom: 1px solid var(--border-light);
          box-shadow: var(--shadow-1);
        }

        .demo-header__nav {
          display: flex;
          align-items: center;
          justify-content: space-between;
          max-width: 1200px;
          margin: 0 auto;
          padding: var(--size-4) var(--size-6);
          gap: var(--size-4);
        }

        .demo-header__left {
          display: flex;
          gap: var(--size-6);
          align-items: center;
        }

        .demo-header__right {
          display: flex;
          gap: var(--size-3);
          align-items: center;
        }

        .demo-nav-item {
          padding: var(--size-2) var(--size-4);
          background: transparent;
          border: none;
          cursor: pointer;
          font-weight: 500;
          color: var(--text-primary);
          transition: color var(--duration-2) var(--ease-out);
        }

        .demo-nav-item:hover {
          color: var(--primary);
        }
      </style>

      <div class="demo-header">
        <div class="demo-header__nav">
          <div class="demo-header__left">
            <button class="demo-nav-item">Find a property</button>
            <button class="demo-nav-item">About us</button>
            <button class="demo-nav-item">Solutions</button>
          </div>
          <div class="demo-header__right">
            <button class="demo-nav-item">User Account</button>
            ${buttonHtml}
          </div>
        </div>
      </div>

      ${formHtml}

      <script>
        (function() {
          const trigger = document.querySelector('.ps-search-trigger');
          const searchForm = document.querySelector('[data-search-form]');

          if (trigger && searchForm) {
            trigger.addEventListener('click', function(e) {
              e.preventDefault();
              searchForm.classList.toggle('ps-search-form--open');
              
              const input = searchForm.querySelector('[data-search-input]');
              if (input && searchForm.classList.contains('ps-search-form--open')) {
                setTimeout(function() { input.focus(); }, 100);
              }
            });

            const closeBtn = searchForm.querySelector('[data-search-close]');
            if (closeBtn) {
              closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                searchForm.classList.remove('ps-search-form--open');
              });
            }

            document.addEventListener('keydown', function(e) {
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
  args: blockSearchData,
  parameters: {
    layout: 'fullscreen',
    docs: {
      description: {
        story:
          'Search trigger button displayed in a realistic header layout. Click the button to open the search form. The form appears below with a smooth animation. Press ESC or click the close button to hide it.',
      },
    },
  },
};
