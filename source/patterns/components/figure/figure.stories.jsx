import figureTemplate from './figure.twig';
import data from './figure.yml';
import './figure.css';

export default {
  title: 'Components/Figure',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Simple image with hover overlay caption. Overlay appears on hover with smooth transition and optional icon.',
      },
    },
  },
  render: (args) => figureTemplate(args),
  argTypes: {
    src: {
      control: 'text',
      description: 'Image source URL',
      table: { category: 'Content' },
    },
    alt: {
      control: 'text',
      description: 'Image alt text (required for accessibility)',
      table: { category: 'Content' },
    },
    caption: {
      control: 'text',
      description: 'Caption text (appears on hover with overlay)',
      table: { category: 'Content' },
    },
    icon: {
      control: 'select',
      options: [
        null,
        'heart',
        'camera',
        'copyright',
        'info',
        'star',
        'check-circle',
        'alert-circle',
        'location',
        'home',
        'building',
        'office',
        'shop',
        'warehouse',
        'commercial-space',
        'land',
        'coworking',
      ],
      description: 'Optional icon name (without icon- prefix)',
      table: { category: 'Content' },
    },
  },
};

export const Default = {
  args: data,
};

export const WithoutIcon = {
  args: {
    ...data,
    icon: null,
  },
};

export const WithoutCaption = {
  args: {
    ...data,
    caption: null,
    icon: null,
  },
};

export const LongCaption = {
  args: {
    ...data,
    caption:
      "Magnifique propriété d'exception située dans un quartier prisé - 4 chambres spacieuses, cuisine moderne équipée, jardin paysager de 500m²",
  },
};
