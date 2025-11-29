import markup from './flag.twig';
import data from './flag.yml';

const settings = {
  title: 'Elements/Flag',
  tags: ['autodocs'],
  args: { ...data },
};

export const flag = {
  name: 'flag',
  render: (args) => markup(args),
  args: { ...data },
};

export default settings;
