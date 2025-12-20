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
 * Default Story - Figma spec (36px height, 2px border)
 */
export const Default = {
  args: {
    name: 'lang',
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
 * Disabled State
 */
export const Disabled = {
  args: {
    ...Default.args,
    disabled: true,
  },
};

/**
 * French Selected - Different initial selection
 */
export const FrenchSelected = {
  args: {
    name: 'lang',
    disabled: false,
    current: {
      code: 'FR',
      label: 'Fr',
      locale: 'fr-FR',
    },
    options: [
      { code: 'FR', label: 'Fr', value: 'fr', locale: 'fr-FR', selected: true, disabled: false },
      { code: 'GB', label: 'En', value: 'en', locale: 'en-GB', selected: false, disabled: false },
      { code: 'ES', label: 'Es', value: 'es', locale: 'es-ES', selected: false, disabled: false },
    ],
  },
};
