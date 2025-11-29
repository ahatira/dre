import markup from './avatar.twig';
import data from './avatar.yml';

const settings = {
  title: 'Elements/Avatar',
  tags: ['autodocs'],
  args: { ...data },
};

export const avatar = {
  name: 'avatar',
  render: (args) => markup(args),
  args: { ...data },
};

export default settings;
