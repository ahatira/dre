import alertTwig from './alert.twig';
import data from './alert.yml';
import './alert.js';

const settings = {
  title: 'Components/Alert',
  tags: ['autodocs'],
  render: (args) => alertTwig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        component:
          'Semantic alert component for status messages with accessible roles and tokenized design.',
      },
    },
  },
  argTypes: {
    variant: {
      description: 'Semantic variant of the alert',
      control: { type: 'select' },
      options: [
        'info',
        'success',
        'warning',
        'error',
        'info-subtle',
        'success-subtle',
        'warning-subtle',
        'error-subtle',
        'default',
        'primary',
        'secondary',
        'light',
        'dark',
      ],
      table: {
        category: 'Appearance',
        type: {
          summary:
            'info | success | warning | error | info-subtle | success-subtle | warning-subtle | error-subtle | default | primary | secondary | light | dark',
        },
        defaultValue: { summary: 'info' },
      },
    },
    title: {
      description: 'Optional title text',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '""' },
      },
    },
    message: {
      description: 'Message content (supports HTML)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '""' },
      },
    },
    icon: {
      description: 'Show icon (icons: info=infos, success=check, warning=help, error=close)',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'true' },
      },
    },
    dismissible: {
      description: 'Show close button with JavaScript dismiss behavior',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    compact: {
      description: 'Reduced padding for dense layouts',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
  },
};

export const Default = {
  render: (args) => alertTwig(args),
  args: { ...data },
};

export const AllVariants = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${alertTwig({ variant: 'info', title: 'Information', message: 'This is an informational alert message.' })}
      ${alertTwig({ variant: 'success', title: 'Success', message: 'Operation completed successfully.' })}
      ${alertTwig({ variant: 'warning', title: 'Warning', message: 'Please review your input before continuing.' })}
      ${alertTwig({ variant: 'error', title: 'Error', message: 'An error occurred. Please try again.' })}
      ${alertTwig({ variant: 'default', title: 'Default', message: 'Neutral alert for general messages.' })}
      ${alertTwig({ variant: 'primary', title: 'Primary', message: 'Brand primary green alert.' })}
      ${alertTwig({ variant: 'secondary', title: 'Secondary', message: 'Brand secondary pink alert.' })}
      ${alertTwig({ variant: 'light', title: 'Light', message: 'Light background for subtle alerts.' })}
      ${alertTwig({ variant: 'dark', title: 'Dark', message: 'Dark background with light text.' })}
    </div>
  `,
};

export const SubtleVariants = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${alertTwig({ variant: 'info-subtle', title: 'Information', message: 'Subtle info alert with soft background and colored text.' })}
      ${alertTwig({ variant: 'success-subtle', title: 'Success', message: 'Subtle success alert for gentle confirmation.' })}
      ${alertTwig({ variant: 'warning-subtle', title: 'Warning', message: 'Subtle warning with light background and accent border.' })}
      ${alertTwig({ variant: 'error-subtle', title: 'Error', message: "You can't compare more than 4 ads at the same time. Delete one to add this one." })}
    </div>
  `,
};

export const WithoutTitle = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${alertTwig({ variant: 'info', message: 'Simple info message without title.' })}
      ${alertTwig({ variant: 'success', message: 'Operation successful!' })}
      ${alertTwig({ variant: 'warning', message: 'Please verify your email address.' })}
      ${alertTwig({ variant: 'error', message: 'Connection lost. Reconnecting...' })}
    </div>
  `,
};

export const WithoutIcon = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${alertTwig({ variant: 'info', title: 'Information', message: 'Alert without icon.', icon: false })}
      ${alertTwig({ variant: 'success', message: 'Text-only success message.', icon: false })}
      ${alertTwig({ variant: 'warning', message: 'Warning without visual icon.', icon: false })}
      ${alertTwig({ variant: 'error', title: 'Error', message: 'Error message, no icon.', icon: false })}
    </div>
  `,
};

export const BrandColors = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${alertTwig({ variant: 'primary', title: 'Primary Brand', message: 'BNP Paribas RealEstate primary green.' })}
      ${alertTwig({ variant: 'secondary', title: 'Secondary Brand', message: 'BNP Paribas RealEstate accent pink.' })}
      ${alertTwig({ variant: 'light', message: 'Light subtle variant for minimal alerts.' })}
      ${alertTwig({ variant: 'dark', message: 'Dark variant with light text for contrasting sections.' })}
    </div>
  `,
};

export const Dismissible = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${alertTwig({ variant: 'info', title: 'Tip', message: 'You can dismiss this message.', dismissible: true })}
      ${alertTwig({ variant: 'success', message: 'Changes saved successfully.', dismissible: true })}
      ${alertTwig({ variant: 'warning', message: 'Low disk space available.', dismissible: true })}
    </div>
  `,
};

export const Compact = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-3); max-width: 320px;">
      ${alertTwig({ variant: 'info', message: 'Compact info alert for sidebars.', compact: true })}
      ${alertTwig({ variant: 'success', message: 'Saved!', compact: true, dismissible: true })}
      ${alertTwig({ variant: 'warning', message: 'Low battery.', compact: true })}
      ${alertTwig({ variant: 'error', message: 'Error!', compact: true })}
    </div>
  `,
};

export const WithHTML = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${alertTwig({
        variant: 'info',
        title: 'Update Available',
        message:
          '<p>A new version is available. <a href="#">Download now</a> to get the latest features.</p>',
      })}
      ${alertTwig({
        variant: 'success',
        message:
          '<p><strong>Payment received!</strong></p><p>Your order will be processed within 24 hours.</p>',
      })}
      ${alertTwig({
        variant: 'warning',
        title: 'Action Required',
        message:
          '<p>Your subscription expires in 3 days.</p><p><a href="#">Renew now</a> to avoid interruption.</p>',
        dismissible: true,
      })}
    </div>
  `,
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Form Submission Success</h3>
        ${alertTwig({
          variant: 'success',
          title: 'Form Submitted',
          message:
            'Thank you for your submission. We will review your application and contact you within 5 business days.',
          dismissible: true,
        })}
      </div>

      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Session Expiration Warning</h3>
        ${alertTwig({
          variant: 'warning',
          title: 'Session Expiring Soon',
          message:
            'Your session will expire in 2 minutes due to inactivity. Click anywhere to stay logged in.',
        })}
      </div>

      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">System Error</h3>
        ${alertTwig({
          variant: 'error',
          title: 'Connection Error',
          message:
            'Unable to connect to the server. Please check your internet connection and try again.',
        })}
      </div>

      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Inline Help (Compact)</h3>
        <div style="max-width: 400px;">
          ${alertTwig({
            variant: 'info',
            message: 'Pro tip: Use keyboard shortcuts to navigate faster.',
            compact: true,
            dismissible: true,
          })}
        </div>
      </div>
    </div>
  `,
};

export default settings;
