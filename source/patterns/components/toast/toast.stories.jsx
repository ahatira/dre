import markup from './toast.twig';
import data from './toast.yml';

const settings = {
  title: 'Components/Toast',
  tags: ['autodocs'],
  argTypes: {
    message: {
      control: 'text',
      description: 'Notification message',
      table: { category: 'Content' },
    },
    type: {
      control: { type: 'select' },
      options: ['success', 'error', 'warning', 'info'],
      description: 'Toast type/variant',
      table: { category: 'State' },
    },
    dismissible: {
      control: 'boolean',
      description: 'Show close button',
      table: { category: 'Configuration' },
    },
    show: {
      control: 'boolean',
      description: 'Display toast',
      table: { category: 'State' },
    },
  },
};

export const Success = {
  name: 'Success',
  render: (args) => markup(args),
  args: { ...data, type: 'success', message: 'Property listed successfully!' },
};

export const ErrorToast = {
  name: 'Error',
  render: (args) => markup(args),
  args: { ...data, type: 'error', message: 'An error occurred. Please try again.' },
};

export const Warning = {
  name: 'Warning',
  render: (args) => markup(args),
  args: { ...data, type: 'warning', message: 'This action cannot be undone.' },
};

export const Info = {
  name: 'Info',
  render: (args) => markup(args),
  args: { ...data, type: 'info', message: 'New properties available in your area.' },
};

export default settings;
