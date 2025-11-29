import markup from './toggle.twig';
import data from './toggle.yml';

const settings = {
  title: 'Elements/Toggle',
  tags: ['autodocs'],
  args: { ...data },
};

export const toggle = {
  name: 'toggle',
  render: (args) => markup(args),
  args: { ...data },
};

export default settings;
