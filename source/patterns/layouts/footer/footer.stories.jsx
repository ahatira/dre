import contactUsTwig from '../../collections/blocks/footer/contact-us/contact-us.twig';
import followUsTwig from '../../collections/blocks/footer/follow-us/follow-us.twig';
import menuTwig from '../../collections/blocks/footer/footer-menu/footer-menu.twig';
import menuLinksTwig from '../../collections/blocks/footer/footer-menu-links/footer-menu-links.twig';
import logo from '../../components/logo/logo.twig';
import footerTemplate from './footer.twig';
import data from './footer.yml';
import '../../collections/blocks/footer/footer-menu-links/footer-menu-links.js';
import '../../elements/collapse/collapse.js';
import './footer.css';

const buildBranding = (brandingData) => {
  if (!brandingData || !brandingData.logo) {
    return '';
  }

  return logo({
    site_logo: brandingData.logo.site_logo,
    site_slogan: brandingData.logo.site_slogan,
    url: brandingData.logo.url,
    rel: brandingData.logo.rel,
  });
};

const buildTopCol1 = (col1Data) => {
  if (!col1Data) {
    return '';
  }
  const contactMarkup = col1Data.contact ? contactUsTwig(col1Data.contact) : '';
  const socialMarkup = col1Data.social ? followUsTwig(col1Data.social) : '';
  return contactMarkup + socialMarkup;
};

const buildTopCol = (menuData) => {
  if (!Array.isArray(menuData)) {
    return '';
  }
  return menuData.map((section) => menuLinksTwig(section)).join('');
};

const buildLegal = (legalData) => {
  if (!legalData) {
    return '';
  }
  return menuTwig(legalData);
};

export default {
  title: 'Layouts/Footer',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Responsive footer with 3-column grid (Desktop): Contact+Social | Menu Col1 | Menu Col2. Bottom section: Branding | Legal | Copyright. Drupal regions: footer_top_col1, footer_top_col2, footer_top_col3, footer_legal, footer_branding, footer_copyright. Mobile-first, token-first, BEM-nested CSS.',
      },
    },
  },
  argTypes: {
    page: {
      control: 'object',
      description: 'Drupal page object with footer regions',
      table: { category: 'Content' },
    },
    modifier_class: {
      control: 'text',
      description: 'Optional modifier class for custom variants',
      table: { category: 'Advanced' },
    },
  },
};

export const Default = {
  render: (args) => {
    const brandingMarkup = buildBranding(args.page?.footer_branding || data.footer_branding);
    const topCol1Markup = buildTopCol1(args.page?.footer_top_col1 || data.footer_top_col1);
    const topCol2Markup = buildTopCol(args.page?.footer_top_col2 || data.footer_top_col2);
    const topCol3Markup = buildTopCol(args.page?.footer_top_col3 || data.footer_top_col3);
    const legalMarkup = buildLegal(args.page?.footer_legal || data.footer_legal);

    return footerTemplate({
      page: {
        footer_branding: brandingMarkup,
        footer_top_col1: topCol1Markup,
        footer_top_col2: topCol2Markup,
        footer_top_col3: topCol3Markup,
        footer_legal: legalMarkup,
        footer_copyright: args.page?.footer_copyright || data.footer_copyright,
      },
      attributes: args.attributes,
      modifier_class: args.modifier_class,
    });
  },
  args: {
    page: {
      footer_branding: data.footer_branding,
      footer_top_col1: data.footer_top_col1,
      footer_top_col2: data.footer_top_col2,
      footer_top_col3: data.footer_top_col3,
      footer_legal: data.footer_legal,
      footer_copyright: data.footer_copyright,
    },
  },
};
