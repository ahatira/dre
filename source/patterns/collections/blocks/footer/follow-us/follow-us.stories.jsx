import followUsTwigTemplate from './follow-us.twig';
import followUsData from './follow-us.yml';

function renderFollowUs(args) {
  return followUsTwigTemplate(args);
}

export default {
  title: 'Collections/Blocks/Footer/Follow Us',
  tags: ['autodocs'],
  render: renderFollowUs,
  argTypes: {
    label: {
      description: 'Section title/label',
      control: 'text',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    label_level: {
      description: 'HTML heading level for label (h2-h6)',
      control: 'select',
      options: ['h2', 'h3', 'h4', 'h5', 'h6'],
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
      },
    },
    items: {
      description: 'Array of social media items with label, icon, and url',
      control: 'object',
      table: {
        category: 'Content',
        type: { summary: 'array' },
      },
    },
  },
};

export const Default = {
  args: followUsData,
};

export const WithMoreNetworks = {
  args: {
    label: 'Suivez-nous',
    label_level: 'h4',
    items: [
      { label: 'Facebook', icon: 'facebook', url: '#', external: true },
      { label: 'LinkedIn', icon: 'linkedin', url: '#', external: true },
      { label: 'Twitter', icon: 'twitter', url: '#', external: true },
      { label: 'YouTube', icon: 'youtube', url: '#', external: true },
      { label: 'Instagram', icon: 'email', url: '#', external: true },
    ],
  },
};

export const Minimal = {
  args: {
    label: 'Connect',
    label_level: 'h4',
    items: [
      { label: 'Facebook', icon: 'facebook', url: '#', external: true },
      { label: 'LinkedIn', icon: 'linkedin', url: '#', external: true },
    ],
  },
};
