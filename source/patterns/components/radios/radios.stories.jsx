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
        component: 'Drupal radios group wrapper. Contains multiple radio items.',
      },
    },
  },
  render: (args) => {
    const radioItems = [
      { ...radioData, id: 'option-1', value: '1', label: 'Option 1', checked: true },
      { ...radioData, id: 'option-2', value: '2', label: 'Option 2' },
      { ...radioData, id: 'option-3', value: '3', label: 'Option 3' },
    ];

    return radiosTwig({
      ...args,
      attributes: { class: args.class },
      children: radioItems.map((item) => radioTwig(item)).join(''),
    });
  },
};

export const Default = {
  args: data,
};
