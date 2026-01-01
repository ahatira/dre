import blockContactTemplate from './block-contact.twig';
import blockContactData from './block-contact.yml';

export default {
  title: 'Layouts/Blocks/Contact Block',
  tags: ['autodocs'],
  render: (args) => blockContactTemplate(args),
};

/**
 * Contact block with email button that triggers modal dialog.
 * The modal displays the contact form for user inquiries.
 */
export const Default = {
  args: {
    ...blockContactData,
    label: 'Contact',
    plugin_id: 'block_contact',
  },
  argTypes: {
    label: {
      control: 'text',
      description: 'Block title',
      table: {
        category: 'Content',
        defaultValue: { summary: 'Contact' },
      },
    },
    button: {
      control: 'object',
      description: 'Contact button configuration',
      table: {
        category: 'Button',
        type: {
          summary: 'object',
          detail:
            'Contains label, variant (secondary), outline (false), fullWidth (true), icon (email-outline)',
        },
      },
    },
    contact_form_url: {
      control: 'text',
      description: 'URL to fetch contact form via AJAX',
      table: {
        category: 'Behavior',
        defaultValue: { summary: '/contact-form' },
      },
    },
    modal_id: {
      control: 'text',
      description: 'Modal element ID',
      table: {
        category: 'Behavior',
        defaultValue: { summary: 'contact-form-modal' },
      },
    },
  },
};

/**
 * Contact block with primary variant button.
 */
export const Primary = {
  args: {
    ...blockContactData,
    label: 'Contact',
    button: {
      label: 'Contact us',
      variant: 'primary',
      outline: false,
      fullWidth: true,
      icon: 'email-outline',
      type: 'button',
    },
  },
};

/**
 * Contact block with outline button style.
 */
export const Outline = {
  args: {
    ...blockContactData,
    label: 'Contact',
    button: {
      label: 'Contact us',
      variant: 'secondary',
      outline: true,
      fullWidth: true,
      icon: 'email-outline',
      type: 'button',
    },
  },
};
