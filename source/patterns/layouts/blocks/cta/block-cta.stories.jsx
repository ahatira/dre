import blockCta from './block-cta.twig';
import blockCtaData from './block-cta.yml';
import './block-cta.css';

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

export const CTALogin = {
  render: (args) => blockCta(args),
  args: {
    ...blockCtaData,
    button: {
      label: 'Log in / Sign up',
      variant: 'primary',
      outline: false,
      fullWidth: true,
      icon: 'account',
      url: '#',
    },
  },
};
