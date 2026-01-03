import contactUs from './contact-us.twig';
import contactUsData from './contact-us.yml';
import './contact-us.css';

export default {
  title: 'Collections/Blocks/Footer/Contact Us',
  tags: ['autodocs'],
  argTypes: {
    title: {
      control: 'text',
      description: 'Section heading displayed above the contact entries',
      table: { category: 'Content', defaultValue: { summary: 'Contact us' } },
    },
    phones: {
      control: 'object',
      description: 'Array of phone entries [{ label, number, href }] displayed in order',
      table: { category: 'Content' },
    },
    email: {
      control: 'object',
      description: 'Email object { address, href } rendered below the phone list',
      table: { category: 'Content' },
    },
  },
};

const Template = (args) => contactUs(args);

export const Default = {
  render: Template,
  args: {
    ...contactUsData,
  },
};

export const SingleCity = {
  render: Template,
  args: {
    label: 'Contact us',
    label_level: 'h4',
    phones: [
      {
        label: 'Head office :',
        number: '+32 2 646 49 49',
        href: 'tel:+3226464949',
      },
    ],
    email: {
      address: 'contact.web@realestate.bnpparibas',
      href: 'mailto:contact.web@realestate.bnpparibas',
    },
  },
};
