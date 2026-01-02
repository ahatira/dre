import ctaBlock from '../blocks/cta/block-cta.twig';
import favoritesBlock from '../blocks/favorites/favorites.twig';
import searchBlock from '../blocks/search/block-search.twig';
import userAccountBlock from '../blocks/user-account/block-user-account.twig';
import header from './header.twig';
import headerData from './header.yml';

export default {
  title: 'Layouts/Header',
  tags: ['autodocs'],
  argTypes: {
    // Configuration
    sticky: {
      control: 'boolean',
      description: 'Enable sticky header behavior on scroll (header stays at top)',
      table: {
        category: 'Configuration',
        defaultValue: { summary: 'true' },
      },
    },

    // Drupal Regions
    'page.header_branding': {
      control: 'text',
      description: 'Drupal region: Header branding (logo zone gauche haut)',
      table: {
        category: 'Drupal Regions',
        type: { summary: 'string (HTML)' },
      },
    },
    'page.header_top': {
      control: 'text',
      description: 'Drupal region: Header top (language selector block)',
      table: {
        category: 'Drupal Regions',
        type: { summary: 'string (HTML)' },
      },
    },
    'page.header_navigation': {
      control: 'text',
      description: 'Drupal region: Primary navigation (menu block)',
      table: {
        category: 'Drupal Regions',
        type: { summary: 'string (HTML)' },
      },
    },
    'page.header_bottom': {
      control: 'text',
      description: 'Drupal region: Header bottom (actions secondaires / CTA)',
      table: {
        category: 'Drupal Regions',
        type: { summary: 'string (HTML)' },
      },
    },

    // Advanced
    modifier_class: {
      control: 'text',
      description: 'Optional modifier class for custom variants or theming',
      table: {
        category: 'Advanced',
        type: { summary: 'string' },
      },
    },
  },
};

/**
 * Default: Rend les 4 régions Drupal simulées avec les blocs intégrés
 */
export const Default = {
  render: (args) => {
    // Rendre les blocs individuellement
    const searchBlockHtml = searchBlock({
      show_form: false,
      search_form_props: {
        placeholder: 'What are you looking for ?',
      },
    });

    const findPropertyCtaHtml = ctaBlock({
      button: {
        label: 'Find a property',
        variant: 'primary',
        outline: true,
        fullWidth: false,
        url: '/find-property',
      },
    });

    const userAccountHtml = userAccountBlock({
      logged_in: false,
      login_button: {
        label: 'Log in / Sign up',
        url: '/user/login',
        variant: 'primary',
        icon: 'account',
        fullWidth: false,
      },
    });

    const contactCtaHtml = ctaBlock({
      button: {
        label: 'Contact us',
        variant: 'secondary',
        fullWidth: false,
        url: '/contact',
      },
    });

    const favoritesBlockHtml = favoritesBlock({
      count: 0,
      url: '/favorites',
    });

    // Remplacer les placeholders par le contenu rendu
    const updatedArgs = {
      ...args,
      page: {
        ...args.page,
        header_bottom: args.page.header_bottom
          .replace('<!-- Find Property CTA Block component -->', findPropertyCtaHtml)
          .replace('<!-- User Account Block component -->', userAccountHtml)
          .replace('<!-- Contact CTA Block component -->', contactCtaHtml)
          .replace('<!-- Favorites Block component -->', favoritesBlockHtml)
          .replace('<!-- Search Block component -->', searchBlockHtml),
      },
    };

    return header(updatedArgs);
  },
  args: {
    ...headerData,
  },
};

/**
 * Mobile Preview: View header in mobile size
 * - Hamburger menu toggle visible
 * - Offcanvas navigation
 */
export const MobilePreview = {
  render: (args) => {
    // Rendre les blocs individuellement
    const searchBlockHtml = searchBlock({
      show_form: false,
      search_form_props: {
        placeholder: 'What are you looking for ?',
      },
    });

    const findPropertyCtaHtml = ctaBlock({
      button: {
        label: 'Find a property',
        variant: 'primary',
        outline: true,
        fullWidth: false,
        url: '/find-property',
      },
    });

    const userAccountHtml = userAccountBlock({
      logged_in: true,
      user_name: 'Enzo',
      menu_items: [
        { label: 'My account', url: '/user/account', icon: 'account' },
        { label: 'My favorites', url: '/user/favorites', icon: 'heart' },
        { label: 'My alerts', url: '/user/alerts', icon: 'alert' },
        { label: 'Logout', url: '/user/logout', icon: 'exit', type: 'logout' },
      ],
    });

    const contactCtaHtml = ctaBlock({
      button: {
        label: 'Contact us',
        variant: 'secondary',
        fullWidth: false,
        url: '/contact',
      },
    });

    const favoritesBlockHtml = favoritesBlock({
      count: 5,
      url: '/favorites',
    });

    // Remplacer les placeholders par le contenu rendu
    const updatedArgs = {
      ...args,
      page: {
        ...args.page,
        header_bottom: args.page.header_bottom
          .replace('<!-- Find Property CTA Block component -->', findPropertyCtaHtml)
          .replace('<!-- User Account Block component -->', userAccountHtml)
          .replace('<!-- Contact CTA Block component -->', contactCtaHtml)
          .replace('<!-- Favorites Block component -->', favoritesBlockHtml)
          .replace('<!-- Search Block component -->', searchBlockHtml),
      },
    };

    return header(updatedArgs);
  },
  args: {
    ...headerData,
  },
  parameters: {
    viewport: {
      defaultViewport: 'mobile1',
    },
  },
};

/**
 * Without Sticky: Header without sticky behavior
 * - Header scrolls normally
 */
export const WithoutSticky = {
  render: (args) => header(args),
  args: {
    ...headerData,
    sticky: false,
  },
};

/**
 * RegionsInjected: Exemple alternatif de contenu région
 */
export const RegionsInjected = {
  render: (args) => header(args),
  args: {
    ...headerData,
    page: {
      header_branding: '<div class="ps-logo"><img src="/logo/logo.svg" alt="Alt brand" /></div>',
      header_navigation:
        '<ul class="menu-primary" role="menubar"><li class="menu-primary__item"><a href="#" class="menu-primary__link">Item</a></li></ul>',
      header_top:
        '<nav class="ps-language-selector" aria-label="Sélecteur de langue"><div class="ps-language-selector__control"><button class="ps-language-selector__button" type="button">Fr</button></div></nav>',
      header_bottom:
        '<div class="ps-header-bottom"><button class="ps-button ps-button--primary">CTA</button></div>',
    },
  },
};
