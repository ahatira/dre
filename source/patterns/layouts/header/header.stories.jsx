import ctaBlock from '../blocks/cta/block-cta.twig';
import favoritesBlock from '../blocks/favorites/favorites.twig';
import searchBlock from '../blocks/search/block-search.twig';
import userAccountBlock from '../blocks/user-account/block-user-account.twig';
import header from './header.twig';
import headerData from './header.yml';

/**
 * Fonction helper pour rendu des blocs du header_bottom
 * Compile les blocs individuels et retourne les HTML strings
 */
function renderHeaderBlocks(userState, blocks) {
  const searchCtaHtml = ctaBlock({
    button: {
      label: blocks.search_cta.label,
      variant: blocks.search_cta.variant,
      outline: blocks.search_cta.outline,
      fullWidth: false,
      url: blocks.search_cta.url,
    },
  });

  const findPropertyLinkHtml = `<a href="${blocks.find_property.url}" class="ps-header-bottom__link">${blocks.find_property.label}</a>`;

  const userAccountHtml = userState.logged_in
    ? userAccountBlock({
        logged_in: true,
        user_name: userState.user_name,
        menu_items: userState.menu_items,
      })
    : userAccountBlock({
        logged_in: false,
        login_button: userState.login_button,
      });

  const contactCtaHtml = ctaBlock({
    button: {
      label: blocks.contact.label,
      variant: blocks.contact.variant,
      fullWidth: false,
      url: blocks.contact.url,
    },
  });

  const favoritesBlockHtml = favoritesBlock({
    count: userState.favorites_count,
    url: '/favorites',
  });

  const searchBlockHtml = searchBlock({
    show_form: false,
  });

  return {
    searchCta: searchCtaHtml,
    findProperty: findPropertyLinkHtml,
    userAccount: userAccountHtml,
    contact: contactCtaHtml,
    favorites: favoritesBlockHtml,
    search: searchBlockHtml,
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
    'page.header_branding': {
      control: 'text',
      description: 'Drupal region: Header branding (logo)',
      table: {
        category: 'Drupal Regions',
        type: { summary: 'string (HTML)' },
      },
    },
    'page.header_navigation': {
      control: 'text',
      description: 'Drupal region: Primary navigation (menu)',
      table: {
        category: 'Drupal Regions',
        type: { summary: 'string (HTML)' },
      },
    },
    'page.header_top': {
      control: 'text',
      description: 'Drupal region: Header top (language selector)',
      table: {
        category: 'Drupal Regions',
        type: { summary: 'string (HTML)' },
      },
    },
    'page.header_bottom': {
      control: 'text',
      description: 'Drupal region: Header bottom (actions & CTA blocks)',
      table: {
        category: 'Drupal Regions',
        type: { summary: 'string (HTML)' },
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
    const blocks = renderHeaderBlocks(args.user_states.not_connected, args.blocks);

    const updatedArgs = {
      ...args,
      page: {
        ...args.page,
        header_bottom: args.page.header_bottom
          .replace('<!-- Search CTA Block component -->', blocks.searchCta)
          .replace('<!-- Find Property CTA Block component -->', blocks.findProperty)
          .replace('<!-- User Account Block component -->', blocks.userAccount)
          .replace('<!-- Contact CTA Block component -->', blocks.contact)
          .replace('<!-- Favorites Block component -->', blocks.favorites)
          .replace('<!-- Search Block component -->', blocks.search),
      },
    };

    return header(updatedArgs);
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
    const blocks = renderHeaderBlocks(args.user_states.connected, args.blocks);

    const updatedArgs = {
      ...args,
      page: {
        ...args.page,
        header_bottom: args.page.header_bottom
          .replace('<!-- Search CTA Block component -->', blocks.searchCta)
          .replace('<!-- Find Property CTA Block component -->', blocks.findProperty)
          .replace('<!-- User Account Block component -->', blocks.userAccount)
          .replace('<!-- Contact CTA Block component -->', blocks.contact)
          .replace('<!-- Favorites Block component -->', blocks.favorites)
          .replace('<!-- Search Block component -->', blocks.search),
      },
    };

    return header(updatedArgs);
  },
  args: {
    ...headerData,
  },
};
