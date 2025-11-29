import markup from './field.twig';
import data from './field.yml';

const settings = {
  title: 'Elements/Field',
  tags: ['autodocs'],
  args: { ...data },
};

export const field = {
  name: 'field',
  render: (args) => markup(args),
  args: { ...data },
};

export default settings;
