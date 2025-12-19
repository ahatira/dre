import headerActions from './header-actions.twig';
import data from './header-actions.yml';

export default {
  title: 'Components/Header Actions',
  tags: ['autodocs'],
  parameters: {
    layout: 'padded',
  },
  argTypes: {
    actions: {
      description: 'Action items array supporting button | link | icon | user | separator',
      control: 'object',
      table: {
        category: 'Structure',
        type: { summary: 'array' },
      },
    },
  },
};

export const Default = {
  render: (args) => headerActions(args),
  args: {
    ...data,
  },
};

export const LoggedOut = {
  render: (args) => headerActions(args),
  args: {
    actions: [
      {
        type: 'button',
        label: 'What are you looking for?',
        variant: 'primary',
        outline: true,
        icon: 'search',
        url: '#',
        size: 'small',
        searchTrigger: true,
      },
      { type: 'separator' },
      { type: 'link', label: 'Find a property', url: '/properties' },
      { type: 'separator' },
      {
        type: 'button',
        label: 'Log in / Sign up',
        variant: 'primary',
        icon: 'user',
        url: '/login',
        size: 'small',
      },
      { type: 'button', label: 'Contact us', variant: 'secondary', url: '/contact', size: 'small' },
      { type: 'icon', icon: 'heart', label: 'Favorites', href: '/favorites', badge: '1' },
    ],
  },
};

export const LoggedIn = {
  render: (args) => headerActions(args),
  args: {
    actions: [
      {
        type: 'button',
        label: 'What are you looking for?',
        variant: 'primary',
        outline: true,
        icon: 'search',
        url: '#',
        size: 'small',
        searchTrigger: true,
      },
      { type: 'separator' },
      { type: 'link', label: 'Find a property', url: '/properties' },
      { type: 'separator' },
      { type: 'user', name: 'Enzo' },
      { type: 'separator' },
      { type: 'button', label: 'Contact us', variant: 'secondary', url: '/contact', size: 'small' },
      { type: 'icon', icon: 'heart', label: 'Favorites', href: '/favorites', badge: '2' },
    ],
  },
};

export const MinimalMobile = {
  render: (args) => headerActions(args),
  args: {
    actions: [
      { type: 'icon', icon: 'search', label: 'Search', href: '/search' },
      { type: 'icon', icon: 'user', label: 'Account', href: '/account' },
      { type: 'icon', icon: 'heart', label: 'Favorites', href: '/favorites' },
    ],
  },
  parameters: {
    viewport: {
      defaultViewport: 'mobile1',
    },
  },
};
