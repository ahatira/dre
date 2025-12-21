import checkboxTwig from '../../elements/checkbox/checkbox.twig';
import checkboxData from '../../elements/checkbox/checkbox.yml';
import checkboxesTwig from './checkboxes.twig';
import data from './checkboxes.yml';

export default {
  title: 'Components/Checkboxes',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Drupal checkboxes group wrapper. Contains multiple checkbox items. Supports 5 layout variants: column (default), inline, grid-2, grid-3, grid-4, plus compact modifier.',
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
        'form-checkboxes--inline',
        'form-checkboxes--compact',
        'form-checkboxes--grid-2',
        'form-checkboxes--grid-3',
        'form-checkboxes--grid-4',
        'form-checkboxes--grid-2--compact',
      ],
      table: { category: 'Layout' },
    },
    children: {
      description: 'Rendered checkbox items (Checkbox atoms)',
      control: false,
      table: { category: 'Content' },
    },
  },
  render: (args) => {
    const checkboxItems = [
      {
        ...checkboxData,
        name: 'features',
        id: 'feature-1',
        value: 'balcony',
        label: 'Balcon',
        checked: true,
      },
      { ...checkboxData, name: 'features', id: 'feature-2', value: 'terrace', label: 'Terrasse' },
      { ...checkboxData, name: 'features', id: 'feature-3', value: 'garden', label: 'Jardin' },
    ];

    return checkboxesTwig({
      ...args,
      children: checkboxItems.map((item) => checkboxTwig(item)).join(''),
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
 * Horizontal row - ideal for short lists or horizontal display
 * Stacks vertically on mobile for better touch targets
 */
export const Inline = {
  render: () => {
    const checkboxItems = [
      {
        ...checkboxData,
        name: 'services',
        id: 'service-wifi',
        value: 'wifi',
        label: 'WiFi',
        checked: true,
      },
      {
        ...checkboxData,
        name: 'services',
        id: 'service-parking',
        value: 'parking',
        label: 'Parking',
      },
      { ...checkboxData, name: 'services', id: 'service-ac', value: 'ac', label: 'Climatisation' },
    ];

    return checkboxesTwig({
      modifier_class: 'form-checkboxes--inline',
      children: checkboxItems.map((item) => checkboxTwig(item)).join(''),
    });
  },
};

/**
 * Grid 2 Columns
 * Two-column layout for medium lists (6-12 options)
 * Example: Property features, equipment
 */
export const Grid2Columns = {
  render: () => {
    const checkboxItems = [
      {
        ...checkboxData,
        name: 'features',
        id: 'feat-1',
        value: 'balcony',
        label: 'Balcon',
        checked: true,
      },
      { ...checkboxData, name: 'features', id: 'feat-2', value: 'terrace', label: 'Terrasse' },
      { ...checkboxData, name: 'features', id: 'feat-3', value: 'garden', label: 'Jardin' },
      { ...checkboxData, name: 'features', id: 'feat-4', value: 'parking', label: 'Parking' },
      { ...checkboxData, name: 'features', id: 'feat-5', value: 'cellar', label: 'Cave' },
      { ...checkboxData, name: 'features', id: 'feat-6', value: 'elevator', label: 'Ascenseur' },
    ];

    return checkboxesTwig({
      modifier_class: 'form-checkboxes--grid-2',
      children: checkboxItems.map((item) => checkboxTwig(item)).join(''),
    });
  },
};

/**
 * Grid 3 Columns
 * Three-column layout for longer lists (12-20 options)
 * Example: Amenities, all property features
 */
export const Grid3Columns = {
  render: () => {
    const checkboxItems = [
      { ...checkboxData, name: 'amenities', id: 'amen-1', value: 'wifi', label: 'WiFi' },
      {
        ...checkboxData,
        name: 'amenities',
        id: 'amen-2',
        value: 'ac',
        label: 'Climatisation',
        checked: true,
      },
      { ...checkboxData, name: 'amenities', id: 'amen-3', value: 'heating', label: 'Chauffage' },
      {
        ...checkboxData,
        name: 'amenities',
        id: 'amen-4',
        value: 'dishwasher',
        label: 'Lave-vaisselle',
      },
      { ...checkboxData, name: 'amenities', id: 'amen-5', value: 'washing', label: 'Lave-linge' },
      { ...checkboxData, name: 'amenities', id: 'amen-6', value: 'dryer', label: 'Sèche-linge' },
      { ...checkboxData, name: 'amenities', id: 'amen-7', value: 'oven', label: 'Four' },
      {
        ...checkboxData,
        name: 'amenities',
        id: 'amen-8',
        value: 'microwave',
        label: 'Micro-ondes',
      },
      { ...checkboxData, name: 'amenities', id: 'amen-9', value: 'fridge', label: 'Réfrigérateur' },
    ];

    return checkboxesTwig({
      modifier_class: 'form-checkboxes--grid-3',
      children: checkboxItems.map((item) => checkboxTwig(item)).join(''),
    });
  },
};

/**
 * Compact Spacing
 * Reduced gap for dense forms (search filters sidebar)
 */
export const Compact = {
  render: () => {
    const checkboxItems = [
      { ...checkboxData, name: 'filters', id: 'filter-1', value: 'new', label: 'Neuf' },
      {
        ...checkboxData,
        name: 'filters',
        id: 'filter-2',
        value: 'recent',
        label: 'Récent',
        checked: true,
      },
      { ...checkboxData, name: 'filters', id: 'filter-3', value: 'renovated', label: 'Rénové' },
      { ...checkboxData, name: 'filters', id: 'filter-4', value: 'old', label: 'Ancien' },
      {
        ...checkboxData,
        name: 'filters',
        id: 'filter-5',
        value: 'to_renovate',
        label: 'À rénover',
      },
    ];

    return checkboxesTwig({
      modifier_class: 'form-checkboxes--compact',
      children: checkboxItems.map((item) => checkboxTwig(item)).join(''),
    });
  },
};

/**
 * Combined: Grid 2 + Compact
 * Two-column grid with tight spacing - ideal for sidebar filters
 */
export const Grid2Compact = {
  render: () => {
    const checkboxItems = [
      {
        ...checkboxData,
        name: 'orientation',
        id: 'orient-n',
        value: 'north',
        label: 'Nord',
        checked: true,
      },
      { ...checkboxData, name: 'orientation', id: 'orient-s', value: 'south', label: 'Sud' },
      { ...checkboxData, name: 'orientation', id: 'orient-e', value: 'east', label: 'Est' },
      { ...checkboxData, name: 'orientation', id: 'orient-w', value: 'west', label: 'Ouest' },
      {
        ...checkboxData,
        name: 'orientation',
        id: 'orient-ne',
        value: 'northeast',
        label: 'Nord-Est',
      },
      {
        ...checkboxData,
        name: 'orientation',
        id: 'orient-se',
        value: 'southeast',
        label: 'Sud-Est',
      },
    ];

    return checkboxesTwig({
      modifier_class: 'form-checkboxes--grid-2--compact',
      children: checkboxItems.map((item) => checkboxTwig(item)).join(''),
    });
  },
};
