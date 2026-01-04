import contactUsTwig from '../../collections/blocks/footer/contact-us/contact-us.twig';
import followUsTwig from '../../collections/blocks/footer/follow-us/follow-us.twig';
import menuTwig from '../../collections/blocks/footer/footer-menu/footer-menu.twig';
import menuLinksTwig from '../../collections/blocks/footer/footer-menu-links/footer-menu-links.twig';
import logo from '../../components/logo/logo.twig';
import footerTemplate from './footer.twig';
import data from './footer.yml';
import '../../elements/collapse/collapse.css';
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

const buildContact = (contactData) => {
  if (!contactData) {
    return '';
  }
  return contactUsTwig(contactData);
};

const buildSocial = (socialData) => {
  if (!socialData) {
    return '';
  }
  return followUsTwig(socialData);
};

const buildMenuCol = (menuData) => {
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
          'Responsive footer with 3-column grid (Desktop): Contact+Social | Menu Col1 | Menu Col2. Bottom section: Branding | Legal | Copyright. Drupal regions: footer_contact, footer_social, footer_menu_col1, footer_menu_col2, footer_legal, footer_branding, footer_copyright. Mobile-first, token-first, BEM-nested CSS.',
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
    const contactMarkup = buildContact(args.page?.footer_contact || data.footer_contact);
    const socialMarkup = buildSocial(args.page?.footer_social || data.footer_social);
    const menuCol1Markup = buildMenuCol(args.page?.footer_menu_col1 || data.footer_menu_col1);
    const menuCol2Markup = buildMenuCol(args.page?.footer_menu_col2 || data.footer_menu_col2);
    const legalMarkup = buildLegal(args.page?.footer_legal || data.footer_legal);

    return footerTemplate({
      page: {
        footer_branding: brandingMarkup,
        footer_contact: contactMarkup,
        footer_social: socialMarkup,
        footer_menu_col1: menuCol1Markup,
        footer_menu_col2: menuCol2Markup,
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
      footer_contact: data.footer_contact,
      footer_social: data.footer_social,
      footer_menu_col1: data.footer_menu_col1,
      footer_menu_col2: data.footer_menu_col2,
      footer_legal: data.footer_legal,
      footer_copyright: data.footer_copyright,
    },
  },
};
