import menuSecondary from './menu-secondary.twig';
import data from './menu-secondary.yml';

export default {
  title: 'Collections/Menu Secondary',
  tags: ['autodocs'],
  parameters: {
    layout: 'padded',
  },
  argTypes: {
    items: {
      description: 'Drupal-compatible menu items (title, url, below for submenu)',
      control: 'object',
      table: {
        category: 'Structure',
        type: { summary: 'array' },
      },
    },
  },
};

export const Default = {
  render: (args) => menuSecondary(args),
  args: {
    ...data,
  },
};

export const LoggedOut = {
  render: (args) => menuSecondary(args),
  args: {
    items: [
      { title: 'What are you looking for?', url: '#' },
      { title: 'Find a property', url: '/properties' },
      { title: 'Log in / Sign up', url: '/login' },
      { title: 'Contact us', url: '/contact' },
    ],
  },
};

export const WithDropdown = {
  render: (args) => menuSecondary(args),
  args: {
    ...data,
  },
};
