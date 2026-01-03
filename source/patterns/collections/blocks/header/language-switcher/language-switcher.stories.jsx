import languageSwitcherTwig from './language-switcher.twig';
import languageSwitcherData from './language-switcher.yml';

export default {
  title: 'Collections/Blocks/Header/Language Switcher',
  tags: ['autodocs'],
  argTypes: {
    name: {
      control: 'text',
      description: 'Input name attribute for the native select fallback',
      table: {
        category: 'Content',
        defaultValue: { summary: 'lang' },
      },
    },
    size: {
      control: 'select',
      options: ['sm', 'md', 'lg'],
      description: 'Size variant of the language selector',
      table: {
        category: 'Presentation',
        defaultValue: { summary: 'sm' },
      },
    },
    disabled: {
      control: 'boolean',
      description: 'Disables the entire selector',
      table: {
        category: 'State',
        defaultValue: { summary: 'false' },
      },
    },
    current: {
      control: 'object',
      description: 'Currently selected language (code, label, locale)',
      table: {
        category: 'Content',
      },
    },
    options: {
      control: 'object',
      description: 'Array of language options',
      table: {
        category: 'Content',
      },
    },
  },
};

/**
 * Default: Language switcher with 5 languages (En, Fr, Es, De, It)
 */
export const Default = {
  render: (args) => languageSwitcherTwig(args),
  args: {
    ...languageSwitcherData,
  },
};

/**
 * French: Current language set to French
 */
export const French = {
  render: (args) => languageSwitcherTwig(args),
  args: {
    ...languageSwitcherData,
    current: {
      code: 'FR',
      label: 'Fr',
      locale: 'fr-FR',
    },
    options: languageSwitcherData.options.map((opt) =>
      opt.code === 'FR' ? { ...opt, selected: true } : { ...opt, selected: false }
    ),
  },
};

/**
 * Disabled: Language switcher in disabled state
 */
export const Disabled = {
  render: (args) => languageSwitcherTwig(args),
  args: {
    ...languageSwitcherData,
    disabled: true,
  },
};
