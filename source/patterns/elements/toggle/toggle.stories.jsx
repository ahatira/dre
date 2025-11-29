import toggleTwig from './toggle.twig';
import data from './toggle.yml';

export default {
  title: 'Elements/Toggle',
  tags: ['autodocs'],
  argTypes: {
    name: { control: 'text' },
    label: { control: 'text' },
    description: { control: 'text' },
    checked: { control: 'boolean' },
    disabled: { control: 'boolean' },
    size: { control: { type: 'select', options: ['small', 'medium', 'large'] } },
    showLabels: { control: 'boolean' },
    onLabel: { control: 'text' },
    offLabel: { control: 'text' },
  },
};

export const Default = {
  args: { ...data },
  render: (args) => toggleTwig(args),
};

export const Checked = {
  args: { ...data, checked: true },
  render: (args) => toggleTwig(args),
};

export const Disabled = {
  args: { ...data, disabled: true },
  render: (args) => toggleTwig(args),
};

export const Small = {
  args: { ...data, size: 'small' },
  render: (args) => toggleTwig(args),
};

export const Large = {
  args: { ...data, size: 'large' },
  render: (args) => toggleTwig(args),
};

export const WithLabels = {
  args: { ...data, showLabels: true, onLabel: 'Oui', offLabel: 'Non' },
  render: (args) => toggleTwig(args),
};

export const AllVariants = {
  render: () => `
    <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
      ${toggleTwig({ ...data })}
      ${toggleTwig({ ...data, checked: true })}
      ${toggleTwig({ ...data, disabled: true })}
      ${toggleTwig({ ...data, size: 'small' })}
      ${toggleTwig({ ...data, size: 'large' })}
      ${toggleTwig({ ...data, showLabels: true, onLabel: 'Oui', offLabel: 'Non' })}
    </div>
  `,
};
