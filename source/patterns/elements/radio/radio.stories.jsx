import markup from './radio.twig';
import data from './radio.yml';

const settings = {
  title: 'Elements/Radio',
  tags: ['autodocs'],
  args: { ...data },
};

export const radio = {
  name: 'radio',
  render: (args) => markup(args),
  args: { ...data },
};

export default settings;
