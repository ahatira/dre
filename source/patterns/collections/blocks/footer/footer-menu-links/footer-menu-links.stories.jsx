import footerMenuLinksTwigTemplate from './footer-menu-links.twig';
import footerMenuLinksData from './footer-menu-links.yml';
import './footer-menu-links.js';

function renderFooterMenuLinks(args) {
  return footerMenuLinksTwigTemplate(args);
}

export default {
  title: 'Collections/Blocks/Footer/Footer Menu Links',
  tags: ['autodocs'],
  render: renderFooterMenuLinks,
  argTypes: {
    label: {
      description: 'Section title/label',
      control: 'text',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    items: {
      description: 'Array of menu items with title and url',
      control: 'object',
      table: {
        category: 'Content',
        type: { summary: 'array' },
      },
    },
  },
};

export const Default = {
  args: footerMenuLinksData,
};

export const ManyLinks = {
  args: {
    label: 'Resources',
    items: [
      { title: 'Documentation', url: '#' },
      { title: 'Blog', url: '#' },
      { title: 'Case Studies', url: '#' },
      { title: 'Webinars', url: '#' },
      { title: 'Contact', url: '#' },
      { title: 'Support', url: '#' },
      { title: 'FAQ', url: '#' },
    ],
  },
};

export const WithSpecialCharacters = {
  args: {
    label: 'À Propos de BNP Paribas',
    items: [
      { title: 'Qui sommes-nous?', url: '#' },
      { title: 'Nos services', url: '#' },
      { title: 'Carrières & Recrutement', url: '#' },
      { title: 'Durabilité & RSE', url: '#' },
    ],
  },
};
