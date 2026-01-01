import blockUserAccount from './block-user-account.twig';
import blockUserAccountData from './block-user-account.yml';
import './block-user-account.css';
import './block-user-account.js';

export default {
  title: 'Layouts/Blocks/User Account',
  tags: ['autodocs'],
  argTypes: {
    plugin_id: {
      control: 'text',
      description: 'Drupal block plugin ID',
      table: { category: 'Drupal', defaultValue: { summary: 'block_user_account' } },
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
    logged_in: {
      control: 'boolean',
      description: 'User authentication state',
      table: { category: 'User', defaultValue: { summary: 'false' } },
    },
    user_name: {
      control: 'text',
      description: 'Display name when logged in',
      table: { category: 'User', defaultValue: { summary: 'User' } },
    },
    menu_items: {
      control: 'object',
      description: 'Array of menu items when logged in',
      table: { category: 'Content' },
    },
    login_button: {
      control: 'object',
      description: 'Login button properties when logged out',
      table: { category: 'Content' },
    },
  },
};

/**
 * Logged In: User menu dropdown with account links
 */
export const LoggedIn = {
  render: (args) => blockUserAccount(args),
  args: {
    ...blockUserAccountData,
    configuration: { provider: 'custom' },
    logged_in: true,
    user_name: 'Enzo',
    menu_items: [
      {
        label: 'My account',
        url: '/user/account',
        icon: 'account',
        type: 'link',
      },
      {
        label: 'My favorites',
        url: '/user/favorites',
        icon: 'heart',
        type: 'link',
      },
      {
        label: 'My alerts',
        url: '/user/alerts',
        icon: 'alert',
        type: 'link',
      },
      {
        label: 'Logout',
        url: '/user/logout',
        icon: 'exit',
        type: 'logout',
      },
    ],
  },
};

/**
 * Logged Out: Login button
 */
export const LoggedOut = {
  render: (args) => blockUserAccount(args),
  args: {
    ...blockUserAccountData,
    configuration: { provider: 'custom' },
    logged_in: false,
    login_button: {
      label: 'Log in / Sign up',
      url: '/user/login',
      variant: 'primary',
      icon: 'account',
      fullWidth: false,
    },
  },
};
