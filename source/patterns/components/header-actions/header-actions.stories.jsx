import headerActions from './header-actions.twig';
import data from './header-actions.yml';

export default {
  title: 'Components/Header Actions',
  tags: ['autodocs'],
  argTypes: {
    actions: {
      description: 'Array of action items (buttons, icons, language selector)',
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
      { type: 'button', label: 'Find a property', variant: 'primary', href: '/properties' },
      { type: 'button', label: 'Log in', variant: 'outline', icon: 'user', href: '/login' },
      { type: 'icon', icon: 'search', label: 'Search', href: '/search' },
      { type: 'language', current: 'gb', label: 'En' },
    ],
  },
};

export const LoggedIn = {
  render: (args) => headerActions(args),
  args: {
    actions: [
      { type: 'button', label: 'Find a property', variant: 'primary', href: '/properties' },
      { type: 'icon', icon: 'heart', label: 'Favorites', href: '/favorites' },
      { type: 'icon', icon: 'user', label: 'Account', href: '/account' },
      { type: 'icon', icon: 'search', label: 'Search', href: '/search' },
      { type: 'language', current: 'gb', label: 'En' },
    ],
  },
};

export const MinimalMobile = {
  render: (args) => headerActions(args),
  args: {
    actions: [
      { type: 'icon', icon: 'search', label: 'Search', href: '/search' },
      { type: 'icon', icon: 'user', label: 'Account', href: '/account' },
      { type: 'language', current: 'gb', label: 'En' },
    ],
  },
  parameters: {
    viewport: {
      defaultViewport: 'mobile1',
    },
  },
};
