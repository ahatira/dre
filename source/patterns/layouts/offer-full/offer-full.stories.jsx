import offerFull from './offer-full.twig';
import offerData from './offer-full.yml';
import './offer-full.css';

export default {
  title: 'Layouts/Offer Full',
  tags: ['autodocs'],
  argTypes: {
    modifier_class: {
      control: 'text',
      description: 'Optional modifier class applied to the root article',
      table: { category: 'Advanced' },
    },
  },
};

export const Default = {
  render: (args) => offerFull(args),
  args: {
    ...offerData,
  },
};

