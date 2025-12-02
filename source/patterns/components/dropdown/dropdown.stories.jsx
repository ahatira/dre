import dropdownTwig from './dropdown.twig';
import dropdownData from './dropdown.yml';

export default {
  title: 'Components/Dropdown',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Accessible select dropdown with custom styling, keyboard navigation, and native `<select>` fallback.\n\n' +
          'See Props, Showcases (AllSizes, AllShapes), and README for complete details on variants, accessibility, and design tokens.',
      },
    },
  },
  argTypes: {
    // Content
    name: {
      description: 'Form field name attribute (required for form submission)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
      },
    },
    label: {
      description: 'Visible button label (uses selected option if not provided)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    placeholder: {
      description: 'Placeholder text when no option selected',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'Select an option' },
      },
    },
    options: {
      description: 'Array of option objects with label, value, selected, and disabled properties',
      control: { type: 'object' },
      table: {
        category: 'Content',
        type: { summary: 'array', required: true },
      },
    },

    // Appearance
    size: {
      description: 'Size variant: small (compact), medium (default), or large (prominent)',
      control: { type: 'select' },
      options: ['small', 'medium', 'large'],
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'medium' },
      },
    },
    shape: {
      description: 'Border radius: none (sharp), rounded (default 4px), or pill (fully rounded)',
      control: { type: 'select' },
      options: ['none', 'rounded', 'pill'],
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'rounded' },
      },
    },

    // Behavior
    disabled: {
      description: 'Disable the dropdown (non-interactive)',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },

    // Accessibility
    attributes: {
      description: 'Additional HTML attributes object',
      control: { type: 'object' },
      table: {
        category: 'Accessibility',
        type: { summary: 'object' },
      },
    },
  },
};

export const Default = {
  render: (args) => dropdownTwig(args),
  args: { ...dropdownData },
};

// All sizes showcase
export const AllSizes = {
  render: () => {
    const baseData = {
      name: 'size_demo',
      options: [
        { label: 'Apartment', value: 'apartment', selected: true },
        { label: 'House', value: 'house' },
        { label: 'Loft', value: 'loft' },
      ],
    };

    const small = dropdownTwig({
      ...baseData,
      name: 'small',
      label: 'Small dropdown',
      size: 'small',
    });
    const medium = dropdownTwig({
      ...baseData,
      name: 'medium',
      label: 'Medium dropdown',
      size: 'medium',
    });
    const large = dropdownTwig({
      ...baseData,
      name: 'large',
      label: 'Large dropdown',
      size: 'large',
    });

    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-6); max-width: 400px;">
        <div>
          <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Small</label>
          ${small}
        </div>
        <div>
          <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Medium (Default)</label>
          ${medium}
        </div>
        <div>
          <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Large</label>
          ${large}
        </div>
      </div>
    `;
  },
};

// All shapes showcase
export const AllShapes = {
  render: () => {
    const baseData = {
      name: 'shape_demo',
      options: [
        { label: 'Apartment', value: 'apartment', selected: true },
        { label: 'House', value: 'house' },
        { label: 'Loft', value: 'loft' },
      ],
    };

    const none = dropdownTwig({
      ...baseData,
      name: 'none',
      label: 'No radius (sharp)',
      shape: 'none',
    });
    const rounded = dropdownTwig({
      ...baseData,
      name: 'rounded',
      label: 'Rounded (default)',
      shape: 'rounded',
    });
    const pill = dropdownTwig({
      ...baseData,
      name: 'pill',
      label: 'Pill (fully rounded)',
      shape: 'pill',
    });

    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-6); max-width: 400px;">
        <div>
          <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">None (Sharp corners)</label>
          ${none}
        </div>
        <div>
          <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Rounded (Default)</label>
          ${rounded}
        </div>
        <div>
          <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Pill (Fully rounded)</label>
          ${pill}
        </div>
      </div>
    `;
  },
};

