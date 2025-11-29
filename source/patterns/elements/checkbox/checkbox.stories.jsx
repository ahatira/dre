import markup from './checkbox.twig';
import data from './checkbox.yml';

const settings = {
  title: 'Elements/Checkbox',
  tags: ['autodocs'],
  args: { ...data },
};

export const checkbox = {
  name: 'checkbox',
  render: (args) => markup(args),
  args: { ...data },
};

export default settings;
