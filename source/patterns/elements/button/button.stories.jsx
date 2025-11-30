import buttonTwig from './button.twig';
import data from './button.yml';
import colorsList from '../../documentation/colors-list.json';
import sizesList from '../../documentation/sizes-list.json';
import iconsList from '../../documentation/icons-list.json';
import variantsList from '../../documentation/variants-list.json';

export default {
  title: 'Elements/Button',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Semantic action button compliant with the Design System.\n\n' +
          '- **Variants**: primary, secondary, success, info, warning, danger, dark, light — colors via brand tokens.\n' +
          '- **Styles**: filled (default) and `outline` (transparent background, tokenized border).\n' +
          '- **Sizes**: small, medium (default), large — heights/spacing driven by tokens.\n' +
          '- **Icons**: optional left/right, via icon name (font `bnpre-icons`).\n' +
          '- **States**: disabled and loading with compliant styles/accessibility.\n' +
          '- **Layout**: `fullWidth` extends to 100% of container.\n' +
          '- **Accessibility**: role/behavior button or link according to `url`; focus visible; textual label required.\n' +
          '- **Minimal markup**: `.ps-button` carries default styles; modifiers only appear when option differs from default.',
      },
    },
  },
  argTypes: {
    // Content
    label: {
      description: 'Button text',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'Button' },
      },
    },
    icon: {
      description: 'Icon name to display (optional)',
      control: { type: 'select' },
      options: iconsList.categories.generic,
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    iconPosition: {
      description: 'Icon position relative to text',
      control: { type: 'select' },
      options: ['left', 'right'],
      table: {
        category: 'Content',
        type: { summary: 'left | right' },
        defaultValue: { summary: 'right' },
      },
    },
    // Appearance
    variant: {
      description: 'Semantic variant',
      control: { type: 'select' },
      options: variantsList.color.components.button,
      table: {
        category: 'Appearance',
        type: { summary: variantsList.color.components.button.join(' | ') },
        defaultValue: { summary: 'primary' },
      },
    },
    outline: {
      description: 'Outline version (border only, transparent background)',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    size: {
      description: 'Button size',
      control: { type: 'select' },
      options: variantsList.size.compact,
      table: {
        category: 'Appearance',
        type: { summary: variantsList.size.compact.join(' | ') },
        defaultValue: { summary: 'medium' },
      },
    },
    fullWidth: {
      description: 'Full width button (width: 100%)',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    // Behavior
    disabled: {
      description: 'Disable button (reduces opacity to 50%)',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    loading: {
      description: 'Display loading state',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    // Link
    url: {
      description: 'Destination URL (transforms button to link)',
      control: { type: 'text' },
      table: {
        category: 'Link',
        type: { summary: 'string' },
      },
    },
    target: {
      description: 'Link target attribute',
      control: { type: 'select' },
      options: ['_self', '_blank'],
      table: {
        category: 'Link',
        type: { summary: '_self | _blank' },
        defaultValue: { summary: '_self' },
      },
    },
  },
};

export const Default = {
  render: (args) => buttonTwig(args),
  args: { ...data },
};

export const AllVariants = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${variantsList.color.components.button.map(variant => buttonTwig({ label: variant.charAt(0).toUpperCase() + variant.slice(1), variant })).join('')}
    </div>
  `,
};

export const AllOutlines = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${variantsList.color.components.button.map(variant => buttonTwig({ label: variant.charAt(0).toUpperCase() + variant.slice(1), variant, outline: true })).join('')}
    </div>
  `,
};

export const AllSizes = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${variantsList.size.compact.map(size => buttonTwig({ label: size.charAt(0).toUpperCase() + size.slice(1), variant: 'primary', size })).join('')}
    </div>
  `,
};

export const WithIcons = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${buttonTwig({ label: 'Rechercher', variant: 'primary', icon: 'search', iconPosition: 'left' })}
      ${buttonTwig({ label: 'Suivant', variant: 'primary', icon: 'arrow-right', iconPosition: 'right' })}
      ${buttonTwig({ icon: 'close', variant: 'primary', size: 'medium' })}
    </div>
  `,
};

export const FullWidth = {
  render: () => buttonTwig({ label: 'Full Width Button', variant: 'primary', fullWidth: true }),
};

export const Loading = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${buttonTwig({ label: 'Chargement...', variant: 'primary', loading: true })}
      ${buttonTwig({ label: 'Chargement...', variant: 'secondary', outline: true, loading: true })}
    </div>
  `,
};

export const Disabled = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${buttonTwig({ label: 'Désactivé', variant: 'primary', disabled: true })}
      ${buttonTwig({ label: 'Désactivé', variant: 'secondary', outline: true, disabled: true })}
    </div>
  `,
};