// With disabled options
export const WithDisabledOptions = {
  render: () => {
    const dropdown = dropdownTwig({
      name: 'property_disabled',
      label: 'Property type',
      options: [
        { label: 'Apartment', value: 'apartment', selected: true },
        { label: 'House', value: 'house' },
        { label: 'Loft', value: 'loft', disabled: true },
        { label: 'Villa', value: 'villa', disabled: true },
        { label: 'Commercial', value: 'commercial' },
      ],
    });

    return `
      <div style="max-width: 400px;">
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">
          Property type (some options disabled)
        </label>
        ${dropdown}
        <p style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--ps-color-text-muted);">
          Try opening the dropdown - Loft and Villa options are disabled
        </p>
      </div>
    `;
  },
};

// Disabled dropdown
export const DisabledDropdown = {
  render: (args) => dropdownTwig(args),
  args: {
    ...dropdownData,
    label: 'Disabled dropdown',
    disabled: true,
  },
};

// Long list with scroll
export const LongList = {
  render: () => {
    const countries = [
      'France',
      'Germany',
      'Spain',
      'Italy',
      'United Kingdom',
      'Belgium',
      'Netherlands',
      'Portugal',
      'Switzerland',
      'Austria',
      'Sweden',
      'Norway',
      'Denmark',
      'Finland',
      'Poland',
      'Czech Republic',
      'Hungary',
      'Greece',
      'Ireland',
      'Luxembourg',
    ];

    const options = countries.map((country, index) => ({
      label: country,
      value: country.toLowerCase().replace(/\s+/g, '_'),
      selected: index === 0,
    }));

    const dropdown = dropdownTwig({
      name: 'country',
      label: 'Select country',
      options,
    });

    return `
      <div style="max-width: 400px;">
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">
          Country (scrollable list)
        </label>
        ${dropdown}
        <p style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--ps-color-text-muted);">
          List has max-height and scroll for many options
        </p>
      </div>
    `;
  },
};

