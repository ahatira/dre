import offerFullTwig from './offer-full.twig';
import offerFullData from './offer-full.yml';
import './offer-full.css';

export default {
  title: 'Layouts/Offer Full',
  tags: ['autodocs'],
  render: (args) => offerFullTwig(args),
  args: offerFullData,

  parameters: {
    docs: {
      description: {
        component:
          '**Complete property detail page layout** for real estate offers.\n\n' +
          '### Architecture\n' +
          '- **Modular structure**: Semantic sections (hero, meta, description, features, etc.)\n' +
          '- **Drupal-compatible**: `<article>` root element with `.container` and `.offer-layout` grid\n' +
          '- **Responsive layout**: Single column mobile → 2-column desktop (main 2fr + sidebar 1fr)\n' +
          '- **Token-First**: All spacing, colors, typography use design system tokens\n\n' +
          '### Sections\n' +
          '- **Hero** - Photo carousel (gallery)\n' +
          '- **Meta** - Header (title, badges, price, details) + Action buttons\n' +
          '- **Description** - Property description with read-more toggle\n' +
          '- **Features** - Equipments, Services, Building Condition, More Info\n' +
          '- **Energy** - DPE/GES widgets (placeholder)\n' +
          '- **Surface Table** - Lots/floors data table\n' +
          '- **Map** - Full-width section with address, map, POI, travel time (placeholders)\n' +
          '- **Sidebar** - Consultant card (sticky on desktop)\n\n' +
          '### Features\n' +
          '- **Skeleton mode**: Loading state with placeholders for Ajax loading\n' +
          '- **Composed components**: Carousel, Card Agent, Feature Sections, Table, Read More\n' +
          '- **Full-width map**: Section outside container for better visual impact\n\n' +
          '### Real Estate Context\n' +
          '- Property: Office space in Madrid Barrio de Chamberí\n' +
          '- Surface: 611.3 m²\n' +
          '- Price: 20,000 €/m²/year\n' +
          '- Consultant: Sophia Dacosta',
      },
    },
    layout: 'fullscreen',
  },

  argTypes: {
    // State
    skeleton: {
      control: 'boolean',
      description: 'Loading state with skeleton placeholders (for Ajax loading)',
      table: {
        category: 'State',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },

    // Gallery
    gallery: {
      control: 'object',
      description: 'Photo carousel configuration (slides, variant, loop, pagination)',
      table: {
        category: 'Gallery',
        type: { summary: 'object' },
      },
    },

    // Header
    header: {
      control: 'object',
      description:
        'Property header data (title, badges, reference, surface, location, price, availability, mandate)',
      table: {
        category: 'Header',
        type: { summary: 'object' },
      },
    },

    // Actions
    actions: {
      control: 'object',
      description: 'Action buttons array (surface table, brochure, visit)',
      table: {
        category: 'Actions',
        type: { summary: 'object' },
      },
    },

    // Sidebar
    consultant: {
      control: 'object',
      description: 'Agent card data (name, phone, avatar, CTA)',
      table: {
        category: 'Sidebar',
        type: { summary: 'object' },
      },
    },

    // Content
    description: {
      control: 'object',
      description: 'Property description with read-more toggle',
      table: {
        category: 'Content',
        type: { summary: 'object' },
      },
    },
    equipments: {
      control: 'object',
      description: 'Equipments feature section (icon, title, items list)',
      table: {
        category: 'Content',
        type: { summary: 'object' },
      },
    },
    services: {
      control: 'object',
      description: 'Services feature section (icon, title, items list, 2-column)',
      table: {
        category: 'Content',
        type: { summary: 'object' },
      },
    },
    building: {
      control: 'object',
      description: 'Building condition feature section',
      table: {
        category: 'Content',
        type: { summary: 'object' },
      },
    },
    information: {
      control: 'object',
      description: 'Additional information feature section',
      table: {
        category: 'Content',
        type: { summary: 'object' },
      },
    },

    // Energy (Placeholder)
    energy: {
      control: 'object',
      description: 'Energy performance data (DPE/GES) - Component to be implemented',
      table: {
        category: 'Content',
        type: { summary: 'object' },
      },
    },

    // Table
    surfaceTable: {
      control: 'object',
      description: 'Surface table data (headers, rows, variants)',
      table: {
        category: 'Data',
        type: { summary: 'object' },
      },
    },

    // Location (Placeholder)
    location: {
      control: 'object',
      description: 'Address and map data - Map widget to be implemented',
      table: {
        category: 'Location',
        type: { summary: 'object' },
      },
    },
    poi: {
      control: 'object',
      description: 'POI filters categories - Component to be implemented',
      table: {
        category: 'Location',
        type: { summary: 'object' },
      },
    },
    travelTime: {
      control: 'object',
      description: 'Travel time calculator config - Component to be implemented',
      table: {
        category: 'Location',
        type: { summary: 'object' },
      },
    },

    // Drupal
    attributes: {
      control: 'object',
      description: 'Drupal Attribute object for additional HTML attributes',
      table: {
        category: 'Drupal',
        type: { summary: 'Drupal\\Core\\Template\\Attribute' },
      },
    },
  },
};

/**
 * Default property detail page with all sections
 */
export const Default = {
  name: 'Default (Full Content)',
  args: {
    ...offerFullData,
  },
};

/**
 * Skeleton loading state for Ajax content loading
 */
export const Skeleton = {
  name: 'Skeleton (Loading State)',
  args: {
    ...offerFullData,
    skeleton: true,
  },
  parameters: {
    docs: {
      description: {
        story:
          'Loading state with skeleton placeholders. Use this variant when fetching property data via Ajax (Drupal node loading).',
      },
    },
  },
};

/**
 * Minimal property (only required fields)
 */
export const Minimal = {
  name: 'Minimal (Required Fields Only)',
  args: {
    header: {
      title: 'Office Space PARIS La Défense',
      surface: '1 250 m²',
      location: 'Paris La Défense',
      price: '540 €/m²/an',
    },
    consultant: {
      name: 'Jean Dupont',
      phone: '+33 1 23 45 67 89',
      cta: {
        text: 'Contact agent',
        url: '#contact',
      },
    },
  },
  parameters: {
    docs: {
      description: {
        story:
          'Minimal property page with only required fields (breadcrumb, header, consultant). All optional sections are hidden.',
      },
    },
  },
};
