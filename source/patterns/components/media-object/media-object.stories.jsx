import twig from './media-object.twig';
import data from './media-object.yml';

export default {
  title: 'Components/Media Object',
  tags: ['autodocs'],
  render: (args) => twig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        component:
          'Semantic label with optional icon. Variants: neutral, primary, success, danger, warning, info, gold, light, dark. States: default, pill, with-icon. Icons via data-icon. Props: label, variant, pill, icon.',
      },
    },
  },
  argTypes: {
    label: { control: 'text', table: { category: 'Content', type: { summary: 'string' } } },
    variant: {
      control: { type: 'select' },
      options: [
        'neutral',
        'primary',
        'success',
        'danger',
        'warning',
        'info',
        'gold',
        'light',
        'dark',
      ],
      table: { category: 'Appearance', type: { summary: 'string' } },
    },
    pill: {
      control: 'boolean',
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    icon: {
      control: { type: 'select' },
      options: ['check', 'info', null],
      table: { category: 'Appearance', type: { summary: 'string|null' } },
    },
  },
};

export const Default = { args: data };

export const WithIcon = { args: { ...data, icon: 'info' } };

export const Pill = { args: { ...data, pill: true } };

export const Variants = {
  render: () => {
    const variants = [
      'neutral',
      'primary',
      'success',
      'danger',
      'warning',
      'info',
      'gold',
      'light',
      'dark',
    ];
    return `
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:var(--size-3)">
        ${variants.map((v) => twig({ label: `Label ${v}`, variant: v, icon: 'check' })).join('')}
      </div>
    `;
  },
};
