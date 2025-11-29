import markup from './label.twig';
import data from './label.yml';

const settings = {
  title: 'Elements/Label',
  tags: ['autodocs'],
  args: { ...data },
};

export const label = {
  name: 'label',
  render: (args) => markup(args),
  args: { ...data },
};

export default settings;
