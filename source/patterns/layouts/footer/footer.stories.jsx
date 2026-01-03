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
          'Responsive footer with contact block, navigation columns, social icons, and legal links compliant with Drupal and token-first CSS.',
      },
    },
  },
  argTypes: {
    contact: {
      control: 'object',
      description: 'Contact block with phone numbers and email',
      table: { category: 'Content' },
    },
    columns: {
      control: 'object',
      description: 'Navigation columns (title + links)',
      table: { category: 'Content' },
    },
    social_links: {
      control: 'object',
      description: 'Social network links with icons',
      table: { category: 'Content' },
    },
    legal_links: {
      control: 'object',
      description: 'Legal links displayed in the bottom bar',
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
