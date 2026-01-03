import menuPrimary from '../../collections/menu-primary/menu-primary.twig';
import logo from '../../components/logo/logo.twig';
import ctaBlock from '../blocks/cta/block-cta.twig';
import favoritesBlock from '../blocks/favorites/favorites.twig';
import languageSwitcher from '../blocks/language-switcher/language-switcher.twig';
import searchBlock from '../blocks/search/block-search.twig';
import userAccountBlock from '../blocks/user-account/block-user-account.twig';
import header from './header.twig';
import headerData from './header.yml';

/**
 * Rend un bloc individuel du header_bottom
 */
function renderBottomBlock(blockObj, userState) {
  let blockHtml = '';

  // Block: Search CTA
  if (blockObj.block_cta_search) {
    const searchCtaHtml = ctaBlock(blockObj.block_cta_search);
    blockHtml += searchCtaHtml;
  }

  // Block: Find Property Link
  if (blockObj.block_link_find_property) {
    const findPropertyHtml = ctaBlock(blockObj.block_link_find_property);
    blockHtml += findPropertyHtml;
  }

  // Block: User Account
  if (blockObj.block_user_account) {
    const userAccountState = blockObj.block_user_account[userState];
    const userAccountHtml = userAccountBlock(userAccountState);
    blockHtml += userAccountHtml;
  }

  // Block: Contact CTA
  if (blockObj.block_cta_contact) {
    const contactHtml = ctaBlock(blockObj.block_cta_contact);
    blockHtml += contactHtml;
  }

  // Block: Favorites
  if (blockObj.block_favorites) {
    const favoritesState = blockObj.block_favorites[userState];
    const favoritesHtml = favoritesBlock(favoritesState);
    blockHtml += favoritesHtml;
  }

  // Block: Search
  if (blockObj.block_search) {
    const searchHtml = searchBlock(blockObj.block_search);
    blockHtml += searchHtml;
  }

  return blockHtml;
}

/**
 * Génère les régions Drupal avec les composants Twig
 * @param {object} data - Les données du header.yml
 * @param {string} userState - 'not_connected' ou 'connected'
 * @returns {object} Les régions HTML pré-rendues
 */
function renderHeaderRegions(data, userState = 'not_connected') {
  // REGION: header_branding
  const brandingHtml = logo(data.header_branding.logo);

  // REGION: header_top (Utilitaires - peut avoir plusieurs blocs)
  let topHtml = '';
  if (data.header_top && Array.isArray(data.header_top)) {
    data.header_top.forEach((blockObj) => {
      if (blockObj.language_switcher) {
        topHtml += languageSwitcher(blockObj.language_switcher);
      }
    });
  }

  // REGION: header_navigation (Primary Menu)
  const navigationHtml = menuPrimary({ items: data.header_navigation.items });

  // REGION: header_bottom (Blocs d'action - flexbox dans le CSS)
  let bottomHtml = '';
  if (data.header_bottom && Array.isArray(data.header_bottom)) {
    data.header_bottom.forEach((blockObj) => {
      bottomHtml += renderBottomBlock(blockObj, userState);
    });
  }

  return {
    header_branding: brandingHtml,
    header_top: topHtml,
    header_navigation: navigationHtml,
    header_bottom: bottomHtml,
  };
}

export default {
  title: 'Layouts/Header',
  tags: ['autodocs'],
  argTypes: {
    sticky: {
      control: 'boolean',
      description: 'Enable sticky header behavior on scroll',
      table: {
        category: 'Configuration',
        defaultValue: { summary: 'true' },
      },
    },
    branding: {
      control: 'object',
      description: 'Logo data (logo_src, logo_alt)',
      table: {
        category: 'Regions / Branding',
      },
    },
    language_switcher: {
      control: 'object',
      description: 'Language switcher data (current, options)',
      table: {
        category: 'Regions / Top',
      },
    },
    menu_primary: {
      control: 'object',
      description: 'Primary menu items',
      table: {
        category: 'Regions / Navigation',
      },
    },
    blocks: {
      control: 'object',
      description:
        'Action blocks (search_cta, find_property, user_account, contact, favorites, search)',
      table: {
        category: 'Regions / Bottom',
      },
    },
    modifier_class: {
      control: 'text',
      description: 'Optional modifier class for custom variants',
      table: {
        category: 'Advanced',
        type: { summary: 'string' },
      },
    },
  },
};

/**
 * NonConnected: Utilisateur non authentifié
 * - Affiche bouton "Log in / Sign up"
 * - Badge favoris vide (count: 0)
 * - Ordre: Search input → Find property → Login → Contact → Favorites → Search trigger
 */
export const NonConnected = {
  render: (args) => {
    const regions = renderHeaderRegions(args, 'not_connected');

    return header({
      sticky: args.sticky,
      page: regions,
      modifier_class: args.modifier_class,
    });
  },
  args: {
    ...headerData,
  },
};

/**
 * Connected: Utilisateur authentifié
 * - Affiche dropdown menu avec profil utilisateur "Enzo"
 * - Badge favoris avec compteur (count: 5)
 * - Menu items: My account, My favorites, My alerts, Logout
 */
export const Connected = {
  render: (args) => {
    const regions = renderHeaderRegions(args, 'connected');

    return header({
      sticky: args.sticky,
      page: regions,
      modifier_class: args.modifier_class,
    });
  },
  args: {
    ...headerData,
  },
};
