import markup from './divider.twig';
import data from './divider.yml';

const settings = {
  title: 'Elements/Divider',
  tags: ['autodocs'],
  args: { ...data },
};

export const divider = {
  name: 'divider',
  render: (args) => markup(args),
  args: { ...data },
};

export default settings;
