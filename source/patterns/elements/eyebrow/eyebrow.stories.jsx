import markup from './eyebrow.twig';
import data from './eyebrow.yml';

const settings = {
  title: 'Elements/Eyebrow',
  tags: ['autodocs'],
  args: { ...data },
};

export const eyebrow = {
  name: 'eyebrow',
  render: (args) => markup(args),
  args: { ...data },
};

export default settings;
