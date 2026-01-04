import logo from '../../components/logo/logo.twig';
import footer from './footer.twig';
import data from './footer.yml';
import '../../elements/collapse/collapse.css';
import '../../elements/collapse/collapse.js';
import './footer.css';

const buildBranding = (brandingData) => {
  if (!brandingData) {
    return '';
  }

  return logo({
    site_logo: brandingData.site_logo,
    site_slogan: brandingData.site_slogan,
    url: brandingData.url,
    rel: brandingData.rel,
  });
};

const prepareArgs = (args) => {
  const brandingMarkup = buildBranding(args.branding || data.branding);

  return {
    ...data,
    ...args,
    branding: brandingMarkup,
  };
};

export default {
  title: 'Layouts/Footer',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Responsive footer with footer blocks: Contact Us, Follow Us, Menu Links, and legal links. Mobile-first, token-first, BEM-nested CSS.',
      },
    },
  },
  argTypes: {
    contact: {
      control: 'object',
      description: 'Contact block data with phone numbers and email',
      table: { category: 'Content' },
    },
    follow_us: {
      control: 'object',
      description: 'Follow Us block with social media links',
      table: { category: 'Content' },
    },
    menu_sections: {
      control: 'object',
      description: 'Array of menu sections (navigation columns)',
      table: { category: 'Content' },
    },
    menu_legal: {
      control: 'object',
      description: 'Legal menu links',
      table: { category: 'Legal' },
    },
    branding: {
      control: 'object',
      description: 'Logo data passed to the branding slot',
      table: { category: 'Brand' },
    },
    copyright: {
      control: 'text',
      description: 'Copyright notice text',
      table: { category: 'Legal' },
    },
    modifier_class: {
      control: 'text',
      description: 'Optional modifier class for custom variants',
      table: { category: 'Advanced' },
    },
  },
};

export const Default = {
  render: (args) => footer(prepareArgs(args)),
  args: {
    ...data,
  },
};
