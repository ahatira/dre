import markup from './link.twig';
import data from './link.yml';

const settings = {
  title: 'Elements/Link',
  tags: ['autodocs'],
  args: { ...data },
};

export const link = {
  name: 'link',
  render: (args) => markup(args),
  args: { ...data },
};

export default settings;
