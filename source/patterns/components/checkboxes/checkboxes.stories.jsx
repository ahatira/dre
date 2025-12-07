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
        component: 'Drupal checkboxes group wrapper. Contains multiple checkbox items.',
      },
    },
  },
  render: (args) => {
    const checkboxItems = [
      { ...checkboxData, id: 'option-1', value: '1', label: 'Option 1', checked: true },
      { ...checkboxData, id: 'option-2', value: '2', label: 'Option 2' },
      { ...checkboxData, id: 'option-3', value: '3', label: 'Option 3' },
    ];

    return checkboxesTwig({
      ...args,
      attributes: { class: args.class },
      children: checkboxItems.map((item) => checkboxTwig(item)).join(''),
    });
  },
};

export const Default = {
  args: data,
};
