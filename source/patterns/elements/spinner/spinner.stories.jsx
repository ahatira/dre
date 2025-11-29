import markup from './spinner.twig';
import data from './spinner.yml';

const settings = {
  title: 'Elements/Spinner',
  tags: ['autodocs'],
  args: { ...data },
};

export const spinner = {
  name: 'spinner',
  render: (args) => markup(args),
  args: { ...data },
};

export default settings;
