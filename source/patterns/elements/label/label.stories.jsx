import labelTwig from './label.twig';
import data from './label.yml';

export default {
  title: 'Elements/Label',
  tags: ['autodocs'],
  args: { ...data.props },
  argTypes: {
    text: { control: 'text', description: 'Texte du label', required: true },
    forId: { control: 'text', description: 'ID du champ ciblé' },
    required: { control: 'boolean', description: 'Champ requis' },
    disabled: { control: 'boolean', description: 'Champ désactivé' },
    attributes: { control: 'object', description: 'Attributs HTML additionnels' },
  },
};

export const Default = {
  name: 'Default',
  render: (args) => labelTwig(args),
  args: { ...data.props },
};

export const Required = {
  name: 'Required',
  render: (args) => labelTwig({ ...args, required: true }),
  args: { ...data.props, required: true },
};

export const Disabled = {
  name: 'Disabled',
  render: (args) => labelTwig({ ...args, disabled: true }),
  args: { ...data.props, disabled: true },
};

export const AllVariants = {
  name: 'All Variants',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 1rem;">
      ${labelTwig({ ...data.props })}
      ${labelTwig({ ...data.props, required: true })}
      ${labelTwig({ ...data.props, disabled: true })}
    </div>
  `,
};
