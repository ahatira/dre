import markup from './menu-item.twig';
import data from './menu-item.yml';

export default {
  title: 'Components/Menu Item',
  tags: ['autodocs'],
  render: (args) => `<ul>${markup(args)}</ul>`,
  args: data,
  argTypes: {
    label: {
      control: 'text',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    href: {
      control: 'text',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    icon: {
      control: 'text',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    badge: {
      control: 'text',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    badge_variant: {
      control: 'select',
      options: [
        'primary',
        'secondary',
        'success',
        'danger',
        'warning',
        'info',
        'gold',
        'light',
        'dark',
      ],
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
      },
    },
    active: {
      control: 'boolean',
      table: {
        category: 'State',
        type: { summary: 'boolean' },
      },
    },
    disabled: {
      control: 'boolean',
      table: {
        category: 'State',
        type: { summary: 'boolean' },
      },
    },
    submenu: {
      control: 'object',
      table: {
        category: 'Content',
        type: { summary: 'array' },
      },
    },
  },
};

export const Default = { args: data };

export const Active = {
  args: { ...data, active: true },
};

export const Disabled = {
  args: { ...data, disabled: true },
};

export const WithIcon = {
  args: { ...data, icon: 'home' },
};

export const WithBadge = {
  args: { ...data, badge: '3', badge_variant: 'danger' },
};

export const WithSubmenu = {
  args: {
    ...data,
    label: 'Main Menu',
    submenu: [
      { label: 'Submenu 1', href: '#sub1' },
      { label: 'Submenu 2', href: '#sub2' },
      { label: 'Submenu 3', href: '#sub3' },
    ],
  },
};