// Grouped sections with checkboxes (Search mockup design)
export const GroupedWithCheckboxes = {
  render: () => {
    return `
      <div style="max-width: 420px;">
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Label</label>
        
        <div class="ps-dropdown ps-dropdown--grouped-prototype" style="position: relative; display: inline-block; width: 100%;">
          <button
            class="ps-dropdown__button"
            type="button"
            aria-haspopup="listbox"
            aria-expanded="false"
            data-grouped-dropdown-button
            style="display: inline-flex; align-items: center; justify-content: space-between; gap: var(--size-2); width: 100%; min-width: var(--ps-dropdown-min-width-medium); padding: var(--size-2) var(--size-3); border: var(--border-size-1) solid var(--gray-300); border-radius: var(--radius-2); background: var(--white); color: var(--ps-color-text); font-family: var(--ps-font-family-primary); font-size: var(--font-size-1); line-height: var(--leading-normal); cursor: pointer;"
          >
            <span style="flex: 1; text-align: left;">Placeholder</span>
            <span data-icon="chevron-down" aria-hidden="true" style="flex-shrink: 0; width: var(--ps-icon-size-20); height: var(--ps-icon-size-20); font-size: var(--ps-icon-size-20); line-height: 1;"></span>
          </button>

          <div
            style="display: none; position: absolute; z-index: var(--layer-40); top: calc(100% + var(--size-1)); left: 0; min-width: 100%; max-height: var(--size-80); overflow-y: auto; background: var(--white); border: var(--border-size-1) solid var(--gray-300); border-radius: var(--radius-2); box-shadow: var(--shadow-4); padding: var(--size-2) 0; margin: 0; list-style: none;"
          >
            <!-- Section 1 -->
            <div style="padding: var(--size-2) var(--size-3); font-weight: var(--font-weight-600); font-size: var(--font-size-0); color: var(--ps-color-text-muted); text-transform: uppercase;">
              Section 1
            </div>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 1</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 2</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 3</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 4</span>
            </label>

            <!-- Divider -->
            <div style="height: var(--border-size-1); background: var(--color-border-default); margin: var(--size-2) 0;"></div>

            <!-- Section 2 -->
            <div style="padding: var(--size-2) var(--size-3); font-weight: var(--font-weight-600); font-size: var(--font-size-0); color: var(--ps-color-text-muted); text-transform: uppercase;">
              Section 2
            </div>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 1</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 2</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 3</span>
            </label>

            <!-- Divider -->
            <div style="height: var(--border-size-1); background: var(--color-border-default); margin: var(--size-2) 0;"></div>

            <!-- Section 3 -->
            <div style="padding: var(--size-2) var(--size-3); font-weight: var(--font-weight-600); font-size: var(--font-size-0); color: var(--ps-color-text-muted); text-transform: uppercase;">
              Section 3
            </div>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 1</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 2</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 3</span>
            </label>

            <!-- Divider -->
            <div style="height: var(--border-size-1); background: var(--color-border-default); margin: var(--size-2) 0;"></div>

            <!-- Section 4 -->
            <div style="padding: var(--size-2) var(--size-3); font-weight: var(--font-weight-600); font-size: var(--font-size-0); color: var(--ps-color-text-muted); text-transform: uppercase;">
              Section 4
            </div>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 1</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 2</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 3</span>
            </label>

            <!-- Footer Button -->
            <div style="padding: var(--size-3) var(--size-3) var(--size-2); border-top: var(--border-size-1) solid var(--color-border-default); margin-top: var(--size-2);">
              <button type="button" style="width: 100%; padding: var(--size-3) var(--size-4); background: var(--primary); color: var(--white); border: none; border-radius: var(--radius-2); cursor: pointer; font-size: var(--font-size-1); font-weight: var(--font-weight-600); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--primary-hover)'" onmouseout="this.style.backgroundColor='var(--primary)'">
                Apply Selection
              </button>
            </div>
          </div>
        </div>

        <p style="margin-top: var(--size-3); font-size: var(--font-size-0); color: var(--ps-color-text-muted);">
          <strong>Design mockup preview</strong>: Grouped sections with checkboxes and footer button.<br>
          This is a visual prototype of the "Search" variant shown in mockups.<br>
          <em>Future implementation will require: multiselect prop, grouped options data structure, and footer slot.</em>
        </p>
      </div>

      <script>
        // Manual toggle for this prototype story (since it doesn't use standard options structure)
        (function() {
          const button = document.querySelector('[data-grouped-dropdown-button]');
          const list = button ? button.nextElementSibling : null;
          
          if (button && list) {
            // Toggle dropdown
            button.addEventListener('click', function(e) {
              e.stopPropagation();
              const isExpanded = button.getAttribute('aria-expanded') === 'true';
              
              if (isExpanded) {
                list.style.display = 'none';
                button.setAttribute('aria-expanded', 'false');
              } else {
                list.style.display = 'block';
                button.setAttribute('aria-expanded', 'true');
              }
            });

            // Close on outside click
            document.addEventListener('click', function(e) {
              if (!button.contains(e.target) && !list.contains(e.target)) {
                list.style.display = 'none';
                button.setAttribute('aria-expanded', 'false');
              }
            });

            // Close on Escape key
            document.addEventListener('keydown', function(e) {
              if (e.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') {
                list.style.display = 'none';
                button.setAttribute('aria-expanded', 'false');
                button.focus();
              }
            });
          }
        })();
      </script>
    `;
  },
};

// Form integration example
export const InForm = {
  render: () => {
    const typeDropdown = dropdownTwig({
      name: 'property_type',
      label: 'Property type',
      options: [
        { label: 'Apartment', value: 'apartment', selected: true },
        { label: 'House', value: 'house' },
        { label: 'Loft', value: 'loft' },
      ],
    });

    const roomsDropdown = dropdownTwig({
      name: 'rooms',
      label: 'Bedrooms',
      options: [
        { label: '1 bedroom', value: '1' },
        { label: '2 bedrooms', value: '2', selected: true },
        { label: '3 bedrooms', value: '3' },
        { label: '4+ bedrooms', value: '4plus' },
      ],
    });

    return `
      <form style="max-width: 400px; display: flex; flex-direction: column; gap: var(--size-4);">
        <div>
          <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Property type</label>
          ${typeDropdown}
        </div>
        <div>
          <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Number of bedrooms</label>
          ${roomsDropdown}
        </div>
        <button type="submit" style="padding: var(--size-3) var(--size-6); background: var(--primary); color: var(--white); border: none; border-radius: var(--radius-2); cursor: pointer; transition: background-color var(--ps-transition-duration-fast) var(--ease-3);" onmouseover="this.style.backgroundColor='var(--primary-hover)'" onmouseout="this.style.backgroundColor='var(--primary)'">
          Search properties
        </button>
      </form>
    `;
  },
};
