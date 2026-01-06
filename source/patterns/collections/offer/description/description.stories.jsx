/**
 * Description (Collection/Organism)
 *
 * Organism composes Read-more component for displaying expandable content.
 * Used for offer descriptions, property details, and rich text content.
 */

import descriptionTemplate from './description.twig';
import data from './description.yml';

const settings = {
  title: 'Collections/Offer/Description',
  tags: ['autodocs'],
  parameters: {
    layout: 'padded',
    docs: {
      description: {
        component:
          'Organism composing Read-more component with optional title. Used for displaying long-form content with expand/collapse functionality.',
      },
    },
  },
  render: (args) => descriptionTemplate(args),
  args: data.args || data,
  argTypes: {
    html_tag: {
      control: 'select',
      options: ['div', 'section', 'article'],
      description: 'Root HTML element (div, section, or article)',
      table: {
        category: 'Layout',
        type: { summary: 'string' },
        defaultValue: { summary: 'div' },
      },
    },
    title_level: {
      control: 'select',
      options: ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
      description: 'Heading level for title',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'h4' },
      },
    },
    title: {
      description: 'Section title (optional)',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    content: {
      description: 'Rich text content (HTML or plain text)',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    maxHeight: {
      control: 'number',
      description: 'Maximum height in pixels before truncation',
      table: {
        category: 'Appearance',
        type: { summary: 'number' },
        defaultValue: { summary: '150' },
      },
    },
    expandLabel: {
      description: 'Label for expand button',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'Voir plus' },
      },
    },
    collapseLabel: {
      description: 'Label for collapse button',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'Voir moins' },
      },
    },
    expanded: {
      control: 'boolean',
      description: 'Initial expanded state',
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    showIcon: {
      control: 'boolean',
      description: 'Show chevron icon in toggle button',
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'true' },
      },
    },
    attributes: {
      description: 'Drupal attributes object for root element',
      table: {
        category: 'Layout',
        type: { summary: 'Drupal.Attribute' },
      },
    },
  },
};

export const Default = {
  args: data.args || data,
};

export const WithTitle = {
  args: {
    html_tag: 'section',
    title_level: 'h3',
    title: 'Présentation du bien',
    content:
      '<p>Spacieux appartement dans le 15ème arrondissement offrant 120 m² de surface habitable.</p>',
    maxHeight: 100,
    expandLabel: 'Lire la suite',
    collapseLabel: 'Masquer',
  },
};

export const PreExpanded = {
  args: {
    title: 'Description complète',
    content:
      '<p>Appartement haussmannien totalement rénové avec hauteur de plafond exceptionnelle.</p><p>Dispose de 3 chambres, 2 salles de bain et une cuisine équipée.</p>',
    expanded: true,
    maxHeight: 200,
  },
};

export const NoIcon = {
  args: {
    title: 'Notes',
    content: "<p>Point fort : Luminosité naturelle</p><p>Point faible : Pas d'ascenseur</p>",
    showIcon: false,
    expandLabel: 'Afficher plus',
    collapseLabel: 'Afficher moins',
  },
};

export default settings;
