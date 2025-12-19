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
      description: 'Menu items array (button | link | icon | user types)',
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
      {
        type: 'button',
        label: 'What are you looking for?',
        variant: 'primary',
        outline: true,
        icon: 'search',
        url: '#',
        size: 'small',
        searchTrigger: true,
        hasSeparator: true,
      },
      {
        type: 'link',
        label: 'Find a property',
        url: '/properties',
        hasSeparator: true,
      },
      {
        type: 'user',
        name: 'Log in / Sign up',
        hasSeparator: true,
        menu: null,
      },
      {
        type: 'button',
        label: 'Contact us',
        variant: 'secondary',
        url: '/contact',
        size: 'small',
        hasSeparator: false,
      },
      {
        type: 'icon',
        icon: 'heart',
        ariaLabel: 'Favorites',
        href: '/favorites',
        badge: null,
        hasSeparator: false,
      },
    ],
  },
};

export const LoggedIn = {
  render: (args) => menuSecondary(args),
  args: {
    ...data,
  },
};

export const UserMenuOpen = {
  render: (args) => menuSecondary(args),
  args: {
    items: [
      {
        type: 'button',
        label: 'What are you looking for?',
        variant: 'primary',
        outline: true,
        icon: 'search',
        url: '#',
        size: 'small',
        searchTrigger: true,
        hasSeparator: true,
      },
      {
        type: 'link',
        label: 'Find a property',
        url: '/properties',
        hasSeparator: true,
      },
      {
        type: 'user',
        name: 'Enzo',
        open: true,
        hasSeparator: true,
        menu: [
          {
            type: 'link',
            label: 'Mon compte',
            url: '/account',
            icon: 'user',
          },
          {
            type: 'link',
            label: 'Mes favoris',
            url: '/favorites',
            icon: 'heart',
          },
          {
            type: 'link',
            label: 'Mes alertes',
            url: '/alerts',
            icon: 'notifications',
          },
          {
            type: 'separator',
          },
          {
            type: 'link',
            label: 'Se déconnecter',
            url: '/logout',
            icon: 'logout',
          },
        ],
      },
      {
        type: 'button',
        label: 'Contact us',
        variant: 'secondary',
        url: '/contact',
        size: 'small',
        hasSeparator: false,
      },
      {
        type: 'icon',
        icon: 'heart',
        ariaLabel: 'Favorites',
        href: '/favorites',
        badge: '2',
        hasSeparator: false,
      },
    ],
  },
};
