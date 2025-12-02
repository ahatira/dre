import alertTwig from './alert.twig';
import data from './alert.yml';

const settings = {
  title: 'Components/Alert',
  tags: ['autodocs'],
  render: (args) => alertTwig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        component:
          'Semantic alert component for status messages with accessible ARIA roles and tokenized design system.',
      },
    },
  },
  argTypes: {
    variant: {
      description: 'Semantic variant (info, success, warning, danger, primary, secondary)',
      control: { type: 'select' },
      options: ['info', 'success', 'warning', 'danger', 'primary', 'secondary'],
      table: {
        category: 'Appearance',
        type: {
          summary: 'info | success | warning | danger | primary | secondary',
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
      description: 'Show icon (info=infos, success=check, warning=help, danger=close)',
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
    attributes: {
      description: 'Drupal attributes object for root element',
      table: {
        category: 'Layout',
        type: { summary: 'Drupal.Attribute' },
      },
    },
  },
};

export const Default = {
  render: (args) => alertTwig(args),
  args: { ...data },
};

export const SemanticVariants = {
  name: 'Semantic Colors',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${alertTwig({ variant: 'info', title: 'New Property Listing', message: 'A new commercial property matching your criteria has been added to the market.' })}
      ${alertTwig({ variant: 'success', title: 'Offer Accepted', message: 'Your offer on 123 Main Street has been accepted. Next steps will be sent via email.' })}
      ${alertTwig({ variant: 'warning', title: 'Document Expiring', message: 'Your property insurance policy expires in 30 days. Please review and renew to avoid coverage gaps.' })}
      ${alertTwig({ variant: 'danger', title: 'Payment Failed', message: 'Your monthly payment could not be processed. Please update your payment method to avoid service interruption.' })}
    </div>
  `,
};

export const BrandVariants = {
  name: 'Brand Colors',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${alertTwig({ variant: 'primary', title: 'BNP Paribas RealEstate', message: 'Primary brand color for featured announcements and important highlights.' })}
      ${alertTwig({ variant: 'secondary', title: 'Special Promotion', message: 'Secondary brand color for promotional content and accent messages.' })}
    </div>
  `,
};

export const BehaviorShowcase = {
  name: 'Behaviors',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Dismissible Alerts</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-4);">
          ${alertTwig({ variant: 'info', title: 'Viewing Scheduled', message: 'Your property viewing is confirmed for tomorrow at 2 PM.', dismissible: true })}
          ${alertTwig({ variant: 'success', message: 'Saved to favorites!', dismissible: true })}
        </div>
      </div>
      
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Without Icon</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-4);">
          ${alertTwig({ variant: 'info', message: 'Text-only alert for minimal design.', icon: false })}
          ${alertTwig({ variant: 'warning', title: 'Notice', message: 'Alert without visual icon indicator.', icon: false })}
        </div>
      </div>
      
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Without Title</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-4);">
          ${alertTwig({ variant: 'success', message: 'Simple success message without title.' })}
          ${alertTwig({ variant: 'danger', message: 'Connection lost. Reconnecting...' })}
        </div>
      </div>
    </div>
  `,
};

export const LayoutShowcase = {
  name: 'Layout Variants',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Default Spacing</h3>
        ${alertTwig({ variant: 'info', title: 'Property Inspection', message: 'The inspection report for 456 Oak Avenue has been uploaded to your dashboard.' })}
      </div>
      
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Compact (Sidebars, Mobile)</h3>
        <div style="max-width: 400px; display: flex; flex-direction: column; gap: var(--size-3);">
          ${alertTwig({ variant: 'info', message: 'New message received.', compact: true, dismissible: true })}
          ${alertTwig({ variant: 'warning', message: 'Low battery.', compact: true })}
          ${alertTwig({ variant: 'success', message: 'Saved!', compact: true })}
        </div>
      </div>
    </div>
  `,
};

export const ContentShowcase = {
  name: 'Rich Content',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${alertTwig({
        variant: 'info',
        title: 'Market Update Available',
        message: '<p>New market analysis report is ready. <a href="#">Download now</a> to view the latest trends and insights.</p>',
      })}
      ${alertTwig({
        variant: 'success',
        message: '<p><strong>Contract Signed!</strong></p><p>Your lease agreement has been executed. Documents will be available within 24 hours.</p>',
      })}
      ${alertTwig({
        variant: 'warning',
        title: 'Action Required',
        message: '<p>Your mortgage pre-approval expires in 7 days.</p><p><a href="#">Renew now</a> to maintain your rate lock.</p>',
        dismissible: true,
      })}
    </div>
  `,
};

export default settings;
