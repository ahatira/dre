import radioTwig from '../../elements/radio/radio.twig';
import radioData from '../../elements/radio/radio.yml';
import radiosTwig from './radios.twig';
import data from './radios.yml';

export default {
  title: 'Components/Radios',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Drupal radios group wrapper. Contains multiple radio items. Supports 5 layout variants: column (default), inline, grid-2, grid-3, grid-4, plus compact modifier.',
      },
    },
  },
  argTypes: {
    modifier_class: {
      description:
        'Modifier classes (--inline, --compact, --grid-2, --grid-3, --grid-4). Can combine: --grid-2--compact',
      control: 'select',
      options: [
        '',
        'form-radios--inline',
        'form-radios--compact',
        'form-radios--grid-2',
        'form-radios--grid-3',
        'form-radios--grid-4',
        'form-radios--grid-2--compact',
      ],
      table: { category: 'Layout' },
    },
    children: {
      description: 'Rendered radio items (Radio atoms)',
      control: false,
      table: { category: 'Content' },
    },
  },
  render: (args) => {
    const radioItems = [
      {
        ...radioData,
        name: 'property_type',
        id: 'option-1',
        value: '1',
        label: 'Appartement',
        checked: true,
      },
      { ...radioData, name: 'property_type', id: 'option-2', value: '2', label: 'Maison' },
      { ...radioData, name: 'property_type', id: 'option-3', value: '3', label: 'Loft' },
    ];

    return radiosTwig({
      ...args,
      children: radioItems.map((item) => radioTwig(item)).join(''),
    });
  },
};

/**
 * Default (Column Layout)
 * Vertical stacking - best for long option lists
 */
export const Default = {
  args: data,
};

/**
 * Inline Layout
 * Horizontal row - ideal for binary choices (Yes/No) or short lists
 * Stacks vertically on mobile for better touch targets
 */
export const Inline = {
  render: () => {
    const radioItems = [
      {
        ...radioData,
        name: 'furnished',
        id: 'furnished-yes',
        value: 'yes',
        label: 'Oui',
        checked: true,
      },
      { ...radioData, name: 'furnished', id: 'furnished-no', value: 'no', label: 'Non' },
    ];

    return radiosTwig({
      modifier_class: 'form-radios--inline',
      children: radioItems.map((item) => radioTwig(item)).join(''),
    });
  },
};

/**
 * Grid 2 Columns
 * Two-column layout for medium lists (6-12 options)
 * Example: Property types, transaction types
 */
export const Grid2Columns = {
  render: () => {
    const radioItems = [
      {
        ...radioData,
        name: 'type',
        id: 'type-1',
        value: 'apartment',
        label: 'Appartement',
        checked: true,
      },
      { ...radioData, name: 'type', id: 'type-2', value: 'house', label: 'Maison' },
      { ...radioData, name: 'type', id: 'type-3', value: 'loft', label: 'Loft' },
      { ...radioData, name: 'type', id: 'type-4', value: 'duplex', label: 'Duplex' },
      { ...radioData, name: 'type', id: 'type-5', value: 'villa', label: 'Villa' },
      { ...radioData, name: 'type', id: 'type-6', value: 'penthouse', label: 'Penthouse' },
    ];

    return radiosTwig({
      modifier_class: 'form-radios--grid-2',
      children: radioItems.map((item) => radioTwig(item)).join(''),
    });
  },
};

/**
 * Grid 3 Columns
 * Three-column layout for longer lists (12-20 options)
 * Example: Neighborhoods, amenities
 */
export const Grid3Columns = {
  render: () => {
    const radioItems = [
      { ...radioData, name: 'district', id: 'dist-1', value: '1', label: '1er arrondissement' },
      { ...radioData, name: 'district', id: 'dist-2', value: '2', label: '2e arrondissement' },
      {
        ...radioData,
        name: 'district',
        id: 'dist-3',
        value: '3',
        label: '3e arrondissement',
        checked: true,
      },
      { ...radioData, name: 'district', id: 'dist-4', value: '4', label: '4e arrondissement' },
      { ...radioData, name: 'district', id: 'dist-5', value: '5', label: '5e arrondissement' },
      { ...radioData, name: 'district', id: 'dist-6', value: '6', label: '6e arrondissement' },
      { ...radioData, name: 'district', id: 'dist-7', value: '7', label: '7e arrondissement' },
      { ...radioData, name: 'district', id: 'dist-8', value: '8', label: '8e arrondissement' },
      { ...radioData, name: 'district', id: 'dist-9', value: '9', label: '9e arrondissement' },
    ];

    return radiosTwig({
      modifier_class: 'form-radios--grid-3',
      children: radioItems.map((item) => radioTwig(item)).join(''),
    });
  },
};

/**
 * Compact Spacing
 * Reduced gap for dense forms (search filters sidebar)
 */
export const Compact = {
  render: () => {
    const radioItems = [
      { ...radioData, name: 'rooms', id: 'rooms-1', value: '1', label: '1 pièce' },
      { ...radioData, name: 'rooms', id: 'rooms-2', value: '2', label: '2 pièces', checked: true },
      { ...radioData, name: 'rooms', id: 'rooms-3', value: '3', label: '3 pièces' },
      { ...radioData, name: 'rooms', id: 'rooms-4', value: '4', label: '4 pièces' },
      { ...radioData, name: 'rooms', id: 'rooms-5', value: '5', label: '5+ pièces' },
    ];

    return radiosTwig({
      modifier_class: 'form-radios--compact',
      children: radioItems.map((item) => radioTwig(item)).join(''),
    });
  },
};

/**
 * Combined: Grid 2 + Compact
 * Two-column grid with tight spacing - ideal for sidebar filters
 */
export const Grid2Compact = {
  render: () => {
    const radioItems = [
      {
        ...radioData,
        name: 'energy',
        id: 'energy-a',
        value: 'A',
        label: 'Classe A',
        checked: true,
      },
      { ...radioData, name: 'energy', id: 'energy-b', value: 'B', label: 'Classe B' },
      { ...radioData, name: 'energy', id: 'energy-c', value: 'C', label: 'Classe C' },
      { ...radioData, name: 'energy', id: 'energy-d', value: 'D', label: 'Classe D' },
      { ...radioData, name: 'energy', id: 'energy-e', value: 'E', label: 'Classe E' },
      { ...radioData, name: 'energy', id: 'energy-f', value: 'F', label: 'Classe F' },
    ];

    return radiosTwig({
      modifier_class: 'form-radios--grid-2--compact',
      children: radioItems.map((item) => radioTwig(item)).join(''),
    });
  },
};
