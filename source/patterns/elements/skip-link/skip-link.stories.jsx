import markup from './skip-link.twig';
import data from './skip-link.yml';

const settings = {
  title: 'Elements/Skip Link',
  tags: ['autodocs'],
  args: { ...data },
};

export const skipLink = {
  name: 'skip-link',
  render: (args) => markup(args),
  args: { ...data },
};

export default settings;
