import favoritesTemplate from './favorites.twig';
import favoritesData from './favorites.yml';

/**
 * Favorites Block - Quick access to user favorites
 *
 * Displays a heart icon that serves as a shortcut to favorites page.
 * - Empty heart when no favorites (count = 0)
 * - Filled heart with count badge when favorites exist (count > 0)
 */
export default {
  title: 'Collections/Blocks/Header/Favorites',
  tags: ['autodocs'],
  argTypes: {
    // Content
    count: {
      name: 'Count',
      description: 'Number of favorites (0 = empty heart, >0 = filled with badge)',
      control: { type: 'number', min: 0, max: 150 },
      table: { category: 'Content' },
    },
    url: {
      name: 'URL',
      description: 'Link to favorites page',
      control: 'text',
      table: { category: 'Content' },
    },
    label: {
      name: 'Accessible Label',
      description: 'Screen reader label',
      control: 'text',
      table: { category: 'Accessibility' },
    },
  },
};

/**
 * Default state - No favorites (empty heart)
 */
export const Default = {
  args: favoritesData,
};

/**
 * Showcase: Empty state
 */
export const Empty = {
  args: {
    count: 0,
    url: '/favorites',
    label: 'Aucun favori',
  },
};

/**
 * Showcase: With few favorites
 */
export const WithFewFavorites = {
  args: {
    count: 3,
    url: '/favorites',
    label: 'Mes 3 biens favoris',
  },
};

/**
 * Showcase: With many favorites
 */
export const WithManyFavorites = {
  args: {
    count: 47,
    url: '/favorites',
    label: 'Mes 47 biens favoris',
  },
};

/**
 * Showcase: Maximum count display (99+)
 */
export const MaxCount = {
  args: {
    count: 150,
    url: '/favorites',
    label: 'Plus de 99 biens favoris',
  },
};

// Render function
Default.render = (args) => favoritesTemplate(args);
Empty.render = (args) => favoritesTemplate(args);
WithFewFavorites.render = (args) => favoritesTemplate(args);
WithManyFavorites.render = (args) => favoritesTemplate(args);
MaxCount.render = (args) => favoritesTemplate(args);
