import metaTemplate from './meta.twig';
import metaData from './meta.yml';

export default {
  title: 'Collections/Offer/Meta',
  tags: ['autodocs'],
  parameters: {
    layout: 'padded',
  },
  render: (args) => metaTemplate(args),
  argTypes: {
    // Content
    title: {
      control: 'text',
      description: 'Offer title',
      table: { category: 'Content' },
    },

    // Reference
    'reference.label': {
      control: 'text',
      description: 'Reference label',
      table: { category: 'Reference' },
    },
    'reference.value': {
      control: 'text',
      description: 'Reference value',
      table: { category: 'Reference' },
    },

    // Building
    'building.label': {
      control: 'text',
      description: 'Building label',
      table: { category: 'Building' },
    },
    'building.value': {
      control: 'text',
      description: 'Building name',
      table: { category: 'Building' },
    },

    // Price
    'price.label': {
      control: 'text',
      description: 'Price label',
      table: { category: 'Price' },
    },
    'price.value': {
      control: 'text',
      description: 'Price value',
      table: { category: 'Price' },
    },
    'price.unit': {
      control: 'text',
      description: 'Price unit',
      table: { category: 'Price' },
    },
    'price.tooltip': {
      control: 'text',
      description: 'Price tooltip (info icon)',
      table: { category: 'Price' },
    },

    // Surface
    'surface_total.label': {
      control: 'text',
      description: 'Surface label',
      table: { category: 'Surface' },
    },
    'surface_total.value': {
      control: 'number',
      description: 'Surface value',
      table: { category: 'Surface' },
    },
    'surface_total.unit': {
      control: 'text',
      description: 'Surface unit',
      table: { category: 'Surface' },
    },

    // Location
    'location.address.postal_code': {
      control: 'text',
      description: 'Postal code',
      table: { category: 'Location' },
    },
    'location.address.locality': {
      control: 'text',
      description: 'City',
      table: { category: 'Location' },
    },

    // Availability
    'availability.label': {
      control: 'text',
      description: 'Availability label',
      table: { category: 'Metadata' },
    },
    'availability.value': {
      control: 'text',
      description: 'Availability value',
      table: { category: 'Metadata' },
    },

    // Mandate type
    'mandate_type.label': {
      control: 'text',
      description: 'Mandate type label',
      table: { category: 'Metadata' },
    },
    'mandate_type.value': {
      control: 'text',
      description: 'Mandate type value',
      table: { category: 'Metadata' },
    },

    // Actions
    'actions.items': {
      control: 'object',
      description: 'Action buttons array',
      table: { category: 'Actions' },
    },
  },
};

// Default story with Madrid office
export const Default = {
  args: metaData,
};

// Paris luxury office
export const ParisLuxury = {
  args: {
    title: 'Rent Offices PARIS La Défense',
    reference: {
      label: 'Reference',
      value: 'OLPAR7500234',
    },
    building: {
      label: 'Building',
      value: 'Tour First',
    },
    price: {
      label: 'Rent',
      value: '850 €',
      unit: 'HT/HC/m²/an',
      tooltip: 'Price excluding charges and tax',
    },
    surface_total: {
      label: 'Surface total',
      value: 2450.5,
      unit: 'm²',
    },
    location: {
      title: 'Location',
      address: {
        address_line1: '1 Place de la Défense',
        address_line2: '',
        postal_code: '92400',
        locality: 'COURBEVOIE',
        region: {
          code: '',
          name: 'Île-de-France',
        },
        country: {
          code: 'FR',
          name: 'France',
        },
      },
    },
    availability: {
      label: 'Available',
      value: 'Q2 2026',
    },
    mandate_type: {
      label: 'Type of mandate',
      value: 'Non-exclusive',
    },
    actions: {
      items: [
        {
          label: 'Access to the surface area table',
          url: '#surface-table',
          variant: 'primary',
          outline: true,
          icon: 'arrow-down',
          icon_position: 'right',
        },
        {
          label: 'Download the brochure',
          url: '#download',
          variant: 'primary',
        },
      ],
    },
  },
};

// Barcelona warehouse
export const BarcelonaWarehouse = {
  args: {
    title: 'Industrial Warehouse BARCELONA Zona Franca',
    reference: {
      label: 'Reference',
      value: 'OLBCN0800156',
    },
    building: {
      label: 'Building',
      value: 'Logistic Center ZAL',
    },
    price: {
      label: 'Rent',
      value: '45 000 €',
      unit: 'HT/HC/month',
      tooltip: 'Monthly rent excluding charges and tax',
    },
    surface_total: {
      label: 'Surface total',
      value: 8500,
      unit: 'm²',
    },
    location: {
      title: 'Location',
      address: {
        address_line1: 'Carrer de la Lleialtat',
        address_line2: '',
        postal_code: '08040',
        locality: 'BARCELONA',
        region: {
          code: '',
          name: 'Catalonia',
        },
        country: {
          code: 'ES',
          name: 'Spain',
        },
      },
    },
    availability: {
      label: 'Available',
      value: 'Immediately',
    },
    mandate_type: {
      label: 'Type of mandate',
      value: 'Exclusive',
    },
    actions: {
      items: [
        {
          label: 'View technical specifications',
          url: '#specs',
          variant: 'primary',
          outline: true,
          icon: 'file',
          icon_position: 'right',
        },
        {
          label: 'Schedule a visit',
          url: '#visit',
          variant: 'primary',
        },
      ],
    },
  },
};

// London office - available soon
export const LondonSoon = {
  args: {
    title: 'Premium Offices LONDON Canary Wharf',
    reference: {
      label: 'Reference',
      value: 'OLLON1400567',
    },
    building: {
      label: 'Building',
      value: 'One Canada Square',
    },
    price: {
      label: 'Rent',
      value: '£ 95',
      unit: 'per sq ft/year',
      tooltip: 'Annual rent per square foot',
    },
    surface_total: {
      label: 'Surface total',
      value: 1850,
      unit: 'm²',
    },
    location: {
      title: 'Location',
      address: {
        address_line1: '1 Canada Square',
        address_line2: '',
        postal_code: 'E14 5AB',
        locality: 'LONDON',
        region: {
          code: '',
          name: 'Greater London',
        },
        country: {
          code: 'GB',
          name: 'United Kingdom',
        },
      },
    },
    availability: {
      label: 'Available',
      value: 'September 2026',
    },
    mandate_type: {
      label: 'Type of mandate',
      value: 'Exclusive',
    },
    actions: {
      items: [
        {
          label: 'Register interest',
          url: '#interest',
          variant: 'primary',
          outline: true,
        },
        {
          label: 'Download brochure',
          url: '#download',
          variant: 'primary',
        },
      ],
    },
  },
};

// Minimal - only required fields
export const Minimal = {
  args: {
    title: 'Office Space for Rent',
    price: {
      value: '50 €',
      unit: 'per m²',
    },
    surface_total: {
      value: 150,
      unit: 'm²',
    },
    location: {
      address: {
        postal_code: '75001',
        locality: 'PARIS',
      },
    },
    actions: {
      items: [
        {
          label: 'Contact us',
          url: '#contact',
          variant: 'primary',
        },
      ],
    },
  },
};
