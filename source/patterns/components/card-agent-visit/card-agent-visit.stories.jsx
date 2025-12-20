import twig from './card-agent-visit.twig';
import data from './card-agent-visit.yml';

export default {
  title: 'Components/Card Agent Visit',
  tags: ['autodocs'],
  render: (args) => twig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        component:
          'Invitation to schedule an agent visit. Uses heading plus CTA with calendar icon. Token-First, BEM, icons via data-icon.',
      },
    },
  },
  argTypes: {
    title: { control: 'text', table: { category: 'Content', type: { summary: 'string' } } },
    'cta.text': { control: 'text', table: { category: 'CTA', type: { summary: 'string' } } },
    'cta.url': { control: 'text', table: { category: 'CTA', type: { summary: 'string' } } },
    'cta.ariaLabel': { control: 'text', table: { category: 'Accessibility', type: { summary: 'string' } } },
  },
};

export const Default = { args: data };
