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
          'Semantic alert component with 8 variants, flexible HTML content, and optional dismissal functionality.',
      },
    },
  },
  argTypes: {
    variant: {
      description: 'Semantic variant (8 options)',
      control: { type: 'select' },
      options: ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'],
      table: {
        category: 'Appearance',
        type: {
          summary: 'primary | secondary | success | danger | warning | info | light | dark',
        },
        defaultValue: { summary: 'primary' },
      },
    },
    content: {
      description: 'Free HTML content (headings, paragraphs, links, icons optional)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string (HTML)' },
        defaultValue: { summary: '""' },
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
    rounded: {
      description: 'Apply border radius (default: no radius)',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    title: {
      description: 'Alert title (heading, composed via heading atom)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    text: {
      description: 'Alert body text (composed via text atom)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    icon: {
      description: 'Optional icon name (composed via icon atom)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    actions: {
      description: 'Array of button props for action buttons (composed via button atoms)',
      control: { type: 'object' },
      table: {
        category: 'Content',
        type: { summary: 'array<ButtonProps>' },
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

export const AllVariants = {
  name: 'All 8 Variants',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${alertTwig({ variant: 'primary', content: 'A simple <strong>primary</strong> alert—check it out!' })}
      ${alertTwig({ variant: 'secondary', content: 'A simple <strong>secondary</strong> alert—check it out!' })}
      ${alertTwig({ variant: 'success', content: 'A simple <strong>success</strong> alert—check it out!' })}
      ${alertTwig({ variant: 'danger', content: 'A simple <strong>danger</strong> alert—check it out!' })}
      ${alertTwig({ variant: 'warning', content: 'A simple <strong>warning</strong> alert—check it out!' })}
      ${alertTwig({ variant: 'info', content: 'A simple <strong>info</strong> alert—check it out!' })}
      ${alertTwig({ variant: 'light', content: 'A simple <strong>light</strong> alert—check it out!' })}
      ${alertTwig({ variant: 'dark', content: 'A simple <strong>dark</strong> alert—check it out!' })}
    </div>
  `,
};

export const WithLinks = {
  name: 'Alert Links',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${alertTwig({
        variant: 'primary',
        content:
          'A property listing with <a href="#" class="ps-alert-link">an example link</a>. Give it a click if you like.',
      })}
      ${alertTwig({
        variant: 'success',
        content:
          'Offer accepted! <a href="#" class="ps-alert-link">View contract details</a> in your dashboard.',
      })}
      ${alertTwig({
        variant: 'warning',
        content:
          'Your insurance expires soon. <a href="#" class="ps-alert-link">Renew now</a> to avoid gaps.',
      })}
      ${alertTwig({
        variant: 'danger',
        content:
          'Payment failed. <a href="#" class="ps-alert-link">Update payment method</a> immediately.',
      })}
    </div>
  `,
};

export const WithHeadings = {
  name: 'Additional Content',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${alertTwig({
        variant: 'success',
        content: `
          <h4 class="ps-alert-heading">Property Inspection Complete!</h4>
          <p>Aww yeah, you successfully completed the inspection for 123 Main Street. The detailed report has been uploaded to your dashboard and is now available for review.</p>
          <hr>
          <p style="margin-bottom: 0;">Whenever you're ready, proceed with the final offer or schedule a follow-up viewing with the property agent.</p>
        `,
      })}
      ${alertTwig({
        variant: 'info',
        content: `
          <h4 class="ps-alert-heading">Market Analysis Available</h4>
          <p>A comprehensive market analysis for downtown commercial properties has been prepared by your real estate advisor.</p>
          <p style="margin-bottom: 0;"><a href="#" class="ps-alert-link">Download report</a> to review key insights and investment opportunities.</p>
        `,
      })}
    </div>
  `,
};

export const WithIcons = {
  name: 'With Icons (Optional)',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${alertTwig({
        variant: 'primary',
        content:
          '<span data-icon="infos" aria-hidden="true" style="margin-right: var(--size-3); font-size: var(--font-size-3);"></span> Featured property listing in your preferred area',
      })}
      ${alertTwig({
        variant: 'success',
        content:
          '<span data-icon="check" aria-hidden="true" style="margin-right: var(--size-3); font-size: var(--font-size-3);"></span> Viewing confirmed for tomorrow at 2 PM',
      })}
      ${alertTwig({
        variant: 'warning',
        content:
          '<span data-icon="help" aria-hidden="true" style="margin-right: var(--size-3); font-size: var(--font-size-3);"></span> Document expires in 30 days',
      })}
      ${alertTwig({
        variant: 'danger',
        content:
          '<span data-icon="close" aria-hidden="true" style="margin-right: var(--size-3); font-size: var(--font-size-3);"></span> Payment processing failed',
      })}
    </div>
  `,
};

export const WithRoundedCorners = {
  name: 'Rounded vs Sharp',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Sharp corners (default):</p>
        ${alertTwig({
          variant: 'primary',
          content: 'Default alert with sharp corners (no border-radius)',
        })}
        ${alertTwig({
          variant: 'success',
          content: 'Success alert with sharp corners',
        })}
      </div>
      <div>
        <p style="margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Rounded corners (rounded: true):</p>
        ${alertTwig({
          variant: 'primary',
          content: 'Alert with rounded corners (border-radius applied)',
          rounded: true,
        })}
        ${alertTwig({
          variant: 'success',
          content: 'Success alert with rounded corners',
          rounded: true,
        })}
      </div>
    </div>
  `,
};

export const DismissibleAlerts = {
  name: 'Dismissible',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${alertTwig({
        variant: 'warning',
        content:
          '<strong>Holy guacamole!</strong> You should check in on some of those fields below.',
        dismissible: true,
      })}
      ${alertTwig({
        variant: 'success',
        content: 'Your property has been saved to favorites!',
        dismissible: true,
      })}
      ${alertTwig({
        variant: 'danger',
        content: `
          <h4 class="ps-alert-heading">Urgent: Payment Overdue</h4>
          <p style="margin-bottom: 0;">Your monthly payment is now 15 days overdue. Please update your payment information immediately to avoid service interruption.</p>
        `,
        dismissible: true,
      })}
    </div>
  `,
};

export const RealEstateExamples = {
  name: 'Real Estate Use Cases',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${alertTwig({
        variant: 'info',
        content: `
          <h4 class="ps-alert-heading">New Property Match</h4>
          <p style="margin-bottom: 0;">A commercial property in your preferred area has been listed. 2,500 sqft office space with parking. <a href="#" class="ps-alert-link">View listing</a></p>
        `,
        dismissible: true,
      })}
      ${alertTwig({
        variant: 'success',
        content:
          '<strong>Offer Accepted!</strong> Your offer on 456 Oak Avenue has been accepted by the seller. Contract details will be sent within 24 hours.',
      })}
      ${alertTwig({
        variant: 'warning',
        content:
          '<strong>Insurance Renewal:</strong> Your property insurance policy expires in 30 days. <a href="#" class="ps-alert-link">Review and renew</a> to maintain coverage.',
      })}
      ${alertTwig({
        variant: 'danger',
        content:
          '<strong>Payment Failed:</strong> Your monthly mortgage payment could not be processed. Please <a href="#" class="ps-alert-link">update payment method</a> to avoid late fees.',
      })}
      ${alertTwig({
        variant: 'primary',
        content:
          '<span data-icon="infos" aria-hidden="true" style="margin-right: var(--size-3); font-size: var(--font-size-3);"></span><strong>BNP Paribas RealEstate</strong> Featured announcement for premium clients',
      })}
    </div>
  `,
};

export const ComposedWithAtoms = {
  name: 'Composed with Atoms (New Pattern)',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${alertTwig({
        variant: 'success',
        icon: 'check',
        title: 'Property Saved!',
        text: 'Your favorite property has been added to your watchlist. You will receive updates on price changes.',
        dismissible: true,
      })}
      ${alertTwig({
        variant: 'warning',
        icon: 'help',
        title: 'Document Expiring Soon',
        text: 'Your property insurance certificate expires in 15 days. Please renew to maintain coverage.',
        actions: [
          { label: 'Renew Now', variant: 'warning', size: 'small' },
          { label: 'Remind Later', variant: 'warning', outline: true, size: 'small' },
        ],
        dismissible: true,
      })}
      ${alertTwig({
        variant: 'danger',
        icon: 'close',
        title: 'Payment Failed',
        text: 'Your monthly payment could not be processed. Please update your payment information immediately.',
        actions: [{ label: 'Update Payment', variant: 'danger', size: 'small' }],
      })}
    </div>
  `,
};

export default settings;
