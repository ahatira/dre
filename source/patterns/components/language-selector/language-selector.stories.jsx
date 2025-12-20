import languageSelectorTemplate from './language-selector.twig';

/**
 * Language Selector - Storybook Stories
 *
 * MANDATORY: tags: ['autodocs'] for automated documentation generation
 */

export default {
  title: 'Components/Language Selector',
  tags: ['autodocs'],
  render: (args) => languageSelectorTemplate(args),
  argTypes: {
    // === CONTENT ===
    name: {
      control: 'text',
      description: 'Input name attribute for native select fallback',
      table: {
        category: 'Content',
        defaultValue: { summary: 'lang' },
      },
    },
    current: {
      control: 'object',
      description: 'Currently selected language (code, label, locale)',
      table: {
        category: 'Content',
        type: { summary: 'object' },
      },
    },
    options: {
      control: 'object',
      description: 'Array of language options',
      table: {
        category: 'Content',
        type: { summary: 'array' },
      },
    },

    // === APPEARANCE ===
    size: {
      control: 'select',
      options: ['sm', 'md', 'lg'],
      description: 'Size variant (sm=36px default, md=40px, lg=48px)',
      table: {
        category: 'Appearance',
        defaultValue: { summary: 'sm' },
      },
    },
    // variant removed: neutral-only

    // === STATE ===
    disabled: {
      control: 'boolean',
      description: 'Disabled state',
      table: {
        category: 'State',
        defaultValue: { summary: false },
      },
    },

    // === ATTRIBUTES ===
    attributes: {
      control: 'object',
      description: 'Additional HTML attributes for nav element',
      table: {
        category: 'Attributes',
      },
    },
  },
};

/**
 * Default Story - Small size (36px, aligned with Figma spec)
 */
export const Default = {
  args: {
    name: 'lang',
    size: 'sm',
    disabled: false,
    current: {
      code: 'GB',
      label: 'En',
      locale: 'en-GB',
    },
    options: [
      {
        code: 'GB',
        label: 'En',
        value: 'en',
        locale: 'en-GB',
        selected: true,
        disabled: false,
      },
      {
        code: 'ES',
        label: 'Es',
        value: 'es',
        locale: 'es-ES',
        selected: false,
        disabled: false,
      },
      {
        code: 'FR',
        label: 'Fr',
        value: 'fr',
        locale: 'fr-FR',
        selected: false,
        disabled: false,
      },
    ],
  },
};

/**
 * Sizes Showcase - All 6 standardized sizes
 */
export const AllSizes = {
  render: () => {
    const baseArgs = {
      name: 'lang',
      // neutral-only
      disabled: false,
      current: {
        code: 'GB',
        label: 'En',
        locale: 'en-GB',
      },
      options: [
        { code: 'GB', label: 'En', value: 'en', locale: 'en-GB', selected: true, disabled: false },
        { code: 'ES', label: 'Es', value: 'es', locale: 'es-ES', selected: false, disabled: false },
        { code: 'FR', label: 'Fr', value: 'fr', locale: 'fr-FR', selected: false, disabled: false },
      ],
    };

    const sizes = ['sm', 'md', 'lg'];
    const sizeLabels = {
      sm: 'Small (36px - Default)',
      md: 'Medium (40px)',
      lg: 'Large (48px)',
    };

    return `
      <div style="display: flex; flex-direction: column; gap: 24px; padding: 24px; background: #f5f5f5;">
        ${sizes
          .map(
            (size) => `
          <div>
            <p style="margin: 0 0 8px 0; font-size: 14px; font-weight: 600; color: #333;">
              ${sizeLabels[size]}
            </p>
            ${languageSelectorTemplate({ ...baseArgs, size })}
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
};

/**
 * Disabled State
 */
export const Disabled = {
  args: {
    ...Default.args,
    disabled: true,
  },
};

/**
 * Large Header Navigation - Used in main navigation
 */
export const LargeHeader = {
  args: {
    name: 'lang',
    size: 'lg',
    disabled: false,
    current: {
      code: 'FR',
      label: 'Fr',
      locale: 'fr-FR',
    },
    options: [
      { code: 'FR', label: 'Fr', value: 'fr', locale: 'fr-FR', selected: true, disabled: false },
      { code: 'GB', label: 'En', value: 'en', locale: 'en-GB', selected: false, disabled: false },
      { code: 'DE', label: 'De', value: 'de', locale: 'de-DE', selected: false, disabled: false },
    ],
  },
};

/**
 * Compact Mobile - Small size for mobile interfaces
 */
export const CompactMobile = {
  args: {
    name: 'lang',
    size: 'sm',
    disabled: false,
    current: {
      code: 'FR',
      label: 'Fr',
      locale: 'fr-FR',
    },
    options: [
      { code: 'FR', label: 'Fr', value: 'fr', locale: 'fr-FR', selected: true, disabled: false },
      { code: 'GB', label: 'En', value: 'en', locale: 'en-GB', selected: false, disabled: false },
    ],
  },
};
