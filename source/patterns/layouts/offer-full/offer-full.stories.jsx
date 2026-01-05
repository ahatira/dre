/**
 * @file
 * Storybook stories for Offer Full layout.
 *
 * @component Offer Full
 * @description Complete page layout for real estate property details
 */

import offerFullData from './offer-full.yml';

export default {
  title: 'Layouts/Offer Full',
  tags: ['autodocs'],
  parameters: {
    layout: 'fullscreen',
    docs: {
      description: {
        component:
          'Complete page layout for displaying real estate property details. Includes hero gallery, metadata, consultant sidebar, features, and interactive map. Responsive design with sticky sidebar on desktop and bottom bar on mobile.',
      },
    },
  },
  argTypes: {
    // Bundle & View Mode
    bundle: {
      control: 'text',
      description: 'Node bundle (always "offer")',
      table: {
        category: 'Bundle',
        defaultValue: { summary: 'offer' },
      },
    },
    view_mode: {
      control: 'text',
      description: 'View mode (always "full")',
      table: {
        category: 'Bundle',
        defaultValue: { summary: 'full' },
      },
    },

    // Building & Reference
    building_name: {
      control: 'object',
      description: 'Building name object with label and value',
      table: {
        category: 'Identification',
      },
    },
    reference: {
      control: 'object',
      description: 'Reference number object',
      table: {
        category: 'Identification',
      },
    },

    // Main Metadata
    title: {
      control: 'text',
      description: 'Property title (H1)',
      table: {
        category: 'Metadata',
      },
    },
    price: {
      control: 'object',
      description: 'Price object with value, currency, unit',
      table: {
        category: 'Metadata',
      },
    },
    surface_total: {
      control: 'object',
      description: 'Total surface area',
      table: {
        category: 'Metadata',
      },
    },
    availability: {
      control: 'object',
      description: 'Availability status',
      table: {
        category: 'Metadata',
      },
    },
    mandate_type: {
      control: 'object',
      description: 'Type of mandate',
      table: {
        category: 'Metadata',
      },
    },

    // Hero
    hero: {
      control: 'object',
      description: 'Hero gallery data with images, 3D visit, plans',
      table: {
        category: 'Hero',
      },
    },

    // Content Sections
    description: {
      control: 'object',
      description: 'Main description object',
      table: {
        category: 'Content',
      },
    },
    equipments: {
      control: 'array',
      description: 'Equipment list',
      table: {
        category: 'Content',
      },
    },
    services: {
      control: 'array',
      description: 'Services list',
      table: {
        category: 'Content',
      },
    },
    energy: {
      control: 'object',
      description: 'Energy performance data (DPE, GES, labels)',
      table: {
        category: 'Content',
      },
    },
    surface_table: {
      control: 'array',
      description: 'Surface breakdown table',
      table: {
        category: 'Content',
      },
    },

    // Location & Map
    location: {
      control: 'object',
      description: 'Address and transport information',
      table: {
        category: 'Location',
      },
    },
    map: {
      control: 'object',
      description: 'Map configuration',
      table: {
        category: 'Location',
      },
    },

    // Consultant
    consultant: {
      control: 'object',
      description: 'Consultant profile data',
      table: {
        category: 'Sidebar',
      },
    },

    // States
    placeholder: {
      control: 'boolean',
      description: 'Show placeholder skeleton mode',
      table: {
        category: 'State',
        defaultValue: { summary: false },
      },
    },
  },
};

/**
 * Default story with complete Madrid office data
 */
export const Default = {
  args: offerFullData,
};

/**
 * Placeholder skeleton mode for loading state
 */
export const Placeholder = {
  args: {
    ...offerFullData,
    placeholder: true,
  },
  parameters: {
    docs: {
      description: {
        story: 'Skeleton loading state displayed while content is being fetched.',
      },
    },
  },
};

/**
 * Minimal data showcase (required fields only)
 */
export const Minimal = {
  args: {
    bundle: 'offer',
    view_mode: 'full',
    title: 'Office Space for Rent',
    building_name: {
      value: 'Modern Business Center',
    },
    reference: {
      value: 'REF123456',
    },
    price: {
      value: 15000,
      currency: '€',
      unit: 'HT/HC/m²/an',
    },
    surface_total: {
      value: 450,
      unit: 'm²',
    },
    hero: {
      images: [
        {
          url: '/images/property/default-01.jpg',
          alt: 'Office space',
        },
      ],
      photos_count: 1,
    },
    consultant: {
      name: 'John Doe',
      phone: '+33 1 23 45 67 89',
    },
    location: {
      address: {
        street: '123 Business Avenue',
        postal_code: '75001',
        city: 'Paris',
      },
      coordinates: {
        lat: 48.8566,
        lng: 2.3522,
      },
    },
    map: {
      enabled: true,
      zoom: 15,
    },
  },
  parameters: {
    docs: {
      description: {
        story: 'Minimal configuration with only required fields populated.',
      },
    },
  },
};

/**
 * Without energy data showcase
 */
export const WithoutEnergy = {
  args: {
    ...offerFullData,
    energy: null,
  },
  parameters: {
    docs: {
      description: {
        story: 'Layout without energy performance section (DPE/GES data unavailable).',
      },
    },
  },
};

/**
 * Without surface table showcase
 */
export const WithoutSurfaceTable = {
  args: {
    ...offerFullData,
    surface_table: null,
  },
  parameters: {
    docs: {
      description: {
        story: 'Layout without surface breakdown table.',
      },
    },
  },
};

/**
 * Consultant without photo showcase
 */
export const ConsultantNoPhoto = {
  args: {
    ...offerFullData,
    consultant: {
      name: 'Marie Dubois',
      phone: '+33 6 12 34 56 78',
      brochure_url: '/documents/brochures/sample.pdf',
    },
  },
  parameters: {
    docs: {
      description: {
        story: 'Sidebar with consultant profile using placeholder avatar (no photo provided).',
      },
    },
  },
};

/**
 * Mobile viewport showcase
 */
export const Mobile = {
  args: offerFullData,
  parameters: {
    viewport: {
      defaultViewport: 'mobile1',
    },
    docs: {
      description: {
        story: 'Mobile layout with single column and bottom bar consultant CTA.',
      },
    },
  },
};

/**
 * Tablet viewport showcase
 */
export const Tablet = {
  args: offerFullData,
  parameters: {
    viewport: {
      defaultViewport: 'tablet',
    },
    docs: {
      description: {
        story: 'Tablet layout transitioning to desktop two-column layout.',
      },
    },
  },
};
