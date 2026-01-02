import blockCta from './block-cta.twig';
import blockCtaData from './block-cta.yml';

export default {
  title: 'Layouts/Blocks/CTA',
  tags: ['autodocs'],
  argTypes: {
    plugin_id: {
      control: 'text',
      description: 'Drupal block plugin ID',
      table: { category: 'Drupal', defaultValue: { summary: 'block_cta' } },
    },
    configuration: {
      control: 'object',
      description: 'Block configuration (expects provider at minimum)',
      table: { category: 'Drupal', defaultValue: { summary: '{ provider: "custom" }' } },
    },
    label: {
      control: 'text',
      description: 'Block title (empty by default)',
      table: { category: 'Drupal', defaultValue: { summary: '' } },
    },
    button: {
      control: 'object',
      description: 'Button properties',
      table: { category: 'Button' },
    },
  },
};

export const CTASearch = {
  render: (args) => blockCta(args),
  args: {
    ...blockCtaData,
    button: {
      label: 'What are you looking for ?',
      variant: 'primary',
      outline: true,
      fullWidth: true,
      icon: null,
      url: '#',
    },
  },
};

export const CTAContact = {
  render: (args) => blockCta(args),
  args: {
    ...blockCtaData,
    button: {
      label: 'Contact us',
      variant: 'secondary',
      outline: false,
      fullWidth: true,
      icon: 'email-outline',
      url: '#',
    },
  },
};

export const CTAFindProperty = {
  render: (args) => blockCta(args),
  args: {
    ...blockCtaData,
    button: {
      label: 'Find a property',
      variant: '',
      icon: null,
      url: '#',
      class: 'no-underline',
      component: 'link',
    },
  },
};
