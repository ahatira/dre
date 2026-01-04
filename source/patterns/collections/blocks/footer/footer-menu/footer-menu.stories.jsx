import footerMenuTwigTemplate from './footer-menu.twig';
import footerMenuData from './footer-menu.yml';

function renderFooterMenu(args) {
  return footerMenuTwigTemplate(args);
}

export default {
  title: 'Collections/Blocks/Footer/Footer Menu',
  tags: ['autodocs'],
  render: renderFooterMenu,
  argTypes: {
    label: {
      description: 'Section title/label (typically hidden)',
      control: 'text',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    label_display: {
      description: 'Label display setting',
      control: 'select',
      options: ['visible', 'hidden'],
      table: {
        category: 'Settings',
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
  args: footerMenuData,
};

export const ShortMenu = {
  args: {
    items: [
      { title: 'Data protection', url: '#' },
      { title: 'Cookie policy', url: '#' },
      { title: 'Disclaimer', url: '#' },
      { title: 'Sitemap', url: '#' },
    ],
  },
};
