import twig from './consultant-card.twig';
import data from './consultant-card.yml';

export default {
  title: 'Components/Consultant Card',
  tags: ['autodocs'],
  render: (args) => twig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        component:
          'Advisor profile card with avatar, name, phone, and CTA. Token-First, BEM, icons via data-icon.',
      },
    },
  },
  argTypes: {
    name: { control: 'text', table: { category: 'Content', type: { summary: 'string' } } },
    title: { control: 'text', table: { category: 'Content', type: { summary: 'string' } } },
    'avatar.url': { control: 'text', table: { category: 'Content', type: { summary: 'string' } } },
    'avatar.alt': { control: 'text', table: { category: 'Content', type: { summary: 'string' } } },
    phone: { control: 'text', table: { category: 'Content', type: { summary: 'string' } } },
    'cta.text': { control: 'text', table: { category: 'CTA', type: { summary: 'string' } } },
    'cta.url': { control: 'text', table: { category: 'CTA', type: { summary: 'string' } } },
  },
};

export const Default = { args: data };
