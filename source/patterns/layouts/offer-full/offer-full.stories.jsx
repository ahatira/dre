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
          '### Key Features\n' +
          '- **Responsive layout**: Single column mobile → 2-column desktop (content 2/3 + sidebar 1/3)\n' +
          '- **Rich content**: Gallery, header, actions, consultant card, specifications, table, location\n' +
          '- **Skeleton mode**: Loading state with skeleton placeholders for Ajax loading\n' +
          '- **Composed components**: Breadcrumb, Carousel, Card Agent, Feature Sections, Table, Read More\n' +
          '- **Placeholders**: Energy widgets, Map, POI filters (components to be implemented)\n' +
          '- **Token-First**: All spacing, colors, typography use design system tokens\n\n' +
          '### Sections\n' +
          '1. **Breadcrumb** - Navigation trail\n' +
          '2. **Gallery** - Photo carousel with pagination\n' +
          '3. **Header** - Title, badges, reference, surface, location, price, availability\n' +
          '4. **Actions Bar** - CTA buttons (surface table, brochure, visit)\n' +
          '5. **Consultant Card** - Agent profile (sidebar, sticky on desktop)\n' +
          '6. **Description** - Long text with "See more" toggle\n' +
          '7. **Feature Sections** - Equipments, Services, Building Condition, More Info\n' +
          '8. **Energy** - DPE/GES widgets (placeholder)\n' +
          '9. **Surface Table** - Lots/floors data table\n' +
          '10. **Location** - Address + Map (placeholder)\n' +
          '11. **POI Filters** - Points of interest checkboxes (placeholder)\n' +
          '12. **Travel Time** - Calculator form (placeholder)\n\n' +
          '### Use Cases\n' +
          '- **Property detail page**: Full Drupal node display\n' +
          '- **Ajax loading**: Use skeleton mode during data fetch\n' +
          '- **Responsive viewing**: Desktop (2 cols) / Tablet (stacked) / Mobile (compact)\n\n' +
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

    // Navigation
    breadcrumb: {
      control: 'object',
      description: 'Breadcrumb navigation with items array',
      table: {
        category: 'Navigation',
        type: { summary: 'object' },
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
    breadcrumb: {
      items: [
        { label: 'Home', url: '/' },
        { label: 'Search', url: '/search' },
        { label: 'Property Detail' },
      ],
    },
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
